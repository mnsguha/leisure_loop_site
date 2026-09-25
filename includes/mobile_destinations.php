<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title ?? 'Explore Destinations | Leisure Loop Trip') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="m-page-body mob-dest-body">

    <?php
    $mobile_header_title = 'Destinations';
    $mobile_active_nav   = 'destinations';
    include __DIR__ . '/mobile_header.php';
    ?>

    <main class="m-main-pad">

        <!-- Compact Scenic Hero Section -->
        <section class="m-dest-hero">
            <img src="assets/img/mobile_splash_bg_2.webp" alt="Scenic Mountains" class="m-dest-hero__img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
            <div class="m-dest-hero__overlay"></div>
            <div class="m-dest-hero__text">
                <p class="m-dest-hero__label">Curated Sanctuaries</p>
                <h2 class="m-dest-hero__title">Explore Destinations</h2>
                <p class="m-dest-hero__sub">Discover the world's most exquisite luxury getaways</p>
            </div>
        </section>

        <!-- Overlapping Pill Search Bar -->
        <div class="m-search-wrap">
            <div class="m-search-pill">
                <span class="material-symbols-outlined m-search-icon">search</span>
                <input type="text" id="destSearchInput" placeholder="Enter your dream destination..." class="m-search-input">
                <button type="button" id="destClearSearch" class="m-search-clear hidden" aria-label="Clear search">
                    <span class="material-symbols-outlined m-search-icon--sm">close</span>
                </button>
            </div>
        </div>

        <!-- Horizontal Category Filter Badges -->
        <div class="m-filter-strip">
            <button type="button" class="m-filter-badge active" data-filter="all">
                <span>All</span>
            </button>
            <button type="button" class="m-filter-badge" data-filter="domestic">
                <span class="m-dot-domestic"></span>
                <span>Domestic</span>
            </button>
            <button type="button" class="m-filter-badge" data-filter="international">
                <span class="m-dot-international"></span>
                <span>International</span>
            </button>
            <button type="button" class="m-filter-badge" data-sort="popular">
                <span class="material-symbols-outlined m-filter-badge__icon">local_fire_department</span>
                <span>Popular</span>
            </button>
        </div>

        <!-- Destination Cards Grid -->
        <div id="destGrid" class="m-dest-grid">
            <?php if (!empty($destinations)): ?>
                <?php foreach ($destinations as $dest): ?>
                    <?php
                        $categorySlug = strtolower($dest['category'] ?? 'domestic');
                        $destName     = $dest['name'] ?? '';

                        $rawImg = !empty($dest['card_image'])
                            ? $dest['card_image']
                            : (!empty($dest['cover_image']) ? $dest['cover_image'] : '');

                        if (!empty($rawImg)) {
                            if (preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $rawImg)) {
                                $cardImg = $rawImg;
                            } else {
                                $cardImg = 'images/dest/' . ltrim($rawImg, '/');
                            }
                        } else {
                            $cardImg = 'assets/img/pkg.jpg';
                        }
                    ?>
                    <a href="destination-details.php?slug=<?= urlencode($dest['slug'] ?? '') ?>&view=mobile"
                       class="m-dest-card js-card"
                       data-name="<?= htmlspecialchars(strtolower($destName)) ?>"
                       data-category="<?= htmlspecialchars($categorySlug) ?>"
                       data-order="<?= (int)($dest['display_order'] ?? 0) ?>">
                        <div class="m-dest-card__img-wrap">
                            <img src="<?= htmlspecialchars($cardImg) ?>"
                                 alt="<?= htmlspecialchars($destName) ?>"
                                 class="m-dest-card__img"
                                 onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                            <div class="m-dest-card__img-overlay"></div>
                            <span class="m-dest-card__badge">
                                <?= htmlspecialchars($dest['category'] ?? 'Domestic') ?>
                            </span>
                        </div>
                        <div class="m-dest-card__body">
                            <h3 class="m-dest-card__name">
                                <?= htmlspecialchars($destName) ?>
                            </h3>
                            <p class="m-dest-card__tagline">
                                <?= htmlspecialchars($dest['tagline'] ?? $dest['short_description'] ?? 'Explore scenic beauty') ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="m-dest-grid__empty">
                    No destinations available.
                </div>
            <?php endif; ?>
        </div>

        <div id="noDestResults" class="m-no-results hidden">
            No destinations found matching your criteria.
        </div>

    </main>

    <?php include __DIR__ . '/mobile_footer.php'; ?>
</body>
</html>
