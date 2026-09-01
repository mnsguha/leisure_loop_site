<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT id, name, cover_image, card_image FROM destinations WHERE name = 'Darjeeling'");
$res = $stmt->fetch();
print_r($res);
?>
