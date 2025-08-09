<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    die("Access denied. Please log in as a student.");
}

if (!isset($_GET['subject'])) {
    die("Invalid subject.");
}

$subject = urldecode($_GET['subject']);

// Fetch materials for the selected subject
$stmt = $conn->prepare("SELECT subject, file_path, note, faculty_id, upload_date FROM upload_materials WHERE subject = :subject");
$stmt->bindParam(':subject', $subject);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <title>View Materials - <?php echo htmlspecialchars($subject); ?></title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            text-align: center;
            margin: 50px;
            margin-left: 320px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .materials {
            width: 100%;
            border-collapse: collapse;
        }

        .materials th, .materials td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .materials th {
            background: #007bff;
            color: white;
        }

        .materials tr:nth-child(even) {
            background: #f9f9f9;
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
    <h2>Materials for <?php echo htmlspecialchars($subject); ?></h2>
    <?php if ($materials): ?>
        <table class="materials">
            <tr>
                <th>Subject</th>
                <th>File</th>
                <th>Note</th>
                <th>Faculty</th>
                <th>Upload Date</th>
            </tr>
            <?php foreach ($materials as $material): 
                // Ensure correct file path
                $filePath = "faculty/uploads/" . basename($material['file_path']); 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($material['subject']); ?></td>
                    <td>
                        <?php if (file_exists($filePath)): ?>
                            <a href="<?php echo htmlspecialchars($filePath); ?>" download>Download</a> |
                            <a href="view_file.php?file=<?php echo urlencode($filePath); ?>" target="_blank">View</a>
                        <?php else: ?>
                            <span style="color: red;">File not found</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($material['note']); ?></td>
                    <td><?php echo htmlspecialchars($material['faculty_id']); ?></td>
                    <td><?php echo htmlspecialchars($material['upload_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No materials found for this subject.</p>
    <?php endif; ?>
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
