<?php
declare(strict_types=1);

require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Fetch input
$hotel_id = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
$rooms    = isset($_POST['rooms']) ? (int)$_POST['rooms'] : 1;

if ($hotel_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid hotel ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT h.starting_tariff, 
               (SELECT MIN(rr.base_rate_2_pax) 
                FROM hotel_room_rates rr 
                JOIN hotel_room_plans rp ON rr.plan_id = rp.id 
                JOIN hotel_rooms r ON rp.room_id = r.id 
                WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0
               ) as dynamic_starting_tariff 
        FROM hotels h 
        WHERE h.id = ? AND h.is_active = 1
    ");
    $stmt->execute([$hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel not found']);
        exit;
    }

    $base_tariff = !empty($hotel['dynamic_starting_tariff']) && $hotel['dynamic_starting_tariff'] > 0
        ? (float)$hotel['dynamic_starting_tariff']
        : (float)($hotel['starting_tariff'] ?? 0);

    $check_in  = isset($_POST['check_in']) ? $_POST['check_in'] : null;
    $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : null;
    $nights = 1;
    if ($check_in && $check_out) {
        $ci = new DateTime($check_in);
        $co = new DateTime($check_out);
        $interval = $ci->diff($co);
        $nights = max(1, $interval->days);
    }

    // Calculate total for requested number of rooms and nights
    $tariff = $base_tariff * max(1, $rooms) * $nights;
    $tax_estimate = (int)round($tariff * 0.18);

    echo json_encode([
        'status' => 'success',
        'data' => [
            'tariff' => $tariff,
            'tax'    => $tax_estimate,
            'formatted_tariff' => number_format($tariff),
            'formatted_tax'    => number_format($tax_estimate)
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
