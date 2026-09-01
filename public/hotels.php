<?php
$page_title = "Exclusive Stays & Luxury Retreats | Leisure Loop";
require_once "../config/db.php";

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
    $sql_sig = "SELECT h.*, (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0) as dynamic_starting_tariff FROM hotels h WHERE h.type='signature' AND h.is_active=1";
    $sql_lux = "SELECT h.*, (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0) as dynamic_starting_tariff FROM hotels h WHERE h.type='luxury' AND h.is_active=1";
    $params = [];
    
    if ($search_query !== '') {
        $sql_sig .= " AND (name LIKE ? OR place LIKE ?)";
        $sql_lux .= " AND (name LIKE ? OR place LIKE ?)";
        $search_param = "%{$search_query}%";
        $params = [$search_param, $search_param];
    }
    
    $sql_sig .= " ORDER BY created_at DESC";
    $sql_lux .= " ORDER BY created_at DESC";

    $stmt_sig = $pdo->prepare($sql_sig);
    $stmt_sig->execute($params);
    $signature = $stmt_sig->fetchAll();

    $stmt_lux = $pdo->prepare($sql_lux);
    $stmt_lux->execute($params);
    $luxury = $stmt_lux->fetchAll();
}

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

if ($is_mobile) {
    include '../includes/mobile_hotels.php';
    exit;
}

include "../includes/header.php";
?>
<link rel="stylesheet" href="css/hotels.css">


<!-- Hero Section -->
<section class="hotels-hero">
    <div class="container">
        <h1>Exclusive Stays & Luxury Retreats</h1>
        <p>Discover handpicked luxury hotels, boutique resorts, and premium accommodations for an unforgettable experience.</p>
    </div>
</section>

<!-- Top Search Bar (MMT Style) -->
<div class="search-bar-container">
    <form method="GET" action="hotels.php" class="search-bar" id="hotelSearchForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <input type="hidden" name="check_in" id="inputCheckIn" value="<?php echo htmlspecialchars($check_in); ?>">
        <input type="hidden" name="check_out" id="inputCheckOut" value="<?php echo htmlspecialchars($check_out); ?>">
        <input type="hidden" name="rooms" id="inputRooms" value="<?php echo (int)$rooms; ?>">
        <input type="hidden" name="adults" id="inputAdults" value="<?php echo (int)$adults; ?>">
        <input type="hidden" name="children" id="inputChildren" value="<?php echo (int)$children; ?>">
        <input type="hidden" name="infants" id="inputInfants" value="<?php echo (int)$infants; ?>">
        
        <div class="search-input-group" style="flex: 1.5;">
            <label class="group-label">City, Area or Property</label>
            <div class="input-with-icon">
                <span class="material-symbols-outlined">location_on</span>
                
<label for="input_fb516d2f" class="sr-only">Enter City or Hotel Name</label>
<input id="input_fb516d2f" type="text" name="q" placeholder="Enter City or Hotel Name" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
        </div>
        <div class="search-input-group" style="flex: 1; cursor: pointer;">
            <label class="group-label">Check-In / Check-Out</label>
            <div class="input-with-icon">
                <span class="material-symbols-outlined">calendar_month</span>
                <input type="text" value="<?php echo date('M d, Y', strtotime($check_in)); ?> - <?php echo date('M d, Y', strtotime($check_out)); ?>" id="displayDates" readonly style="cursor: pointer;">
            </div>
        </div>
        <div class="search-input-group" style="flex: 1; position: relative;" id="guestsContainer">
            <label class="group-label">Rooms & Guests</label>
            <div class="input-with-icon">
                <span class="material-symbols-outlined">group</span>
                <input type="text" value="<?php echo $rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?>, <?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?>" id="displayGuests" readonly style="cursor: pointer;">
            </div>
            
            <!-- Guests Dropdown -->
            <div class="guests-dropdown" id="guestsDropdown">
                <div class="guest-row">
                    <div class="guest-label">Rooms</div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="btnRoomsMinus" <?php echo $rooms <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valRooms"><?php echo $rooms; ?></span>
                        <button type="button" class="btn-count" id="btnRoomsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Adults<span class="guest-sublabel">Aged 12+ years</span></div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="btnAdultsMinus" <?php echo $adults <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valAdults"><?php echo $adults; ?></span>
                        <button type="button" class="btn-count" id="btnAdultsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Children<span class="guest-sublabel">Aged 2-11 years</span></div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="btnChildrenMinus" <?php echo $children <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valChildren"><?php echo $children; ?></span>
                        <button type="button" class="btn-count" id="btnChildrenPlus">+</button>
                    </div>
                </div>
                <button type="button" class="btn-apply-guests" id="btnApplyGuests">APPLY</button>
            </div>
        </div>
        <button type="submit" class="btn-search">SEARCH</button>
    </form>
</div>



<div class="section-container">
    <div class="hotel-filters">
        <button class="filter-btn active" data-filter="all">All Properties</button>
        <button class="filter-btn" data-filter="signature">Our Signature Properties</button>
        <button class="filter-btn" data-filter="luxury">Our Partner/Associate Brands</button>
    </div>

    <div class="hotel-list">
        <?php 
        // Combine arrays so we loop through all of them
        $all_hotels = array_merge($signature, $luxury);
        foreach ($all_hotels as $hotel): 
            $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$hotel['id']]);
            $thumbs = $stmt->fetchAll();
            $amenities = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
        ?>
        <?php 
        $query_string = http_build_query([
            'check_in' => $check_in,
            'check_out' => $check_out,
            'rooms' => $rooms,
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants
        ]);
        ?>
        <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>&<?php echo $query_string; ?>" class="hotel-card" data-category="<?php echo $hotel['type']; ?>">
            <div class="hotel-gallery-col">
                <div class="main-img-wrap">
                    <?php $main_img = preg_match('/^https?:\/\//i', $hotel['main_image']) ? $hotel['main_image'] : (strpos($hotel['main_image'], '../') === 0 ? ltrim($hotel['main_image'], '../') : $hotel['main_image']); ?>
                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                    <div class="hotel-badge"><?php echo $hotel['star_category']; ?> Star</div>
                </div>
                <?php if (!empty($thumbs)): ?>
                <div class="thumb-row">
                    <?php foreach($thumbs as $index => $th): 
                        $th_src = preg_match('/^https?:\/\//i', $th['image_url']) ? $th['image_url'] : $th['image_url'];
                    ?>
                    <div class="thumb-wrap">
                        <img src="<?php echo htmlspecialchars($th_src); ?>" alt="Thumb">
                        <?php if($index == 2): ?>
                        <div class="thumb-overlay">More</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="hotel-info-col">
                <div class="hotel-title-row" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                    <h3 class="hotel-name" style="margin-bottom: 0;"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                    <?php if (!empty($hotel['google_rating'])): ?>
                    <div class="google-review-badge" style="margin: 0; flex-shrink: 0;">
                        <span class="gr-star">★</span> 
                        <span style="font-weight: 600; color: #fff;"><?php echo number_format($hotel['google_rating'], 1); ?></span> 
                        <span class="gr-count">(<?php echo (int)$hotel['google_review_count']; ?> Google Reviews)</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hotel-location" style="margin-top: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo htmlspecialchars($hotel['place']); ?>
                </div>

                <div class="hotel-desc">
                    <?php echo htmlspecialchars($hotel['description']); ?>
                </div>
                <?php if (strlen($hotel['description']) > 150): ?>
                <div class="read-more-btn" data-action="eval:event.preventDefault(); openInfoModal('<?php echo addslashes(htmlspecialchars($hotel['name'])); ?>', 'ABOUT THE HOTEL', document.getElementById('desc-content-<?php echo $hotel['id']; ?>').innerHTML)">... Read More</div>
                <?php endif; ?>
                <div id="desc-content-<?php echo $hotel['id']; ?>" style="display:none;"><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></div>
                
                <div class="amenities-section">
                    <?php if (!empty($amenities)): ?>
                    <div class="amenities-title">Amenities</div>
                    <div class="amenities-grid">
                        <?php 
                        $visible_amenities = array_slice($amenities, 0, 6);
                        foreach($visible_amenities as $am): 
                        ?>
                        <div class="amenity-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php echo htmlspecialchars(trim($am)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($amenities) > 6): ?>
                    <div class="view-more-btn" data-action="eval:event.preventDefault(); openInfoModal('<?php echo addslashes(htmlspecialchars($hotel['name'])); ?>', 'AMENITIES', document.getElementById('amenities-content-<?php echo $hotel['id']; ?>').innerHTML)">View More <svg style="width:12px;height:12px;vertical-align:middle;margin-left:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <?php endif; ?>
                    <div id="amenities-content-<?php echo $hotel['id']; ?>" style="display:none;">
                        <div class="modal-amenities-grid">
                            <?php foreach($amenities as $am): ?>
                            <div class="modal-amenity-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <?php echo htmlspecialchars(trim($am)); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hotel-footer">
                    <div>
                        <div class="price-label">Starting from</div>
                        <div class="price-value">₹<?php echo number_format($hotel['dynamic_starting_tariff'] > 0 ? $hotel['dynamic_starting_tariff'] : $hotel['starting_tariff']); ?> <span>/ night</span></div>
                    </div>
                    <div class="action-buttons">
                        <div class="btn-outline">Quick Details</div>
                        <div class="btn-solid">Check Availability</div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="js/modules/hotels.js" defer></script>

<!-- Info Modal -->
<div id="infoModal" class="info-modal" data-action="close-info-modal-if-self">
    <div class="info-modal-content">
        <div class="info-modal-close" data-action="close-info-modal">&times;</div>
        <h2 id="infoModalTitle" class="info-modal-title">Hotel Name</h2>
        <div id="infoModalSubtitle" class="info-modal-subtitle">ABOUT THE HOTEL</div>
        <div id="infoModalBody" class="info-modal-body"></div>
    </div>
</div>



<?php include "../includes/footer.php"; ?>
