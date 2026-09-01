<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT * FROM destinations WHERE category='international'");
$intl_destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($intl_destinations);
?>
