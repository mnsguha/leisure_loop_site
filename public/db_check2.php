<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT id, title, slug, destination, is_active FROM packages");
$pkgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($pkgs, JSON_PRETTY_PRINT);
