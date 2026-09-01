<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT name, cover_image, card_image FROM destinations");
$res = $stmt->fetchAll();
print_r($res);
?>
