<?php
require_once __DIR__ . '/../includes/config.php';
$host = 'localhost';
$dbname = 'student_passwords';
$username = 'passwords_user';
$password = ''; //No password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
