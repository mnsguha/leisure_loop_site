<?php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT id, title, destination, is_active FROM packages WHERE destination LIKE '%Meghalaya%'");
$packages = $stmt->fetchAll();

echo "Packages for Meghalaya:\n";
print_r($packages);

$stmt = $pdo->query("SELECT name FROM destinations WHERE name LIKE '%Meghalaya%'");
$destinations = $stmt->fetchAll();

echo "\nDestinations matching Meghalaya:\n";
print_r($destinations);
?>
