<?php
require_once '../config/db.php';
$stmt = $pdo->query("SELECT title, destination, is_international FROM packages");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
