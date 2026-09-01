<?php
require 'config/db.php';
try {
    $pdo->exec('ALTER TABLE cab_bookings ADD COLUMN trip_type VARCHAR(50) DEFAULT NULL');
    $pdo->exec('ALTER TABLE cab_bookings ADD COLUMN duration VARCHAR(50) DEFAULT NULL');
    $pdo->exec('ALTER TABLE cab_bookings ADD COLUMN itinerary_details TEXT DEFAULT NULL');
    echo "Migration successful.\n";
} catch (Exception $e) {
    echo "Error/Note: " . $e->getMessage() . "\n";
}
?>
