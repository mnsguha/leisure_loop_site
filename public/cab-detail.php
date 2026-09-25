<?php
$page_title = "Cab Details | Leisure Loop";
$body_class = 'page-cab-detail';
require_once "../config/db.php";

// Rule 9: session, CSRF token + form time-trap stamp for POST checkout forms
require_once '../includes/functions.php';
csrf_stamp_form();

// Extract incoming search query parameters for pre-filling
$today_date = date('Y-m-d');
$s_type = isset($_GET['type']) ? trim($_GET['type']) : 'oneway';

// Display strings (Rule 16: calculated once in controller)
switch ($s_type) {
    case 'hourly':    $display_service_options = 'Hourly'; break;
    case 'itinerary': $display_service_options = 'Disposal, Point to Point'; break;
    default:          $display_service_options = 'Oneway'; break;
}
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
        
        $stmt = $pdo->prepare("
            SELECT v.*, cc.name AS cab_class_name 
            FROM vehicles v 
            LEFT JOIN cab_classes cc ON v.cab_class_id = cc.id 
            WHERE v.cab_class_id = ? AND v.is_active=1 
            ORDER BY v.name ASC
        ");
        $stmt->execute([$cab_class ? $cab_class['id'] : 0]);
        $vehicles = $stmt->fetchAll();
    } else {
        $cab_class = null;
        $stmt = $pdo->query("
            SELECT v.*, cc.name AS cab_class_name 
            FROM vehicles v 
            LEFT JOIN cab_classes cc ON v.cab_class_id = cc.id 
            WHERE v.is_active=1 
            ORDER BY v.name ASC
        ");
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

    $stmt = $pdo->prepare("
        SELECT v.*, cc.name AS cab_class_name 
        FROM vehicles v 
        LEFT JOIN cab_classes cc ON v.cab_class_id = cc.id 
        WHERE v.cab_class_id = ? AND v.is_active=1 
        ORDER BY v.name ASC
    ");
    $stmt->execute([$id]);
    $vehicles = $stmt->fetchAll();
}

// Hourly duration in hours (0 = daily/itinerary pricing)
$hours = ($s_type === 'hourly' && !empty($s_duration)) ? (int)$s_duration : 0;

// Fetch future vehicle rates scoped to this page's vehicles (Rule 16: single fetch in controller)
$all_vehicle_rates = [];
$fallback_mins = [];

if (!empty($vehicles)) {
    if (!empty($cab_class)) {
        $ratesStmt = $pdo->prepare("
            SELECT vr.vehicle_id, vr.rate_date, vr.price, vr.price_per_hour, vr.extra_km_rate
            FROM vehicle_rates vr
            JOIN vehicles v ON v.id = vr.vehicle_id
            WHERE v.cab_class_id = ? AND vr.rate_date >= CURDATE()
        ");
        $ratesStmt->execute([(int)$cab_class['id']]);
    } else {
        $ratesStmt = $pdo->query("
            SELECT vr.vehicle_id, vr.rate_date, vr.price, vr.price_per_hour, vr.extra_km_rate
            FROM vehicle_rates vr
            JOIN vehicles v ON v.id = vr.vehicle_id
            WHERE v.is_active = 1 AND vr.rate_date >= CURDATE()
        ");
    }
    while ($row = $ratesStmt->fetch(PDO::FETCH_ASSOC)) {
        $all_vehicle_rates[$row['vehicle_id']][$row['rate_date']] = [
            'price' => $row['price'],
            'price_per_hour' => $row['price_per_hour'],
            'extra_km_rate' => $row['extra_km_rate'],
        ];
    }

    // Single grouped 90-day fallback minimum per vehicle (replaces per-vehicle N+1 queries)
    if (!empty($cab_class)) {
        $minStmt = $pdo->prepare("
            SELECT vr.vehicle_id,
                   MIN(vr.price) AS min_daily,
                   MIN(COALESCE(vr.price_per_hour, vr.price)) AS min_per_hour,
                   MIN(vr.extra_km_rate) AS min_extra_km
            FROM vehicle_rates vr
            JOIN vehicles v ON v.id = vr.vehicle_id
            WHERE v.cab_class_id = ?
              AND vr.rate_date >= CURDATE()
              AND vr.rate_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
            GROUP BY vr.vehicle_id
        ");
        $minStmt->execute([(int)$cab_class['id']]);
    } else {
        $minStmt = $pdo->query("
            SELECT vr.vehicle_id,
                   MIN(vr.price) AS min_daily,
                   MIN(COALESCE(vr.price_per_hour, vr.price)) AS min_per_hour,
                   MIN(vr.extra_km_rate) AS min_extra_km
            FROM vehicle_rates vr
            JOIN vehicles v ON v.id = vr.vehicle_id
            WHERE v.is_active = 1
              AND vr.rate_date >= CURDATE()
              AND vr.rate_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
            GROUP BY vr.vehicle_id
        ");
    }
    while ($row = $minStmt->fetch(PDO::FETCH_ASSOC)) {
        $fallback_mins[$row['vehicle_id']] = [
            'min_daily' => $row['min_daily'],
            'min_per_hour' => $row['min_per_hour'],
            'min_extra_km' => $row['min_extra_km'],
        ];
    }

    // Compute display rate per vehicle (bcmath only — no float math, Rule 7)
    foreach ($vehicles as $i => $v) {
        $vid = (int)$v['id'];
        $rate = null;
        if (!empty($s_date) && isset($all_vehicle_rates[$vid][$s_date])) {
            $dayRates = $all_vehicle_rates[$vid][$s_date];
            if ($hours > 0 && $dayRates['price_per_hour'] !== null && $dayRates['price_per_hour'] !== '') {
                $rate = bcmul((string)$dayRates['price_per_hour'], (string)$hours, 2);
            } else {
                $rate = $dayRates['price'];
            }
        } elseif (isset($fallback_mins[$vid])) {
            if ($hours > 0) {
                $minPerHour = $fallback_mins[$vid]['min_per_hour'];
                $rate = ($minPerHour !== null && $minPerHour !== '')
                    ? bcmul((string)$minPerHour, (string)$hours, 2)
                    : null;
            } else {
                $rate = $fallback_mins[$vid]['min_daily'];
            }
        }
        // Zero rate renders as N/A (matches JS behaviour)
        if ($rate !== null && $rate !== '' && bccomp((string)$rate, '0', 2) === 0) {
            $rate = null;
        }
        $vehicles[$i]['display_rate'] = $rate;
    }

    // Sort ascending by display rate; unrated vehicles last
    usort($vehicles, static function (array $a, array $b): int {
        $ra = $a['display_rate'] ?? null;
        $rb = $b['display_rate'] ?? null;
        if ($ra === null && $rb === null) {
            return 0;
        }
        if ($ra === null) {
            return 1;
        }
        if ($rb === null) {
            return -1;
        }
        return bccomp((string)$ra, (string)$rb, 2);
    });
}

// Class-header starting rate (rendered in view; Rule 16: calculated once in controller)
$class_min_rate = null;
if (!$is_search && !empty($cab_class)) {
    $stmt = $pdo->prepare("
        SELECT MIN(vr.price) AS min_rate
        FROM vehicles v
        JOIN vehicle_rates vr ON vr.vehicle_id = v.id
            AND vr.rate_date >= CURDATE()
            AND vr.rate_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
        WHERE v.cab_class_id = ? AND v.is_active = 1
    ");
    $stmt->execute([(int)$cab_class['id']]);
    $class_min_rate = $stmt->fetchColumn();
    if ($class_min_rate === false) {
        $class_min_rate = null;
    }
}

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

// Rule 16: deterministic platform detection with query override
if (isset($_GET['view'])) {
    $is_mobile = ($_GET['view'] === 'mobile');
}

// Fetch all cab classes for the dropdown
$stmt = $pdo->query("SELECT * FROM cab_classes WHERE is_active=1 ORDER BY id ASC");
$cab_classes = $stmt->fetchAll();

// Fetch "Rent For" durations for the hourly tab (Rule 16: fetched once in controller)
$stmt = $pdo->query("SELECT id, hours, km_limit, label FROM cab_durations WHERE is_active=1 ORDER BY sort_order ASC, hours ASC");
$cab_durations = $stmt->fetchAll();

// Hourly kilometer context (Rule 16: composed once in controller)
$selected_km_limit = null;
if ($s_type === 'hourly' && $s_duration !== '') {
    foreach ($cab_durations as $cd) {
        if ((string)$cd['hours'] === $s_duration) {
            $selected_km_limit = $cd['km_limit'];
            break;
        }
    }
}

$footer_duration_text = '';
if ($hours > 0) {
    $footer_duration_text = ($selected_km_limit !== null)
        ? 'For ' . $hours . ' hr and ' . (int)$selected_km_limit . ' Km.'
        : 'For ' . $hours . ' hr.';
}

// Per-vehicle "Kilometer Charges" line (hourly searches only)
if ($hours > 0 && !empty($vehicles)) {
    foreach ($vehicles as $i => $v) {
        $vid = (int)$v['id'];
        $extra = null;
        if (!empty($s_date)
                && isset($all_vehicle_rates[$vid][$s_date])
                && array_key_exists('extra_km_rate', $all_vehicle_rates[$vid][$s_date])
                && $all_vehicle_rates[$vid][$s_date]['extra_km_rate'] !== null) {
            $extra = $all_vehicle_rates[$vid][$s_date]['extra_km_rate'];
        } elseif (isset($fallback_mins[$vid]['min_extra_km']) && $fallback_mins[$vid]['min_extra_km'] !== null) {
            $extra = $fallback_mins[$vid]['min_extra_km'];
        }

        if ($extra !== null && $extra !== '' && $selected_km_limit !== null) {
            $rateStr = rtrim(rtrim(number_format((float)$extra, 2, '.', ''), '0'), '.');
            $vehicles[$i]['km_charges_text'] = (int)$selected_km_limit
                . ' kms included after that ' . $rateStr . '/km charge applicable';
        } elseif ($selected_km_limit !== null) {
            $vehicles[$i]['km_charges_text'] = (int)$selected_km_limit
                . ' kms included in this rental';
        }
    }
}

// Rule 16: isolate mobile/desktop views + enqueue only view-scoped scripts
if ($is_mobile) {
    $extra_scripts = '<script src="js/modules/cabs.js?v=' . time() . '" defer></script>'
        . '<script src="js/modules/mobile-cab-detail.js?v=' . time() . '" defer></script>';
    include '../includes/mobile_cab_detail.php';
} else {
    include '../includes/desktop_cab_detail.php';
}
exit;
