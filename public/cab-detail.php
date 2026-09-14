<?php
$page_title = "Cab Details | Leisure Loop";
$body_class = 'page-cab-detail';
require_once "../config/db.php";

// Extract incoming search query parameters for pre-filling
$today_date = date('Y-m-d');
$s_type = isset($_GET['type']) ? trim($_GET['type']) : 'oneway';
$s_pickup = isset($_GET['pickup_location']) ? trim($_GET['pickup_location']) : '';
$s_drop = isset($_GET['drop_location']) ? trim($_GET['drop_location']) : '';
$s_date = (!empty($_GET['travel_date']) && $_GET['travel_date'] >= $today_date) ? trim($_GET['travel_date']) : '';
$s_drop_date = (!empty($_GET['return_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['return_date']) && $_GET['return_date'] >= $today_date) ? trim($_GET['return_date']) : '';
$s_time = isset($_GET['travel_time']) ? trim($_GET['travel_time']) : '';
$s_duration = isset($_GET['duration']) ? trim($_GET['duration']) : '';
$s_itinerary = isset($_GET['itinerary_details']) ? trim($_GET['itinerary_details']) : '';
$s_cab_type = isset($_GET['cab_type']) ? trim($_GET['cab_type']) : ($cab_class['name'] ?? '');

$is_search = isset($_GET['search']) && $_GET['search'] == '1';

if ($is_search) {
    $cab_type = isset($_GET['cab_type']) ? $_GET['cab_type'] : '';
    if (!empty($cab_type)) {
        $stmt = $pdo->prepare("SELECT * FROM cab_classes WHERE name = ? AND is_active=1");
        $stmt->execute([$cab_type]);
        $cab_class = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE cab_class_id = ? AND is_active=1 ORDER BY price_per_day ASC");
        $stmt->execute([$cab_class ? $cab_class['id'] : 0]);
        $vehicles = $stmt->fetchAll();
    } else {
        $cab_class = null;
        $stmt = $pdo->query("SELECT * FROM vehicles WHERE is_active=1 ORDER BY price_per_day ASC");
        $vehicles = $stmt->fetchAll();
    }
    $page_title = "Cab Search Results | Leisure Loop";
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id || !$pdo) { header("Location: cabs.php"); exit; }

    $stmt = $pdo->prepare("SELECT * FROM cab_classes WHERE id = ? AND is_active=1");
    $stmt->execute([$id]);
    $cab_class = $stmt->fetch();
    if (!$cab_class) { header("Location: cabs.php"); exit; }

    $page_title = $cab_class['name'] . " | Leisure Loop";

    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE cab_class_id = ? AND is_active=1 ORDER BY price_per_day ASC");
    $stmt->execute([$id]);
    $vehicles = $stmt->fetchAll();
}

// Fetch all future vehicle rates
$stmt = $pdo->query("SELECT vehicle_id, rate_date, price FROM vehicle_rates WHERE rate_date >= CURDATE()");
$all_vehicle_rates = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_vehicle_rates[$row['vehicle_id']][$row['rate_date']] = (float)$row['price'];
}

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

// Fetch all cab classes for the dropdown
$stmt = $pdo->query("SELECT * FROM cab_classes WHERE is_active=1 ORDER BY id ASC");
$cab_classes = $stmt->fetchAll();

if ($is_mobile) {
    include '../includes/mobile_cabs.php'; // Or mobile_cab_detail.php if there was one (the code had include '../includes/mobile_cabs.php';)
} else {
    include '../includes/desktop_cab_detail.php';
}
exit;
