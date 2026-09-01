<?php
require __DIR__ . '/../config/db.php';
$sql = "CREATE TABLE IF NOT EXISTS hotel_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
)";
try {
    $pdo->exec($sql);
    echo "Table created successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
