<?php
$page_title = "Exclusive Stays & Luxury Retreats | Leisure Loop";
require_once "../config/db.php";
require_once '../includes/functions.php';
csrf_stamp_form();

$signature = [];
$luxury = [];

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$check_in = isset($_GET['check_in']) ? trim($_GET['check_in']) : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? trim($_GET['check_out']) : date('Y-m-d', strtotime('+1 day'));
$rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 1;
$adults = isset($_GET['adults']) ? (int)$_GET['adults'] : 2;
$children = isset($_GET['children']) ? (int)$_GET['children'] : 0;
$infants = isset($_GET['infants']) ? (int)$_GET['infants'] : 0;

if (isset($pdo)) {
    $sql_sig = "SELECT h.*, COALESCE((SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.rate_date = ? AND rr.base_rate_2_pax > 0), (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0)) as dynamic_starting_tariff, (SELECT image_url FROM hotel_images WHERE hotel_id = h.id ORDER BY created_at ASC LIMIT 1) as main_image FROM hotels h WHERE h.type='signature' AND h.is_active=1";
    $sql_lux = "SELECT h.*, COALESCE((SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.rate_date = ? AND rr.base_rate_2_pax > 0), (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0)) as dynamic_starting_tariff, (SELECT image_url FROM hotel_images WHERE hotel_id = h.id ORDER BY created_at ASC LIMIT 1) as main_image FROM hotels h WHERE h.type='luxury' AND h.is_active=1";
    
    $params_sig = [$check_in];
    $params_lux = [$check_in];
    
    if ($search_query !== '') {
        $sql_sig .= " AND (name LIKE ? OR place LIKE ?)";
        $sql_lux .= " AND (name LIKE ? OR place LIKE ?)";
        $search_param = "%{$search_query}%";
        $params_sig[] = $search_param;
        $params_sig[] = $search_param;
        $params_lux[] = $search_param;
        $params_lux[] = $search_param;
    }
    
    $sql_sig .= " ORDER BY created_at DESC";
    $sql_lux .= " ORDER BY created_at DESC";

    $stmt_sig = $pdo->prepare($sql_sig);
    $stmt_sig->execute($params_sig);
    $signature = $stmt_sig->fetchAll();

    $stmt_lux = $pdo->prepare($sql_lux);
    $stmt_lux->execute($params_lux);
    $luxury = $stmt_lux->fetchAll();
}

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

if (isset($_GET['view'])) {
    $is_mobile = ($_GET['view'] === 'mobile');
}

if ($is_mobile) {
    include '../includes/mobile_hotels.php';
} else {
    include '../includes/desktop_hotels.php';
}
exit;
