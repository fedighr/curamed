<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "curamed");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $query = "SELECT * FROM utilisateur WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $verification_code = rand(1000, 9999);
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['email'] = $email;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'topfadighribi11@gmail.com';
                $mail->Password = 'skbqackvjratqaxn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('topfadighribi11@gmail.com', 'CURAMED');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Verification Code';
                $mail->Body = 'Your verification code: <b>' . $verification_code . '</b>';
                $mail->send();
                $success = "Verification code sent to your email.";
                $step = 2;
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Email not found in our system.";
        }
    }
    elseif (isset($_POST["verification_code"])) {
        $entered_code = $_POST["verification_code"];
        if ($entered_code == $_SESSION['verification_code']) {
            $step = 3;
        } else {
            $error = "Invalid verification code.";
            $step = 2;
        }
    }
    elseif (isset($_POST["new_password"])) {
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        
        if ($new_password == $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $_SESSION['email'];
            $query = "UPDATE utilisateur SET mot_de_passe = '$hashed_password' WHERE email = '$email'";
            if (mysqli_query($conn, $query)) {
                session_destroy();
                header("Location: login.html");
                exit;
            } else {
                $error = "Error updating password.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - CURAMED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <style>
        .password-reset-card {
            max-width: 500px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: #16a085;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            width: 30%;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            background: #f0f0f0;
            color: #666;
        }
        .step.active {
            background: #16a085;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="password-reset-card card">
            <div class="card-header text-center">
                <h3 class="mb-0">Password Reset</h3>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <div class="step-indicator">
                    <div class="step <?= $step == 1 ? 'active' : '' ?>">1. Verify Email</div>
                    <div class="step <?= $step == 2 ? 'active' : '' ?>">2. Enter Code</div>
                    <div class="step <?= $step == 3 ? 'active' : '' ?>">3. New Password</div>
                </div>

                <?php if ($step == 1): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Verification Code</button>
                </form>

                <?php elseif ($step == 2): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">Verification Code</label>
                        <input type="text" class="form-control" id="verification_code" name="verification_code" required>
                        <small class="text-muted">Check your email for the verification code</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify Code</button>
                </form>

                <?php elseif ($step == 3): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Reset Password</button>
                </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="login.html" class="text-decoration-none">Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>