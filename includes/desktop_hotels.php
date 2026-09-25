<?php
// includes/desktop_hotels.php
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

<!-- Top Search Bar -->
<div class="search-bar-wrapper">
    <div class="search-bar-container">
        <form method="GET" action="hotels.php" class="search-bar" id="desktop-hotelSearchForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="rooms" id="desktop-inputRooms" value="<?php echo (int)$rooms; ?>">
            <input type="hidden" name="adults" id="desktop-inputAdults" value="<?php echo (int)$adults; ?>">
            <input type="hidden" name="children" id="desktop-inputChildren" value="<?php echo (int)$children; ?>">
            <input type="hidden" name="infants" id="desktop-inputInfants" value="<?php echo (int)$infants; ?>">
            
            <div class="search-input-group search-group-large">
                <label class="group-label" for="desktop-hotelQueryInput">City, Area or Property</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">location_on</span>
                    <input id="desktop-hotelQueryInput" type="text" name="q" placeholder="Enter City or Hotel Name" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </div>

            <div class="search-input-group search-group-pointer">
                <label class="group-label" for="desktop-inputCheckIn">Check-In</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <input type="date" name="check_in" id="desktop-inputCheckIn" value="<?php echo htmlspecialchars($check_in); ?>" min="<?php echo date('Y-m-d'); ?>" class="pointer-input">
                </div>
            </div>

            <div class="search-input-group search-group-pointer">
                <label class="group-label" for="desktop-inputCheckOut">Check-Out</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <input type="date" name="check_out" id="desktop-inputCheckOut" value="<?php echo htmlspecialchars($check_out); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="pointer-input">
                </div>
            </div>

            <div class="search-input-group search-group-guests" id="desktop-guestsContainer">
                <label class="group-label" for="desktop-displayGuests">Rooms & Guests</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">group</span>
                    <input type="text" value="<?php echo $rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?>, <?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?>" id="desktop-displayGuests" readonly class="pointer-input">
                </div>
                
                <div class="guests-dropdown" id="desktop-guestsDropdown">
                    <div class="guest-row">
                        <div class="guest-label">Rooms</div>
                        <div class="guest-counter">
                            <button type="button" class="btn-count" id="desktop-btnRoomsMinus" <?php echo $rooms <= 1 ? 'disabled' : ''; ?>>-</button>
                            <span class="count-val" id="desktop-valRooms"><?php echo $rooms; ?></span>
                            <button type="button" class="btn-count" id="desktop-btnRoomsPlus">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div class="guest-label">Adults<span class="guest-sublabel">Aged 12+ years</span></div>
                        <div class="guest-counter">
                            <button type="button" class="btn-count" id="desktop-btnAdultsMinus" <?php echo $adults <= 1 ? 'disabled' : ''; ?>>-</button>
                            <span class="count-val" id="desktop-valAdults"><?php echo $adults; ?></span>
                            <button type="button" class="btn-count" id="desktop-btnAdultsPlus">+</button>
                        </div>
                    </div>
                    <div class="guest-row">
                        <div class="guest-label">Children<span class="guest-sublabel">Aged 2-11 years</span></div>
                        <div class="guest-counter">
                            <button type="button" class="btn-count" id="desktop-btnChildrenMinus" <?php echo $children <= 0 ? 'disabled' : ''; ?>>-</button>
                            <span class="count-val" id="desktop-valChildren"><?php echo $children; ?></span>
                            <button type="button" class="btn-count" id="desktop-btnChildrenPlus">+</button>
                        </div>
                    </div>
                    <button type="button" class="btn-apply-guests" id="desktop-btnApplyGuests">APPLY</button>
                </div>
            </div>
            <button type="submit" class="btn-search">SEARCH</button>
        </form>
    </div>
</div>

<div class="section-container">
    <div class="hotel-filters">
        <button type="button" class="filter-btn active" data-filter="all">All Properties</button>
        <button type="button" class="filter-btn" data-filter="signature">Our Signature Properties</button>
        <button type="button" class="filter-btn" data-filter="luxury">Our Partner/Associate Brands</button>
    </div>

    <div class="hotel-list">
        <?php 
        $all_hotels = array_merge($signature, $luxury);
        foreach ($all_hotels as $hotel): 
            $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? ORDER BY created_at ASC LIMIT 1, 3");
            $stmt->execute([$hotel['id']]);
            $thumbs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $amenities = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
            $query_string = http_build_query([
                'check_in' => $check_in,
                'check_out' => $check_out,
                'rooms' => $rooms,
                'adults' => $adults,
                'children' => $children,
                'infants' => $infants
            ]);
            $main_img_raw = $hotel['main_image'] ?? 'assets/img/hotel.jpg';
            $main_img = preg_match('/^https?:\/\//i', $main_img_raw) ? $main_img_raw : 'admin/' . ltrim(str_replace('../', '', $main_img_raw), '/');
        ?>
        <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>&<?php echo $query_string; ?>" class="hotel-card" data-category="<?php echo $hotel['type']; ?>">
            <div class="hotel-gallery-col">
                <div class="main-img-wrap">
                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" onerror="if(!this.dataset.fallbackDone){this.dataset.fallbackDone='1';this.src='assets/img/pkg.jpg';}">
                    <div class="hotel-badge"><?php echo $hotel['star_category']; ?> Star</div>
                </div>
                <?php if (!empty($thumbs)): ?>
                <div class="thumb-row">
                    <?php foreach($thumbs as $index => $th): 
                        $th_src = preg_match('/^https?:\/\//i', $th['image_url']) ? $th['image_url'] : $th['image_url'];
                    ?>
                    <div class="thumb-wrap">
                        <img src="<?php echo htmlspecialchars($th_src); ?>" alt="Thumb" onerror="if(!this.dataset.fallbackDone){this.dataset.fallbackDone='1';this.src='assets/img/pkg.jpg';}">
                        <?php if($index == 2): ?>
                        <div class="thumb-overlay">More</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="hotel-info-col">
                <div class="hotel-title-row hotel-title-flex">
                    <h3 class="hotel-name mb-0"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                    <?php if (!empty($hotel['google_rating'])): ?>
                    <div class="google-review-badge badge-no-margin">
                        <span class="gr-star">★</span> 
                        <span class="gr-rating-text"><?php echo number_format($hotel['google_rating'], 1); ?></span> 
                        <span class="gr-count">(<?php echo (int)$hotel['google_review_count']; ?> Google Reviews)</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hotel-location mt-8">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo htmlspecialchars($hotel['place']); ?>
                </div>

                <div class="hotel-desc">
                    <?php echo htmlspecialchars($hotel['description']); ?>
                </div>
                <?php if (strlen($hotel['description']) > 150): ?>
                <div class="read-more-btn" data-action="open-info-modal" data-hotel-name="<?php echo htmlspecialchars($hotel['name'], ENT_QUOTES); ?>" data-modal-title="ABOUT THE HOTEL" data-content-id="desktop-desc-content-<?php echo $hotel['id']; ?>">... Read More</div>
                <?php endif; ?>
                <div id="desktop-desc-content-<?php echo $hotel['id']; ?>" class="modal-content-hidden"><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></div>
                
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
                    <div class="view-more-btn" data-action="open-info-modal" data-hotel-name="<?php echo htmlspecialchars($hotel['name'], ENT_QUOTES); ?>" data-modal-title="AMENITIES" data-content-id="desktop-amenities-content-<?php echo $hotel['id']; ?>">View More <svg class="view-more-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <?php endif; ?>
                    <div id="desktop-amenities-content-<?php echo $hotel['id']; ?>" class="modal-content-hidden">
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

<script src="js/modules/hotels.js" defer></script>

<!-- Info Modal -->
<div id="desktop-infoModal" class="info-modal" data-action="close-info-modal-if-self" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="info-modal-content">
        <div class="info-modal-close" data-action="close-info-modal">&times;</div>
        <h2 id="desktop-infoModalTitle" class="info-modal-title">Hotel Name</h2>
        <div id="desktop-infoModalSubtitle" class="info-modal-subtitle">ABOUT THE HOTEL</div>
        <div id="desktop-infoModalBody" class="info-modal-body"></div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
