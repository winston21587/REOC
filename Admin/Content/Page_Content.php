<?php
    require_once '../../class/Admin.php';
    include '../../class/clean.php';
    session_start();
// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true);
}
// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php");
    exit();
}

// Start CSRF token generation if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Logout logic
if (isset($_POST['logout'])) {
    // Validate CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        session_destroy();
        header("Location: ../../login.php");
        exit();
    } else {
        echo "<script>alert('Invalid CSRF token.');</script>";
    }
}

    $admin = new admin();
    $cmsData = $admin->getcmsData();


    function getValue($cmsData, $type) {
    foreach ($cmsData as $row) {
        if ($row['type'] === $type) {
            return $row['content']; // Return the matching row
        }
    }
    return null; // Return null if no match is found
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Sanitize and prepare data for update
    $dataToUpdate = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'update') { // Exclude the submit button
            $dataToUpdate[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Sanitize input
        }
    }

    // Debugging: Log the data to be updated
    error_log("Data to update: " . print_r($dataToUpdate, true));

    // Call the update function
    if ($admin->updateCMSContent($dataToUpdate)) {
        $_SESSION['message'] = "Content updated successfully!";
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid form resubmission
        exit();
    } else {
        error_log("Update failed."); // Log failure
        echo "<script>alert('Failed to update content.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-content-page</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-cms.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<!-- sweet alert -->

<body>
    <?php if (isset($_SESSION['message'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?= $_SESSION['message'] ?>',
        timer: 2000,
        showConfirmButton: false
    });
    </script>
    <?php unset($_SESSION['message']);
     endif;  ?>



    <?php require '../../sidebar/sidebar.html' ?>

    <main id="content">
        <h2 class="cms_Head">Content Manager</h2>
            <div class="top-content">
                <h2>Top Content</h2>
                <form method="POST">
                    <label for="Page_1_head">Header 1:</label>
                    <textarea name="Page_1_head" id="head1" rows="2"><?= getValue($cmsData, "Page_1_head") ?></textarea>

                    <label for="Page_1_text">Text:</label>
                    <textarea name="Page_1_text" id="text-content1" rows="5"><?= getValue($cmsData, "Page_1_text") ?></textarea>

                    <label for="Page_2_head">Header 2:</label>
                    <textarea name="Page_2_head" id="head2" rows="2"><?= getValue($cmsData, "Page_2_head") ?></textarea>

                    <label for="Page_2_text">Text:</label>
                    <textarea name="Page_2_text" id="text-content2" rows="5"><?= getValue($cmsData, "Page_2_text") ?></textarea>

                    <label for="Page_3_head">Header 3:</label>
                    <textarea name="Page_3_head" id="head3" rows="2"><?= getValue($cmsData, "Page_3_head") ?></textarea>

                    <label for="Page_3_text">Text:</label>
                    <textarea name="Page_3_text" id="text-content3" rows="5"><?= getValue($cmsData, "Page_3_text") ?></textarea>
                    <button type="submit" name="update" class="submit-btn">Update Content</button>

               </form>
            </div>
            <div class="join_us">
            <h2>Join Us Content</h2>
            <form method="POST">
                    <label for="Join_Us_text">Join Us Text:</label> 
                    <textarea name="Join_Us_text" id="text-content3" rows="5"><?= getValue($cmsData, "Join_Us_text") ?></textarea>
                    <button type="submit" name="update" class="submit-btn">Update Content</button>
            </form>
            </div>
            <div class="footer">
            <h2>Footer Content</h2>
                <form method="POST">
                    <label for="footer_location">Location:</label> 
                    <textarea name="footer_location" id="text-content3" rows="4"><?= getValue($cmsData, "footer_location") ?></textarea>
                    <label for="footer_email">Email:</label> 
                    <textarea name="footer_email" id="text-content3" rows="4"><?= getValue($cmsData, "footer_email") ?></textarea>
                    <label for="footer_contact">Contact:</label> 
                    <textarea name="footer_contact" id="text-content3" rows="4"><?= getValue($cmsData, "footer_contact") ?></textarea>
                    <label for="footer_link_fb">Facebook Link:</label> 
                    <textarea name="footer_link_fb" id="text-content3" rows="4"><?= getValue($cmsData, "footer_link_fb") ?></textarea>
                    <label for="footer_link_twitter">Twitter Link:</label>
                    <textarea name="footer_link_twitter" id="text-content3" rows="4"><?= getValue($cmsData, "footer_link_twitter") ?></textarea>
                    <label for="footer_link_instagram">Instagram Link:</label>
                    <textarea name="footer_link_instagram" id="text-content3" rows="4"><?= getValue($cmsData, "footer_link_instagram") ?></textarea>                    
                    <button type="submit" name="update" class="submit-btn">Update Content</button>
                </form>
            </div>
    </main>
</body>
<script>
   
</script>

</html>