<?php
require_once '../config/db.php';
try {
    $pdo->exec("ALTER TABLE destinations ADD COLUMN card_image VARCHAR(255) DEFAULT ''");
    echo "Successfully added card_image to destinations table.";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column card_image already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
