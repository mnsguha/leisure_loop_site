<?php
require 'config/db.php';
try {
    $stmt = $pdo->query("
        SELECT h.name, 
               (SELECT MIN(rr.base_rate_2_pax) 
                FROM hotel_room_rates rr
                JOIN hotel_room_plans rp ON rr.plan_id = rp.id
                JOIN hotel_rooms r ON rp.room_id = r.id
                WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0) as dynamic_starting_tariff
        FROM hotels h WHERE h.is_active=1
    ");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo $e->getMessage();
}
