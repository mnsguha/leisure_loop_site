<?php
require_once 'config/db.php';
$stmt = $pdo->query("DESCRIBE settings");
$cols = $stmt->fetchAll();
print_r($cols);
?>
