<?php
declare(strict_types=1);

/**
 * Migration: 001_create_reviews_table
 * Description: Creates the reviews table with referential integrity to packages
 */

// We assume $pdo is already available from the script that requires this file.
if (!isset($pdo) || !$pdo instanceof PDO) {
    throw new Exception("Migration failed: PDO connection is required but not found.");
}

$sql = "
    CREATE TABLE IF NOT EXISTS reviews (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        package_id INT NOT NULL, -- Matched to the INT type of the existing packages table
        customer_name VARCHAR(150) NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL DEFAULT NULL,
        
        FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($sql);
