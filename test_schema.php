<?php
$host = '127.0.0.1';
$db   = 'leisure_loop_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass);

$stmt = $pdo->query('SHOW TABLES');
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables:\n";
print_r($tables);

if (in_array('hotel_images', $tables)) {
    echo "\nhotel_images schema:\n";
    $stmt = $pdo->query('DESCRIBE hotel_images');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}
if (in_array('hotels', $tables)) {
    echo "\nhotels schema:\n";
    $stmt = $pdo->query('DESCRIBE hotels');
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
}
