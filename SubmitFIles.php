<?php
session_start();
// Default value for success is false
$success = false;
// Include database connection
require_once 'dbConnCode.php';  // This file contains the $conn variable for mysqli
require 'vendor/autoload.php'; // Load PHPMailer
include 'navbar/navbar.php'; // Include the navbar
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
// Fetch application status from the database
$query = "SELECT status FROM application_status WHERE id = 1";  // Assuming status is stored in row with id 1
$result = mysqli_query($conn, $query);
 
// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
 
$row = mysqli_fetch_assoc($result);
$application_status = $row['status']; // Get the status (open or closed)
// Fetch all colleges from the database
$query = "SELECT college_name_and_color FROM colleges";
$result = mysqli_query($conn, $query);
 
// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
// Get user_id from session
$user_id = $_SESSION['user_id'];  // Get the current user's ID
 
 
 
// Fetch user email from the database
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();
 
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
    $stmt = $conn->prepare("INSERT INTO Researcher_title_informations (user_id, study_protocol_title, college, research_category, adviser_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $study_protocol_title, $college, $research_category, $adviser_name);
    $stmt->execute();
 
    // Get the ID of the inserted researcher title information
    $researcher_title_id = $conn->insert_id;
 
    // Handle insertion of researcher names (including co-researchers)
    $researcher_first_names = $_POST['researcher_first_name'];
    $researcher_last_names = $_POST['researcher_last_name'];
    $researcher_middle_initials = $_POST['researcher_middle_initial'];
 
 
    for ($i = 0; $i < count($researcher_first_names); $i++) {
        $first_name = filter_var($researcher_first_names[$i], FILTER_SANITIZE_STRING);
        $last_name = filter_var($researcher_last_names[$i], FILTER_SANITIZE_STRING);
        $middle_initial = !empty($researcher_middle_initials[$i]) ? filter_var($researcher_middle_initials[$i], FILTER_SANITIZE_STRING) : null;
 
 
        // Insert into Researcher_involved
        $stmt = $conn->prepare("INSERT INTO Researcher_involved (researcher_title_id, first_name, last_name, middle_initial ) VALUES (?, ?, ?, ? )");
        $stmt->bind_param("isss", $researcher_title_id, $first_name, $last_name, $middle_initial, );
        $stmt->execute();
    }
 
 
 
 
 
    $upload_dir = 'uploads/';
    $allowed_extensions = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg']; // Allowed file types
    $max_file_size = 20 * 1024 * 1024; // 20MB
 
    // Function to generate a unique filename if the file already exists
    function getUniqueFileName($directory, $filename)
    {
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
        'application_form',
        'research_protocol',
        'technical_review_clearance',
        'data_instruments',
        'informed_consent',
        'cv',
        'study_protocol_form',
        'informed_consent_form',
        'exempt_review_form'
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
                $stmt = $conn->prepare("INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path) VALUES (?, ?, ?, ?)");
                $file_type = ucfirst(str_replace('_', ' ', $file));
                $stmt->bind_param("isss", $researcher_title_id, $file_type, $file_name, $file_path);
                $stmt->execute();
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
                    $stmt = $conn->prepare("INSERT INTO researcher_files (researcher_title_id, file_type, filename, file_path) VALUES (?, 'Other', ?, ?)");
                    $stmt->bind_param("iss", $researcher_title_id, $unique_other_file_name, $file_path);
                    $stmt->execute();
                }
            }
        }
    }
}
 
 
?>
 
 
<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/styles.css">
    <link rel="stylesheet" type="text/css" href="./css/login1.css">
    <link rel="stylesheet" type="text/css" href="./css/login2.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Document</title>
 
    <script>
 
        let otherFiles = 0;
        function addOtherFile() {
            const container = document.getElementById('other-files-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <!-- Other Documents -->
                <div class="my-4 grid grid-cols-1 gap-2">
                    <h1 class="font-medium">
                        Document #${++otherFiles}
                    </h1>
                    <div class="flex items-center justify-center w-full">
                        <label for="other_files[]"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                        class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">(with
                                    researcher/s signature in pdf
                                    file)</p>
                            </div>
                            <input id="other_files[]" name="other_files[]" type="file" class="hidden"
                                accept=".pdf" />
                        </label>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }
 
        let coResearchers = 0;
 
        // Adds fields for co-researchers (same structure as main researcher)
        function addCoResearcher() {
            const container = document.getElementById('co-researcher-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                    <label>#${++coResearchers}</label>
                    <div class="grid grid-cols-5 gap-2">
                        <input type="text" name="researcher_first_name[]" placeholder="First Name"
                            class="py-2.5 col-span-2 px-4 bg-gray-100 rounded-lg" required>
                        <input type="text" name="researcher_last_name[]" placeholder="Last Name"
                            class="py-2.5 col-span-2 px-4 bg-gray-100 rounded-lg" required>
                        <input type="text" name="researcher_middle_initial[]" placeholder="M.I."
                            class="py-2.5 px-4 w-14 bg-gray-100 rounded-lg" maxlength="2" required>
                    </div>
                </div>
            `;
            container.appendChild(div);
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
        document.addEventListener("DOMContentLoaded", function () {
            toggleAdviserInput();
        });
    </script>
    <style>
        html,
        body {
            width: 100%;
            max-width: 100vw;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
    </style>
</head>
 
<body>
    
    <main>
        <div class="mt-24 bg-white rounded-xl mx-auto max-w-4xl min-h-96 w-full shadow p-0">
            <div class="h-44 w-full relative">
                <div
                    class="flex flex-col items-center justify-center absolute h-full w-full bg-gradient bg-gradient-to-b from-red-600/50 to-red-950/50">
                    <h1 class="text-3xl text-white font-bold uppercase">reoc-wmsu portal</h1>
                    <p class="text-xl text-white font-thin uppercase">Application Form</p>
                </div>
                <img src="./img/wmsu5.jpg" class="object-cover h-44 rounded-t-xl">
            </div>
            <?php if ($application_status === 'open'): ?>
                <form method="POST" enctype="multipart/form-data" action="" class="p-12 text-start">
                    <h1 class="sm:text-lg font-medium mb-4">Research Information</h1>
                    <div class="space-y-4">
 
                        <!-- Study Protocol Title -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label>Study Protocol Title</label>
                            <input type="text" name="study_protocol_title" placeholder="Enter Title"
                                class="py-2.5 px-4 bg-gray-100 rounded-lg" required>
                        </div>
 
 
                        <!-- Research Category & Fees -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label>Research Category & Fees</label>
                            <select type="text" name="research_category_dropdown"
                                onchange="toggleOtherInput(this, 'other_category_input'); toggleAdviserInput()"
                                class="py-2.5 px-4 bg-gray-100 rounded-lg" required>
                                <option value="WMSU Undergraduate Thesis - 300.00">WMSU Undergraduate Thesis -
                                    300.00</option>
                                <option value="WMSU Master's Thesis - 700.00">WMSU Master's Thesis - 700.00</option>
                                <option value="WMSU Dissertation - 1,500.00">WMSU Dissertation - 1,500.00</option>
                                <option value="WMSU Institutionally Funded Research - 2,000.00">WMSU Institutionally
                                    Funded Research - 2,000.00</option>
                                <option value="Externally Funded Research / Other Institution - 3,000.00">Externally
                                    Funded Research / Other Institution - 3,000.00</option>
                            </select>
                            <!-- Hidden input to store the selected research category value -->
                            <input type="hidden" name="hidden_research_category" id="hidden_research_category">
                            <input type="text" id="other_category_input" name="other_category"
                                placeholder="Specify Other Category" style="display:none;"><br>
                        </div>
 
                        <h1 class="sm:text-lg font-medium mb-4">Researcher Information</h1>
 
                        <!-- Full Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label>Full Name</label>
                            <div class="grid grid-cols-5 gap-2">
                                <input type="text" name="researcher_first_name[]" placeholder="First Name"
                                    class="py-2.5 col-span-2 px-4 bg-gray-100 rounded-lg" required>
                                <input type="text" name="researcher_last_name[]" placeholder="Last Name"
                                    class="py-2.5 col-span-2 px-4 bg-gray-100 rounded-lg" required>
                                <input type="text" name="researcher_middle_initial[]" placeholder="M.I."
                                    class="py-2.5 px-4 w-14 bg-gray-100 rounded-lg" maxlength="2" required>
                            </div>
                        </div>
 
                        <!-- Co-Researchers -->
                        <h1 class="mb-4">Co-Researchers</h1>
                        <div id="co-researcher-container" class="space-y-2"></div>
                        <button type="button" onclick="addCoResearcher()"
                            class="py-1.5 text-xs px-4 bg-red-800 hover:bg-red-800/80 text-white rounded-full">Add
                            Co-researcher</button>
 
                        <!-- College -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label>College</label>
                            <select type="text" name="college_dropdown"
                                onchange="toggleOtherInput(this, 'other_college_input'); handleCollegeChange()"
                                class="py-2.5 px-4 bg-gray-100 rounded-lg" required>
                                <?php
                                // Fetch each row and create an option for each college
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . htmlspecialchars($row['college_name_and_color']) . '">' . htmlspecialchars($row['college_name_and_color']) . '</option>';
                                }
                                ?>
                                <option value="Other">Other (Please specify)</option>
 
                            </select>
                        </div>
 
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label></label>
                            <input type="text" id="other_college_input" name="other_college"
                                placeholder="Specify Other College" style="display:none;"
                                class="py-2.5 px-4 bg-gray-100 rounded-lg"><br>
                        </div>
 
                        <!-- Name of Adviser -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                            <label>Name of Adviser</label>
                            <input type="text" name="adviser_name" id="adviser_name" placeholder="Enter Title"
                                class="py-2.5 px-4 bg-gray-100 rounded-lg">
                        </div>
 
                        <h1 class="sm:text-lg font-medium mb-4">Necessary Files</h1>
                        <h4>Upload the Soft Copy of the research here:</h4>
 
                        <!-- File Input Application Form -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Application Form For Research
                                Ethics Review - WMSU-REOC-FR-001 </h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="application_form"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(with researcher/s
                                            signature in PDF file)</p>
                                    </div>
                                    <input id="application_form" name="application_form" type="file" class="hidden"
                                        accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Research Protocol/Proposal -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Research Protocol/Proposal</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="research_protocol"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(with page and line number in
                                            PDF file)</p>
                                    </div>
                                    <input id="research_protocol" name="research_protocol" type="file" class="hidden"
                                        accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Technical Review Clearance -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Technical Review Clearance</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="technical_review_clearance"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(PDF file)</p>
                                    </div>
                                    <input id="technical_review_clearance" name="technical_review_clearance" type="file"
                                        class="hidden" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Data Collection Instruments -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Data Collection Instruments</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="data_instruments"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(with page and line number in
                                            PDF file)</p>
                                    </div>
                                    <input id="data_instruments" name="data_instruments" type="file" class="hidden"
                                        accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
 
                        <!-- Informed Consent/Assent -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Informed Consent/Assent</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="informed_consent"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(with page and line number in
                                            PDF file)</p>
                                    </div>
                                    <input id="informed_consent" name="informed_consent" type="file" class="hidden"
                                        accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Curriculum Vitae of Researcher/s -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Curriculum Vitae of Researcher/s</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="cv"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(PDF file)</p>
                                    </div>
                                    <input id="cv" name="cv" type="file" class="hidden" accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Completed Study Protocol Assessment Form -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Completed
                                Study Protocol Assessment Form -
                                WMSU-REOC-FR-004</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="study_protocol_form"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(fill up the
                                            required details with asterisk in the word
                                            file)</p>
                                    </div>
                                    <input id="study_protocol_form" name="study_protocol_form" type="file" class="hidden"
                                        accept=".doc,.docx" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Completed Study Protocol Assessment Form -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Completed
                                Informed Consent Assessment Form</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="informed_consent_form"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(fill up
                                            the required details with asterisk in
                                            the word file)</p>
                                    </div>
                                    <input id="informed_consent_form" name="informed_consent_form" type="file"
                                        class="hidden" accept=".doc,.docx" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Completed Exempt Review Assessment -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">Completed
                                Exempt Review Assessment Form -
                                WMSU-REOC-FR-006</h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="exempt_review_form"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(fill up
                                            the required details with asterisk in
                                            the word file)</p>
                                    </div>
                                    <input id="exempt_review_form" name="exempt_review_form" type="file" class="hidden"
                                        accept=".doc,.docx" />
                                </label>
                            </div>
                        </div>
 
                        <!-- Other Documents -->
                        <div class="my-4 grid grid-cols-1 gap-2">
                            <h1 class="font-medium">
                                <strong>
                                    Other documents
                                    (NCIP Clearance,
                                    MOA, MOU, etc.)
                                </strong>
                            </h1>
                            <div class="flex items-center justify-center w-full">
                                <label for="other_files[]"
                                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">(with
                                            researcher/s signature in pdf
                                            file)</p>
                                    </div>
                                    <input id="other_files[]" name="other_files[]" type="file" class="hidden"
                                        accept=".pdf" />
                                </label>
                            </div>
                        </div>
 
                        <div id="other-files-container">
                            <button type="button" onclick="addOtherFile()"
                                class="py-1.5 text-xs px-2 bg-red-800 hover:bg-red-800/80 text-white rounded-full">Add
                                More Documents</button>
                        </div>
                    </div>
 
                    <div class="flex items-cente justify-center w-full mt-12">
                        <button type="submit" id="prevButton"
                            class="py-2.5 px-4 bg-red-800 hover:bg-red-800/80 text-white rounded-full">Submit Form</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Message when application is closed -->
                <p>Submission Closed: We are not currently
                    accepting applications. Please check
                    back later.</p>
            <?php endif; ?>
        </div>
    </main>
 
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
 
 
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script src='https://unpkg.com/feather-icons'></script>
    <script src="./js/main.js"></script>
    <script src="./js/swiper.js"></script>
    <script src="./js/footer.js"></script>
    <script src="./js/faq.js"></script>
 
 
    <script src="./js/fonts.js"></script>
</body>
 
</html>
