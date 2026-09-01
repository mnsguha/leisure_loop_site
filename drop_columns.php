<?php
require_once 'config/db.php';
try {
    $stmt = $pdo->query("ALTER TABLE hotel_room_plans DROP COLUMN base_tariff, DROP COLUMN extra_bed, DROP COLUMN cnb, DROP COLUMN discount;");
    echo "Columns dropped successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
