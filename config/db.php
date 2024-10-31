<?php
// $host = 'ewr1.clusters.zeabur.com';
// $db = 'nefelibata';
// $user = 'root';
// $pass = 'sK5GC2wY784NOFmqb1V369pUaXLZB0cz';
// $charset = 'utf8mb4';

// $dsn = "mysql:host=$host;port=32172;dbname=$db;charset=$charset"; 

$host = 'localhost';
$db = 'nefelibata';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
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
