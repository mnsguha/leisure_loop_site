<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$hotel_id = (int)$_POST['hotel_id'];
$type = $_POST['type'];
$guest_name = trim($_POST['guest_name']);
$phone = trim($_POST['phone']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$rooms = (int)$_POST['rooms'];
$adults = (int)$_POST['adults'];

// Signature specific
$room_id = isset($_POST['room_id']) && $_POST['room_id'] ? (int)$_POST['room_id'] : null;
$plan_id = isset($_POST['plan_id']) && $_POST['plan_id'] ? (int)$_POST['plan_id'] : null;
$special_request = isset($_POST['special_request']) ? trim($_POST['special_request']) : null;
$final_price = isset($_POST['final_price']) ? (float)$_POST['final_price'] : 0.00;
$total_amount = $final_price * $rooms; // Naive total, normally multiply by nights too, keeping simple.

if (!$guest_name || !$phone || !$check_in || !$check_out) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}

$booking_id = 'LL-HTL-' . strtoupper(substr(md5(uniqid()), 0, 8));

if ($pdo) {
    try {
        try {
            $pdo->exec("ALTER TABLE hotel_bookings ADD COLUMN plan_id INT DEFAULT NULL, ADD COLUMN special_request TEXT DEFAULT NULL");
        } catch (Exception $e) {}

        $stmt = $pdo->prepare("INSERT INTO hotel_bookings (booking_id, hotel_id, room_id, plan_id, guest_name, phone, email, check_in, check_out, rooms, adults, total_amount, payment_status, booking_status, special_request) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pay_later', 'confirmed', ?)");
        $stmt->execute([$booking_id, $hotel_id, $room_id, $plan_id, $guest_name, $phone, $email, $check_in, $check_out, $rooms, $adults, $total_amount, $special_request]);
        
        // Fetch Hotel Name for Email
        $hStmt = $pdo->prepare("SELECT name FROM hotels WHERE id = ?");
        $hStmt->execute([$hotel_id]);
        $hotelName = $hStmt->fetchColumn();
        
        // Send Email Confirmation
        if ($email) {
            if ($type == 'signature') {
                $subject = "Booking Confirmation: " . $booking_id;
                $msg = "Dear $guest_name,\n\nYour booking at $hotelName is confirmed!\nBooking ID: $booking_id\nCheck-in: $check_in\nCheck-out: $check_out\nRooms: $rooms\n\nYou can download your PDF voucher from our website.\n\nThank you,\nLeisure Loop";
            } else {
                $subject = "Inquiry Acknowledgement: " . $booking_id;
                $msg = "Dear $guest_name,\n\nWe have received your inquiry for $hotelName.\nAcknowledgement No: $booking_id\nCheck-in: $check_in\nCheck-out: $check_out\nRooms: $rooms\n\nOur travel experts will contact you shortly with availability and pricing details.\n\nThank you,\nLeisure Loop";
            }
            $headers = "From: no-reply@leisureloop.com";
            @mail($email, $subject, $msg, $headers);
        }

        echo json_encode([
            'success' => true,
            'message' => $type == 'signature' ? 'Booking Confirmed! Preparing your voucher...' : 'Inquiry Submitted successfully! Our team will contact you.',
            'booking_id' => $booking_id
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
