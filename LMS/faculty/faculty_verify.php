<?php
session_start();
require ('../config.php');

if (!isset($_SESSION['email'])) {
    header("Location: faculty_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    // Fetch user's verification code
    $stmt = $conn->prepare("SELECT * FROM faculty WHERE email = :email");
    $stmt->execute(['email' => $_SESSION['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $code === $user['verification_code']) {
        // Clear verification code and mark as verified
        $stmt = $conn->prepare("UPDATE faculty SET verification_code = NULL, is_verified = 1 WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);

        $_SESSION['faculty_id'] = $user['faculty_id'];
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
    <title>Verify</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        h1 {
            color: #333;
        }
        .error {
            color: red;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Verify Code</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Enter 6-digit code:</label>
            <input type="text" name="code" required maxlength="6">
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>