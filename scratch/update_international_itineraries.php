<?php
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query('SELECT id, title, days, nights, destination FROM packages WHERE is_international = 1');
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($packages);
