<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT profile_picture FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $_SESSION['matric_number']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_picture = $user['profile_picture'] ?? 'default-profile.png';
$matric_number = $_SESSION['matric_number'];
$message = "";

// Fetch student's year
$stmt = $conn->prepare("SELECT year FROM student WHERE matric_number = :matric_number");
$stmt->execute(['matric_number' => $matric_number]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student data not found. Please contact admin.");
}

$student_year = $student['year'];

// Fetch assignments for the student's year
$stmt = $conn->prepare("SELECT * FROM upload_assignment WHERE year = :year");
$stmt->execute(['year' => $student_year]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle assignment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_assignment'])) {
    $assignment_id = $_POST['assignment_id'];

    // Check if the student has already uploaded this assignment
    $check_stmt = $conn->prepare("SELECT * FROM assignment_submissions WHERE matric_number = :matric_number AND assignment_id = :assignment_id");
    $check_stmt->execute([
        'matric_number' => $matric_number,
        'assignment_id' => $assignment_id
    ]);

    if ($check_stmt->rowCount() > 0) {
        $message = "You have already submitted this assignment.";
    } else {
        // File Upload Handling
        if (!empty($_FILES['assignment_file']['name'])) {
            $upload_dir = "uploads/assignments/";
            $file_name = time() . "_" . basename($_FILES["assignment_file"]["name"]);
            $target_file = $upload_dir . $file_name;
            $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'png', 'txt'];
            if (!in_array($file_ext, $allowed_types)) {
                $message = "Invalid file type. Allowed: PDF, DOC, DOCX, JPG, PNG, TXT.";
            } else {
                if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO assignment_submissions (matric_number, assignment_id, file_path) VALUES (:matric_number, :assignment_id, :file_path)");
                    $stmt->execute([
                        'matric_number' => $matric_number,
                        'assignment_id' => $assignment_id,
                        'file_path' => $file_name
                    ]);

                    $message = "Assignment submitted successfully!";
                } else {
                    $message = "Error uploading file.";
                }
            }
        } else {
            $message = "Please select a file to upload.";
        }
    }
}

// Fetch student's submitted assignments
$submitted_stmt = $conn->prepare("SELECT assignment_id FROM assignment_submissions WHERE matric_number = :matric_number");
$submitted_stmt->execute(['matric_number' => $matric_number]);
$submitted_assignments = $submitted_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 40px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    p {
        text-align: center;
        color: #d9534f;
        font-size: 16px;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #007bff;
        color: white;
        text-transform: uppercase;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .download-btn {
        display: inline-block;
        padding: 8px 12px;
        background: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
    }

    .download-btn:hover {
        background: #218838;
    }

    .upload-form {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    input[type="file"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: #f9f9f9;
    }

    button {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        background: #007bff;
        color: white;
        cursor: pointer;
        font-size: 14px;
    }

    button:hover {
        background: #0056b3;
    }

    .status-submitted {
        color: green;
        font-weight: bold;
    }

    .status-pending {
        color: red;
        font-weight: bold;
    }

    .due-date-warning {
        color: red;
        font-weight: bold;
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
    <div class="container">
    <h2>Assignments</h2>
    <p><?php echo $message; ?></p>

    <?php if (empty($assignments)) : ?>
        <p>No assignments available for your year.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Note</th>
                    <th>Due Date</th>
                    <th>Download</th>
                    <th>Status</th>
                    <th>Upload Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment) : ?>
                    <?php
                    $is_due = (date('Y-m-d') == $assignment['due_date']) ? "<span class='due-date-warning'>This assignment is due</span>" : "";
                    $is_submitted = in_array($assignment['id'], $submitted_assignments) ? "<span class='status-submitted'>Submitted</span>" : "<span class='status-pending'>Not Submitted</span>";
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['note']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['due_date']) . " $is_due"; ?></td>
                        <td><a class="download-btn" href="download.php?file=<?php echo urlencode($assignment['file_path']); ?>" target="_blank">Download</a></td>
                        <td><?php echo $is_submitted; ?></td>
                        <td>
                            <?php if (!in_array($assignment['id'], $submitted_assignments)) : ?>
                                <form class="upload-form" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                    <input type="file" name="assignment_file" required>
                                    <button type="submit" name="submit_assignment">Upload</button>
                                </form>
                            <?php else : ?>
                                <span class="status-submitted">Already Submitted</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
