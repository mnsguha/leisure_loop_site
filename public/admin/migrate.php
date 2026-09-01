<?php
require_once '../../config/db.php';

try {
    $sql = "
        ALTER TABLE destinations 
        ADD COLUMN altitude VARCHAR(255) NULL AFTER category,
        ADD COLUMN best_time VARCHAR(255) NULL AFTER altitude,
        ADD COLUMN duration VARCHAR(255) NULL AFTER best_time,
        ADD COLUMN story_narrative_image_2 VARCHAR(255) NULL AFTER story_narrative_image,
        ADD COLUMN parallax_layers_json TEXT NULL AFTER duration,
        ADD COLUMN local_experiences_json TEXT NULL AFTER parallax_layers_json
    ";
    $pdo->exec($sql);
    echo "SUCCESS";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "SUCCESS (already exists)";
    } else {
        echo "ERROR: " . $e->getMessage();
    }
}
