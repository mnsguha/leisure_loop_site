<?php
$pdo = new PDO("mysql:host=localhost;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

try {
    // 1. Create hotel_room_plans table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hotel_room_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT NOT NULL,
        plan_name VARCHAR(255) NOT NULL,
        base_tariff DECIMAL(10,2) NOT NULL,
        discount_percent DECIMAL(5,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES hotel_rooms(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Check if hotel_rooms still has base_tariff
    $stmt = $pdo->query("SHOW COLUMNS FROM hotel_rooms LIKE 'base_tariff'");
    $has_base_tariff = $stmt->rowCount() > 0;

    if ($has_base_tariff) {
        // 2. Migrate data
        echo "Migrating existing rooms to plans...\n";
        $rooms = $pdo->query("SELECT id, base_tariff, discount_percent FROM hotel_rooms")->fetchAll();
        
        $insert_stmt = $pdo->prepare("INSERT INTO hotel_room_plans (room_id, plan_name, base_tariff, discount_percent) VALUES (?, 'Room Only', ?, ?)");
        foreach ($rooms as $room) {
            // Check if a plan already exists for this room
            $check = $pdo->prepare("SELECT id FROM hotel_room_plans WHERE room_id = ?");
            $check->execute([$room['id']]);
            if ($check->rowCount() == 0) {
                $insert_stmt->execute([$room['id'], $room['base_tariff'], $room['discount_percent']]);
            }
        }

        // 3. Drop columns from hotel_rooms
        echo "Dropping old columns from hotel_rooms...\n";
        $pdo->exec("ALTER TABLE hotel_rooms DROP COLUMN base_tariff, DROP COLUMN discount_percent");
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
