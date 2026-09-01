<?php
require_once __DIR__ . '/../config/db.php';
try {
    // Check if column already exists
    $result = $pdo->query("SHOW COLUMNS FROM `packages` LIKE 'is_international'");
    $exists = $result->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE `packages` ADD COLUMN `is_international` TINYINT(1) DEFAULT 0 AFTER `is_featured`");
        echo "Column 'is_international' added successfully.\n";
    } else {
        echo "Column 'is_international' already exists.\n";
    }

    // Set first 3 packages as international for demonstration
    $pdo->exec("UPDATE `packages` SET `is_international` = 1 LIMIT 3");
    echo "First 3 packages set as international.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
