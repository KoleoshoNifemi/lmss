<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - Learning Management System</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .btn {
            width: 200px;
            padding: 15px;
            margin: 10px;
            text-align: center;
            border-radius: 30px;
            font-size: 18px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            color: white;
            border: none;
            cursor: pointer;
        }
        .start-learning {
            background-color: #00FF99;
        }
        .get-started {
            background-color: #0099FF;
        }
        .login {
            background-color: #FF0000;
        }
    </style>
</head>
<body>

    <!-- LMS Logo -->
    <img src="images/lms_logo.png" alt="LMS Logo" class="logo">

    <!-- Buttons -->
    <a href="register.php" class="btn start-learning">Start Learning</a>
    <a href="register.php" class="btn get-started">Get started</a>
    <a href="login.php" class="btn login">Login</a>

</body>
</html>
