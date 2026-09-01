<?php
require_once __DIR__ . '/config/db.php';

if ($pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET hero_text_sub = 'LEISURE LOOP TRIP', hero_text_main = 'Where Every Journey Begins' WHERE id = 1");
        $stmt->execute();
        echo "Database updated successfully.\n";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not connect to database.\n";
}
?>
