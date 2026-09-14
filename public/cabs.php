<?php
$page_title = "Cab Booking | Premium Chauffeur Driven Cars";
require_once "../config/db.php";

// Date default for date inputs
$today_date = date('Y-m-d');

// Fetch all active cab classes for vehicle type dropdowns
$cab_classes = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM cab_classes WHERE is_active=1 ORDER BY id ASC");
        $cab_classes = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch cab classes: " . $e->getMessage());
        $cab_classes = [];
    }
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
