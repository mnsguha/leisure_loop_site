<?php
declare(strict_types=1);

/**
 * Migration: 20260923_add_cab_durations
 * Description: Creates the cab_durations table so the "Rent For" duration
 *              dropdown is fetched from the database instead of hardcoded,
 *              and seeds it with the plain hours set (2/4/6/8/10/12).
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
    $tables = $pdo->query("SHOW TABLES LIKE 'cab_durations'")->fetchAll();
    if (empty($tables)) {
        $pdo->exec("CREATE TABLE cab_durations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            hours INT NOT NULL,
            km_limit INT NULL,
            label VARCHAR(100) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    $count = (int)$pdo->query("SELECT COUNT(*) FROM cab_durations")->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare("INSERT INTO cab_durations (hours, km_limit, label, sort_order) VALUES (?, ?, ?, ?)");
        $rows = [
            [2, 20, '2 Hours', 1],
            [4, 40, '4 Hours', 2],
            [6, 60, '6 Hours', 3],
            [8, 80, '8 Hours', 4],
            [10, 100, '10 Hours', 5],
            [12, 120, '12 Hours', 6],
        ];
        foreach ($rows as $r) {
            $stmt->execute($r);
        }
    }
}

function down(PDO $pdo): void {
    $pdo->exec("DROP TABLE IF EXISTS cab_durations");
}
