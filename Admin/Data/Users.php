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
include('../../dbConnCode.php');  // Assuming you have a db connection file

// Handle activation/deactivation request
if (isset($_GET['toggle_id'])) {
    $user_id = $_GET['toggle_id'];
    // Toggle the isActive status
    $current_status_query = "SELECT isActive FROM users WHERE id = ?";
    $stmt = $conn->prepare($current_status_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // If currently active, set to inactive (0), else set to active (1)
    $new_status = $row['isActive'] == 1 ? 0 : 1;

    $update_query = "UPDATE users SET isActive = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ii', $new_status, $user_id);
    $stmt->execute();

    // Redirect back to the same page to reflect changes
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle mobile number update
if (isset($_POST['update_mobile'])) {
    $user_id = $_POST['user_id'];
    $new_mobile_number = $_POST['mobile_number'];

    // Update the mobile number in the database
    $update_mobile_query = "UPDATE researcher_profiles SET mobile_number = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_mobile_query);
    $stmt->bind_param('si', $new_mobile_number, $user_id);
    $stmt->execute();

    // Redirect back to the same page after updating with a query string to trigger the SweetAlert
    header('Location: ' . $_SERVER['PHP_SELF'] . '?mobile_update=success');
    exit;
}

// Fetch all users with their mobile numbers
$sql = "
    SELECT u.id, u.email, u.isActive, rp.mobile_number
    FROM users AS u
    LEFT JOIN researcher_profiles AS rp ON u.id = rp.user_id
    LEFT JOIN user_roles AS ur ON u.id = ur.user_id
    LEFT JOIN roles AS r ON ur.role_id = r.id
    WHERE u.email != '' AND u.email IS NOT NULL
    AND r.name = 'Researcher';
";

$result = mysqli_query($conn, $sql);


$total_users_query = "
    SELECT COUNT(*) AS total_users
    FROM users AS u
    LEFT JOIN user_roles AS ur ON u.id = ur.user_id
    LEFT JOIN roles AS r ON ur.role_id = r.id
    WHERE u.email != '' AND u.email IS NOT NULL
    AND r.name = 'Researcher';
";

$total_result = mysqli_query($conn, $total_users_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_users = $total_row['total_users'];



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
                <td class="actions">
                    <!-- Toggle activation/deactivation button -->
                    <a href="?toggle_id=<?php echo $user['id']; ?>">
                        <button class="disablebtn">
                            <?php echo $user['isActive'] == 1 ? 'Disable' : 'Activate'; ?>
                        </button>
                    </a>
                </td>
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

<script>
        // SweetAlert when toggling status
        <?php if (isset($_GET['toggle_id'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Account status updated',
                text: '<?php echo $row['isActive'] == 1 ? "Account has been activated." : "Account has been deactivated."; ?>',
                showConfirmButton: false,
                timer: 2000
            });
        <?php endif; ?>

        // SweetAlert when mobile number is updated
        <?php if (isset($_GET['mobile_update']) && $_GET['mobile_update'] == 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Mobile number updated successfully!',
                showConfirmButton: false,
                timer: 2000
            });
        <?php endif; ?>

        // Real-time search function
        function searchFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById('searchBox');
            filter = input.value.toUpperCase();
            table = document.getElementById('userTable');
            tr = table.getElementsByTagName('tr');
            
            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName('td')[0]; // Search by email column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        // Print table
        function printTable() {
            var printWindow = window.open('', '', 'height=600,width=800');

            // Get the total users count and inject it into the print window
            var totalUsers = "<?php echo $total_users; ?>";

            // Write the HTML to the print window, including styles and the total count of users
            printWindow.document.write('<html><head><title>Print User List</title>');
            printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; }');
            printWindow.document.write('th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }');
            printWindow.document.write('th { background-color: #800000; color: white; }');
            printWindow.document.write('tr:nth-child(even) { background-color: #f2f2f2; }</style></head><body>');
            printWindow.document.write('<h1>User List</h1>');
            printWindow.document.write('<p>Total Users: ' + totalUsers + '</p>');
            printWindow.document.write(document.getElementById('userTable').outerHTML);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.print();
        }
    </script>
</html>