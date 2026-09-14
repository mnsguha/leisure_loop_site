<?php
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/package-detail.css?v=<?php echo time(); ?>">


<!-- Zero-CDN: Local Map Assets & Native HTML5 Date Inputs -->
<link rel="stylesheet" href="css/vendor/leaflet.css">
<script src="js/vendor/leaflet.js" defer></script>



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
<div class="package-header-card">
    <h1 class="detail-title">
        <?php echo htmlspecialchars($pkg['title']); ?> 
    </h1>
    <div class="detail-meta">
        <?php echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT); ?> Nights / <?php echo str_pad($pkg['days'], 2, '0', STR_PAD_LEFT); ?> Days in <?php echo htmlspecialchars($pkg['destination']); ?>
    </div>
    
    <div class="rating-badges">
        <div class="rating-badge-main">
            <?php echo number_format($rating_score, 1); ?>
            <span class="rating-badge-label">GUEST EXPERIENCES</span>
        </div>
        <div class="rating-count">(<?php echo $rating_count; ?> reviews)</div>
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
    <div class="details-ribbon mb-8">
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
                            <p><?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            
        <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
            <section class="detail-section-spacing fixed-departure-section">
                <span class="section-label logistics-label">LOGISTICS & STAY</span>
                <h2 class="serif-accent fixed-departure-title">Fixed Departure <span class="fixed-departure-subtitle">Details</span></h2>
                
                <div class="fixed-departure-grid">
                    <?php if (!empty($pkg['hotel_details'])): ?>
                    <div class="fixed-departure-card">
                        <div class="fixed-departure-card__icon fixed-departure-card__icon--blue">
                            <span class="material-symbols-outlined">hotel</span>
                        </div>
                        <h4 class="fixed-departure-card__heading">Accommodations</h4>
                        <div class="fixed-departure-card__body">
                            <?php echo nl2br(htmlspecialchars($pkg['hotel_details'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pkg['vehicle_details'])): ?>
                    <div class="fixed-departure-card">
                        <div class="fixed-departure-card__icon fixed-departure-card__icon--purple">
                            <span class="material-symbols-outlined">directions_car</span>
                        </div>
                        <h4 class="fixed-departure-card__heading">Transportation</h4>
                        <div class="fixed-departure-card__body">
                            <?php echo nl2br(htmlspecialchars($pkg['vehicle_details'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($pkg['meal_plan_details'])): ?>
                    <div class="fixed-departure-card">
                        <div class="fixed-departure-card__icon fixed-departure-card__icon--orange">
                            <span class="material-symbols-outlined">restaurant</span>
                        </div>
                        <h4 class="fixed-departure-card__heading">Meal Plan</h4>
                        <div class="fixed-departure-card__body">
                            <?php echo nl2br(htmlspecialchars($pkg['meal_plan_details'])); ?>
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
        <div class="bespoke-section-header">
            <span class="bespoke-section-header__eyebrow">CURATED ACCOMMODATION TIERS</span>
            <div class="bespoke-section-header__top">
                <h2 class="serif-accent bespoke-section-header__title">Select Your Hotel Category</h2>
                <div class="bespoke-section-header__badge">
                    <span class="material-symbols-outlined">verified</span> Handpicked verified properties
                </div>
            </div>
            <p class="bespoke-section-header__desc">
                Tailor your journey's ambiance. Whether you desire essential cozy comfort or royal palatial suites, our handpicked properties guarantee world-class Himalayan hospitality.
            </p>
        </div>

        <!-- Compact Selection Cards -->
        <div class="compact-selector-grid">
            <?php foreach ($hotel_categories as $idx => $stay):
                $is_sel = !empty($stay['recommended']);
            ?>
            <label class="compact-stay-card <?php echo $is_sel ? 'selected-stay' : ''; ?>" id="stay-card-<?php echo $idx; ?>" data-stay-idx="<?php echo $idx; ?>" data-stay-label="<?php echo htmlspecialchars($stay['stars'] . ' ' . explode(' ', $stay['title'])[0]); ?>">
                <input type="radio" name="hotel_selection_radio" value="<?php echo $idx; ?>" class="sr-only stay-radio-btn" <?php echo $is_sel ? 'checked' : ''; ?>>
                <span class="compact-selector__stars"><?php echo $stay['stars_display']; ?></span>
                <span class="compact-selector__name"><?php echo htmlspecialchars($stay['title']); ?></span>
                <div class="stay-indicator">
                    <span class="material-symbols-outlined icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined icon-checked">check_circle</span>
                    <span class="btn-text-unchecked">Select</span>
                    <span class="btn-text-checked">Selected</span>
                </div>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Detail View (Horizontal Cards) -->
        <div class="detail-stay-container">
            <?php foreach ($hotel_categories as $idx => $stay):
                $is_sel = !empty($stay['recommended']);
            ?>
            <div class="stay-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="stay-detail-<?php echo $idx; ?>">
                <div class="stay-detail-card-inner">
                    <div class="detail-card-img-col">
                        <img src="<?php echo htmlspecialchars($stay['img']); ?>" alt="<?php echo htmlspecialchars($stay['title']); ?>">
                        <div class="detail-card-img-gradient" aria-hidden="true"></div>
                        <div class="detail-card-img-gradient--mobile" aria-hidden="true"></div>
                        <div class="detail-card-img-badge"><?php echo $stay['stars_display']; ?></div>
                    </div>
                    <div class="detail-card-body-col">
                        <span class="detail-card__eyebrow"><?php echo htmlspecialchars($stay['tagline']); ?></span>
                        <h3 class="detail-card__name"><?php echo htmlspecialchars($stay['title']); ?></h3>
                        <p class="detail-card__desc"><?php echo htmlspecialchars($stay['desc']); ?></p>
                        <div class="detail-card__features">
                            <?php foreach ($stay['features'] as $feat): ?>
                            <div class="detail-card__feature">
                                <span class="material-symbols-outlined detail-card__feature-icon--green">check_circle</span>
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
        <div class="bespoke-section-header">
            <span class="bespoke-section-header__eyebrow">CHAUFFEUR DRIVEN FLEET</span>
            <div class="bespoke-section-header__top">
                <h2 class="serif-accent bespoke-section-header__title">Select Your Private Cab</h2>
                <div class="bespoke-section-header__badge">
                    <span class="material-symbols-outlined">directions_car_filled</span> Seamless private cab transfers for your journey
                </div>
            </div>
            <p class="bespoke-section-header__desc">
                Experience seamless, private luxury transfers and panoramic mountain expeditions with our professional mountain-specialist chauffeurs.
            </p>
        </div>

        <!-- Compact Selection Cards -->
        <div class="compact-selector-grid">
            <?php foreach ($cab_types as $idx => $cab):
                $is_sel = !empty($cab['recommended']);
            ?>
            <label class="compact-cab-card <?php echo $is_sel ? 'selected-cab' : ''; ?>" id="cab-card-<?php echo $idx; ?>" data-cab-idx="<?php echo $idx; ?>" data-cab-label="<?php echo htmlspecialchars($cab['name']); ?>">
                <input type="radio" name="cab_selection_radio" value="<?php echo $idx; ?>" class="sr-only stay-radio-btn" <?php echo $is_sel ? 'checked' : ''; ?>>
                <span class="compact-selector__capacity">
                    <span class="material-symbols-outlined" aria-hidden="true">group</span> <?php echo $cab['seats']; ?>
                </span>
                <span class="compact-selector__name"><?php echo htmlspecialchars($cab['name']); ?></span>
                <div class="stay-indicator">
                    <span class="material-symbols-outlined icon-unchecked">radio_button_unchecked</span>
                    <span class="material-symbols-outlined icon-checked">check_circle</span>
                    <span class="btn-text-unchecked">Select</span>
                    <span class="btn-text-checked">Selected</span>
                </div>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Detail View (Horizontal Cards) -->
        <div class="detail-cab-container">
            <?php foreach ($cab_types as $idx => $cab):
                $is_sel = !empty($cab['recommended']);
            ?>
            <div class="cab-detail-card <?php echo $is_sel ? 'is-active' : ''; ?>" id="cab-detail-<?php echo $idx; ?>">
                <div class="cab-detail-card-inner">
                    <div class="detail-card-img-col">
                        <img src="<?php echo htmlspecialchars($cab['img']); ?>" alt="<?php echo htmlspecialchars($cab['name']); ?>">
                        <div class="detail-card-img-gradient" aria-hidden="true"></div>
                        <div class="detail-card-img-gradient--mobile" aria-hidden="true"></div>
                        <div class="detail-card-img-badge detail-card-img-badge--white">
                            <span class="material-symbols-outlined" aria-hidden="true">group</span> <?php echo $cab['seats']; ?>
                        </div>
                    </div>
                    <div class="detail-card-body-col">
                        <span class="detail-card__eyebrow"><?php echo htmlspecialchars($cab['category']); ?></span>
                        <h3 class="detail-card__name"><?php echo htmlspecialchars($cab['name']); ?></h3>
                        <p class="detail-card__desc"><?php echo htmlspecialchars($cab['desc']); ?></p>
                        <div class="detail-card__features">
                            <div class="detail-card__feature">
                                <span class="material-symbols-outlined detail-card__feature-icon--gold">luggage</span>
                                <span>Bag Capacity: <?php echo $cab['bags']; ?></span>
                            </div>
                            <?php foreach ($cab['features'] as $feat): ?>
                            <div class="detail-card__feature">
                                <span class="material-symbols-outlined detail-card__feature-icon--green">check_circle</span>
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
<section class="inc-exc-grid detail-section-spacing">
    <div class="desktop-inc-card">
        <h3 class="inc-card-title">
            <span class="material-symbols-outlined">check_circle</span> Inclusions
        </h3>
        <ul class="inc-exc-list">
            <?php foreach ($inclusions_arr as $inc): ?>
            <li class="inc-exc-list__item">
                <span class="material-symbols-outlined inc-exc-list__icon inc-exc-list__icon--green">check</span>
                <span class="inc-exc-list__text"><?php echo htmlspecialchars($inc); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="desktop-exc-card">
        <h3 class="exc-card-title">
            <span class="material-symbols-outlined">cancel</span> Exclusions
        </h3>
        <ul class="inc-exc-list">
            <?php foreach ($exclusions_arr as $exc): ?>
            <li class="inc-exc-list__item">
                <span class="material-symbols-outlined inc-exc-list__icon inc-exc-list__icon--red">close</span>
                <span class="inc-exc-list__text"><?php echo htmlspecialchars($exc); ?></span>
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
    <aside class="sidebar-column">
        <div class="concierge-sidebar">
            <div class="concierge-sidebar__header">
                <span class="sidebar-starting-label">STARTING FROM</span>
                <div class="sidebar-price-row">
                    <span class="sidebar-price-main">&#8377;<?php echo number_format($pkg['price']); ?></span>
                    <?php if(!empty($original_price)): ?>
                        <span class="sidebar-price-original">&#8377;<?php echo number_format($original_price); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Info List -->
            <div class="sidebar-info-list">
                <div class="sidebar-info-row">
                    <span class="sidebar-info-row__label">Tour Code:</span>
                    <span class="sidebar-info-row__val"><?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?></span>
                </div>
                <div class="sidebar-info-row">
                    <span class="sidebar-info-row__label">Hotel Category:</span>
                    <span class="sidebar-info-row__val" id="summary_stay_val">4 Star Luxury</span>
                </div>
                <div class="sidebar-info-row">
                    <span class="sidebar-info-row__label">Private Cab:</span>
                    <span class="sidebar-info-row__val" id="summary_cab_val">Innova / Xylo / Scorpio</span>
                </div>
            </div>

            <!-- SUBMIT ENQUIRY Button -->
            <button data-action="open-checkout" class="enquiry-btn-gold" aria-label="Submit enquiry for this package">
                <div class="enquiry-btn-left">
                    <div class="enquiry-btn-icon-wrap">
                        <span class="material-symbols-outlined enquiry-btn-icon" aria-hidden="true">assignment</span>
                    </div>
                    <div class="enquiry-btn-text">
                        <div class="enquiry-btn-title">SUBMIT ENQUIRY</div>
                        <div class="enquiry-btn-subtitle">CUSTOM TOUR QUOTES</div>
                    </div>
                </div>
                <span class="material-symbols-outlined enquiry-btn-arrow" aria-hidden="true">arrow_forward</span>
            </button>

            <!-- Quick Summary -->
            <div class="sidebar-quick-summary">
                <div class="sidebar-summary-item">
                    <div class="sidebar-summary-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                    </div>
                    <div>
                        <span class="sidebar-summary-label">Duration</span>
                        <span class="sidebar-summary-val"><?php echo htmlspecialchars($pkg['nights'] . ' Nights &amp; ' . $pkg['days'] . ' Days'); ?></span>
                    </div>
                </div>
                <div class="sidebar-summary-item">
                    <div class="sidebar-summary-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                    </div>
                    <div class="min-w-0">
                        <span class="sidebar-summary-label">Places to Visit</span>
                        <span class="sidebar-summary-val"><?php echo htmlspecialchars($pkg['destination']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Package Includes Icons -->
            <div class="pkg-includes-box">
                <p class="pkg-includes-label">Package Includes</p>
                <div class="pkg-includes-grid">
                    <div class="pkg-includes-item">
                        <span class="material-symbols-outlined" aria-hidden="true">hotel</span>
                        <span class="pkg-includes-item__label">Hotel</span>
                    </div>
                    <div class="pkg-includes-item">
                        <span class="material-symbols-outlined" aria-hidden="true">photo_camera</span>
                        <span class="pkg-includes-item__label">Sightseeing</span>
                    </div>
                    <div class="pkg-includes-item">
                        <span class="material-symbols-outlined" aria-hidden="true">directions_car</span>
                        <span class="pkg-includes-item__label">Transfer</span>
                    </div>
                    <div class="pkg-includes-item">
                        <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                        <span class="pkg-includes-item__label">Meal</span>
                    </div>
                </div>
            </div>

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
                <h2 class="serif-accent related-title">Maybe you <span class="italic">like.</span></h2>
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
<div id="packageCheckoutModal" data-nights="<?php echo (int)($pkg['nights'] ?? 1); ?>" data-days="<?php echo (int)($pkg['days'] ?? 2); ?>">
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
                            <label for="modal_travel_date_input">Travel Date *</label>
                            <input type="date" name="travel_date" id="modal_travel_date_input" class="p-form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($check_in >= date('Y-m-d') ? $check_in : date('Y-m-d')); ?>" required>
                        </div>
                        <div class="p-form-group">
                            <label for="modal_adults_input">No. of Guests *</label>
                            <input type="number" name="adults" id="modal_adults_input" class="p-form-control" value="<?php echo max(1, (int)$adults); ?>" min="1" required data-change="update-price">
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
                            <div class="p-phone-input-group">
                                <span class="p-phone-prefix">+91</span>
                                <input type="tel" name="phone" class="p-form-control p-phone-field" required>
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
                <div id="packageModalFormMsg" class="p-form-feedback"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="p-modal-right">
            <h3 class="p-modal-summary-title">Enquiry Summary</h3>
            
            <h4 class="p-modal-pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h4>
            <p class="p-modal-pkg-code">TOUR CODE: <?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?></p>

            <!-- Duration & Travellers Meta -->
            <div class="p-modal-meta-box">
                <div class="p-modal-meta-row">
                    <span class="p-modal-meta-label">Duration:</span>
                    <strong class="p-modal-meta-val"><?php echo (int)($pkg['nights'] ?? 1); ?> Nights / <?php echo (int)($pkg['days'] ?? 2); ?> Days</strong>
                </div>
                <div class="p-modal-meta-row">
                    <span class="p-modal-meta-label">No. of Travellers:</span>
                    <strong class="p-modal-meta-val" id="modal_summary_travellers"><?php echo max(1, (int)$adults); ?> Travellers</strong>
                </div>
            </div>

            <!-- EaseMyTrip-Style Date Grid -->
            <div class="p-modal-dates-card">
                <div class="p-modal-date-col">
                    <span class="p-modal-date-label">
                        <span class="material-symbols-outlined">calendar_month</span> Start Date
                    </span>
                    <span class="p-modal-date-val" id="modal_summary_start_date">-</span>
                </div>

                <div class="p-modal-nights-pill">
                    <span class="material-symbols-outlined">nightlight</span>
                    <span><?php echo (int)($pkg['nights'] ?? 1); ?>N</span>
                </div>

                <div class="p-modal-date-col p-modal-date-col--right">
                    <span class="p-modal-date-label">
                        End Date <span class="material-symbols-outlined">event</span>
                    </span>
                    <span class="p-modal-date-val" id="modal_summary_end_date">-</span>
                </div>
            </div>
            
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

            <!-- EaseMyTrip-Style Inclusions Strip (Dark Luxury Theme) -->
            <div class="p-modal-amenities-strip">
                <div class="p-modal-amenity-item" title="Hotel Included">
                    <span class="material-symbols-outlined">hotel</span>
                    <span>Stay</span>
                </div>
                <div class="p-modal-amenity-item" title="Sightseeing Included">
                    <span class="material-symbols-outlined">photo_camera</span>
                    <span>Sightseeing</span>
                </div>
                <div class="p-modal-amenity-item" title="Private Transfers Included">
                    <span class="material-symbols-outlined">directions_car</span>
                    <span>Transfers</span>
                </div>
                <div class="p-modal-amenity-item" title="Meals Included">
                    <span class="material-symbols-outlined">restaurant</span>
                    <span>Meals</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

