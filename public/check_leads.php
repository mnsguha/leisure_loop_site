<?php
require 'g:/Antigravity/leisure_loop_site/config/db.php';
$stmt = $pdo->query('DESCRIBE leads');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
