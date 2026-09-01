<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT name, parallax_layers_json FROM destinations WHERE slug = 'darjeeling'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
