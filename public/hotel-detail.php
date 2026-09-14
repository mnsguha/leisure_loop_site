<?php
$page_title = "Hotel Details | Leisure Loop";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id || !$pdo) { header("Location: hotels.php"); exit; }

$stmt = $pdo->prepare("SELECT h.*, (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0) as dynamic_starting_tariff FROM hotels h WHERE h.id = ? AND h.is_active=1");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) { header("Location: hotels.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$hotel_images = $stmt->fetchAll();

$page_title = $hotel['name'] . " | Leisure Loop";

$check_in  = isset($_GET['check_in'])  ? trim($_GET['check_in'])  : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? trim($_GET['check_out']) : date('Y-m-d', strtotime('+1 day'));
$rooms     = isset($_GET['rooms'])    ? (int)$_GET['rooms']    : 1;
$adults    = isset($_GET['adults'])   ? (int)$_GET['adults']   : 2;
$children  = isset($_GET['children']) ? (int)$_GET['children'] : 0;
$infants   = isset($_GET['infants'])  ? (int)$_GET['infants']  : 0;

$check_date = $check_in;

// Fetch Rooms and Plans — Controller Invariant (Backend Rule #16):
// All DB reads happen here, never inside view partials.
$rooms_with_plans = [];
$stmt = $pdo->prepare("SELECT * FROM hotel_rooms WHERE hotel_id = ?");
$stmt->execute([$id]);
$db_rooms = $stmt->fetchAll();

foreach ($db_rooms as $room) {
    $inv_stmt = $pdo->prepare("SELECT available_rooms FROM room_inventory WHERE room_id = ? AND inventory_date = ?");
    $inv_stmt->execute([$room['id'], $check_date]);
    $inv_res = $inv_stmt->fetch();
    $room['current_availability'] = $inv_res ? (int)$inv_res['available_rooms'] : (int)$room['total_rooms'];

    $p_stmt = $pdo->prepare("SELECT * FROM hotel_room_plans WHERE room_id = ? ORDER BY id ASC");
    $p_stmt->execute([$room['id']]);
    $plans = $p_stmt->fetchAll();

    foreach ($plans as &$plan) {
        $dr_stmt = $pdo->prepare("SELECT * FROM hotel_room_rates WHERE plan_id = ? ORDER BY rate_date ASC");
        $dr_stmt->execute([$plan['id']]);
        $plan['date_rates'] = $dr_stmt->fetchAll();
    }
    unset($plan);

    $room['plans'] = $plans;
    $rooms_with_plans[] = $room;
}

// Backend Rule #16 — Deterministic Platform Detection with ?view= override for testing
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

if (isset($_GET['view'])) {
    $is_mobile = ($_GET['view'] === 'mobile');
}

// Backend Rule #16 — Script Enqueueing Scoping: load mobile JS only for mobile view
if ($is_mobile) {
    $extra_scripts = '<script src="js/modules/mobile-hotel-detail.js?v=' . time() . '" defer></script>';
    include '../includes/mobile_hotel-detail.php';
} else {
    include '../includes/desktop_hotel_detail.php';
}
exit;
