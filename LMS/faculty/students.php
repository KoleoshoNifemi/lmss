<?php
session_start();
require('../config.php');

// Fetch available years for dropdown
$yearOptions = "";
$stmt = $conn->query("SELECT year FROM years ORDER BY year DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $yearOptions .= "<option value='{$row['year']}'>{$row['year']}</option>";
}

// Handle form submission
$students = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['year'])) {
    $selectedYear = $_POST['year'];

    $stmt = $conn->prepare("SELECT full_name, matric_number, email FROM student WHERE year = :year");
    $stmt->bindParam(':year', $selectedYear);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
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

        .table-container {
            margin-top: 20px;
            width: 80%;
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .no-data {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Select Year</h2>
    <form method="POST" action="students.php">
        <label for="year">Select Year:</label>
        <select name="year" id="year" required>
            <option value="">-- Select Year --</option>
            <?php echo $yearOptions; ?>
        </select>
        <button type="submit">Enter</button>
    </form>
</div>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['year'])): ?>
    <div class="table-container">
        <h2>Students for Year: <?= htmlspecialchars($_POST['year']) ?></h2>
        <?php if (!empty($students)): ?>
            <table>
                <tr>
                    <th>Full Name</th>
                    <th>Matric Number</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                        <td><?= htmlspecialchars($student['matric_number']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No students found for the selected year.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
