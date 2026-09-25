<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Anti-bot guard: honeypot -> CSRF -> time-trap -> rate limits.
lead_guard_json($_POST);

$hotel_id = (int)($_POST['hotel_id'] ?? 0);
$type = $_POST['type'] ?? '';
$guest_name = trim($_POST['guest_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$rooms = (int)($_POST['rooms'] ?? 1);
$adults = (int)($_POST['adults'] ?? 1);

// Signature specific
$room_id = isset($_POST['room_id']) && $_POST['room_id'] ? (int)$_POST['room_id'] : null;
$plan_id = isset($_POST['plan_id']) && $_POST['plan_id'] ? (int)$_POST['plan_id'] : null;
$special_request = isset($_POST['special_request']) ? trim($_POST['special_request']) : null;
$children = isset($_POST['children']) ? (int)$_POST['children'] : 0;

if (!$guest_name || !$phone || !$check_in || !$check_out) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}

$total_amount = 0.00;
if ($type == 'signature' && $plan_id && $pdo) {
    // Backend validation of price
    $stmt = $pdo->prepare("SELECT rp.* FROM hotels h JOIN hotel_rooms r ON h.id = r.hotel_id JOIN hotel_room_plans rp ON r.id = rp.room_id WHERE rp.id = ?");
    $stmt->execute([$plan_id]);
    $planData = $stmt->fetch();
    
    if ($planData) {
        $start = new DateTime($check_in);
        $end = new DateTime($check_out);
        $nights = max(1, $start->diff($end)->days);
        $extraAdults = max(0, $adults - (2 * $rooms));
        
        $totalBase = 0;
        $totalTax = 0;
        
        for ($i = 0; $i < $nights; $i++) {
            $cur = clone $start;
            $cur->modify("+$i days");
            $nightStr = $cur->format('Y-m-d');
            
            $rStmt = $pdo->prepare("SELECT * FROM hotel_room_rates WHERE plan_id = ? AND rate_date = ?");
            $rStmt->execute([$plan_id, $nightStr]);
            $rate = $rStmt->fetch();
            
            if (!$rate) {
                $rStmt = $pdo->prepare("SELECT * FROM hotel_room_rates WHERE plan_id = ? ORDER BY rate_date ASC LIMIT 1");
                $rStmt->execute([$plan_id]);
                $rate = $rStmt->fetch();
            }
            
            $baseRate = $rate ? (float)$rate['base_rate_2_pax'] : 0;
            $adultRate = $rate ? (float)$rate['extra_adult_rate'] : 0;
            $childRate = $rate ? (float)$rate['cnb_rate'] : 0;
            
            $dailyBase = ($baseRate * $rooms) + ($extraAdults * $adultRate) + ($children * $childRate);
            $dailyBasePerRoom = $dailyBase / $rooms;
            $dailyTax = 0;
            if ($dailyBasePerRoom <= 1000) $dailyTax = 0;
            else if ($dailyBasePerRoom <= 7500) $dailyTax = $dailyBase * 0.05;
            else $dailyTax = $dailyBase * 0.18;
            $totalBase += $dailyBase;
            $totalTax += $dailyTax;
        }
        $total_amount = $totalBase + $totalTax;
    }
} else {
    $total_amount = isset($_POST['final_price']) ? (float)$_POST['final_price'] : 0.00;
}

$booking_id = 'LL-HTL-' . strtoupper(substr(md5(uniqid()), 0, 8));

if ($pdo) {
    try {
        try {
            $pdo->exec("ALTER TABLE hotel_bookings ADD COLUMN plan_id INT DEFAULT NULL, ADD COLUMN special_request TEXT DEFAULT NULL");
        } catch (Exception $e) {}

        $stmt = $pdo->prepare("INSERT INTO hotel_bookings (booking_id, hotel_id, room_id, plan_id, guest_name, phone, email, check_in, check_out, rooms, adults, total_amount, payment_status, booking_status, special_request) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pay_later', 'confirmed', ?)");
        $stmt->execute([$booking_id, $hotel_id, $room_id, $plan_id, $guest_name, $phone, $email, $check_in, $check_out, $rooms, $adults, $total_amount, $special_request]);
        pdf_grant_access('hotel', (string) $booking_id);
        
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
