<?php
/**
 * Cab Booking API Endpoint
 */
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

function cleanInput($key) {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
}

$name = cleanInput('name');
$phone = cleanInput('phone');
$email = filter_var(cleanInput('email'), FILTER_SANITIZE_EMAIL);

// Shared fields
$trip_type = cleanInput('trip_type');
$cab_type = cleanInput('cab_type');

// Tab specific fields
$pickup_location = cleanInput('pickup_location');
$drop_location = cleanInput('drop_location');
$travel_date = cleanInput('travel_date');
$travel_time = cleanInput('travel_time');
$duration = cleanInput('duration');
$itinerary_details = cleanInput('itinerary_details');

if (!$name || !$phone || !$trip_type) {
    echo json_encode(['success' => false, 'message' => 'Name, Phone, and Trip Type are required.']);
    exit;
}

if ($pdo) {
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO cab_bookings (name, phone, email, pickup_location, drop_location, travel_date, travel_time, cab_type, trip_type, duration, itinerary_details)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $name,
            $phone,
            $email ?: null,
            $pickup_location ?: null,
            $drop_location ?: null,
            $travel_date ?: null,
            $travel_time ?: null,
            $cab_type ?: null,
            $trip_type,
            $duration ?: null,
            $itinerary_details ?: null
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Your cab booking request has been received! Our team will contact you shortly.'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
}
?>
