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



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Users</title>
    <link rel="icon" type="image/x-icon" href="../../img/reoclogo1.jpg">
    <link rel="stylesheet" href="../../sidebar/sidebar.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="../../css/admin-users.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js" ></script>

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
      <h2>USERS</h2>
      <table id="myTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Mobile No.</th>
                <th>Account Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($admin->getUserInfo() as $user): ?>
            <tr>
                <td><?= clean($user['id']) ?></td>
                <td><?= clean($user['email']) ?></td>
                <td><?= clean($user['mobile_number']) ?></td>
                <td><?= clean($user['isActive'] == 1 ? 'Active' : 'Inactive') ?></td>
                <td><button>Disable</button></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </main>
</body>
<script>
$(document).ready(function() {
    $('#myTable').DataTable({
        "paging": true,          // Enables pagination
        "searching": true,       // Enables search box
        "ordering": true,        // Enables sorting
        "info": true,            // Shows "Showing X of Y entries"
        "lengthMenu": [5, 10, 25, 50],  // Controls entries per page
    });
});
</script>
</html>