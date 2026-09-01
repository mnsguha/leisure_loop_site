<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $sql = "CREATE TABLE IF NOT EXISTS cab_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100),
        pickup_location VARCHAR(255),
        drop_location VARCHAR(255),
        travel_date DATE,
        travel_time VARCHAR(20),
        cab_type VARCHAR(50),
        status ENUM('new', 'contacted', 'confirmed', 'completed', 'cancelled') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table cab_bookings created successfully!";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
