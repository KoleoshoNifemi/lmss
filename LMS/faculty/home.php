<?php
session_start();

if (!isset($_SESSION['faculty_id'])) {  
    header("Location: faculty_login.php");
    exit();
}

// Fetch user's profile picture from the database
require ('../config.php');
$stmt = $conn->prepare("SELECT profile_picture FROM faculty WHERE faculty_id = :faculty_id");
$stmt->execute(['faculty_id' => $_SESSION['faculty_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png'; // Use a default image if no profile picture is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS - Home</title>
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="home.php" class="navbar-logo">LMS</a>
            <ul class="navbar-menu">
                <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="courses.php"><i class="fas fa-book"></i> Courses</a></li>
                
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

    <!-- Homepage Content -->
    <div class="home-content">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['faculty_id']); ?>!</h1>
        <!--<p>Explore our courses and resources to enhance your learning experience.</p> -->
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