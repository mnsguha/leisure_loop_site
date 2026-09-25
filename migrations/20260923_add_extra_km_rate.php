<?php
declare(strict_types=1);

/**
 * Migration: 20260923_add_extra_km_rate
 * Description: Adds the per-vehicle extra-kilometre rate (hourly rental
 *              "Kilometer Charges" line) to vehicle_rates and backfills
 *              the site-wide default of 13.50 on first creation.
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
    $cols = $pdo->query("SHOW COLUMNS FROM vehicle_rates LIKE 'extra_km_rate'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE vehicle_rates ADD COLUMN extra_km_rate DECIMAL(10,2) NULL DEFAULT 13.50 AFTER price_per_hour");
        $pdo->exec("UPDATE vehicle_rates SET extra_km_rate = 13.50 WHERE extra_km_rate IS NULL");
    }
}

function down(PDO $pdo): void {
    $cols = $pdo->query("SHOW COLUMNS FROM vehicle_rates LIKE 'extra_km_rate'")->fetchAll();
    if (!empty($cols)) {
        $pdo->exec("ALTER TABLE vehicle_rates DROP COLUMN extra_km_rate");
    }
}
