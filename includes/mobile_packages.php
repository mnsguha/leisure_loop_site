<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title ?? 'Curated Packages | Leisure Loop Trip') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="m-page-body mob-pkg-body">

    <?php
    $mobile_header_title = 'Tour Packages';
    $mobile_active_nav   = 'packages';
    $back_url            = 'index.php?view=mobile';
    include __DIR__ . '/mobile_header.php';
    ?>

    <main class="m-pkg-main">

        <!-- 1. Auto-Scrolling Hero Section (matches desktop) -->
        <?php if (!empty($hero_destinations)): ?>
        <section class="m-pkg-hero" id="mobHeroSection" aria-label="Featured destinations carousel">
            <div class="m-pkg-hero__track" id="mobHeroTrack">
                <?php foreach ($hero_destinations as $idx => $h_dest): 
                    $bg_img = !empty($h_dest['cover_image']) ? $h_dest['cover_image'] : (!empty($h_dest['card_image']) ? $h_dest['card_image'] : 'assets/img/pkg.jpg');
                    $bg_img = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $bg_img) ? $bg_img : 'images/dest/' . ltrim($bg_img, '/');
                ?>
                <div class="m-pkg-hero__slide <?= $idx === 0 ? 'active' : '' ?>" data-slide="<?= $idx ?>">
                    <img src="<?= htmlspecialchars($bg_img) ?>" alt="<?= htmlspecialchars($h_dest['name']) ?> Tour Packages" class="m-pkg-hero__img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-pkg-hero__overlay"></div>
                    <div class="m-pkg-hero__content">
                        <span class="m-pkg-hero__badge-row">
                            <span class="m-pkg-hero__badge-dot"></span> Curated Signature Holiday Packages
                        </span>
                        <h2 class="m-pkg-hero__title">
                            <?= htmlspecialchars($h_dest['name']) ?> <span class="m-pkg-hero__title-sub">Tour Packages</span>
                        </h2>
                        <?php if (!empty($h_dest['tagline'])): ?>
                            <p class="m-pkg-hero__tagline"><?= htmlspecialchars($h_dest['tagline']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($hero_destinations) > 1): ?>
            <div class="m-hero-dots" role="tablist" aria-label="Slide navigation">
                <?php foreach ($hero_destinations as $idx => $s): ?>
                    <span class="m-hero-dot <?= $idx === 0 ? 'active' : '' ?>" data-dot="<?= $idx ?>" role="tab" aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $idx + 1 ?>"></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- 2. Search Dock -->
        <div class="m-pkg-search-wrap">
            <label for="mobSearchInput" class="sr-only">Search destinations or themes</label>
            <div class="m-search-pill m-search-pill--pkg">
                <span class="material-symbols-outlined m-search-icon" aria-hidden="true">search</span>
                <input type="text" id="mobSearchInput" placeholder="Enter your dream destination or theme..." class="m-search-input m-search-input--pkg">
                <button type="button" id="mobSearchClear" class="m-search-clear is-hidden" aria-label="Clear search">
                    <span class="material-symbols-outlined m-search-icon--sm" aria-hidden="true">close</span>
                </button>
            </div>
        </div>

        <!-- 3. Signature Destinations (matches desktop: portrait cards, horizontal scroll, arrows, NO Explore text) -->
        <?php if (!empty($domestic_destinations) || !empty($international_destinations)): ?>
        <section class="m-pkg-section" aria-label="Signature destinations">
            <div class="m-pkg-carousel-header">
                <h3 class="m-pkg-section__title" >Signature Destinations</h3>
                <p class="m-pkg-section__subtitle">Immerse yourself in breathtaking mountain retreats, misty valley tea gardens, and timeless cultural realms across India.</p>
                <div class="m-pkg-carousel-controls">
                    <div class="m-pills-row" role="tablist" aria-label="Destination type filter">
                        <button type="button" class="m-pill js-sig-tab m-pill--active" data-target="mob-sig-all" role="tab" aria-selected="true" aria-controls="mob-sig-all">
                            <span class="material-symbols-outlined m-pill-icon">public</span> All Destinations
                        </button>
                        <button type="button" class="m-pill js-sig-tab m-pill--inactive" data-target="mob-sig-domestic" role="tab" aria-selected="false" aria-controls="mob-sig-domestic">
                            <span class="material-symbols-outlined m-pill-icon">landscape</span> Domestic
                        </button>
                        <button type="button" class="m-pill js-sig-tab m-pill--inactive" data-target="mob-sig-international" role="tab" aria-selected="false" aria-controls="mob-sig-international">
                            <span class="material-symbols-outlined m-pill-icon">flight_takeoff</span> International
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- All Destinations -->
            <div class="m-pkg-carousel-track js-sig-track" id="mob-sig-all" role="tabpanel">
                <?php 
                $all_sig = array_merge(
                    array_map(function($d) { $d['scope'] = 'domestic'; return $d; }, $domestic_destinations),
                    array_map(function($d) { $d['scope'] = 'international'; return $d; }, $international_destinations)
                );
                foreach ($all_sig as $dest): 
                    $img = !empty($dest['card_image']) ? $dest['card_image'] : 'assets/img/pkg.jpg';
                    $img = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $img) ? $img : 'images/dest/' . ltrim($img, '/');
                    $dest_link = 'all-tours.php?destination=' . urlencode(trim($dest['name'])) . '&type=' . urlencode($dest['scope']) . '&view=mobile';
                ?>
                    <a href="<?= htmlspecialchars($dest_link) ?>" class="m-pkg-carousel-card m-sig-dest-card" data-scope="<?= htmlspecialchars($dest['scope']) ?>" aria-label="Explore <?= htmlspecialchars($dest['name']) ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" class="m-sig-dest-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="m-sig-dest-card__overlay">
                            <h4 class="m-sig-dest-card__name"><?= htmlspecialchars($dest['name']) ?></h4>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Domestic -->
            <div class="m-pkg-carousel-track js-sig-track is-hidden" id="mob-sig-domestic" role="tabpanel" aria-hidden="true">
                <?php foreach ($domestic_destinations as $dest): 
                    $img = !empty($dest['card_image']) ? $dest['card_image'] : 'assets/img/pkg.jpg';
                    $img = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $img) ? $img : 'images/dest/' . ltrim($img, '/');
                    $dest_link = 'all-tours.php?destination=' . urlencode(trim($dest['name'])) . '&type=domestic&view=mobile';
                ?>
                    <a href="<?= htmlspecialchars($dest_link) ?>" class="m-pkg-carousel-card m-sig-dest-card" data-scope="domestic" aria-label="Explore <?= htmlspecialchars($dest['name']) ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" class="m-sig-dest-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="m-sig-dest-card__overlay">
                            <h4 class="m-sig-dest-card__name"><?= htmlspecialchars($dest['name']) ?></h4>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- International -->
            <div class="m-pkg-carousel-track js-sig-track is-hidden" id="mob-sig-international" role="tabpanel" aria-hidden="true">
                <?php foreach ($international_destinations as $dest): 
                    $img = !empty($dest['card_image']) ? $dest['card_image'] : 'assets/img/pkg.jpg';
                    $img = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $img) ? $img : 'images/dest/' . ltrim($img, '/');
                    $dest_link = 'all-tours.php?destination=' . urlencode(trim($dest['name'])) . '&type=international&view=mobile';
                ?>
                    <a href="<?= htmlspecialchars($dest_link) ?>" class="m-pkg-carousel-card m-sig-dest-card" data-scope="international" aria-label="Explore <?= htmlspecialchars($dest['name']) ?>">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" class="m-sig-dest-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="m-sig-dest-card__overlay">
                            <h4 class="m-sig-dest-card__name"><?= htmlspecialchars($dest['name']) ?></h4>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 4. Top Trending Tours (matches desktop: portrait cards, horizontal scroll, arrows) -->
        <?php 
        $trending_mobile = array_filter($packages_mobile, fn($p) => !empty($p['is_trending']) || !empty($p['is_featured']));
        if (!empty($trending_mobile)): 
        ?>
        <section class="m-pkg-section" aria-label="Top trending tours">
            <div class="m-pkg-carousel-header">
                <h3 class="m-pkg-section__title">
                    Top Trending Tours
                </h3>
                <p class="m-pkg-section__subtitle">Explore our most sought-after holiday packages across India and around the globe.</p>
                <div class="m-pkg-carousel-controls">
                    <div class="m-pills-row" role="tablist" aria-label="Trending filter">
                        <button type="button" class="m-pill js-trend-tab m-pill--active" data-target="mob-trend-all" role="tab" aria-selected="true" aria-controls="mob-trend-all">
                            <span class="material-symbols-outlined m-pill-icon">star</span> All Trending
                        </button>
                        <button type="button" class="m-pill js-trend-tab m-pill--inactive" data-target="mob-trend-domestic" role="tab" aria-selected="false" aria-controls="mob-trend-domestic">
                            <span class="material-symbols-outlined m-pill-icon">landscape</span> Domestic
                        </button>
                        <button type="button" class="m-pill js-trend-tab m-pill--inactive" data-target="mob-trend-international" role="tab" aria-selected="false" aria-controls="mob-trend-international">
                            <span class="material-symbols-outlined m-pill-icon">flight_takeoff</span> International
                        </button>
                    </div>
                </div>
            </div>

            <!-- All Trending -->
            <div class="m-pkg-carousel-track js-trend-track" id="mob-trend-all" role="tabpanel">
                <?php foreach ($trending_mobile as $pkg): 
                    $t_img = !empty($pkg['image_url']) ? $pkg['image_url'] : 'assets/img/pkg.jpg';
                    $pkgImg = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $t_img) ? $t_img : 'images/dest/' . ltrim($t_img, '/');
                    $days = (int)($pkg['days'] ?? 4);
                    $nights = (int)($pkg['nights'] ?? ($days > 1 ? $days - 1 : 1));
                    $price = (float)($pkg['price'] ?? 0);
                    $is_intl = !empty($pkg['is_international']) ? 'international' : 'domestic';
                    $type_badge = $is_intl === 'international' ? '✈️ International' : '🇮🇳 Domestic';
                ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-trend-card" data-scope="<?= $is_intl ?>" aria-label="<?= htmlspecialchars($pkg['title']) ?> - <?= $nights ?>N/<?= $days ?>D from ₹<?= number_format($price) ?>">
                    <div class="m-trend-card__badges">
                        <span class="m-trend-card__badge-hot">🔥 TRENDING</span>
                        <span class="m-trend-card__badge-type"><?= $type_badge ?></span>
                    </div>
                    <img src="<?= htmlspecialchars($pkgImg) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="m-trend-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-trend-card__overlay">
                        <div class="m-trend-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($pkg['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-trend-card__title"><?= htmlspecialchars($pkg['title']) ?></h4>
                        <div class="m-trend-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-trend-card__footer">
                            <div class="m-trend-card__price-group">
                                <span class="m-trend-card__price-lbl">Starting From</span>
                                <span class="m-trend-card__price-val">₹<?= number_format($price) ?></span>
                            </div>
                            <span class="m-trend-card__explore">Explore <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Domestic Trending -->
            <div class="m-pkg-carousel-track js-trend-track is-hidden" id="mob-trend-domestic" role="tabpanel" aria-hidden="true">
                <?php foreach ($trending_mobile as $pkg): 
                    if (!empty($pkg['is_international'])) continue;
                    $t_img = !empty($pkg['image_url']) ? $pkg['image_url'] : 'assets/img/pkg.jpg';
                    $pkgImg = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $t_img) ? $t_img : 'images/dest/' . ltrim($t_img, '/');
                    $days = (int)($pkg['days'] ?? 4);
                    $nights = (int)($pkg['nights'] ?? ($days > 1 ? $days - 1 : 1));
                    $price = (float)($pkg['price'] ?? 0);
                ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-trend-card" data-scope="domestic" aria-label="<?= htmlspecialchars($pkg['title']) ?> - <?= $nights ?>N/<?= $days ?>D from ₹<?= number_format($price) ?>">
                    <div class="m-trend-card__badges">
                        <span class="m-trend-card__badge-hot">🔥 TRENDING</span>
                        <span class="m-trend-card__badge-type">🇮🇳 Domestic</span>
                    </div>
                    <img src="<?= htmlspecialchars($pkgImg) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="m-trend-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-trend-card__overlay">
                        <div class="m-trend-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($pkg['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-trend-card__title"><?= htmlspecialchars($pkg['title']) ?></h4>
                        <div class="m-trend-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-trend-card__footer">
                            <div class="m-trend-card__price-group">
                                <span class="m-trend-card__price-lbl">Starting From</span>
                                <span class="m-trend-card__price-val">₹<?= number_format($price) ?></span>
                            </div>
                            <span class="m-trend-card__explore">Explore <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- International Trending -->
            <div class="m-pkg-carousel-track js-trend-track is-hidden" id="mob-trend-international" role="tabpanel" aria-hidden="true">
                <?php foreach ($trending_mobile as $pkg): 
                    if (empty($pkg['is_international'])) continue;
                    $t_img = !empty($pkg['image_url']) ? $pkg['image_url'] : 'assets/img/pkg.jpg';
                    $pkgImg = preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $t_img) ? $t_img : 'images/dest/' . ltrim($t_img, '/');
                    $days = (int)($pkg['days'] ?? 4);
                    $nights = (int)($pkg['nights'] ?? ($days > 1 ? $days - 1 : 1));
                    $price = (float)($pkg['price'] ?? 0);
                ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-trend-card" data-scope="international" aria-label="<?= htmlspecialchars($pkg['title']) ?> - <?= $nights ?>N/<?= $days ?>D from ₹<?= number_format($price) ?>">
                    <div class="m-trend-card__badges">
                        <span class="m-trend-card__badge-hot">🔥 TRENDING</span>
                        <span class="m-trend-card__badge-type">✈️ International</span>
                    </div>
                    <img src="<?= htmlspecialchars($pkgImg) ?>" alt="<?= htmlspecialchars($pkg['title']) ?>" class="m-trend-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-trend-card__overlay">
                        <div class="m-trend-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($pkg['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-trend-card__title"><?= htmlspecialchars($pkg['title']) ?></h4>
                        <div class="m-trend-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-trend-card__footer">
                            <div class="m-trend-card__price-group">
                                <span class="m-trend-card__price-lbl">Starting From</span>
                                <span class="m-trend-card__price-val">₹<?= number_format($price) ?></span>
                            </div>
                            <span class="m-trend-card__explore">Explore <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 5. Holiday Themes (matches desktop: circle cards, horizontal scroll, arrows) -->
        <?php if (!empty($curated_pkg_themes)): ?>
        <section class="m-pkg-section" aria-label="Holiday themes">
            <div class="m-pkg-carousel-header">
                <h3 class="m-pkg-section__title" >Explore Holiday Collections By Theme</h3>
            </div>
            <div class="m-pkg-carousel-track m-pkg-carousel-track--themes" id="mobThemeTrack">
                <?php foreach ($curated_pkg_themes as $th): ?>
                <a href="all-tours.php?theme=<?= urlencode($th['slug']) ?>&view=mobile" class="m-pkg-carousel-card m-theme-card" aria-label="<?= htmlspecialchars($th['title']) ?> tours">
                    <div class="m-theme-card__img-wrap">
                        <img src="<?= htmlspecialchars($th['img']) ?>" alt="<?= htmlspecialchars($th['title']) ?>" class="m-theme-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="m-theme-card__overlay"></div>
                    </div>
                    <h4 class="m-theme-card__title"><?= htmlspecialchars($th['title']) ?></h4>
                    <span class="m-theme-card__sub"><?= htmlspecialchars($th['sub']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <a href="all-tours.php?view=mobile" class="m-explore-all-btn" aria-label="Explore all tours">
                <span class="material-symbols-outlined" aria-hidden="true">globe_asia</span>
                Explore All Tours & Holiday Collections
                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
            </a>
        </section>
        <?php endif; ?>

        <!-- 6. Exclusive Offers (matches desktop: portrait cards, horizontal scroll, emerald theme, arrows) -->
        <?php 
        $offer_packages = array_filter($packages_mobile ?? [], fn($p) => !empty($p['original_price']) && floatval($p['original_price']) > floatval($p['price']));
        if (empty($offer_packages)) $offer_packages = array_slice($packages_mobile ?? [], 0, 5);
        if (!empty($offer_packages)):
        ?>
        <section class="m-pkg-section m-offers-section" aria-label="Exclusive holiday offers">
            <div class="m-pkg-carousel-header">
                <h3 class="m-pkg-section__title">
                    Exclusive Holiday Offers
                </h3>
                <p class="m-pkg-section__subtitle">Unbeatable limited-time discounts and curated seasonal savings across domestic and international sanctuaries.</p>
                <div class="m-pkg-carousel-controls">
                    <div class="m-pills-row m-pills-row--emerald" role="tablist" aria-label="Offers filter">
                        <button type="button" class="m-pill m-pill--emerald js-offer-tab m-pill--active" data-target="mob-offer-all" role="tab" aria-selected="true" aria-controls="mob-offer-all">
                            <span class="material-symbols-outlined m-pill-icon">bolt</span> All Deals
                        </button>
                        <button type="button" class="m-pill m-pill--emerald js-offer-tab m-pill--inactive" data-target="mob-offer-domestic" role="tab" aria-selected="false" aria-controls="mob-offer-domestic">
                            <span class="material-symbols-outlined m-pill-icon">landscape</span> Domestic
                        </button>
                        <button type="button" class="m-pill m-pill--emerald js-offer-tab m-pill--inactive" data-target="mob-offer-international" role="tab" aria-selected="false" aria-controls="mob-offer-international">
                            <span class="material-symbols-outlined m-pill-icon">flight_takeoff</span> International
                        </button>
                    </div>
                </div>
            </div>

            <!-- All Offers -->
            <div class="m-pkg-carousel-track js-offer-track" id="mob-offer-all" role="tabpanel">
                <?php foreach ($offer_packages as $op): 
                    $o_img = !empty($op['image_url']) ? $op['image_url'] : 'assets/img/pkg.jpg';
                    if (!preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $o_img)) $o_img = 'images/dest/' . ltrim($o_img, '/');
                    $o_price = floatval($op['price'] ?? 0);
                    $o_orig = !empty($op['original_price']) ? floatval($op['original_price']) : floatval($o_price * 1.35);
                    if ($o_orig <= $o_price) $o_orig = $o_price * 1.35;
                    $discount = round((($o_orig - $o_price) / $o_orig) * 100);
                    $days = (int)($op['days'] ?? 4);
                    $nights = (int)($op['nights'] ?? ($days > 1 ? $days - 1 : 1));
                    $o_scope = !empty($op['is_international']) ? 'international' : 'domestic';
                    $o_badge = $o_scope === 'international' ? '✈️ International' : '🇮🇳 Domestic';
                ?>
                <a href="package-detail.php?slug=<?= urlencode($op['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-offer-card" data-scope="<?= $o_scope ?>" aria-label="<?= htmlspecialchars($op['title']) ?> - <?= $discount ?>% off">
                    <div class="m-offer-card__badges">
                        <span class="m-offer-card__badge-discount">⚡ <?= $discount ?>% OFF</span>
                        <span class="m-offer-card__badge-type"><?= $o_badge ?></span>
                    </div>
                    <img src="<?= htmlspecialchars($o_img) ?>" alt="<?= htmlspecialchars($op['title']) ?>" class="m-offer-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-offer-card__overlay">
                        <div class="m-offer-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($op['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-offer-card__title"><?= htmlspecialchars($op['title']) ?></h4>
                        <div class="m-offer-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-offer-card__footer">
                            <div class="m-offer-card__pricing">
                                <span class="m-offer-card__original">₹<?= number_format($o_orig) ?></span>
                                <div class="m-offer-card__final-row">
                                    <span class="m-offer-card__final">₹<?= number_format($o_price) ?></span>
                                    <span class="m-offer-card__per-person">/person</span>
                                </div>
                            </div>
                            <span class="m-offer-card__claim">View Deal <span class="material-symbols-outlined" aria-hidden="true">local_offer</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Domestic Offers -->
            <div class="m-pkg-carousel-track js-offer-track is-hidden" id="mob-offer-domestic" role="tabpanel" aria-hidden="true">
                <?php foreach ($offer_packages as $op): 
                    if (!empty($op['is_international'])) continue;
                    $o_img = !empty($op['image_url']) ? $op['image_url'] : 'assets/img/pkg.jpg';
                    if (!preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $o_img)) $o_img = 'images/dest/' . ltrim($o_img, '/');
                    $o_price = floatval($op['price'] ?? 0);
                    $o_orig = !empty($op['original_price']) ? floatval($op['original_price']) : floatval($o_price * 1.35);
                    if ($o_orig <= $o_price) $o_orig = $o_price * 1.35;
                    $discount = round((($o_orig - $o_price) / $o_orig) * 100);
                    $days = (int)($op['days'] ?? 4);
                    $nights = (int)($op['nights'] ?? ($days > 1 ? $days - 1 : 1));
                ?>
                <a href="package-detail.php?slug=<?= urlencode($op['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-offer-card" data-scope="domestic" aria-label="<?= htmlspecialchars($op['title']) ?> - <?= $discount ?>% off">
                    <div class="m-offer-card__badges">
                        <span class="m-offer-card__badge-discount">⚡ <?= $discount ?>% OFF</span>
                        <span class="m-offer-card__badge-type">🇮🇳 Domestic</span>
                    </div>
                    <img src="<?= htmlspecialchars($o_img) ?>" alt="<?= htmlspecialchars($op['title']) ?>" class="m-offer-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-offer-card__overlay">
                        <div class="m-offer-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($op['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-offer-card__title"><?= htmlspecialchars($op['title']) ?></h4>
                        <div class="m-offer-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-offer-card__footer">
                            <div class="m-offer-card__pricing">
                                <span class="m-offer-card__original">₹<?= number_format($o_orig) ?></span>
                                <div class="m-offer-card__final-row">
                                    <span class="m-offer-card__final">₹<?= number_format($o_price) ?></span>
                                    <span class="m-offer-card__per-person">/person</span>
                                </div>
                            </div>
                            <span class="m-offer-card__claim">View Deal <span class="material-symbols-outlined" aria-hidden="true">local_offer</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- International Offers -->
            <div class="m-pkg-carousel-track js-offer-track is-hidden" id="mob-offer-international" role="tabpanel" aria-hidden="true">
                <?php foreach ($offer_packages as $op): 
                    if (empty($op['is_international'])) continue;
                    $o_img = !empty($op['image_url']) ? $op['image_url'] : 'assets/img/pkg.jpg';
                    if (!preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $o_img)) $o_img = 'images/dest/' . ltrim($o_img, '/');
                    $o_price = floatval($op['price'] ?? 0);
                    $o_orig = !empty($op['original_price']) ? floatval($op['original_price']) : floatval($o_price * 1.35);
                    if ($o_orig <= $o_price) $o_orig = $o_price * 1.35;
                    $discount = round((($o_orig - $o_price) / $o_orig) * 100);
                    $days = (int)($op['days'] ?? 4);
                    $nights = (int)($op['nights'] ?? ($days > 1 ? $days - 1 : 1));
                ?>
                <a href="package-detail.php?slug=<?= urlencode($op['slug'] ?? '') ?>&view=mobile" class="m-pkg-carousel-card m-offer-card" data-scope="international" aria-label="<?= htmlspecialchars($op['title']) ?> - <?= $discount ?>% off">
                    <div class="m-offer-card__badges">
                        <span class="m-offer-card__badge-discount">⚡ <?= $discount ?>% OFF</span>
                        <span class="m-offer-card__badge-type">✈️ International</span>
                    </div>
                    <img src="<?= htmlspecialchars($o_img) ?>" alt="<?= htmlspecialchars($op['title']) ?>" class="m-offer-card__img" loading="lazy" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    <div class="m-offer-card__overlay">
                        <div class="m-offer-card__dest">
                            <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                            <?= htmlspecialchars($op['destination'] ?? 'India') ?>
                        </div>
                        <h4 class="m-offer-card__title"><?= htmlspecialchars($op['title']) ?></h4>
                        <div class="m-offer-card__duration">🕒 <?= $nights ?>N / <?= $days ?>D</div>
                        <div class="m-offer-card__footer">
                            <div class="m-offer-card__pricing">
                                <span class="m-offer-card__original">₹<?= number_format($o_orig) ?></span>
                                <div class="m-offer-card__final-row">
                                    <span class="m-offer-card__final">₹<?= number_format($o_price) ?></span>
                                    <span class="m-offer-card__per-person">/person</span>
                                </div>
                            </div>
                            <span class="m-offer-card__claim">View Deal <span class="material-symbols-outlined" aria-hidden="true">local_offer</span></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 7. CTA Portfolio -->
        <section class="m-pkg-section m-cta-portfolio" aria-label="Explore all tours">
            <div class="m-cta-card">
                <div class="m-cta-badge">
                    <span class="material-symbols-outlined">explore</span> COMPLETE TOUR INVENTORY
                </div>
                <h3 class="m-cta-heading">
                    Ready to Find Your <span class="m-cta-gold-italic">Dream Sanctuary?</span>
                </h3>
                <p class="m-cta-desc">
                    Browse our complete catalog of curated luxury holidays, customized itineraries, and guaranteed fixed group departures with interactive budget and duration filters.
                </p>
                <a href="all-tours.php?view=mobile" class="m-cta-btn">
                    ✨ Explore All Available Tours
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                </a>
            </div>
        </section>

        <!-- 8. On-Trip Assistance -->
        <section class="m-pkg-section m-assistance-section" aria-label="On-trip assistance">
            <div class="m-assistance-card">
                <h4 class="m-assistance-heading">Hassle Free. 24X7 on-trip assistance</h4>
                <div class="m-assistance-links">
                    <a href="tel:+918918921629" class="m-assistance-item" aria-label="Call for assistance">
                        <span class="material-symbols-outlined m-assistance-icon" aria-hidden="true">call</span>
                        <span class="m-assistance-text">+91 89189 21629</span>
                    </a>
                    <a href="mailto:curator@leisurelooptrip.in" class="m-assistance-item" aria-label="Email for assistance">
                        <span class="material-symbols-outlined m-assistance-icon" aria-hidden="true">alternate_email</span>
                        <span class="m-assistance-text">curator@leisurelooptrip.in</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <?php 
    $modal_prefix = 'mob-';
    $extra_scripts = '<script src="js/modules/mobile_packages.js?v=' . time() . '" defer></script>';
    include __DIR__ . '/mobile_footer.php'; 
    ?>

</body>
</html>
