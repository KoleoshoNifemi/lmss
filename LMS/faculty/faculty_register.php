<?php
session_start();
require ('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $faculty_id = trim($_POST['faculty_id']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    //$year = trim($_POST['year']);
    $profile_picture = $_FILES['profile_picture'];

    // Validation
    if (empty($full_name) || empty($faculty_id) || empty($email) || empty($password) || empty($confirm_password) 
     ) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM faculty WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already registered.";
        } else {
            // Check if matric number already exists
            $stmt = $conn->prepare("SELECT * FROM faculty WHERE faculty_id = :faculty_id");
            $stmt->execute(['faculty_id' => $faculty_id]);    
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
                $stmt = $conn->prepare("INSERT INTO faculty (full_name, faculty_id, email, password, profile_picture) VALUES (:full_name, :faculty_id, :email, :password, :profile_picture)");
                $stmt->execute([
                    'full_name' => $full_name,
                    'faculty_id' => $faculty_id,
                    'email' => $email,
                    'password' => $hashed_password,
                    'profile_picture' => $profile_picture_path
                ]);

                $_SESSION['success'] = "Registration successful. Please login.";
                header("Location: faculty_login.php");
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
    <title>Faculty Registration</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Full Name:</label>
        <input type="text" name="full_name" required><br>
        <label>Faculty ID:</label>
        <input type="text" name="faculty_id" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*"><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="faculty_login.php">Login</a></p>
</body>
</html>
