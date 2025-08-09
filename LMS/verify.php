<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    // Fetch user's verification code
    $stmt = $conn->prepare("SELECT * FROM student WHERE email = :email");
    $stmt->execute(['email' => $_SESSION['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $code === $user['verification_code']) {
        // Clear verification code and mark as verified
        $stmt = $conn->prepare("UPDATE student SET verification_code = NULL, is_verified = 1 WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);

        $_SESSION['matric_number'] = $user['matric_number'];
        header("Location: home.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
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
            text-align: center;
            letter-spacing: 2px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #218838;
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
    </style>
</head>
<body>

    <div class="container">
    <img src="images/lms_logo.png" alt="LMS Logo" class="logo">
        <h1>Verify Code</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Enter 6-digit code:</label>
            <input type="text" name="code" required maxlength="6" pattern="\d{6}" title="Enter a 6-digit code">
            
            <button type="submit">Verify</button>
        </form>

        <p>Didn't receive the code? <a href="resend.php">Resend Code</a></p>
    </div>

</body>
</html>
