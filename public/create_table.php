<?php
require '../config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hotel_room_rates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        plan_id INT NOT NULL,
        rate_date DATE NOT NULL,
        base_rate_2_pax DECIMAL(10,2) DEFAULT 0,
        base_rate_1_pax DECIMAL(10,2) DEFAULT 0,
        extra_adult_rate DECIMAL(10,2) DEFAULT 0,
        cnb_rate DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_plan_date (plan_id, rate_date),
        FOREIGN KEY (plan_id) REFERENCES hotel_room_plans(id) ON DELETE CASCADE
    )");
    echo "Table hotel_room_rates created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
