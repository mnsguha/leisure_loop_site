<?php
require_once '../config/db.php';
$stmt = $pdo->prepare("SELECT description_rich, highlights FROM packages WHERE slug = 'north-sikkim-frozen-lake'");
$stmt->execute();
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
