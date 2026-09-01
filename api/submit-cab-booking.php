<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

function cleanInput($key) {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

$name = cleanInput('guest_name');
$phone = cleanInput('phone');
$email = filter_var(cleanInput('email'), FILTER_SANITIZE_EMAIL);
$special_request = cleanInput('special_request');

$vehicle_id = cleanInput('vehicle_id');
$vehicle_name = cleanInput('vehicle_name');
$service_type = cleanInput('service_type');
$final_price = cleanInput('final_price');
$pickup_location = cleanInput('pickup_location');
$drop_location = cleanInput('drop_location');
$travel_date = cleanInput('travel_date');
$travel_time = cleanInput('travel_time');

$trip_type = cleanInput('trip_type');
$duration = cleanInput('duration');
$search_itinerary_details = cleanInput('search_itinerary_details');

if (!$name || !$phone || !$vehicle_id) {
    echo json_encode(['success' => false, 'message' => 'Name, Phone, and Vehicle details are required.']);
    exit;
}

$itinerary_details = json_encode([
    'vehicle_id' => $vehicle_id,
    'vehicle_name' => $vehicle_name,
    'service_type' => $service_type,
    'final_price' => $final_price,
    'special_request' => $special_request,
    'duration' => $duration,
    'search_itinerary_details' => $search_itinerary_details
]);

if ($pdo) {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO cab_bookings (name, phone, email, pickup_location, drop_location, travel_date, travel_time, cab_type, trip_type, itinerary_details)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $name,
            $phone,
            $email ?: null,
            $pickup_location ?: null,
            $drop_location ?: null,
            $travel_date ?: null,
            $travel_time ?: null,
            $vehicle_name ?: null,
            $trip_type ?: $service_type,
            $itinerary_details
        ]);
        
        $booking_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id,
            'message' => 'Booking successful'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
}
?>