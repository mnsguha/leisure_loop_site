<?php
$skipMobileOnboarding = isset($_GET['onboarding']) && $_GET['onboarding'] === 'skip';
include '../includes/header.php';

// Find the first slide URL for fallback
$mobile_hero_bg = 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000';
if (!empty($hero_slides)) {
    $firstSlide = $hero_slides[0];
    $slideUrl = $firstSlide['url'];
    if (strpos($slideUrl, 'http') !== 0) {
        $slideUrl = 'admin/uploads/' . $slideUrl;
    }
    $mobile_hero_bg = $slideUrl;
}
?>

<?php if (!$skipMobileOnboarding): ?>
    <div class="mobile-splash-view" id="mobile-splash-view">
        <?php include '../includes/mobile_splash.php'; ?>
    </div>
<?php endif; ?>

<div class="mobile-home-view" id="mobile-home-view">

    <!-- 1. Flipkart-Style Sticky App Header -->
    <header class="mobile-app-header" id="mobileAppHeader">
        <div class="mobile-header-glow"></div>
        
        <!-- Brand Logo Row -->
        <div class="mobile-brand-row">
            <a href="index.php" class="mobile-brand-link" aria-label="Leisure Loop Home">
                <img class="mobile-brand-icon" src="assets/img/mobile_icon.png" alt="Leisure Loop Icon">
                <img class="mobile-brand-text" src="assets/img/mobile_text.png" alt="Leisure Loop Trip">
            </a>
        </div>

        <!-- Location Selector Trigger -->
        <button type="button" class="mobile-location-trigger" data-action="open-location" aria-label="Select City">
            <span class="material-symbols-outlined mobile-icon--sm">location_on</span>
            <span class="mobile-location-text" id="currentLocationText">Location not set</span>
            <span class="mobile-location-prompt">Select your location &gt;</span>
        </button>

        <!-- Search Bar Trigger -->
        <div class="mobile-search-dock">
            <button type="button" class="mobile-search-trigger" data-action="open-search" aria-label="Open Search">
                <span class="material-symbols-outlined mobile-icon--md">search</span>
                <span class="mobile-search-placeholder">Search for destinations...</span>
                <span class="material-symbols-outlined mobile-icon--md mobile-text-muted">mic</span>
            </button>
        </div>

        <!-- Horizontal Service Navigation Tabs -->
        <nav class="mobile-category-tabs" aria-label="Category Navigation">
            <a href="packages.php?filter=offers" class="mobile-tab-item active">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon mobile-text-gold">local_offer</span>
                </div>
                <span class="mobile-tab-label">Offers</span>
            </a>
            <a href="packages.php" class="mobile-tab-item">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon">luggage</span>
                </div>
                <span class="mobile-tab-label">Tours</span>
            </a>
            <a href="packages.php?type=fixed" class="mobile-tab-item">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon">event_available</span>
                </div>
                <span class="mobile-tab-label">Fixed Dept.</span>
            </a>
            <a href="hotels.php" class="mobile-tab-item">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon">bed</span>
                </div>
                <span class="mobile-tab-label">Hotels</span>
            </a>
            <a href="cabs.php" class="mobile-tab-item">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon">local_taxi</span>
                </div>
                <span class="mobile-tab-label">Cabs</span>
            </a>
            <a href="packages.php?theme=events" class="mobile-tab-item">
                <div class="mobile-tab-icon-wrap">
                    <span class="material-symbols-outlined mobile-tab-icon">celebration</span>
                </div>
                <span class="mobile-tab-label">Events</span>
            </a>
        </nav>
    </header>

    <main class="mobile-home-main">
        <!-- 2. Hero Coverflow Carousel -->
        <section class="mobile-hero-section">
            <div class="swiper hero-coverflow">
                <div class="swiper-wrapper">
                    <?php 
                    $carousel_packages = [];
                    if (isset($pdo)) {
                        $carousel_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
                    }
                    foreach ($carousel_packages as $pkg):
                        $img = !empty($pkg['image_url']) ? $pkg['image_url'] : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800';
                    ?>
                    <div class="swiper-slide hero-coverflow-slide">
                        <img src="<?= htmlspecialchars($img); ?>" class="hero-coverflow-img" alt="<?= htmlspecialchars($pkg['title']); ?>">
                        <div class="hero-coverflow-overlay"></div>
                        <a href="package-detail.php?slug=<?= htmlspecialchars($pkg['slug']); ?>" class="hero-coverflow-link" aria-label="View <?= htmlspecialchars($pkg['title']); ?>"></a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <!-- 3. The Curated Collection (Domestic Packages) -->
        <?php 
            $featured_packages = [];
            if (isset($pdo)) {
                $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 0 ORDER BY id DESC LIMIT 6")->fetchAll();
            }
        ?>
        <section class="m-home-section" id="featured">
            <div class="m-section-head">
                <div>
                    <span class="m-section-eyebrow">The Curated Collection</span>
                    <h2 class="m-section-title">of the <span class="text-gold-italic">Season.</span></h2>
                </div>
                <a href="packages.php?type=domestic" class="m-see-all-link">
                    See All 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($featured_packages as $pkg): ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug']) ?>" class="app-pkg-card">
                    <div class="app-pkg-img" data-bg="<?= htmlspecialchars($pkg['image_url'] ?: 'assets/img/pkg.jpg'); ?>">
                        <div class="app-pkg-badge">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                $days = ($itinerary && is_array($itinerary)) ? count($itinerary) : 3;
                                echo max(1, $days - 1) . "N / " . $days . "D";
                            }
                            ?>
                        </div>
                        <div class="app-pkg-content">
                            <span class="app-pkg-dest"><?= htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
                            <h3 class="app-pkg-title"><?= htmlspecialchars($pkg['title']); ?></h3>
                            <div class="app-pkg-price">FROM &#8377;<?= number_format($pkg['price']); ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 4. Signature Terrains (Destinations) -->
        <?php
            $destinations_list = [];
            if (isset($pdo)) {
                $destinations_list = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE d.is_active = 1 ORDER BY d.display_order ASC, d.created_at DESC")->fetchAll();
            }
            $domestic = array_filter($destinations_list, fn($d) => strtolower($d['category']) === 'domestic');
            $international = array_filter($destinations_list, fn($d) => strtolower($d['category']) === 'international');
        ?>
        <section class="m-home-section" id="mobile-destinations">
            <div class="m-dest-card-wrapper">
                <div class="m-section-head">
                    <div>
                        <span class="m-section-eyebrow">Curated Escapes</span>
                        <h2 class="m-section-title">Signature <span class="text-gold-italic">Terrains</span></h2>
                    </div>
                    <div class="m-icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                </div>
                
                <!-- Domestic Subsection -->
                <div class="m-subhead">
                    <span class="m-subhead-bar"></span>
                    <h3 class="m-subhead-title">Domestic</h3>
                </div>
                <div class="dest-mini-carousel">
                    <?php foreach ($domestic as $dest): ?>
                    <a href="destination-details.php?slug=<?= urlencode($dest['slug']) ?>" class="dest-mini-card">
                        <div class="dest-mini-img" data-bg="<?= htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>"></div>
                        <div class="dest-mini-info">
                            <h4 class="dest-mini-name"><?= htmlspecialchars($dest['name']); ?></h4>
                            <span class="dest-mini-count"><?= (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- International Subsection -->
                <div class="m-subhead">
                    <span class="m-subhead-bar"></span>
                    <h3 class="m-subhead-title">International</h3>
                </div>
                <div class="dest-mini-carousel">
                    <?php foreach ($international as $dest): ?>
                    <a href="destination-details.php?slug=<?= urlencode($dest['slug']) ?>" class="dest-mini-card">
                        <div class="dest-mini-img" data-bg="<?= htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>"></div>
                        <div class="dest-mini-info">
                            <h4 class="dest-mini-name"><?= htmlspecialchars($dest['name']); ?></h4>
                            <span class="dest-mini-count"><?= (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- 5. Global Escapes -->
        <?php 
            $global_packages = [];
            if (isset($pdo)) {
                $global_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 1 ORDER BY id DESC LIMIT 6")->fetchAll();
            }
        ?>
        <?php if (!empty($global_packages)): ?>
        <section class="m-home-section" id="global-escapes">
            <div class="m-section-head">
                <div>
                    <span class="m-section-eyebrow">World Collection</span>
                    <h2 class="m-section-title">Global <span class="text-gold-italic">Escapes.</span></h2>
                </div>
                <a href="packages.php?type=international" class="m-see-all-link">
                    See All 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($global_packages as $pkg): ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug']) ?>" class="app-pkg-card">
                    <div class="app-pkg-img" data-bg="<?= htmlspecialchars($pkg['image_url'] ?: 'assets/img/pkg.jpg'); ?>">
                        <div class="app-pkg-badge">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                echo "CUSTOM";
                            }
                            ?>
                        </div>
                        <div class="app-pkg-content">
                            <span class="app-pkg-dest"><?= htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
                            <h3 class="app-pkg-title"><?= htmlspecialchars($pkg['title']); ?></h3>
                            <div class="app-pkg-price">FROM &#8377;<?= number_format($pkg['price']); ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 6. Promotional Marquee Banners -->
        <?php
            $promos = [];
            if (isset($pdo)) {
                try {
                    $promos = $pdo->query("SELECT * FROM advertisements WHERE page_type IN ('home', 'home_middle') AND is_active = 1")->fetchAll();
                } catch (Exception $e) {}
            }
        ?>
        <?php if (!empty($promos)): ?>
        <section class="m-home-section m-promo-section">
            <div class="promo-carousel" id="promoCarousel">
                <?php foreach ($promos as $index => $promo): 
                    $bg_img = !empty($promo['image_url']) ? htmlspecialchars($promo['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
                ?>
                <div class="promo-card">
                    <div class="promo-card-bg" data-bg="<?= $bg_img; ?>"></div>
                    <div class="promo-card-overlay"></div>
                    <div class="promo-card-body">
                        <div class="promo-badge">
                            <span class="promo-badge-text">LEISURE LOOP TRIP</span>
                        </div>
                        <h3 class="promo-title"><?= htmlspecialchars($promo['title']); ?></h3>
                        <a href="contact.php" class="promo-btn">
                            <?= htmlspecialchars($promo['btn_text'] ?: 'Book Now'); ?> &rarr;
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="promo-dots" id="promoDots">
                <?php foreach ($promos as $index => $promo): ?>
                <span class="promo-dot <?= $index === 0 ? 'active' : ''; ?>" data-index="<?= $index; ?>"></span>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 7. Curated Themes Grid -->
        <?php
        $theme_counts_mobile = [];
        $theme_meta_mobile = [];
        $default_bg_mobile = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
        $default_icon_mobile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';

        if (isset($pdo)) {
            try {
                $all_pkgs_mobile = $pdo->query("SELECT tour_type FROM packages WHERE is_active = 1")->fetchAll();
                foreach ($all_pkgs_mobile as $row) {
                    if (!empty($row['tour_type'])) {
                        $tags = array_map('trim', explode(',', $row['tour_type']));
                        foreach ($tags as $tag) {
                            $normalized = ucwords(strtolower($tag));
                            $normalized = trim(preg_replace('/\b(tours|tour)\b/i', '', $normalized));
                            if ($normalized !== '') {
                                $theme_counts_mobile[$normalized] = ($theme_counts_mobile[$normalized] ?? 0) + 1;
                            }
                        }
                    }
                }
                
                $db_categories_mobile = $pdo->query("SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                foreach ($db_categories_mobile as $cat) {
                    $theme_meta_mobile[$cat['name']] = [
                        'bg' => !empty($cat['image_url']) ? $cat['image_url'] : $default_bg_mobile,
                        'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon_mobile,
                    ];
                }
            } catch (Exception $e) {}
        }
        ?>
        <?php if (!empty($theme_counts_mobile)): ?>
        <section class="m-home-section mobile-theme-section" id="mobile-curated-themes">
            <div class="mobile-theme-header">
                <span class="mobile-theme-kicker">CURATED THEMES</span>
                <h2 class="mobile-theme-title">Bespoke Travel <span class="text-gold-italic">Experiences.</span></h2>
            </div>

            <!-- Pill Navigation Strip -->
            <div class="mobile-theme-pills-wrap">
                <div class="mobile-theme-pills">
                    <?php foreach ($theme_counts_mobile as $name => $count): 
                        $meta = $theme_meta_mobile[$name] ?? ['icon' => $default_icon_mobile];
                    ?>
                    <a href="packages.php?theme=<?= urlencode($name); ?>" class="mobile-theme-pill">
                        <span class="mobile-theme-pill-icon"><?= $meta['icon']; ?></span>
                        <span class="mobile-theme-pill-name"><?= htmlspecialchars($name); ?></span>
                        <span class="mobile-theme-pill-count">(<?= $count; ?>)</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Circular Grid -->
            <div class="mobile-theme-grid">
                <?php foreach ($theme_counts_mobile as $name => $count): 
                    $meta = $theme_meta_mobile[$name] ?? ['bg' => $default_bg_mobile];
                ?>
                <a href="packages.php?theme=<?= urlencode($name); ?>" class="mobile-theme-card">
                    <div class="mobile-theme-card-image">
                        <div class="mobile-theme-card-image-fill" data-bg="<?= htmlspecialchars($meta['bg']); ?>"></div>
                    </div>
                    <span class="mobile-theme-card-name"><?= htmlspecialchars($name); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 8. Genuine Moments Gallery Marquee -->
        <?php
            $gallery_images_mobile = [];
            if (isset($pdo)) {
                try {
                    $gallery_images_mobile = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY display_order ASC LIMIT 16")->fetchAll();
                } catch (Exception $e) {}
            }
            if (empty($gallery_images_mobile)) {
                $gallery_images_mobile = [
                    ['image_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800'],
                    ['image_url' => 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800'],
                    ['image_url' => 'https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=800'],
                    ['image_url' => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800'],
                ];
            }
            $half = ceil(count($gallery_images_mobile) / 2);
            $top_track = array_slice($gallery_images_mobile, 0, $half);
            $bottom_track = array_slice($gallery_images_mobile, $half);
        ?>
        <section class="m-home-section mobile-gallery-section">
            <div class="m-gallery-head">
                <h2 class="m-section-title">Genuine Moments,<br><span class="text-gold-italic">Unforgettable Journeys.</span></h2>
                <p class="m-section-subtitle">Glimpse into the extraordinary adventures of our travelers.</p>
            </div>

            <!-- Top Left Marquee -->
            <div class="mobile-gallery-marquee-wrapper">
                <div class="mobile-gallery-marquee m-track-left">
                    <div class="mobile-gallery-content">
                        <?php foreach($top_track as $img): ?>
                        <div class="m-smile-card"><div class="m-smile-img" data-bg="<?= htmlspecialchars($img['image_url']); ?>"></div></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mobile-gallery-content" aria-hidden="true">
                        <?php foreach($top_track as $img): ?>
                        <div class="m-smile-card"><div class="m-smile-img" data-bg="<?= htmlspecialchars($img['image_url']); ?>"></div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Right Marquee -->
            <div class="mobile-gallery-marquee-wrapper">
                <div class="mobile-gallery-marquee m-track-right">
                    <div class="mobile-gallery-content">
                        <?php foreach($bottom_track as $img): ?>
                        <div class="m-smile-card"><div class="m-smile-img" data-bg="<?= htmlspecialchars($img['image_url']); ?>"></div></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mobile-gallery-content" aria-hidden="true">
                        <?php foreach($bottom_track as $img): ?>
                        <div class="m-smile-card"><div class="m-smile-img" data-bg="<?= htmlspecialchars($img['image_url']); ?>"></div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="m-gallery-cta">
                <a href="gallery.php" class="btn-outline-gold">Explore Gallery</a>
            </div>
        </section>

        <!-- 9. Upcoming Fixed Departures -->
        <?php if (!empty($fixed_departures)): ?>
        <section class="m-home-section" id="mobile-fixed-departures">
            <div class="m-fixed-dep-card">
                <div class="m-fixed-dep-head">
                    <h2 class="m-fixed-dep-title">Upcoming Fixed Departures</h2>
                </div>
                <div class="m-fixed-dep-carousel">
                    <?php foreach ($fixed_departures as $fd): 
                        $img = preg_match('/^https?:\/\//i', $fd['image_url']) ? $fd['image_url'] : ltrim($fd['image_url'], '/');
                        $status = $fd['status'];
                        $date_str = date('M d', strtotime($fd['start_date'])) . ' - ' . date('M d', strtotime($fd['end_date']));
                    ?>
                    <a href="package-detail.php?slug=<?= htmlspecialchars($fd['package_slug']); ?>&fd=<?= (int)$fd['id']; ?>" class="fixed-dep-item">
                        <div class="fixed-dep-img-wrap">
                            <div class="fixed-dep-img" data-bg="<?= htmlspecialchars($img); ?>"></div>
                            <div class="fixed-dep-status <?= $status === 'Sold Out' ? 'status-sold-out' : ''; ?>">
                                <?= htmlspecialchars($status); ?>
                            </div>
                        </div>
                        <div class="fixed-dep-info">
                            <h3 class="fixed-dep-name"><?= htmlspecialchars($fd['package_title']); ?></h3>
                            <div class="fixed-dep-date"><?= $date_str; ?></div>
                            <div class="fixed-dep-price">From &#8377;<?= number_format($fd['price']); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- 10. Stats Strip -->
        <section class="m-home-section stats-strip">
            <div class="stats-carousel">
                <div class="stats-item">
                    <span class="stats-num">500+</span>
                    <span class="stats-label">Bespoke Journeys</span>
                </div>
                <div class="stats-item">
                    <span class="stats-num">12+</span>
                    <span class="stats-label">Years Excellence</span>
                </div>
                <div class="stats-item">
                    <span class="stats-num">4.9</span>
                    <span class="stats-label">Client Rating</span>
                </div>
                <div class="stats-item">
                    <span class="stats-num">24/7</span>
                    <span class="stats-label">Priority Support</span>
                </div>
            </div>
        </section>
    </main>

    <!-- Search Bottom Sheet Modal (Rule 11: Z-Index 200/205) -->
    <div class="search-modal-overlay" id="searchModalOverlay" data-action="close-search" role="button" aria-label="Close Search"></div>
    <div class="search-modal-content" id="searchModal" aria-hidden="true">
        <div class="modal-sheet-header">
            <div>
                <span class="sheet-eyebrow">BESPOKE TRAVEL</span>
                <h2 class="sheet-title">Plan Your Journey</h2>
            </div>
            <button type="button" class="sheet-close-btn" data-action="close-search" aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-sheet-body">
            <form action="api-submit-lead.php" method="POST" class="modal-sheet-form">
                <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="source" value="mobile_home_search">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">explore</span>
                    <input class="form-input" placeholder="Where do you want to go?" type="text" name="destination" required>
                </div>
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">calendar_month</span>
                    <input class="form-input" placeholder="Travel Date" type="text" name="date" required>
                </div>
                <button type="submit" class="btn-primary btn-search-submit">
                    Search Trips
                </button>
            </form>
        </div>
    </div>

    <!-- Location Bottom Sheet Modal (Rule 11: Z-Index 200/205) -->
    <div class="search-modal-overlay" id="locationModalOverlay" data-action="close-location" role="button" aria-label="Close Location Picker"></div>
    <div class="search-modal-content" id="locationModal" aria-hidden="true">
        <div class="modal-sheet-header">
            <div>
                <span class="sheet-eyebrow">CURRENT CITY</span>
                <h2 class="sheet-title">Select Location</h2>
            </div>
            <button type="button" class="sheet-close-btn" data-action="close-location" aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-sheet-body">
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">search</span>
                <input class="form-input" placeholder="Search for your city..." type="text" id="citySearchInput">
            </div>
            
            <button type="button" class="btn-use-location" data-action="use-location" id="currentLocationBtn">
                <div class="location-icon-bubble">
                    <span class="material-symbols-outlined">my_location</span>
                </div>
                <div class="location-text-col">
                    <div class="location-title">Use my current location</div>
                    <div class="location-sub">Detect automatically</div>
                </div>
            </button>

            <div class="city-picker-list" id="cityList">
                <div class="city-picker-item" data-action="select-city" data-city="New Delhi">
                    <span class="material-symbols-outlined">location_city</span>
                    <span>New Delhi, India</span>
                </div>
                <div class="city-picker-item" data-action="select-city" data-city="Mumbai">
                    <span class="material-symbols-outlined">location_city</span>
                    <span>Mumbai, India</span>
                </div>
                <div class="city-picker-item" data-action="select-city" data-city="Kolkata">
                    <span class="material-symbols-outlined">location_city</span>
                    <span>Kolkata, India</span>
                </div>
                <div class="city-picker-item" data-action="select-city" data-city="Bangalore">
                    <span class="material-symbols-outlined">location_city</span>
                    <span>Bangalore, India</span>
                </div>
                <div class="city-picker-item" data-action="select-city" data-city="Chennai">
                    <span class="material-symbols-outlined">location_city</span>
                    <span>Chennai, India</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Universal Bottom Navigation (Rule 11: Z-Index 45) -->
    <?php include __DIR__ . '/mobile_bottom_nav.php'; ?>

</div> <!-- End mobile-home-view -->

<?php
$extra_scripts = '
<script src="js/modules/mobile-splash.js?v=' . time() . '" defer></script>
<script src="js/modules/mobile-home.js?v=' . time() . '" defer></script>
';
include __DIR__ . '/mobile_footer.php';
?>
