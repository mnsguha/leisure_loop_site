<?php
require '../config/db.php';
$stmt1 = $pdo->query('DESCRIBE room_inventory');
print_r($stmt1->fetchAll(PDO::FETCH_ASSOC));
