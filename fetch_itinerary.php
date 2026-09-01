<?php
require 'config/db.php';
$stmt = $pdo->prepare("SELECT itinerary FROM packages WHERE slug = 'meghalaya-living-roots'");
$stmt->execute();
echo $stmt->fetchColumn();
