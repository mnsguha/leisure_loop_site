<?php
require 'config/db.php';
$stmt = $pdo->prepare("SELECT cover_image FROM destinations WHERE slug='sikkim'");
$stmt->execute();
$res = $stmt->fetch();
echo $res['cover_image'] ?? 'NOT_FOUND';
