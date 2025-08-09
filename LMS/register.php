<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $matric_number = trim($_POST['matric_number']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $year = trim($_POST['year']);
    $profile_picture = $_FILES['profile_picture'];

    // Validation
    if (empty($full_name) || empty($matric_number) || empty($email) || empty($password) || empty($confirm_password) || empty($year)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM student WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered.";
        } else {
            // Check if matric number already exists
            $stmt = $conn->prepare("SELECT * FROM student WHERE matric_number = :matric_number");
            $stmt->execute(['matric_number' => $matric_number]);    
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "Your matric number has already been registered. Kindly login.";
            } else {
                // Handle profile picture upload
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0775, true);
                }

                $profile_picture_path = 'default.png'; // Default image

                if ($profile_picture['error'] === UPLOAD_ERR_OK) {
                    $file_ext = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array(strtolower($file_ext), $allowed_exts)) {
                        $file_name = uniqid() . '.' . $file_ext;
                        $file_path = $upload_dir . $file_name;

                        if (move_uploaded_file($profile_picture['tmp_name'], $file_path)) {
                            $profile_picture_path = $file_name; // Store only filename
                        } else {
                            $_SESSION['error'] = "Failed to upload profile picture.";
                            header("Location: register.php");
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
                        header("Location: register.php");
                        exit();
                    }
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into database
                $stmt = $conn->prepare("INSERT INTO student (full_name, matric_number, email, password, year, profile_picture) VALUES (:full_name, :matric_number, :email, :password, :year, :profile_picture)");
                $stmt->execute([
                    'full_name' => $full_name,
                    'matric_number' => $matric_number,
                    'email' => $email,
                    'password' => $hashed_password,
                    'year' => $year,
                    'profile_picture' => $profile_picture_path
                ]);

                $_SESSION['success'] = "Registration successful. Please login.";
                header("Location: login.php");
                exit();
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
    <title>Register</title>
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
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
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
        .success {
            color: green;
            margin-bottom: 10px;
            font-size: 14px;
        }
        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            text-align: left;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
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
        .file-input {
            border: 1px dashed #ccc;
            padding: 10px;
            cursor: pointer;
            text-align: center;
        }
        .file-input:hover {
            border-color: #007BFF;
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
        <h1>Register</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Matric Number:</label>
            <input type="text" name="matric_number" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <label>Year:</label>
            <select name="year" required>
                <?php for ($i = 2020; $i <= date("Y"); $i++) {
                    echo "<option value='$i'>$i</option>";
                } ?>
            </select>

            <label>Profile Picture:</label>
            <input type="file" name="profile_picture" accept="image/*" class="file-input">

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

</body>
</html>
