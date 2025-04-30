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
$vms = $admin->showVM();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["save_mission"])) {
            $mission = clean($_POST['mission']);
            $admin->updateVM( $mission,1);
            $_SESSION['message'] = "Mission Updated Successfully!";

    }
        if (isset($_POST["save_vision"])) {
            $vision = clean($_POST['vision']);
            $admin->updateVM( $vision,2);
            $_SESSION['message'] = "Vision Updated Successfully!";

    }
        if (isset($_POST["save_goals"])) {
            $goals = clean($_POST['goals']);
            $admin->updateVM( $goals,3);
            $_SESSION['message'] = "Goals Updated Successfully!";

    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Mission/Vision</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-vm.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <h2>Mission Vission Manager</h2>
        <div class="vm-container">
            <div class="mission-container">
                <h3>MISSION</h3>
                <form method="POST">
                    <textarea  name="mission" id="mission" cols="30" rows="10"><?= $vms[0]['content'] ?></textarea>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="save_mission">Save</button>
                </form>
            </div>
            <div class="vision-container">
                <h3>VISION</h3>
                <form method="POST">
                    <textarea  name="vision" id="vision" cols="30" rows="10"><?= $vms[1]['content'] ?></textarea>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="save_vision">Save</button>
                </form>
            </div>
            <div class="goals-container">
                <h3>GOALS</h3>
                <form method="POST">
                    <textarea  name="goals" id="goals" cols="30" rows="10"><?= $vms[2]['content'] ?></textarea>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="save_goals">Save</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>