<?php
require_once './class/clean.php';
require_once './class/Submit.php';
$submit = new Submit();
session_start();
// Default value for success is false
$success = false;
// Include database connection
require_once 'dbConnCode.php';  // This file contains the $conn variable for mysqli
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the user is logged in and is a researcher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Researcher') {
    header("Location: login.php");
    exit();
}
// Logout logic
if (isset($_POST['logout'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        session_destroy(); // Destroy the session to log the user out
        header("Location: login.php");
        exit();
    } else {
        echo "<script>alert('Invalid CSRF token.');</script>";
    }
}


$user_id = $_SESSION['user_id'];  // Get the current user's ID

$result = $submit->ApplicationStatus(1);
if ($result == false) {
    die("failed to get Application Status");
}


// Fetch application status from the database
// $query = "SELECT status FROM application_status WHERE id = 1";  // Assuming status is stored in row with id 1
// $result = mysqli_query($conn, $query);

// // Check if query was successful
// if (!$result) {
//     die("Query failed: " . mysqli_error($conn));
// }
// $row = mysqli_fetch_assoc($result);

// $query = "SELECT college_name_and_color FROM colleges";
// $result = mysqli_query($conn, $query);

$application_status = $result['status']; // Get the status (open or closed)

// Fetch all colleges from the database
$collegeList = $submit->GetColleges();


// Check if the query was successful
if ($result == false) {
    die("Query failed to get college list ");
}
// Get user_id from session

// Fetch user email from the database
// $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $stmt->bind_result($email);
// $stmt->fetch();
// $stmt->close();

$useremail = $submit->fetchUserEmail($user_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     // Rate limiting
     if (isset($_SESSION['last_submission_time']) && (time() - $_SESSION['last_submission_time']) < 10) {
        // Redirect to researcherHome.php if submission is too quick
        header("Location: researcherHome.php");
        exit();
    }
    $_SESSION['last_submission_time'] = time(); // Update the last submission time
    

    // Sanitize inputs
    $study_protocol_title = filter_input(INPUT_POST, 'study_protocol_title', FILTER_SANITIZE_STRING);
    $adviser_name = filter_input(INPUT_POST, 'adviser_name', FILTER_SANITIZE_STRING);
    $college_dropdown = filter_input(INPUT_POST, 'college_dropdown', FILTER_SANITIZE_STRING);
    $other_college = filter_input(INPUT_POST, 'other_college', FILTER_SANITIZE_STRING);
    $research_category_dropdown = filter_input(INPUT_POST, 'research_category_dropdown', FILTER_SANITIZE_STRING);
    $other_category = filter_input(INPUT_POST, 'other_category', FILTER_SANITIZE_STRING);
    $hidden_research_category = filter_input(INPUT_POST, 'hidden_research_category', FILTER_SANITIZE_STRING); // Get the value from the hidden input

    // Get the college value (use the "Other" field if applicable)
    $college = $college_dropdown === 'Other' ? $other_college . ' - Brown' : $college_dropdown;

   // Determine the research category value
if (!empty($hidden_research_category)) {
    // Use the hidden research category if it's set (this is when the dropdown is disabled)
    $research_category = $hidden_research_category;
} else {
    // Otherwise, fall back to the regular dropdown value
    $research_category = $research_category_dropdown === 'Other' ? $other_category : $research_category_dropdown;
}

// Add logic to decode the apostrophe entity if present
if (strpos($research_category, '&#39;') !== false) {
    $research_category = html_entity_decode($research_category, ENT_QUOTES);
}

    // Insert into Researcher_title_informations
    // $stmt = $conn->prepare("INSERT INTO Researcher_title_informations
    //  (user_id, study_protocol_title, college, research_category, adviser_name) VALUES (?, ?, ?, ?, ?)");
    // $stmt->bind_param("issss", $user_id, $study_protocol_title, $college, $research_category, $adviser_name);
    // $stmt->execute();

    $submit->researchTitleInfo($user_id, $study_protocol_title, $college, $research_category, $adviser_name);

    // Get the ID of the inserted researcher title information
    // $researcher_title_id = $conn->insert_id; // questionable(fixed)

    $researcher_title_id = $submit->getTitleID($user_id, $study_protocol_title);
    
    // Handle insertion of researcher names (including co-researchers)
    $researcher_first_names = $_POST['researcher_first_name'];
    $researcher_last_names = $_POST['researcher_last_name'];
    $researcher_middle_initials = $_POST['researcher_middle_initial'];
  

    // for ($i = 0; $i < count($researcher_first_names); $i++) {
    //     $first_name = filter_var($researcher_first_names[$i], FILTER_SANITIZE_STRING);
    //     $last_name = filter_var($researcher_last_names[$i], FILTER_SANITIZE_STRING);
    //     $middle_initial = !empty($researcher_middle_initials[$i]) ? filter_var($researcher_middle_initials[$i], FILTER_SANITIZE_STRING) : null;
    

    // }
    
    // // Insert into Researcher_involved
    // $stmt = $conn->prepare("INSERT INTO Researcher_involved (researcher_title_id, first_name, last_name, middle_initial ) VALUES (?, ?, ?, ? )");
    // $stmt->bind_param("isss", $researcher_title_id, $first_name, $last_name, $middle_initial, );
                                                                                          //  ^ T.T
    // $stmt->execute();          

    // ig this adds all the co-researcher -winston
    for ($i = 0; $i < count($researcher_first_names); $i++) {
        $first_name = clean($researcher_first_names[$i]);
        $last_name = clean($researcher_last_names[$i]);
        $middle_initial = !empty($researcher_middle_initials[$i]) ? clean($researcher_middle_initials[$i]) : null;
        $submit->researchInvolved( $researcher_title_id, $first_name, $last_name, $middle_initial);
    }  
        
        


$upload_dir = 'uploads/';  
$allowed_extensions = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg']; // Allowed file types
$max_file_size = 20 * 1024 * 1024; // 20MB

// Function to generate a unique filename if the file already exists
function getUniqueFileName($directory, $filename) {
    $file_parts = pathinfo($filename);
    $base_name = $file_parts['filename'];
    $extension = isset($file_parts['extension']) ? '.' . $file_parts['extension'] : '';
    $counter = 1;

    while (file_exists($directory . $filename)) {
        $filename = $base_name . "($counter)" . $extension;
        $counter++;
    }
    return $filename;
}

$files = [
    'application_form', 'research_protocol', 'technical_review_clearance', 
    'data_instruments', 'informed_consent', 'cv', 
    'study_protocol_form', 'informed_consent_form', 'exempt_review_form'
];

foreach ($files as $file) {
    if (!empty($_FILES[$file]['name'])) {
        // Check file size
        if ($_FILES[$file]['size'] > $max_file_size) {
            exit("Error: The file size for $file exceeds the maximum limit of 20 MB.");
        }

        // Validate file extension
        $file_ext = strtolower(pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            exit("Error: Invalid file type for $file. Only " . implode(", ", $allowed_extensions) . " files are allowed.");
        }

        // Get a unique filename
        $file_name = getUniqueFileName($upload_dir, basename($_FILES[$file]['name']));
        $file_path = $upload_dir . $file_name;

        // Move uploaded file
        if (move_uploaded_file($_FILES[$file]['tmp_name'], $file_path)) {
            // $stmt = $conn->prepare("INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path) VALUES (?, ?, ?, ?)");
            // $file_type = ucfirst(str_replace('_', ' ', $file));
            // $stmt->bind_param("isss", $researcher_title_id, $file_type, $file_name, $file_path);
            // $stmt->execute();
            $submit->UploadFile($researcher_title_id, $file_type, $file_name, $file_path);
        }
    }
}

// Handle additional documents (other files)
if (!empty($_FILES['other_files']['name'][0])) {
    foreach ($_FILES['other_files']['name'] as $key => $other_file_name) {
        if (!empty($other_file_name)) {
            // Check file size
            if ($_FILES['other_files']['size'][$key] > $max_file_size) {
                exit("Error: The file size for $other_file_name exceeds the maximum limit of 20 MB.");
            }

            // Validate file extension
            $file_ext = strtolower(pathinfo($other_file_name, PATHINFO_EXTENSION));
            if (!in_array($file_ext, $allowed_extensions)) {
                exit("Error: Invalid file type for $other_file_name. Only " . implode(", ", $allowed_extensions) . " files are allowed.");
            }

            // Get a unique filename
            $unique_other_file_name = getUniqueFileName($upload_dir, basename($other_file_name));
            $file_path = $upload_dir . $unique_other_file_name;

            // Move uploaded file
            if (move_uploaded_file($_FILES['other_files']['tmp_name'][$key], $file_path)) {
                // $stmt = $conn->prepare("INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path) VALUES (?, 'Other', ?, ?)");
                // $stmt->bind_param("iss", $researcher_title_id, $unique_other_file_name, $file_path);
                // $stmt->execute();

                $submit->moveUploadFiles($researcher_title_id, $unique_other_file_name, $file_path);
            }
        }
    }
}
}


?>






<!DOCTYPE html>
<html lang="en">

<head>
    <title>Application Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/styles.css">
    <link rel="stylesheet" type="text/css" href="./css/login1.css">
    <link rel="stylesheet" type="text/css" href="./css/login2.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <link rel="stylesheet" type="text/css" href="./css/SubmitFilesPhp.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">






<script>
function addOtherFile() {
    const container = document.getElementById('other-files-container');
    const div = document.createElement('div');
    div.innerHTML = '<input type="file" name="other_files[]" required>';
    container.appendChild(div);
}
// Adds fields for co-researchers (same structure as main researcher)
function addCoResearcher() {
    const container = document.getElementById('co-researcher-container');
    const div = document.createElement('div');
    div.innerHTML = `
               
                <input type="text" name="researcher_first_name[]" placeholder="First Name" required>
                <input type="text" name="researcher_last_name[]" placeholder="Last Name" required>
                <input type="text" name="researcher_middle_initial[]" placeholder="M.I." maxlength="2">
                <button type="button" onclick="removeCoResearcher(this)">Remove</button>
            `;
    container.appendChild(div);
}

function removeCoResearcher(button) {
    button.parentElement.remove();
}

function toggleOtherInput(selectElement, otherInputId) {
    var otherInput = document.getElementById(otherInputId);
    if (selectElement.value === 'Other') {
        otherInput.style.display = 'inline';
    } else {
        otherInput.style.display = 'none';
        otherInput.value = ''; // Reset the input field when not used
    }

    // Call handleCollegeChange if the College dropdown is involved
    if (selectElement.id === 'college_dropdown') {
        handleCollegeChange();
    }
}

function handleCollegeChange() {
    const collegeDropdown = document.getElementById("college_dropdown");
    const researchCategoryDropdown = document.getElementById("research_category_dropdown");
    const hiddenResearchCategory = document.getElementById("hidden_research_category");

    if (collegeDropdown.value === "Other") {
        // Set Research Category to "Externally Funded Research / Other Institution - 3,000.00"
        researchCategoryDropdown.value = "Externally Funded Research / Other Institution - 3,000.00";
        // Disable the dropdown to prevent changes
        researchCategoryDropdown.disabled = true;

        // Set the value of the hidden input
        hiddenResearchCategory.value = "Externally Funded Research / Other Institution - 3,000.00";
    } else {
        // Re-enable the Research Category dropdown if College is not "Other"
        researchCategoryDropdown.disabled = false;

        // Reset hidden input value if necessary
        hiddenResearchCategory.value = '';
    }
}

function toggleAdviserInput() {
    var categoryDropdown = document.getElementById('research_category_dropdown');
    var adviserInput = document.getElementById('adviser_name');
    var selectedValue = categoryDropdown.value;

    // Check if the selected value is one of the required categories
    if (selectedValue === "WMSU Undergraduate Thesis - 300.00" ||
        selectedValue === "WMSU Master's Thesis - 700.00" ||
        selectedValue === "WMSU Dissertation - 1,500.00") {
        adviserInput.setAttribute('required', 'required');
    } else {
        adviserInput.removeAttribute('required');
    }
}
// Call toggleAdviserInput on page load in case there is a default selected value
document.addEventListener("DOMContentLoaded", function() {
    toggleAdviserInput();
});
</script>


</head>

<body>


    <!-- Header Section -->
    <header>
        <a href="#" class="brand">
            <img src="img/logos.png" class="logo">
            <span class="reoc">Research Ethics Oversite Committee Portal</span>
        </a>

        <div class="menu-btn">
            <div class="navigation">
                <div class="navigation-items">
                    <a href="researcherHome.php"><strong>Home</strong></a>
                    <div class="dropdown1">
                        <a href="#"><strong>Applications</strong></a>
                        <div class="dropdown-content1">
                            <div class="file-item1">
                                <a href="SubmitFiles.php"><strong>Submit Application</strong></a>
                            </div>
                            <div class="file-item1">
                                <a href="viewApplications.php"><strong>View Applications</strong></a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <a href="#"><strong>Downloadables</strong></a>
                        <div class="dropdown-content">
                            <div class="file-item">
                                <span><strong>Application Form (WMSU-REOC-FR-001)</strong></span>
                                <a href="./files/2-FR.002-Application-Form.doc" download>Download</a>
                            </div>
                            <div class="file-item">
                                <span><strong>Study Protocol Assessment Form (WMSU-REOC-FR-004)</strong></span>
                                <a href="./files/4-FR.004-Study-Protocol-Assessment-Form-Copy.docx"
                                    download>Download</a>
                            </div>
                            <div class="file-item">
                                <span><strong>Informed Consent Assessment Form (WMSU-REOC-FR-005)</strong></span>
                                <a href="./files/5-FR.005-Informed-Consent-Assessment-Form (1).docx"
                                    download>Download</a>
                            </div>
                            <div class="file-item">
                                <span><strong>Exempt Review Assessment Form (WMSU-REOC-FR-006)</strong></span>
                                <a href="./files/6-FR.006-EXEMPT-REVIEW-ASSESSMENT-FORM (1).docx" download>Download</a>
                            </div>
                        </div>
                    </div>

                    <a href="./instructions.html"><strong>Instructions</strong></a>


                    <!-- Logout Button -->
                    <form method="POST" action="researcherHome.php" style="display: inline;">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <button type="submit" name="logout" class="logout-button">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    </div>

    </form>


    <div class="limiter">
        <div class="container-login1001">
            <div class="wrap-login1001">
                <div class="login100-form1-title" style="background-image: url(./img/wmsu5.jpg);">
                    <span class="login100-form1-title-1">
                        reoc-wmsu portal
                    </span>
                    <h4 class="sign">APPLICATION FORM </h4>
                </div>

                <?php if ($application_status === 'open'): ?>
                <form method="POST" enctype="multipart/form-data" class="login100-form1 validate-form">
                    <div id="pageContent" class="page">





                        <h3>Research Information</h3>

                        <div class="wrap-input100 validate-input m-b-26" data-validate="Title is required">
                            <span class="label-input100">Study Protocol Title</span>
                            <input class="input100" type="text" name="study_protocol_title" required
                                placeholder="Enter Title">
                            <span class="focus-input100"></span>
                        </div>

                        <div class="wrap-input1001 validate-input m-b-26" data-validate="Research Category is required">
                            <span class="label-input100">Research Category & Fees</span>
                            <span class="choice">
                                <select class="input1001" name="research_category_dropdown"
                                    id="research_category_dropdown"
                                    onchange="toggleOtherInput(this, 'other_category_input'); toggleAdviserInput()"
                                    required>
                                    <option value="WMSU Undergraduate Thesis - 300.00">WMSU Undergraduate Thesis -
                                        300.00</option>
                                    <option value="WMSU Master's Thesis - 700.00">WMSU Master's Thesis - 700.00</option>
                                    <option value="WMSU Dissertation - 1,500.00">WMSU Dissertation - 1,500.00</option>
                                    <option value="WMSU Institutionally Funded Research - 2,000.00">WMSU Institutionally
                                        Funded Research - 2,000.00</option>
                                    <option value="Externally Funded Research / Other Institution - 3,000.00">Externally
                                        Funded Research / Other Institution - 3,000.00</option>
                                </select>
                            </span>

                            <!-- Hidden input to store the selected research category value -->
                            <input type="hidden" name="hidden_research_category" id="hidden_research_category">
                            <input type="text" id="other_category_input" name="other_category"
                                placeholder="Specify Other Category" style="display:none;"><br>

                        </div>




                        <br>
                        <br>
                        <br>
                        <h3>Researcher Information</h3>

                        <form class="login100-form validate-form">
                            <div class="name-fields">


                                <div class="wrap-input100SN validate-input m-b-18" data-validate="Required">
                                    <span class="label-input100">Full Name</span>
                                    <input class="input100" type="text" name="researcher_first_name[]"
                                        placeholder="First Name" required>
                                    <span class="focus-input100"></span>
                                </div>



                                <div class="wrap-input100FN validate-input m-b-18" data-validate="Required">
                                    <input class="input100" type="text" name="researcher_last_name[]"
                                        placeholder="Last Name" required>
                                    <span class="focus-input100"></span>
                                </div>



                                <div class="wrap-input100MI">
                                    <input class="input100" type="text" name="researcher_middle_initial[]"
                                        placeholder="M.I." maxlength="2">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>


                            <label>Co-researchers</label>
                            <div id="co-researcher-container"></div>
                            <button type="button" onclick="addCoResearcher()" class="cobtn">Add
                                Co-researcher</button><br>


                            <div class="wrap-input1001 validate-input m-b-26"
                                data-validate="Research Category is required">
                                <span class="label-input100">College</span>
                                <span class="choice">
                                    <select class="input1001" name="college_dropdown" id="college_dropdown"
                                        onchange="toggleOtherInput(this, 'other_college_input'); handleCollegeChange()"
                                        required>

                                </span>
                            </div>

                            <?php
    // Fetch each row and create an option for each college
    foreach ($collegeList as $college) {
        echo '<option value="' . htmlspecialchars($college['college_name_and_color']) . '">'
         . htmlspecialchars($college['college_name_and_color']) . '</option>';
    }
    ?>
                            <option value="Other">Other (Please specify)</option>
                            </select>

                            <input type="text" id="other_college_input" name="other_college"
                                placeholder="Specify Other College" style="display:none;"><br>

                            <div class="wrap-input100 validate-input m-b-26">
                                <span class="label-input100">Name of Adviser</span>
                                <input class="input100" type="text" id="adviser_name" name="adviser_name">
                                <span class="focus-input100"></span>
                            </div>


                            <br>
                            <br>
                            <br>
                            <h3>Necessary Files</h3>
                            <br>
                            <br>
                            <h4 style="margin-left:-5px;">Upload the Soft Copy of the research here:</h4>
                            <div class="move">
                                <form class="login100-form validate-form">


                                    <form class="login100-form validate-form">



                                        <form class="login100-form validate-form">
                                            <div class="wrap-input200 validate-input m-b-26"
                                                data-validate="Email Address is required">
                                                <span class="label-input200"><strong>Application Form For Research
                                                        Ethics Review - WMSU-REOC-FR-001 </strong>(with researcher/s
                                                    signature in pdf file)</span>
                                                <input class="input200" type="file" name="application_form"
                                                    accept=".pdf" required>

                                            </div>

                                            <form class="login100-form validate-form">

                                                <div class="wrap-input200 validate-input m-b-26"
                                                    data-validate="Email Address is required">
                                                    <span class="label-input200"><strong>Research Protocol/Proposal
                                                        </strong>(with page and line number in pdf file)</span>
                                                    <input class="input200" type="file" name="research_protocol"
                                                        accept=".pdf" required>

                                                    <div class="wrap-input200 validate-input m-b-26"
                                                        data-validate="Email Address is required">
                                                        <span class="label-input200"><strong>Technical Review Clearance
                                                            </strong>(pdf file)</span>
                                                        <input class="input200" type="file"
                                                            name="technical_review_clearance" accept=".pdf" required>

                                                    </div>





                                                    <form class="login100-form validate-form">

                                                        <div class="wrap-input200 validate-input m-b-26"
                                                            data-validate="Email Address is required">
                                                            <span class="label-input200"><strong>Data Collection
                                                                    Instruments </strong>(with page and line number in
                                                                pdf file)</span>
                                                            <input class="input200" type="file" name="data_instruments"
                                                                accept=".pdf" required>

                                                        </div>





                                                        <form class="login100-form validate-form">

                                                            <div class="wrap-input200 validate-input m-b-26"
                                                                data-validate="Email Address is required">
                                                                <span class="label-input200"><strong>Informed
                                                                        Consent/Assent </strong>(with page and line
                                                                    number in pdf file)</span>
                                                                <input class="input200" type="file"
                                                                    name="informed_consent" accept=".pdf" required>

                                                            </div>

                                                            <form class="login100-form validate-form">

                                                                <div class="wrap-input200 validate-input m-b-26"
                                                                    data-validate="Email Address is required">
                                                                    <span class="label-input200"><strong>Curriculum
                                                                            Vitae of Researcher/s</strong>(pdf
                                                                        file)</span>
                                                                    <input class="input200" type="file" name="cv"
                                                                        accept=".pdf" required>

                                                                </div>


                                                                <form class="login100-form validate-form">

                                                                    <div class="wrap-input200 validate-input m-b-26"
                                                                        data-validate="Email Address is required">
                                                                        <span class="label-input200"><strong>Completed
                                                                                Study Protocol Assessment Form -
                                                                                WMSU-REOC-FR-004 </strong>(fill up the
                                                                            required details with asterisk in the word
                                                                            file)</span>
                                                                        <input class="input200" type="file"
                                                                            name="study_protocol_form"
                                                                            accept=".doc,.docx" required>

                                                                    </div>


                                                                    <form class="login100-form validate-form">

                                                                        <div class="wrap-input200 validate-input m-b-26"
                                                                            data-validate="Email Address is required">
                                                                            <span class="label-input200"><strong>Completed
                                                                                    Informed Consent Assessment Form -
                                                                                    WMSU-REOC-FR-005 </strong>(fill up
                                                                                the required details with asterisk in
                                                                                the word file)</span>
                                                                            <input class="input200" type="file"
                                                                                name="informed_consent_form"
                                                                                accept=".doc,.docx" required>

                                                                        </div>



                                                                        <form class="login100-form validate-form">

                                                                            <div class="wrap-input200 validate-input m-b-26"
                                                                                data-validate="Email Address is required">
                                                                                <span class="label-input200"><strong>Completed
                                                                                        Exempt Review Assessment Form -
                                                                                        WMSU-REOC-FR-006 </strong>(fill
                                                                                    up the required details with
                                                                                    asterisk in the word file)</span>
                                                                                <input class="input200" type="file"
                                                                                    name="exempt_review_form"
                                                                                    accept=".doc,.docx" required>

                                                                            </div>





                                                                            <form class="login100-form validate-form">
                                                                                <div class="wrap-input200 validate-input m-b-26"
                                                                                    data-validate="Email Address is required">
                                                                                    <span
                                                                                        class="label-input200"><strong>Other
                                                                                            documents (NCIP Clearance,
                                                                                            MOA, MOU, etc. in pdf
                                                                                            file)</strong>(with
                                                                                        researcher/s signature in pdf
                                                                                        file)</span>
                                                                                    <div id="other-files-container">
                                                                                        <input class="input200"
                                                                                            type="file"
                                                                                            name="other_files[]"
                                                                                            accept=".pdf">
                                                                                        <button class="addbtn "
                                                                                            type="button"
                                                                                            onclick="addOtherFile()">Add
                                                                                            More</button>

                                                                                    </div>
                                                                            </form>

                                                                            <div class="container-login100-form-btn"
                                                                                style="display: flex; gap: 10px;">
                                                                                <button class="login100-form-btn"
                                                                                    type="submit" id="prevButton">
                                                                                    Submit
                                                                                </button>

                                                                            </div>
                                                                            <?php else: ?>
                                                                            <!-- Message when application is closed -->
                                                                            <p>Submission Closed: We are not currently
                                                                                accepting applications. Please check
                                                                                back later.</p>
                                                                            <?php endif; ?>
                                                </div>
                            </div>
                    </div>



                                                        </form>
            </div>
        </div>       
    </div>




    </div>
    </div>
    </form>

    </div>
                                                  <!-- bro wtf -->

    </form>

    </div>
    </li>
    </form>
    </div>

    </div>

    </div>
    </div>
    </div>
    </div>



    <script>
    // Check if the PHP success variable is true
    var success = <?php echo json_encode($success); ?>; // Encode the PHP variable to JavaScript
    if (success) {
        Swal.fire({
            title: 'Success!',
            text: 'Files uploaded successfully. You will be redirected to the homepage shortly. Please check your email for confirmation.',
            icon: 'success',
            timer: 3000, // Auto close after 3 seconds
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'researcherHome.php'; // Redirect after the alert
        });
    }
    </script>

    <footer class="footer">
        <div class="owl-carousel">

            <a href="#" class="gallery__photo">
                <img src="img/wmsu55.jpg" alt="" />

            </a>
            <a href="#" class="gallery__photo">
                <img src="img/wmsu11.jpg" alt="" />

            </a>
            <a href="#" class="gallery__photo">
                <img src="img/reoc11.jpg" alt="" />

            </a>
            <a href="#" class="gallery__photo">
                <img src="img/wmsu22.jpg" alt="" />

            </a>
            <a href="#" class="gallery__photo">
                <img src="img/reoc22.jpg" alt="" />

            </a>
            <a href="#" class="gallery__photo">
                <img src="img/wmsu44.jpg" alt="" />

            </a>

        </div>
        <div class="footer__redes">
            <ul class="footer__redes-wrapper">
                <li>
                    <a href="#" class="footer__link">
                        <i class=""></i>
                        Normal Road, Baliwasan, Z.C.
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__link">
                        <i class=""></i>
                        09112464566
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__link">
                        <i class=""></i>
                        wmsureoc@gmail.com
                    </a>
                </li>
                <li>
                    <a href="#" class="footer__link">
                        <i class="fab fa-phone-alt"></i>

                    </a>
                </li>
            </ul>
        </div>
        <div class="separador"></div>
        <p class="footer__texto">RESEARCH ETHICS OVERSITE COMMITTEE - WMSU</p>
    </footer>







    <!-- partial -->


    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script src='https://unpkg.com/feather-icons'></script>
    <script src="./js/main.js"></script>
    <script src="./js/swiper.js"></script>
    <script src="./js/footer.js"></script>
    <script src="./js/faq.js"></script>


    <script src="./js/fonts.js"></script>


    </div>
</body>

</html>