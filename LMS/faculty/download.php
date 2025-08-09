<?php
session_start();

if (!isset($_SESSION['faculty_id'])) {
    die("Access Denied!");
}

require('../config.php');

if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("No file specified.");
}

$file_name = basename($_GET['file']);
$file_path = "../uploads/assignments/" . $file_name;

if (!file_exists($file_path)) {
    die("File not found.");
}

header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . $file_name . "\"");
readfile($file_path);
exit();
?>
