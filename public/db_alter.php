<?php
require '../config/db.php';
$stmt = $pdo->query("DESCRIBE hotels");
$cols = $stmt->fetchAll();
foreach($cols as $c) {
    echo $c['Field'] . "\n";
}
?>
