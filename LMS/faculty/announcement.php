<?php
session_start();
require('../config.php');

if (!isset($_SESSION['faculty_id'])) {
    die("Access denied. Please log in as faculty.");
}

$faculty_id = $_SESSION['faculty_id'];

// Fetch available years for dropdown
$yearOptions = "";
$stmt = $conn->query("SELECT year FROM years ORDER BY year DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $yearOptions .= "<option value='{$row['year']}'>{$row['year']}</option>";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $announcement = $_POST['announcement'] ?? null;
        $year = $_POST['year'] ?? null;

        if (empty($announcement) || empty($year)) {
            throw new Exception("All fields are required.");
        }

        $stmt = $conn->prepare("INSERT INTO announcements (faculty_id, announcement, year) 
                                VALUES (:faculty_id, :announcement, :year)");

        $stmt->bindParam(':faculty_id', $faculty_id);
        $stmt->bindParam(':announcement', $announcement);
        $stmt->bindParam(':year', $year);

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . implode(" | ", $stmt->errorInfo()));
        }

        echo "<p style='color: green;'>Announcement posted successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            flex-direction: column;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Post Announcement</h2>
    <form method="POST" action="announcement.php">
        <label for="announcement">Announcement:</label>
        <textarea name="announcement" id="announcement" required></textarea>

        <label for="year">Select Year:</label>
        <select name="year" id="year" required>
            <option value="">-- Select Year --</option>
            <?php echo $yearOptions; ?>
        </select>

        <button type="submit">Post Announcement</button>
    </form>
</div>

</body>
</html>
