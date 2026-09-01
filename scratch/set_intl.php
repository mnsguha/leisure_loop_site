<?php
require_once __DIR__ . '/../config/db.php';
if ($pdo) {
    try {
        $pdo->exec("UPDATE packages SET is_international = 1 WHERE is_active = 1 LIMIT 3");
        echo "Set 3 active packages to international = 1\n";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not connect to database.\n";
}
