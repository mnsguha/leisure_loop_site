<?php
require '../config/db.php';
try {
    $pdo->exec("ALTER TABLE packages ADD COLUMN itinerary_heading VARCHAR(255) DEFAULT 'Day-by-Day Journey'");
    echo "Column added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
