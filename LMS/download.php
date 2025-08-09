<?php
session_start();

if (!isset($_SESSION['matric_number'])) {
    die("Access Denied!");
}

require('config.php');

if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("No file specified.");
}

$file_name = basename($_GET['file']);
$file_path = "faculty/uploads/" . $file_name;

if (!file_exists($file_path)) {
    die("File not found.");
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
header("Content-Length: " . filesize($file_path));
readfile($file_path);
exit();
?>
