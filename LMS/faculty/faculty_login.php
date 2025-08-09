<?php
session_start();
require ('../config.php');
require ('../vendor/autoload.php'); // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email || empty($password)) {
        $_SESSION['error'] = "Invalid email format or password is empty.";
    } else {
        // Fetch user from database
        $stmt = $conn->prepare("SELECT * FROM faculty WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Generate 6-digit verification code
            $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Save verification code in the database
            $stmt = $conn->prepare("UPDATE faculty SET verification_code = :code WHERE id = :id");
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
                $mail->setFrom($_ENV['SMTP_USER'], 'Your Name');
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
                
                header("Location: faculty_verify.php");
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
    <title>Faculty Login</title>
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
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        p {
            margin-top: 10px;
        }
        a {
            color: #007bff;
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
        <h1>Faculty Login</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
         <!-- Botton to myID login -->
  <div class="button-group">
    <!-- New button for external myid.php page -->
    <a href="login.html" class="login-btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
      <!-- <img src="images/myid-logo.png" alt="myID" class="btn-icon-rounded" /> -->
      <div>
        <span><strong>Login with myID</strong></span>
        <span style="color:rgb(41, 19, 186)"> <br>(MyID Page)</span>
      </div>
    </a>
  </div>
        
        <p>Don't have an account? <a href="faculty_register.php">Register</a></p>
    </div>
</body>
</html>
