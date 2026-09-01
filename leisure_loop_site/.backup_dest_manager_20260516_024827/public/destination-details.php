<?php
require_once '../config/db.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

if (!$pdo) {
    die("Database connection failed.");
}

// Fetch destination details
try {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE slug = :slug AND is_active = 1");
    $stmt->execute(['slug' => $slug]);
    $destination = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}

if (!$destination) {
    header('Location: index.php');
    exit;
}

// Fetch related packages — match on 'destination' column (exact) or title keyword (fallback)
$related_packages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE (destination = :name OR title LIKE :like_name) AND is_active = 1 LIMIT 6");
    $stmt->execute(['name' => $destination['name'], 'like_name' => '%' . $destination['name'] . '%']);
    $related_packages = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silently fail — show empty state
    $related_packages = [];
}

$sightseeing = json_decode($destination['sightseeing_json'], true) ?: [];

include '../includes/header.php';
?>

<main class="destination-page">
    <!-- Hero Reveal Section -->
    <section class="dest-hero" style="background-image: url('<?php echo htmlspecialchars($destination['cover_image']); ?>');">
        <div class="dest-hero-overlay"></div>
        <div class="container">
            <div class="dest-hero-content">
                <span class="dest-tagline-gold"><?php echo htmlspecialchars($destination['tagline']); ?></span>
                <h1 class="dest-title-reveal"><?php echo htmlspecialchars($destination['name']); ?></h1>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="mouse"></div>
            <span>Explore the Terrain</span>
        </div>
    </section>

    <!-- Editorial Story Section -->
    <section class="dest-story-section">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <span class="section-label-gold">The Narrative</span>
                    <h2 class="section-title">A Journey of <span class="serif" style="color: var(--gold); font-style: italic;">Discovery.</span></h2>
                    <div class="editorial-text">
                        <?php echo nl2br(htmlspecialchars($destination['description_long'])); ?>
                    </div>
                </div>
                <div class="story-sidebar">
                    <div class="concierge-card glass-card">
                        <h3>Private Concierge</h3>
                        <p>Our travel curators are ready to craft your bespoke <?php echo htmlspecialchars($destination['name']); ?> itinerary.</p>
                        <ul class="concierge-features">
                            <li><i class="fas fa-check"></i> Private Luxury Transfers</li>
                            <li><i class="fas fa-check"></i> Handpicked Accommodations</li>
                            <li><i class="fas fa-check"></i> 24/7 Ground Assistance</li>
                        </ul>
                        <a href="#inquire" class="btn-gold" style="display: block; text-align: center; margin-top: 20px;">Inquire Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sightseeing 3-Column Grid -->
    <section class="sightseeing-section">
        <div class="container">
            <div class="section-header-center">
                <span class="section-label-gold">Curated Highlights</span>
                <h2 class="section-title">Popular <span class="serif" style="color: var(--gold); font-style: italic;">Sightseeing.</span></h2>
                <p style="color: rgba(255,255,255,0.4); margin-top: 12px; font-size: 0.9rem;">Discover the iconic places that define this destination.</p>
            </div>

            <div class="sightseeing-grid">
                <?php foreach ($sightseeing as $spot): ?>
                <div class="sight-card">
                    <div class="sight-img-wrapper">
                        <div class="sight-img" style="background-image: url('<?php echo htmlspecialchars($spot['image']); ?>');"></div>
                    </div>
                    <div class="sight-info">
                        <h3><?php echo htmlspecialchars($spot['title']); ?></h3>
                        <p><?php echo htmlspecialchars($spot['desc']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Related Packages Film Strip -->
    <section class="dest-packages-section">
        <div class="container">
            <div class="section-header">
                <div class="header-left">
                    <span class="section-label-gold">Signature Journeys</span>
                    <h2 class="section-title">Tours around <span class="serif" style="color: var(--gold); font-style: italic;"><?php echo htmlspecialchars($destination['name']); ?>.</span></h2>
                </div>
            </div>
            
            <div class="packages-carousel">
                <?php if (empty($related_packages)): ?>
                    <p class="no-packages">New signature journeys for <?php echo htmlspecialchars($destination['name']); ?> are being curated. Inquire for private arrangements.</p>
                <?php else: ?>
                    <?php foreach ($related_packages as $pkg): ?>
                    <div class="package-card dest-pkg-card">
                        <div class="pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url']); ?>');">
                            <div class="pkg-badge">
                                <?php 
                                    $itin = json_decode($pkg['itinerary'], true);
                                    $day_count = is_array($itin) ? count($itin) : 0;
                                    echo $day_count > 0 ? $day_count . ' DAYS' : 'CUSTOM';
                                ?>
                            </div>
                            <div class="pkg-overlay"></div>
                            <div class="pkg-overlay-content">
                                <h3 class="pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            </div>
                        </div>
                        <div class="pkg-footer dest-pkg-footer">
                            <div class="pkg-price">FROM <b>&#8377;<?php echo number_format($pkg['price']); ?></b></div>
                            <div class="dest-pkg-actions">
                                <a href="package-detail.php?slug=<?php echo $pkg['slug']; ?>" class="pkg-btn" title="View Details">&rarr;</a>
                                <?php 
                                    $wa_msg = urlencode("Hi! I'm interested in the *" . $pkg['title'] . "* package for *" . $destination['name'] . "*. Please share more details.");
                                ?>
                                <a href="https://wa.me/918918921629?text=<?php echo $wa_msg; ?>" 
                                   target="_blank" 
                                   class="pkg-enquire-btn"
                                   title="Enquire on WhatsApp">
                                    <svg width="14" height="14" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.6-2.8-23.6-8.7-45-27.7-16.6-14.8-27.8-33.1-31.1-38.6-3.2-5.6-.3-8.6 2.5-11.4 2.5-2.5 5.5-6.5 8.3-9.7 2.8-3.2 3.7-5.6 5.6-9.3 1.9-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.6 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                                    Plan This Journey
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
