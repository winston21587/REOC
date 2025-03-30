<?php
// Start the session
session_start();

// Regenerate session ID to prevent fixation
if (!isset($_SESSION['user_id'])) {
    session_regenerate_id(true); // Regenerate session id on first visit
}

// Check if the user is logged in and if their role is 'Admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include('../dbConnCode.php');

// Handle form submission for adding a new college
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_college'])) {
    $college_name_and_color = mysqli_real_escape_string($conn, $_POST['college_name_and_color']);
    
    // Insert the new college into the database
    $insert_query = "INSERT INTO colleges (college_name_and_color) VALUES ('$college_name_and_color')";
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['message'] = 'College added successfully!';
        $_SESSION['message_type'] = 'success'; // Set message type
    } else {
        $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    // Redirect to avoid resubmitting on page reload
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle editing of a college
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_college'])) {
    $college_id = intval($_POST['college_id']);
    $college_name_and_color = mysqli_real_escape_string($conn, $_POST['college_name_and_color']);
    
    // Update the college name and color
    $update_query = "UPDATE colleges SET college_name_and_color = '$college_name_and_color' WHERE id = $college_id";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = 'College updated successfully!';
        $_SESSION['message_type'] = 'success'; // Set message type
    } else {
        $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }
    // Redirect to avoid resubmitting on page reload
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle deleting a college
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Delete the college from the database
    $delete_query = "DELETE FROM colleges WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['message'] = 'College deleted successfully!';
        $_SESSION['message_type'] = 'success'; // Set message type
    } else {
        $_SESSION['message'] = 'Error: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    // Redirect to avoid resubmitting on page reload
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch all colleges from the database
$query = "SELECT * FROM colleges";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">


<head>
<title>Admin-Colleges</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/admin-college.css">
<link rel="stylesheet" href="../sidebar/sidebar.css">
</head>

<body>

<?php require '../sidebar/sidebar.html' ?>
<main id="content">
<h1 class="vision2">Colleges</h1>
    <!-- Form to add a new college -->
    <div class="form-container" >
        <form method="POST">
            <label for="college_name_and_color">College Name and Color:</label>
            <input type="text" name="college_name_and_color" required>
            <button type="submit" name="add_college">Add College</button>
        </form>
    </div>


    <table>
        <tr class="table-header">
            <th>College Name and Color</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['college_name_and_color']); ?></td>
                <td class="actions">
                    <!-- Edit form -->
                    <form method="POST" class="action-form">
                        <input type="hidden" name="college_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="text" name="college_name_and_color" value="<?php echo htmlspecialchars($row['college_name_and_color']); ?>" required class="college-input">
                        <button type="submit" name="edit_college">Edit</button>
                    </form>
                   <!-- Delete button (with SweetAlert) -->
<a href="javascript:void(0);" onclick="deleteCollege(<?php echo htmlspecialchars($row['id']); ?>)">Delete</a>

                </td>
            </tr>
        <?php } ?>
    </table>

</main>
</body>
<!-- SweetAlert message -->
    <script>
        function deleteCollege(collegeId) {
        // Show SweetAlert for confirmation before deleting
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, perform the delete action
                window.location.href = "?delete_id=" + collegeId;
            }
        });
    }
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                icon: '<?php echo $_SESSION['message_type']; ?>',
                title: '<?php echo $_SESSION['message']; ?>',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                window.location.reload(); // Refresh the page to reflect changes
            });
        <?php endif; ?>
    </script>
</html>