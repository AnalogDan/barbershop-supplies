<?php
//For going live
// $host = 'localhost';
// $db   = 'u486057861_barbershop';
// $user = 'u486057861_user';
// $pass = 'DeP0!rS1;Li';
// $charset = 'utf8mb4';

//For testing locally
$host = 'localhost';
$db   = 'barbershop';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>