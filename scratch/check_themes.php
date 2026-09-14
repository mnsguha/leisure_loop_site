<?php
// Run this from public/ dir where the web server runs
$host = 'localhost';
$db   = 'leisure_loop_db';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

$r = $pdo->query('DESCRIBE tour_categories');
foreach ($r->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . ' | ' . $row['Type'] . "\n";
}
echo "---DATA SAMPLE---\n";
$r2 = $pdo->query('SELECT * FROM tour_categories LIMIT 3');
foreach ($r2->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row) . "\n";
}
