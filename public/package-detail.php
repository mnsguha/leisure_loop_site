<?php
declare(strict_types=1);
require_once '../config/db.php';
require_once '../includes/functions.php';
csrf_stamp_form();


    $check_in = isset($_GET['check_in']) ? trim($_GET['check_in']) : date('Y-m-d');
    $check_out = isset($_GET['check_out']) ? trim($_GET['check_out']) : date('Y-m-d', strtotime('+3 days'));
    $rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 1;
    $adults = isset($_GET['adults']) ? (int)$_GET['adults'] : 2;
    $children = isset($_GET['children']) ? (int)$_GET['children'] : 0;
    $infants = isset($_GET['infants']) ? (int)$_GET['infants'] : 0;

    $slug = $_GET['slug'] ?? '';
    $pkg = null;

    if ($pdo && $slug) {
        try {
            $stmt = $pdo->prepare("SELECT p.*, d.terms_conditions as dest_terms FROM packages p LEFT JOIN destinations d ON p.destination = d.name WHERE p.slug = ? AND p.is_active = 1");
            $stmt->execute([$slug]);
            $pkg = $stmt->fetch();

            // Intelligent fallback: match prefixes, substrings, or normalized titles if historical/shortened slug is passed
            if (!$pkg) {
                $clean_slug = preg_replace('/-[0-9]+$/', '', $slug);
                $stmt = $pdo->prepare("SELECT p.*, d.terms_conditions as dest_terms FROM packages p LEFT JOIN destinations d ON p.destination = d.name WHERE (p.slug LIKE ? OR ? LIKE CONCAT(p.slug, '%') OR p.slug = ? OR REPLACE(LOWER(p.title), ' ', '-') LIKE ?) AND p.is_active = 1 LIMIT 1");
                $stmt->execute([
                    $clean_slug . '%', 
                    $slug, 
                    $clean_slug,
                    '%' . str_replace('-', '%', $clean_slug) . '%'
                ]);
                $pkg = $stmt->fetch();
            }
        } catch (PDOException $e) {
            error_log("package-detail.php query failed: " . $e->getMessage());
            $pkg = null;
        }
    }

    $package_departures = [];
    if ($pkg && ($pkg['package_type'] ?? '') === 'fixed' && $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM fixed_departures WHERE package_id = ? AND is_active = 1 AND start_date >= CURDATE() ORDER BY start_date ASC");
            $stmt->execute([$pkg['id']]);
            $package_departures = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("package-detail.php departures query failed: " . $e->getMessage());
            $package_departures = [];
        }
    }

    if (!$pkg) {
        header('Location: packages.php');
        exit;
    }

    $itinerary = json_decode($pkg['itinerary'], true) ?: [];

    // Meta attributes
    $page_title = $pkg['title'] . " | Leisure Loop Trip";
    $meta_desc = "Explore " . $pkg['title'] . ". Starting at INR " . number_format((float)$pkg['price']) . ". Bespoke " . count($itinerary) . "-day cinematic journey curated by Leisure Loop.";
    $meta_image = $pkg['image_url'] ?? 'images/placeholder.jpg';
    $meta_keywords = $pkg['title'] . ", luxury tour, bespoke travel, " . count($itinerary) . " day itinerary";

    // Dynamic Variables & Fallbacks
    $rating_score = !empty($pkg['rating_score']) ? (float)$pkg['rating_score'] : 4.8;
    $rating_count = !empty($pkg['rating_count']) ? (int)$pkg['rating_count'] : 150;
    $tour_code = !empty($pkg['tour_code']) ? htmlspecialchars($pkg['tour_code']) : 'VD-' . str_pad((string)$pkg['id'], 4, '0', STR_PAD_LEFT);
    $tour_type = !empty($pkg['tour_type']) ? htmlspecialchars($pkg['tour_type']) : 'Honeymoon, Hill station, Wild Life Tour';
    $original_price = !empty($pkg['original_price']) ? (float)$pkg['original_price'] : null;
    
    $highlights_arr = !empty($pkg['highlights'])
        ? array_filter(array_map('trim', explode("\n", $pkg['highlights'])))
        : [];

    $inclusions_arr = !empty($pkg['inclusions'])
        ? array_filter(array_map('trim', explode("\n", $pkg['inclusions'])))
        : [];

    $exclusions_arr = !empty($pkg['exclusions'])
        ? array_filter(array_map('trim', explode("\n", $pkg['exclusions'])))
        : [];

    $description_rich = !empty($pkg['description_rich']) ? $pkg['description_rich'] : '';

    $photos_arr = [];
    if ($pdo && $pkg) {
        try {
            $stmt = $pdo->prepare("SELECT image_url FROM package_images WHERE package_id = ? ORDER BY display_order ASC");
            $stmt->execute([$pkg['id']]);
            $photos_arr = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {}
    }
    
    // Graceful fallback: use main package image if gallery is empty
    if (empty($photos_arr) && !empty($pkg['image_url'])) {
        $photos_arr = [$pkg['image_url']];
    }

    // Load dynamic destinations list for form
    $destinations_list = [];
    try {
        $destinations_list = $pdo ? $pdo->query("SELECT name FROM destinations WHERE is_active=1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_COLUMN) : [];
    } catch (Exception $e) { $destinations_list = []; }

    // Bespoke Stay & Fleet Options (Hotel Star Categories & Cabs)
    $hotel_categories = [
        [
            "stars" => "2 Star",
            "stars_display" => "★★",
            "title" => "Comfort & Clean Stay",
            "tagline" => "Essential Comfort",
            "desc" => "Hygienic, cozy rooms with private bathrooms, scenic balcony spots, and warm regional hospitality.",
            "img" => "images/hotels/2star.jpg",
            "features" => ["En-suite Bathroom", "Daily Breakfast", "Valley View", "Free WiFi"]
        ],
        [
            "stars" => "3 Star",
            "stars_display" => "★★★",
            "title" => "Premium/Boutique Stay",
            "tagline" => "Boutique Character",
            "desc" => "Handpicked boutique heritage houseboats or hotels featuring authentic architecture and upscale dining.",
            "img" => "images/hotels/3star.jpg",
            "features" => ["Curated Dining", "Heated Rooms", "Prime Location", "Room Service"]
        ],
        [
            "stars" => "4 Star",
            "stars_display" => "★★★★",
            "title" => "Luxury Retreat",
            "tagline" => "Recommended",
            "desc" => "Superior luxury properties offering executive suites, gourmet Wazwan/continental cuisine, and VIP treatment.",
            "img" => "images/hotels/4star.webp",
            "features" => ["VIP Welcome Drinks", "Multi-Cuisine Dining", "Private Lounge", "Express Transfers"],
            "recommended" => true
        ],
        [
            "stars" => "5 Star",
            "stars_display" => "★★★★★",
            "title" => "Ultra Luxury",
            "tagline" => "Royal Indulgence",
            "desc" => "World-class signature palace hotels, lavish suites, private butler assistance, and exclusive spa access.",
            "img" => "https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=700",
            "features" => ["Private Butler", "Spa & Wellness", "Chauffeur on Standby", "Gourmet Grid"]
        ]
    ];

    $cab_types = [
        [
            "name" => "Wagon R/Swift Dzire",
            "category" => "Compact Comfort",
            "seats" => "4 Seats",
            "bags" => "2 Bags",
            "desc" => "Economical and agile, ideal for couples or solo adventurers exploring scenic circuits with light baggage.",
            "img" => "uploads/1786989749_wagonr.webp",
            "features" => ["Air-Conditioned", "Experienced Chauffeur", "Compact Mobility"]
        ],
        [
            "name" => "Sumo/Bolero",
            "category" => "Multi-Terrain Utility",
            "seats" => "6-7 Seats",
            "bags" => "4 Bags",
            "desc" => "Rugged workhorses engineered for challenging high-altitude mountain ascents and unpaved scenic trails.",
            "img" => "uploads/bolero.webp",
            "features" => ["High Ground Clearance", "Mountain Specialist Driver", "Roof Luggage Rack"]
        ],
        [
            "name" => "Innova/Xylo",
            "category" => "Executive SUV",
            "seats" => "6-7 Seats",
            "bags" => "5 Bags",
            "desc" => "Our recommended executive choice featuring plush captain seating, strong climate control, and smooth highway absorption.",
            "img" => "uploads/1786990397_crysta.webp",
            "features" => ["Captain Seating", "Superior Absorption", "VIP Ambient Quietness"],
            "recommended" => true
        ],
        [
            "name" => "Tempo Traveller",
            "category" => "Luxury Group Coach",
            "seats" => "12-16 Seats",
            "bags" => "12+ Bags",
            "desc" => "Spacious high-roof luxury coach with push-back recliners, large panoramic viewing windows, and heavy luggage capacity.",
            "img" => "uploads/1786990498_force.webp",
            "features" => ["Push-Back Recliners", "Panoramic Windows", "Dedicated Luggage Vault"]
        ]
    ];

    // Fetch Other Tours (Same Destination)
    $other_tours = [];
    if ($pdo && $pkg) {
        try {
            $stmt = $pdo->prepare("SELECT title, slug, price, image_url, nights, days FROM packages WHERE destination = ? AND id != ? AND is_active = 1 LIMIT 6");
            $stmt->execute([$pkg['destination'], $pkg['id']]);
            $other_tours = $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("package-detail.php other tours query failed: " . $e->getMessage());
            $other_tours = [];
        }
    }

    // Detect Mobile
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
                 || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

    if (isset($_GET['view'])) {
        $is_mobile = ($_GET['view'] === 'mobile');
    }

    if ($is_mobile) {
        include '../includes/mobile_package_details.php';
    } else {
        include '../includes/desktop_package_details.php';
    }
    exit;
?>

