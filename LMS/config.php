
<?php
require 'vendor/autoload.php'; // Load Composer dependencies

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database Connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=user_auth", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
