<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['matric_number'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require 'config.php';

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, matric_number, email, year, profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $_SESSION['matric_number']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Check if profile picture exists
$profilePic = (!empty($user['profile_picture']) && file_exists('uploads/' . $user['profile_picture']))
    ? 'uploads/' . htmlspecialchars($user['profile_picture'])
    : 'uploads/default.png'; // Fallback image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Eduguard</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin-top: 20px;
            margin: 50px;
            margin-left: 400px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="home.php" class="navbar-logo">LMS</a>
            <ul class="navbar-menu">
                <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="courses.php"><i class="fas fa-book"></i> Courses</a></li>
                <li><a href="favorites.php"><i class="fas fa-info-circle"></i> Favorites</a></li>
                <li><a href="announcements.php"><i class="fas fa-file"></i> Announcements</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li>
                    <a href="profile.php" class="profile-link">
                        <div class="profile-picture">
                            <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
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

    <!-- Profile Content -->
    <div class="profile-content">
        <h1>Profile Details</h1>
        <div class="profile-details">
            <div class="profile-picture-large">
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
            </div>
            <div class="profile-info">
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                <p><strong>Matric Number:</strong> <?php echo htmlspecialchars($user['matric_number']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Year:</strong> <?php echo htmlspecialchars($user['year']); ?></p>
                <a href="logout.php">logout</a>
            </div>
        </div>
    </div>

    <!-- JavaScript for Mobile Menu -->
    <script>
        const mobileMenu = document.getElementById('mobile-menu');
        const navbarMenu = document.querySelector('.navbar-menu');

        mobileMenu.addEventListener('click', () => {
            navbarMenu.classList.toggle('active');
        });
    </script>
</body>
</html>
