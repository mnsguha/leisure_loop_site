<?php
require_once 'includes/functions.php';
require_once 'config/db.php';

$stmt = $pdo->query("DESCRIBE hotel_room_plans;");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($result);
