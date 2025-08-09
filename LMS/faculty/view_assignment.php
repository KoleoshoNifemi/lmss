<?php
session_start();

if (!isset($_SESSION['faculty_id'])) {  
    header("Location: faculty_login.php");
    exit();
}

require ('../config.php');
$stmt = $conn->prepare("SELECT profile_picture FROM faculty WHERE faculty_id = :faculty_id");
$stmt->execute(['faculty_id' => $_SESSION['faculty_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png'; // Use a default image if no profile picture is set
$message = "";

// Fetch available years
$year_stmt = $conn->query("SELECT DISTINCT year FROM years ORDER BY year ASC");
$years = $year_stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 1: Select Year
$selected_year = $_POST['year'] ?? '';
$subjects = [];

if (!empty($selected_year)) {
    // Fetch available subjects for the selected year
    $subject_stmt = $conn->prepare("SELECT DISTINCT subject_name FROM subject WHERE year = :year ORDER BY subject_name ASC");
    $subject_stmt->execute(['year' => $selected_year]);
    $subjects = $subject_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Step 2: Select Subject and Fetch Students
$students = [];
if (!empty($_POST['subject'])) {
    $selected_subject = $_POST['subject'];

    $stmt = $conn->prepare("
        SELECT s.full_name, s.matric_number, 
               a.file_path, a.upload_date 
        FROM student s
        LEFT JOIN assignment_submissions a ON s.matric_number = a.matric_number
        LEFT JOIN upload_assignment u ON a.assignment_id = u.id AND u.subject_name = :subject
        WHERE s.year = :year
        ORDER BY s.full_name ASC
    ");
    $stmt->execute(['year' => $selected_year, 'subject' => $selected_subject]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($students)) {
        $message = "No students found in the selected year.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submitted Assignments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px gray;
        }

        h2 {
            text-align: center;
            color: #004080;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            background-color: #f9f9f9;
        }

        button {
            background-color: #004080;
            color: white;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #003366;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #004080;
            color: white;
        }

        .message {
            text-align: center;
            font-size: 16px;
            color: red;
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
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
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

<div class="container">
    <h2>View Submitted Assignments</h2>

    <!-- Step 1: Select Year -->
    <form method="POST">
        <div class="form-group">
            <label for="year">Select Year:</label>
            <select name="year" required onchange="this.form.submit()">
                <option value="">-- Select Year --</option>
                <?php foreach ($years as $year) : ?>
                    <option value="<?= htmlspecialchars($year['year']) ?>" <?= isset($selected_year) && $selected_year == $year['year'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($year['year']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (!empty($selected_year) && !empty($subjects)): ?>
        <!-- Step 2: Select Subject -->
        <form method="POST">
            <input type="hidden" name="year" value="<?= htmlspecialchars($selected_year) ?>">
            <div class="form-group">
                <label for="subject">Select Subject:</label>
                <select name="subject" required onchange="this.form.submit()">
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($subjects as $subject) : ?>
                        <option value="<?= htmlspecialchars($subject['subject_name']) ?>" <?= isset($selected_subject) && $selected_subject == $subject['subject_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['subject_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    <?php endif; ?>

    <p class="message"><?php echo $message; ?></p>

    <?php if (!empty($students)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Matric Number</th>
                    <th>Submission Status</th>
                    <th>View Assignment</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['matric_number']); ?></td>
                        <td>
                            <?php echo !empty($student['file_path']) ? '<span style="color:green;">Submitted</span>' : '<span style="color:red;">Not Submitted</span>'; ?>
                        </td>
                        <td>
                            <?php if (!empty($student['file_path'])) : ?>
                                <a href="download.php?file=<?php echo urlencode($student['file_path']); ?>" target="_blank">View</a>
                            <?php else : ?>
                                <span style="color:gray;">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo !empty($student['upload_date']) ? htmlspecialchars($student['upload_date']) : '<span style="color:gray;">N/A</span>'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
