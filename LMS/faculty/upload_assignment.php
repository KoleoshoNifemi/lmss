<?php
session_start();
require '../config.php';

if (!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}

$message = ""; // To store success or error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = $_SESSION['faculty_id'];
    $subject_name = $_POST['subject_name'];
    $note = $_POST['note'];
    $due_date = $_POST['due_date'];
    $year = $_POST['year'];

    if (!empty($_FILES['file']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        $filePath = $targetDir . $fileName;
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

        // Allowed file types
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'avi'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO upload_assignment (faculty_id, subject_name, file_name, file_path, note, due_date, year) 
                                        VALUES (:faculty_id, :subject_name, :file_name, :file_path, :note, :due_date, :year)");
                $stmt->bindParam(':faculty_id', $faculty_id);
                $stmt->bindParam(':subject_name', $subject_name);
                $stmt->bindParam(':file_name', $fileName);
                $stmt->bindParam(':file_path', $filePath);
                $stmt->bindParam(':note', $note);
                $stmt->bindParam(':due_date', $due_date);
                $stmt->bindParam(':year', $year);

                if ($stmt->execute()) {
                    $message = "Assignment uploaded successfully!";
                } else {
                    $message = "Error uploading assignment.";
                }
            } else {
                $message = "File upload failed.";
            }
        } else {
            $message = "Invalid file type. Allowed: jpg, png, jpeg, gif, pdf, doc, docx, txt, mp4, avi.";
        }
    } else {
        $message = "Please select a file to upload.";
    }
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
    <title>Upload Assignment</title>
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
    <h2>Upload Assignment</h2>
    <form action="upload_assignment.php" method="POST" enctype="multipart/form-data">
        <label for="subject_name">Subject Name:</label>
        <select name="subject_name" required>
        <option value="Maths">Maths</option>
        <option value="English">English</option>
        <option value="OOP">OOP</option>
        <option value="Java">Java</option>
        </select>

        <label for="file">Upload Document (Video/Image/File):</label>
        <input type="file" name="file" id="file" required>

        <label for="note">Note:</label>
        <textarea name="note" id="note" rows="4" required></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required>

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
