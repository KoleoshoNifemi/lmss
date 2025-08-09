<?php
require ('../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['year'])) {
    $year = $_POST['year'];

    $stmt = $conn->prepare("SELECT DISTINCT subject_name FROM subject WHERE year = :year ORDER BY subject_name ASC");
    $stmt->execute(['year' => $year]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<option value="">-- Select Subject --</option>';
    foreach ($subjects as $subject) {
        echo '<option value="'.htmlspecialchars($subject['subject_name']).'">'.htmlspecialchars($subject['subject_name']).'</option>';
    }
}
?>
