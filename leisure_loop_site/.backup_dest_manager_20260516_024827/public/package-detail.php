<?php
    require_once '../config/db.php';
    require_once '../includes/functions.php';

    $slug = $_GET['slug'] ?? '';
    $pkg = null;

    if ($pdo && $slug) {
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        $pkg = $stmt->fetch();
        if ($pkg && !isset($pkg['map_coords'])) {
            $pkg['map_coords'] = '';
        }
    }

    if (!$pkg) {
        header('Location: packages.php');
        exit;
    }

    $itinerary = json_decode($pkg['itinerary'], true) ?: [];

    $page_title = $pkg['title'] . " | Leisure Loop Trip";
    $meta_desc = "Explore " . $pkg['title'] . ". Starting at INR " . number_format($pkg['price']) . ". Bespoke " . count($itinerary) . "-day cinematic journey curated by Leisure Loop.";
    $meta_image = $pkg['image_url'];
    $meta_keywords = $pkg['title'] . ", luxury tour, bespoke travel, " . count($itinerary) . " day itinerary";

    include '../includes/header.php';
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo addslashes($pkg['title']); ?>",
  "image": "<?php echo $pkg['image_url']; ?>",
  "description": "<?php echo addslashes($meta_desc); ?>",
  "brand": {
    "@type": "Brand",
    "name": "Leisure Loop Trip"
  },
  "offers": {
    "@type": "Offer",
    "priceCurrency": "INR",
    "price": "<?php echo $pkg['price']; ?>",
    "availability": "https://schema.org/InStock"
  }
}
</script>

<header class="hero" style="height: 70vh;">
    <div class="hero-video-container">
        <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
    </div>
    <div class="container hero-content" style="text-align: left; margin-inline: 0;">
        <span class="tagline">Starting from &#8377;<?php echo number_format($pkg['price']); ?></span>
        <h1 style="font-size: clamp(3rem, 8vw, 6rem);"><?php echo htmlspecialchars($pkg['title']); ?></h1>
        <div style="display: flex; gap: 2rem; margin-top: 2rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: var(--gold); font-size: 1rem;">Day Plan</span>
                <span style="text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.8rem; font-weight: 600;"><?php echo count($itinerary); ?> Days</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: var(--gold); font-size: 1rem;">Route</span>
                <span style="text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.8rem; font-weight: 600;">Multiple Destinations</span>
            </div>
        </div>
    </div>
</header>

<div class="container" style="display: grid; grid-template-columns: 1fr 380px; gap: 6rem; padding: 8rem 0;">
    <main>
        <section style="margin-bottom: 6rem;">
            <span class="section-label">The Journey</span>
            <h2 class="serif" style="font-size: 3rem; margin-bottom: 3rem;">Detailed Itinerary</h2>

            <div class="timeline">
                <?php foreach ($itinerary as $day): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="day-num">Day <?php echo htmlspecialchars((string) ($day['day'] ?? '')); ?></span>
                        <h3><?php echo htmlspecialchars($day['title'] ?? 'Journey Highlight'); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($day['desc'] ?? '')); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($pkg['map_coords'])): ?>
        <section style="margin-bottom: 6rem;">
            <span class="section-label">Geographic Context</span>
            <h2 class="serif" style="font-size: 3rem; margin-bottom: 3rem;">Journey Route</h2>
            <div id="map" style="height: 450px; border-radius: 4px; border: 1px solid var(--glass-border); filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const coords = "<?php echo $pkg['map_coords']; ?>".split(',');
                    let lat = 27.3314;
                    let lng = 88.6138;

                    if (coords.length === 2) {
                        lat = parseFloat(coords[0]);
                        lng = parseFloat(coords[1]);
                    }

                    const map = L.map('map', {
                        center: [lat, lng],
                        zoom: 10,
                        scrollWheelZoom: false
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    L.marker([lat, lng]).addTo(map)
                        .bindPopup("<?php echo addslashes($pkg['title']); ?>")
                        .openPopup();
                });
            </script>
        </section>
        <?php endif; ?>

        <section style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; border-top: 1px solid var(--glass-border); padding-top: 6rem;">
            <div>
                <h3 class="serif" style="font-size: 1.8rem; margin-bottom: 2rem; color: var(--gold);">Inclusions</h3>
                <ul class="check-list" style="list-style: none; color: var(--text-muted);">
                    <li style="margin-bottom: 1rem;">Premium Boutique Accommodation</li>
                    <li style="margin-bottom: 1rem;">Private Luxury Transportation</li>
                    <li style="margin-bottom: 1rem;">Professional Local Tour Curator</li>
                    <li style="margin-bottom: 1rem;">All Sightseeing as per Itinerary</li>
                    <li style="margin-bottom: 1rem;">Permits and Entrance Fees</li>
                </ul>
            </div>
            <div>
                <h3 class="serif" style="font-size: 1.8rem; margin-bottom: 2rem; color: #f87171;">Exclusions</h3>
                <ul class="cross-list" style="list-style: none; color: var(--text-muted);">
                    <li style="margin-bottom: 1rem;">Airfare to and from the hub</li>
                    <li style="margin-bottom: 1rem;">Personal expenses and gratuity</li>
                    <li style="margin-bottom: 1rem;">Items not mentioned in inclusions</li>
                </ul>
            </div>
        </section>
    </main>

    <aside>
        <div style="position: sticky; top: 120px;">
            <div class="glass-card" style="background: var(--glass); border: 1px solid var(--glass-border); padding: 3rem; border-radius: 4px;">
                <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 1.5rem; text-align: center;">Inquire About <br>This Escape</h3>
                <form id="contactForm" action="../api/submit-lead.php" method="POST" class="js-lead-form">
                    <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                    <div style="margin-bottom: 1.5rem;">
                        <input type="text" name="name" placeholder="YOUR NAME" required style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <input type="tel" name="phone" placeholder="PHONE NUMBER" required style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none;">
                    </div>
                    <button type="submit" class="btn-gold" style="width: 100%;">Get Custom Quote</button>
                </form>
                <p style="text-align: center; font-size: 0.7rem; color: var(--text-muted); margin-top: 1.5rem; letter-spacing: 0.05em;">Our expert curators usually respond within 4 hours.</p>
            </div>
        </div>
    </aside>
</div>

<?php include '../includes/footer.php'; ?>
