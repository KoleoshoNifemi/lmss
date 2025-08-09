<?php
session_start();

if (!isset($_SESSION['matric_number'])) {
    header("Location: login.php");
    exit();
}

// Fetch user's profile picture from the database
require 'config.php';
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
    <title>LMS - Home</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .home-content { text-align: center; padding: 20px; }
        .quick-access { display: flex; justify-content: space-around; margin-top: 20px; }
        .card { width: 30%; padding: 20px; background: #f9f9f9; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .featured-courses { margin-top: 40px; text-align: center; }
        .courses-container { display: flex; justify-content: center; gap: 20px; }
        .course-card { padding: 15px; background: #eef; border-radius: 8px; width: 200px; text-align: center; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
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
                <li><a href="favorites.php"><i class="fas fa-star"></i> Favorites</a></li>
                <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="assignments.php"><i class="fas fa-envelope"></i> Assignments</a></li>
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

    <!-- Homepage Content -->
    <div class="home-content">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['matric_number']); ?>!</h1>
        <p>Explore our courses and resources to enhance your learning experience.</p>
        <p>We help to build your dream</p> 
        
 
        <!-- Homepage images -->
        <div class="homepage-images">

            <img src="images/default-profile.png" alt=" "width="300" height="200"> 
            
    
        </div> 

        <p>The #1 learning platform in the world</p> 

        <!-- Quick Access Section -->
        <div class="quick-access">
            <div class="card">
                <i class="fas fa-book"></i>
                <h3><a href="courses.php">My Courses</a></h3>
                <p>Access your enrolled courses and learning materials.</p>
            </div>
            <div class="card">
                <i class="fas fa-tasks"></i>
                <h3><a href="assignments.php">Pending Assignments</a></h3>
                <p>Stay on top of your assignments and due dates.</p>
            </div>
            <div class="card">
                <i class="fas fa-bell"></i>
                <h3><a href="announcements.php">Latest Announcements</a></h3>
                <p>Stay informed with updates from your instructors.</p>
            </div>
        </div>

        <!-- Featured Courses -->
        <section class="featured-courses">
            <h2>Featured Courses</h2>
            <div class="courses-container">
                <div class="course-card">
                    <h3>Web Development</h3>
                    <p>Learn HTML, CSS, JavaScript, and more.</p>
                </div>
                <div class="course-card">
                    <h3>Data Science</h3>
                    <p>Explore Python, Machine Learning, and AI.</p>
                </div>
                <div class="course-card">
                    <h3>Cybersecurity</h3>
                    <p>Understand network security and ethical hacking.</p>
                </div>
            </div>
        </section>
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