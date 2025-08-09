<?php
session_start();
require('config.php');

if (!isset($_SESSION['matric_number']) || !isset($_POST['file_path'])) {
    die("Invalid request.");
}

$matric_number = $_SESSION['matric_number'];
$file_path = $_POST['file_path'];

// Check if already in favorites
$stmt = $conn->prepare("SELECT * FROM favorites WHERE matric_number = :matric_number AND file_path = :file_path");
$stmt->execute(['matric_number' => $matric_number, 'file_path' => $file_path]);

if ($stmt->fetch()) {
    // Remove from favorites
    $deleteStmt = $conn->prepare("DELETE FROM favorites WHERE matric_number = :matric_number AND file_path = :file_path");
    $deleteStmt->execute(['matric_number' => $matric_number, 'file_path' => $file_path]);
    echo "Removed from favorites!";
} else {
    // Add to favorites
    $insertStmt = $conn->prepare("INSERT INTO favorites (matric_number, file_path, subject, note, faculty_id, upload_date) 
                                  SELECT :matric_number, file_path, subject, note, faculty_id, upload_date 
                                  FROM upload_materials WHERE file_path = :file_path");
    $insertStmt->execute(['matric_number' => $matric_number, 'file_path' => $file_path]);
    echo "Added to favorites!";
}
?>
