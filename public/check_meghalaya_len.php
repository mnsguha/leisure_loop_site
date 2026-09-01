<?php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT id, title, destination, LENGTH(destination) as dest_len FROM packages WHERE destination LIKE '%Meghalaya%'");
$packages = $stmt->fetchAll();
print_r($packages);

$stmt = $pdo->query("SELECT name, LENGTH(name) as name_len FROM destinations WHERE name LIKE '%Meghalaya%'");
$destinations = $stmt->fetchAll();
print_r($destinations);
?>
