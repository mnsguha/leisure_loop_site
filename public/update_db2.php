<?php
require_once '../config/db.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    

    $pdo->exec("ALTER TABLE packages ADD COLUMN hotel_details TEXT");
    echo "Added hotel_details\n";
    $pdo->exec("ALTER TABLE packages ADD COLUMN vehicle_details TEXT");
    echo "Added vehicle_details\n";
    $pdo->exec("ALTER TABLE packages ADD COLUMN meal_plan_details TEXT");
    echo "Added meal_plan_details\n";
} catch (Exception $e) { echo $e->getMessage() . "\n"; }
