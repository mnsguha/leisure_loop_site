<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT name, image_url FROM accreditations");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
