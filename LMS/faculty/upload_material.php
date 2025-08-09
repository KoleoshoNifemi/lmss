<?php
session_start();
require('../config.php');

if (!isset($_SESSION['faculty_id'])) {
    die("Access denied. Please log in as faculty.");
}

$message = ""; // Store success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $subject = $_POST['subject'] ?? null;
        $note = $_POST['note'] ?? null;
        $year = $_POST['year'] ?? null;
        $faculty_id = $_SESSION['faculty_id'];

        if (empty($subject) || empty($note) || empty($year)) {
            throw new Exception("All fields are required.");
        }

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new Exception("Failed to create upload directory.");
        }

        if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $_FILES["file"]["error"]);
        }

        $fileName = basename($_FILES["file"]["name"]);
        $filePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file format. Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT.");
        }

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        $stmt = $conn->prepare("INSERT INTO upload_materials (faculty_id, subject, file_path, note, year) 
                                VALUES (:faculty_id, :subject, :file_path, :note, :year)");
        $stmt->bindParam(':faculty_id', $faculty_id);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':file_path', $filePath);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':year', $year);

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . implode(" | ", $stmt->errorInfo()));
        }

        $message = "Material uploaded successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch subjects from the `subject` table
$subjectOptions = "";
$stmt = $conn->query("SELECT subject_name FROM subject ORDER BY subject_name ASC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $subjectOptions .= "<option value='{$row['subject_name']}'>{$row['subject_name']}</option>";
}

// Fetch year options from the `years` table
$yearOptions = "";
$stmt = $conn->query("SELECT year FROM years ORDER BY year DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $yearOptions .= "<option value='{$row['year']}'>{$row['year']}</option>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material</title>
    <link rel="stylesheet" href="../CSS/faculty_styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 15px;
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

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
    <script>
        function showMessage() {
            var msg = "<?php echo $message; ?>";
            if (msg !== "") {
                alert(msg);
            }
        }
        window.onload = showMessage;
    </script>
</head>
<body>

<div class="form-container">
    <h2>Upload Material</h2>
    <form action="upload_material.php" method="POST" enctype="multipart/form-data">
        <label for="subject">Subject:</label>
        <select name="subject" id="subject" required>
            <option value="">Select Subject</option>
            <?php echo $subjectOptions; ?>
        </select>

        <label for="file">Upload File:</label>
        <input type="file" name="file" id="file" required>

        <label for="note">Note:</label>
        <textarea name="note" id="note" rows="4" required></textarea>

        <label for="year">Year:</label>
        <select name="year" id="year" required>
            <option value="">Select Year</option>
            <?php echo $yearOptions; ?>
        </select>

        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>
