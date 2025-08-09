<?php
session_start();

if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}

// Fetch user's profile picture from the database
require('../config.php');
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
    <title>Eduguard - Home</title>
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #34495e;
        }

        /* Content Area */
        .content {
            margin-left: 260px;
            padding: 20px;
        }

        .profile-picture img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    

    <!-- Main Content Area -->
    <div class="content" id="main-content">

    
        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['faculty_id']); ?>!</h2>
        <p>Select an option from the sidebar.</p>
    </div>

    <!-- JavaScript for Loading Content -->
    <script>
        function loadContent(page) {
            const contentDiv = document.getElementById("main-content");
            fetch(page)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(err => console.error("Error loading content: ", err));
        }
    </script>

</body>
</html>
