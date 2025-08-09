<?php
session_start();
require 'config.php';
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email || empty($password)) {
        $_SESSION['error'] = "Invalid email format or password is empty.";
    } else {
        // Fetch user from database
        $stmt = $conn->prepare("SELECT * FROM student WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Generate 6-digit verification code
            $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Save verification code in the database
            $stmt = $conn->prepare("UPDATE student SET verification_code = :code WHERE id = :id");
            $stmt->execute(['code' => $verification_code, 'id' => $user['id']]);

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USER'];
                $mail->Password   = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port       = $_ENV['SMTP_PORT'];

                // Recipients
                $mail->setFrom($_ENV['SMTP_USER'], 'Learning management system');
                $mail->addAddress($email);

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Verification Code';
                $mail->Body    = "<p>Your verification code is: <strong>$verification_code</strong></p>";
                $mail->AltBody = "Your verification code is: $verification_code";

                // Send email
                $mail->send();

                // Store email in session for verification
                $_SESSION['email'] = $email;
                session_regenerate_id(true);
                
                header("Location: verify.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Email could not be sent. Please try again later.";
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .logo {
            width: 100px;
            margin-bottom: 15px;
        }   
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }
        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        p {
            margin-top: 15px;
            font-size: 14px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        
        .login-btn {
            background: #1abc34;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background:rgb(45, 189, 205); /* A darker green for hover */
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="container">
    <img src="images/lms_logo.png" alt="LMS Logo" class="logo">
        <h1>Login</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
             <!-- Botton to myID login -->
  <div class="button-group">
    <!-- New button for external login.html page -->
    <a href="login.html" class="login-btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
      <!-- <img src="images/myid-logo.png" alt="myID" class="btn-icon-rounded" /> -->
      <div>
        <span><strong>Login with myID</strong></span>
        <span style="color:rgb(41, 19, 186)"> <br>(MyID Page)</span>
      </div>
    </a>
  </div>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>

</body>
</html>
