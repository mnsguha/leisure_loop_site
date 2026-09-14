<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->query("DESCRIBE packages");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($columns);
