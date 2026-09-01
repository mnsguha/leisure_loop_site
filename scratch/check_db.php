<?php
require 'config/db.php';
$stmt = $pdo->query('DESCRIBE leads');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
