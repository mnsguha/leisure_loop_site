<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT parallax_layers_json FROM destinations WHERE slug='dooars'");
print_r(json_decode($stmt->fetchColumn(), true));
?>
