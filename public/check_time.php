<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT id, title, destination, is_active FROM packages WHERE id=6");
print_r($stmt->fetch());
?>
