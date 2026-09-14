<?php 
    require_once '../config/db.php';
    require_once '../includes/functions.php';
    
    $page_title = "Signature Occasions & Events | Leisure Loop Trip";
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/events.css">

<?php
    $premium_hotels = [];
    if ($pdo) {
        try {
            $premium_hotels = $pdo->query("SELECT * FROM hotels WHERE star_category >= 4 ORDER BY created_at DESC LIMIT 8")->fetchAll();
        } catch (Exception $e) {
            $premium_hotels = [];
        }
    }

    $domestic_destinations = [];
    if ($pdo) {
        try {
            $domestic_destinations = $pdo->query("SELECT * FROM destinations WHERE is_active = 1 AND category = 'domestic' ORDER BY display_order ASC LIMIT 8")->fetchAll();
        } catch (Exception $e) {
            $domestic_destinations = [];
        }
    }

    $indian_festivals = [
        ['name' => 'Durga Puja', 'slug' => 'durga-puja', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Diwali', 'slug' => 'diwali', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Christmas', 'slug' => 'xmass', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'New Year', 'slug' => 'new-year', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Pongal', 'slug' => 'pongal', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Ganesh Chaturthi', 'slug' => 'ganesh-chaturthi', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Rath Yatra', 'slug' => 'rath-yatra', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Holi', 'slug' => 'holi', 'img' => 'assets/img/pkg.jpg'],
    ];

    function renderPropertiesTrack($trackId, $title, $subtitle, $icon, $hotels, $eventType) {
        if (empty($hotels)) return;
        ?>
        <section class="events-section-wrap">
            <div class="events-header-bar">
                <div class="events-title-group">
                    <h2 class="events-section-title">
                        <span class="material-symbols-outlined gold-icon"><?php echo $icon; ?></span> 
                        <?php echo htmlspecialchars($title); ?>
                    </h2>
                    <p class="events-section-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
                </div>
                <div class="events-controls">
                    <button class="events-nav-btn" data-action="scroll-track" data-target="<?php echo $trackId; ?>" data-dir="-1" aria-label="Previous">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button class="events-nav-btn" data-action="scroll-track" data-target="<?php echo $trackId; ?>" data-dir="1" aria-label="Next">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
            <div class="events-track-wrapper">
                <div id="<?php echo $trackId; ?>" class="events-cards-track">
                    <?php foreach ($hotels as $hotel): 
                        $h_img = !empty($hotel['main_image']) ? $hotel['main_image'] : 'assets/img/pkg.jpg';
                        $h_name = $hotel['name'];
                        $h_place = $hotel['place'];
                        $h_stars = (int)$hotel['star_category'];
                    ?>
                    <div class="events-venue-card" data-action="open-event-quote" data-event-type="<?php echo htmlspecialchars($eventType, ENT_QUOTES); ?>" data-venue="<?php echo htmlspecialchars($h_name, ENT_QUOTES); ?>">
                        <div class="events-top-badges">
                            <span class="events-badge-rating">⭐️ <?php echo $h_stars; ?> Star</span>
                        </div>
                        <img src="<?php echo htmlspecialchars($h_img); ?>" alt="<?php echo htmlspecialchars($h_name); ?>" class="events-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="events-card-overlay">
                            <div class="events-card-dest">
                                <span class="material-symbols-outlined" style="font-size: 1rem;">location_on</span>
                                <?php echo htmlspecialchars($h_place); ?>
                            </div>
                            <h3 class="events-card-title"><?php echo htmlspecialchars($h_name); ?></h3>
                            <div class="events-card-sub">Curated Luxury Venue</div>
                            <button type="button" class="events-btn-quote">
                                Request Quote <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }

    function renderDestinationsTrack($trackId, $title, $subtitle, $icon, $destinations) {
        if (empty($destinations)) return;
        ?>
        <section class="events-section-wrap">
            <div class="events-header-bar">
                <div class="events-title-group">
                    <h2 class="events-section-title">
                        <span class="material-symbols-outlined gold-icon"><?php echo $icon; ?></span> 
                        <?php echo htmlspecialchars($title); ?>
                    </h2>
                    <p class="events-section-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
                </div>
                <div class="events-controls">
                    <button class="events-nav-btn" data-action="scroll-track" data-target="<?php echo $trackId; ?>" data-dir="-1" aria-label="Previous">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button class="events-nav-btn" data-action="scroll-track" data-target="<?php echo $trackId; ?>" data-dir="1" aria-label="Next">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
            <div class="events-track-wrapper">
                <div id="<?php echo $trackId; ?>" class="events-cards-track">
                    <?php foreach ($destinations as $s_dest): 
                        if (isset($s_dest['img'])) {
                            $sd_img = $s_dest['img'];
                            $sd_name = $s_dest['name'];
                            $sd_link = "all-tours.php?theme=" . urlencode($s_dest['slug'] ?? '');
                        } else {
                            $sd_img = !empty($s_dest['card_image']) ? $s_dest['card_image'] : (!empty($s_dest['cover_image']) ? $s_dest['cover_image'] : 'assets/img/pkg.jpg');
                            $sd_name = $s_dest['name'];
                            $sd_link = "all-tours.php?destination=" . urlencode(trim($sd_name)) . "&type=domestic";
                        }
                    ?>
                    <a href="<?php echo htmlspecialchars($sd_link); ?>" class="events-portrait-card">
                        <img src="<?php echo htmlspecialchars($sd_img); ?>" alt="<?php echo htmlspecialchars($sd_name); ?>" class="events-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="events-portrait-overlay">
                            <h3 class="events-portrait-name"><?php echo htmlspecialchars($sd_name); ?></h3>
                            <div class="events-portrait-action">
                                <span>Explore Tours</span>
                                <span class="material-symbols-outlined" style="font-size: 0.95rem;">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
?>

<main>
    <!-- Main Hero -->
    <section class="events-hero">
        <img src="assets/img/events-hero.jpg" class="events-hero-img" alt="Events Hero" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
        <div class="events-hero-overlay"></div>
        <div class="events-hero-content">
            <span class="events-hero-badge">
                <span class="material-symbols-outlined" style="font-size: 1rem;">auto_awesome</span> Signature Occasions
            </span>
            <h1 class="events-hero-title">Celebrate Life</h1>
            <p class="events-hero-desc">Curated festive tours, romantic getaways, destination weddings, and corporate retreats designed for unforgettable memories.</p>
        </div>
    </section>

    <!-- Destination Weddings -->
    <?php 
    renderPropertiesTrack('wedHotels', 'Luxury Wedding Venues', 'Handpicked luxury venues and resorts curated perfectly for your special day.', 'favorite', $premium_hotels, 'Destination Wedding'); 
    renderDestinationsTrack('wedDests', 'Top Wedding Destinations in India', 'Discover iconic locales across India, perfectly suited to celebrate your union.', 'celebration', $domestic_destinations);
    ?>

    <!-- Romantic & Honeymoon Getaways -->
    <?php 
    renderPropertiesTrack('valHotels', 'Romantic Getaways & Resorts', 'Handpicked luxury venues and boutique resorts curated for couples.', 'volunteer_activism', $premium_hotels, 'Romantic Getaway'); 
    renderDestinationsTrack('valDests', 'Top Romantic Destinations in India', 'Discover iconic locales across India, perfectly suited for your romantic escape.', 'favorite', $domestic_destinations);
    ?>

    <!-- Corporate Offsites & MICE -->
    <?php 
    renderPropertiesTrack('corpHotels', 'Elite Corporate Venues', 'Handpicked luxury hotels and resorts curated perfectly for corporate offsites.', 'business_center', $premium_hotels, 'Corporate Offsite'); 
    renderDestinationsTrack('corpDests', 'Top Corporate Destinations in India', 'Explore iconic locations across India, perfectly suited for team building and MICE.', 'location_city', $domestic_destinations);
    ?>

    <!-- Festivals of India -->
    <?php 
    renderDestinationsTrack('festivalsTrack', 'Popular Festivals in India', 'Discover vibrant cultural celebrations and book curated tours for each occasion.', 'celebration', $indian_festivals);
    renderDestinationsTrack('festDests', 'Top Festive Destinations in India', 'Explore iconic locations across India, perfectly suited to experience grand festivals.', 'local_fire_department', $domestic_destinations);
    ?>
</main>

<!-- Unified Event Quote Inquiry Modal -->
<div id="eventsQuoteModal" class="events-modal-overlay">
    <div class="events-modal-content">
        <span class="events-modal-close" data-action="close-modal" data-target="#eventsQuoteModal">&times;</span>
        <h3 id="eventModalTitle" class="modal-title">Plan Your Celebration</h3>
        <p id="eventModalSubtitle" class="modal-subtitle">Bespoke event planning & luxury accommodations</p>
        
        <form id="eventsQuoteForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="event_type" id="eventInputType" value="Signature Occasion">
            <input type="hidden" name="venue_name" id="eventInputVenue" value="">
            
            <div class="events-form-group">
                <label>Your Name *</label>
                <input type="text" class="events-form-input" name="name" required placeholder="Enter full name">
            </div>
            <div class="events-form-group">
                <label>Contact Number *</label>
                <input type="tel" class="events-form-input" name="phone" required placeholder="+91 xxxxx xxxxx">
            </div>
            <div class="events-form-group">
                <label>Email Address</label>
                <input type="email" class="events-form-input" name="email" placeholder="Optional but recommended">
            </div>
            <div class="events-form-group">
                <label>Estimated Guests / Dates</label>
                <input type="text" class="events-form-input" name="details" placeholder="e.g. 150 guests, Nov 2026">
            </div>
            
            <button type="submit" class="events-btn-submit" id="eventSubmitBtn">Submit Celebration Request</button>
            <div id="eventModalMsg" class="events-feedback-msg"></div>
        </form>
    </div>
</div>

<script src="js/modules/events.js" defer></script>
<?php include '../includes/footer.php'; ?>