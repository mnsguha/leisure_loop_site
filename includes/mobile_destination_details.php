<?php
declare(strict_types=1);

if (!isset($pdo) || !$pdo) {
    require_once '../config/db.php';
}

// Sticky "Enquire Now" bar owns the bottom edge on this page
// (same convention as mobile_cab_detail / mobile_hotel_detail).
$hide_bottom_nav = true;

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: destinations.php?view=mobile');
    exit;
}

// Fetch destination details
try {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE slug = :slug AND is_active = 1");
    $stmt->execute(['slug' => $slug]);
    $destination = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: destinations.php?view=mobile');
    exit;
}

if (!$destination) {
    header('Location: destinations.php?view=mobile');
    exit;
}

// Fetch related packages
$related_packages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE (destination = :name OR title LIKE :like_name) AND is_active = 1 GROUP BY title LIMIT 6");
    $stmt->execute(['name' => $destination['name'], 'like_name' => '%' . $destination['name'] . '%']);
    $related_packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related_packages = [];
}

$sightseeing = json_decode($destination['sightseeing_json'] ?? '[]', true) ?: [];
$local_experiences = json_decode($destination['local_experiences_json'] ?? '[]', true) ?: [];

// Image resolution helper
function resolveDestImg(?string $img): string {
    if (empty($img)) return 'assets/img/pkg.jpg';
    if (preg_match('/^(http|\/|assets\/|uploads\/|images\/)/i', $img)) {
        return $img;
    }
    return 'images/dest/' . ltrim($img, '/');
}

$page_title = htmlspecialchars($destination['name']) . " | Leisure Loop Trip";

// Hero slider slides (auto-scrolling, hotel-detail style): optional video first,
// then the original destination cover, then story images (deduped by resolved path)
$hero_cover = !empty($destination['cover_image'])
    ? $destination['cover_image']
    : (!empty($destination['card_image']) ? $destination['card_image'] : 'images/placeholder.jpg');
$hero_candidates = array_filter([
    $hero_cover,
    $destination['story_narrative_image']   ?? '',
    $destination['story_narrative_image_2'] ?? '',
    $destination['story_narrative_image_3'] ?? '',
]);
$hero_images = [];
$hero_seen   = [];
foreach ($hero_candidates as $hero_candidate) {
    $hero_resolved = resolveDestImg($hero_candidate);
    if (!isset($hero_seen[$hero_resolved])) {
        $hero_seen[$hero_resolved] = true;
        $hero_images[] = $hero_resolved;
    }
}
if (empty($hero_images)) {
    $hero_images = [resolveDestImg($hero_cover)];
}
$hero_has_video = !empty($destination['hero_video_url']);
$hero_slide_count = count($hero_images) + ($hero_has_video ? 1 : 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?= time() ?>">
</head>
<body class="mob-dest-body mob-dest-detail">

    <!-- Hero Header -->
    <div class="mob-dest__hero">
        <div class="mob-dest__hero-slider" id="mobDestHeroSlider" role="group" aria-label="<?= htmlspecialchars($destination['name']) ?> photos">
            <?php if ($hero_has_video): ?>
            <div class="mob-dest__hero-slide">
                <video autoplay loop muted playsinline class="mob-dest__hero-video">
                    <source src="<?= htmlspecialchars($destination['hero_video_url']) ?>" type="video/mp4">
                </video>
            </div>
            <?php endif; ?>
            <?php foreach ($hero_images as $hero_idx => $hero_img): ?>
            <div class="mob-dest__hero-slide">
                <img src="<?= htmlspecialchars($hero_img) ?>" alt="<?= htmlspecialchars($destination['name']) ?> photo <?= $hero_idx + 1 ?>" class="mob-dest__hero-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mob-dest__hero-overlay" aria-hidden="true"></div>

        <!-- Topbar -->
        <div class="mob-dest__hero-topbar">
            <a href="destinations.php?view=mobile" class="mob-dest__back" aria-label="Back to destinations">
                <span class="material-symbols-outlined icon-back-hero">arrow_back</span>
            </a>
            <span class="mob-dest__category-badge">
                <?= htmlspecialchars($destination['category'] ?? 'Sanctuary') ?>
            </span>
        </div>

        <!-- Hero Content -->
        <div class="mob-dest__hero-content">
            <span class="mob-dest__tagline"><?= htmlspecialchars($destination['tagline'] ?: 'Himalayan Sanctuary') ?></span>
            <h1 class="mob-dest__name"><?= htmlspecialchars($destination['name']) ?></h1>
        </div>

        <?php if ($hero_slide_count > 1): ?>
        <!-- Slider Dots -->
        <div class="mob-dest__hero-dots" aria-hidden="true">
            <?php for ($hero_idx = 0; $hero_idx < $hero_slide_count; $hero_idx++): ?>
            <span class="mob-dest__hero-dot<?= $hero_idx === 0 ? ' is-active' : '' ?>"></span>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Facts Grid -->
    <div class="mob-dest__facts">
        <div class="mob-dest__fact">
            <span class="mob-dest__fact-label">Altitude</span>
            <span class="mob-dest__fact-value"><?= htmlspecialchars(!empty($destination['altitude']) ? $destination['altitude'] : 'Varied') ?></span>
        </div>
        <div class="mob-dest__fact">
            <span class="mob-dest__fact-label">Best Time</span>
            <span class="mob-dest__fact-value"><?= htmlspecialchars(!empty($destination['best_time']) ? $destination['best_time'] : 'Year Round') ?></span>
        </div>
        <?php if (!empty($destination['difficulty'])): ?>
        <div class="mob-dest__fact">
            <span class="mob-dest__fact-label">Difficulty</span>
            <span class="mob-dest__fact-value"><?= htmlspecialchars($destination['difficulty']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($destination['ideal_for'])): ?>
        <div class="mob-dest__fact">
            <span class="mob-dest__fact-label">Ideal For</span>
            <span class="mob-dest__fact-value"><?= htmlspecialchars($destination['ideal_for']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Narrative Section -->
    <section class="mob-dest__narrative">
        <div class="mob-dest__section-head">
            <span class="mob-dest__accent-bar mob-dest__accent-bar--gold" aria-hidden="true"></span>
            <h2 class="mob-dest__section-title">The Narrative</h2>
        </div>
        <div class="mob-dest__narrative-body dropcap">
            <?php
                $desc_text = !empty($destination['description_long']) ? $destination['description_long'] : (!empty($destination['description']) ? $destination['description'] : '');
                if (empty(trim($desc_text))) {
                    $desc_text = 'Discover ' . ($destination['name'] ?? 'this sanctuary') . ' with our exclusive journeys.';
                }
                echo nl2br(htmlspecialchars($desc_text));
            ?>
        </div>
    </section>

    <!-- Sightseeing Highlights -->
    <?php if (!empty($sightseeing)): ?>
    <section class="mob-dest__section">
        <div class="mob-dest__section-bar">
            <div class="mob-dest__section-head mob-dest__section-head--flush">
                <span class="mob-dest__accent-bar mob-dest__accent-bar--sky" aria-hidden="true"></span>
                <h2 class="mob-dest__section-title--sm">Must Visit Places</h2>
            </div>
            <span class="mob-dest__swipe-hint">Swipe &rarr;</span>
        </div>
        <div class="mob-dest__scroll-row">
            <?php foreach ($sightseeing as $sight): ?>
            <div class="mob-dest__sight-card">
                <div class="mob-dest__sight-img-wrap">
                    <img src="<?= htmlspecialchars(resolveDestImg($sight['image'] ?? '')) ?>" class="mob-dest__sight-img" alt="<?= htmlspecialchars($sight['title'] ?? '') ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                </div>
                <div class="mob-dest__sight-body">
                    <h3 class="mob-dest__sight-title"><?= htmlspecialchars($sight['title'] ?? '') ?></h3>
                    <p class="mob-dest__sight-desc"><?= htmlspecialchars($sight['description'] ?? $sight['desc'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Local Experiences -->
    <?php if (!empty($local_experiences)): ?>
    <section class="mob-dest__section">
        <div class="mob-dest__section-bar--start">
            <span class="mob-dest__accent-bar mob-dest__accent-bar--amber" aria-hidden="true"></span>
            <h2 class="mob-dest__section-title--sm">Local Experiences</h2>
        </div>
        <div class="mob-dest__scroll-row">
            <?php foreach ($local_experiences as $exp): ?>
            <div class="mob-dest__exp-card">
                <div class="mob-dest__exp-icon-wrap">
                    <span class="material-symbols-outlined mob-dest__exp-icon"><?= htmlspecialchars($exp['icon'] ?? 'hotel_class') ?></span>
                </div>
                <h3 class="mob-dest__exp-title"><?= htmlspecialchars($exp['title'] ?? '') ?></h3>
                <p class="mob-dest__exp-desc"><?= htmlspecialchars($exp['description'] ?? $exp['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Tour Packages -->
    <?php if (!empty($related_packages)): ?>
    <section class="mob-dest__section">
        <div class="mob-dest__section-bar">
            <div class="mob-dest__section-head mob-dest__section-head--flush">
                <span class="mob-dest__accent-bar mob-dest__accent-bar--emerald" aria-hidden="true"></span>
                <h2 class="mob-dest__section-title--sm">Curated Tours</h2>
            </div>
            <a href="packages.php?view=mobile" class="mob-dest__view-all">View All &rarr;</a>
        </div>
        <div class="mob-dest__scroll-row">
            <?php foreach ($related_packages as $pkg): ?>
            <a href="package-detail.php?slug=<?= urlencode($pkg['slug']) ?>&view=mobile" class="app-pkg-card">
                <div class="app-pkg-img" data-bg="<?= htmlspecialchars(resolveDestImg($pkg['image_url'] ?? '')) ?>">
                    <div class="app-pkg-badge"><?= htmlspecialchars((string)($pkg['nights'] ?? '3')) ?>N / <?= htmlspecialchars((string)($pkg['days'] ?? '4')) ?>D</div>
                    <div class="app-pkg-content">
                        <span class="app-pkg-dest"><?= htmlspecialchars($pkg['destination'] ?? 'TOUR') ?></span>
                        <h3 class="app-pkg-title"><?= htmlspecialchars($pkg['title']) ?></h3>
                        <?php if (!empty($pkg['base_price']) || !empty($pkg['price'])): ?>
                        <div class="app-pkg-price">FROM &#8377;<?= number_format((float)($pkg['base_price'] ?? $pkg['price'] ?? 0)) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sticky CTA Triggering Global Modal -->
    <div class="sticky-cta">
        <button type="button" data-action="open-enquiry-modal" class="btn-primary">Enquire Now</button>
    </div>

    <?php include __DIR__ . '/mobile_footer.php'; ?>
