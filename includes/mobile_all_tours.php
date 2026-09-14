<?php
// Only fall back if $packages was never set by the controller.
// An empty array is a valid filtered result (zero matches) and must NOT be overridden.
if (!isset($packages)) {
    if (isset($catalog_packages)) {
        $packages = $catalog_packages;
    } elseif (isset($packages_mobile)) {
        $packages = $packages_mobile;
    } else {
        $packages = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="mob-tours-body">

    <!-- 1. Sticky App Header & Category Navigation -->
    <header class="mob-tours__header">
        <div class="mob-tours__header-row">
            <a href="packages.php?view=mobile" class="mob-tours__back" aria-label="Back">
                <span class="material-symbols-outlined mob-tours__fab-icon">arrow_back</span>
            </a>
            <span class="mob-tours__header-title">All Tours &amp; Collections</span>
            <div class="mob-tours__header-spacer"></div>
        </div>

        <nav class="mob-tours__cat-nav" aria-label="Category">
            <a href="offers.php?view=mobile" class="mob-tours__cat-item">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--gold">local_offer</span>
                <span class="mob-tours__cat-label">Offers</span>
            </a>
            <a href="all-tours.php?view=mobile" class="mob-tours__cat-item is-active">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--gold">explore</span>
                <span class="mob-tours__cat-label mob-tours__cat-label--active">Tours</span>
            </a>
            <a href="destinations.php?view=mobile" class="mob-tours__cat-item">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--gold">location_on</span>
                <span class="mob-tours__cat-label">Destinations</span>
            </a>
            <a href="fixed-departures.php?view=mobile" class="mob-tours__cat-item">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--amber">event_available</span>
                <span class="mob-tours__cat-label">Fixed Departure</span>
            </a>
            <a href="hotels.php?view=mobile" class="mob-tours__cat-item">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--purple">hotel</span>
                <span class="mob-tours__cat-label">Hotels</span>
            </a>
            <a href="cabs.php?view=mobile" class="mob-tours__cat-item">
                <span class="material-symbols-outlined mob-tours__cat-icon mob-tours__cat-icon--emerald">directions_car</span>
                <span class="mob-tours__cat-label">Cabs</span>
            </a>
        </nav>
    </header>

    <!-- 2. Search Dock -->
    <div class="mob-tours__search-dock">
        <form action="all-tours.php" method="GET" class="mob-tours__search-form">
            <input type="hidden" name="view" value="mobile">
            <span class="material-symbols-outlined mob-tours__search-icon" aria-hidden="true">search</span>
            <label for="tours_search_q" class="sr-only">Search destinations or tours</label>
            <input id="tours_search_q" type="text" name="q" value="<?= htmlspecialchars($keyword_filter ?? '') ?>" placeholder="Search destinations or tours..." class="mob-tours__search-input">
            <button type="submit" class="mob-tours__search-btn">Search</button>
        </form>
    </div>

    <!-- 3. Theme Filter Pills -->
    <div class="mob-tours__pills">
        <div class="mob-tours__pills-row">
            <a href="all-tours.php?view=mobile" class="mob-tours__pill <?= empty($selected_theme_keys) ? 'mob-tours__pill--active' : 'mob-tours__pill--inactive' ?>">
                All Tours
            </a>
            <?php foreach (array_slice($all_themes ?? [], 0, 6) as $thItem):
                $isThemeActive = in_array(catalogKey((string)$thItem), $selected_theme_keys, true);
            ?>
            <a href="all-tours.php?view=mobile&theme=<?= urlencode($thItem) ?>" class="mob-tours__pill <?= $isThemeActive ? 'mob-tours__pill--active' : 'mob-tours__pill--inactive' ?>">
                <?= htmlspecialchars($thItem) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <main class="mob-tours__main">
        <!-- 4. Package Cards Grid -->
        <div id="tourGrid" class="mob-tours__grid">
            <?php if (!empty($packages)): ?>
                <?php foreach ($packages as $pkg): ?>
                    <?php
                        $pkgTitle = $pkg['title'] ?? '';
                        $pkgDest = $pkg['destination'] ?? '';
                        $pkgDays = (int)($pkg['days'] ?? 4);
                        $pkgNights = (int)($pkg['nights'] ?? ($pkgDays > 1 ? $pkgDays - 1 : 1));
                        $pkgPrice = (float)($pkg['price'] ?? 0);
                        $rawImg = $pkg['image_url'] ?? $pkg['image'] ?? '';
                        if (!empty($rawImg)) {
                            $pkgImg = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $rawImg) ? $rawImg : 'images/dest/' . ltrim($rawImg, '/');
                        } else {
                            $pkgImg = 'assets/img/pkg.jpg';
                        }
                        $pkgTheme = strtolower($pkg['tour_type'] ?? $pkg['theme'] ?? 'standard');
                    ?>
                    <a href="package-detail.php?slug=<?= urlencode($pkg['slug'] ?? '') ?>&view=mobile"
                       data-theme="<?= htmlspecialchars($pkgTheme) ?>"
                       class="js-tour-card mob-tours__card">
                        <div class="mob-tours__card-img-wrap">
                            <img src="<?= htmlspecialchars($pkgImg) ?>" alt="<?= htmlspecialchars($pkgTitle) ?>" class="mob-tours__card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                            <div class="mob-tours__card-overlay" aria-hidden="true"></div>
                            <div class="mob-tours__card-badge"><?= $pkgNights ?>N / <?= $pkgDays ?>D</div>
                        </div>
                        <div class="mob-tours__card-body">
                            <span class="mob-tours__card-dest"><?= htmlspecialchars($pkgDest ?: 'Himalayas') ?></span>
                            <h3 class="mob-tours__card-title"><?= htmlspecialchars($pkgTitle) ?></h3>
                            <?php if ($pkgPrice > 0): ?>
                                <div class="mob-tours__card-price">
                                    <span class="mob-tours__card-price-from">From </span>&#8377;<?= number_format($pkgPrice) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mob-tours__empty">
                    <div class="mob-tours__empty-icon-wrap">
                        <span class="material-symbols-outlined mob-tours__empty-icon">remove_circle</span>
                    </div>
                    <h2 class="mob-tours__empty-title">No collections match your current filters.</h2>
                    <p class="mob-tours__empty-desc">Try clearing your filters or reach out to our concierge to craft a bespoke itinerary.</p>
                    <a href="all-tours.php?view=mobile" class="mob-tours__empty-reset">
                        <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
                        Reset All Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- 5. Floating Action Bar -->
    <div class="mob-tours__fab" id="floatingFilterDock" aria-haspopup="dialog" aria-expanded="false" aria-controls="filterSideDrawer">
        <button type="button" id="btnOpenSort" class="mob-tours__fab-btn">
            <span class="material-symbols-outlined mob-tours__fab-icon">sort</span>
            <span class="mob-tours__fab-label">Sort</span>
            <span id="dockSortCount" class="mob-tours__fab-count" aria-label="Active sort: 0"></span>
        </button>
        <button type="button" id="btnOpenFilter" class="mob-tours__fab-btn">
            <span class="material-symbols-outlined mob-tours__fab-icon">tune</span>
            <span class="mob-tours__fab-label">Filter</span>
            <span id="dockFilterCount" class="mob-tours__fab-count" aria-label="Active filters: 0">All</span>
        </button>
    </div>

    <!-- 6. Sort Drawer -->
    <div id="sortDrawerBackdrop" class="mob-tours__backdrop" aria-hidden="true"></div>
    <aside id="sortSideDrawer" class="mob-tours__drawer" role="dialog" aria-modal="true" aria-label="Sort Tours">
        <div class="mob-tours__drawer-head">
            <h2 class="mob-tours__drawer-title">Sort Tours</h2>
            <button id="sortCloseBtn" type="button" class="mob-tours__drawer-close" aria-label="Close">&times;</button>
        </div>
        <div class="mob-tours__drawer-body">
            <fieldset class="mob-tours__fieldset">
                <legend class="mob-tours__legend">Sort Collection</legend>
                <div class="mob-tours__options">
                    <?php foreach ([
                        ['default','Curated Selection'],
                        ['price_asc','Price: Low to High'],
                        ['price_desc','Price: High to Low'],
                        ['duration_asc','Duration: Shortest First'],
                        ['duration_desc','Duration: Longest First'],
                    ] as [$val,$label]):
                        $sortRadioId = 'mob_sort_' . $val;
                    ?>
                    <label class="mob-tours__option" for="<?= $sortRadioId ?>">
                        <input type="radio" id="<?= $sortRadioId ?>" name="catalog_sort" value="<?= $val ?>" <?= $val === 'default' ? 'checked' : '' ?>>
                        <div class="mob-tours__indicator">
                            <div class="mob-tours__indicator-dot"></div>
                        </div>
                        <span class="mob-tours__option-label"><?= htmlspecialchars($label) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <fieldset class="mob-tours__fieldset">
                <legend class="mob-tours__legend">Budget Per Traveler</legend>
                <div class="mob-tours__options">
                    <?php foreach ([
                        ['all','','','All Price Ranges'],
                        ['tier1','','25000','Under &#8377;25,000'],
                        ['tier2','25000','50000','&#8377;25,000 &ndash; &#8377;50,000'],
                        ['tier3','50000','','Above &#8377;50,000'],
                    ] as [$val,$min,$max,$label]):
                        $budgetId = 'mob_budget_' . $val;
                    ?>
                    <label class="mob-tours__option" for="<?= $budgetId ?>">
                        <input type="radio" id="<?= $budgetId ?>" name="budget_tier" value="<?= $val ?>" data-min="<?= $min ?>" data-max="<?= $max ?>" <?= $val === 'all' ? 'checked' : '' ?>>
                        <div class="mob-tours__indicator">
                            <div class="mob-tours__indicator-dot"></div>
                        </div>
                        <span class="mob-tours__option-label"><?= $label ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="price_min" value="">
                <input type="hidden" id="price_max" value="">
            </fieldset>
        </div>
        <div class="mob-tours__drawer-foot">
            <button type="button" class="mob-tours__drawer-clear" data-action="reset-filters">Clear</button>
            <button type="button" class="mob-tours__drawer-apply" data-action="close-filter-drawer">Apply Sort</button>
        </div>
    </aside>

    <!-- 7. Filter Drawer -->
    <div id="filterDrawerBackdrop" class="mob-tours__backdrop" aria-hidden="true"></div>
    <aside id="filterSideDrawer" class="mob-tours__drawer" role="dialog" aria-modal="true" aria-label="Filter Tours">
        <div class="mob-tours__drawer-head">
            <h2 class="mob-tours__drawer-title">Filter Tours</h2>
            <button id="drawerCloseBtn" type="button" class="mob-tours__drawer-close" aria-label="Close">&times;</button>
        </div>
        <div class="mob-tours__drawer-body">
            <?php if (!empty($all_themes)): ?>
            <fieldset class="mob-tours__fieldset">
                <legend class="mob-tours__legend">Tour Themes</legend>
                <div class="mob-tours__options">
                    <?php foreach ($all_themes as $theme):
                        $isChecked = in_array(catalogKey((string)$theme), $selected_theme_keys, true) ? 'checked' : '';
                        $themeId = 'mob_theme_' . htmlspecialchars(catalogKey((string)$theme));
                    ?>
                        <label class="mob-tours__option" for="<?= $themeId ?>">
                            <input type="checkbox" id="<?= $themeId ?>" class="theme-checkbox" value="<?= htmlspecialchars(strtolower($theme)) ?>" <?= $isChecked ?>>
                            <div class="mob-tours__indicator mob-tours__indicator--checkbox">
                                <span class="material-symbols-outlined mob-tours__indicator-check">check</span>
                            </div>
                            <span class="mob-tours__option-label"><?= htmlspecialchars($theme) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <?php if (!empty($all_destinations)): ?>
            <fieldset class="mob-tours__fieldset">
                <legend class="mob-tours__legend">Destinations</legend>
                <div class="mob-tours__options">
                    <?php foreach ($all_destinations as $dest):
                        $isChecked = in_array(catalogKey((string)$dest), $selected_destination_keys, true) ? 'checked' : '';
                        $destId = 'mob_dest_' . htmlspecialchars(catalogKey((string)$dest));
                    ?>
                        <label class="mob-tours__option" for="<?= $destId ?>">
                            <input type="checkbox" id="<?= $destId ?>" class="dest-checkbox" value="<?= htmlspecialchars(strtolower($dest)) ?>" <?= $isChecked ?>>
                            <div class="mob-tours__indicator mob-tours__indicator--checkbox">
                                <span class="material-symbols-outlined mob-tours__indicator-check">check</span>
                            </div>
                            <span class="mob-tours__option-label"><?= htmlspecialchars(ucwords($dest)) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <?php if (!empty($all_durations)): ?>
            <fieldset class="mob-tours__fieldset">
                <legend class="mob-tours__legend">Trip Duration</legend>
                <div class="mob-tours__options">
                    <?php foreach ($all_durations as $key => $val):
                        $durId = 'mob_dur_' . htmlspecialchars(catalogKey((string)$key));
                    ?>
                        <label class="mob-tours__option" for="<?= $durId ?>">
                            <input type="checkbox" id="<?= $durId ?>" class="duration-checkbox" value="<?= htmlspecialchars((string)$key) ?>">
                            <div class="mob-tours__indicator mob-tours__indicator--checkbox">
                                <span class="material-symbols-outlined mob-tours__indicator-check">check</span>
                            </div>
                            <span class="mob-tours__option-label"><?= htmlspecialchars($val['label']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endif; ?>
        </div>
        <div class="mob-tours__drawer-foot">
            <button type="button" class="mob-tours__drawer-clear" data-action="reset-filters">Clear All</button>
            <button type="button" class="mob-tours__drawer-apply" data-action="close-filter-drawer">Apply Filters</button>
        </div>
    </aside>

    <?php 
    $extra_scripts = '<script src="js/modules/all-tours.js" defer></script>';
    include __DIR__ . '/mobile_footer.php'; 
    ?>
</body>
</html>
