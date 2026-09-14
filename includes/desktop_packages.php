<?php
// includes/desktop_packages.php
include '../includes/header.php';
?>
<link rel="stylesheet" href="css/packages.css?v=<?= time() ?>">

    <?php if (!empty($hero_destinations)): ?>
    <!-- 1. Auto-Scrolling Destinations Hero -->
    <section class="emt-pkg-hero" id="heroCarouselSection">
        <div class="hero-slides-wrapper">
            <?php foreach ($hero_destinations as $idx => $h_dest): 
                $bg_img = !empty($h_dest['cover_image']) ? $h_dest['cover_image'] : (!empty($h_dest['card_image']) ? $h_dest['card_image'] : 'assets/img/pkg.jpg');
            ?>
            <div class="hero-slide-bg <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide="<?php echo $idx; ?>">
                <img class="hero-slide-img" src="<?php echo htmlspecialchars($bg_img); ?>" alt="<?php echo htmlspecialchars($h_dest['name']); ?> Tour Packages" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                <div class="emt-pkg-hero-gradient"></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($hero_destinations) > 1): ?>
        <button class="hero-arrow-btn hero-arrow-left" data-action="hero-slide" data-dir="-1" aria-label="Previous destination">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="hero-arrow-btn hero-arrow-right" data-action="hero-slide" data-dir="1" aria-label="Next destination">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
        <?php endif; ?>

        <div class="emt-pkg-hero-content">
            <span class="luxury-accent-badge">
                <span class="material-symbols-outlined icon-gold-sm">verified</span>
                <?php echo !empty($theme_filter) ? htmlspecialchars($theme_filter) . ' Signature Collection' : 'Curated Signature Holiday Packages'; ?>
            </span>

            <div class="hero-typography-slider">
                <?php foreach ($hero_destinations as $idx => $h_dest): 
                    $dest_name    = !empty($h_dest['name']) ? $h_dest['name'] : 'Exclusive';
                    $dest_tagline = !empty($h_dest['tagline']) ? $h_dest['tagline'] : "Where Every Experience Counts!";
                ?>
                <div class="hero-slide-text <?php echo $idx === 0 ? 'active' : ''; ?>" data-slide="<?php echo $idx; ?>">
                    <h1 class="hero-dest-script-title">
                        <?php echo htmlspecialchars($dest_name); ?> <span class="hero-dest-accent-serif">Tour Packages</span>
                    </h1>
                    <p class="hero-dest-subtitle">
                        <?php echo htmlspecialchars($dest_tagline); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (count($hero_destinations) > 1): ?>
        <div class="hero-slider-nav">
            <?php foreach ($hero_destinations as $idx => $h_dest): ?>
            <button class="hero-slide-dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-action="hero-slide-to" data-idx="<?php echo $idx; ?>" aria-label="Go to slide <?php echo $idx + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Multi-Parameter Smart Search Dock -->
    <div class="search-bar-wrapper">
    <div class="search-bar-container">
        <div class="search-bar">
            <div class="search-input-group">
                <label class="group-label" for="desktopSearchKeyword">Where To?</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">location_on</span>
                    <input type="text" id="desktopSearchKeyword" placeholder="Search destinations...">
                </div>
            </div>

            <div class="search-input-group">
                <label class="group-label" for="desktopSearchTheme">Tour Theme</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">explore</span>
                    <select id="desktopSearchTheme" data-change="submit-landing-search">
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
                <label class="group-label" for="desktopSearchDuration">Trip Duration</label>
                <div class="input-with-icon">
                    <span class="material-symbols-outlined">schedule</span>
                    <select id="desktopSearchDuration" data-change="submit-landing-search">
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
    <!-- Sanctuaries Showcase -->
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
                
                <div class="sanctuaries-controls">
                    <div class="trending-pills-group trending-pills-group--flush">
                        <button class="trending-pill active" data-action="filter-pkg" data-type="signature" data-val="domestic">
                            <span class="material-symbols-outlined pill-icon">landscape</span> Domestic
                        </button>
                        <button class="trending-pill" data-action="filter-pkg" data-type="signature" data-val="international">
                            <span class="material-symbols-outlined pill-icon">flight_takeoff</span> International
                        </button>
                    </div>

                    <div class="sanctuaries-nav-arrows">
                        <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="sanctuariesTrack" data-direction="-1" aria-label="Previous Destinations">
                            <span class="material-symbols-outlined nav-arrow-icon">chevron_left</span>
                        </button>
                        <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="sanctuariesTrack" data-direction="1" aria-label="Next Destinations">
                            <span class="material-symbols-outlined nav-arrow-icon">chevron_right</span>
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
                        $sd_img   = !empty($s_dest['card_image']) ? $s_dest['card_image'] : (!empty($s_dest['cover_image']) ? $s_dest['cover_image'] : 'assets/img/pkg.jpg');
                        $sd_name  = $s_dest['name'] ?? 'Darjeeling';
                        $sd_scope = $s_dest['scope'];
                        $sd_link  = "all-tours.php?destination=" . urlencode(trim($sd_name)) . "&type=" . $sd_scope;
                    ?>
                    <a href="<?php echo htmlspecialchars($sd_link); ?>" class="sanctuary-portrait-card signature-dest-card<?php echo $sd_scope === 'international' ? ' is-hidden' : ''; ?>" data-scope="<?php echo $sd_scope; ?>">
                        <img src="<?php echo htmlspecialchars($sd_img); ?>" alt="<?php echo htmlspecialchars($sd_name); ?>" class="sanctuary-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="sanctuary-card-overlay">
                            <h3 class="sanctuary-dest-name"><?php echo htmlspecialchars($sd_name); ?></h3>
                            <div class="sanctuary-dest-explore">
                                <span>Explore</span>
                                <span class="material-symbols-outlined explore-arrow">arrow_forward</span>
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
    $trending_packages = array_filter($packages, fn($p) => !empty($p['is_trending']) || !empty($p['is_featured']));
    if (!empty($trending_packages)):
    ?>
    <!-- Top Trending Tours -->
    <section class="trending-tours-section">
        <div class="trending-header-bar">
            <div class="trending-title-group">
                <h2 class="trending-title">
                    <span class="icon-fire">🔥</span> Top Trending Tours
                </h2>
                <p class="trending-subtitle">Explore our most sought-after holiday packages across India and around the globe.</p>
            </div>
            <div class="trending-controls">
                <div class="trending-pills-group">
                    <button class="trending-pill active" data-action="filter-pkg" data-type="trending" data-val="all">
                        <span class="material-symbols-outlined pill-icon">star</span> All Trending
                    </button>
                    <button class="trending-pill" data-action="filter-pkg" data-type="trending" data-val="domestic">
                        <span class="material-symbols-outlined pill-icon">landscape</span> Domestic
                    </button>
                    <button class="trending-pill" data-action="filter-pkg" data-type="trending" data-val="international">
                        <span class="material-symbols-outlined pill-icon">flight_takeoff</span> International
                    </button>
                </div>
                
                <div class="trending-nav-arrows">
                    <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="trendingTrack" data-direction="-1" aria-label="Previous">
                        <span class="material-symbols-outlined nav-arrow-icon">chevron_left</span>
                    </button>
                    <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="trendingTrack" data-direction="1" aria-label="Next">
                        <span class="material-symbols-outlined nav-arrow-icon">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="trending-track-wrapper">
            <div id="trendingTrack" class="trending-cards-track">
                <?php foreach ($trending_packages as $tp): 
                    $t_img      = !empty($tp['image_url']) ? $tp['image_url'] : 'assets/img/pkg.jpg';
                    $is_intl    = !empty($tp['is_international']) ? 'international' : 'domestic';
                    $type_badge = !empty($tp['is_international']) ? '✈️ International' : '🇮🇳 Domestic';
                    $t_price    = !empty($tp['price']) ? number_format((float)$tp['price']) : 'On Request';
                    $t_dur      = (!empty($tp['nights']) && !empty($tp['days'])) ? "{$tp['nights']}N / {$tp['days']}D" : (!empty($tp['days']) ? "{$tp['days']} Days" : 'Custom');
                ?>
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($tp['slug'] ?? ''); ?>" class="trending-tour-card" data-scope="<?php echo $is_intl; ?>">
                    <div class="trending-top-badges">
                        <span class="badge-trending-hot">🔥 TRENDING</span>
                        <span class="badge-trending-type"><?php echo $type_badge; ?></span>
                    </div>
                    <img src="<?php echo htmlspecialchars($t_img); ?>" alt="<?php echo htmlspecialchars($tp['title']); ?>" class="trending-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="trending-card-overlay">
                        <div class="trending-card-dest">
                            <span class="material-symbols-outlined dest-pin-icon">location_on</span>
                            <?php echo htmlspecialchars($tp['destination'] ?? 'India'); ?>
                        </div>
                        <h3 class="trending-card-title"><?php echo htmlspecialchars($tp['title']); ?></h3>
                        <div class="trending-card-duration">
                            🕒 <?php echo htmlspecialchars($t_dur); ?>
                        </div>
                        <div class="trending-card-footer">
                            <div class="trending-price-group">
                                <span class="trending-price-lbl">Starting From</span>
                                <span class="trending-price-val"><?php echo $t_price !== 'On Request' ? '₹' . $t_price : $t_price; ?></span>
                            </div>
                            <span class="btn-trending-explore">
                                Explore <span class="material-symbols-outlined explore-arrow">arrow_forward</span>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Holiday Collections By Theme -->
    <section class="theme-circle-section">
        <div class="theme-strip-heading">Explore Holiday Collections By Theme</div>
        <div class="theme-carousel-wrapper">
            <button class="emt-nav-btn emt-nav-prev" data-action="carousel-scroll" data-target="themeCarouselTrack" data-direction="-1" aria-label="Previous">
                <span class="material-symbols-outlined nav-arrow-lg">chevron_left</span>
            </button>
            
            <div id="themeCarouselTrack" class="theme-carousel-track">
                <?php 
                // $curated_pkg_themes is populated in public/packages.php controller
                $carousel_items = !empty($curated_pkg_themes) ? array_merge($curated_pkg_themes, $curated_pkg_themes, $curated_pkg_themes, $curated_pkg_themes) : [];

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

            <button class="emt-nav-btn emt-nav-next" data-action="carousel-scroll" data-target="themeCarouselTrack" data-direction="1" aria-label="Next">
                <span class="material-symbols-outlined nav-arrow-lg">chevron_right</span>
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
    $offer_packages = array_filter($packages, fn($p) => !empty($p['original_price']) && floatval($p['original_price']) > floatval($p['price']));
    if (empty($offer_packages)) $offer_packages = array_slice($packages, 0, 5);
    if (!empty($offer_packages)):
    ?>
    <!-- Exclusive Offers Showcase -->
    <section class="trending-tours-section trending-tours-section--offers">
        <div class="trending-header-bar">
            <div class="trending-title-group">
                <h2 class="trending-title">
                    <span class="icon-gift">🎁</span> Exclusive Holiday Offers
                </h2>
                <p class="trending-subtitle">Unbeatable limited-time discounts and curated seasonal savings across domestic and international sanctuaries.</p>
            </div>
            <div class="trending-controls">
                <div class="trending-pills-group trending-pills-group--emerald">
                    <button class="offer-pill active" data-action="filter-pkg" data-type="offers" data-val="all">
                        <span class="material-symbols-outlined pill-icon">bolt</span> All Deals
                    </button>
                    <button class="offer-pill" data-action="filter-pkg" data-type="offers" data-val="domestic">
                        <span class="material-symbols-outlined pill-icon">landscape</span> Domestic
                    </button>
                    <button class="offer-pill" data-action="filter-pkg" data-type="offers" data-val="international">
                        <span class="material-symbols-outlined pill-icon">flight_takeoff</span> International
                    </button>
                </div>
                
                <div class="trending-nav-arrows">
                    <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="offersTrack" data-direction="-1" aria-label="Previous">
                        <span class="material-symbols-outlined nav-arrow-icon">chevron_left</span>
                    </button>
                    <button class="sanctuaries-btn-nav" data-action="carousel-scroll" data-target="offersTrack" data-direction="1" aria-label="Next">
                        <span class="material-symbols-outlined nav-arrow-icon">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="trending-track-wrapper">
            <div id="offersTrack" class="trending-cards-track">
                <?php foreach ($offer_packages as $op): 
                    $o_img        = !empty($op['image_url']) ? $op['image_url'] : 'assets/img/pkg.jpg';
                    $o_scope      = !empty($op['is_international']) ? 'international' : 'domestic';
                    $o_badge      = !empty($op['is_international']) ? '✈️ International' : '🇮🇳 Domestic';
                    $o_price_num  = floatval($op['price']);
                    $o_orig_num   = !empty($op['original_price']) ? floatval($op['original_price']) : floatval($o_price_num * 1.35);
                    if ($o_orig_num <= $o_price_num) $o_orig_num = $o_price_num * 1.35;
                    $discount_pct = round((($o_orig_num - $o_price_num) / $o_orig_num) * 100);
                    $o_price      = number_format($o_price_num);
                    $o_orig_price = number_format($o_orig_num);
                    $o_dur        = (!empty($op['nights']) && !empty($op['days'])) ? "{$op['nights']}N / {$op['days']}D" : (!empty($op['days']) ? "{$op['days']} Days" : 'Custom');
                ?>
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($op['slug'] ?? ''); ?>" class="trending-tour-card offer-tour-card" data-scope="<?php echo $o_scope; ?>">
                    <div class="trending-top-badges">
                        <span class="badge-offer-discount">⚡ <?php echo $discount_pct; ?>% OFF</span>
                        <span class="badge-trending-type"><?php echo $o_badge; ?></span>
                    </div>
                    <img src="<?php echo htmlspecialchars($o_img); ?>" alt="<?php echo htmlspecialchars($op['title']); ?>" class="trending-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="trending-card-overlay">
                        <div class="trending-card-dest text-emerald-accent">
                            <span class="material-symbols-outlined dest-pin-icon">location_on</span>
                            <?php echo htmlspecialchars($op['destination'] ?? 'India'); ?>
                        </div>
                        <h3 class="trending-card-title"><?php echo htmlspecialchars($op['title']); ?></h3>
                        <div class="offer-card-duration">
                            🕒 <?php echo htmlspecialchars($o_dur); ?>
                        </div>
                        <div class="trending-card-footer">
                            <div class="offer-price-stack">
                                <span class="offer-price-orig">₹<?php echo $o_orig_price; ?></span>
                                <div class="offer-price-final-row">
                                    <span class="trending-price-val text-emerald-accent">₹<?php echo $o_price; ?></span>
                                    <span class="offer-per-person">/person</span>
                                </div>
                            </div>
                            <span class="btn-offer-claim">
                                View Deal <span class="material-symbols-outlined dest-pin-icon">local_offer</span>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Complete Portfolio CTA Showcase -->
    <section class="cta-portfolio-section">
        <div class="cta-portfolio-card">
            <div class="cta-portfolio-glow"></div>
            
            <span class="cta-portfolio-badge">
                <span class="material-symbols-outlined dest-pin-icon">explore</span> Complete Tour Inventory
            </span>
            
            <h2 class="cta-portfolio-heading">
                Ready to Find Your <span class="gold-italic">Dream Sanctuary?</span>
            </h2>
            
            <p class="cta-portfolio-desc">
                Browse our complete catalog of curated luxury holidays, customized itineraries, and guaranteed fixed group departures with interactive budget and duration filters.
            </p>
            
            <a href="all-tours.php" class="cta-portfolio-btn">
                ✨ Explore All Available Tours <span class="material-symbols-outlined arrow-lg">arrow_forward</span>
            </a>
        </div>
    </section>

    <script src="js/modules/packages.js?v=<?= time() ?>" defer></script>

<?php include '../includes/footer.php'; ?>
