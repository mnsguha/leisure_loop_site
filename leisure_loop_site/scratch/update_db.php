<?php
require 'config/db.php';
try {
    $pdo->exec("ALTER TABLE leads ADD COLUMN destination VARCHAR(255) AFTER customer_email");
    $pdo->exec("ALTER TABLE leads ADD COLUMN travel_date VARCHAR(100) AFTER destination");
    $pdo->exec("ALTER TABLE leads ADD COLUMN adults INT DEFAULT 0 AFTER travel_date");
    $pdo->exec("ALTER TABLE leads ADD COLUMN children INT DEFAULT 0 AFTER adults");
    echo "Columns added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
