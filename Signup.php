<?php
session_start();
require 'dbConnCode.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$error = '';
$verificationCodeSent = false;
if (isset($_GET['role'])) {
    $role = htmlspecialchars($_GET['role']);
} else {
    $role = 'Researcher ';
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function generateVerificationCode() {
    return sprintf('%06d', mt_rand(100000, 999999));
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'CSRF token validation failed.';
    } else {
        if (isset($_POST['email']) && isset($_POST['mobile'])) {
            $email = trim($_POST['email']);
            $mobile = trim($_POST['mobile']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format.';
            } elseif (!preg_match('/^(\+639\d{9}|0\d{10})$/', $mobile)) {
                $error = 'Invalid mobile number format.';
            } else {
                if (strpos($mobile, '+63') === 0) {
                    $mobile = '0' . substr($mobile, 3);
                }
                $password = trim($_POST['password']);
                $rePassword = trim($_POST['re_password']);
                if (empty($password) || empty($rePassword)) {
                    $error = 'Please fill in both password fields.';
                } elseif ($password !== $rePassword) {
                    $error = 'Passwords do not match.';
                } elseif (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters long.';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $verificationCode = generateVerificationCode();
                    $stmt = $conn->prepare("INSERT INTO users (verification_code, temporaryemailholder, password) VALUES (?, ?, ?)");
                    if ($stmt->execute([$verificationCode, $email, $hashedPassword])) {
                        $_SESSION['email'] = $email;
                        $_SESSION['mobile'] = $mobile;
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'wbtester33@gmail.com';
                            $mail->Password = 'bljerhkjpgbkvjbv';
                            $mail->SMTPSecure = 'ssl';
                            $mail->Port = 465;
                            $mail->setFrom('wmsuREOC@gmail.com', 'Research Ethics Online Committee');
                            $mail->addAddress($email);
                            $mail->isHTML(true);
                            $mail->Subject = 'Your Verification Code';
                            $mail->Body = "Your verification code is: $verificationCode";
                            $mail->SMTPOptions = [
                                'ssl' => [
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                    'allow_self_signed' => true
                                ]
                            ];
                            $mail->send();
                            $verificationCodeSent = true;
                        } catch (Exception $e) {
                            $error = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
                            error_log($error);
                        }
                        if ($verificationCodeSent) {
                            $_SESSION['role'] = $role;
                            header("Location: researcherAccountcreation.php?role=" . urlencode($role));
                            exit;
                        }
                    } else {
                        $error = "Error inserting verification code into the database: " . $stmt->error;
                        error_log($error);
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
    <title>Sign In Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/login1.css">
    <link rel="stylesheet" type="text/css" href="./css/login2.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <link rel="stylesheet" href="./css/SignupPhp.css">
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-form-title" style="background-image: url(./img/wmsu5.jpg);">
                    <span class="login100-form-title-1">reoc-wmsu portal</span>
                    <h4 class="sign">SIGN UP</h4>
                </div>
                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="" class="login100-form validate-form">
                    <div class="wrap-input100 validate-input m-b-15" data-validate="Email Address is required">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <span class="label-input100">Email Address</span>
                        <input class="input100" type="email" id="email" name="email" required placeholder="Enter your email">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-15" data-validate="Contact Number is required">
                        <span class="label-input100">Contact Number</span>
                        <input class="input100" type="tel" id="mobile" name="mobile" required placeholder="Enter your mobile number" pattern="(\+639\d{9}|0\d{10})" title="Format: 09773106532 or +639773106532">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="mobile-rules">
                        <p class="Input_rules">Mobile number must:</p>
                        <ul>
                            <li id="mobile-length" class="invalid">Be exactly 11 digits long (without country code) or 13 characters long (with +63)</li>
                            <li id="mobile-format" class="invalid">Start with 0 or +639</li>
                        </ul>
                    </div>
                    <div class="wrap-input100 validate-input m-b-15" data-validate="Contact Number is required">
                        <span class="label-input100">Password</span>
                        <input class="input100" type="password" id="password" name="password" required placeholder="Enter your password">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-15" data-validate="Contact Number is required">
                        <span class="label-input100">Re-enter Password</span>
                        <input class="input100" type="password" id="re_password" name="re_password" required placeholder="Re-enter your password">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="password-rules">
                        <p class="Input_rules">Password must be at least 6 characters long, and include:</p>
                        <ul>
                            <li id="length" class="invalid">At least 6 characters long</li>
                            <li id="uppercase" class="invalid">At least one uppercase letter (A-Z)</li>
                            <li id="lowercase" class="invalid">At least one lowercase letter (a-z)</li>
                            <li id="number" class="invalid">At least one number (0-9)</li>
                            <li id="special" class="invalid">At least one special character (!, @, #, etc.)</li>
                        </ul>
                    </div>
                    <div class="flex-sb-m w-full p-b-15">
                        <div class="contact100-form-checkbox"></div>
                    </div>
                    <div class="container-login100-form-btn2">
                        <button class="login100-form-btn2" type="submit">Send Verification Code</button>
                    </div>
                </form>
                <script>
                    const passwordInput = document.getElementById('password');
                    const rePasswordInput = document.getElementById('re_password');
                    const lengthRule = document.getElementById('length');
                    const uppercaseRule = document.getElementById('uppercase');
                    const lowercaseRule = document.getElementById('lowercase');
                    const numberRule = document.getElementById('number');
                    const specialRule = document.getElementById('special');
                    const mobileInput = document.getElementById('mobile');
                    const mobileLengthRule = document.getElementById('mobile-length');
                    const mobileFormatRule = document.getElementById('mobile-format');
                    passwordInput.addEventListener('input', function() {
                        const password = passwordInput.value;
                        if (password.length >= 6) {
                            lengthRule.classList.remove('invalid');
                            lengthRule.classList.add('valid');
                            lengthRule.textContent = '✓ At least 6 characters long';
                        } else {
                            lengthRule.classList.remove('valid');
                            lengthRule.classList.add('invalid');
                            lengthRule.textContent = 'X At least 6 characters long';
                        }
                        if (/[A-Z]/.test(password)) {
                            uppercaseRule.classList.remove('invalid');
                            uppercaseRule.classList.add('valid');
                            uppercaseRule.textContent = '✓ At least one uppercase letter (A-Z)';
                        } else {
                            uppercaseRule.classList.remove('valid');
                            uppercaseRule.classList.add('invalid');
                            uppercaseRule.textContent = 'X At least one uppercase letter (A-Z)';
                        }
                        if (/[a-z]/.test(password)) {
                            lowercaseRule.classList.remove('invalid');
                            lowercaseRule.classList.add('valid');
                            lowercaseRule.textContent = '✓ At least one lowercase letter (a-z)';
                        } else {
                            lowercaseRule.classList.remove('valid');
                            lowercaseRule.classList.add('invalid');
                            lowercaseRule.textContent = 'X At least one lowercase letter (a-z)';
                        }
                        if (/\d/.test(password)) {
                            numberRule.classList.remove('invalid');
                            numberRule.classList.add('valid');
                            numberRule.textContent = '✓ At least one number (0-9)';
                        } else {
                            numberRule.classList.remove('valid');
                            numberRule.classList.add('invalid');
                            numberRule.textContent = 'X At least one number (0-9)';
                        }
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
                    mobileInput.addEventListener('input', function() {
                        const mobile = mobileInput.value;
                        if (mobile.length === 11 || (mobile.startsWith('+639') && mobile.length === 13)) {
                            mobileLengthRule.classList.remove('invalid');
                            mobileLengthRule.classList.add('valid');
                            mobileLengthRule.textContent = '✓ Be exactly 11 digits long (without country code) or 13 characters long (with +63)';
                        } else {
                            mobileLengthRule.classList.remove('valid');
                            mobileLengthRule.classList.add('invalid');
                            mobileLengthRule.textContent = 'X Be exactly 11 digits long (without country code) or 13 characters long (with +63)';
                        }
                        const mobileRegex = /^(0\d{10}|\+639\d{9})$/;
                        if (mobileRegex.test(mobile)) {
                            mobileFormatRule.classList.remove('invalid');
                            mobileFormatRule.classList.add('valid');
                            mobileFormatRule.textContent = '✓ Start with 0 or +639';
                        } else {
                            mobileFormatRule.classList.remove('valid');
                            mobileFormatRule.classList.add('invalid');
                            mobileFormatRule.textContent = 'X Start with 0 or +639';
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</body>
</html>