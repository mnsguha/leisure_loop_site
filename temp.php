<?php
require_once 'config/db.php';
$cols = $pdo->query("SHOW COLUMNS FROM vehicles")->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $cols);
