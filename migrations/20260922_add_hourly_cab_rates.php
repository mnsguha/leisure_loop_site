<?php
declare(strict_types=1);

/**
 * Migration: 20260922_add_hourly_cab_rates
 * Description: Replaces 6 per-duration hourly columns with a single price_per_hour column.
 */

if (!isset($pdo) || !$pdo instanceof PDO) {
    if (php_sapi_name() === 'cli') {
        require_once __DIR__ . '/../config/db.php';
    } else {
        throw new Exception("Migration failed: PDO connection is required but not found.");
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    up($pdo);
    exit("Migration executed successfully.\n");
}

function up(PDO $pdo): void {
    $existing = $pdo->query("SHOW COLUMNS FROM vehicle_rates")->fetchAll(PDO::FETCH_COLUMN);
    $old_cols = ['price_2h','price_4h','price_6h','price_8h','price_10h','price_12h'];
    foreach ($old_cols as $col) {
        if (in_array($col, $existing, true)) {
            $pdo->exec("ALTER TABLE vehicle_rates DROP COLUMN $col");
        }
    }
    if (!in_array('price_per_hour', $existing, true)) {
        $pdo->exec("ALTER TABLE vehicle_rates ADD COLUMN price_per_hour DECIMAL(10,2) NULL");
    }
}

function down(PDO $pdo): void {
    $existing = $pdo->query("SHOW COLUMNS FROM vehicle_rates")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('price_per_hour', $existing, true)) {
        $pdo->exec("ALTER TABLE vehicle_rates DROP COLUMN price_per_hour");
    }
    $old_cols = ['price_2h','price_4h','price_6h','price_8h','price_10h','price_12h'];
    foreach ($old_cols as $col) {
        if (!in_array($col, $existing, true)) {
            $pdo->exec("ALTER TABLE vehicle_rates ADD COLUMN $col DECIMAL(10,2) NULL");
        }
    }
}
