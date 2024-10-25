<?php
// $host = 'localhost';
// $db = 'nefelibata';
// $user = 'root';
// $pass = '';
// $charset = 'utf8mb4';

// $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// $host = 'mysql.zeabur.internal';
$host = 'fra1.clusters.zeabur.com'; 
$db = 'nefelibata';
$user = 'root';
$pass = 'Nir0hVtSnHbQ13974L65RCceMWO8K2dA';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=32168;dbname=$db;charset=$charset"; 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
