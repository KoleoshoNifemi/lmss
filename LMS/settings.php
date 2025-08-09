<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    header("Location: login.php");
    exit();
}

$matric_number = $_SESSION['matric_number'];
$message = "";

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, email, profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $matric_number]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$full_name = $user['full_name'];
$email = $user['email'];
$profile_picture = $user['profile_picture'] ?? 'default-profile.png';

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_full_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $profile_picture = $user['profile_picture']; // Keep the old picture by default

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $upload_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]); // Unique filename
        $target_file = $upload_dir . $file_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_types)) {
            $message = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF.";
        } else {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $file_name; // Save only the filename
                $_SESSION['profile_picture'] = $profile_picture; // Update session
            } else {
                $message = "Error uploading file.";
            }
        }
    }

    // Update profile in database
    $stmt = $conn->prepare("UPDATE student SET full_name = :full_name, email = :email, profile_picture = :profile_picture WHERE matric_number = :matric_number");
    $stmt->execute([
        'full_name' => $new_full_name,
        'email' => $new_email,
        'profile_picture' => $profile_picture,
        'matric_number' => $matric_number
    ]);

    $message = "Profile updated successfully!";
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch stored password
    $stmt = $conn->prepare("SELECT password FROM student WHERE matric_number = :matric_number");
    $stmt->execute(['matric_number' => $matric_number]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($old_password, $user['password'])) {
        $message = "Old password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
    } else {
        // Hash new password and update
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE student SET password = :password WHERE matric_number = :matric_number");
        $stmt->execute(['password' => $hashed_password, 'matric_number' => $matric_number]);

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
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .settings-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .settings-container h2,
        .settings-container h3 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 10px;
        }

        .btn {
            display: block;
            width: 100%;
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0056b3;
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
                <li><a href="favorites.php"><i class="fas fa-star"></i> Favorites</a></li>
                <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="assignments.php"><i class="fas fa-envelope"></i> Assignments</a></li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="profile.php" class="profile-link">
                        <div class="profile-picture">
                            <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>">
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

<div class="settings-container">
    <h2>Settings</h2>

    <?php if (!empty($message)) : ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Profile Update Form -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label>Profile Picture:</label>
            <input type="file" name="profile_picture">
        </div>

        <button type="submit" name="update_profile" class="btn">Save Changes</button>
    </form>

    <h3>Change Password</h3>

    <!-- Password Change Form -->
    <form method="POST">
        <div class="form-group">
            <label>Old Password:</label>
            <input type="password" name="old_password" required>
        </div>

        <div class="form-group">
            <label>New Password:</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="form-group">
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>
</div>

</body>
</html>

