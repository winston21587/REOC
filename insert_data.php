<?php
include "dbConnCode.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $study_protocol_title = $_POST['study_protocol_title'];
    $research_category = $_POST['research_category'];
    $college = $_POST['college'];
    $adviser_name = $_POST['adviser_name'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email']; // Get email from the form

    // Insert into ResearcherTitleInfo_NoUser
    $stmt = $conn->prepare("INSERT INTO ResearcherTitleInfo_NoUser (study_protocol_title, research_category, college, adviser_name, mobile_number, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $study_protocol_title, $research_category, $college, $adviser_name, $mobile_number, $email);


    if ($stmt->execute()) {
        $researcher_title_id = $stmt->insert_id;
        $stmt->close();

        // Insert Researchers into ResearcherInvolved_NoUser
        $first_names = $_POST['first_name'];
        $last_names = $_POST['last_name'];
        $middle_initials = $_POST['middle_initial'];

        $stmt = $conn->prepare("INSERT INTO ResearcherInvolved_NoUser (researcher_title_id, first_name, last_name, middle_initial) VALUES (?, ?, ?, ?)");
        
        for ($i = 0; $i < count($first_names); $i++) {
            $first_name = $first_names[$i];
            $last_name = $last_names[$i];
            $middle_initial = !empty($middle_initials[$i]) ? $middle_initials[$i] : NULL;
            $stmt->bind_param("isss", $researcher_title_id, $first_name, $last_name, $middle_initial);
            $stmt->execute();
        }
        $stmt->close();

     // Function to generate a unique file name
function getUniqueFileName($upload_dir, $file_name) {
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_base = pathinfo($file_name, PATHINFO_FILENAME);
    $unique_file_name = $file_name;
    $counter = 1;

    // Check if file already exists and append a number to make it unique
    while (file_exists($upload_dir . $unique_file_name)) {
        $unique_file_name = $file_base . "_" . $counter . "." . $file_ext;
        $counter++;
    }

    return $unique_file_name;
}

// Directory where files will be saved
$upload_dir = 'uploads/';
$files = ['application_form', 'research_protocol', 'technical_review_clearance', 'data_collection', 'informed_consent', 'cv', 'study_protocol_form', 'informed_consent_form', 'exempt_review_form'];

foreach ($files as $file) {
    if (!empty($_FILES[$file]['name'])) {
        // Check file size (20 MB limit)
        if ($_FILES[$file]['size'] > 20 * 1024 * 1024) {
            die("Error: The file size for $file exceeds the maximum limit of 20 MB.");
        }

        // Generate unique file name
        $file_name = getUniqueFileName($upload_dir, basename($_FILES[$file]['name']));
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES[$file]['tmp_name'], $file_path)) {
            // Insert into the database using mysqli
            $stmt = $conn->prepare("INSERT INTO researcher_filesnouser (researcher_title_id, file_type, filename, file_path) VALUES (?, ?, ?, ?)");
            $file_type = ucfirst(str_replace('_', ' ', $file));  // Format the file type
            $stmt->bind_param("isss", $researcher_title_id, $file_type, $file_name, $file_path);
            $stmt->execute();
        }
    }
}

// Handle other files (additional documents)
if (!empty($_FILES['other_docs']['name'][0])) {  
    foreach ($_FILES['other_docs']['name'] as $key => $other_file_name) {
        if (!empty($other_file_name)) {
            // Check file size (20 MB limit)
            if ($_FILES['other_docs']['size'][$key] > 20 * 1024 * 1024) {
                die("Error: The file size for $other_file_name exceeds the maximum limit of 20 MB.");
            }

            // Generate unique file name
            $unique_other_file_name = getUniqueFileName($upload_dir, basename($other_file_name));
            $file_path = $upload_dir . $unique_other_file_name;

            if (move_uploaded_file($_FILES['other_docs']['tmp_name'][$key], $file_path)) {
                $stmt = $conn->prepare("INSERT INTO researcher_filesnouser (researcher_title_id, file_type, filename, file_path) VALUES (?, 'Other', ?, ?)");
                $stmt->bind_param("iss", $researcher_title_id, $unique_other_file_name, $file_path);
                $stmt->execute();
            }
        }
    }
}

        echo "Data and files successfully inserted!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
