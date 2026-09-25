<?php
// Requires $signature, $luxury, $search_query, and dates from hotels.php
$all_hotels = array_merge($signature ?? [], $luxury ?? []);

$check_in_ts = strtotime($check_in ?? date('Y-m-d'));
$check_out_ts = strtotime($check_out ?? date('Y-m-d', strtotime('+1 day')));

$query_params = [
    'check_in'  => $check_in,
    'check_out' => $check_out,
    'rooms'     => $rooms,
    'adults'    => $adults,
    'children'  => $children,
    'infants'   => $infants,
    'view'      => 'mobile'
];
$detail_query_string = http_build_query($query_params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo htmlspecialchars($page_title ?? 'Luxury Hotels | Leisure Loop'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mobile-views.css?v=<?php echo time(); ?>">
</head>
<body class="m-hotels-body">

    <?php
    $mobile_header_title = 'Hotels & Homestays';
    $mobile_active_nav   = 'hotels';
    include __DIR__ . '/mobile_header.php';
    ?>

    <!-- Dynamic Hero Carousel -->
    <?php
    $hero_hotels = array_slice($all_hotels, 0, 4);
    if (!empty($hero_hotels)):
    ?>
    <section class="m-hotel-hero" id="mHotelHero">
        <div class="m-hotel-hero-slider">
            <?php foreach ($hero_hotels as $idx => $h): 
                $h_img_raw = $h['main_image'] ?? '';
                $h_img = preg_match('/^https?:\/\//i', $h_img_raw) 
                    ? $h_img_raw 
                    : 'admin/' . ltrim(str_replace('../', '', $h_img_raw), '/');
                if (empty($h_img_raw)) $h_img = 'assets/img/pkg.jpg';
            ?>
            <div class="m-hotel-hero-slide <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-slide-idx="<?php echo $idx; ?>">
                <img src="<?php echo htmlspecialchars($h_img); ?>" alt="<?php echo htmlspecialchars($h['name']); ?>" class="m-hotel-hero-img" loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                <div class="m-hotel-hero-overlay"></div>
                <div class="m-hotel-hero-content">
                    <span class="m-hotel-hero-kicker">CURATED LUXURY RETREATS</span>
                    <h2 class="m-hotel-hero-title"><?php echo htmlspecialchars($h['name']); ?></h2>
                    <p class="m-hotel-hero-place">
                        <span class="material-symbols-outlined">location_on</span>
                        <?php echo htmlspecialchars($h['place']); ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="m-hotel-hero-dots" id="mHotelHeroDots">
            <?php foreach ($hero_hotels as $idx => $h): ?>
            <span class="m-hotel-hero-dot <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-dot-idx="<?php echo $idx; ?>"></span>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- MakeMyTrip / EaseMyTrip Style Search Card -->
    <section class="m-hotel-search-section">
        <form method="GET" action="hotels.php" id="mHotelSearchForm" class="m-hotel-search-card">
            <input type="hidden" name="view" value="mobile">
            <input type="hidden" name="rooms" id="mInputRooms" value="<?php echo (int)$rooms; ?>">
            <input type="hidden" name="adults" id="mInputAdults" value="<?php echo (int)$adults; ?>">
            <input type="hidden" name="children" id="mInputChildren" value="<?php echo (int)$children; ?>">

            <!-- Field 1: City / Property -->
            <div class="m-search-field-row">
                <span class="material-symbols-outlined m-search-icon">location_on</span>
                <div class="m-search-field-content">
                    <label for="mSearchCity" class="m-search-kicker">CITY, LOCATION OR PROPERTY</label>
                    <input type="text" name="q" id="mSearchCity" class="m-search-input" placeholder="" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </div>

            <!-- Field 2: Dates Grid (EaseMyTrip / MMT) -->
            <div class="m-search-dates-grid" id="mDateGridTrigger">
                <div class="m-search-date-col">
                    <label class="m-search-kicker">CHECK-IN</label>
                    <div class="m-search-date-display">
                        <span class="m-date-day" id="mDisplayCheckInDay"><?php echo date('jS M', $check_in_ts); ?></span>
                        <span class="m-date-sub" id="mDisplayCheckInYear">'<?php echo date('y, D', $check_in_ts); ?></span>
                    </div>
                    <input type="hidden" name="check_in" id="mCheckInDate" value="<?php echo htmlspecialchars($check_in); ?>">
                </div>

                <div class="m-search-nights-pill">
                    <span class="material-symbols-outlined">nightlight</span>
                    <span id="mDisplayNightsPill"><?php echo max(1, (int)round(($check_out_ts - $check_in_ts) / 86400)); ?>N</span>
                </div>

                <div class="m-search-date-col m-search-date-col--right">
                    <label class="m-search-kicker">CHECK-OUT</label>
                    <div class="m-search-date-display">
                        <span class="m-date-day" id="mDisplayCheckOutDay"><?php echo date('jS M', $check_out_ts); ?></span>
                        <span class="m-date-sub" id="mDisplayCheckOutYear">'<?php echo date('y, D', $check_out_ts); ?></span>
                    </div>
                    <input type="hidden" name="check_out" id="mCheckOutDate" value="<?php echo htmlspecialchars($check_out); ?>">
                </div>
            </div>

            <!-- Field 3: Rooms & Guests Row -->
            <div class="m-search-field-row m-search-field-row--clickable" data-action="open-guest-modal">
                <span class="material-symbols-outlined m-search-icon">group</span>
                <div class="m-search-field-content">
                    <span class="m-search-kicker">ROOMS &amp; GUESTS</span>
                    <div class="m-search-val" id="mDisplayGuestsSummary">
                        <?php echo (int)$rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?>, <?php echo (int)$adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', ' . (int)$children . ' Child' : ''; ?>
                    </div>
                </div>
                <span class="material-symbols-outlined m-chevron-icon">chevron_right</span>
            </div>

            <button type="submit" class="btn-primary m-hotel-search-btn">SEARCH HOTELS</button>
        </form>
    </section>

    <!-- Category Filter Tabs -->
    <div class="m-hotels-filter-wrap">
        <div class="m-hotels-filters">
            <button type="button" class="m-hotel-filter-btn active" data-action="filter-hotels" data-category="all">
                All Properties (<?php echo count($all_hotels); ?>)
            </button>
            <button type="button" class="m-hotel-filter-btn" data-action="filter-hotels" data-category="signature">
                Signature (<?php echo count($signature ?? []); ?>)
            </button>
            <button type="button" class="m-hotel-filter-btn" data-action="filter-hotels" data-category="luxury">
                Partner Brands (<?php echo count($luxury ?? []); ?>)
            </button>
        </div>
    </div>

    <!-- Hotel Listing Cards Container -->
    <main class="m-hotels-list" id="mHotelsList">
        <?php foreach ($all_hotels as $hotel): 
            $raw_img = $hotel['main_image'] ?? '';
            $main_img = preg_match('/^https?:\/\//i', $raw_img) 
                ? $raw_img 
                : 'admin/' . ltrim(str_replace('../', '', $raw_img), '/');
            
            if (empty($raw_img)) {
                $main_img = 'assets/img/pkg.jpg';
            }

            $tariff = !empty($hotel['dynamic_starting_tariff']) && $hotel['dynamic_starting_tariff'] > 0 
                ? (float)$hotel['dynamic_starting_tariff'] 
                : (float)($hotel['starting_tariff'] ?? 0);

            $amenities = !empty($hotel['amenities']) ? array_slice(array_map('trim', explode(',', $hotel['amenities'])), 0, 3) : [];
        ?>
        <article class="m-hotel-card" data-category="<?php echo htmlspecialchars($hotel['type'] ?? 'luxury'); ?>">
            <a href="hotel-detail.php?id=<?php echo (int)$hotel['id']; ?>&<?php echo $detail_query_string; ?>" class="m-hotel-card-link">
                <div class="m-hotel-img-wrapper">
                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="m-hotel-img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-hotel-overlay"></div>
                    
                    <!-- Top Badges -->
                    <div class="m-hotel-badges-top">
                        <span class="m-hotel-star-badge"><?php echo str_repeat('★', (int)($hotel['star_category'] ?? 4)); ?></span>
                        <?php if (!empty($hotel['google_rating'])): ?>
                        <span class="m-hotel-rating-badge">
                            <span class="material-symbols-outlined star-icon">star</span>
                            <span><?php echo number_format((float)$hotel['google_rating'], 1); ?></span>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Bottom Category Pill -->
                    <div class="m-hotel-tag-badge">
                        <?php echo ($hotel['type'] ?? '') === 'signature' ? 'SIGNATURE PROPERTY' : 'PARTNER RETREAT'; ?>
                    </div>
                </div>

                <div class="m-hotel-body">
                    <h2 class="m-hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                    
                    <div class="m-hotel-location">
                        <span class="material-symbols-outlined">location_on</span>
                        <span><?php echo htmlspecialchars($hotel['place']); ?></span>
                    </div>

                    <?php if (!empty($amenities)): ?>
                    <div class="m-hotel-amenities">
                        <?php foreach ($amenities as $am): ?>
                        <span class="m-hotel-amenity-chip"><?php echo htmlspecialchars($am); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="m-hotel-footer">
                        <div class="m-hotel-pricing">
                            <span class="m-hotel-price-label">Starting from</span>
                            <div class="m-hotel-price-val">
                                ₹<?php echo number_format($tariff); ?>
                                <span class="m-hotel-price-unit">/ night</span>
                            </div>
                        </div>
                        <span class="m-hotel-cta-btn">View Rooms</span>
                    </div>
                </div>
            </a>
        </article>
        <?php endforeach; ?>
    </main>

    <!-- MakeMyTrip Style Rooms & Guests Bottom Sheet -->
    <div class="modal-overlay" id="mHotelGuestModalOverlay" data-action="close-guest-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content m-guest-sheet" id="mHotelGuestModal">
        <div class="modal-header m-guest-sheet-header">
            <div>
                <span class="m-modal-kicker">TRAVELLERS &amp; ROOMS</span>
                <h3 class="m-guest-sheet-title">Select Guests</h3>
            </div>
            <button type="button" class="modal-close" data-action="close-guest-modal" aria-label="Close modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="m-guest-sheet-body">
            <!-- Row 1: Rooms -->
            <div class="m-counter-row">
                <div>
                    <div class="m-counter-title">Rooms</div>
                    <div class="m-counter-sub">Minimum 1 Room</div>
                </div>
                <div class="m-counter-controls">
                    <button type="button" class="m-btn-counter" id="mBtnRoomsMinus" data-action="dec-rooms">-</button>
                    <span class="m-counter-val" id="mValRooms"><?php echo (int)$rooms; ?></span>
                    <button type="button" class="m-btn-counter" id="mBtnRoomsPlus" data-action="inc-rooms">+</button>
                </div>
            </div>

            <!-- Row 2: Adults -->
            <div class="m-counter-row">
                <div>
                    <div class="m-counter-title">Adults</div>
                    <div class="m-counter-sub">Aged 12+ years</div>
                </div>
                <div class="m-counter-controls">
                    <button type="button" class="m-btn-counter" id="mBtnAdultsMinus" data-action="dec-adults">-</button>
                    <span class="m-counter-val" id="mValAdults"><?php echo (int)$adults; ?></span>
                    <button type="button" class="m-btn-counter" id="mBtnAdultsPlus" data-action="inc-adults">+</button>
                </div>
            </div>

            <!-- Row 3: Children -->
            <div class="m-counter-row">
                <div>
                    <div class="m-counter-title">Children</div>
                    <div class="m-counter-sub">Aged 0-11 years</div>
                </div>
                <div class="m-counter-controls">
                    <button type="button" class="m-btn-counter" id="mBtnChildrenMinus" data-action="dec-children">-</button>
                    <span class="m-counter-val" id="mValChildren"><?php echo (int)$children; ?></span>
                    <button type="button" class="m-btn-counter" id="mBtnChildrenPlus" data-action="inc-children">+</button>
                </div>
            </div>

            <button type="button" class="btn-primary m-guest-apply-btn" data-action="apply-guests-modal">DONE</button>
        </div>
    </div>

<?php
// We load the extra scripts via mobile_footer.php or directly.
$extra_scripts = '<script src="js/modules/mobile-datepicker.js?v=' . time() . '" defer></script>';
?>

    <!-- Mobile Date Picker Bottom Sheet -->
    <div id="mDatePickerSheet" class="m-datepicker-overlay is-hidden" aria-hidden="true">
        <div class="m-datepicker-sheet">
            <div class="m-datepicker-header">
                <div class="m-datepicker-header-row">
                    <div class="m-datepicker-header-col">
                        <span class="m-datepicker-kicker">Check-In Date</span>
                        <span class="m-datepicker-val" id="mDpCheckInVal">Select Date</span>
                    </div>
                    <div class="m-datepicker-header-col m-datepicker-header-col--right">
                        <span class="m-datepicker-kicker">Check-Out Date</span>
                        <span class="m-datepicker-val" id="mDpCheckOutVal">Select Date</span>
                    </div>
                    <button type="button" class="m-datepicker-close" id="mDpCloseBtn" aria-label="Close Date Picker">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="m-datepicker-weekdays">
                    <span>SUN</span><span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span>
                </div>
            </div>
            
            <div class="m-datepicker-body" id="mDpCalendarBody">
                <!-- JS Calendar Months Injected Here -->
            </div>

            <div class="m-datepicker-footer">
                <button type="button" class="m-datepicker-done-btn" id="mDpDoneBtn">Done</button>
            </div>
        </div>
    </div>

    <?php include 'mobile_footer.php'; ?>
</body>
</html>
