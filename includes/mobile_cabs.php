<?php
$hero_img = 'assets/img/pkg.jpg';
$today_date = $today_date ?? date('Y-m-d');
$cab_classes = $cab_classes ?? [];
$hero_vehicles = $hero_vehicles ?? [];

// Hero slides from vehicles table (admin/vehicles.php) — data fetched once in cabs.php controller
$hero_slides = [];
foreach ($hero_vehicles as $v) {
    $raw = $v['image'] ?? '';
    if ($raw !== '' && $raw !== null) {
        $src = preg_match('/^https?:\/\//i', $raw) ? $raw : ltrim($raw, '/');
    } else {
        continue;
    }
    $bits = array_filter([
        $v['class_name'] ?? '',
        !empty($v['pax_capacity']) ? ((int)$v['pax_capacity'] . ' Pax') : '',
        ($v['ac_type'] ?? '') !== '' ? strtoupper($v['ac_type']) : '',
    ]);
    $hero_slides[] = [
        'src'  => $src,
        'name' => $v['name'] ?? '',
        'desc' => implode(' · ', $bits),
    ];
}
// Fallback: cab classes, then static image
if (empty($hero_slides)) {
    foreach ($cab_classes as $cc) {
        $raw = $cc['image'] ?? '';
        if ($raw !== '' && $raw !== null) {
            $src = preg_match('/^https?:\/\//i', $raw) ? $raw : ltrim($raw, '/');
        } else {
            $src = $hero_img;
        }
        $hero_slides[] = [
            'src'  => $src,
            'name' => $cc['name'] ?? '',
            'desc' => $cc['description'] ?? '',
        ];
    }
}
if (empty($hero_slides)) {
    $hero_slides[] = ['src' => $hero_img, 'name' => '', 'desc' => ''];
}
$hero_slides = array_slice($hero_slides, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Chauffeur Driven Cars | Leisure Loop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?php echo time(); ?>">
</head>
<body class="m-hotels-body">

    <?php
    $mobile_header_title = 'Cab Search';
    $mobile_active_nav   = 'cabs';
    include __DIR__ . '/mobile_header.php';
    ?>

    <main class="content-area m-cabs-main">

        <!-- Hero (auto-scroll vehicle images from vehicles table) -->
        <section class="m-cabs-hero" id="mCabsHero" aria-label="Featured vehicles">
            <div class="m-cabs-hero-slider" id="mCabsHeroSlider">
                <?php foreach ($hero_slides as $idx => $slide): ?>
                <div class="m-cabs-hero-slide<?php echo $idx === 0 ? ' is-active' : ''; ?>" data-slide-idx="<?php echo (int)$idx; ?>">
                    <img src="<?php echo htmlspecialchars($slide['src']); ?>" class="m-cabs-hero-img" alt="<?php echo $slide['name'] !== '' ? htmlspecialchars($slide['name']) : 'Premium cab'; ?>" loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">
                    <div class="m-cabs-hero-overlay" aria-hidden="true"></div>
                    <div class="m-cabs-hero-content">
                        <?php if ($idx === 0): ?>
                        <h2 class="m-cabs-hero-title"><?php echo htmlspecialchars($slide['name'] !== '' ? $slide['name'] : 'Premium Fleet'); ?></h2>
                        <?php else: ?>
                        <p class="m-cabs-hero-title m-cabs-hero-title--slide"><?php echo htmlspecialchars($slide['name'] !== '' ? $slide['name'] : 'Premium Fleet'); ?></p>
                        <?php endif; ?>
                        <p class="m-cabs-hero-sub"><?php echo htmlspecialchars($slide['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($hero_slides) > 1): ?>
            <div class="m-cabs-hero-dots" id="mCabsHeroDots" aria-hidden="true">
                <?php foreach ($hero_slides as $idx => $slide): ?>
                <span class="m-cabs-hero-dot<?php echo $idx === 0 ? ' is-active' : ''; ?>" data-dot-idx="<?php echo (int)$idx; ?>"></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Search -->
        <section class="m-cabs-search-section">
            <div class="m-cab-search-tabs m-cab-search-tabs--in-card" role="tablist" aria-label="Cab service type">
                <button type="button" class="m-cab-tab" role="tab" aria-selected="true" aria-controls="mCabSearchCard" id="mCabTabOneway" data-type="oneway" tabindex="0">Oneway</button>
                <button type="button" class="m-cab-tab" role="tab" aria-selected="false" aria-controls="mCabSearchCard" id="mCabTabHourly" data-type="hourly" tabindex="-1">Hourly</button>
                <button type="button" class="m-cab-tab" role="tab" aria-selected="false" aria-controls="mCabSearchCard" id="mCabTabItinerary" data-type="itinerary" tabindex="-1">Itinerary</button>
            </div>

            <form action="cab-detail.php" method="GET" class="m-cab-search-card m-hotel-search-card" id="mCabSearchCard" role="tabpanel" aria-labelledby="mCabTabOneway">
                <input type="hidden" name="search" value="1">
                <input type="hidden" name="type" id="mCabSearchType" value="oneway">
                <input type="hidden" name="view" value="mobile">

                <div class="m-search-field-row" id="mCabRowPickup">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">location_on</span>
                    <div class="m-search-field-content">
                        <label for="mCabPickup" class="m-search-kicker">PICKUP LOCATION</label>
                        <input type="text" id="mCabPickup" name="pickup_location" class="m-search-input" placeholder="Enter pickup city or area" autocomplete="off" required>
                    </div>
                </div>

                <div class="m-search-field-row" id="mCabRowDrop">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">location_on</span>
                    <div class="m-search-field-content">
                        <label for="mCabDrop" class="m-search-kicker">DROP LOCATION</label>
                        <input type="text" id="mCabDrop" name="drop_location" class="m-search-input" placeholder="Enter drop city or area" autocomplete="off" required>
                    </div>
                </div>

                <div class="m-search-field-row is-hidden" id="mCabRowItinerary">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">route</span>
                    <div class="m-search-field-content">
                        <label for="mCabItinerary" class="m-search-kicker">ITINERARY / ROUTE DETAILS</label>
                        <input type="text" id="mCabItinerary" name="itinerary_details" class="m-search-input" placeholder="e.g. Delhi → Agra → Jaipur → Delhi" disabled>
                    </div>
                </div>

                <div class="m-cab-dates-row">
                    <div class="m-search-field-row m-cab-date-field" id="mCabRowTravelDate">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">calendar_month</span>
                        <div class="m-search-field-content">
                            <label for="mCabTravelDate" class="m-search-kicker">PICKUP DATE</label>
                            <input type="date" id="mCabTravelDate" name="travel_date" class="m-search-input" min="<?php echo htmlspecialchars($today_date); ?>" required>
                        </div>
                    </div>

                    <div class="m-search-field-row m-cab-date-field is-hidden" id="mCabRowReturnDate">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">calendar_month</span>
                        <div class="m-search-field-content">
                            <label for="mCabReturnDate" class="m-search-kicker">RETURN DATE</label>
                            <input type="date" id="mCabReturnDate" name="return_date" class="m-search-input" min="<?php echo htmlspecialchars($today_date); ?>" disabled>
                        </div>
                    </div>

                    <div class="m-search-field-row m-cab-date-field" id="mCabRowTravelTime">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">schedule</span>
                        <div class="m-search-field-content">
                            <label for="mCabTravelTime" class="m-search-kicker">TIME</label>
                            <input type="time" id="mCabTravelTime" name="travel_time" class="m-search-input">
                        </div>
                    </div>
                </div>

                <div class="m-search-field-row is-hidden" id="mCabRowDuration">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">hourglass_top</span>
                    <div class="m-search-field-content">
                        <label for="mCabDuration" class="m-search-kicker">RENT FOR</label>
                        <select id="mCabDuration" name="duration" class="m-search-input" disabled>
                            <option value="">Select duration</option>
                            <?php if (!empty($cab_durations)): foreach ($cab_durations as $cd): ?>
                            <option value="<?php echo htmlspecialchars((string)$cd['hours']); ?>"><?php echo htmlspecialchars($cd['label']); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary m-hotel-search-btn">SEARCH CABS</button>
            </form>
        </section>

        <!-- Flexible Hourly / Outstation (desktop parity) -->
        <section class="m-cabs-rentals" aria-labelledby="mCabsRentalsTitle">
            <div class="m-cabs-rentals-head">
                <h2 class="section-title m-cabs-rentals-title" id="mCabsRentalsTitle">Flexible Hourly Car Rentals &amp; Outstation Cabs</h2>
                <p class="m-cabs-rentals-sub">Rent a car for convenient and affordable travel across major destinations. Enjoy the freedom to explore at your own pace.</p>
            </div>

            <?php if (!empty($cab_classes)): ?>
            <div class="m-cabs-rentals-grid">
                <?php foreach ($cab_classes as $cc): ?>
                <a href="cab-detail.php?id=<?php echo (int)$cc['id']; ?>" class="m-cabs-rentals-card">
                    <?php
                    $img_path = !empty($cc['image'])
                        ? (preg_match('/^https?:\/\//i', $cc['image']) ? $cc['image'] : ltrim($cc['image'], '/'))
                        : 'assets/img/pkg.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($cc['name']); ?>" class="m-cabs-rentals-img" loading="lazy">
                    <div class="m-cabs-rentals-info">
                        <h3 class="m-cabs-rentals-name"><?php echo htmlspecialchars($cc['name']); ?></h3>
                        <p class="m-cabs-rentals-desc"><?php echo htmlspecialchars($cc['description'] ?? ''); ?></p>
                        <div class="m-cabs-rentals-price">
                            <?php if ($cc['min_rate'] !== null): ?>
                            <span class="m-cabs-rentals-amount">&#8377;<?php echo number_format($cc['min_rate']); ?></span>
                            <span class="m-cabs-rentals-unit">/ day onwards</span>
                            <?php else: ?>
                            <span class="m-cabs-rentals-unit">Contact for pricing</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="m-cabs-rentals-empty">No cab classes available at the moment.</p>
            <?php endif; ?>
        </section>

        <!-- Popular Cab Routes (desktop parity) -->
        <section class="m-cabs-routes" aria-labelledby="mCabsRoutesTitle">
            <div class="m-cabs-routes-head">
                <h2 class="section-title m-cabs-routes-title" id="mCabsRoutesTitle">Popular Cab Routes</h2>
                <p class="m-cabs-routes-sub">Book reliable cab services across top destinations.</p>
            </div>
            <div class="m-cabs-routes-grid">
                <a href="cabs.php?view=mobile" class="m-cabs-routes-card" aria-label="Search cabs Bagdogra to Darjeeling">
                    <img src="assets/img/pkg.jpg" alt="Bagdogra to Darjeeling Route" class="m-cabs-routes-img" loading="lazy">
                    <div class="m-cabs-routes-overlay">
                        <h3 class="m-cabs-routes-name">Bagdogra &rarr; Darjeeling</h3>
                    </div>
                </a>
                <a href="cabs.php?view=mobile" class="m-cabs-routes-card" aria-label="Search cabs NJP to Gangtok">
                    <img src="assets/img/pkg.jpg" alt="NJP to Gangtok Route" class="m-cabs-routes-img" loading="lazy">
                    <div class="m-cabs-routes-overlay">
                        <h3 class="m-cabs-routes-name">NJP &rarr; Gangtok</h3>
                    </div>
                </a>
                <a href="cabs.php?view=mobile" class="m-cabs-routes-card" aria-label="Search cabs Darjeeling to Pelling">
                    <img src="assets/img/pkg.jpg" alt="Darjeeling to Pelling Route" class="m-cabs-routes-img" loading="lazy">
                    <div class="m-cabs-routes-overlay">
                        <h3 class="m-cabs-routes-name">Darjeeling &rarr; Pelling</h3>
                    </div>
                </a>
                <a href="cabs.php?view=mobile" class="m-cabs-routes-card" aria-label="Search cabs Gangtok to Kalimpong">
                    <img src="assets/img/pkg.jpg" alt="Gangtok to Kalimpong Route" class="m-cabs-routes-img" loading="lazy">
                    <div class="m-cabs-routes-overlay">
                        <h3 class="m-cabs-routes-name">Gangtok &rarr; Kalimpong</h3>
                    </div>
                </a>
            </div>
        </section>

    </main>

    <?php
    $extra_scripts = '<script src="js/modules/cabs.js?v=<?php echo time(); ?>" defer></script>';
    include __DIR__ . '/mobile_footer.php';
    ?>
</body>
</html>
