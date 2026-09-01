<?php
require_once __DIR__ . '/../config/db.php';

try {
    // 1. Hotels Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hotels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        place VARCHAR(255) NOT NULL,
        star_category INT DEFAULT 3,
        type ENUM('signature', 'luxury') DEFAULT 'signature',
        starting_tariff DECIMAL(10,2) DEFAULT 0.00,
        main_image VARCHAR(500),
        description TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Hotel Rooms Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hotel_rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hotel_id INT NOT NULL,
        room_type_name VARCHAR(255) NOT NULL,
        base_tariff DECIMAL(10,2) NOT NULL,
        discount_percent DECIMAL(5,2) DEFAULT 0.00,
        room_image VARCHAR(500),
        amenities TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
    )");

    // 3. Hotel Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hotel_bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        hotel_id INT NOT NULL,
        room_id INT,
        guest_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100),
        check_in DATE,
        check_out DATE,
        rooms INT DEFAULT 1,
        adults INT DEFAULT 2,
        children INT DEFAULT 0,
        total_amount DECIMAL(10,2) DEFAULT 0.00,
        payment_status ENUM('pay_later', 'paid') DEFAULT 'pay_later',
        booking_status ENUM('confirmed', 'pending', 'cancelled', 'completed') DEFAULT 'confirmed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert the 3 Signature Hotels if they don't exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM hotels WHERE type = 'signature'");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO hotels (name, place, star_category, type, main_image, description) VALUES
        ('Tripoo Potala Palace', 'Darjeeling', 4, 'signature', 'https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=800', 'Experience colonial charm and breathtaking views of the Kanchenjunga.'),
        ('Tripoo Rishum Haapo Retreat', 'Pelling', 4, 'signature', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800', 'A serene Himalayan retreat surrounded by virgin pine forests.'),
        ('Aspen Retreat By Manaya Hotels', 'Darjeeling', 5, 'signature', 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=800', 'Ultimate luxury in the heart of Darjeeling with premium concierge services.')");
    }

    echo "Hotel tables created successfully!";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
