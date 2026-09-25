<?php
/**
 * Desktop View — Destinations
 * Expects from controller (destinations.php):
 *   $destinations   array   All active destination rows (with tour_count)
 *   $all_best_times array   Sorted list of unique best-time strings
 *   $page_title     string  <title> value
 */

// Type filtering for hero copy (still driven by ?type= on desktop)
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
if ($type === 'domestic' || $type === 'international') {
    $hero_title    = ucfirst($type) . ' Destinations';
    $hero_subtitle = 'Discover the finest ' . $type . ' luxury experiences handpicked for you.';
} else {
    $hero_title    = 'Explore Destinations';
    $hero_subtitle = "Discover the world's most exquisite luxury experiences handpicked for you.";
}

include '../includes/header.php';
?>
<link rel="stylesheet" href="css/destinations.css">


<!-- 1. Panoramic EaseMyTrip-Inspired Hero Banner -->
<section class="emt-dest-hero">
    <div class="emt-hero-content">
        <div class="hero-luxury-badge">
            <span>✨</span> PRIVATE CURATED SANCTUARIES
        </div>
        <h1><?php echo htmlspecialchars($hero_title); ?></h1>
        <p class="hero-tagline"><?php echo htmlspecialchars($hero_subtitle); ?></p>

        <!-- Interactive Capsule Search Dock -->
        <div class="emt-search-dock-wrapper">
            <div class="emt-search-dock">
                <div class="dock-field field-where">
                    <span class="material-symbols-outlined dock-field-icon">place</span>
                    <div class="dock-field-content">
                        <span class="dock-label">Where To?</span>
                        <label for="destSearchInput" class="sr-only">Search destination, region or vibe...</label>
                        <input type="text" id="destSearchInput" class="dock-input" placeholder="Search destination, region or vibe...">
                    </div>
                </div>

                <div class="dock-divider"></div>

                <div class="dock-field field-when">
                    <span class="material-symbols-outlined dock-field-icon">calendar_month</span>
                    <div class="dock-field-content">
                        <span class="dock-label">Travel Season</span>
                        <label for="destSeasonSelect" class="sr-only">Travel Season</label>
                        <select id="destSeasonSelect" class="dock-select" data-change="apply-filters">
                            <option value="">Any Season / All Year</option>
                            <?php foreach ($all_best_times as $time): ?>
                                <option value="<?php echo htmlspecialchars(strtolower(trim($time))); ?>"><?php echo htmlspecialchars($time); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="button" class="dock-search-btn" data-action="apply-filters" title="Explore Destinations">
                    <span class="material-symbols-outlined">search</span> Explore
                </button>
            </div>
        </div>
    </div>
</section>

<!-- 2. Quick-Discovery Theme Ribbon Bar -->
<section class="quick-discovery-section">
    <div class="quick-discovery-ribbon">
        <button type="button" class="discovery-chip active" data-filter="all" data-action="quick-chip" data-val="all">
            <span>🌟</span> All Collections
        </button>
        <button type="button" class="discovery-chip" data-filter="domestic" data-action="quick-chip" data-val="domestic">
            <span>🏔️</span> Domestic Sanctuaries
        </button>
        <button type="button" class="discovery-chip" data-filter="international" data-action="quick-chip" data-val="international">
            <span>🌴</span> International Paradises
        </button>
        <button type="button" class="discovery-chip" data-filter="summer" data-action="quick-chip" data-val="summer">
            <span>☀️</span> Summer &amp; Monsoon
        </button>
        <button type="button" class="discovery-chip" data-filter="winter" data-action="quick-chip" data-val="winter">
            <span>❄️</span> Winter &amp; Autumn
        </button>
    </div>
</section>

<!-- Breadcrumb Strip & Live Counter -->
<div class="catalog-breadcrumb catalog-breadcrumb-extended">
    <div class="catalog-breadcrumb-links">
        <a href="index.php">Home</a>
        <span>&gt;</span>
        <?php if (!empty($type)): ?>
            <a href="destinations.php">Destinations</a>
            <span>&gt;</span>
            <span class="catalog-breadcrumb-active"><?php echo htmlspecialchars($hero_title); ?></span>
        <?php else: ?>
            <span class="catalog-breadcrumb-active">Destinations</span>
        <?php endif; ?>
    </div>
    <span id="resultsCountBadge" class="results-count-pill"><?php echo count($destinations); ?> Sanctuaries</span>
</div>

<!-- 3. Main Catalog Layout -->
<div class="catalog-layout">
    <main class="packages-grid-display catalog-grid-main">
        <!-- Destination Cards Grid -->
        <div id="packages-grid-inner" class="catalog-grid">
            <?php foreach ($destinations as $dest):
                $img = !empty($dest['card_image'])
                    ? $dest['card_image']
                    : (!empty($dest['cover_image']) ? $dest['cover_image'] : '');

                $times_data = [];
                if (!empty($dest['best_time'])) {
                    foreach (explode(',', $dest['best_time']) as $t) {
                        $times_data[] = strtolower(trim($t));
                    }
                }
                $times_str           = implode('|', $times_data);
                $dest_category_clean = strtolower(trim($dest['category'] ?? 'domestic'));
            ?>
            <div class="catalog-card-item js-dest-card"
                 data-name="<?php echo htmlspecialchars(strtolower($dest['name'] ?? '')); ?>"
                 data-category="<?php echo htmlspecialchars($dest_category_clean); ?>"
                 data-order="<?php echo $dest['display_order'] ?? 0; ?>"
                 data-tagline="<?php echo htmlspecialchars(strtolower($dest['tagline'] ?? '')); ?>"
                 data-times="<?php echo htmlspecialchars($times_str); ?>">
                <a href="destination-details.php?slug=<?php echo htmlspecialchars($dest['slug'] ?? ''); ?>" class="catalog-card-anchor">
                    <div class="catalog-card-image-shell">
                        <?php if (!empty($img)): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="catalog-card-img" alt="<?php echo htmlspecialchars($dest['name'] ?? ''); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <?php else: ?>
                            <div class="catalog-card-img catalog-card-img-placeholder"></div>
                        <?php endif; ?>
                        <?php if (!empty($dest['altitude'])): ?>
                        <div class="days-nights-pill">📍 <?php echo htmlspecialchars($dest['altitude']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="catalog-card-body">
                        <span class="catalog-card-destination">
                            <span>✦</span> <?php echo htmlspecialchars($dest['category'] ?? 'Sanctuary'); ?> SANCTUARY
                        </span>
                        <h3 class="catalog-card-title"><?php echo htmlspecialchars($dest['name'] ?? 'Destination'); ?></h3>
                        <div class="catalog-card-tagline"><?php echo htmlspecialchars($dest['tagline'] ?? 'Immerse yourself in world-class luxury and scenic Himalayan grandeur.'); ?></div>
                        <div class="card-footer-strip">
                            <div class="tours-count">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="var(--gold, #C5A059)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?php echo $dest['tour_count'] ?? 0; ?> SIGNATURE TOURS</span>
                            </div>
                            <span class="explore-link-btn">EXPLORE &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State Placeholder -->
        <div id="no-packages-placeholder" class="catalog-empty-state is-hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold, #C5A059)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="catalog-empty-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3 class="catalog-empty-title">No matching destinations found</h3>
            <p class="catalog-empty-text">We couldn't find any journeys matching your exact search parameters. Please try broadening your filter criteria or search terms.</p>
            <button type="button" data-action="reset-filters" class="catalog-empty-btn">Reset Filters</button>
        </div>
    </main>
</div>

<script src="js/modules/destinations.js?v=1787348699" defer></script>

<?php include '../includes/footer.php'; ?>
