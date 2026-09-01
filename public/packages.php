<?php 
    require_once '../config/db.php';
    require_once '../includes/functions.php';
    
    $theme_filter = isset($_GET['theme']) ? trim($_GET['theme']) : '';
    $dest_filter = isset($_GET['destination']) ? trim($_GET['destination']) : '';
    
    $page_title = "Curated Experiences | Leisure Loop Trip";
    if (!empty($theme_filter)) {
        $page_title = htmlspecialchars($theme_filter) . " Escapes | Leisure Loop Trip";
    } elseif (!empty($dest_filter)) {
        $page_title = htmlspecialchars($dest_filter) . " Signature Tours | Leisure Loop Trip";
    }
    
    // Simple Mobile Detect
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

    if ($is_mobile) {
        include '../includes/mobile_packages.php';
        exit;
    }

    // Smart Catalog Redirect: If filter parameters are passed, directly display the complete tours catalog page
    if (!empty($_GET['theme']) || !empty($_GET['type']) || !empty($_GET['destination']) || !empty($_GET['filter']) || !empty($_GET['q']) || !empty($_GET['package_type']) || isset($_GET['view'])) {
        header("Location: all-tours.php?" . $_SERVER['QUERY_STRING']);
        exit;
    }

    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/packages.css">
<?php
// Fetch all active packages
    $packages = [];
    $active_tab = isset($_GET['package_type']) && $_GET['package_type'] === 'fixed' ? 'fixed' : 'curated';
    if ($pdo) {
        $type_filter_val = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
        $query = "SELECT * FROM packages WHERE is_active = 1 AND package_type = " . $pdo->quote($active_tab);
        if ($type_filter_val === 'domestic') {
            $query .= " AND is_international = 0";
        } elseif ($type_filter_val === 'international') {
            $query .= " AND is_international = 1";
        }
        $query .= " ORDER BY created_at DESC";
        
        $packages = $pdo->query($query)->fetchAll();
        
        // Fetch active saved destinations for auto-scrolling hero banner
        try {
            $hero_destinations = $pdo->query("SELECT name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC LIMIT 10")->fetchAll();
        } catch (Exception $e) {
            $hero_destinations = [];
        }
        
        // Fetch active domestic destinations for the Sanctuaries of India showcase
        try {
            $domestic_destinations = $pdo->query("SELECT id, name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 AND category = 'domestic' ORDER BY display_order ASC, name ASC")->fetchAll();
        } catch (Exception $e) {
            $domestic_destinations = [];
        }
        
        // Fetch active international destinations
        try {
            $international_destinations = $pdo->query("SELECT id, name, slug, cover_image, card_image, tagline FROM destinations WHERE is_active = 1 AND category = 'international' ORDER BY display_order ASC, name ASC")->fetchAll();
        } catch (Exception $e) {
            $international_destinations = [];
        }
    } else {
        $hero_destinations = [];
        $domestic_destinations = [];
        $international_destinations = [];
    }
    
    // Fallback if no domestic destinations found in DB yet
    if (empty($domestic_destinations)) {
        $domestic_destinations = [
            ['name' => 'Darjeeling', 'slug' => 'darjeeling', 'card_image' => 'images/packages/darjeeling.jpg'],
            ['name' => 'Sikkim', 'slug' => 'sikkim', 'card_image' => 'images/packages/sikkim.jpg'],
            ['name' => 'Kashmir', 'slug' => 'kashmir', 'card_image' => 'images/packages/kashmir.jpg'],
            ['name' => 'Meghalaya', 'slug' => 'meghalaya', 'card_image' => 'images/packages/meghalaya.jpg'],
            ['name' => 'Ladakh', 'slug' => 'ladakh', 'card_image' => 'images/packages/ladakh.jpg'],
            ['name' => 'Kerala', 'slug' => 'kerala', 'card_image' => 'assets/img/pkg.jpg']
        ];
    }
    
    // Fallback if no international destinations found in DB yet
    if (empty($international_destinations)) {
        $international_destinations = [
            ['name' => 'Bhutan', 'slug' => 'bhutan', 'card_image' => 'assets/img/pkg.jpg'],
            ['name' => 'Nepal', 'slug' => 'nepal', 'card_image' => 'assets/img/pkg.jpg'],
            ['name' => 'Thailand', 'slug' => 'thailand', 'card_image' => 'assets/img/pkg.jpg'],
            ['name' => 'Maldives', 'slug' => 'maldives', 'card_image' => 'assets/img/pkg.jpg'],
            ['name' => 'Bali', 'slug' => 'bali', 'card_image' => 'assets/img/pkg.jpg'],
            ['name' => 'Dubai', 'slug' => 'dubai', 'card_image' => 'assets/img/pkg.jpg']
        ];
    }
    
    // Fallback if no destinations in DB yet
    if (empty($hero_destinations)) {
        $hero_destinations = [
            ['name' => 'Signature Luxury', 'cover_image' => 'assets/img/pkg.jpg', 'tagline' => "Discover the world's most breathtaking sanctuaries with all-inclusive luxury itineraries."],
            ['name' => 'Exquisite Escapes', 'cover_image' => 'assets/img/pkg.jpg', 'tagline' => "Handpicked elite resorts, private chauffeur fleets, and bespoke experiences."]
        ];
    }
    
    // Extract unique dynamic categories for filters
    $all_themes = [];
    $all_destinations = [];
    $all_durations = [];
    $max_price_in_db = 0;
    $min_price_in_db = 999999;
    
    foreach ($packages as $pkg) {
        // Themes
        if (!empty($pkg['tour_type'])) {
            $types = explode(',', $pkg['tour_type']);
            foreach ($types as $t) {
                $t = trim($t);
                if ($t !== '' && !in_array($t, $all_themes)) {
                    $all_themes[] = $t;
                }
            }
        }
        
        // Destinations
        if (!empty($pkg['destination'])) {
            $d = trim($pkg['destination']);
            if (!in_array($d, $all_destinations)) {
                $all_destinations[] = $d;
            }
        }
        
        // Durations
        $days = (int)($pkg['days'] ?? 0);
        $nights = (int)($pkg['nights'] ?? 0);
        if ($days > 0 || $nights > 0) {
            $dur_label = sprintf("%02d Nights / %02d Days", $nights, $days);
            $key = $nights . '-' . $days;
            if (!isset($all_durations[$key])) {
                $all_durations[$key] = [
                    'label' => $dur_label,
                    'nights' => $nights,
                    'days' => $days
                ];
            }
        }
        
        // Price bounds
        $price = (float)$pkg['price'];
        if ($price > $max_price_in_db) {
            $max_price_in_db = $price;
        }
        if ($price < $min_price_in_db) {
            $min_price_in_db = $price;
        }
    }
    
    // Fallbacks if db is empty
    if (empty($packages)) {
        $min_price_in_db = 0;
        $max_price_in_db = 50000;
    }
    
    // Sort filters
    sort($all_themes);
    sort($all_destinations);
    
    uasort($all_durations, function($a, $b) {
        return $a['nights'] <=> $b['nights'];
    });
?>

<?php
    // Theme icon mapper for EaseMyTrip inspired circles
    $theme_icons_map = [
        'leisure' => 'self_improvement',
        'honeymoon' => 'favorite',
        'hill station' => 'terrain',
        'spiritual' => 'temple_hindu',
        'adventure' => 'kitesurfing',
        'nature' => 'nature_people',
        'treks' => 'hiking',
        'wildlife' => 'cruelty_free',
        'heritage' => 'castle',
        'beach' => 'beach_access'
    ];
?>

    <!-- 1. EaseMyTrip Inspired Auto-Scrolling Saved Destinations Hero -->
    <section class="emt-pkg-hero" id="heroCarouselSection">
        <div class="hero-slides-wrapper">
            <?php foreach ($hero_destinations as $idx => $h_dest): 
                $bg_img = !empty($h_dest['cover_image']) ? $h_dest['cover_image'] : (!empty($h_dest['card_image']) ? $h_dest['card_image'] : 'assets/img/pkg.jpg');
            ?>
            <div class="hero-slide-bg <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide="<?php echo $idx; ?>">
                <img class="hero-slide-img" src="<?php echo htmlspecialchars($bg_img); ?>" alt="<?php echo htmlspecialchars($h_dest['name']); ?> Tour Packages">
                <div class="emt-pkg-hero-gradient"></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($hero_destinations) > 1): ?>
        <!-- Side Navigation Arrows -->
        <button class="hero-arrow-btn hero-arrow-left" data-action="hero-slide" data-dir="-1" aria-label="Previous destination">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="hero-arrow-btn hero-arrow-right" data-action="hero-slide" data-dir="1" aria-label="Next destination">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
        <?php endif; ?>

        <div class="emt-pkg-hero-content">
            <span class="luxury-accent-badge">
                <span class="material-symbols-outlined" style="font-size: 1.1rem; color: var(--gold);">verified</span>
                <?php echo !empty($theme_filter) ? htmlspecialchars($theme_filter) . ' Signature Collection' : 'Curated Signature Holiday Packages'; ?>
            </span>

            <div class="hero-typography-slider">
                <?php foreach ($hero_destinations as $idx => $h_dest): 
                    $dest_name = !empty($h_dest['name']) ? $h_dest['name'] : 'Exclusive';
                    $dest_tagline = !empty($h_dest['tagline']) ? $h_dest['tagline'] : "Where Every Experience Counts!";
                ?>
                <div class="hero-slide-text <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide="<?php echo $idx; ?>">
                    <h1 class="hero-dest-script-title">
                        <?php echo htmlspecialchars($dest_name); ?> <span style="font-family: 'Playfair Display', serif; font-style: italic; font-weight: 700; font-size: 0.85em;">Tour Packages</span>
                    </h1>
                    <p class="hero-dest-subtitle">
                        <?php echo htmlspecialchars($dest_tagline); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

        <?php if (count($hero_destinations) > 1): ?>
        <!-- Bottom Slide Dots -->
        <div class="hero-slider-nav">
            <?php foreach ($hero_destinations as $idx => $h_dest): ?>
            <button class="hero-slide-dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-action="hero-slide-to" data-idx="<?php echo $idx; ?>" aria-label="Go to destination slide <?php echo $idx + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- Multi-Parameter Smart Search Dock -->
    <div class="search-bar-wrapper">
    <div class="search-bar-container">
        <div class="search-bar">
            <div class="search-input-group">
                <label class="group-label">Where To?</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">location_on</span>
                    
<label for="search_keyword_input" class="sr-only">Search destinations...</label>
<input type="text" id="search_keyword_input" placeholder="Search destinations...">
                </div>
            </div>

            <div class="search-input-group">
                <label class="group-label">Tour Theme</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">explore</span>
                    <select id="search_theme_select" data-change="submit-landing-search">
                        <option value="">All Holiday Themes</option>
                        <?php foreach ($all_themes as $t_item): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($t_item)); ?>" <?php echo (strtolower($theme_filter) === strtolower($t_item)) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t_item); ?> Tours
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="search-input-group">
                <label class="group-label">Trip Duration</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">schedule</span>
                    <select id="search_dur_select" data-change="submit-landing-search">
                        <option value="">Any Duration</option>
                        <?php foreach ($all_durations as $key => $dur_info): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($dur_info['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <button class="btn-search" data-action="submit-landing-search">SEARCH HOLIDAYS</button>
    </div>
    </div>

    <?php if (!empty($domestic_destinations) || !empty($international_destinations)): ?>
    <!-- 1.2. Sanctuaries of India: Signature Destinations Showcase -->
    <section class="sanctuaries-section-wrap">
        <div class="sanctuaries-glass-container">
            <div class="sanctuaries-header">
                <div class="sanctuaries-title-box">
                    <h2 class="sanctuaries-title" id="sanctuariesMainTitle">
                        <span class="material-symbols-outlined gold-icon-badge">auto_awesome</span> 
                        Domestic Signature Destinations
                    </h2>
                    <p class="sanctuaries-subtitle" id="sanctuariesSubtitle">Immerse yourself in breathtaking mountain retreats, misty valley tea gardens, and timeless cultural realms across India.</p>
                </div>
                
                <div class="sanctuaries-controls" style="display: flex; align-items: center; gap: 20px;">
                    <div class="trending-pills-group" style="margin-bottom: 0;">
                        <button class="trending-pill active" data-action="filter-pkg" data-type="signature" data-val="domestic">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">landscape</span> Domestic
                        </button>
                        <button class="trending-pill" data-action="filter-pkg" data-type="signature" data-val="international">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">flight_takeoff</span> International
                        </button>
                    </div>

                    <div class="sanctuaries-nav-arrows">
                        <button class="sanctuaries-btn-nav" id="sanctuariesBtnPrev" aria-label="Previous Destinations">
                            <span class="material-symbols-outlined" style="font-size: 1.6rem;">chevron_left</span>
                        </button>
                        <button class="sanctuaries-btn-nav" id="sanctuariesBtnNext" aria-label="Next Destinations">
                            <span class="material-symbols-outlined" style="font-size: 1.6rem;">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="sanctuaries-track-wrapper">
                <div class="sanctuaries-cards-track" id="sanctuariesTrack">
                    <?php 
                    $all_showcase_destinations = array_merge(
                        array_map(function($d) { $d['scope'] = 'domestic'; return $d; }, $domestic_destinations),
                        array_map(function($d) { $d['scope'] = 'international'; return $d; }, $international_destinations)
                    );
                    foreach ($all_showcase_destinations as $s_dest): 
                        $sd_img = !empty($s_dest['card_image']) ? $s_dest['card_image'] : (!empty($s_dest['cover_image']) ? $s_dest['cover_image'] : 'assets/img/pkg.jpg');
                        $sd_name = $s_dest['name'] ?? 'Darjeeling';
                        $sd_scope = $s_dest['scope'];
                        $sd_link = "all-tours.php?destination=" . urlencode(trim($sd_name)) . "&type=" . $sd_scope;
                    ?>
                    <a href="<?php echo htmlspecialchars($sd_link); ?>" class="sanctuary-portrait-card signature-dest-card" data-scope="<?php echo $sd_scope; ?>" <?php echo $sd_scope === 'international' ? 'style="display: none;"' : ''; ?>>
                        <img src="<?php echo htmlspecialchars($sd_img); ?>" alt="<?php echo htmlspecialchars($sd_name); ?>" class="sanctuary-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="sanctuary-card-overlay">
                            <h3 class="sanctuary-dest-name"><?php echo htmlspecialchars($sd_name); ?></h3>
                            <div class="sanctuary-dest-explore">
                                <span>Explore</span>
                                <span class="material-symbols-outlined" style="font-size: 0.95rem;">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Filter trending packages for showcase
    $trending_packages = array_filter($packages, function($p) {
        return !empty($p['is_trending']) || !empty($p['is_featured']);
    });
    if (!empty($trending_packages)):
    ?>
    <!-- 1.5. Top Trending Tours Showcase (Domestic & International) -->
    <section class="trending-tours-section">
        <div class="trending-header-bar">
            <div class="trending-title-group">
                <h2 class="trending-title">
                    <span style="color: #fb923c;">🔥</span> Top Trending Tours
                </h2>
                <p class="trending-subtitle">Explore our most sought-after holiday packages across India and around the globe.</p>
            </div>
            <div class="trending-controls">
                <div class="trending-pills-group">
                    <button class="trending-pill active" data-action="filter-pkg" data-type="trending" data-val="all">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">star</span> All Trending
                    </button>
                    <button class="trending-pill" data-action="filter-pkg" data-type="trending" data-val="domestic">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">landscape</span> Domestic
                    </button>
                    <button class="trending-pill" data-action="filter-pkg" data-type="trending" data-val="international">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">flight_takeoff</span> International
                    </button>
                </div>
            </div>
        </div>

        <div class="trending-track-wrapper">
            <div id="trendingTrack" class="trending-cards-track">
                <?php foreach ($trending_packages as $tp): 
                    $t_img = !empty($tp['image_url']) ? $tp['image_url'] : 'assets/img/pkg.jpg';
                    $is_intl = !empty($tp['is_international']) ? 'international' : 'domestic';
                    $type_badge = !empty($tp['is_international']) ? '✈️ International' : '🇮🇳 Domestic';
                    $t_price = !empty($tp['price']) ? number_format((float)$tp['price']) : 'On Request';
                    $t_dur = (!empty($tp['nights']) && !empty($tp['days'])) ? "{$tp['nights']}N / {$tp['days']}D" : (!empty($tp['days']) ? "{$tp['days']} Days" : 'Custom');
                ?>
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($tp['slug'] ?? ''); ?>" class="trending-tour-card" data-scope="<?php echo $is_intl; ?>">
                    <div class="trending-top-badges">
                        <span class="badge-trending-hot">🔥 TRENDING</span>
                        <span class="badge-trending-type"><?php echo $type_badge; ?></span>
                    </div>
                    <img src="<?php echo htmlspecialchars($t_img); ?>" alt="<?php echo htmlspecialchars($tp['title']); ?>" class="trending-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="trending-card-overlay">
                        <div class="trending-card-dest">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">location_on</span>
                            <?php echo htmlspecialchars($tp['destination'] ?? 'India'); ?>
                        </div>
                        <h3 class="trending-card-title"><?php echo htmlspecialchars($tp['title']); ?></h3>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.7); margin-bottom: 8px; font-weight: 500;">
                            🕒 <?php echo htmlspecialchars($t_dur); ?>
                        </div>
                        <div class="trending-card-footer">
                            <div class="trending-price-group">
                                <span class="trending-price-lbl">Starting From</span>
                                <span class="trending-price-val"><?php echo $t_price !== 'On Request' ? '₹' . $t_price : $t_price; ?></span>
                            </div>
                            <span class="btn-trending-explore">
                                Explore <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_forward</span>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 2. Interactive Theme Discovery Circle Carousel (Pic 1 & EaseMyTrip Style) -->
    <section class="theme-circle-section">
        <div class="theme-strip-heading">Explore Holiday Collections By Theme</div>
        <div class="theme-carousel-wrapper">
            <button class="emt-nav-btn emt-nav-prev" id="themeBtnPrev" aria-label="Previous">
                <span class="material-symbols-outlined" style="font-size: 1.8rem;">chevron_left</span>
            </button>
            
            <div id="themeCarouselTrack" class="theme-carousel-track">
                <?php 
                $curated_pkg_themes = [
                    ['title' => 'Beach', 'slug' => 'Beach', 'img' => 'images/themes/beach.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Nature', 'slug' => 'Nature', 'img' => 'images/themes/nature.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Treks', 'slug' => 'Treks', 'img' => 'images/themes/treks.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Leisure', 'slug' => 'Leisure', 'img' => 'images/themes/leisure.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Honeymoon', 'slug' => 'Honeymoon', 'img' => 'images/themes/honeymoon.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Cultural', 'slug' => 'Cultural', 'img' => 'images/themes/cultural.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Off Beat', 'slug' => 'Off Beat', 'img' => 'images/themes/offbeat.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Tea Gardens', 'slug' => 'Tea Gardens', 'img' => 'images/themes/teagarden.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Wellness', 'slug' => 'Wellness', 'img' => 'images/themes/wellness.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Couples', 'slug' => 'Couples', 'img' => 'images/themes/couple.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Adventure', 'slug' => 'Adventure', 'img' => 'images/themes/adventure.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Private', 'slug' => 'Private', 'img' => 'images/themes/private.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Road Trip', 'slug' => 'Road Trip', 'img' => 'images/themes/roadtrip.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Hill Station', 'slug' => 'Hill Station', 'img' => 'images/themes/hillstation.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Family', 'slug' => 'Family', 'img' => 'images/themes/family.webp', 'sub' => 'Explore Now'],
                    ['title' => 'Spiritual', 'slug' => 'Spiritual', 'img' => 'images/themes/spiritual.webp', 'sub' => 'Explore Now'],
                ];

                // Duplicate items so all 16 themes show cleanly across full pages of 5 cards without hitting a hard right scroll boundary
                $carousel_items = array_merge($curated_pkg_themes, $curated_pkg_themes, $curated_pkg_themes, $curated_pkg_themes);

                foreach ($carousel_items as $th): 
                    $slug_val = strtolower($th['slug']);
                    $is_active = ($slug_val !== '' && $slug_val === strtolower($theme_filter));
                    $link = "all-tours.php?theme=" . urlencode(strtolower($th['slug']));
                ?>
                <a href="<?php echo htmlspecialchars($link); ?>" class="emt-theme-card <?php echo $is_active ? 'active' : ''; ?>">
                    <div class="emt-circle-photo">
                        <div class="emt-circle-bg" style="background-image: url('<?php echo htmlspecialchars($th['img']); ?>');"></div>
                        <div class="emt-circle-overlay"></div>
                    </div>
                    <h3 class="emt-circle-title"><?php echo htmlspecialchars($th['title']); ?></h3>
                    <span class="emt-circle-sub"><?php echo htmlspecialchars($th['sub']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>

            <button class="emt-nav-btn emt-nav-next" id="themeBtnNext" aria-label="Next">
                <span class="material-symbols-outlined" style="font-size: 1.8rem;">chevron_right</span>
            </button>
        </div>

        <div class="all-collections-banner-wrap">
            <a href="all-tours.php" class="btn-explore-all-tours">
                <span class="material-symbols-outlined icon-globe">globe_asia</span>
                <span>Explore All Tours & Holiday Collections</span>
                <span class="material-symbols-outlined icon-arrow">arrow_forward</span>
            </a>
        </div>
    </section>

    <?php
    // Filter discounted packages for Exclusive Offers showcase
    $offer_packages = array_filter($packages, function($p) {
        return !empty($p['original_price']) && floatval($p['original_price']) > floatval($p['price']);
    });
    // Fallback: if somehow empty, just pick up to 5 packages so section is never blank
    if (empty($offer_packages)) {
        $offer_packages = array_slice($packages, 0, 5);
    }
    if (!empty($offer_packages)):
    ?>
    <!-- 2.5. Exclusive Offers & Deals Showcase (Discounts & Savings) -->
    <section class="trending-tours-section" style="background: #040912; border-bottom: 1px solid rgba(16, 185, 129, 0.15);">
        <div class="trending-header-bar">
            <div class="trending-title-group">
                <h2 class="trending-title">
                    <span style="color: #10b981;">🎁</span> Exclusive Holiday Offers
                </h2>
                <p class="trending-subtitle">Unbeatable limited-time discounts and curated seasonal savings across domestic and international sanctuaries.</p>
            </div>
            <div class="trending-controls">
                <div class="trending-pills-group" style="border-color: rgba(16, 185, 129, 0.25);">
                    <button class="offer-pill active" data-action="filter-pkg" data-type="offers" data-val="all">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">bolt</span> All Deals
                    </button>
                    <button class="offer-pill" data-action="filter-pkg" data-type="offers" data-val="domestic">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">landscape</span> Domestic
                    </button>
                    <button class="offer-pill" data-action="filter-pkg" data-type="offers" data-val="international">
                        <span class="material-symbols-outlined" style="font-size: 1.1rem;">flight_takeoff</span> International
                    </button>
                </div>
            </div>
        </div>

        <div class="trending-track-wrapper">
            <div id="offersTrack" class="trending-cards-track">
                <?php foreach ($offer_packages as $op): 
                    $o_img = !empty($op['image_url']) ? $op['image_url'] : 'assets/img/pkg.jpg';
                    $o_scope = !empty($op['is_international']) ? 'international' : 'domestic';
                    $o_badge = !empty($op['is_international']) ? '✈️ International' : '🇮🇳 Domestic';
                    $o_price_num = floatval($op['price']);
                    $o_orig_num = !empty($op['original_price']) ? floatval($op['original_price']) : floatval($o_price_num * 1.35);
                    if ($o_orig_num <= $o_price_num) $o_orig_num = $o_price_num * 1.35;
                    
                    $discount_pct = round((($o_orig_num - $o_price_num) / $o_orig_num) * 100);
                    $o_price = number_format($o_price_num);
                    $o_orig_price = number_format($o_orig_num);
                    $o_dur = (!empty($op['nights']) && !empty($op['days'])) ? "{$op['nights']}N / {$op['days']}D" : (!empty($op['days']) ? "{$op['days']} Days" : 'Custom');
                ?>
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($op['slug'] ?? ''); ?>" class="trending-tour-card offer-tour-card" data-scope="<?php echo $o_scope; ?>" style="border-color: rgba(16, 185, 129, 0.25);">
                    <div class="trending-top-badges">
                        <span class="badge-offer-discount">⚡ <?php echo $discount_pct; ?>% OFF</span>
                        <span class="badge-trending-type"><?php echo $o_badge; ?></span>
                    </div>
                    <img src="<?php echo htmlspecialchars($o_img); ?>" alt="<?php echo htmlspecialchars($op['title']); ?>" class="trending-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="trending-card-overlay">
                        <div class="trending-card-dest" style="color: #34d399;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">location_on</span>
                            <?php echo htmlspecialchars($op['destination'] ?? 'India'); ?>
                        </div>
                        <h3 class="trending-card-title"><?php echo htmlspecialchars($op['title']); ?></h3>
                        <div style="font-size: 0.82rem; color: rgba(255,255,255,0.7); margin-bottom: 6px; font-weight: 500;">
                            🕒 <?php echo htmlspecialchars($o_dur); ?>
                        </div>
                        <div class="trending-card-footer">
                            <div class="trending-price-group" style="min-width: 0; flex: 1; padding-right: 6px;">
                                <span style="font-size: 0.75rem; color: #f87171; text-decoration: line-through; font-weight: 600; line-height: 1.2;">₹<?php echo $o_orig_price; ?></span>
                                <div style="display: flex; align-items: baseline; gap: 3px; flex-wrap: wrap;">
                                    <span class="trending-price-val" style="color: #34d399; font-size: 1.15rem;">₹<?php echo $o_price; ?></span>
                                    <span style="font-size: 0.68rem; color: rgba(255,255,255,0.6); font-weight: 400;">/person</span>
                                </div>
                            </div>
                            <span class="btn-offer-claim">
                                View Deal <span class="material-symbols-outlined" style="font-size: 1rem;">local_offer</span>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. Complete Portfolio CTA Showcase -->
    <section class="section" style="padding: 4rem 1.5rem 6rem; max-width: 1200px; margin: 0 auto;">
        <div style="background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 28px; padding: 4rem 2rem; text-align: center; position: relative; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.5); backdrop-filter: blur(16px);">
            <div style="position: absolute; top: -50%; left: 50%; transform: translateX(-50%); width: 600px; height: 300px; background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, transparent 70%); pointer-events: none;"></div>
            
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.4); border-radius: 50px; color: var(--gold); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem;">
                <span class="material-symbols-outlined" style="font-size: 1rem;">explore</span> Complete Tour Inventory
            </span>
            
            <h2 style="font-size: 2.5rem; font-weight: 800; color: #fff; margin-bottom: 1.2rem; font-family: var(--font-heading); line-height: 1.2;">
                Ready to Find Your <span style="color: var(--gold); font-style: italic;">Dream Sanctuary?</span>
            </h2>
            
            <p style="color: rgba(255, 255, 255, 0.75); font-size: 1.1rem; max-width: 700px; margin: 0 auto 2.5rem; line-height: 1.6;">
                Browse our complete catalog of <?php echo count($packages); ?>+ curated luxury holidays, customized itineraries, and guaranteed fixed group departures with interactive budget and duration filters.
            </p>
            
            <a href="all-tours.php" style="display: inline-flex; align-items: center; gap: 12px; padding: 16px 36px; background: linear-gradient(135deg, #e5c158 0%, #b89130 100%); color: #000; font-weight: 700; font-size: 1.1rem; border-radius: 50px; text-decoration: none; box-shadow: 0 10px 25px rgba(212, 175, 55, 0.4); transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 30px rgba(212, 175, 55, 0.6)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(212, 175, 55, 0.4)';">
                ✨ Explore All Available Tours <span class="material-symbols-outlined" style="font-size: 1.3rem;">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- Client-Side Search Engine for Landing Hub -->
    <script src="js/modules/packages.js" defer></script>

<?php include '../includes/footer.php'; ?>
