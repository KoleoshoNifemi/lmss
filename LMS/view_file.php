<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    die("Access denied. Please log in as a student.");
}
if (!isset($_GET['file'])) {
    die("<h2 style='color: red; text-align: center;'>Error: No file specified!</h2>");
}

// Decode and ensure correct file path
$file = urldecode($_GET['file']);
$filePath = __DIR__ . "/" . $file; // Adjusted to access correct directory

// Ensure the file exists
if (!file_exists($filePath)) {
    die("<h2 style='color: red; text-align: center;'>Error: File not found!</h2>");
}

// Get file extension
$fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

$allowedImages = ['jpg', 'jpeg', 'png', 'gif'];
$allowedVideos = ['mp4', 'webm', 'ogg'];
$allowedDocs = ['pdf'];

// Fetch user's profile picture from the database
$stmt = $conn->prepare("SELECT profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $_SESSION['matric_number']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png'; // Use a default image if no profile picture is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>View File</title>
    <style>
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
            text-align: center;
            
        }

        iframe, video, img {
            width: 100%;
            height: 80vh; /* Increased height for better viewing */
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .down a {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        a:hover {
            background: #0056b3;
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
                <li><a href="logout.php"><i class="fas fa-file"></i> Pages</a></li>
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
<div class="container">
    <h2>Viewing File</h2>
    
    <?php if (in_array($fileExt, $allowedDocs)): ?>
        <iframe src="<?php echo htmlspecialchars($file); ?>" frameborder="0"></iframe>

    <?php elseif (in_array($fileExt, $allowedVideos)): ?>
        <video controls>
            <source src="<?php echo htmlspecialchars($file); ?>" type="video/<?php echo $fileExt; ?>">
            Your browser does not support the video tag.
        </video>

    <?php elseif (in_array($fileExt, $allowedImages)): ?>
        <img src="<?php echo htmlspecialchars($file); ?>" alt="Image Preview">

    <?php else: ?>
        <p>Cannot preview this file type. Please <a href="<?php echo htmlspecialchars($file); ?>" download>Download Here</a>.</p>
    <?php endif; ?>

    <br>
    <div class="down" > 
        <a href="<?php echo htmlspecialchars($file); ?>" download>Download File</a>
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
