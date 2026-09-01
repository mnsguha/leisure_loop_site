<?php
require_once __DIR__ . '/../config/db.php';

if ($pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET hero_text_sub = 'The Art of Discovery' WHERE id = 1 AND hero_text_sub = 'Explore Without Limits'");
        $stmt->execute();
        echo "Successfully updated database settings tagline!\n";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Could not connect to database.\n";
}
