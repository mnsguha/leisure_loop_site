<?php
require_once '../config/db.php';
$all_pkgs = $pdo->query("SELECT id, title, destination, is_active FROM packages WHERE is_active = 1")->fetchAll();
print_r($all_pkgs);
?>
