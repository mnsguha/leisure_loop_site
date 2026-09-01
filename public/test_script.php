<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT id, name, slug, is_active FROM destinations");
$dests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($dests);
echo "</pre>";
