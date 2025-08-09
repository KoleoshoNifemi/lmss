<?php
session_start();  // Start the session to retrieve session data

// Check if the student is logged in and the matric_number is set in the session
if (!isset($_SESSION['matric_number'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database configuration file
require 'config.php';
$stmt = $conn->prepare("SELECT profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $_SESSION['matric_number']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement</title>
    <link rel="stylesheet" href="CSS/styles.css">
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

<?php
try {
    // Fetch the student's year from the database based on the matric_number
    $matric_number = $_SESSION['matric_number'];

    $query = "SELECT year FROM student WHERE matric_number = :matric_number";
    $stmt = $conn->prepare($query);

    // Bind the matric_number parameter
    $stmt->bindParam(':matric_number', $matric_number, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Check if the student exists and fetch their year
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $student_year = $student['year'];  // Get the year from the student record
    } else {
        // If the student does not exist, redirect to an error or login page
        echo "Student not found!";
        exit();
    }

    // Prepare the SQL query to fetch announcements based on student's year
    $query = "SELECT faculty_id, announcement, created_at FROM announcements WHERE year = :year";
    $stmt = $conn->prepare($query);

    // Bind the year parameter
    $stmt->bindParam(':year', $student_year, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Check if there are any announcements
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Announcements for Year " . htmlspecialchars($student_year) . "</h1>";

    if ($announcements) {
        foreach ($announcements as $announcement) {
            echo "<div class='announcement'>";
            echo "<p><strong>Faculty ID:</strong> " . htmlspecialchars($announcement['faculty_id']) . "</p>";
            echo "<p><strong>Announcement:</strong> " . nl2br(htmlspecialchars($announcement['announcement'])) . "</p>";
            echo "<p><strong>Created:</strong> " . htmlspecialchars($announcement['created_at']) . "</p>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>No announcements for your year.</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
