<?php 
    require_once '../config/db.php';
    require_once '../includes/functions.php';
    
    $page_title = "Signature Occasions & Events | Leisure Loop Trip";
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/occasions.css">
<?php
// Fetch Premium Properties
    $premium_hotels = [];
    if ($pdo) {
        try {
            $premium_hotels = $pdo->query("SELECT * FROM hotels WHERE star_category >= 4 ORDER BY created_at DESC LIMIT 8")->fetchAll();
        } catch (Exception $e) {
            $premium_hotels = [];
        }
    }

    // Fetch Domestic Destinations
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
        ['name' => 'X\'Mass', 'slug' => 'xmass', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'New Year', 'slug' => 'new-year', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Pongal', 'slug' => 'pongal', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Ganesh Chaturthi', 'slug' => 'ganesh-chaturthi', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Rath Yatra', 'slug' => 'rath-yatra', 'img' => 'assets/img/pkg.jpg'],
        ['name' => 'Holi', 'slug' => 'holi', 'img' => 'assets/img/pkg.jpg'],
    ];

    // Helper Functions for rendering tracks
    function renderPropertiesTrack($trackId, $title, $subtitle, $icon, $hotels) {
        if (empty($hotels)) return;
        ?>
        <section class="trending-tours-section">
            <div class="trending-header-bar">
                <div class="trending-title-group">
                    <h2 class="trending-title">
                        <span style="color: var(--gold);"><span class="material-symbols-outlined" style="font-size:inherit;"><?php echo $icon; ?></span></span> <?php echo $title; ?>
                    </h2>
                    <p class="trending-subtitle"><?php echo $subtitle; ?></p>
                </div>
            </div>
            <div class="trending-track-wrapper">
                <div id="<?php echo $trackId; ?>" class="trending-cards-track">
                    <?php foreach ($hotels as $hotel): 
                        $h_img = !empty($hotel['main_image']) ? $hotel['main_image'] : 'assets/img/pkg.jpg';
                        $h_name = $hotel['name'];
                        $h_place = $hotel['place'];
                        $h_stars = (int)$hotel['star_category'];
                    ?>
                    <a href="javascript:void(0);" class="trending-tour-card" style="display:block; opacity:1;">
                        <div class="trending-top-badges">
                            <span class="badge-trending-type">⭐️ <?php echo $h_stars; ?> Star</span>
                        </div>
                        <img src="<?php echo htmlspecialchars($h_img); ?>" alt="<?php echo htmlspecialchars($h_name); ?>" class="trending-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="trending-card-overlay">
                            <div class="trending-card-dest">
                                <span class="material-symbols-outlined" style="font-size: 1rem;">location_on</span>
                                <?php echo htmlspecialchars($h_place); ?>
                            </div>
                            <h3 class="trending-card-title"><?php echo htmlspecialchars($h_name); ?></h3>
                            <div style="font-size: 0.82rem; color: rgba(255,255,255,0.7); margin-bottom: 8px; font-weight: 500;">Luxury Stay</div>
                            <div class="trending-card-footer">
                                <span class="btn-trending-explore" style="width: 100%; text-align: center; justify-content: center;">
                                    Request Quote <span class="material-symbols-outlined" style="font-size: 1rem;">mail</span>
                                </span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }

    function renderDestinationsTrack($trackId, $title, $subtitle, $icon, $destinations) {
        if (empty($destinations)) return;
        ?>
        <section class="sanctuaries-section-wrap">
            <div class="sanctuaries-glass-container">
                <div class="sanctuaries-header">
                    <div class="sanctuaries-title-box">
                        <h2 class="sanctuaries-title">
                            <span class="material-symbols-outlined gold-icon-badge"><?php echo $icon; ?></span> 
                            <?php echo $title; ?>
                        </h2>
                        <p class="sanctuaries-subtitle"><?php echo $subtitle; ?></p>
                    </div>
                </div>
                <div class="sanctuaries-track-wrapper">
                    <div class="sanctuaries-cards-track" id="<?php echo $trackId; ?>">
                        <?php foreach ($destinations as $s_dest): 
                            if (isset($s_dest['img'])) { // Festival array
                                $sd_img = $s_dest['img'];
                                $sd_name = $s_dest['name'];
                                $sd_link = "all-tours.php?theme=" . urlencode($s_dest['slug'] ?? '');
                            } else { // Database Destination
                                $sd_img = !empty($s_dest['card_image']) ? $s_dest['card_image'] : (!empty($s_dest['cover_image']) ? $s_dest['cover_image'] : 'assets/img/pkg.jpg');
                                $sd_name = $s_dest['name'];
                                $sd_link = "all-tours.php?destination=" . urlencode(trim($sd_name)) . "&type=domestic";
                            }
                        ?>
                        <a href="<?php echo htmlspecialchars($sd_link); ?>" class="sanctuary-portrait-card signature-dest-card" style="display:block; opacity:1;">
                            <img src="<?php echo htmlspecialchars($sd_img); ?>" alt="<?php echo htmlspecialchars($sd_name); ?>" class="sanctuary-card-img" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                            <div class="sanctuary-card-overlay">
                                <h3 class="sanctuary-dest-name"><?php echo htmlspecialchars($sd_name); ?></h3>
                                <div class="sanctuary-dest-explore">
                                    <span>Explore Tours</span>
                                    <span class="material-symbols-outlined" style="font-size: 0.95rem;">arrow_forward</span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
?>

<main>
    <!-- Main Hero -->
    <section class="packages-hero" style="position:relative; height: 60vh; min-height: 500px; display: flex; align-items: center; overflow: hidden; background: #000;">
        <img src="assets/img/events-hero.jpg" style="position: absolute; width: 100%; height: 100%; object-fit: cover; opacity: 0.5;" alt="Events Hero" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
        <div style="position: absolute; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,1) 0%, transparent 60%);"></div>
        <div style="position: relative; z-index: 2; padding: 0 4rem; max-width: 1400px; margin: 0 auto; width: 100%;">
            <span style="color: var(--gold); font-size:1.1rem; font-weight: 600; text-transform:uppercase; letter-spacing: 2px; margin-bottom: 10px; display:block;">Signature Occasions</span>
            <h1 style="font-size: 4.5rem; font-weight: 700; color: white; margin-bottom: 20px; font-family: var(--font-heading);">Celebrate Life</h1>
            <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 30px; max-width: 600px;">Curated festive tours, romantic getaways, destination weddings and corporate retreats designed for unforgettable memories.</p>
        </div>
    </section>

    <!-- Destination Weddings -->
    <?php 
    renderPropertiesTrack('wedHotels', 'Luxury Wedding Venues', 'Handpicked luxury venues and resorts curated perfectly for your special day.', 'favorite', $premium_hotels); 
    renderDestinationsTrack('wedDests', 'Top Wedding Destinations in India', 'Discover iconic locales across India, perfectly suited to celebrate your union.', 'celebration', $domestic_destinations);
    ?>

    <!-- Valentine's -->
    <?php 
    renderPropertiesTrack('valHotels', 'Romantic Getaways & Resorts', 'Handpicked luxury venues and resorts curated perfectly for couples.', 'volunteer_activism', $premium_hotels); 
    renderDestinationsTrack('valDests', 'Top Romantic Destinations in India', 'Discover iconic locales across India, perfectly suited for your romantic escape.', 'favorite', $domestic_destinations);
    ?>

    <!-- Corporate -->
    <?php 
    renderPropertiesTrack('corpHotels', 'Elite Corporate Venues', 'Handpicked luxury hotels and resorts curated perfectly for corporate offsites.', 'business_center', $premium_hotels); 
    renderDestinationsTrack('corpDests', 'Top Corporate Destinations in India', 'Explore iconic locations across India, perfectly suited for team building and MICE.', 'location_city', $domestic_destinations);
    ?>

    <!-- Festivals -->
    <?php 
    renderDestinationsTrack('festivalsTrack', 'Popular Festivals in India', 'Discover vibrant cultural celebrations and book curated tours for each occasion.', 'celebration', $indian_festivals);
    renderDestinationsTrack('festDests', 'Top Festive Destinations in India', 'Explore iconic locations across India, perfectly suited to experience grand festivals.', 'local_fire_department', $domestic_destinations);
    ?>

</main>
<script src="js/modules/content-pages.js" defer></script>
<?php include '../includes/footer.php'; ?>