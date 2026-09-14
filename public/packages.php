<?php
declare(strict_types=1);

require_once '../config/db.php';
require_once '../includes/functions.php';

// Device / View Detection
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isMobile = (bool) preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent)
    || (bool) preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

if (isset($_GET['view'])) {
    $isMobile = ($_GET['view'] === 'mobile');
}

$theme_filter = isset($_GET['theme']) ? trim($_GET['theme']) : '';
$dest_filter  = isset($_GET['destination']) ? trim($_GET['destination']) : '';

$page_title = "Curated Experiences | Leisure Loop Trip";
if (!empty($theme_filter)) {
    $page_title = htmlspecialchars($theme_filter) . " Escapes | Leisure Loop Trip";
} elseif (!empty($dest_filter)) {
    $page_title = htmlspecialchars($dest_filter) . " Signature Tours | Leisure Loop Trip";
}

// Redirect explicit search queries to the all-tours catalog
if (!empty($_GET['theme']) || !empty($_GET['type']) || !empty($_GET['destination']) || !empty($_GET['filter']) || !empty($_GET['q']) || !empty($_GET['package_type'])) {
    header("Location: all-tours.php?" . $_SERVER['QUERY_STRING']);
    exit;
}

// Model Data Retrieval
$packages   = [];
$active_tab = (isset($_GET['package_type']) && $_GET['package_type'] === 'fixed') ? 'fixed' : 'curated';

if ($pdo) {
    $type_filter_val = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
    $query = "SELECT * FROM packages WHERE is_active = 1 AND package_type = " . $pdo->quote($active_tab);
    if ($type_filter_val === 'domestic') {
        $query .= " AND is_international = 0";
    } elseif ($type_filter_val === 'international') {
        $query .= " AND is_international = 1";
    }
    $query .= " ORDER BY created_at DESC";
    try {
        $packages = $pdo->query($query)->fetchAll();
    } catch (PDOException $e) {
        error_log("packages.php query failed: " . $e->getMessage());
        $packages = [];
    }

    try {
        $hero_destinations = $pdo->query("SELECT name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 ORDER BY created_at DESC LIMIT 10")->fetchAll();
    } catch (Exception $e) { $hero_destinations = []; }

    try {
        $domestic_destinations = $pdo->query("SELECT id, name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 AND (category = 'domestic' OR category IS NULL) ORDER BY name ASC")->fetchAll();
    } catch (Exception $e) { 
        // Fallback: try without category filter
        try {
            $domestic_destinations = $pdo->query("SELECT id, name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        } catch (Exception $e2) { $domestic_destinations = []; }
    }

    try {
        $international_destinations = $pdo->query("SELECT id, name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 AND category = 'international' ORDER BY name ASC")->fetchAll();
    } catch (Exception $e) { $international_destinations = []; }
} else {
    $hero_destinations          = [];
    $domestic_destinations       = [];
    $international_destinations = [];
}



$all_themes       = [];
$all_destinations = [];
$all_durations    = [];
$max_price_in_db  = 0;
$min_price_in_db  = 999999;

foreach ($packages as $pkg) {
    if (!empty($pkg['tour_type'])) {
        foreach (explode(',', $pkg['tour_type']) as $t) {
            $t = trim($t);
            if ($t !== '' && !in_array($t, $all_themes, true)) $all_themes[] = $t;
        }
    }
    if (!empty($pkg['destination'])) {
        $d = trim($pkg['destination']);
        if (!in_array($d, $all_destinations, true)) $all_destinations[] = $d;
    }
    $days   = (int)($pkg['days'] ?? 0);
    $nights = (int)($pkg['nights'] ?? 0);
    if ($days > 0 || $nights > 0) {
        $key = $nights . '-' . $days;
        if (!isset($all_durations[$key])) {
            $all_durations[$key] = [
                'label'  => sprintf("%02d Nights / %02d Days", $nights, $days),
                'nights' => $nights,
                'days'   => $days
            ];
        }
    }
    $price = (float)$pkg['price'];
    if ($price > $max_price_in_db) $max_price_in_db = $price;
    if ($price < $min_price_in_db) $min_price_in_db = $price;
}

if (empty($packages)) {
    $min_price_in_db = 0;
    $max_price_in_db = 50000;
}

sort($all_themes);
sort($all_destinations);
uasort($all_durations, fn($a, $b) => $a['nights'] <=> $b['nights']);

// Themes — Single Source of Truth: tour_categories table (managed via admin/themes.php)
$curated_pkg_themes = [];
if ($pdo) {
    try {
        $theme_rows = $pdo->query("SELECT name, image_url, tagline FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
        foreach ($theme_rows as $tr) {
            $slug = strtolower(str_replace([' ', '&', '/'], '-', trim($tr['name'])));
            $curated_pkg_themes[] = [
                'title' => $tr['name'],
                'slug'  => $slug,
                'img'   => !empty($tr['image_url']) ? $tr['image_url'] : 'assets/img/pkg.jpg',
                'sub'   => !empty($tr['tagline']) ? $tr['tagline'] : 'Explore Now',
            ];
        }
    } catch (Exception $e) { $curated_pkg_themes = []; }
}

// ── Mobile Data Preparation (Rule 16 Compliance) ─────────────
if ($isMobile) {
    // 1. Fetch Mobile Packages Catalog (if not already hydrated)
    if (!isset($packages_mobile)) {
        try {
            $stmt_mob = $pdo->prepare("
                SELECT *
                FROM packages 
                WHERE is_active = 1 
                ORDER BY is_featured DESC, created_at DESC
            ");
            $stmt_mob->execute();
            $packages_mobile = $stmt_mob->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch mobile packages: " . $e->getMessage());
            $packages_mobile = [];
        }
    }

    // 2. Fetch Aggregated Filter Metadata
    try {
        $stmt_th = $pdo->query("SELECT DISTINCT tour_type FROM packages WHERE is_active = 1 AND tour_type IS NOT NULL AND tour_type != '' ORDER BY tour_type ASC");
        $all_themes = $stmt_th->fetchAll(PDO::FETCH_COLUMN);

        $stmt_dest = $pdo->query("SELECT DISTINCT destination FROM packages WHERE is_active = 1 AND destination IS NOT NULL AND destination != '' ORDER BY destination ASC");
        $all_destinations = $stmt_dest->fetchAll(PDO::FETCH_COLUMN);

        $stmt_price = $pdo->query("SELECT MIN(price) AS min_p, MAX(price) AS max_p FROM packages WHERE is_active = 1 AND price > 0");
        $price_bounds = $stmt_price->fetch(PDO::FETCH_ASSOC);
        $min_price = (float)($price_bounds['min_p'] ?? 5000);
        $max_price = (float)($price_bounds['max_p'] ?? 100000);
    } catch (PDOException $e) {
        $all_themes = [];
        $all_destinations = [];
        $min_price = 5000;
        $max_price = 100000;
    }

    // 3. Dispatch Mobile View
    include '../includes/mobile_packages.php';
    exit;
}

// Otherwise render desktop view
include '../includes/desktop_packages.php';
exit;
