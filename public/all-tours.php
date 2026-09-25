<?php
    declare(strict_types=1);

require_once '../config/db.php';
require_once '../includes/functions.php';
csrf_stamp_form();

function catalogCsvParam(string $key): array
    {
        $raw = isset($_GET[$key]) ? trim((string)$_GET[$key]) : '';
        if ($raw === '') {
            return [];
        }

        $values = array_map('trim', explode(',', $raw));
        $values = array_filter($values, static fn(string $value): bool => $value !== '' && strlen($value) <= 100);

        return array_values(array_unique($values));
    }

    function catalogKey(string $value): string
    {
        return strtolower(trim($value));
    }

    function packageThemeKeys(array $package): array
    {
        if (empty($package['tour_type'])) {
            return [];
        }

        $themes = array_map('catalogKey', explode(',', (string)$package['tour_type']));
        $themes = array_filter($themes, static fn(string $theme): bool => $theme !== '');

        return array_values(array_unique($themes));
    }

    function packageDurationKey(array $package): string
    {
        return (int)($package['nights'] ?? 0) . '-' . (int)($package['days'] ?? 0);
    }

    $active_tab = (isset($_GET['package_type']) && $_GET['package_type'] === 'fixed') ? 'fixed' : 'curated';
    $type_filter_val = strtolower(trim((string)($_GET['type'] ?? '')));
    $keyword_filter = trim((string)($_GET['q'] ?? ''));
    $theme_filter = trim((string)($_GET['theme'] ?? ''));
    $dest_filter = trim((string)($_GET['destination'] ?? ''));
    $duration_filter = trim((string)($_GET['dur'] ?? ''));
    $selected_themes = catalogCsvParam('themes');
    $selected_destinations = catalogCsvParam('dest');
    $selected_durations = catalogCsvParam('durations');
    $allowed_sorts = ['default', 'price_asc', 'price_desc', 'duration_asc', 'duration_desc'];
    $requested_sort = (string)($_GET['sort'] ?? 'default');
    $sort_filter = in_array($requested_sort, $allowed_sorts, true) ? $requested_sort : 'default';
    $price_min = (isset($_GET['price_min']) && is_numeric($_GET['price_min'])) ? (float)$_GET['price_min'] : null;
    $price_max = (isset($_GET['price_max']) && is_numeric($_GET['price_max'])) ? (float)$_GET['price_max'] : null;

    if ($theme_filter !== '') {
        $selected_themes[] = $theme_filter;
    }
    if ($dest_filter !== '') {
        $selected_destinations[] = $dest_filter;
    }
    if ($duration_filter !== '') {
        $selected_durations[] = $duration_filter;
    }

    $selected_themes = array_values(array_unique($selected_themes));
    $selected_destinations = array_values(array_unique($selected_destinations));
    $selected_durations = array_values(array_unique($selected_durations));
    $selected_theme_keys = array_map('catalogKey', $selected_themes);
    $selected_destination_keys = array_map('catalogKey', $selected_destinations);
    $selected_duration_keys = array_map('catalogKey', $selected_durations);
    $budget_tier = 'all';

    if ($price_min === null && $price_max === 25000.0) {
        $budget_tier = 'tier1';
    } elseif ($price_min === 25000.0 && $price_max === 50000.0) {
        $budget_tier = 'tier2';
    } elseif ($price_min === 50000.0 && $price_max === null) {
        $budget_tier = 'tier3';
    }

    $page_title = "Curated Experiences | Leisure Loop Trip";
    if (!empty($theme_filter)) {
        $page_title = htmlspecialchars($theme_filter) . " Escapes | Leisure Loop Trip";
    } elseif (!empty($dest_filter)) {
        $page_title = htmlspecialchars($dest_filter) . " Signature Tours | Leisure Loop Trip";
    }

    // ── Fetch active packages and build dynamic filter arrays ─────────────────
    $packages = [];
    $catalog_packages = [];

    if (isset($pdo) && $pdo) {
        $query = "SELECT * FROM packages WHERE is_active = :is_active AND package_type = :package_type";
        $params = [
            ':is_active' => 1,
            ':package_type' => $active_tab,
        ];

        if ($type_filter_val === 'domestic') {
            $query .= " AND is_international = :is_international";
            $params[':is_international'] = 0;
        } elseif ($type_filter_val === 'international') {
            $query .= " AND is_international = :is_international";
            $params[':is_international'] = 1;
        }

        $query .= " ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $catalog_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $all_themes = [];
    if (isset($pdo) && $pdo) {
        try {
            $catStmt = $pdo->query("SELECT name FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC");
            while ($catRow = $catStmt->fetch(PDO::FETCH_ASSOC)) {
                $catName = trim($catRow['name'] ?? '');
                if ($catName !== '' && !in_array($catName, $all_themes, true)) {
                    $all_themes[] = $catName;
                }
            }
        } catch (PDOException $e) {}
    }

    $all_destinations = [];
    $all_durations = [];

    foreach ($catalog_packages as $pkg) {
        if (empty($all_themes) && !empty($pkg['tour_type'])) {
            foreach (explode(',', $pkg['tour_type']) as $t) {
                $t = trim($t);
                if ($t !== '' && !in_array($t, $all_themes, true)) {
                    $all_themes[] = $t;
                }
            }
        }
        if (!empty($pkg['destination'])) {
            $d = trim($pkg['destination']);
            if (!in_array($d, $all_destinations, true)) {
                $all_destinations[] = $d;
            }
        }
        $nights = (int)($pkg['nights'] ?? 0);
        $days   = (int)($pkg['days']   ?? 0);
        if ($days > 0 || $nights > 0) {
            $key = $nights . '-' . $days;
            if (!isset($all_durations[$key])) {
                $all_durations[$key] = [
                    'label'  => sprintf('%02d Nights / %02d Days', $nights, $days),
                    'nights' => $nights,
                    'days'   => $days,
                ];
            }
        }
    }

    foreach ($selected_destinations as $selected_destination) {
        $found = false;
        foreach ($all_destinations as $d) {
            if (strcasecmp($d, $selected_destination) === 0) { $found = true; break; }
        }
        if (!$found) { $all_destinations[] = $selected_destination; }
    }
    foreach ($selected_themes as $selected_theme) {
        $found = false;
        foreach ($all_themes as $t) {
            if (strcasecmp($t, $selected_theme) === 0) { $found = true; break; }
        }
        if (!$found) { $all_themes[] = $selected_theme; }
    }

    if ($duration_filter !== '' && !isset($all_durations[$duration_filter])) {
        $duration_parts = array_map('intval', explode('-', $duration_filter));
        $all_durations[$duration_filter] = [
            'label' => sprintf('%02d Nights / %02d Days', $duration_parts[0] ?? 0, $duration_parts[1] ?? 0),
            'nights' => $duration_parts[0] ?? 0,
            'days' => $duration_parts[1] ?? 0,
        ];
    }

    $packages = array_values(array_filter($catalog_packages, static function (array $pkg) use (
        $keyword_filter,
        $selected_theme_keys,
        $selected_destination_keys,
        $selected_duration_keys,
        $price_min,
        $price_max
    ): bool {
        if ($keyword_filter !== '') {
            $search_text = strtolower(implode(' ', [
                (string)($pkg['title'] ?? ''),
                (string)($pkg['destination'] ?? ''),
                (string)($pkg['tour_type'] ?? ''),
            ]));
            $kw = strtolower($keyword_filter);

            $sd_circuit = [
                'sikkim', 'gangtok', 'darjeeling', 'kalimpong', 
                'pelling', 'lachen', 'lachung', 'namchi', 
                'ravangla', 'siliguri', 'kurseong', 'mirik', 'njp', 'bagdogra'
            ];

            $is_sd_search = false;
            foreach ($sd_circuit as $place) {
                if (strpos($kw, $place) !== false) {
                    $is_sd_search = true;
                    break;
                }
            }

            if ($is_sd_search) {
                $pkg_in_circuit = false;
                foreach ($sd_circuit as $place) {
                    if (strpos($search_text, $place) !== false) {
                        $pkg_in_circuit = true;
                        break;
                    }
                }
                if (!$pkg_in_circuit) {
                    return false;
                }
            } else {
                if (strpos($search_text, $kw) === false) {
                    return false;
                }
            }
        }

        if (!empty($selected_theme_keys) && empty(array_intersect($selected_theme_keys, packageThemeKeys($pkg)))) {
            return false;
        }

        if (!empty($selected_destination_keys) && !in_array(catalogKey((string)($pkg['destination'] ?? '')), $selected_destination_keys, true)) {
            return false;
        }

        if (!empty($selected_duration_keys) && !in_array(packageDurationKey($pkg), $selected_duration_keys, true)) {
            return false;
        }

        $price = (float)($pkg['price'] ?? 0);
        if ($price_min !== null && $price < $price_min) {
            return false;
        }
        if ($price_max !== null && $price > $price_max) {
            return false;
        }

        return true;
    }));

    usort($packages, static function (array $a, array $b) use ($sort_filter): int {
        return match ($sort_filter) {
            'price_asc' => (float)($a['price'] ?? 0) <=> (float)($b['price'] ?? 0),
            'price_desc' => (float)($b['price'] ?? 0) <=> (float)($a['price'] ?? 0),
            'duration_asc' => [(int)($a['days'] ?? 0), (int)($a['nights'] ?? 0)] <=> [(int)($b['days'] ?? 0), (int)($b['nights'] ?? 0)],
            'duration_desc' => [(int)($b['days'] ?? 0), (int)($b['nights'] ?? 0)] <=> [(int)($a['days'] ?? 0), (int)($a['nights'] ?? 0)],
            default => (int)strtotime((string)($b['created_at'] ?? '')) <=> (int)strtotime((string)($a['created_at'] ?? '')),
        };
    });

    if ($theme_filter === '' && !empty($selected_themes)) {
        $theme_filter = $selected_themes[0];
    }
    if ($dest_filter === '' && !empty($selected_destinations)) {
        $dest_filter = $selected_destinations[0];
    }

    if (!empty($dest_filter)) {
        $found = false;
        foreach ($all_destinations as $d) {
            if (strcasecmp($d, $dest_filter) === 0) { $found = true; break; }
        }
        if (!$found) { $all_destinations[] = $dest_filter; }
    }

    sort($all_themes);
    sort($all_destinations);
    uasort($all_durations, fn($a, $b) => $a['nights'] <=> $b['nights']);

    $package_count = count($packages);

    // ── Mobile Device Routing Check ───────────────────────────────────────────
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $explicit_mobile = isset($_GET['view']) && $_GET['view'] === 'mobile';
    $auto_mobile = (bool) preg_match(
        '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',
        $useragent
    );

    if ($explicit_mobile || $auto_mobile) {
        include '../includes/mobile_all_tours.php';
        exit;
    }

    // ── Desktop Layout Rendering ──────────────────────────────────────────────
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/all-tours.css">

    <!-- ── 1. Search Bar ──────────────────────────────────────────────────── -->
    <section class="search-dock-section">
        <div class="search-bar-container">
            <div class="search-bar">

                <div class="input-with-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                    <label class="sr-only" for="search_keyword_input">Search destinations or tours</label>
                    <input
                        type="text"
                        id="search_keyword_input"
                        placeholder="Search destinations or tours..."
                        value="<?php 
                            if (isset($_GET['q']) && trim($_GET['q']) !== '') {
                                echo htmlspecialchars(trim($_GET['q']));
                            }
                        ?>">
                </div>

                <div class="input-with-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">explore</span>
                    <label class="sr-only" for="search_theme_select">Holiday theme</label>
                    <select id="search_theme_select" data-change="apply-filters">
                        <option value="">All Holiday Themes</option>
                        <?php foreach ($all_themes as $t_item): ?>
                            <option
                                value="<?php echo htmlspecialchars(strtolower($t_item)); ?>"
                                <?php echo (strtolower($theme_filter) === strtolower($t_item)) ? 'selected' : ''; ?>
                            ><?php echo htmlspecialchars($t_item); ?> Tours</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-with-icon">
                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                    <label class="sr-only" for="search_dur_select">Trip duration</label>
                    <select id="search_dur_select" data-change="apply-filters">
                        <option value="">Any Duration</option>
                        <?php foreach ($all_durations as $key => $dur_info): ?>
                            <option
                                value="<?php echo htmlspecialchars($key); ?>"
                                <?php echo ($duration_filter !== '' && catalogKey((string)$key) === catalogKey($duration_filter)) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($dur_info['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-search" data-action="apply-search" aria-label="Search holiday packages">SEARCH HOLIDAYS</button>
            </div>
        </div>
    </section>

    <!-- ── 2. Breadcrumb & Results Bar ───────────────────────────────────── -->
    <div class="catalog-nav-bar">
        <nav class="catalog-breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span aria-hidden="true">&gt;</span>
            <a href="all-tours.php">Holiday Tours</a>
            <span aria-hidden="true">&gt;</span>
            <span class="catalog-breadcrumb-current" aria-current="page">
                <?php echo !empty($theme_filter) ? htmlspecialchars($theme_filter) . ' Tours' : 'All Collections'; ?>
            </span>
        </nav>
        <span id="resultsCountBadge" class="results-count-pill">
            <?php echo $package_count; ?> Tour Packages Available
        </span>
    </div>

    <!-- ── 3. Curated / Fixed Departures Switcher ────────────────────────── -->
    <div class="package-switcher-container">
        <?php $type_param = isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?>
        <div class="package-switcher-pill" role="tablist">
            <a
                href="?package_type=curated<?php echo $type_param; ?>"
                class="package-switcher-tab <?php echo $active_tab === 'curated' ? 'active' : 'inactive'; ?>"
                role="tab"
                aria-selected="<?php echo $active_tab === 'curated' ? 'true' : 'false'; ?>"
            >
                <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                Curated Experiences
            </a>
            <a
                href="?package_type=fixed<?php echo $type_param; ?>"
                class="package-switcher-tab <?php echo $active_tab === 'fixed' ? 'active' : 'inactive'; ?>"
                role="tab"
                aria-selected="<?php echo $active_tab === 'fixed' ? 'true' : 'false'; ?>"
            >
                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                Fixed Departures
            </a>
        </div>
    </div>

    <!-- ── 4. Full-Width Package Grid ────────────────────────────────────── -->
    <section class="catalog-section" id="catalog-main-anchor">
        <div class="packages-catalog-layout">
            <main class="packages-grid-display" id="packagesGridDisplay">
                <div id="packages-grid-inner" class="packages-grid-inner">
                    <?php foreach ($packages as $pkg):
                        $nights     = (int)($pkg['nights'] ?? 0);
                        $days       = (int)($pkg['days']   ?? 0);
                        $price      = (float)$pkg['price'];
                        $origPrice  = !empty($pkg['original_price'])
                            ? (float)$pkg['original_price']
                            : ($price > 0 ? $price * 1.15 : null);

                        $pkg_themes = [];
                        if (!empty($pkg['tour_type'])) {
                            foreach (explode(',', $pkg['tour_type']) as $t) {
                                $pkg_themes[] = strtolower(trim($t));
                            }
                        }
                        $themes_data  = implode('|', $pkg_themes);
                        $duration_key = $nights . '-' . $days;
                    ?>
                    <div class="js-catalog-card"
                         data-id="<?php echo (int)$pkg['id']; ?>"
                         data-title="<?php echo htmlspecialchars(strtolower($pkg['title'])); ?>"
                         data-price="<?php echo $price; ?>"
                         data-destination="<?php echo htmlspecialchars(strtolower(trim($pkg['destination']))); ?>"
                         data-themes="<?php echo htmlspecialchars($themes_data); ?>"
                         data-duration="<?php echo htmlspecialchars($duration_key); ?>"
                         data-days="<?php echo $days; ?>"
                         data-nights="<?php echo $nights; ?>"
                         data-created="<?php echo (int)strtotime($pkg['created_at']); ?>">

                        <a href="package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" class="ota-package-card">
                            <div class="card-media-shell">
                                <img
                                    src="<?php echo !empty($pkg['image_url']) ? htmlspecialchars($pkg['image_url']) : 'assets/img/pkg.jpg'; ?>"
                                    onerror="this.onerror=null; this.src='assets/img/pkg.jpg';"
                                    alt="<?php echo htmlspecialchars($pkg['title']); ?>"
                                    class="card-cover-img"
                                    loading="lazy"
                                >
                                <span class="card-badge-gold">✨ SIGNATURE TOUR</span>
                                <span class="card-badge-dark"><?php echo sprintf('%02d N / %02d D', $nights, $days); ?></span>
                            </div>

                            <div class="card-body-content">
                                <div>
                                    <div class="card-dest-label"><?php echo htmlspecialchars($pkg['destination']); ?> ESCAPES</div>
                                    <h3 class="card-tour-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>

                                    <div class="card-amenities-strip">
                                        <span class="amenity-item"><i class="material-symbols-outlined" aria-hidden="true">hotel</i> Luxury Stays</span>
                                        <span class="amenity-item"><i class="material-symbols-outlined" aria-hidden="true">directions_car</i> Private Cabs</span>
                                        <span class="amenity-item"><i class="material-symbols-outlined" aria-hidden="true">restaurant</i> Gourmet Meals</span>
                                        <span class="amenity-item"><i class="material-symbols-outlined" aria-hidden="true">photo_camera</i> VIP Tours</span>
                                    </div>
                                </div>

                                <div class="card-footer-strip">
                                    <div class="pricing-block">
                                        <span class="start-lbl">Starting from</span>
                                        <div class="amount">
                                            <?php if ($origPrice && $origPrice > $price): ?>
                                                <span class="orig-price">&#8377;<?php echo number_format($origPrice); ?></span>
                                            <?php endif; ?>
                                            &#8377;<?php echo number_format($price); ?> <span class="per-pax">/ person</span>
                                        </div>
                                    </div>
                                    <div class="btn-card-explore">
                                        VIEW TOUR
                                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State -->
                <div id="no-packages-placeholder" class="catalog-empty-state" <?php echo empty($packages) ? '' : 'hidden'; ?> aria-live="polite">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <p class="empty-state-title">No collections match your current filters.</p>
                    <p class="empty-state-sub">Try clearing your filters or reach out to our concierge to craft a bespoke itinerary.</p>
                    <div class="empty-state-actions">
                        <button class="btn-reset-empty" data-action="reset-filters">
                            <span class="material-symbols-outlined" aria-hidden="true">restart_alt</span>
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </section>

    <!-- ── 5. Trust Guarantee Strip ──────────────────────────────────────── -->
    <section class="trust-guarantee-section">
        <div class="trust-grid">
            <div class="trust-card">
                <div class="trust-icon-circle" aria-hidden="true">
                    <span class="material-symbols-outlined">verified_user</span>
                </div>
                <h4 class="trust-card-title">100% Guaranteed Departures</h4>
                <p class="trust-card-desc">Book with confidence — all-inclusive luxury itineraries with zero hidden costs.</p>
            </div>
            <div class="trust-card">
                <div class="trust-icon-circle" aria-hidden="true">
                    <span class="material-symbols-outlined">support_agent</span>
                </div>
                <h4 class="trust-card-title">24/7 Dedicated Concierge</h4>
                <p class="trust-card-desc">Personalized on-ground VIP assistance from arrival to departure.</p>
            </div>
            <div class="trust-card">
                <div class="trust-icon-circle" aria-hidden="true">
                    <span class="material-symbols-outlined">diamond</span>
                </div>
                <h4 class="trust-card-title">Handpicked Luxury Sanctuaries</h4>
                <p class="trust-card-desc">Rigorously vetted premium resorts and exclusive private chauffeur-driven transfers.</p>
            </div>
            <div class="trust-card">
                <div class="trust-icon-circle" aria-hidden="true">
                    <span class="material-symbols-outlined">tune</span>
                </div>
                <h4 class="trust-card-title">Bespoke Customization</h4>
                <p class="trust-card-desc">Tailor any itinerary or design your dream holiday with our travel specialists.</p>
            </div>
        </div>
    </section>

    <!-- ── 6. Floating Filter & Sorting Pill ────────────────────────────── -->
    <button
        id="floatingFilterDock"
        class="floating-filter-dock"
        type="button"
        aria-haspopup="dialog"
        aria-expanded="false"
        aria-controls="filterSideDrawer"
    >
        <div class="dock-icon-circle" aria-hidden="true">
            <span class="material-symbols-outlined">tune</span>
        </div>
        <span>FILTERS &amp; SORTING</span>
        <span id="dockFilterCount" class="dock-badge-counter" aria-label="Active filters: 0">All</span>
    </button>

    <!-- ── 7. Slide-Out Filter & Sort Drawer ─────────────────────────────── -->
    <div id="filterDrawerBackdrop" class="filter-drawer-backdrop" aria-hidden="true"></div>

    <aside
        id="filterSideDrawer"
        class="filter-side-drawer"
        role="dialog"
        aria-modal="true"
        aria-label="Filter and Sort Tours"
        aria-hidden="true"
    >
        <div class="drawer-header">
            <div class="drawer-title-box">
                <h2 class="drawer-title">Filter &amp; Sort</h2>
                <span id="drawerLiveCount" class="drawer-subtitle" aria-live="polite">
                    <?php echo $package_count; ?> Packages Available
                </span>
            </div>
            <button
                class="drawer-close-btn"
                id="drawerCloseBtn"
                type="button"
                aria-label="Close filter drawer"
            >&times;</button>
        </div>

        <div class="drawer-body" id="drawerBody">

            <!-- Sort Collection -->
            <fieldset class="filter-group">
                <legend class="filter-group-title">Sort Collection</legend>
                <div class="sort-options-list">
                    <label class="custom-radio-option">
                        <input type="radio" name="catalog_sort" value="default" <?php echo $sort_filter === 'default' ? 'checked' : ''; ?> data-change="apply-filters">
                        <span class="radio-label">Curated Selection</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="catalog_sort" value="price_asc" <?php echo $sort_filter === 'price_asc' ? 'checked' : ''; ?> data-change="apply-filters">
                        <span class="radio-label">Price: Low to High</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="catalog_sort" value="price_desc" <?php echo $sort_filter === 'price_desc' ? 'checked' : ''; ?> data-change="apply-filters">
                        <span class="radio-label">Price: High to Low</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="catalog_sort" value="duration_asc" <?php echo $sort_filter === 'duration_asc' ? 'checked' : ''; ?> data-change="apply-filters">
                        <span class="radio-label">Duration: Shortest First</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="catalog_sort" value="duration_desc" <?php echo $sort_filter === 'duration_desc' ? 'checked' : ''; ?> data-change="apply-filters">
                        <span class="radio-label">Duration: Longest First</span>
                    </label>
                </div>
            </fieldset>

            <!-- Tour Themes -->
            <?php if (!empty($all_themes)): ?>
            <fieldset class="filter-group">
                <legend class="filter-group-title">Tour Themes</legend>
                <div class="checkbox-options-list">
                    <?php foreach ($all_themes as $theme):
                        $isChecked = in_array(catalogKey((string)$theme), $selected_theme_keys, true) ? 'checked' : '';
                    ?>
                        <label class="custom-checkbox-option">
                            <input type="checkbox" class="theme-checkbox" value="<?php echo htmlspecialchars($theme); ?>" <?php echo $isChecked; ?> data-change="apply-filters">
                            <span class="checkbox-label"><?php echo htmlspecialchars($theme); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <!-- Destinations -->
            <?php if (!empty($all_destinations)): ?>
            <fieldset class="filter-group">
                <legend class="filter-group-title">Destinations</legend>
                <div class="checkbox-options-list">
                    <?php foreach ($all_destinations as $dest):
                        $isChecked = in_array(catalogKey((string)$dest), $selected_destination_keys, true) ? 'checked' : '';
                    ?>
                        <label class="custom-checkbox-option">
                            <input type="checkbox" class="dest-checkbox" value="<?php echo htmlspecialchars($dest); ?>" <?php echo $isChecked; ?> data-change="apply-filters">
                            <span class="checkbox-label"><?php echo htmlspecialchars(ucwords($dest)); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <!-- Trip Duration -->
            <?php if (!empty($all_durations)): ?>
            <fieldset class="filter-group">
                <legend class="filter-group-title">Trip Duration</legend>
                <div class="checkbox-options-list">
                    <?php foreach ($all_durations as $key => $val): ?>
                        <label class="custom-checkbox-option">
                            <input type="checkbox" class="duration-checkbox" value="<?php echo htmlspecialchars($key); ?>" <?php echo in_array(catalogKey((string)$key), $selected_duration_keys, true) ? 'checked' : ''; ?> data-change="apply-filters">
                            <span class="checkbox-label"><?php echo htmlspecialchars($val['label']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <!-- Budget Per Traveler -->
            <fieldset class="filter-group">
                <legend class="filter-group-title">Budget Per Traveler</legend>
                <div class="sort-options-list">
                    <label class="custom-radio-option">
                        <input type="radio" name="budget_tier" value="all" <?php echo $budget_tier === 'all' ? 'checked' : ''; ?> data-change="set-budget" data-min="" data-max="">
                        <span class="radio-label">All Price Ranges</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="budget_tier" value="tier1" <?php echo $budget_tier === 'tier1' ? 'checked' : ''; ?> data-change="set-budget" data-min="" data-max="25000">
                        <span class="radio-label">Under &#8377;25,000</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="budget_tier" value="tier2" <?php echo $budget_tier === 'tier2' ? 'checked' : ''; ?> data-change="set-budget" data-min="25000" data-max="50000">
                        <span class="radio-label">&#8377;25,000 &ndash; &#8377;50,000</span>
                    </label>
                    <label class="custom-radio-option">
                        <input type="radio" name="budget_tier" value="tier3" <?php echo $budget_tier === 'tier3' ? 'checked' : ''; ?> data-change="set-budget" data-min="50000" data-max="">
                        <span class="radio-label">Above &#8377;50,000</span>
                    </label>
                    <input type="hidden" id="price_min" value="<?php echo $price_min !== null ? htmlspecialchars((string)(int)$price_min) : ''; ?>">
                    <input type="hidden" id="price_max" value="<?php echo $price_max !== null ? htmlspecialchars((string)(int)$price_max) : ''; ?>">
                </div>
            </fieldset>

        </div>

        <div class="drawer-footer">
            <button class="btn-drawer-reset" type="button" data-action="reset-filters">Clear All</button>
            <button class="btn-drawer-apply" type="button" data-action="close-filter-drawer">
                VIEW TOURS &#8594;
            </button>
        </div>
    </aside>

    <script src="js/modules/all-tours.js" defer></script>

<?php include '../includes/footer.php'; ?>
