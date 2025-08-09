<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    die("Access denied. Please log in as a student.");
}

$matric_number = $_SESSION['matric_number'];

// Get student's year based on matric_number
$stmt = $conn->prepare("SELECT year FROM student WHERE matric_number = :matric_number");
$stmt->bindParam(':matric_number', $matric_number);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

$studentYear = $student['year'];

// Fetch subjects from `subject` table based on student's year
$stmt = $conn->prepare("SELECT subject_name FROM subject WHERE year = :year ORDER BY subject_name ASC");
$stmt->bindParam(':year', $studentYear);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's profile picture from the database
$stmt = $conn->prepare("SELECT profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $_SESSION['matric_number']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png'; // Default profile picture
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin: 50px;
            margin-left: 400px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .subject-list {
            list-style: none;
            padding: 0;
        }

        .subject-list li {
            background: #007bff;
            color: white;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .subject-list li:hover {
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
    <div class="container">
        <h2>My Courses</h2>
        <?php if (!empty($subjects)) : ?>
            <ul class="subject-list">
                <?php foreach ($subjects as $subject) : ?>
                    <li onclick="viewMaterials('<?php echo urlencode($subject['subject_name']); ?>')">
                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No subjects available for your year.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function viewMaterials(subjectName) {
        window.location.href = "view_materials.php?subject=" + encodeURIComponent(subjectName);
    }

    const mobileMenu = document.getElementById('mobile-menu');
    const navbarMenu = document.querySelector('.navbar-menu');

    mobileMenu.addEventListener('click', () => {
        navbarMenu.classList.toggle('active');
    });
</script>

</body>
</html>
