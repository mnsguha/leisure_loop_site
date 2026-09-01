<?php
    require_once '../config/db.php';
    require_once '../includes/functions.php';

    $slug = $_GET['slug'] ?? '';
    $pkg = null;

    if ($pdo && $slug) {
        $stmt = $pdo->prepare("SELECT p.*, d.terms_conditions as dest_terms FROM packages p LEFT JOIN destinations d ON p.destination = d.name WHERE p.slug = ? AND p.is_active = 1");
        $stmt->execute([$slug]);
        $pkg = $stmt->fetch();
    }

    if (!$pkg) {
        header('Location: packages.php');
        exit;
    }

    $itinerary = json_decode($pkg['itinerary'], true) ?: [];

    // Meta attributes
    $page_title = $pkg['title'] . " | Leisure Loop Trip";
    $meta_desc = "Explore " . $pkg['title'] . ". Starting at INR " . number_format($pkg['price']) . ". Bespoke " . count($itinerary) . "-day cinematic journey curated by Leisure Loop.";
    $meta_image = $pkg['image_url'];
    $meta_keywords = $pkg['title'] . ", luxury tour, bespoke travel, " . count($itinerary) . " day itinerary";

    // Dynamic Variables & Fallbacks
    $rating_score = !empty($pkg['rating_score']) ? (float)$pkg['rating_score'] : 4.8;
    $rating_count = !empty($pkg['rating_count']) ? (int)$pkg['rating_count'] : 150;
    $tour_code = !empty($pkg['tour_code']) ? htmlspecialchars($pkg['tour_code']) : 'VD-' . str_pad($pkg['id'], 4, '0', STR_PAD_LEFT);
    $tour_type = !empty($pkg['tour_type']) ? htmlspecialchars($pkg['tour_type']) : 'Honeymoon, Hill station, Wild Life Tour';
    $original_price = !empty($pkg['original_price']) ? (float)$pkg['original_price'] : null;
    
    $highlights_arr = [];
    if (!empty($pkg['highlights'])) {
        $highlights_arr = array_filter(array_map('trim', explode("\n", $pkg['highlights'])));
    } else {
        $highlights_arr = [
            "Exclusive Curated Sightseeing Grid Highlights",
            "Premium Boutique Heritage Accommodations",
            "Private Luxury Chauffeur & Logistics Support",
            "Bespoke Local Experience Coordinator Access"
        ];
    }

    $inclusions_arr = [];
    if (!empty($pkg['inclusions'])) {
        $inclusions_arr = array_filter(array_map('trim', explode("\n", $pkg['inclusions'])));
    } else {
        $inclusions_arr = [
            "Ultra-Premium Boutique Accommodations",
            "Private Luxury Saloon Transportation",
            "Bespoke Professional Experience Host",
            "Curated Dining Grid Permitted Options",
            "VIP Permits & Priority Entry Accents"
        ];
    }

    $exclusions_arr = [];
    if (!empty($pkg['exclusions'])) {
        $exclusions_arr = array_filter(array_map('trim', explode("\n", $pkg['exclusions'])));
    } else {
        $exclusions_arr = [
            "Inter-state Flight & Transit Tickets",
            "Personal Discretionary Expenses",
            "Gratuities and Driver Incentives"
        ];
    }

    $description_rich = !empty($pkg['description_rich']) ? $pkg['description_rich'] : '';

    $photos_arr = [];
    if (!empty($pkg['photos'])) {
        $photos_arr = array_filter(array_map('trim', explode(",", $pkg['photos'])));
    } else {
        // Fallback placeholder premium images from Unsplash
        $photos_arr = [
            "https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800",
            "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800",
            "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800"
        ];
    }

    // Load dynamic destinations list for form
    $destinations_list = [];
    try {
        $destinations_list = $pdo ? $pdo->query("SELECT name FROM destinations WHERE is_active=1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_COLUMN) : [];
    } catch (Exception $e) { $destinations_list = []; }

    include '../includes/header.php';
?>
<!-- Custom Premium Fonts and Leaflet map styling -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Obsidian & Gold Luxury Design Overrides */
    body {
        background-color: #050a14;
        color: #f1f5f9;
    }
    
    .serif-accent {
        font-family: 'Playfair Display', 'Georgia', serif;
        font-style: italic;
        color: var(--gold);
    }
    
    /* Elegant details ribbon */
    .details-ribbon {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(197, 160, 89, 0.15);
        border-radius: 100px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 1.5rem 2rem;
        margin-top: -3rem;
        position: relative;
        z-index: 10;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
    }
    
    .ribbon-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0 1.5rem;
        border-right: 1px solid rgba(197, 160, 89, 0.15);
    }
    
    .ribbon-item:last-child {
        border-right: none;
    }
    
    .ribbon-icon {
        color: var(--gold);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(197, 160, 89, 0.08);
        border: 1px solid rgba(197, 160, 89, 0.2);
    }
    
    .ribbon-icon svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }
    
    .ribbon-text span {
        display: block;
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    
    .ribbon-text strong {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: white;
        margin-top: 0.2rem;
    }

    /* Asymmetric Mosaic Grid */
    .mosaic-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .mosaic-item {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid rgba(197, 160, 89, 0.12);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        height: 260px;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .mosaic-item.tall {
        grid-row: span 2;
        height: 536px;
    }
    
    .mosaic-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .mosaic-item:hover img {
        transform: scale(1.06);
    }
    
    .mosaic-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(5, 10, 20, 0.8) 0%, rgba(5, 10, 20, 0) 60%);
        opacity: 0;
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
        transition: opacity 0.4s;
    }
    
    .mosaic-item:hover .mosaic-overlay {
        opacity: 1;
    }
    
    .mosaic-overlay p {
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--gold);
        font-weight: 500;
    }

    /* Luxury Timeline */
    .luxury-timeline {
        position: relative;
        padding-left: 2.5rem;
        margin-top: 3rem;
    }
    
    .luxury-timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: linear-gradient(to bottom, var(--gold) 0%, rgba(197, 160, 89, 0.1) 100%);
    }
    
    .timeline-node {
        position: relative;
        margin-bottom: 2.5rem;
    }
    
    .timeline-node:last-child {
        margin-bottom: 0;
    }
    
    .timeline-bullet {
        position: absolute;
        left: -2.5rem;
        top: 6px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #050a14;
        border: 2px solid var(--gold);
        box-shadow: 0 0 10px var(--gold-glow);
        z-index: 2;
        transition: transform 0.3s;
    }
    
    .timeline-node:hover .timeline-bullet {
        transform: scale(1.3);
        background: var(--gold);
    }
    
    .timeline-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(197, 160, 89, 0.1);
        padding: 2rem;
        border-radius: 16px;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .timeline-box:hover {
        border-color: rgba(197, 160, 89, 0.3);
        background: rgba(255, 255, 255, 0.03);
        transform: translateY(-2px);
    }
    
    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .timeline-header span {
        font-size: 0.8rem;
        color: var(--gold);
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    
    .timeline-header h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: white;
    }

    /* Private Concierge Form */
    .concierge-card {
        background: rgba(5, 10, 20, 0.75);
        backdrop-filter: blur(30px);
        border: 1px solid rgba(197, 160, 89, 0.25);
        padding: 3rem;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    }
    
    .concierge-input-shell {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .concierge-input-shell input, .concierge-input-shell select {
        width: 100%;
        background: transparent;
        border: none;
        border-bottom: 1px solid rgba(197, 160, 89, 0.2);
        padding: 0.8rem 0;
        color: white;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }
    
    .concierge-input-shell select option {
        background: #050a14;
        color: white;
    }
    
    .concierge-input-shell input:focus, .concierge-input-shell select:focus {
        border-color: var(--gold);
    }
    
    .concierge-input-shell label {
        position: absolute;
        left: 0;
        top: -12px;
        font-size: 0.75rem;
        color: var(--gold);
        letter-spacing: 0.05em;
        text-transform: uppercase;
        pointer-events: none;
    }

    /* Beautiful rating badge */
    .rating-badge-floating {
        position: absolute;
        bottom: 2rem;
        right: 2rem;
        background: rgba(5, 10, 20, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(197, 160, 89, 0.3);
        border-radius: 12px;
        padding: 1.2rem 1.8rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        z-index: 10;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    
    .rating-score-num {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--gold);
        line-height: 1;
    }
    
    .rating-stars-col {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    
    .rating-stars-stars {
        color: var(--gold);
        font-size: 0.95rem;
        letter-spacing: 0.05em;
    }
    
    .rating-review-count {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Luxury list style */
    .luxury-list {
        list-style: none;
        padding: 0;
    }
    
    .luxury-list li {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.2rem;
        color: #cbd5e1;
        line-height: 1.6;
    }
    
    .luxury-list li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 6px;
        width: 10px;
        height: 10px;
        background: radial-gradient(circle, var(--gold) 0%, rgba(197, 160, 89, 0.2) 100%);
        border: 1px solid var(--gold);
        box-shadow: 0 0 8px var(--gold-glow);
        transform: rotate(45deg);
    }

    .form-success-card {
        text-align: center;
        padding: 2rem 0;
    }
    
    .form-success-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(197, 160, 89, 0.1);
        border: 1px solid var(--gold);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem auto;
        color: var(--gold);
        font-size: 1.8rem;
        box-shadow: 0 0 20px rgba(197, 160, 89, 0.2);
    }

    .hero-video-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }
    
    .hero-video-container::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(5, 10, 20, 0.3) 0%, rgba(5, 10, 20, 0.8) 100%);
        z-index: 1;
    }
    
    .hero-video-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .details-ribbon {
            grid-template-columns: 1fr 1fr;
            border-radius: 24px;
            padding: 1.5rem;
            gap: 1.5rem;
            margin-top: -2rem;
        }
        .ribbon-item:nth-child(2) {
            border-right: none;
        }
        .ribbon-item {
            padding: 0 0.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .details-ribbon {
            grid-template-columns: 1fr;
            border-radius: 16px;
        }
        .ribbon-item {
            border-right: none;
            border-bottom: 1px solid rgba(197, 160, 89, 0.12);
            padding-bottom: 1rem;
        }
        .ribbon-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .mosaic-grid {
            grid-template-columns: 1fr;
        }
        .mosaic-item.tall {
            height: 260px;
        }
    }
</style>

<!-- JSON-LD Product Schema for SEO -->
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

<!-- The Cinematic Portal (Hero Banner) -->
<header class="hero" style="height: 75vh; position: relative; display: flex; align-items: center; justify-content: center; text-align: center;">
    <div class="hero-video-container">
        <?php 
            $hero_bg = !empty($pkg['banner_image_url']) ? $pkg['banner_image_url'] : $pkg['image_url']; 
        ?>
        <img src="<?php echo htmlspecialchars($hero_bg); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
    </div>
    <div class="container hero-content" style="position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center;">
        <h1 style="font-size: clamp(2.5rem, 5vw, 4.5rem); line-height: 1.1; font-family: 'Inter', sans-serif; font-weight: 700; margin-top: 1rem; text-shadow: 0 4px 12px rgba(0,0,0,0.5); text-transform: capitalize;">
            <?php echo htmlspecialchars($pkg['title']); ?>
        </h1>
        
        <?php if (!empty($pkg['nights']) && !empty($pkg['days'])): ?>
        <div style="margin-top: 1.5rem; font-size: 1.1rem; color: #e2e8f0; text-shadow: 0 2px 4px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap; justify-content: center;">
            <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
            <span><?php echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT); ?> Nights / <?php echo str_pad($pkg['days'], 2, '0', STR_PAD_LEFT); ?> Days</span>
            <span style="opacity: 0.5;">|</span>
            <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <span><?php echo htmlspecialchars($pkg['destination']); ?></span>
        </div>
        <?php endif; ?>

        <button onclick="openLeadModal();" style="margin-top: 2.5rem; background: rgba(10, 10, 10, 0.6); color: var(--gold); border: 1px solid rgba(197, 160, 89, 0.5); padding: 1rem 2.5rem; font-size: 0.95rem; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 20px rgba(197, 160, 89, 0.15); cursor: pointer; display: flex; align-items: center; gap: 0.8rem; text-transform: uppercase; transition: all 0.3s ease; backdrop-filter: blur(10px); letter-spacing: 0.1em;">
            <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
            UNLOCK BESPOKE PRICING
        </button>

        <!-- Inline Customer Review Tag -->
        <div style="margin-top: 3rem; background: rgba(7, 12, 24, 0.85); backdrop-filter: blur(15px); border: 1px solid rgba(197, 160, 89, 0.25); border-radius: 12px; padding: 1.2rem 1.5rem; display: flex; gap: 1.2rem; box-shadow: 0 15px 35px rgba(0,0,0,0.5); z-index: 20;">
            <div style="width: 3px; background: linear-gradient(to bottom, var(--gold), transparent); border-radius: 4px;"></div>
            <div style="text-align: left;">
                <div style="color: #cbd5e1; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.4rem;">Guest Experiences</div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: white; line-height: 1;"><?php echo number_format($rating_score, 1); ?></span>
                    <div>
                        <div style="color: var(--gold); font-size: 1rem; line-height: 1;">★★★★★</div>
                        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.3rem;"><?php echo $rating_count; ?>+ verified reviews</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container" style="padding-bottom: 8rem;">
    
    <!-- The Details Ribbon (Dynamic metadata widgets) -->
    <div class="details-ribbon">
        <!-- Card 1: Tour Code -->
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <svg viewBox="0 0 24 24"><path d="M4 6H2v12h2v-2h2v2h2v-2h2v2h2v-2h2v2h2v-2h2v2h2V6h-2V4h-2v2h-2V4h-2v2h-2V4h-2v2H8V4H6v2H4V6zm4 6H6V9h2v3zm4 0h-2V9h2v3zm4 0h-2V9h2v3z"/></svg>
            </div>
            <div class="ribbon-text">
                <span>Tour Code</span>
                <strong><?php echo $tour_code; ?></strong>
            </div>
        </div>
        <!-- Card 2: Destination -->
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            </div>
            <div class="ribbon-text">
                <span>Destination</span>
                <strong><?php echo htmlspecialchars($pkg['destination']); ?></strong>
            </div>
        </div>
        <!-- Card 3: Tour Type -->
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
            <div class="ribbon-text">
                <span>Escape Level</span>
                <strong style="font-size: 0.8rem; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;"><?php echo $tour_type; ?></strong>
            </div>
        </div>
        <!-- Card 4: Price Block -->
        <div class="ribbon-item">
            <div class="ribbon-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
            </div>
            <div class="ribbon-text">
                <span>Dynamic Pricing</span>
                <strong>
                    &#8377;<?php echo number_format($pkg['price']); ?>
                    <?php if ($original_price): ?>
                        <span style="font-size: 0.75rem; text-decoration: line-through; color: var(--text-muted); font-weight: 400; margin-left: 0.4rem;">&#8377;<?php echo number_format($original_price); ?></span>
                    <?php endif; ?>
                </strong>
            </div>
        </div>
    </div>

    <!-- Asymmetric Editorial Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 5rem; margin-top: 6rem;">
        
        <!-- Left Column: The Narrative -->
        <main>
            <!-- ✦ Overview Section ✦ -->
            <section style="margin-bottom: 3.5rem;">
                <span class="section-label">THE CURATION</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 1.8rem; font-weight: 500;">Overview</h2>

                <?php
                    // Combine highlights + description_rich into the overview
                    $overview_text = '';
                    if (!empty($description_rich)) {
                        $overview_text = $description_rich;
                    } elseif (!empty($highlights_arr)) {
                        $overview_text = implode("\n", $highlights_arr);
                    }
                ?>
                <?php if (!empty($overview_text)): ?>
                <div style="color: #cbd5e1; line-height: 1.9; font-size: 0.97rem;">
                    <?php echo nl2br(htmlspecialchars($overview_text)); ?>
                </div>
                <?php endif; ?>
            </section>

            <!-- ✦ Trust Badges Strip ✦ -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.8rem; margin-bottom: 5rem; padding: 2rem 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(197,160,89,0.12); border-radius: 14px;">
                <?php
                $trust_badges = [
                    ['icon' => '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>', 'label' => 'Safe Travel'],
                    ['icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>', 'label' => 'Flexible Plans'],
                    ['icon' => '<path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>', 'label' => 'Easy Booking'],
                    ['icon' => '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>', 'label' => 'Expert Guides'],
                    ['icon' => '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>', 'label' => '24/7 Support'],
                ];
                foreach ($trust_badges as $badge):
                ?>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.6rem; flex: 1; min-width: 80px; text-align: center;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; border: 1.5px solid rgba(197,160,89,0.4); background: rgba(197,160,89,0.06); display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 22px; height: 22px; fill: var(--gold);" viewBox="0 0 24 24"><?php echo $badge['icon']; ?></svg>
                    </div>
                    <span style="font-size: 0.75rem; color: #94a3b8; letter-spacing: 0.03em; text-align: center; line-height: 1.3;"><?php echo $badge['label']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ✦ Tour Highlights ✦ -->
            <?php if (!empty($highlights_arr)): ?>
            <section style="margin-bottom: 5rem; margin-top: 1rem;">
                <span class="section-label">WHAT'S INCLUDED</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2rem; font-weight: 500;">Tour Highlights</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem 2rem;">
                    <?php foreach ($highlights_arr as $hl): ?>
                    <div style="display: flex; align-items: flex-start; gap: 0.9rem; padding: 0.9rem 0; border-bottom: 1px solid rgba(197,160,89,0.08);">
                        <svg style="width: 18px; height: 18px; fill: var(--gold); flex-shrink: 0; margin-top: 0.1rem;" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                        <span style="color: #e2e8f0; font-size: 0.92rem; line-height: 1.5;"><?php echo htmlspecialchars($hl); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Mosaic Visual Journal (Photos Section) -->
            <?php if (!empty($photos_arr)): ?>
            <section style="margin-bottom: 5rem;">
                <span class="section-label">VISUAL RECORD</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.2rem; font-weight: 500;">The Visual Journal</h2>
                
                <div class="mosaic-grid">
                    <?php if (isset($photos_arr[0])): ?>
                    <div class="mosaic-item tall">
                        <img src="<?php echo htmlspecialchars($photos_arr[0]); ?>" alt="Visual Archive 1">
                        <div class="mosaic-overlay"><p>Immersive Landscapes</p></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($photos_arr[1])): ?>
                    <div class="mosaic-item">
                        <img src="<?php echo htmlspecialchars($photos_arr[1]); ?>" alt="Visual Archive 2">
                        <div class="mosaic-overlay"><p>Luxury Escapes</p></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($photos_arr[2])): ?>
                    <div class="mosaic-item">
                        <img src="<?php echo htmlspecialchars($photos_arr[2]); ?>" alt="Visual Archive 3">
                        <div class="mosaic-overlay"><p>Curated Memories</p></div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- ✦ Interactive Accordion Itinerary ✦ -->
            <section style="margin-bottom: 5rem;">
                <span class="section-label">THE ITINERARY</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.2rem; font-weight: 500;">Day-by-Day Journey</h2>

                <div id="itinerary-accordion" style="border-top: 1px solid rgba(197,160,89,0.15);">
                    <?php foreach ($itinerary as $i => $day): ?>
                    <div class="acc-item" style="border-bottom: 1px solid rgba(197,160,89,0.12);">
                        <button class="acc-trigger" data-index="<?php echo $i; ?>" data-coords="<?php echo htmlspecialchars($day['coords'] ?? ''); ?>" onclick="toggleAccordion(this)" style="width: 100%; background: none; border: none; cursor: pointer; padding: 1.5rem 0; display: flex; align-items: center; gap: 1.2rem; text-align: left;">
                            <!-- Circle +/- icon -->
                            <span class="acc-icon" style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background: rgba(197,160,89,0.12); border: 1.5px solid rgba(197,160,89,0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 1.3rem; font-weight: 300; line-height: 1; transition: all 0.3s;">+</span>
                            <!-- Day label + title -->
                            <div>
                                <span style="font-size: 0.75rem; color: #64748b; letter-spacing: 0.1em; text-transform: uppercase; display: block;">Day <?php echo htmlspecialchars((string)($day['day'] ?? $i+1)); ?></span>
                                <span class="acc-title" style="font-family: 'Inter', sans-serif; font-size: 1.05rem; color: #e2e8f0; font-weight: 500;"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                            </div>
                        </button>
                        <!-- Collapsible description -->
                        <div class="acc-body" style="overflow: hidden; max-height: 0; transition: max-height 0.4s ease, padding 0.3s ease; padding: 0 0 0 3.2rem;">
                            <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.8; padding-bottom: 1.5rem;">
                                <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- ✦ Animated Journey Map ✦ -->
            <?php
            $has_day_coords = false;
            foreach ($itinerary as $d) { if (!empty($d['coords'])) { $has_day_coords = true; break; } }
            $show_map = $has_day_coords || !empty($pkg['map_coords']);
            ?>
            <?php if ($show_map): ?>
            <section style="margin-bottom: 5rem;">
                <span class="section-label">GEOGRAPHIC CONTEXT</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.2rem; font-weight: 500;">Bespoke Route Map</h2>
                <div id="journey-map" style="height: 420px; border-radius: 16px; border: 1px solid rgba(197, 160, 89, 0.15); overflow: hidden;"></div>
            </section>
            <?php endif; ?>

            <!-- Accordion + Map JS -->
            <script>
            // ── Itinerary data from PHP ──────────────────────────────────────
            const itineraryData = <?php echo json_encode(array_values($itinerary)); ?>;
            const fallbackCoords = [<?php
                $mc = $pkg['map_coords'] ?? '';
                $parts = explode(',', $mc);
                echo (count($parts) === 2) ? ((float)trim($parts[0])) . ',' . ((float)trim($parts[1])) : '27.3314,88.6138';
            ?>];

            // ── Build waypoints list ─────────────────────────────────────────
            const waypoints = itineraryData.map((d, i) => {
                if (d.coords && d.coords.trim()) {
                    const p = d.coords.split(',');
                    if (p.length === 2) return { lat: parseFloat(p[0]), lng: parseFloat(p[1]), title: d.title || ('Day '+(i+1)) };
                }
                return null;
            }).filter(Boolean);

            // ── Map init ─────────────────────────────────────────────────────
            let map, carMarker, routeLine, destMarkers = [];

            document.addEventListener('DOMContentLoaded', () => {
                if (!document.getElementById('journey-map')) return;

                const center = waypoints.length ? [waypoints[0].lat, waypoints[0].lng] : fallbackCoords;
                map = L.map('journey-map', { center, zoom: 9, scrollWheelZoom: false });

                // Dark style tile layer
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap, &copy; CartoDB'
                }).addTo(map);

                if (waypoints.length > 0) {
                    // Draw dashed gold route line
                    const latlngs = waypoints.map(w => [w.lat, w.lng]);
                    routeLine = L.polyline(latlngs, {
                        color: '#C5A059',
                        weight: 2,
                        opacity: 0.6,
                        dashArray: '8, 10'
                    }).addTo(map);

                    // Destination label markers
                    waypoints.forEach((wp, i) => {
                        const pin = L.divIcon({
                            className: '',
                            html: `<div style="background:rgba(7,12,24,0.9);border:1px solid rgba(197,160,89,0.5);color:#C5A059;padding:4px 10px;border-radius:20px;font-size:11px;font-family:Inter,sans-serif;white-space:nowrap;">${wp.title}</div>`,
                            iconAnchor: [0, 0]
                        });
                        destMarkers.push(L.marker([wp.lat, wp.lng], { icon: pin }).addTo(map));
                    });

                    // Car marker at start
                    const carIcon = L.divIcon({
                        className: '',
                        html: `<div id="car-icon" style="font-size:26px;filter:drop-shadow(0 0 6px rgba(197,160,89,0.8));transform:rotate(90deg);">🚗</div>`,
                        iconAnchor: [13, 13]
                    });
                    carMarker = L.marker([waypoints[0].lat, waypoints[0].lng], { icon: carIcon, zIndexOffset: 1000 }).addTo(map);

                    map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
                }
            });

            // ── Animate car to coords ─────────────────────────────────────────
            function animateCarTo(lat, lng) {
                if (!carMarker) return;
                const startPos = carMarker.getLatLng();
                const steps = 40;
                let step = 0;
                const dLat = (lat - startPos.lat) / steps;
                const dLng = (lng - startPos.lng) / steps;

                // Rotate car direction
                const angle = Math.atan2(lng - startPos.lng, lat - startPos.lat) * (180 / Math.PI);
                const carEl = document.getElementById('car-icon');
                if (carEl) carEl.style.transform = `rotate(${angle}deg)`;

                const timer = setInterval(() => {
                    step++;
                    const cur = carMarker.getLatLng();
                    carMarker.setLatLng([cur.lat + dLat, cur.lng + dLng]);
                    if (step >= steps) {
                        clearInterval(timer);
                        carMarker.setLatLng([lat, lng]);
                        map.panTo([lat, lng], { animate: true, duration: 0.6 });
                    }
                }, 18);
            }

            // ── Accordion toggle ─────────────────────────────────────────────
            function toggleAccordion(btn) {
                const item = btn.closest('.acc-item');
                const body = item.querySelector('.acc-body');
                const icon = btn.querySelector('.acc-icon');
                const titleEl = btn.querySelector('.acc-title');
                const isOpen = body.style.maxHeight !== '0px' && body.style.maxHeight !== '';

                // Close all
                document.querySelectorAll('.acc-body').forEach(b => { b.style.maxHeight = '0'; b.style.paddingBottom = '0'; });
                document.querySelectorAll('.acc-icon').forEach(ic => { ic.textContent = '+'; ic.style.background = 'rgba(197,160,89,0.12)'; ic.style.color = 'var(--gold)'; });
                document.querySelectorAll('.acc-title').forEach(t => t.style.color = '#e2e8f0');

                if (!isOpen) {
                    body.style.maxHeight = body.scrollHeight + 'px';
                    icon.textContent = '−';
                    icon.style.background = 'var(--gold)';
                    icon.style.color = '#000';
                    titleEl.style.color = 'var(--gold)';

                    // Trigger map animation
                    const coords = btn.dataset.coords;
                    if (coords && coords.trim()) {
                        const p = coords.split(',');
                        if (p.length === 2) animateCarTo(parseFloat(p[0]), parseFloat(p[1]));
                    } else {
                        // fallback: move to indexed waypoint if available
                        const idx = parseInt(btn.dataset.index || 0);
                        if (waypoints[idx]) animateCarTo(waypoints[idx].lat, waypoints[idx].lng);
                    }
                }
            }

            // Open first item by default
            document.addEventListener('DOMContentLoaded', () => {
                const firstBtn = document.querySelector('.acc-trigger');
                if (firstBtn) setTimeout(() => firstBtn.click(), 400);
            });
            </script>


            <!-- Exquisite Inclusions / Exclusions -->
            <section style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; border-top: 1px solid rgba(197, 160, 89, 0.15); padding-top: 5rem;">
                <!-- Inclusions -->
                <div style="background: rgba(101, 163, 13, 0.05); border: 1px solid rgba(101, 163, 13, 0.15); border-radius: 12px; padding: 2.5rem;">
                    <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 2rem; color: #a3e635; font-weight: 500;">Inclusions</h3>
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                        <?php foreach ($inclusions_arr as $inc): ?>
                            <li style="color: #e2e8f0; padding-left: 2.2rem; position: relative; margin-bottom: 1.2rem; line-height: 1.6; font-size: 0.95rem;">
                                <svg style="position: absolute; left: 0; top: 0.2rem; width: 20px; height: 20px; fill: #65a30d;" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                <?php echo htmlspecialchars($inc); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Exclusions -->
                <div style="background: rgba(248, 113, 113, 0.05); border: 1px solid rgba(248, 113, 113, 0.15); border-radius: 12px; padding: 2.5rem;">
                    <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 2rem; color: #fca5a5; font-weight: 500;">Exclusions</h3>
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                        <?php foreach ($exclusions_arr as $exc): ?>
                            <li style="color: #cbd5e1; padding-left: 2.2rem; position: relative; margin-bottom: 1.2rem; line-height: 1.6; font-size: 0.95rem;">
                                <svg style="position: absolute; left: 0; top: 0.2rem; width: 20px; height: 20px; fill: #ef4444;" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                <?php echo htmlspecialchars($exc); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <!-- ✦ Terms & Conditions ✦ -->
            <?php 
                $tc_to_display = (!empty($pkg['use_destination_terms']) && !empty($pkg['dest_terms'])) ? $pkg['dest_terms'] : ($pkg['terms_conditions'] ?? '');
                if (!empty($tc_to_display)): 
            ?>
            <section style="margin-top: 5rem; border-top: 1px solid rgba(197,160,89,0.15); padding-top: 4rem;">
                <span class="section-label">LEGAL & POLICIES</span>
                <h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.5rem; font-weight: 500;">Terms &amp; Conditions</h2>

                <div style="display: flex; flex-direction: column; gap: 0; position: relative;">
                    <div id="tc-content-wrapper" style="position: relative; max-height: 180px; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);">
                        <div style="padding: 1.5rem 0; color: #94a3b8; font-size: 0.95rem; line-height: 1.8;">
                            <?php
                                $tc_text = htmlspecialchars(trim($tc_to_display));
                                $tc_text = nl2br($tc_text);
                                // Replace ##text## with gold span
                                $tc_text = preg_replace('/##(.*?)##/s', '<strong style="color: var(--gold); font-weight: 600; font-family: \'Inter\', sans-serif; letter-spacing: 0.02em;">$1</strong>', $tc_text);
                                echo $tc_text;
                            ?>
                        </div>
                        <div id="tc-fade-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 60px; background: linear-gradient(to top, #050a14 10%, transparent); transition: opacity 0.3s; pointer-events: none; opacity: 1;"></div>
                    </div>

                    <button id="tc-toggle-btn" style="background: none; border: none; color: var(--gold); font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem; padding: 0.5rem 0; font-family: 'Inter', sans-serif; letter-spacing: 0.05em; text-transform: uppercase; width: fit-content; outline: none;">
                        <span id="tc-btn-text">Read More</span>
                        <svg id="tc-btn-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s ease-in-out; transform: rotate(0deg);">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </div>
            </section>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const wrapper = document.getElementById('tc-content-wrapper');
                const btn = document.getElementById('tc-toggle-btn');
                const btnText = document.getElementById('tc-btn-text');
                const btnIcon = document.getElementById('tc-btn-icon');
                const fade = document.getElementById('tc-fade-overlay');

                if (!wrapper || !btn) return;

                // Adjust dynamically in case content is actually shorter than 180px
                if (wrapper.scrollHeight <= 180) {
                    wrapper.style.maxHeight = 'none';
                    btn.style.display = 'none';
                    fade.style.opacity = '0';
                    return;
                }

                btn.addEventListener('click', function() {
                    const isCollapsed = wrapper.style.maxHeight === '180px' || wrapper.style.maxHeight === '';
                    if (isCollapsed) {
                        wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
                        btnText.textContent = 'Read Less';
                        btnIcon.style.transform = 'rotate(180deg)';
                        fade.style.opacity = '0';
                    } else {
                        wrapper.style.maxHeight = '180px';
                        btnText.textContent = 'Read More';
                        btnIcon.style.transform = 'rotate(0deg)';
                        fade.style.opacity = '1';
                    }
                });
            });
            </script>
            <?php endif; ?>

            <!-- ✦ Dynamic Advertisement Banner ✦ -->
            <?php
            if ($pdo) {
                try {
                    $ad = $pdo->query("SELECT * FROM advertisements WHERE page_type = 'package' AND is_active = 1")->fetch();
                    if ($ad):
                        $ad_bg = !empty($ad['image_url']) ? htmlspecialchars($ad['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=2000';
            ?>
            <div class="ad-banner" style="margin-top: 4rem; position: relative; min-height: 280px; border-radius: 16px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; background: url('<?php echo $ad_bg; ?>') no-repeat center center; background-size: cover; display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #050a14 0%, rgba(5, 10, 20, 0.85) 45%, rgba(5, 10, 20, 0.2) 100%); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; padding: 3rem; max-width: 65%; display: flex; flex-direction: column; align-items: flex-start; gap: 1.5rem;">
                    <h3 class="serif" style="font-size: 2.2rem; color: #ffffff; font-weight: 500; line-height: 1.3; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        <?php echo htmlspecialchars($ad['title']); ?>
                    </h3>
                    <a href="<?php echo htmlspecialchars($ad['btn_link']); ?>" class="btn-gold" style="padding: 1rem 2.5rem; font-size: 0.9rem; font-weight: 600; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 4px 20px rgba(197, 160, 89, 0.25); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; color: #000; background: var(--gold); border: none;">
                        <span><?php echo htmlspecialchars($ad['btn_text']); ?></span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
            <?php
                    endif;
                } catch (Exception $e) {}
            }
            ?>
        </main>

        <!-- Right Column: Sticky Private Concierge Lead Form -->
        <aside>
            <div style="position: sticky; top: 120px;">
                <div class="concierge-card" id="formContainer">
                    <h3 class="serif" style="font-size: 1.6rem; margin-bottom: 0.5rem; text-align: center; font-weight: 500;">Private Concierge</h3>
                    <p style="font-size: 0.75rem; text-align: center; color: var(--gold); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 2.5rem;">Request Custom Quotation</p>
                    
                    <form id="luxuryForm" action="api-submit-lead.php" method="POST" class="js-lead-form">
                        <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                        
                        <div class="concierge-input-shell">
                            <input type="text" name="name" required placeholder=" ">
                            <label>Full Guest Name</label>
                        </div>
                        
                        <div class="concierge-input-shell">
                            <input type="tel" name="phone" required placeholder=" ">
                            <label>Secure Phone Number</label>
                        </div>
                        
                        <div class="concierge-input-shell">
                            <input type="email" name="email" required placeholder=" ">
                            <label>Private Email ID</label>
                        </div>
                        
                        <div class="concierge-input-shell">
                            <select name="destination" required>
                                <option value="">Select Destination</option>
                                <?php foreach ($destinations_list as $dest): ?>
                                    <option value="<?php echo htmlspecialchars($dest); ?>" <?php echo strtolower($pkg['destination']) === strtolower($dest) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dest); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Preferred Escape</label>
                        </div>

                        <div class="concierge-input-shell">
                            <input type="date" name="date" required placeholder=" ">
                            <label>Approx Travel Date</label>
                        </div>

                        <div class="form-row" style="margin-bottom: 2.5rem;">
                            <div class="concierge-input-shell" style="margin-bottom: 0;">
                                <input type="number" min="1" name="adults" value="2" required placeholder=" ">
                                <label>Adult Guests</label>
                            </div>
                            <div class="concierge-input-shell" style="margin-bottom: 0;">
                                <input type="number" min="0" name="children" value="0" required placeholder=" ">
                                <label>Children</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-gold" style="width: 100%; padding: 1.2rem; font-size: 0.9rem; letter-spacing: 0.1em; border-radius: 12px; font-weight: 600; box-shadow: 0 0 20px rgba(197,160,89,0.2);">
                            INITIATE BESPOKE PLANNING &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
    </div>

    <!-- ✦ Related Packages Section ("Maybe you like") ✦ -->
    <?php
    if ($pdo) {
        try {
            // Extract keywords from destination (e.g. "Sikkim & Darjeeling" -> ["Sikkim", "Darjeeling"])
            $keywords = preg_split('/[\s&,.\-\/]+|and/i', $pkg['destination']);
            $keywords = array_filter(array_map('trim', $keywords), function($val) {
                return strlen($val) > 2; // only keep words longer than 2 characters
            });

            $related_pkgs = [];

            if (!empty($keywords)) {
                $sql = "SELECT * FROM packages WHERE id != ? AND is_active = 1 AND (";
                $params = [$pkg['id']];
                $or_clauses = [];
                foreach ($keywords as $kw) {
                    $or_clauses[] = "destination LIKE ?";
                    $params[] = '%' . $kw . '%';
                }
                $sql .= implode(" OR ", $or_clauses) . ") LIMIT 6";
                
                $dest_stmt = $pdo->prepare($sql);
                $dest_stmt->execute($params);
                $related_pkgs = $dest_stmt->fetchAll();
            }

            if (!empty($related_pkgs)):
    ?>
    <section class="related-packages-section" style="margin-top: 6rem; border-top: 1px solid rgba(197, 160, 89, 0.15); padding-top: 5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem;">
            <div>
                <span class="section-label" style="color: var(--gold); letter-spacing: 0.25em;">OTHER TOURS</span>
                <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif;">Maybe you <span style="font-style: italic;">like.</span></h2>
            </div>
            <!-- Interactive Carousel Navigation Arrows (visible only if there are enough items to slide) -->
            <?php if (count($related_pkgs) > 3): ?>
            <div style="display: flex; gap: 0.8rem;">
                <button class="carousel-btn prev-related" style="width: 44px; height: 44px; border-radius: 50%; border: 1px solid rgba(197,160,89,0.3); background: transparent; color: var(--gold); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.3s;" onclick="scrollRelated(-1)">&larr;</button>
                <button class="carousel-btn next-related" style="width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--gold); background: transparent; color: var(--gold); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.3s;" onclick="scrollRelated(1)">&rarr;</button>
            </div>
            <?php endif; ?>
        </div>

        <div class="related-slider-container" style="overflow: hidden; position: relative; width: 100%;">
            <div class="related-grid" id="relatedGrid" style="display: flex; gap: 2rem; transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1); width: 100%;">
                <?php foreach ($related_pkgs as $r_pkg): 
                    $r_itinerary = json_decode($r_pkg['itinerary'], true);
                    $r_days = is_array($r_itinerary) ? count($r_itinerary) : 0;
                    $r_nights = max(1, $r_days - 1);
                ?>
                <div class="related-card" style="flex: 0 0 calc(33.333% - 1.34rem); min-width: 290px; background: rgba(255,255,255,0.01); border: 1px solid rgba(197, 160, 89, 0.12); border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); cursor: pointer; box-shadow: 0 10px 30px rgba(0,0,0,0.2);" onclick="window.location.href='package-detail.php?slug=<?php echo $r_pkg['slug']; ?>'">
                    <!-- Thumbnail with zooming and badges -->
                    <div style="position: relative; height: 260px; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($r_pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($r_pkg['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);" class="r-img">
                        <!-- Badges/Durations -->
                        <div style="position: absolute; top: 16px; left: 16px; background: rgba(7, 12, 24, 0.75); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; padding: 6px 14px; border-radius: 50px; backdrop-filter: blur(10px);">
                            <?php echo $r_nights . " NTS / " . $r_days . " DAYS"; ?>
                        </div>
                        <!-- Central Hover Arrow Overlay -->
                        <div style="position: absolute; inset: 0; background: rgba(5,10,20,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.4s;" class="r-overlay">
                            <span style="font-size: 1.8rem; color: #fff; width: 50px; height: 50px; border-radius: 50%; border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; background: rgba(197,160,89,0.1); transform: scale(0.8); transition: transform 0.4s;" class="r-arrow">&rarr;</span>
                        </div>
                    </div>
                    <!-- Body info -->
                    <div style="padding: 1.8rem; display: flex; flex-direction: column; flex-grow: 1; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.65rem; color: var(--gold); letter-spacing: 0.2em; text-transform: uppercase; font-weight: 600;"><?php echo htmlspecialchars($r_pkg['destination']); ?></span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-family: 'Playfair Display', serif; color: white; margin: 0; line-height: 1.4; font-weight: 500; min-height: 3.4rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars($r_pkg['title']); ?>
                        </h3>
                        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1.2rem; margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em;">FROM <strong style="font-size: 1.15rem; color: white; font-family: 'Inter', sans-serif; font-weight: 700; margin-left: 0.4rem;">&#8377;<?php echo number_format($r_pkg['price']); ?></strong></div>
                            <span style="width: 36px; height: 36px; border-radius: 50%; background: var(--gold); color: #000; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 700; transition: transform 0.3s;" class="r-btn">&rarr;</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        let currentTranslate = 0;
        function scrollRelated(direction) {
            const grid = document.getElementById('relatedGrid');
            if (!grid) return;
            const card = grid.querySelector('.related-card');
            if (!card) return;
            
            const cardWidth = card.getBoundingClientRect().width;
            const gap = parseFloat(window.getComputedStyle(grid).gap) || 0;
            const scrollAmount = cardWidth + gap;
            
            const maxScroll = -(grid.scrollWidth - grid.clientWidth);
            currentTranslate += direction * -scrollAmount;
            
            if (currentTranslate > 0) currentTranslate = 0;
            if (currentTranslate < maxScroll) currentTranslate = maxScroll;
            
            grid.style.transform = `translateX(${currentTranslate}px)`;
        }
        </script>

        <style>
            .related-card:hover {
                transform: translateY(-8px);
                border-color: var(--gold) !important;
                box-shadow: 0 15px 35px rgba(197, 160, 89, 0.15) !important;
            }
            .related-card:hover .r-img {
                transform: scale(1.06);
            }
            .related-card:hover .r-overlay {
                opacity: 1;
            }
            .related-card:hover .r-arrow {
                transform: scale(1);
            }
            .related-card:hover .r-btn {
                transform: scale(1.1);
                box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
            }
            @media (max-width: 991px) {
                .related-card {
                    flex: 0 0 calc(50% - 1rem) !important;
                }
            }
            @media (max-width: 640px) {
                .related-card {
                    flex: 0 0 100% !important;
                }
            }
        </style>
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("luxuryForm");
    const container = document.getElementById("formContainer");

    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            
            // Render glowing loader state on the button
            const btn = form.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "COMMUNICATING CONCIERGE...";
            btn.style.boxShadow = "0 0 30px var(--gold)";

            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Success state in Obsidian/Gold Card
                    container.innerHTML = `
                        <div class="form-success-card">
                            <div class="form-success-icon">✓</div>
                            <h3 class="serif" style="font-size: 1.6rem; color: white; margin-bottom: 0.8rem; font-weight: 500;">Connection Secure</h3>
                            <p style="color: var(--gold); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 1.5rem;">Lead Synchronized</p>
                            <p style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.7; margin-bottom: 2rem;">
                                Thank you. Your luxury curation request has been received. Our dedicated travel host will call you shortly to outline the next steps.
                            </p>
                            <button onclick="window.location.reload();" class="btn-outline" style="padding: 0.8rem 2rem; font-size: 0.8rem; letter-spacing: 0.08em; border-radius: 8px;">
                                SUBMIT NEW ESCAPE
                            </button>
                        </div>
                    `;
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.boxShadow = "none";
                    alert(data.message || "An unexpected issue occurred. Please try again.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.style.boxShadow = "none";
                alert("Network communication error. Please try again.");
            });
        });
    }
});
</script>

<!-- Lead Capture Modal Overlay -->
<div id="leadCaptureModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 9999; align-items: flex-start; justify-content: center; backdrop-filter: blur(15px); padding: 2rem 1rem; overflow-y: auto;">
    <div style="background: #050a14; width: 100%; max-width: 950px; border-radius: 16px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; display: flex; flex-direction: row; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8); margin: auto;">
        
        <!-- Close Button -->
        <button onclick="closeLeadModal();" style="position: absolute; top: 1.5rem; right: 1.5rem; width: 36px; height: 36px; background: rgba(255,255,255,0.05); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); border-radius: 50%; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.3s;">&times;</button>
        
        <!-- Left Column: Form -->
        <div style="flex: 1.2; padding: 3rem; background: #050a14; color: #f1f5f9;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <svg style="width: 40px; height: 40px; fill: var(--gold); margin: 0 auto 0.8rem auto;" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zM7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 2.88-2.88 7.19-5 9.88C9.92 16.21 7 11.85 7 9z"/><circle cx="12" cy="9" r="2.5"/></svg>
                <h2 style="font-family: 'Playfair Display', serif; font-weight: 400; font-size: 1.8rem; color: #ffffff; letter-spacing: 0.02em;">Curate Your Escape</h2>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem; letter-spacing: 0.05em; text-transform: uppercase;">Unlock bespoke pricing</p>
            </div>
            
            <form id="modalLeadForm" action="api-submit-lead.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem;">
                <input type="hidden" name="package_id" value="<?php echo $pkg['id']; ?>">
                
                <div style="grid-column: span 2;">
                    <input type="text" name="name" required placeholder="Full Name" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none; transition: border-color 0.3s;">
                </div>
                <div>
                    <input type="tel" name="phone" required placeholder="Phone Number" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none;">
                </div>
                <div>
                    <input type="email" name="email" required placeholder="Email Address" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none;">
                </div>
                <div>
                    <input type="date" name="date" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #94a3b8; outline: none; color-scheme: dark;">
                </div>
                <div>
                    <input type="number" min="1" name="adults" value="2" required placeholder="Guests" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none;">
                </div>
                <div style="grid-column: span 2;">
                    <select name="destination" required style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none;">
                        <option value="" style="background: #050a14; color: white;">Select Destination</option>
                        <?php foreach ($destinations_list as $dest): ?>
                            <option value="<?php echo htmlspecialchars($dest); ?>" style="background: #050a14; color: white;" <?php echo strtolower($pkg['destination']) === strtolower($dest) ? 'selected' : ''; ?>><?php echo htmlspecialchars($dest); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="grid-column: span 2;">
                    <textarea name="message" rows="2" placeholder="Any specific requirements?" style="width: 100%; padding: 1rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(197, 160, 89, 0.2); border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #f1f5f9; outline: none; resize: none;"></textarea>
                </div>
                
                <div style="grid-column: span 2; display: flex; align-items: center; border: 1px solid rgba(197, 160, 89, 0.2); padding: 0.8rem 1rem; border-radius: 8px; width: fit-content; gap: 0.8rem; background: rgba(255,255,255,0.02);">
                    <input type="checkbox" required id="modal_robot" style="width: 16px; height: 16px; accent-color: var(--gold);">
                    <label for="modal_robot" style="font-size: 0.85rem; color: #94a3b8; cursor: pointer;">I am human</label>
                </div>
                
                <div style="grid-column: span 2; margin-top: 1rem;">
                    <button type="submit" id="modalSubmitBtn" style="width: 100%; padding: 1rem; background: var(--gold); color: #000; border: none; border-radius: 8px; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.1em; cursor: pointer; text-transform: uppercase; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s; box-shadow: 0 0 20px rgba(197, 160, 89, 0.3);">
                        <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
                        REQUEST PRIVATE QUOTE
                    </button>
                </div>
            </form>
            <div id="modalFormSuccess" style="display: none; text-align: center; padding: 2rem 0;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(197, 160, 89, 0.1); border: 1px solid var(--gold); color: var(--gold); font-size: 2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">✓</div>
                <h3 style="font-size: 1.8rem; font-family: 'Playfair Display', serif; color: white; margin-bottom: 0.5rem;">Connection Secure</h3>
                <p style="color: #94a3b8; margin-bottom: 2rem;">Your luxury curation request has been received. Our concierge will contact you shortly.</p>
                <button onclick="closeLeadModal();" style="padding: 0.8rem 2.5rem; background: transparent; color: var(--gold); border: 1px solid var(--gold); border-radius: 50px; cursor: pointer; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.8rem;">Close</button>
            </div>
        </div>
        
        <!-- Right Column: Benefits -->
        <div style="flex: 1; background: #070c18; padding: 3rem 2.5rem; border-left: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: white; margin-bottom: 2.5rem; font-weight: 400;">The Leisure Loop Experience</h3>
            
            <ul style="list-style: none; padding: 0; margin: 0 0 3rem 0; display: flex; flex-direction: column; gap: 1.5rem; font-size: 0.9rem; color: #cbd5e1;">
                <li style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="color: var(--gold); margin-top: 0.1rem;">
                        <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                    </div>
                    <div>
                        <div style="color: white; font-weight: 600; margin-bottom: 0.2rem;">Expert Consultation</div>
                        <div style="color: #64748b; font-size: 0.8rem;">Bespoke planning by specialists.</div>
                    </div>
                </li>
                <li style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="color: var(--gold); margin-top: 0.1rem;">
                        <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    </div>
                    <div>
                        <div style="color: white; font-weight: 600; margin-bottom: 0.2rem;">Absolute Privacy</div>
                        <div style="color: #64748b; font-size: 0.8rem;">Zero spam. Your data is encrypted.</div>
                    </div>
                </li>
                <li style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="color: var(--gold); margin-top: 0.1rem;">
                        <svg style="width: 18px; height: 18px; fill: currentColor;" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/></svg>
                    </div>
                    <div>
                        <div style="color: white; font-weight: 600; margin-bottom: 0.2rem;">Verified Excellence</div>
                        <div style="color: #64748b; font-size: 0.8rem;">Consistent 5-star guest ratings.</div>
                    </div>
                </li>
            </ul>
            
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-top: auto; text-align: center;">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Direct Concierge</div>
                <div style="font-weight: 700; color: var(--gold); font-size: 1.2rem; letter-spacing: 0.05em;">+91 98000 98000</div>
            </div>
        </div>
        
        <!-- Mobile styling for modal -->
        <style>
            @media (max-width: 768px) {
                #leadCaptureModal > div {
                    flex-direction: column !important;
                    height: 95vh;
                    overflow-y: auto;
                }
                #modalLeadForm {
                    grid-template-columns: 1fr !important;
                }
                #modalLeadForm > div {
                    grid-column: span 1 !important;
                }
            }
        </style>
    </div>
</div>

<script>
// ── Modal open / close helpers ──────────────────────────────────────────────
function openLeadModal() {
    const modal = document.getElementById('leadCaptureModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // prevent background scroll
    // Scroll modal to top each time it opens
    modal.scrollTop = 0;
}

function closeLeadModal() {
    document.getElementById('leadCaptureModal').style.display = 'none';
    document.body.style.overflow = ''; // restore background scroll
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLeadModal();
});

// Close when clicking the dark backdrop (outside the inner card)
document.getElementById('leadCaptureModal').addEventListener('click', function(e) {
    if (e.target === this) closeLeadModal();
});

// ── Form submit handler ──────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", function() {
    const modalForm = document.getElementById("modalLeadForm");
    
    if (modalForm) {
        modalForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const btn = document.getElementById("modalSubmitBtn");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "Submitting...";
            
            const formData = new FormData(modalForm);

            fetch(modalForm.action, {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    modalForm.style.display = 'none';
                    document.getElementById('modalFormSuccess').style.display = 'block';
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert(data.message || "An unexpected issue occurred.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert("Network communication error.");
            });
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
