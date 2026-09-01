<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT parallax_layers FROM destinations WHERE slug='sikkim'");
echo $stmt->fetchColumn();
?>
