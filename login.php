<?php
session_start();
require './class/loginClass.php';
require_once './class/loginClass.php';
$login = new Login();

$error = '';
$login_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $password = trim($_POST['password']);
        if ($login->fetchUser($email) != false) {
            $user = $login->fetchUser($email);
            if ($user['isActive'] == 1) {
                if (password_verify($password, $user['password'])) {
                    $userId = $user['id'];
                    $role = $login->FindRole($userId);
                    if ($role != false) {
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['role'] = $role;
                        $login_success = true;
                        if ($role == 'Admin') {
                            $redirect_page = 'Admin/Dashboard.php';
                        } elseif ($role == 'Reviewer') {
                            $redirect_page = 'reviewerHome.php';
                        } else {
                            $redirect_page = 'viewApplications.php';
                        }
                    }
                } else {
                    $error = 'Incorrect password.';
                }
            } else {
                $error = 'Your account is not yet activated. Please contact the REOC admin for account activation.';
            }
        } else {
            $error = 'No user found with this email.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Form</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/login1.css">
    <link rel="stylesheet" type="text/css" href="./css/login2.css">
    <link rel="icon" type="image/x-icon" href="./img/reoclogo1.jpg">
    <link rel="stylesheet" href="./css/LoginPhp.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-form-title" style="background-image: url(./img/wmsu5.jpg);">
                    <span class="login100-form-title-1">reoc-wmsu portal</span>
                    <h4 class="sign">LOG IN </h4>
                </div>
                <?php if ($error): ?>
                <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: '<?php echo $error; ?>'
                });
                </script>
                <?php endif; ?>
                <form class="login100-form validate-form" method="POST" action="login.php">
                    <div class="wrap-input100 validate-input m-b-26" data-validate="Username is required">
                        <span class="label-input100">Email Address</span>
                        <input class="input100" type="email" id="email" name="email" placeholder="Enter Email Address">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-18" data-validate="Password is required">
                        <span class="label-input100">Password</span>
                        <input class="input100" type="password" id="password" name="password" placeholder="Enter password">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="p-b-30 resetP">
                        <div>
                            <a href="forgotPasswordRequest.php" class="txt1">Forgot Password</a>
                        </div>
                    </div>
                    <div class="container-login100-form-btn2">
                        <button class="login100-form-btn2" type="submit">Log In</button>
                        <a href="Signup.php" class="txt2">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/daterangepicker/daterangepicker.js"></script>
    <script src="vendor/countdowntime/countdowntime.js"></script>
    <script src="./js/fonts.js"></script>
    <?php if ($login_success): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome, <?php echo $_SESSION['role']; ?>!',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = '<?php echo $redirect_page; ?>';
    });
    </script>
    <?php endif; ?>
</body>
</html>