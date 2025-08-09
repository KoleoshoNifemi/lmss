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
    <title>LMS - Course</title>
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: calc(100vh - 63.5px);
            position: fixed;
            left: 0;
            top: 63.5px;
            background: rgb(238, 238, 240);
            padding-top: 70px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: black;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #0d28c0;
        }

        /* Content Area */
        .content-container {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
            height: calc(100vh - 63.5px);
        }

        .content-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        .profile-picture img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
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
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="view_assignment.php"><i class="fas fa-file"></i> Assignments</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
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

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="#" onclick="loadContent('upload_material.php')"><i class="fas fa-upload"></i> Upload Material</a>
        <a href="#" onclick="loadContent('upload_assignment.php')"><i class="fas fa-file"></i> Upload Assignment</a>
        <a href="#" onclick="loadContent('students.php')"><i class="fas fa-users"></i> Students</a>
        <a href="#" onclick="loadContent('announcement.php')"><i class="fas fa-bullhorn"></i> Announcement</a>
    </div>

    <!-- Main Content Area (Using an iframe) -->
    <div class="content-container">
        <iframe id="content-frame" class="content-frame" ></iframe>
    </div>

    <!-- JavaScript for Loading Content -->
    <script>
        function loadContent(page) {
            document.getElementById("content-frame").src = page;
        }
    </script>
</body>
</html>
