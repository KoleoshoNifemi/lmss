<?php
session_start();
require ('../config.php');

if (!isset($_SESSION['faculty_id'])) {  
    header("Location: faculty_login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$message = "";

// Fetch current faculty details
$stmt = $conn->prepare("SELECT full_name, email, profile_picture FROM faculty WHERE faculty_id = :faculty_id");
$stmt->execute(['faculty_id' => $faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

// Update Profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $profile_picture = $faculty['profile_picture'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $upload_dir . $file_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed_types)) {
            $message = "Invalid file type. Allowed: JPG, JPEG, PNG.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $file_name;
            } else {
                $message = "Error uploading profile picture.";
            }
        }
    }

    // Update database
    $update_stmt = $conn->prepare("UPDATE faculty SET full_name = :full_name, email = :email, profile_picture = :profile_picture WHERE faculty_id = :faculty_id");
    $update_stmt->execute([
        'full_name' => $full_name,
        'email' => $email,
        'profile_picture' => $profile_picture,
        'faculty_id' => $faculty_id
    ]);

    $message = "Profile updated successfully!";
}

// Change Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password
    $stmt = $conn->prepare("SELECT password FROM faculty WHERE faculty_id = :faculty_id");
    $stmt->execute(['faculty_id' => $faculty_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($old_password, $faculty['password'])) {
        $message = "Old password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE faculty SET password = :password WHERE faculty_id = :faculty_id");
        $update_stmt->execute([
            'password' => $hashed_password,
            'faculty_id' => $faculty_id
        ]);

        $message = "Password changed successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .profile-img {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-img img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #007bff;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar">
        <div class="navbar-container">
            <a href="home.php" class="navbar-logo">LMS</a>
            <ul class="navbar-menu">
                <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="courses.php"><i class="fas fa-book"></i> Courses</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="view_assignment.php"><i class="fas fa-file"></i> Assignments</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="profile.php" class="profile-link">
                        <div class="profile-picture">
                        <img src="uploads/<?php echo htmlspecialchars($faculty['profile_picture']); ?>" alt="Profile Picture">
                        </div>
                    </a>
                </li>
            </ul>
            <div class="navbar-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

<div class="container">
    <h2>Manage Profile</h2>
    <div class="message"><?php echo $message; ?></div>

    <div class="profile-img">
        <img src="uploads/<?php echo htmlspecialchars($faculty['profile_picture']); ?>" alt="Profile Picture">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($faculty['full_name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" required>

        <label for="profile_picture">Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

<div class="container">
    <h2>Change Password</h2>
    <form method="POST">
        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit" name="change_password">Change Password</button>
    </form>
</div>

</body>
</html>
