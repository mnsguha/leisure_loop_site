<?php
$pdo = new PDO("mysql:host=localhost;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

try {
    echo "Adding extra guest columns...\n";
    $pdo->exec("ALTER TABLE hotel_room_plans ADD COLUMN IF NOT EXISTS extra_bed_tariff DECIMAL(10,2) DEFAULT 0 AFTER base_tariff");
    $pdo->exec("ALTER TABLE hotel_room_plans ADD COLUMN IF NOT EXISTS cnb_tariff DECIMAL(10,2) DEFAULT 0 AFTER extra_bed_tariff");
    
    $pdo->exec("ALTER TABLE plan_date_rates ADD COLUMN IF NOT EXISTS extra_bed_tariff DECIMAL(10,2) DEFAULT 0 AFTER tariff");
    $pdo->exec("ALTER TABLE plan_date_rates ADD COLUMN IF NOT EXISTS cnb_tariff DECIMAL(10,2) DEFAULT 0 AFTER extra_bed_tariff");
    
    echo "Done";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
