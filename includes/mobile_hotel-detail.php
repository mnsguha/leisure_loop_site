<?php
// includes/mobile_hotel-detail.php
// Backend Rule #16: This partial is ONLY dispatched from hotel-detail.php when $is_mobile === true.
// All DB reads completed in the controller. No queries here.

// Build hero images array — primary from hotel_images, fallback to hotel.main_image
$hero_images = [];
if (!empty($hotel_images)) {
    foreach ($hotel_images as $img) {
        $url = $img['image_url'] ?? '';
        if (!empty($url)) {
            $hero_images[] = preg_match('/^https?:\/\//i', $url)
                ? $url
                : 'admin/' . ltrim(str_replace('../', '', $url), '/');
        }
    }
}
if (empty($hero_images)) {
    $raw_hero = $hotel['main_image'] ?? $hotel['image_url'] ?? '';
    $fallback = empty($raw_hero)
        ? 'assets/img/pkg.jpg'
        : (preg_match('/^https?:\/\//i', $raw_hero)
            ? $raw_hero
            : 'admin/' . ltrim(str_replace('../', '', $raw_hero), '/'));
    $hero_images[] = $fallback;
}
$photo_count = count($hero_images);

// Star rating
$star_count = min(5, max(1, (int)($hotel['star_category'] ?? 3)));

// Review
$rating      = !empty($hotel['google_rating']) ? (float)$hotel['google_rating'] : 0;
$rating_label = $rating >= 4.5 ? 'Exceptional' : ($rating >= 4.0 ? 'Excellent' : ($rating >= 3.5 ? 'Very Good' : 'Good'));

// Amenities
$amenities_raw = !empty($hotel['amenities'])
    ? array_filter(array_map('trim', explode(',', $hotel['amenities'])))
    : [];
$amenities_preview = array_slice($amenities_raw, 0, 4);

// Tariff
$tariff = !empty($hotel['dynamic_starting_tariff']) && $hotel['dynamic_starting_tariff'] > 0
    ? (float)$hotel['dynamic_starting_tariff']
    : (float)($hotel['starting_tariff'] ?? 0);
$tax_estimate = 0;
if ($tariff <= 1000) $tax_estimate = 0;
else if ($tariff <= 7500) $tax_estimate = (int)round($tariff * 0.05);
else $tax_estimate = (int)round($tariff * 0.18);

// Date display helpers
$check_in_ts  = strtotime($check_in);
$check_out_ts = strtotime($check_out);
$nights       = max(1, (int)round(($check_out_ts - $check_in_ts) / 86400));
$date_display = date('j M', $check_in_ts) . ' - ' . date('j M', $check_out_ts);
$guest_display = $rooms . ' Room' . ($rooms > 1 ? 's' : '') . ', ' . $adults . ' Guest' . ($adults > 1 ? 's' : '');

// Overview excerpt
$full_desc    = $hotel['description'] ?? $hotel['overview'] ?? '';
$desc_excerpt = mb_strlen($full_desc) > 180 ? mb_substr($full_desc, 0, 180) . '...' : $full_desc;

// CSRF token
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title ?? ($hotel['name'] . ' | Leisure Loop Trip')) ?></title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr($full_desc, 0, 155)) ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="m-page-body m-hotel-detail-body">

    <!-- ── Hero Image Slider ─────────────────────────────────────────── -->
    <section class="mhd-hero" id="mHdHeroSection" aria-label="Hotel photos">

        <!-- Floating Back Button -->
        <a href="hotels.php?view=mobile" class="mhd-hero-btn mhd-hero-btn--back" aria-label="Go back to hotels">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>

        <!-- Floating Share Button -->
        <button type="button" class="mhd-hero-btn mhd-hero-btn--share" id="mHdShareBtn" aria-label="Share this hotel">
            <span class="material-symbols-outlined">share</span>
            <span class="mhd-share-label">Share</span>
        </button>

        <!-- Scroll-snap Slider -->
        <div class="mhd-hero-slider" id="mHdHeroSlider" role="button" aria-label="Open photo gallery" tabindex="0">
            <?php foreach ($hero_images as $idx => $img_url): ?>
            <div class="mhd-hero-slide">
                <img
                    src="<?= htmlspecialchars($img_url) ?>"
                    alt="<?= htmlspecialchars($hotel['name']) ?> — photo <?= $idx + 1 ?>"
                    class="mhd-hero-img"
                    loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>"
                    onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Top gradient for button legibility -->
        <div class="mhd-hero-gradient-top" aria-hidden="true"></div>
        <!-- Bottom gradient -->
        <div class="mhd-hero-gradient-bottom" aria-hidden="true"></div>

        <!-- Dot Pagination -->
        <div class="mhd-hero-dots" id="mHdHeroDots" aria-hidden="true">
            <?php foreach ($hero_images as $idx => $img_url): ?>
            <span class="mhd-hero-dot <?= $idx === 0 ? 'is-active' : '' ?>" data-dot-idx="<?= $idx ?>"></span>
            <?php endforeach; ?>
        </div>


    </section>

    <!-- ── Overlapping Content Card ──────────────────────────────────── -->
    <div class="mhd-card" id="mHdContentCard">

        <!-- Sticky Tab Navigation -->
        <nav class="mhd-tab-nav no-scrollbar" id="mHdTabNav" aria-label="Hotel detail sections">
            <button type="button" class="mhd-tab-btn is-active" data-target="mHdOverviewSection">Overview</button>
            <button type="button" class="mhd-tab-btn" data-target="mHdAmenitiesSection">Amenities</button>
            <button type="button" class="mhd-tab-btn" data-target="mHdReviewsSection">Reviews</button>

            <button type="button" class="mhd-tab-btn" data-target="mHdBookingSection">Book</button>
        </nav>

        <!-- ── Overview Section ────────────────────────────────────────────── -->
        <!-- mhd-section--bare: strips the section's own card surface; inner .mhd-property-card is the card -->
        <section class="mhd-section mhd-section--bare" id="mHdOverviewSection" aria-labelledby="mHdOverviewHeading">

            <!-- Card 1: Property info card (name → property overview) -->
            <div class="mhd-property-card">

                <!-- Hotel Name & Stars -->
                <h1 class="mhd-hotel-name" id="mHdOverviewHeading"><?= htmlspecialchars($hotel['name'] ?? '') ?></h1>
                <div class="mhd-star-row" aria-label="<?= $star_count ?> star hotel">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                    <span class="mhd-star <?= $s <= $star_count ? 'mhd-star--filled' : '' ?>" aria-hidden="true">★</span>
                    <?php endfor; ?>
                </div>

                <!-- Location (left) + Review (right) — single combined meta row -->
                <div class="mhd-meta-row" aria-label="Hotel location and rating">

                    <!-- Left: Location -->
                    <div class="mhd-meta-col mhd-meta-col--location">
                        <span class="material-symbols-outlined mhd-location-icon" aria-hidden="true">location_on</span>
                        <span class="mhd-location-name"><?= htmlspecialchars($hotel['place'] ?? $hotel['location'] ?? 'Sikkim, India') ?></span>
                    </div>

                    <!-- Right: Review Badge — only rendered when rating exists -->
                    <?php if ($rating > 0): ?>
                    <button type="button"
                        class="mhd-meta-col mhd-meta-col--review"
                        data-action="scroll-to-reviews"
                        aria-label="Rating <?= number_format($rating, 1) ?> — <?= $rating_label ?>. View reviews.">
                        <span class="mhd-rating-badge"><?= number_format($rating, 1) ?></span>
                        <div class="mhd-meta-review-text">
                            <span class="mhd-rating-label"><?= $rating_label ?></span>
                            <?php if (!empty($hotel['google_review_count'])): ?>
                            <span class="mhd-rating-sub"><?= (int)$hotel['google_review_count'] ?> Ratings</span>
                            <?php endif; ?>
                        </div>
                        <span class="material-symbols-outlined mhd-chevron" aria-hidden="true">chevron_right</span>
                    </button>
                    <?php endif; ?>

                </div>

                <!-- Overview Description -->
                <?php if (!empty($full_desc)): ?>
                <div class="mhd-overview-text">
                    <p class="mhd-desc-excerpt" id="mHdDescExcerpt"><?= nl2br(htmlspecialchars($desc_excerpt)) ?></p>
                    <?php if (mb_strlen($full_desc) > 180): ?>
                    <p class="mhd-desc-full is-hidden" id="mHdDescFull"><?= nl2br(htmlspecialchars($full_desc)) ?></p>
                    <button type="button" class="mhd-read-more-btn" id="mHdReadMoreBtn" aria-expanded="false" aria-label="Expand property overview">
                        <span class="material-symbols-outlined mhd-bounce-arrow">keyboard_arrow_down</span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div><!-- /.mhd-property-card -->

        </section>

        <!-- ── Date & Guest Selection Row ───────────────────────────────── -->
        <section class="mhd-booking-bar-section">
            <!-- Card 2: Date/Guest booking card -->
            <div class="mhd-booking-card">
                <div class="mhd-date-guest-row">
                    <!-- Date Pill -->
                    <button type="button"
                        class="mhd-pill mhd-pill--dates"
                        id="mHdDatePill"
                        data-action="open-hotel-datepicker"
                        aria-label="Select dates: <?= htmlspecialchars($date_display) ?>">
                        <span class="material-symbols-outlined mhd-pill-icon">calendar_month</span>
                        <span class="mhd-pill-val"><?= htmlspecialchars($date_display) ?></span>
                    </button>

                    <!-- Guest Pill -->
                    <button type="button"
                        class="mhd-pill mhd-pill--guests"
                        id="mHdGuestPill"
                        data-action="open-hotel-guestpicker"
                        aria-label="Select guests: <?= htmlspecialchars($guest_display) ?>">
                        <span class="material-symbols-outlined mhd-pill-icon">group</span>
                        <span class="mhd-pill-val"><?= htmlspecialchars($guest_display) ?></span>
                    </button>
                </div>
                <p class="mhd-timings-line">
                    Check in: <strong>1:30 PM</strong> / Check out: <strong>11 AM</strong>
                </p>
            </div><!-- /.mhd-booking-card -->
        </section>

        <!-- ── Amenities Section ─────────────────────────────────────── -->
        <section class="mhd-section mhd-amenities-section" id="mHdAmenitiesSection" aria-labelledby="mHdAmenitiesHeading">
            <h2 class="mhd-section-title" id="mHdAmenitiesHeading">Amenities</h2>

            <?php if (!empty($amenities_preview)): ?>
            <div class="mhd-amenity-grid">
                <?php foreach ($amenities_preview as $am): ?>
                <div class="mhd-amenity-item">
                    <span class="material-symbols-outlined mhd-amenity-icon">check_circle</span>
                    <span class="mhd-amenity-label"><?= htmlspecialchars($am) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($amenities_raw) > 4): ?>
            <button type="button" class="mhd-see-all-btn" id="mHdSeeAllAmenities" data-all-amenities="<?= htmlspecialchars(implode('||', $amenities_raw)) ?>" aria-expanded="false" aria-label="Expand all amenities">
                <span class="material-symbols-outlined mhd-bounce-arrow">keyboard_arrow_down</span>
            </button>
            <?php endif; ?>
            <?php else: ?>
            <p class="mhd-empty-note">Amenity details coming soon.</p>
            <?php endif; ?>
        </section>

        <!-- ── Reviews Section ──────────────────────────────────────── -->
        <?php if ($rating > 0): ?>
        <section class="mhd-section" id="mHdReviewsSection" aria-labelledby="mHdReviewsHeading">
            <h2 class="mhd-section-title" id="mHdReviewsHeading">Reviews &amp; Ratings</h2>
            <div class="mhd-reviews-summary">
                <div class="mhd-reviews-score-block">
                    <span class="mhd-rating-badge mhd-rating-badge--large"><?= number_format($rating, 1) ?></span>
                    <div>
                        <span class="mhd-rating-label"><?= $rating_label ?></span>
                        <?php if (!empty($hotel['google_review_count'])): ?>
                        <span class="mhd-rating-sub"><?= htmlspecialchars($hotel['google_review_count']) ?> Ratings</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ── Rooms & Plans Section ────────────────────────────── -->
        <section class="mhd-section" id="mHdRoomsSection" aria-labelledby="mHdRoomsHeading">
            <h2 class="mhd-section-title" id="mHdRoomsHeading">Select Your Room</h2>
            
            <div class="mhd-rooms-list">
                <?php foreach($rooms_with_plans as $index => $room): ?>
                <div class="mhd-room-card">
                    <div class="mhd-room-header">
                        <?php 
                        $main_img = !empty($room['room_image']) ? $room['room_image'] : '../assets/images/placeholder.jpg'; 
                        $room_images = [$main_img];
                        if (!empty($room['additional_images'])) {
                            $add_imgs = json_decode($room['additional_images'], true);
                            if (is_array($add_imgs)) {
                                $room_images = array_merge($room_images, $add_imgs);
                            }
                        }
                        ?>
                        <button type="button" class="mhd-room-img-wrap" 
                                data-images="<?= htmlspecialchars(json_encode($room_images), ENT_QUOTES) ?>" 
                                data-room-name="<?= htmlspecialchars($room['room_type_name'], ENT_QUOTES) ?>"
                                aria-label="View photos of <?= htmlspecialchars($room['room_type_name'], ENT_QUOTES) ?>">
                            <img src="<?= htmlspecialchars($main_img) ?>" alt="Room Image" class="mhd-room-img" loading="lazy">
                        </button>
                        <div class="mhd-room-info">
                            <h3 class="mhd-room-name"><?= htmlspecialchars($room['room_type_name']) ?></h3>
                            <ul class="mhd-room-features">
                                <li>
                                    <span class="material-symbols-outlined">square_foot</span>
                                    <?= !empty($room['room_size']) ? htmlspecialchars($room['room_size']) : 'Size N/A' ?>
                                </li>
                                <li>
                                    <span class="material-symbols-outlined">bed</span>
                                    <?= !empty($room['bed_type']) ? htmlspecialchars($room['bed_type']) : 'Bed N/A' ?>
                                </li>
                                <li>
                                    <span class="material-symbols-outlined">balcony</span>
                                    <?= !empty($room['view_type']) ? htmlspecialchars($room['view_type']) : 'View N/A' ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mhd-room-plans">
                        <?php foreach($room['plans'] as $p_idx => $plan): 
                            $default_rate = !empty($plan['current_rate']) ? $plan['current_rate']['base_rate_2_pax'] : 0;
                            $tax_est = 0;
                            if ($default_rate <= 1000) {
                                $tax_est = 0;
                            } else if ($default_rate <= 7500) {
                                $tax_est = $default_rate * 0.05;
                            } else {
                                $tax_est = $default_rate * 0.18;
                            }
                            $plan_id_attr = "plan_{$room['id']}_{$plan['id']}";
                            $inclusions = array_filter(array_map('trim', explode(',', $plan['inclusions'] ?? '')));
                        ?>
                        <label class="mhd-plan-radio-row" for="<?= $plan_id_attr ?>">
                            <div class="mhd-plan-radio-header">
                                <div class="mhd-plan-radio-title">
                                    <input type="radio" 
                                           name="selected_plan" 
                                           id="<?= $plan_id_attr ?>" 
                                           class="mhd-plan-radio-input"
                                           value="<?= $plan['id'] ?>"
                                           data-room-id="<?= $room['id'] ?>"
                                           data-room-name="<?= htmlspecialchars($room['room_type_name'], ENT_QUOTES) ?>"
                                           data-plan-name="<?= htmlspecialchars($plan['plan_name'], ENT_QUOTES) ?>"
                                           data-price="<?= $default_rate ?>"
                                           data-tax="<?= $tax_est ?>">
                                    <span class="mhd-plan-name"><?= htmlspecialchars($plan['plan_name']) ?></span>
                                </div>
                                <div class="mhd-plan-price-block">
                                    <span class="mhd-plan-price">₹<?= number_format($default_rate) ?></span>
                                    <span class="mhd-plan-taxes">+ ₹<?= number_format($tax_est) ?> Taxes &amp; fees</span>
                                    <span class="mhd-plan-per-night">Per night</span>
                                </div>
                            </div>
                            <?php if (!empty($inclusions)): ?>
                            <ul class="mhd-plan-inclusions">
                                <?php foreach($inclusions as $inc): ?>
                                <li><span class="material-symbols-outlined mhd-inc-check">check</span> <?= htmlspecialchars($inc) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>



        <!-- Bottom padding to clear the fixed bottom bar -->
        <div class="mhd-bottom-spacer" aria-hidden="true"></div>

    </div><!-- /.mhd-card -->

    <!-- ── Fixed Bottom Action Bar ──────────────────────────────────── -->
    <div class="mhd-bottom-bar" id="mHdBottomBar" role="region" aria-label="Booking summary">
        <div class="mhd-bottom-price">
            <?php if ($tariff > 0): ?>
            <span class="mhd-price-amount">₹<?= number_format($tariff) ?></span>
            <span class="mhd-price-taxes">+ ₹<?= number_format($tax_estimate) ?> taxes &amp; fees per night</span>
            <?php else: ?>
            <span class="mhd-price-on-request">Price on Request</span>
            <?php endif; ?>
        </div>
        <div class="mhd-bottom-actions">
            <button type="button" class="mhd-wishlist-btn" id="mHdWishlistBtn" aria-label="Save to wishlist">
                <span class="material-symbols-outlined">favorite_border</span>
            </button>
            <button type="button" class="mhd-select-room-btn" id="mHdSelectRoomBtn" aria-label="Select a room">
                SELECT ROOM
            </button>
        </div>
    </div>

    <!-- ── Bento Gallery Modal ──────────────────────────────────────── -->
    <div class="mhd-gallery-modal is-hidden" id="mHdGalleryModal" role="dialog" aria-modal="true" aria-label="Photo Gallery" aria-hidden="true">
        <div class="mhd-gallery-header">
            <h3 class="mhd-gallery-title">Photos &amp; Videos</h3>
            <button type="button" class="mhd-gallery-close" id="mHdGalleryCloseBtn" aria-label="Close gallery">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="mhd-gallery-body no-scrollbar">
            <div class="mhd-bento-grid">
                <?php foreach ($hero_images as $idx => $img_url): ?>
                <button type="button" class="mhd-bento-item" data-index="<?= $idx ?>" aria-label="View photo <?= $idx + 1 ?> fullscreen">
                    <img src="<?= htmlspecialchars($img_url) ?>" alt="Gallery photo <?= $idx + 1 ?>" loading="lazy">
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── Fullscreen Lightbox Slider ───────────────────────────────── -->
    <div class="mhd-lightbox is-hidden" id="mHdLightbox" role="dialog" aria-modal="true" aria-label="Fullscreen photo viewer" aria-hidden="true">
        <div class="mhd-lightbox-header">
            <div class="mhd-lightbox-counter" id="mHdLightboxCounter">1 / <?= count($hero_images) ?></div>
            <button type="button" class="mhd-lightbox-close" id="mHdLightboxCloseBtn" aria-label="Close fullscreen viewer">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="mhd-lightbox-slider" id="mHdLightboxSlider">
            <?php foreach ($hero_images as $idx => $img_url): ?>
            <div class="mhd-lightbox-slide">
                <img src="<?= htmlspecialchars($img_url) ?>" alt="Fullscreen photo <?= $idx + 1 ?>" loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Room Gallery Modal (Dynamic) ─────────────────────────────── -->
    <div class="mhd-room-gallery-modal is-hidden" id="mHdRoomGalleryModal" role="dialog" aria-modal="true" aria-label="Room Photo Gallery" aria-hidden="true">
        <div class="mhd-room-gallery-overlay" id="mHdRoomGalleryOverlay"></div>
        <div class="mhd-room-gallery-content">
            <div class="mhd-room-gallery-header">
                <h3 class="mhd-room-gallery-title" id="mHdRoomGalleryTitle">Room Name</h3>
                <button type="button" class="mhd-room-gallery-close" id="mHdRoomGalleryCloseBtn" aria-label="Close room gallery">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="mhd-room-gallery-slider-wrap">
                <div class="mhd-room-gallery-slider" id="mHdRoomGallerySlider">
                    <!-- Slides injected by JS -->
                </div>
                <div class="mhd-room-gallery-counter" id="mHdRoomGalleryCounter">1 / 1</div>
            </div>
        </div>
    </div>

    <!-- ── Review Details Modal (Bottom Sheet) ───────────────────────── -->
    <div class="mhd-room-gallery-modal mhd-price-details-modal is-hidden" id="mHdPriceDetailsModal" role="dialog" aria-modal="true" aria-label="Review Details" aria-hidden="true">
        <div class="mhd-room-gallery-overlay" id="mHdPriceDetailsOverlay"></div>
        <div class="mhd-room-gallery-content">
            <div class="mhd-room-gallery-header">
                <h3 class="mhd-room-gallery-title">Review Details</h3>
                <button type="button" class="mhd-room-gallery-close" id="mHdPriceDetailsCloseBtn" aria-label="Close review details">
                    <span class="material-symbols-outlined" aria-hidden="true">close</span>
                </button>
            </div>
            <div class="mhd-pd-body">
                <div class="mhd-pd-hotel-name"><?php echo htmlspecialchars($hotel['name']); ?>, <?php echo htmlspecialchars($hotel['place']); ?></div>
                
                <div class="mhd-pd-dates">
                    <div class="mhd-pd-date-box">
                        <div class="mhd-pd-date-label">CHECK IN</div>
                        <div class="mhd-pd-date-val" id="mHdSidebarCheckIn">--</div>
                    </div>
                    <div class="mhd-pd-nights">
                        <span class="material-symbols-outlined">dark_mode</span>
                        <span class="mhd-pd-nights-val" id="mHdSidebarNights">1N</span>
                    </div>
                    <div class="mhd-pd-date-box">
                        <div class="mhd-pd-date-label">CHECK OUT</div>
                        <div class="mhd-pd-date-val" id="mHdSidebarCheckOut">--</div>
                    </div>
                </div>

                <div class="mhd-pd-room-name" id="mHdDispRoomName">ROOM NAME</div>
                <div class="mhd-pd-plan-name" id="mHdDispPlanName">Plan Name</div>
                
                <div class="mhd-pd-row">
                    <span class="mhd-pd-row-net">Net Rate :</span>
                    <span class="mhd-pd-val-net" id="mHdSidebarNetRate">₹0</span>
                </div>
                <div class="mhd-pd-row mhd-pd-row-gst">
                    <span>GST :</span>
                    <span id="mHdSidebarTotalTax">+ ₹0 Taxes &amp; fees</span>
                </div>
                <div class="mhd-pd-divider"></div>
                <div class="mhd-pd-total">
                    <span>TOTAL PAYABLE :</span>
                    <span id="mHdSidebarTotalFinal">₹0</span>
                </div>
                
                <button type="button" class="mhd-pd-proceed" id="mHdBtnProceed">
                    SUBMIT
                </button>
            </div>
        </div>
    </div>

    <!-- ── Checkout Modal (Fullscreen) ──────────────────────────────── -->
    <div class="mhd-gallery-modal mhd-checkout-modal is-hidden" id="mHdCheckoutModal" role="dialog" aria-modal="true" aria-label="Guest Information" aria-hidden="true">
        <div class="mhd-gallery-header mhd-checkout-header">
            <button type="button" class="mhd-gallery-close" id="mHdCheckoutCloseBtn" aria-label="Close checkout">
                <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <h3 class="mhd-gallery-title">Enquiry Summary</h3>
        </div>
        <div class="mhd-gallery-body mhd-checkout-body">
            
            <div class="mhd-summary-card">
                <div class="mhd-sc-hotel">
                    <div class="mhd-sc-hotel-info">
                        <div class="mhd-sc-hotel-name" id="mHdCheckoutSummaryHotelName"><?php echo htmlspecialchars($hotel['name']); ?></div>
                        <div class="mhd-sc-hotel-loc"><?php echo htmlspecialchars($hotel['city'] ?? 'Gangtok'); ?></div>
                    </div>
                    <div class="mhd-sc-hotel-img">
                        <?php 
                        $hero_img = !empty($hotel_images) ? $hotel_images[0]['image_url'] : 'placeholder.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($hero_img); ?>" alt="Hotel">
                    </div>
                </div>
                
                <div class="mhd-sc-divider"></div>
                
                <div class="mhd-sc-dates">
                    <div class="mhd-sc-date-col">
                        <div class="mhd-sc-date-label">CHECK-IN</div>
                        <div class="mhd-sc-date-val" id="mHdCheckoutSummaryCheckIn">--</div>
                    </div>
                    <div class="mhd-sc-nights">
                        <span id="mHdCheckoutSummaryNights">1N</span>
                    </div>
                    <div class="mhd-sc-date-col right">
                        <div class="mhd-sc-date-label">CHECK-OUT</div>
                        <div class="mhd-sc-date-val" id="mHdCheckoutSummaryCheckOut">--</div>
                    </div>
                </div>
                
                <div class="mhd-sc-divider"></div>
                
                <div class="mhd-sc-guests">
                    <div class="mhd-sc-guests-label">Guests & Rooms</div>
                    <div class="mhd-sc-guests-val" id="mHdCheckoutSummaryGuests">--</div>
                </div>
            </div>

            <div class="mhd-summary-card">
                <div class="mhd-sc-room-name" id="mHdCheckoutSummaryRoomName">--</div>
                <div class="mhd-sc-plan-name" id="mHdCheckoutSummaryPlanName">--</div>
                
                <div class="mhd-sc-divider"></div>
                
                <div class="mhd-sc-price-row">
                    <span>Net Rate :</span>
                    <span id="mHdCheckoutSummaryBasePrice">--</span>
                </div>
                <div class="mhd-sc-price-row">
                    <span>GST :</span>
                    <span id="mHdCheckoutSummaryTaxes">Included</span>
                </div>
                <div class="mhd-sc-divider"></div>
                <div class="mhd-sc-total-row">
                    <span>TOTAL PAYABLE</span>
                    <span id="mHdCheckoutSummaryTotal">--</span>
                </div>
            </div>
            <form id="mobileHotelDetailForm" action="api-submit-hotel-booking.php">
                <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $hotel['type']; ?>">
                <input type="hidden" name="room_id" id="mHdFormRoomId">
                <input type="hidden" name="plan_id" id="mHdFormPlanId">
                <input type="hidden" name="final_price" id="mHdFormFinalPrice">
                <input type="hidden" name="selected_room" id="mHdFormSelectedRoom">
                <input type="hidden" name="selected_plan_name" id="mHdFormSelectedPlanName">
                
                <input type="hidden" name="rooms" id="mHdFormRooms" value="1">
                <input type="hidden" name="adults" id="mHdFormAdults" value="2">
                <input type="hidden" name="children" id="mHdFormChildren" value="0">
                <input type="hidden" name="check_in" id="mHdFormCheckIn" value="<?php echo htmlspecialchars($check_in); ?>">
                <input type="hidden" name="check_out" id="mHdFormCheckOut" value="<?php echo htmlspecialchars($check_out); ?>">
                
                <div class="mhd-co-section">
                    <h4 class="mhd-co-section-title">Guest Information</h4>
                    
                    <div class="mhd-co-form-group">
                        <label for="mHdGuestName" class="mhd-co-label">First Name *</label>
                        <input type="text" id="mHdGuestName" name="guest_name" required class="mhd-co-input">
                    </div>
                    
                    <div class="mhd-co-form-group">
                        <label for="mHdGuestLastName" class="mhd-co-label">Last Name *</label>
                        <input type="text" id="mHdGuestLastName" name="guest_last_name" required class="mhd-co-input">
                    </div>
                    
                    <div class="mhd-co-form-group">
                        <label for="mHdGuestEmail" class="mhd-co-label">Email Address *</label>
                        <input type="email" id="mHdGuestEmail" name="email" required class="mhd-co-input">
                    </div>
                    
                    <div class="mhd-co-form-group">
                        <label for="mHdGuestPhone" class="mhd-co-label">Phone Number *</label>
                        <input type="tel" id="mHdGuestPhone" name="phone" required class="mhd-co-input">
                    </div>
                    
                    <div class="mhd-co-form-group">
                        <label for="mHdSpecialRequests" class="mhd-co-label">Special Requests</label>
                        <textarea id="mHdSpecialRequests" name="special_request" rows="3" class="mhd-co-input textarea--no-resize"></textarea>
                    </div>
                </div>
                
                <div id="mHdFormMsg" class="mhd-form-feedback form-feedback"></div>
                
                <button type="submit" id="mHdCheckoutSubmitBtn" class="mhd-co-submit">
                    <?php echo $hotel['type'] == 'signature' ? 'CONFIRM BOOKING' : 'SUBMIT INQUIRY'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- ── Mobile Pickers (Date & Guest) ────────────────────────────── -->
    <?php include __DIR__ . '/mobile_hotel_datepicker.php'; ?>
    <?php include __DIR__ . '/mobile_hotel_guestpicker.php'; ?>

<?php 
$hide_bottom_nav = true; 
include __DIR__ . '/mobile_footer.php'; 
?>
