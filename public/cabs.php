<?php
$page_title = "Cab Booking | Premium Chauffeur Driven Cars";
require_once "../config/db.php";
require_once '../includes/functions.php';
csrf_stamp_form();

// Date default for date inputs
$today_date = date('Y-m-d');

// Fetch all active cab classes for vehicle type dropdowns
$cab_classes = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM cab_classes WHERE is_active=1 ORDER BY id ASC");
        $cab_classes = $stmt->fetchAll();
        
        // Attach lowest rate for next 90 days to each cab class
        $stmt = $pdo->query("
            SELECT cc.id as cab_class_id, MIN(vr.price) as min_rate
            FROM cab_classes cc
            JOIN vehicles v ON v.cab_class_id = cc.id AND v.is_active = 1
            JOIN vehicle_rates vr ON vr.vehicle_id = v.id AND vr.rate_date >= CURDATE() AND vr.rate_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
            WHERE cc.is_active = 1
            GROUP BY cc.id
        ");
        $min_rates = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        foreach ($cab_classes as &$cc) {
            $cc['min_rate'] = $min_rates[$cc['id']] ?? null;
        }
        unset($cc);

        // "Rent For" durations for the hourly tab (Rule 16: fetched once in controller)
        $stmt = $pdo->query("SELECT id, hours, km_limit, label FROM cab_durations WHERE is_active=1 ORDER BY sort_order ASC, hours ASC");
        $cab_durations = $stmt->fetchAll();

        // Active vehicles for mobile hero (Rule 16: fetched once in controller)
        $stmt = $pdo->query("
            SELECT v.id, v.name, v.image, v.pax_capacity, v.ac_type, c.name AS class_name
            FROM vehicles v
            LEFT JOIN cab_classes c ON v.cab_class_id = c.id
            WHERE v.is_active = 1
              AND v.image IS NOT NULL
              AND v.image <> ''
            ORDER BY v.id ASC
        ");
        $hero_vehicles = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch cab classes: " . $e->getMessage());
        $cab_classes = [];
        $cab_durations = [];
        $hero_vehicles = [];
    }
} else {
    $cab_durations = [];
    $hero_vehicles = [];
}

// Device detection
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

if (isset($_GET['view'])) {
    $is_mobile = ($_GET['view'] === 'mobile');
}

if ($is_mobile) {
    include '../includes/mobile_cabs.php';
} else {
    include '../includes/desktop_cabs.php';
}
exit;
