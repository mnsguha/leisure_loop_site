<?php
/**
 * Cab Booking API Endpoint
 */
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ll_json_response('error', 'METHOD_NOT_ALLOWED', 'Invalid request method.');
}

// Anti-bot guard: honeypot -> CSRF -> time-trap -> rate limits.
lead_guard_json($_POST);

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
    ll_json_response('error', 'VALIDATION_ERROR', 'Name, Phone, and Trip Type are required.');
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
        
        ll_json_response('success', 'OK', 'Your cab booking request has been received! Our team will contact you shortly.');
    } catch (PDOException $e) {
        ll_json_response('error', 'INTERNAL_ERROR', 'A database error occurred. Please try again.');
    }
} else {
    ll_json_response('error', 'INTERNAL_ERROR', 'Database connection failed.');
}
?>
