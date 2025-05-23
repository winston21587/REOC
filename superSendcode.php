<?php
session_start();
require 'dbConnCode.php'; // Include your database connection file
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = ''; // Initialize error variable
$verificationCodeSent = false; // Track if code was sent

// Check if the role is provided in the URL
if (isset($_GET['role'])) {
    $role = htmlspecialchars($_GET['role']); // Sanitize the role parameter
} else {
    $role = 'Role not specified'; // Fallback if no role is provided
}

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to generate a 6-digit verification code
function generateVerificationCode() {
    return sprintf('%06d', mt_rand(100000, 999999));
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'CSRF token validation failed.';
        
    } else {
        // Validate the email input
        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format.';
            } else {
                // Validate passwords
                $password = trim($_POST['password']);
                $rePassword = trim($_POST['re_password']);

                if (empty($password) || empty($rePassword)) {
                    $error = 'Please fill in both password fields.';
                } elseif ($password !== $rePassword) {
                    $error = 'Passwords do not match.';
                } elseif (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters long.';
                } else {
                    // Hash the password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Generate verification code
                    $verificationCode = generateVerificationCode();

                    // Insert email and verification code into the database
                    $stmt = $conn->prepare("INSERT INTO users (verification_code, temporaryemailholder, password) VALUES (?, ?, ?)");
                    if ($stmt->execute([$verificationCode, $email, $hashedPassword])) {
                        // Store email in session
                        $_SESSION['email'] = $email;

                        // Send verification email
                        $mail = new PHPMailer(true);
                        try {
                            // Server settings
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'westkiria@gmail.com'; // Replace with your email
                            $mail->Password = 'qpktvouqahvubayd'; // Replace with your email password
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            // Recipients
                            $mail->setFrom('westkiria@gmail.com', 'West Kiria');
                            $mail->addAddress($email);

                            // Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Your Verification Code';
                            $mail->Body    = "Your verification code is: $verificationCode";

                            $mail->send();
                            $verificationCodeSent = true; // Mark that the code was sent
                        } catch (Exception $e) {
                            $error = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
                            error_log($error); // Log the error for debugging
                        }

                        // Redirect or notify that the email was sent
                        if ($verificationCodeSent) {
                            $_SESSION['role'] = $role; // Store the role in the session
                            header("Location: adminAccountcreation.php?role=" . urlencode($role));
                            exit;
                        }
                    } else {
                        $error = "Error inserting verification code into the database: " . implode(", ", $stmt->errorInfo());
                        error_log($error); // Log the error for debugging
                    }
                }
            }
        } else {
            $error = 'Please enter an email.';
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superuser Email Verification</title>
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
        .error {
            color: red;
        }
        .password-rules {
            font-size: 0.9em;
            color: #666;
        }
        .invalid {
            color: red;
        }
        .valid {
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Email Verification</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password">
        
        <label for="re_password">Re-enter Password:</label>
        <input type="password" id="re_password" name="re_password" required placeholder="Re-enter your password">
        
        <!-- Password rules message -->
        <div class="password-rules">
            Password must be at least 6 characters long, and include:
            <ul>
                <li id="length" class="invalid">At least 6 characters long</li>
                <li id="uppercase" class="invalid">At least one uppercase letter (A-Z)</li>
                <li id="lowercase" class="invalid">At least one lowercase letter (a-z)</li>
                <li id="number" class="invalid">At least one number (0-9)</li>
                <li id="special" class="invalid">At least one special character (!, @, #, etc.)</li>
            </ul>
        </div>

        <button type="submit">Send Verification Code</button>
    </form>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const rePasswordInput = document.getElementById('re_password');
    const lengthRule = document.getElementById('length');
    const uppercaseRule = document.getElementById('uppercase');
    const lowercaseRule = document.getElementById('lowercase');
    const numberRule = document.getElementById('number');
    const specialRule = document.getElementById('special');

    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;

        // Validate password length
        if (password.length >= 6) {
            lengthRule.classList.remove('invalid');
            lengthRule.classList.add('valid');
            lengthRule.textContent = '✓ At least 6 characters long';
        } else {
            lengthRule.classList.remove('valid');
            lengthRule.classList.add('invalid');
            lengthRule.textContent = 'X At least 6 characters long';
        }

        // Validate uppercase letter
        if (/[A-Z]/.test(password)) {
            uppercaseRule.classList.remove('invalid');
            uppercaseRule.classList.add('valid');
            uppercaseRule.textContent = '✓ At least one uppercase letter (A-Z)';
        } else {
            uppercaseRule.classList.remove('valid');
            uppercaseRule.classList.add('invalid');
            uppercaseRule.textContent = 'X At least one uppercase letter (A-Z)';
        }

        // Validate lowercase letter
        if (/[a-z]/.test(password)) {
            lowercaseRule.classList.remove('invalid');
            lowercaseRule.classList.add('valid');
            lowercaseRule.textContent = '✓ At least one lowercase letter (a-z)';
        } else {
            lowercaseRule.classList.remove('valid');
            lowercaseRule.classList.add('invalid');
            lowercaseRule.textContent = 'X At least one lowercase letter (a-z)';
        }

        // Validate number
        if (/\d/.test(password)) {
            numberRule.classList.remove('invalid');
            numberRule.classList.add('valid');
            numberRule.textContent = '✓ At least one number (0-9)';
        } else {
            numberRule.classList.remove('valid');
            numberRule.classList.add('invalid');
            numberRule.textContent = 'X At least one number (0-9)';
        }

        // Validate special character
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            specialRule.classList.remove('invalid');
            specialRule.classList.add('valid');
            specialRule.textContent = '✓ At least one special character (!, @, #, etc.)';
        } else {
            specialRule.classList.remove('valid');
            specialRule.classList.add('invalid');
            specialRule.textContent = 'X At least one special character (!, @, #, etc.)';
        }
    });
</script>

</body>
</html>
