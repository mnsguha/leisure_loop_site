<?php
    require_once '../config/db.php';
    require_once '../config/recaptcha.php';
    require_once '../includes/functions.php';
    $use_recaptcha = recaptchaIsConfigured();
    $recaptcha_site_key = recaptchaSiteKey();


    $check_in = isset($_GET['check_in']) ? trim($_GET['check_in']) : date('Y-m-d');
    $check_out = isset($_GET['check_out']) ? trim($_GET['check_out']) : date('Y-m-d', strtotime('+3 days'));
    $rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 1;
    $adults = isset($_GET['adults']) ? (int)$_GET['adults'] : 2;
    $children = isset($_GET['children']) ? (int)$_GET['children'] : 0;
    $infants = isset($_GET['infants']) ? (int)$_GET['infants'] : 0;

    $slug = $_GET['slug'] ?? '';
    $pkg = null;

    if ($pdo && $slug) {
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
    }

    $package_departures = [];
    if ($pkg && ($pkg['package_type'] ?? '') === 'fixed' && $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM fixed_departures WHERE package_id = ? AND is_active = 1 AND start_date >= CURDATE() ORDER BY start_date ASC");
        $stmt->execute([$pkg['id']]);
        $package_departures = $stmt->fetchAll();
    }

    if (!$pkg) {
        header('Location: packages.php');
        exit;
    }

    $itinerary = json_decode($pkg['itinerary'], true) ?: [];

    // Meta attributes
    $page_title = $pkg['title'] . " | Leisure Loop Trip";
    $meta_desc = "Explore " . $pkg['title'] . ". Starting at INR " . number_format($pkg['price']) . ". Bespoke " . count($itinerary) . "-day cinematic journey curated by Leisure Loop.";
    $meta_image = $pkg['image_url'];
    $meta_keywords = $pkg['title'] . ", luxury tour, bespoke travel, " . count($itinerary) . " day itinerary";

    // Dynamic Variables & Fallbacks
    $rating_score = !empty($pkg['rating_score']) ? (float)$pkg['rating_score'] : 4.8;
    $rating_count = !empty($pkg['rating_count']) ? (int)$pkg['rating_count'] : 150;
    $tour_code = !empty($pkg['tour_code']) ? htmlspecialchars($pkg['tour_code']) : 'VD-' . str_pad($pkg['id'], 4, '0', STR_PAD_LEFT);
    $tour_type = !empty($pkg['tour_type']) ? htmlspecialchars($pkg['tour_type']) : 'Honeymoon, Hill station, Wild Life Tour';
    $original_price = !empty($pkg['original_price']) ? (float)$pkg['original_price'] : null;
    
    $highlights_arr = [];
    if (!empty($pkg['highlights'])) {
        $highlights_arr = array_filter(array_map('trim', explode("\n", $pkg['highlights'])));
    } else {
        $highlights_arr = [
            "Exclusive Curated Sightseeing Grid Highlights",
            "Premium Boutique Heritage Accommodations",
            "Private Luxury Chauffeur & Logistics Support",
            "Bespoke Local Experience Coordinator Access"
        ];
    }

    $inclusions_arr = [];
    if (!empty($pkg['inclusions'])) {
        $inclusions_arr = array_filter(array_map('trim', explode("\n", $pkg['inclusions'])));
    } else {
        $inclusions_arr = [
            "Ultra-Premium Boutique Accommodations",
            "Private Luxury Saloon Transportation",
            "Bespoke Professional Experience Host",
            "Curated Dining Grid Permitted Options",
            "VIP Permits & Priority Entry Accents"
        ];
    }

    $exclusions_arr = [];
    if (!empty($pkg['exclusions'])) {
        $exclusions_arr = array_filter(array_map('trim', explode("\n", $pkg['exclusions'])));
    } else {
        $exclusions_arr = [
            "Inter-state Flight & Transit Tickets",
            "Personal Discretionary Expenses",
            "Gratuities and Driver Incentives"
        ];
    }

    $description_rich = !empty($pkg['description_rich']) ? $pkg['description_rich'] : '';

    $photos_arr = [];
    if ($pdo && $pkg) {
        try {
            $stmt = $pdo->prepare("SELECT image_url FROM package_images WHERE package_id = ? ORDER BY display_order ASC");
            $stmt->execute([$pkg['id']]);
            $photos_arr = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {}
    }
    
    // Fallback if no images are uploaded in the gallery
    if (empty($photos_arr)) {
        // Use the main card image if available, else static placeholders
        if (!empty($pkg['image_url'])) {
            $photos_arr = [$pkg['image_url']];
        } else {
            $photos_arr = [
                "images/pkg/stitch_img_1.jpg",
                "images/pkg/stitch_img_2.jpg",
                "images/pkg/stitch_img_6.jpg"
            ];
        }
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

    // Detect Mobile
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
                 || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    
    if ($is_mobile) {
        include '../includes/mobile_package_details.php';
        exit;
    }

    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/package-detail.css?v=<?php echo time(); ?>">


<!-- Custom Premium Fonts and Leaflet map styling -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Flatpickr for Date Range -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Tailwind CSS for layout utilities (hotel/cab cards, responsive grid) -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&amp;family=Inter:wght@300;400;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

<div id="pkg-data-store" hidden
    data-gallery="<?php echo isset($all_images) ? htmlspecialchars(json_encode(array_values($all_images)), ENT_QUOTES, 'UTF-8') : '[]'; ?>"
    data-itinerary="<?php echo isset($itinerary) ? htmlspecialchars(json_encode(array_values($itinerary)), ENT_QUOTES, 'UTF-8') : '[]'; ?>"
    data-coords="<?php 
        $mc = $pkg['map_coords'] ?? '';
        $parts = explode(',', $mc);
        echo (count($parts) === 2) ? ((float)trim($parts[0])) . ',' . ((float)trim($parts[1])) : '27.3314,88.6138';
    ?>">
</div>
<script src="js/modules/package-detail.js?v=<?php echo time(); ?>" defer></script>

<div class="package-detail-page">

<!-- Top Search Bar -->
<div class="search-bar-container">
    <div class="search-bar">
        <input type="hidden" id="inputCheckIn" value="<?php echo htmlspecialchars($check_in); ?>">
        <input type="hidden" id="inputCheckOut" value="<?php echo htmlspecialchars($check_out); ?>">
        <input type="hidden" id="inputRooms" value="<?php echo (int)$rooms; ?>">
        <input type="hidden" id="inputAdults" value="<?php echo (int)$adults; ?>">
        <input type="hidden" id="inputChildren" value="<?php echo (int)$children; ?>">
        <input type="hidden" id="inputInfants" value="<?php echo (int)$infants; ?>">

        <div class="search-input-group search-group-wide">
            <div class="search-field-label">DESTINATION</div>
            <input type="text" value="<?php echo htmlspecialchars($pkg['title']); ?>" readonly>
        </div>
        <div class="search-input-group">
            <div class="search-field-label">TRAVEL DATES</div>
            <input type="text" value="<?php echo date('M d, Y', strtotime($check_in)); ?> - <?php echo date('M d, Y', strtotime($check_out)); ?>" id="displayDates" readonly class="cursor-pointer">
        </div>
        <div class="search-input-group search-group-relative" id="guestsContainer">
            <div class="search-field-label">GUESTS</div>
            <input type="text" value="<?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?>" id="displayGuests" readonly class="cursor-pointer">
            
            <!-- Guests Dropdown -->
            <div class="guests-dropdown" id="guestsDropdown">
                <div class="guest-row">
                    <div class="guest-label">Adults</div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="btnAdultsMinus" <?php echo $adults <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valAdults"><?php echo $adults; ?></span>
                        <button type="button" class="btn-count" id="btnAdultsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">
                        Children
                        <span class="guest-sublabel">2 - 11 Years Old</span>
                    </div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="btnChildrenMinus" <?php echo $children <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valChildren"><?php echo $children; ?></span>
                        <button type="button" class="btn-count" id="btnChildrenPlus">+</button>
                    </div>
                </div>
                <button type="button" class="btn-apply-guests" id="btnApplyGuests">APPLY</button>
            </div>
        </div>
        <button type="button" class="btn-search" id="btnPerformSearch" data-href="package-detail.php?slug=<?php echo urlencode($pkg['slug']); ?>">SEARCH</button>
    </div>
</div>

<?php 
$all_images = [];
if (!empty($pkg['image_url']) && trim($pkg['image_url']) !== '') {
    $all_images[] = trim($pkg['image_url']);
}
foreach ($photos_arr as $p) {
    $p = trim($p);
    if ($p !== '' && $p !== trim($pkg['image_url'])) {
        $all_images[] = $p;
    }
}
?>

<!-- Main Details Layout -->
<div class="detail-container">

    <!-- Details Ribbon moved to Main Content -->

<!-- Header Info -->
<div class="package-header-card" style="margin-top: 0;">
    <h1 class="detail-title">
        <?php echo htmlspecialchars($pkg['title']); ?> 
    </h1>
    <div class="detail-meta">
        <?php echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT); ?> Nights / <?php echo str_pad($pkg['days'], 2, '0', STR_PAD_LEFT); ?> Days in <?php echo htmlspecialchars($pkg['destination']); ?>
    </div>
    
    <div class="rating-badges">
        <div class="rating-badge-main">
            <?php echo number_format($rating_score, 1); ?> 
            <span style="font-weight: 400; font-size: 0.85rem;">GUEST EXPERIENCES</span>
        </div>
        <div style="color: #666; font-size: 0.85rem; display: flex; align-items: center;">
            (<?php echo $rating_count; ?> reviews)
        </div>
        <div class="rating-badge"><?php echo htmlspecialchars($tour_type); ?></div>
    </div>
</div>

<!-- Bento Box Gallery -->
<?php if(!empty($all_images) && count($all_images) >= 3): ?>
<div class="bento-gallery">
    <img src="<?php echo htmlspecialchars($all_images[0]); ?>" class="bento-item bento-main" data-action="open-lightbox" data-idx="0">
    <img src="<?php echo htmlspecialchars($all_images[1]); ?>" class="bento-item" data-action="open-lightbox" data-idx="1">
    <img src="<?php echo htmlspecialchars($all_images[2]); ?>" class="bento-item" data-action="open-lightbox" data-idx="2">
    
    <?php if(isset($all_images[3])): ?>
        <img src="<?php echo htmlspecialchars($all_images[3]); ?>" class="bento-item" data-action="open-lightbox" data-idx="3">
    <?php endif; ?>
    
    <?php if(isset($all_images[4])): ?>
        <div class="bento-overlay-container" data-action="open-lightbox" data-idx="4">
            <img src="<?php echo htmlspecialchars($all_images[4]); ?>" class="bento-item">
            <?php if(count($all_images) > 5): ?>
                <div class="bento-overlay">+<?php echo count($all_images) - 5; ?> More</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php elseif(!empty($all_images)): ?>
    <img src="<?php echo htmlspecialchars($all_images[0]); ?>" class="bento-single-img" data-action="open-lightbox" data-idx="0">
<?php endif; ?>

    <!-- Details Ribbon (Tour Code Pill) -->
    <div class="details-ribbon" style="margin-bottom: 2rem;">
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <span class="material-symbols-outlined">qr_code</span>
            </div>
            <div class="ribbon-text">
                <span>TOUR CODE</span>
                <strong><?php echo $tour_code; ?></strong>
            </div>
        </div>
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <span class="material-symbols-outlined">explore</span>
            </div>
            <div class="ribbon-text">
                <span>DESTINATION</span>
                <strong><?php echo htmlspecialchars($pkg['destination']); ?></strong>
            </div>
        </div>
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <span class="material-symbols-outlined">hiking</span>
            </div>
            <div class="ribbon-text">
                <span>ESCAPE LEVEL</span>
                <strong><?php echo $tour_type; ?></strong>
            </div>
        </div>
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <span class="material-symbols-outlined">payments</span>
            </div>
            <div class="ribbon-text">
                <span>DYNAMIC PRICING</span>
                <strong>From ₹<?php echo number_format($pkg['price']); ?></strong>
            </div>
        </div>
    </div>

<!-- Overview -->
    <div class="package-columns">
        <div class="main-content">
            <div class="content-spacing">

<section class="overview-section detail-section-spacing">
<p class="section-subtitle">THE CURATION</p>
<h2 class="section-title">Overview</h2>

<div class="overview-text">
    <?php echo nl2br(htmlspecialchars(!empty($pkg['description_rich']) ? $pkg['description_rich'] : "Experience a masterfully curated journey that intertwines luxury and authenticity. Discover iconic landmarks, boutique accommodations, and bespoke local experiences designed exclusively for the discerning traveler.")); ?>
</div>

<h3 class="highlights-title">Tour Highlights</h3>
<?php if (!empty($highlights_arr)): ?>
<div class="highlights-grid">
    <?php foreach ($highlights_arr as $hl): ?>
    <div class="highlight-item">
        <span class="material-symbols-outlined icon-verified">verified</span>
        <p><?php echo htmlspecialchars($hl); ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</section>

        <!-- ✦ Trust Badges Strip ✦ -->
            <div class="trust-badges-strip detail-section-spacing">
                <?php
                $trust_badges = [
                    ['icon' => '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>', 'label' => 'Safe Travel'],
                    ['icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>', 'label' => 'Flexible Plans'],
                    ['icon' => '<path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>', 'label' => 'Easy Booking'],
                    ['icon' => '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>', 'label' => 'Expert Guides'],
                    ['icon' => '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>', 'label' => '24/7 Support'],
                ];
                foreach ($trust_badges as $badge):
                ?>
                <div class="trust-badge-item">
                    <div class="trust-badge-icon">
                        <svg viewBox="0 0 24 24"><?php echo $badge['icon']; ?></svg>
                    </div>
                    <span class="trust-badge-label"><?php echo $badge['label']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>

        <!-- ✦ Interactive Accordion Itinerary ✦ -->
            <section class="itinerary-section detail-section-spacing">
                <span class="section-label">THE ITINERARY</span>
                <h2 class="serif-accent section-title"><?php echo htmlspecialchars($pkg['itinerary_heading'] ?? 'Day-by-Day Journey'); ?></h2>

                <div id="itinerary-accordion" class="glass-acc-container">
                    <?php foreach ($itinerary as $i => $day): ?>
                    <div class="glass-acc-item">
                        <button type="button" class="glass-acc-trigger" data-index="<?php echo $i; ?>" data-coords="<?php echo htmlspecialchars($day['coords'] ?? ''); ?>">
                            <div class="glass-acc-left">
                                <div class="acc-day-col">
                                    <span class="acc-day-label">Day</span>
                                    <span class="glass-acc-num"><?php echo str_pad((string)($day['day'] ?? $i+1), 2, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="glass-acc-title-group">
                                    <span class="glass-acc-title"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                                </div>
                            </div>
                            <span class="glass-acc-chevron">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div class="glass-acc-body">
                            <p class="text-slate-400 text-[0.95rem] leading-[1.8] pb-6">
                                <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            
        
        <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
            <section class="detail-section-spacing fixed-departure-section">
                <span class="section-label logistics-label">LOGISTICS & STAY</span>
                <h2 class="serif-accent fixed-departure-title">Fixed Departure <span class="fixed-departure-subtitle">Details</span></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php if (!empty($pkg['hotel_details'])): ?>
                    <div class="glass-card p-6 rounded-2xl fixed-departure-card">
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 mb-4">
                            <span class="material-symbols-outlined">hotel</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">Accommodations</h4>
                        <div class="text-sm text-on-surface-variant leading-relaxed">
                            <?php echo nl2br($pkg['hotel_details']); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pkg['vehicle_details'])): ?>
                    <div class="glass-card p-6 rounded-2xl fixed-departure-card">
                        <div class="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400 mb-4">
                            <span class="material-symbols-outlined">directions_car</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">Transportation</h4>
                        <div class="text-sm text-on-surface-variant leading-relaxed">
                            <?php echo nl2br($pkg['vehicle_details']); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pkg['meal_plan_details'])): ?>
                    <div class="glass-card p-6 rounded-2xl fixed-departure-card">
                        <div class="w-12 h-12 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-400 mb-4">
                            <span class="material-symbols-outlined">restaurant</span>
                        </div>
                        <h4 class="font-bold text-white mb-2">Meal Plan</h4>
                        <div class="text-sm text-on-surface-variant leading-relaxed">
                            <?php echo nl2br($pkg['meal_plan_details']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

<!-- ✦ BESPOKE STAY & FLEET OPTIONS ✦ -->
<section class="detail-section-spacing" id="bespoke-showcase">
    <!-- Hotel Star Categories Showcase -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row mb-8 gap-4 w-full">
            <div class="w-full">
                <span class="font-label-caps text-secondary text-xs tracking-widest block mb-2">CURATED ACCOMMODATION TIERS</span>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-1">
                    <h2 class="serif-accent text-2xl md:text-3xl font-normal text-white">Select Your Hotel Category</h2>
                    <div class="text-[11px] md:text-xs text-secondary flex items-center gap-1 bg-secondary/10 px-3 py-1.5 rounded-full border border-secondary/20 w-fit shrink-0">
                        <span class="material-symbols-outlined text-sm">verified</span> Handpicked verified properties
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm md:text-base mt-2 max-w-2xl">
                    Tailor your journey's ambiance. Whether you desire essential cozy comfort or royal palatial suites, our handpicked properties guarantee world-class Himalayan hospitality.
                </p>
            </div>
        </div>

        <!-- Compact Selection Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <?php foreach ($hotel_categories as $idx => $stay): 
                $is_sel = !empty($stay['recommended']); 
            ?>
            <label class="compact-stay-card <?php echo $is_sel ? 'selected-stay border-secondary/60 bg-secondary/5 selected-stay-bg' : 'border-white/10 bg-white/5'; ?> p-5 flex flex-col items-center justify-center text-center cursor-pointer border rounded-xl hover:border-secondary/40 transition-all duration-300 relative" id="stay-card-<?php echo $idx; ?>" data-stay-idx="<?php echo $idx; ?>" data-stay-label="<?php echo htmlspecialchars($stay['stars'] . ' ' . explode(' ', $stay['title'])[0]); ?>">
                <input type="radio" name="hotel_selection_radio" value="<?php echo $idx; ?>" class="sr-only stay-radio-btn" <?php echo $is_sel ? 'checked' : ''; ?>>
                <div class="text-secondary font-bold text-sm tracking-widest mb-1"><?php echo $stay['stars_display']; ?></div>
                <h3 class="text-white font-medium text-[13px] leading-snug mb-4 h-10 flex items-center justify-center"><?php echo htmlspecialchars($stay['title']); ?></h3>
                
                <div class="w-full flex items-center justify-center gap-2 py-2 rounded-lg border text-xs transition-all duration-300 stay-indicator">
                    <span class="material-symbols-outlined text-[16px] icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined text-[16px] icon-checked">check_circle</span>
                    <span class="btn-text-unchecked">Select</span>
                    <span class="btn-text-checked">Selected</span>
                </div>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Detail View (Horizontal Cards) -->
        <div class="detail-stay-container relative">
            <?php foreach ($hotel_categories as $idx => $stay): 
                $is_sel = !empty($stay['recommended']); 
            ?>
            <div class="stay-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="stay-detail-<?php echo $idx; ?>">
                <div class="flex flex-col md:flex-row bg-[#131a28] rounded-xl border border-white/10 overflow-hidden shadow-2xl hover:border-secondary/40 transition-colors duration-300">
                    <div class="md:w-[45%] relative h-64 md:h-auto">
                        <img src="<?php echo htmlspecialchars($stay['img']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($stay['title']); ?>">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#131a28] hidden md:block"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#131a28] to-transparent md:hidden"></div>
                        <div class="absolute top-4 left-4 bg-black/70 backdrop-blur-md text-secondary px-3 py-1.5 rounded-md font-bold text-sm shadow">
                            <?php echo $stay['stars_display']; ?>
                        </div>
                    </div>
                    <div class="md:w-[55%] p-6 md:p-10 flex flex-col justify-center">
                        <div class="text-[11px] font-extrabold uppercase tracking-widest text-secondary/90 mb-2">
                            <?php echo htmlspecialchars($stay['tagline']); ?>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-serif text-white font-bold mb-4 leading-tight">
                            <?php echo htmlspecialchars($stay['title']); ?>
                        </h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed mb-8">
                            <?php echo htmlspecialchars($stay['desc']); ?>
                        </p>
                        <div class="grid grid-cols-2 gap-y-4 gap-x-6 pt-6 border-t border-white/10">
                            <?php foreach ($stay['features'] as $feat): ?>
                            <div class="flex items-center gap-2 text-[13px] text-white/80 font-medium">
                                <span class="material-symbols-outlined text-[#28a745] text-[18px]">check_circle</span>
                                <span><?php echo htmlspecialchars($feat); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cab Fleet Showcase -->
    <div>
        <div class="flex flex-col md:flex-row mb-8 gap-4 w-full">
            <div class="w-full">
                <span class="font-label-caps text-secondary text-xs tracking-widest block mb-2">CHAUFFEUR DRIVEN FLEET</span>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-1">
                    <h2 class="serif-accent text-2xl md:text-3xl font-normal text-white">Select Your Private Cab</h2>
                    <div class="text-[11px] md:text-xs text-secondary flex items-center gap-1 bg-secondary/10 px-3 py-1.5 rounded-full border border-secondary/20 w-fit shrink-0">
                        <span class="material-symbols-outlined text-sm">directions_car_filled</span> Seamless private cab transfers for your journey
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm md:text-base mt-2 max-w-2xl">
                    Experience seamless, private luxury transfers and panoramic mountain expeditions with our professional mountain-specialist chauffeurs.
                </p>
            </div>
        </div>

        <!-- Compact Selection Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <?php foreach ($cab_types as $idx => $cab): 
                $is_sel = !empty($cab['recommended']); 
            ?>
            <label class="compact-cab-card <?php echo $is_sel ? 'selected-cab border-secondary/60 bg-secondary/5 selected-stay-bg' : 'border-white/10 bg-white/5'; ?> p-5 flex flex-col items-center justify-center text-center cursor-pointer border rounded-xl hover:border-secondary/40 transition-all duration-300 relative" id="cab-card-<?php echo $idx; ?>" data-cab-idx="<?php echo $idx; ?>" data-cab-label="<?php echo htmlspecialchars($cab['name']); ?>">
                <input type="radio" name="cab_selection_radio" value="<?php echo $idx; ?>" class="sr-only stay-radio-btn" <?php echo $is_sel ? 'checked' : ''; ?>>
                <div class="text-secondary font-bold text-[11px] tracking-widest mb-1 uppercase flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">group</span> <?php echo $cab['seats']; ?>
                </div>
                <h3 class="text-white font-medium text-[13px] leading-snug mb-4 h-10 flex items-center justify-center"><?php echo htmlspecialchars($cab['name']); ?></h3>
                
                <div class="w-full flex items-center justify-center gap-2 py-2 rounded-lg border text-xs transition-all duration-300 stay-indicator">
                    <span class="material-symbols-outlined text-[16px] icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined text-[16px] icon-checked">check_circle</span>
                    <span class="btn-text-unchecked">Select</span>
                    <span class="btn-text-checked">Selected</span>
                </div>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Detail View (Horizontal Cards) -->
        <div class="detail-cab-container relative">
            <?php foreach ($cab_types as $idx => $cab): 
                $is_sel = !empty($cab['recommended']); 
            ?>
            <div class="cab-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="cab-detail-<?php echo $idx; ?>">
                <div class="flex flex-col md:flex-row bg-[#131a28] rounded-xl border border-white/10 overflow-hidden shadow-2xl hover:border-secondary/40 transition-colors duration-300">
                    <div class="md:w-[45%] relative h-64 md:h-auto">
                        <img src="<?php echo htmlspecialchars($cab['img']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($cab['name']); ?>">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#131a28] hidden md:block"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#131a28] to-transparent md:hidden"></div>
                        <div class="absolute top-4 left-4 bg-black/70 backdrop-blur-md text-white/90 px-2.5 py-1.5 rounded-md font-medium text-xs border border-white/10 flex items-center gap-2 shadow">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm text-secondary">group</span> <?php echo $cab['seats']; ?></span>
                        </div>
                    </div>
                    <div class="md:w-[55%] p-6 md:p-10 flex flex-col justify-center">
                        <div class="text-[11px] font-extrabold uppercase tracking-widest text-secondary/90 mb-2">
                            <?php echo htmlspecialchars($cab['category']); ?>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-serif text-white font-bold mb-4 leading-tight">
                            <?php echo htmlspecialchars($cab['name']); ?>
                        </h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                            <?php echo htmlspecialchars($cab['desc']); ?>
                        </p>
                        
                        <div class="grid grid-cols-2 gap-y-4 gap-x-6 pt-6 border-t border-white/10">
                            <div class="flex items-center gap-2 text-[13px] text-white/80 font-medium">
                                <span class="material-symbols-outlined text-secondary text-[18px]">luggage</span>
                                <span>Bag Capacity: <?php echo $cab['bags']; ?></span>
                            </div>
                            <?php foreach ($cab['features'] as $feat): ?>
                            <div class="flex items-center gap-2 text-[13px] text-white/80 font-medium">
                                <span class="material-symbols-outlined text-[#28a745] text-[18px]">check_circle</span>
                                <span><?php echo htmlspecialchars($feat); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Inclusions/Exclusions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-8 detail-section-spacing">
    <div class="bg-inclusion-green border border-green-500/20 rounded-3xl p-8">
        <h3 class="text-green-400 font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            Inclusions
        </h3>
        <ul class="space-y-4 text-on-surface-variant">
            <?php foreach ($inclusions_arr as $inc): ?>
            <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                <?php echo htmlspecialchars($inc); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="bg-exclusion-red border border-red-500/20 rounded-3xl p-8">
        <h3 class="text-red-400 font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">cancel</span>
            Exclusions
        </h3>
        <ul class="space-y-4 text-on-surface-variant">
            <?php foreach ($exclusions_arr as $exc): ?>
            <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-red-500 text-sm mt-1">close</span>
                <?php echo htmlspecialchars($exc); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

        <!-- ✦ Animated Journey Map ✦ -->
            <?php
            $has_day_coords = false;
            foreach ($itinerary as $d) { if (!empty($d['coords'])) { $has_day_coords = true; break; } }
            $show_map = $has_day_coords || !empty($pkg['map_coords']);
            ?>
            <?php if ($show_map): ?>
            <section class="map-section detail-section-spacing">
                <span class="section-label">GEOGRAPHIC CONTEXT</span>
                <h2 class="serif-accent section-title">Bespoke Route Map</h2>
                <div id="journey-map" class="journey-map-box"></div>
            </section>
            <?php endif; ?>

            
        <!-- ✦ Terms & Conditions ✦ -->
            <?php 
                $tc_to_display = (!empty($pkg['use_destination_terms']) && !empty($pkg['dest_terms'])) ? $pkg['dest_terms'] : ($pkg['terms_conditions'] ?? '');
                if (!empty($tc_to_display)): 
            ?>
            <section class="tc-section detail-section-spacing">
                <span class="section-label">LEGAL & POLICIES</span>
                <h2 class="serif-accent section-title">Terms &amp; Conditions</h2>

                <div class="tc-outer">
                    <div id="tc-content-wrapper" class="tc-content-wrapper">
                        <div class="tc-body">
                            <?php
                                $tc_text = htmlspecialchars(trim($tc_to_display));
                                $tc_text = nl2br($tc_text);
                                // Replace ##text## with gold span
                                $tc_text = preg_replace('/##(.*?)##/s', '<strong class="tc-gold-highlight">$1</strong>', $tc_text);
                                echo $tc_text;
                            ?>
                        </div>
                        <div id="tc-fade-overlay" class="tc-fade-overlay"></div>
                    </div>

                    <button id="tc-toggle-btn" class="tc-toggle-btn">
                        <span id="tc-btn-text">Read More</span>
                        <svg id="tc-btn-icon" class="tc-btn-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </div>
            </section>
            
            <?php endif; ?>

            </div> <!-- Close content-spacing -->
    </div> <!-- Close main-content -->
    
    <!-- Right Sidebar (Sticky) -->
    <aside class="sidebar-column">
        <div class="concierge-sidebar">
            <div class="concierge-sidebar__header">
                <div>
                    <p class="font-label-caps text-on-surface-variant mb-2 tracking-widest text-[10px]">STARTING FROM</p>
                    <div class="flex items-baseline gap-3">
                        <p class="font-display-lg text-secondary text-3xl font-bold">₹<?php echo number_format($pkg['price']); ?></p>
                        <?php if(!empty($original_price)): ?>
                            <p class="text-on-surface-variant line-through text-sm">₹<?php echo number_format($original_price); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info List -->
            <div class="space-y-2 text-sm mb-8">
                <div class="flex justify-between items-center border-b border-white/10 pb-2">
                    <span class="text-white/60">Tour Code:</span>
                    <span class="text-white font-medium"><?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-white/10 pb-2">
                    <span class="text-white/60">Hotel Category:</span>
                    <span class="text-white font-medium" id="summary_stay_val">4 Star Luxury</span>
                </div>
                <div class="flex justify-between items-center pb-1">
                    <span class="text-white/60">Private Cab:</span>
                    <span class="text-white font-medium" id="summary_cab_val">Innova / Xylo / Scorpio</span>
                </div>
            </div>

            <!-- SUBMIT ENQUIRY Button -->
            <button data-action="open-checkout" class="w-full px-6 py-4 rounded-[30px] transition-all duration-300 mb-2 flex items-center justify-between font-bold hover:brightness-110 enquiry-btn-gold">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center enquiry-btn-icon-wrap">
                        <span class="material-symbols-outlined enquiry-btn-icon">assignment</span>
                    </div>
                    <div class="text-left">
                        <div class="text-[15px] font-bold enquiry-btn-title">SUBMIT ENQUIRY</div>
                        <div class="text-[9px] tracking-widest font-bold enquiry-btn-subtitle">CUSTOM TOUR QUOTES</div>
                    </div>
                </div>
                <span class="material-symbols-outlined enquiry-btn-arrow">arrow_forward</span>
            </button>
            

            <!-- Quick Summary -->
            <div class="space-y-4 border-t border-white/10 pt-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-secondary border border-white/10">
                        <span class="material-symbols-outlined" style="font-size: 18px;">schedule</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/50 tracking-wider uppercase font-bold">Duration</p>
                        <p class="text-sm text-white font-medium"><?php echo htmlspecialchars($pkg['nights'] . ' Nights & ' . $pkg['days'] . ' Days'); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-secondary border border-white/10">
                        <span class="material-symbols-outlined" style="font-size: 18px;">location_on</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-white/50 tracking-wider uppercase font-bold">Places to Visit</p>
                        <p class="text-sm text-white font-medium line-clamp-1"><?php echo htmlspecialchars($pkg['destination']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Package Includes Icons -->
            <div class="mt-8 p-4 rounded-xl border border-white/10 bg-white/5">
                <p class="text-center text-[10px] text-white/50 tracking-wider uppercase font-bold mb-4">Package Includes</p>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="flex flex-col items-center gap-1 opacity-70">
                        <span class="material-symbols-outlined text-secondary">hotel</span>
                        <span class="text-[9px] text-white/70">Hotel</span>
                    </div>
                    <div class="flex flex-col items-center gap-1 opacity-70">
                        <span class="material-symbols-outlined text-secondary">photo_camera</span>
                        <span class="text-[9px] text-white/70">Sightseeing</span>
                    </div>
                    <div class="flex flex-col items-center gap-1 opacity-70">
                        <span class="material-symbols-outlined text-secondary">directions_car</span>
                        <span class="text-[9px] text-white/70">Transfer</span>
                    </div>
                    <div class="flex flex-col items-center gap-1 opacity-70">
                        <span class="material-symbols-outlined text-secondary">restaurant</span>
                        <span class="text-[9px] text-white/70">Meal</span>
                    </div>
                </div> <!-- End grid -->
            </div> <!-- End Package Includes -->

        </div> <!-- End concierge-sidebar -->
    </aside>
    </div> <!-- Close flex container -->
</div> <!-- Close detail-container -->

<!-- ✦ Related Packages Section ("Maybe you like") ✦ -->
    <?php
    if ($pdo) {
        try {
            // Extract keywords from destination (e.g. "Sikkim & Darjeeling" -> ["Sikkim", "Darjeeling"])
            $keywords = preg_split('/[\s&,.\-\/]+|and/i', $pkg['destination']);
            $keywords = array_filter(array_map('trim', $keywords), function($val) {
                return strlen($val) > 2; // only keep words longer than 2 characters
            });

            $related_pkgs = [];

            if (!empty($keywords)) {
                $sql = "SELECT * FROM packages WHERE id != ? AND is_active = 1 AND (";
                $params = [$pkg['id']];
                $or_clauses = [];
                foreach ($keywords as $kw) {
                    $or_clauses[] = "destination LIKE ?";
                    $params[] = '%' . $kw . '%';
                }
                $sql .= implode(" OR ", $or_clauses) . ") LIMIT 6";
                
                $dest_stmt = $pdo->prepare($sql);
                $dest_stmt->execute($params);
                $related_pkgs = $dest_stmt->fetchAll();
            }

            if (!empty($related_pkgs)):
    ?>
    <section class="related-packages-section detail-container">
        <div class="related-header">
            <div>
                <span class="section-label related-label">OTHER TOURS</span>
                <h2 class="serif-accent related-title">Maybe you <span style="font-style: italic;">like.</span></h2>
            </div>
            <!-- Interactive Carousel Navigation Arrows (visible only if there are enough items to slide) -->
            <?php if (count($related_pkgs) > 3): ?>
            <div class="related-nav">
                <button class="carousel-btn prev-related" data-action="scroll-related" data-dir="-1">&larr;</button>
                <button class="carousel-btn next-related" data-action="scroll-related" data-dir="1">&rarr;</button>
            </div>
            <?php endif; ?>
        </div>

        <div class="related-slider-container">
            <div class="related-grid" id="relatedGrid">
                <?php foreach ($related_pkgs as $r_pkg): 
                    $r_itinerary = json_decode($r_pkg['itinerary'], true);
                    $r_days = is_array($r_itinerary) ? count($r_itinerary) : 0;
                    $r_nights = max(1, $r_days - 1);
                ?>
                <div class="related-card" data-href="package-detail.php?slug=<?php echo urlencode($r_pkg['slug']); ?>">
                    <!-- Thumbnail with zooming and badges -->
                    <div class="related-thumb-wrapper">
                        <img src="<?php echo htmlspecialchars($r_pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($r_pkg['title']); ?>" class="r-img">
                        <!-- Badges/Durations -->
                        <div class="related-badges">
                            <?php echo $r_nights . " NTS / " . $r_days . " DAYS"; ?>
                        </div>
                        <!-- Central Hover Arrow Overlay -->
                        <div class="r-overlay">
                            <span class="r-arrow">&rarr;</span>
                        </div>
                    </div>
                    <!-- Body info -->
                    <div class="related-body">
                        <div class="related-meta">
                            <span class="related-dest"><?php echo htmlspecialchars($r_pkg['destination']); ?></span>
                        </div>
                        <h3 class="related-heading">
                            <?php echo htmlspecialchars($r_pkg['title']); ?>
                        </h3>
                        <div class="related-footer">
                            <div class="related-price">FROM <strong class="related-price-val">&#8377;<?php echo number_format($r_pkg['price']); ?></strong></div>
                            <span class="r-btn">&rarr;</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        

        
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>
</div>




<!-- Accordion + Map JS -->
            


            
</div>

<!-- Lightbox Modal HTML -->
<div id="lightboxModal">
    <div class="lightbox-header">
        <span id="lightboxCounter">1 / <?php echo count($all_images); ?></span>
        <span class="lightbox-close" data-action="close-lightbox">&times;</span>
    </div>
    <div class="lightbox-main">
        <div class="lightbox-prev" data-action="prev-lightbox">&#10094;</div>
        <img id="lightboxMainImg" src="">
        <div class="lightbox-next" data-action="next-lightbox">&#10095;</div>
    </div>
    <div class="lightbox-thumbnails" id="lightboxThumbnails">
        <?php foreach ($all_images as $index => $img_src): ?>
            <img src="<?php echo htmlspecialchars($img_src); ?>" class="lightbox-thumbnail" data-action="open-lightbox" data-idx="<?php echo $index; ?>" id="lb-thumb-<?php echo $index; ?>">
        <?php endforeach; ?>
    </div>
</div>

<!-- Package Checkout Modal -->
<div id="packageCheckoutModal">
    <div class="p-modal-content">
        <!-- Left: Form -->
        <div class="p-modal-left">
            <div class="p-modal-header">
                <h2>Guest Details</h2>
                <div class="p-close-modal" data-action="close-checkout">&times;</div>
            </div>
            
            <form id="packageCheckoutForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                <input type="hidden" name="package_title" value="<?php echo htmlspecialchars($pkg['title']); ?>">
                <input type="hidden" name="tour_code" value="<?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?>">
                <input type="hidden" name="selected_hotel" id="modal_hotel_input" value="4 Star Luxury">
                <input type="hidden" name="selected_cab" id="modal_cab_input" value="Innova / Xylo / Scorpio">
                
                <div class="p-form-section">
                    <h3>Guest Information</h3>
                    <div class="p-form-row">
                        <div class="p-form-group">
                            <label>First Name *</label>
                            <input type="text" name="guest_name" class="p-form-control" required>
                        </div>
                        <div class="p-form-group">
                            <label>Last Name *</label>
                            <input type="text" name="guest_last_name" class="p-form-control" required>
                        </div>
                    </div>
                    
                    <div class="p-form-row">
                        <div class="p-form-group">
                            <label>Travel Date *</label>
                            <input type="date" name="travel_date" class="p-form-control" required>
                        </div>
                        <div class="p-form-group">
                            <label>No. of Guests *</label>
                            <input type="number" name="adults" id="modal_adults_input" class="p-form-control" value="2" min="1" required data-change="update-price">
                        </div>
                    </div>

                    <div class="p-form-group">
                        <label>Any special request (optional)</label>
                        <input type="text" name="special_request" class="p-form-control">
                    </div>
                </div>

                <div class="p-form-section">
                    <h3>Contact Details</h3>
                    <div class="p-form-row">
                        <div class="p-form-group">
                            <label>Mobile number *</label>
                            <div style="display: flex;">
                                <input type="text" value="+91" class="p-form-control" style="width: 60px; border-right: none; border-radius: 4px 0 0 4px; background: rgba(0,0,0,0.5); color: rgba(255,255,255,0.5);" readonly>
                                <input type="tel" name="phone" class="p-form-control" style="border-radius: 0 4px 4px 0;" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="p-form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="p-btn-proceed" id="btnPackageSubmitModal">
                    Submit Inquiry
                </button>
                <div id="packageModalFormMsg" style="margin-top: 15px; font-weight: 600; text-align: center;"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="p-modal-right">
            <h3 class="p-modal-summary-title">Enquiry Summary</h3>
            
            <h4 class="p-modal-pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h4>
            <p class="p-modal-pkg-code">TOUR CODE: <?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?></p>
            
            <div class="p-modal-details-box">
                <div class="p-modal-detail-row">
                    <span class="p-modal-detail-label">Hotel Category</span>
                    <span class="p-modal-detail-val" id="modal_summary_hotel">4 Star Luxury</span>
                </div>
                <div class="p-modal-detail-row">
                    <span class="p-modal-detail-label">Private Cab</span>
                    <span class="p-modal-detail-val" id="modal_summary_cab">Innova / Xylo / Scorpio</span>
                </div>
            </div>
            
            <div class="p-modal-total-row">
                <span class="p-modal-total-label">Total Amount:</span>
                <span class="p-modal-total-val" id="modal_summary_total">₹0</span>
            </div>
            <div class="p-modal-note">
                <span>(Calculated based on Base Price &times; Guests)</span>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
