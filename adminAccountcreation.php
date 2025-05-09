<?php
session_start();

require 'dbConnCode.php'; // Database connection file

$error = ''; // To store error messages
$success = ''; // To store success messages

// Check if the role is provided in the session
if (!isset($_SESSION['role'])) {
    $error = 'Role not specified. Please restart the process.';
} else {
    $role = $_SESSION['role']; // Retrieve role from session
}

// Check if the email is provided in the session
if (!isset($_SESSION['email'])) {
    $error = 'No email found in session. Please restart the process.';
} else {
    $email = $_SESSION['email'];

    // Initialize failed attempts if not set
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0; // Start attempts at 0
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve and sanitize inputs
        $verificationCode = trim($_POST['verificationCode']);

       
        // Check if the user has exceeded 5 failed attempts
        if ($_SESSION['failed_attempts'] >= 5) {
            $_SESSION['is_max_attempts_reached'] = true;
            // Display message before redirecting
            // Delete the user row from the database
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE temporaryemailholder = ?");
            $deleteStmt->bind_param("s", $email);
            $deleteStmt->execute();

            // Destroy the session before redirecting
            session_destroy();
        } else {
            $_SESSION['is_max_attempts_reached'] = false;
            // Check if the verification code matches the one in the database
            $stmt = $conn->prepare("SELECT verification_code FROM users WHERE temporaryemailholder = ?");
            $stmt->bind_param("s", $email); // Bind the email parameter
            $stmt->execute();
            $result = $stmt->get_result(); // Get the result set

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc(); // Fetch the row
                if ($row['verification_code'] === $verificationCode) {
                    // Reset failed attempts on success
                    $_SESSION['failed_attempts'] = 0;

                    // Update the user record and set the email, verification_code to NULL
                    $updateStmt = $conn->prepare("UPDATE users SET email = ?, verification_code = NULL, temporaryemailholder = NULL WHERE temporaryemailholder = ?");
                    $updateStmt->bind_param("ss", $email, $email); // Bind the email and temporary email
                    if ($updateStmt->execute()) {
                        // Get the user ID using the email
                        $userIdStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                        $userIdStmt->bind_param("s", $email);
                        $userIdStmt->execute();
                        $userIdResult = $userIdStmt->get_result();

                        if ($userIdResult && $userIdResult->num_rows > 0) {
                            $userRow = $userIdResult->fetch_assoc();
                            $userId = $userRow['id']; // Get the user ID

                            // Determine role_id based on role name
                            $roleIdStmt = $conn->prepare("SELECT id FROM roles WHERE name = ?");
                            $roleIdStmt->bind_param("s", $role);
                            $roleIdStmt->execute();
                            $roleIdResult = $roleIdStmt->get_result();

                            if ($roleIdResult && $roleIdResult->num_rows > 0) {
                                $roleRow = $roleIdResult->fetch_assoc();
                                $roleId = $roleRow['id']; // Get the role ID

                                // Insert into user_roles table
                                $insertRoleStmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                                $insertRoleStmt->bind_param("ii", $userId, $roleId); // Bind user ID and role ID
                                if ($insertRoleStmt->execute()) {
                                    $success = 'Account created successfully! Wait for the account activation.';
                                    // Unset session variables
                                    unset($_SESSION['email'], $_SESSION['password'], $_SESSION['role']);
                                } else {
                                    $error = 'Error assigning role. Please try again.';
                                }
                            } else {
                                $error = 'Invalid role specified.';
                            }
                        } else {
                            $error = 'Error retrieving user ID. Please try again.';
                        }
                    } else {
                        $error = 'Error creating account. Please try again.';
                    }
                } else {
                    // Increment failed attempts on wrong verification code
                    $_SESSION['failed_attempts'] += 1;
                    $error = 'Invalid verification code. Attempt ' . $_SESSION['failed_attempts'] . ' of 5.';
                }
            } else {
                $error = 'Invalid email or verification code.';
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
    <title>Admin Account Creation</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        button {
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Admin Account</h2>

    <!-- Display error or success messages -->
    <script>
    // Check if there's an error or success message to display
    const errorMessage = <?php echo json_encode($error); ?>;
    const successMessage = <?php echo json_encode($success); ?>;
    const isMaxAttemptsReached = <?php echo json_encode($_SESSION['is_max_attempts_reached'] ?? false); ?>; // Check the session variable

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: successMessage,
        }).then(() => {
            // Redirect to login or another page after 3 seconds
           
                window.location.href = 'login.php'; // Change to your desired redirect URL
           
        });
    }

    if (errorMessage) {
        if (isMaxAttemptsReached) {
            // Display message for too many attempts without an OK button
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Too many attempts. Please try again later.',
                showCancelButton: false, // Prevent showing buttons
            }).then(() => {
                // Redirect immediately
                window.location.href = 'superUsersignup.php'; // Redirect to the signup page
            });
        } else {
            // Display regular error message for invalid verification code
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
            });
        }
    }

    // Optionally, if too many attempts were reached when the page loads
    if (isMaxAttemptsReached) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Too many attempts. Please try again later.',
            showCancelButton: false, // Prevent showing buttons
        }).then(() => {
            // Redirect immediately
            window.location.href = 'superUsersignup.php'; // Redirect to the signup page
        });
    }
</script>


    <!-- Form for account creation -->
    <form method="POST" action="">
        <label for="verificationCode">Verification Code:</label>
        <input type="text" id="verificationCode" name="verificationCode" required placeholder="Enter the code sent to your email">

        <button type="submit">Create Account</button>
    </form>
</div>

</body>
</html>
