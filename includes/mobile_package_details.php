<?php
if (!$pdo) {
    die("Database connection failed.");
}

// Variables are already set by public/package-detail.php
// $pkg, $itinerary, $highlights_arr, $inclusions_arr, $exclusions_arr, $photos_arr, $destinations_list

$page_title = htmlspecialchars($pkg['title']) . " | Leisure Loop";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/vendor/leaflet.css" />
    <script src="js/vendor/leaflet.js"></script>
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="mob-pkg-body">

    <!-- App Sticky Navigation -->
    <div class="app-header" id="appHeader">
        <a aria-label="Back to Packages" href="packages.php" class="header-btn">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <span class="mob-pkg__nav-label">Package Details</span>
        <button class="header-btn" data-action="share-package" data-title="<?php echo htmlspecialchars($pkg['title'] ?? 'Leisure Loop Package'); ?>">
            <span class="material-symbols-outlined text-[19px]">share</span>
        </button>
    </div>

    <?php
    $nights_count = !empty($pkg['nights']) ? (int)$pkg['nights'] : max(1, (int)($pkg['days'] ?? 1) - 1);
    $days_count = !empty($pkg['days']) ? (int)$pkg['days'] : ($nights_count + 1);
    $tour_types_list = array_filter(array_map('trim', explode(',', $tour_type)));
    ?>

    <!-- 1. Header Typography & Identity Block -->
    <div class="mob-pkg__title-block">
        <h1 class="mob-pkg__title">
            <?= htmlspecialchars($pkg['title']) ?>
        </h1>
        <div class="mob-pkg__meta-row">
            <span class="material-symbols-outlined mob-pkg__meta-icon">schedule</span>
            <span><?= str_pad((string)$nights_count, 2, '0', STR_PAD_LEFT) ?> Nights / <?= str_pad((string)$days_count, 2, '0', STR_PAD_LEFT) ?> Days in <?= htmlspecialchars($pkg['destination']) ?></span>
        </div>
        <?php if (!empty($tour_types_list)): ?>
        <div class="mob-pkg__theme-pills">
            <?php foreach ($tour_types_list as $tt): ?>
            <span class="m-theme-pill">
                <?= htmlspecialchars($tt) ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- 2. Compact Hero Media Carousel -->
    <div class="hero-carousel-wrapper">
        <div class="hero-carousel" id="heroCarousel">
            <?php foreach ($photos_arr as $photo): ?>
            <div class="hero-slide">
                <img src="<?php echo htmlspecialchars($photo); ?>" class="hero-img" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                <div class="hero-overlay"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="hero-dots" id="heroDots">
            <?php foreach ($photos_arr as $idx => $photo): ?>
            <div class="dot <?php echo $idx === 0 ? 'active' : ''; ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. Obsidian & Gold Credential Ribbon (Tour Code + Reviews) -->
    <div class="m-credential-wrap">
        <div class="m-credential-pill">
            <div class="m-credential-group">
                <span class="material-symbols-outlined m-credential-icon">star</span>
                <span class="m-credential-score"><?= number_format($rating_score, 1) ?>/5</span>
                <span class="m-credential-count">(<?= $rating_count ?> reviews)</span>
            </div>
            <div class="m-credential-divider"></div>
            <div class="m-credential-group">
                <span class="m-credential-label">Code:</span>
                <span class="m-credential-value"><?= $tour_code ?></span>
            </div>
        </div>
    </div>

    <!-- 4. Tour Highlights Section -->
    <?php if (!empty($highlights_arr)): ?>
    <div class="section m-highlights-section">
        <div class="mob-pkg__highlights">
            <div class="mob-pkg__highlights-head">
                <div class="mob-pkg__highlights-left">
                    <span class="material-symbols-outlined mob-pkg__highlights-icon">auto_awesome</span>
                    <h2 class="mob-pkg__highlights-title">Tour Highlights</h2>
                </div>
                <span class="mob-pkg__highlights-badge">Curated</span>
            </div>
            <div class="mob-pkg__highlights-list">
                <?php
                $hl_icons = ['verified', 'hotel_class', 'directions_car', 'landscape', 'explore', 'local_see'];
                foreach ($highlights_arr as $i => $highlight):
                    $hl_icon = $hl_icons[$i % count($hl_icons)];
                ?>
                <div class="mob-pkg__highlight-item">
                    <div class="mob-pkg__highlight-icon-wrap">
                        <span class="material-symbols-outlined mob-pkg__highlight-icon"><?= $hl_icon ?></span>
                    </div>
                    <div>
                        <p class="mob-pkg__highlight-text">
                            <?= htmlspecialchars($highlight) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Itinerary Accordion -->
    <?php if (!empty($itinerary)): ?>
    <div class="section">
        <h2 class="section-title">Day-by-Day Journey</h2>
        <div class="m-itinerary-grid">
            <?php foreach ($itinerary as $i => $day): 
                $day_num = $day['day'] ?? ($i + 1);
            ?>
            <div class="m-itinerary-card accordion-item <?php echo $i == 0 ? 'active' : ''; ?>">
                <div class="m-itinerary-header accordion-header" data-action="toggle-accordion">
                    <div class="m-itinerary-left">
                        <div class="m-itinerary-day-col">
                            <span class="m-day-label">DAY</span>
                            <span class="m-day-num"><?php echo str_pad((string)$day_num, 2, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="m-itinerary-title">
                            <?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?>
                        </div>
                    </div>
                    <span class="material-symbols-outlined accordion-icon">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content">
                    <div class="m-itinerary-desc">
                        <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fixed Departure Details (Logistics & Stay) -->
    <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
    <div class="section">
        <h2 class="section-title m-title-green">Logistics &amp; Stay</h2>

        <?php if (!empty($pkg['hotel_details'])): ?>
        <div class="mob-pkg__logistics-card">
            <div class="mob-pkg__logistics-head">
                <span class="material-symbols-outlined mob-pkg__logistics-icon--blue">hotel</span>
                <h4 class="mob-pkg__logistics-heading">Accommodations</h4>
            </div>
            <div class="mob-pkg__logistics-body">
                <?php echo nl2br(htmlspecialchars($pkg['hotel_details'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($pkg['vehicle_details'])): ?>
        <div class="mob-pkg__logistics-card">
            <div class="mob-pkg__logistics-head">
                <span class="material-symbols-outlined mob-pkg__logistics-icon--purple">directions_car</span>
                <h4 class="mob-pkg__logistics-heading">Transportation</h4>
            </div>
            <div class="mob-pkg__logistics-body">
                <?php echo nl2br(htmlspecialchars($pkg['vehicle_details'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($pkg['meal_plan_details'])): ?>
        <div class="mob-pkg__logistics-card">
            <div class="mob-pkg__logistics-head">
                <span class="material-symbols-outlined mob-pkg__logistics-icon--orange">restaurant</span>
                <h4 class="mob-pkg__logistics-heading">Meal Plan</h4>
            </div>
            <div class="mob-pkg__logistics-body">
                <?php echo nl2br(htmlspecialchars($pkg['meal_plan_details'])); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Bespoke Stay Options (Hotel Categories) -->
    <div class="section">
        <span class="m-section-eyebrow">CURATED ACCOMMODATION TIERS</span>
        <h2 class="section-title">Select Hotel Category</h2>
        <p class="m-section-subtitle">Tailor your journey's ambiance with handpicked verified properties.</p>
        
        <!-- 4 Compact Selectors in 2x2 Grid -->
        <div class="mob-pkg__selector-grid mb-4">
            <?php foreach ($hotel_categories as $idx => $stay):
                $is_sel = !empty($stay['recommended']);
            ?>
            <div class="m-selector-card <?php echo $is_sel ? 'selected' : ''; ?>"
                 data-action="m-select-stay"
                 data-stay-idx="<?php echo $idx; ?>"
                 data-stay-label="<?php echo htmlspecialchars($stay['stars'] . ' ' . explode(' ', $stay['title'])[0]); ?>"
                 role="button"
                 tabindex="0">
                <span class="mob-pkg__selector-stars"><?php echo $stay['stars_display']; ?></span>
                <span class="mob-pkg__selector-name"><?php echo htmlspecialchars($stay['title']); ?></span>
                <div class="m-selector-indicator">
                    <span class="material-symbols-outlined text-sm icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined text-sm icon-checked">check_circle</span>
                    <span class="indicator-text-unchecked">Select</span>
                    <span class="indicator-text-checked">Selected</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Single Active Hotel Detail Card -->
        <div class="m-stay-details-wrap">
            <?php foreach ($hotel_categories as $idx => $stay):
                $is_sel = !empty($stay['recommended']);
            ?>
            <div class="m-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="m-stay-detail-<?php echo $idx; ?>">
                <div class="mob-pkg__detail-img-wrap">
                    <img src="<?php echo htmlspecialchars($stay['img']); ?>" class="mob-pkg__detail-img" alt="<?php echo htmlspecialchars($stay['title']); ?>">
                    <div class="mob-pkg__detail-img-overlay" aria-hidden="true"></div>
                    <div class="mob-pkg__detail-img-badge"><?php echo $stay['stars_display']; ?></div>
                </div>
                <div class="mob-pkg__detail-body">
                    <span class="mob-pkg__detail-eyebrow"><?php echo htmlspecialchars($stay['tagline']); ?></span>
                    <h3 class="mob-pkg__detail-name"><?php echo htmlspecialchars($stay['title']); ?></h3>
                    <p class="mob-pkg__detail-desc"><?php echo htmlspecialchars($stay['desc']); ?></p>
                    <div class="mob-pkg__detail-features">
                        <?php foreach ($stay['features'] as $feat): ?>
                        <div class="mob-pkg__detail-feature">
                            <span class="material-symbols-outlined mob-pkg__detail-feature-icon--green">check_circle</span>
                            <span><?php echo htmlspecialchars($feat); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cab Fleet Selection -->
    <div class="section">
        <span class="m-section-eyebrow">CHAUFFEUR DRIVEN FLEET</span>
        <h2 class="section-title">Select Private Cab</h2>
        <p class="m-section-subtitle">Dedicated mountain-specialist vehicle &amp; experienced chauffeur.</p>

        <!-- 4 Compact Selectors in 2x2 Grid -->
        <div class="mob-pkg__selector-grid mb-4">
            <?php foreach ($cab_types as $idx => $cab):
                $is_sel = !empty($cab['recommended']);
            ?>
            <div class="m-selector-card <?php echo $is_sel ? 'selected' : ''; ?>"
                 data-action="m-select-cab"
                 data-cab-idx="<?php echo $idx; ?>"
                 data-cab-label="<?php echo htmlspecialchars($cab['name']); ?>"
                 role="button"
                 tabindex="0">
                <span class="mob-pkg__selector-capacity">
                    <span class="material-symbols-outlined icon-xs-inline">group</span> <?php echo $cab['seats']; ?>
                </span>
                <span class="mob-pkg__selector-name"><?php echo htmlspecialchars($cab['name']); ?></span>
                <div class="m-selector-indicator">
                    <span class="material-symbols-outlined text-sm icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined text-sm icon-checked">check_circle</span>
                    <span class="indicator-text-unchecked">Select</span>
                    <span class="indicator-text-checked">Selected</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Single Active Cab Detail Card -->
        <div class="m-cab-details-wrap">
            <?php foreach ($cab_types as $idx => $cab):
                $is_sel = !empty($cab['recommended']);
            ?>
            <div class="m-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="m-cab-detail-<?php echo $idx; ?>">
                <div class="mob-pkg__detail-img-wrap">
                    <img src="<?php echo htmlspecialchars($cab['img']); ?>" class="mob-pkg__detail-img" alt="<?php echo htmlspecialchars($cab['name']); ?>">
                    <div class="mob-pkg__detail-img-overlay" aria-hidden="true"></div>
                    <div class="mob-pkg__detail-img-badge mob-pkg__detail-img-badge--white">
                        <span class="material-symbols-outlined icon-xs-gold">group</span> <?php echo $cab['seats']; ?>
                    </div>
                </div>
                <div class="mob-pkg__detail-body">
                    <span class="mob-pkg__detail-eyebrow"><?php echo htmlspecialchars($cab['category']); ?></span>
                    <h3 class="mob-pkg__detail-name"><?php echo htmlspecialchars($cab['name']); ?></h3>
                    <p class="mob-pkg__detail-desc"><?php echo htmlspecialchars($cab['desc']); ?></p>
                    <div class="mob-pkg__detail-features">
                        <div class="mob-pkg__detail-feature">
                            <span class="material-symbols-outlined mob-pkg__detail-feature-icon--gold">luggage</span>
                            <span>Bags: <?php echo $cab['bags']; ?></span>
                        </div>
                        <?php foreach ($cab['features'] as $feat): ?>
                        <div class="mob-pkg__detail-feature">
                            <span class="material-symbols-outlined mob-pkg__detail-feature-icon--green">check_circle</span>
                            <span><?php echo htmlspecialchars($feat); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Inclusions & Exclusions Luxury Card Accordions -->
    <div class="section m-inc-exc-section">
        <span class="m-section-eyebrow">CLARITY &amp; TRANSPARENCY</span>
        <h2 class="section-title m-inc-exc-title">What's Included &amp; Excluded</h2>

        <div class="m-cards-wrapper">
            <!-- Inclusions Card (Emerald Theme) -->
            <div class="accordion-item m-card-inc active">
                <div class="accordion-header m-card-header" data-action="toggle-accordion" role="button" tabindex="0">
                    <div class="m-card-header-left">
                        <span class="material-symbols-outlined m-header-icon m-header-icon--inc">check_circle</span>
                        <span class="m-card-title m-card-title--inc">Inclusions</span>
                    </div>
                    <span class="material-symbols-outlined accordion-icon m-card-arrow m-card-arrow--inc">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content">
                    <div class="m-card-inner">
                        <ul class="m-feature-list">
                            <?php foreach ($inclusions_arr as $inc): ?>
                            <li class="m-feature-item">
                                <span class="material-symbols-outlined m-item-icon m-item-icon--inc">check</span>
                                <span class="m-item-text"><?= htmlspecialchars($inc); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Exclusions Card (Crimson Theme) -->
            <div class="accordion-item m-card-exc">
                <div class="accordion-header m-card-header" data-action="toggle-accordion" role="button" tabindex="0">
                    <div class="m-card-header-left">
                        <span class="material-symbols-outlined m-header-icon m-header-icon--exc">cancel</span>
                        <span class="m-card-title m-card-title--exc">Exclusions</span>
                    </div>
                    <span class="material-symbols-outlined accordion-icon m-card-arrow m-card-arrow--exc">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content">
                    <div class="m-card-inner">
                        <ul class="m-feature-list">
                            <?php foreach ($exclusions_arr as $exc): ?>
                            <li class="m-feature-item">
                                <span class="material-symbols-outlined m-item-icon m-item-icon--exc">close</span>
                                <span class="m-item-text"><?= htmlspecialchars($exc); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="section">
        <h2 class="section-title">Tour Route</h2>
        <div class="map-container" id="mapWrap">
            <div class="map-overlay" id="mapOverlay" data-action="enable-map">
                <div class="map-overlay-text">Tap to interact</div>
            </div>
            <?php
            $mc = $pkg['map_coords'] ?? '';
            $parts = explode(',', $mc);
            $fallback = (count($parts) === 2) ? ((float)trim($parts[0])) . ',' . ((float)trim($parts[1])) : '27.3314,88.6138';
            ?>
            <div id="tourMap" class="map-full" data-itinerary='<?php echo htmlspecialchars(json_encode(array_values($itinerary)), ENT_QUOTES, 'UTF-8'); ?>' data-fallback-coords="<?php echo htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8'); ?>"></div>
        </div>
    </div>

    <!-- Terms & Conditions Section -->
    <?php
        $default_fallback_terms = "##Booking Confirmation##: Bookings are confirmed only upon receipt of 50% advance deposit.\n##Cancellation Policy##: Cancellations made 15 days prior to arrival will incur a 20% administrative fee. Cancellations made within 7 days are non-refundable.\n##Permits & Identity##: All guests must carry valid government-issued photo ID cards and passport-size photographs for Himalayan restricted-area entry permits.\n##Weather Delays##: Route alterations caused by landslides, snow blocks, or national park regulations will be accommodated on best-effort basis without liability for missed connections.";

        $tc_raw = '';
        if (!empty($pkg['use_destination_terms']) && !empty($pkg['dest_terms'])) {
            $tc_raw = $pkg['dest_terms'];
        } elseif (!empty($pkg['terms_conditions'])) {
            $tc_raw = $pkg['terms_conditions'];
        } else {
            $tc_raw = $default_fallback_terms;
        }
    ?>
    <div class="section m-tc-section">
        <span class="m-section-eyebrow">LEGAL &amp; POLICIES</span>
        <h2 class="section-title">Terms &amp; Conditions</h2>
        <div class="m-tc-card">
            <div class="m-tc-content">
                <?php
                    $tc_text = htmlspecialchars(trim($tc_raw));
                    $tc_text = nl2br($tc_text);
                    $tc_text = preg_replace('/##(.*?)##/s', '<strong class="tc-gold-highlight">$1</strong>', $tc_text);
                    echo $tc_text;
                ?>
            </div>
        </div>
    </div>

    <!-- Other Tours Section -->
    <?php if (!empty($other_tours)): ?>
    <div class="section m-other-tours-section">
        <span class="m-section-eyebrow">EXPLORE MORE</span>
        <h2 class="section-title">Other Tours You May Like</h2>
        <div class="m-carousel-container">
            <?php foreach ($other_tours as $tour): ?>
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($tour['slug']); ?>&view=mobile" class="m-tour-card">
                    <div class="m-tour-card-img">
                        <img src="<?php echo htmlspecialchars($tour['image_url'] ?? 'images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" loading="lazy">
                        <div class="m-tour-duration"><?php echo $tour['nights']; ?>N / <?php echo $tour['days']; ?>D</div>
                    </div>
                    <div class="m-tour-card-body">
                        <h3 class="m-tour-card-title"><?php echo htmlspecialchars($tour['title']); ?></h3>
                        <div class="m-tour-card-price">From &#8377;<?php echo number_format($tour['price']); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="spacer-cta"></div> <!-- Spacer for Sticky CTA -->

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <div class="cta-price-col">
            <span class="cta-price-label">Starting From</span>
            <span class="cta-price-val">&#8377;<?php echo number_format($pkg['price'] ?? 0); ?></span>
        </div>
        <button data-action="open-modal" class="btn-primary">Enquire Now</button>
    </div>

    <!-- Enquiry Modal Full-Height Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content m-enquiry-sheet" id="enquiryModal" data-nights="<?php echo (int)$nights_count; ?>" data-days="<?php echo (int)$days_count; ?>" data-base-price="<?php echo (float)($pkg['price'] ?? 0); ?>">
        <div class="modal-header m-enquiry-sheet-header">
            <div>
                <span class="m-modal-kicker">TRAVELLER DETAILS</span>
                <h2 class="modal-title m-modal-main-title">Review &amp; Enquire</h2>
            </div>
            <button class="modal-close" data-action="close-modal" aria-label="Close modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="modal-body m-enquiry-sheet-body">
            <form action="api-submit-package-booking.php" method="POST" id="mobileEnquiryForm">
                <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="package_id" value="<?php echo (int)($pkg['id'] ?? 0); ?>">
                <input type="hidden" name="package_title" value="<?php echo htmlspecialchars($pkg['title']); ?>">
                <input type="hidden" name="tour_code" value="<?php echo htmlspecialchars($tour_code ?? 'TBA'); ?>">
                <input type="hidden" name="selected_hotel" id="m_preferred_stay_input" value="4 Star Luxury">
                <input type="hidden" name="selected_cab" id="m_preferred_cab_input" value="Innova / Xylo / Scorpio">
                <input type="hidden" name="source" value="mobile_package_details">

                <!-- 1. Package Identity & Meta Overview -->
                <div class="m-sheet-card">
                    <h3 class="m-sheet-pkg-title"><?= htmlspecialchars($pkg['title']) ?></h3>
                    <div class="m-sheet-meta-row">
                        <span class="m-sheet-meta-pill">Code: <?= $tour_code ?></span>
                        <span class="m-sheet-meta-pill"><?= (int)$nights_count ?>N / <?= (int)$days_count ?>D</span>
                    </div>

                    <!-- EaseMyTrip Date Grid -->
                    <div class="m-sheet-date-grid">
                        <div class="m-sheet-date-col">
                            <span class="m-sheet-date-label">Start Date</span>
                            <span class="m-sheet-date-val" id="m_modal_start_date_display">-</span>
                        </div>
                        <div class="m-sheet-nights-pill">
                            <span class="material-symbols-outlined">nightlight</span>
                            <span><?= (int)$nights_count ?>N</span>
                        </div>
                        <div class="m-sheet-date-col m-sheet-date-col--right">
                            <span class="m-sheet-date-label">End Date</span>
                            <span class="m-sheet-date-val" id="m_modal_end_date_display">-</span>
                        </div>
                    </div>

                    <!-- Luxury Inclusion Badges -->
                    <div class="m-sheet-amenities-strip">
                        <div class="m-sheet-amenity-item" title="Stay Included">
                            <span class="material-symbols-outlined">hotel</span>
                            <span>Stay</span>
                        </div>
                        <div class="m-sheet-amenity-item" title="Sightseeing Included">
                            <span class="material-symbols-outlined">photo_camera</span>
                            <span>Sightseeing</span>
                        </div>
                        <div class="m-sheet-amenity-item" title="Transfers Included">
                            <span class="material-symbols-outlined">directions_car</span>
                            <span>Transfers</span>
                        </div>
                        <div class="m-sheet-amenity-item" title="Meals Included">
                            <span class="material-symbols-outlined">restaurant</span>
                            <span>Meals</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Interactive Selection Summary -->
                <div class="m-sheet-card">
                    <div class="m-sheet-spec-row">
                        <span class="m-sheet-spec-label">Selected Stay Tier:</span>
                        <strong id="m_summary_stay_val" class="m-sheet-spec-val mob-pkg__spec-gold">4 Star Luxury</strong>
                    </div>
                    <div class="m-sheet-spec-row m-sheet-spec-row--bordered">
                        <span class="m-sheet-spec-label">Selected Private Cab:</span>
                        <strong id="m_summary_cab_val" class="m-sheet-spec-val mob-pkg__spec-white">Innova / Xylo / Scorpio</strong>
                    </div>
                </div>

                <!-- 3. Primary Traveller Details Form -->
                <div class="m-sheet-card">
                    <span class="m-sheet-subheading">GUEST INFORMATION</span>

                    <div class="mb-3">
                        <label for="m_guest_first_name" class="m-form-label">First Name *</label>
                        <input id="m_guest_first_name" type="text" name="guest_name" class="form-input" placeholder="First Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="m_guest_last_name" class="m-form-label">Last Name *</label>
                        <input id="m_guest_last_name" type="text" name="guest_last_name" class="form-input" placeholder="Last Name" required>
                    </div>

                    <div class="mob-pkg__form-row">
                        <div>
                            <label for="m_travel_date_input" class="m-form-label">Travel Date *</label>
                            <input id="m_travel_date_input" type="date" name="travel_date" class="form-input m-date-input" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div>
                            <label for="m_guests_select" class="m-form-label">No. of Guests *</label>
                            <select id="m_guests_select" name="adults" class="form-input form-select m-select-input">
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5 Guests</option>
                                <option value="6">6+ Guests</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="m_special_requests" class="m-form-label">Special Requests (Optional)</label>
                        <input id="m_special_requests" type="text" name="special_request" class="form-input" placeholder="e.g. Early check-in, ground floor room">
                    </div>

                    <span class="m-sheet-subheading mt-4">CONTACT DETAILS</span>

                    <div class="form-group mb-3">
                        <label for="m_guest_phone" class="m-form-label">Phone Number *</label>
                        <div class="mob-pkg__field-wrap">
                            <span class="material-symbols-outlined form-icon" aria-hidden="true">call</span>
                            <input id="m_guest_phone" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="m_guest_email" class="m-form-label">Email Address *</label>
                        <div class="mob-pkg__field-wrap">
                            <span class="material-symbols-outlined form-icon" aria-hidden="true">mail</span>
                            <input id="m_guest_email" type="email" name="email" class="form-input" placeholder="Email Address" required>
                        </div>
                    </div>
                </div>

                <div class="spacer-form"></div> <!-- Spacer for Sticky Sheet Footer -->

                <div id="mModalFormMsg" class="p-form-feedback"></div>

                <!-- 4. EaseMyTrip-Style Sticky Bottom Footer inside Sheet -->
                <div class="m-sheet-sticky-footer">
                    <div class="m-sheet-footer-pricing">
                        <span class="m-sheet-footer-label">Total Amount:</span>
                        <span class="m-sheet-footer-total" id="m_modal_total_amount_val">&#8377;<?= number_format(($pkg['price'] ?? 0) * 2) ?></span>
                        <span class="m-sheet-footer-note">(Base Price &times; Guests)</span>
                    </div>
                    <button type="submit" class="btn-primary m-sheet-submit-btn" id="mBtnPackageSubmit">SUBMIT ENQUIRY</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
</body>
</html>
