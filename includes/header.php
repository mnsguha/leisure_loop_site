<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Leisure Loop Trip | Elite Travel Experiences'; ?></title>
    
    <meta name="description" content="<?php echo $meta_desc ?? 'Cinematic travel experiences for the modern explorer. Bespoke itineraries and world-class service by Leisure Loop Trip.'; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords ?? 'Sikkim tours, Luxury travel India, Himalayan escapes, Leisure Loop Trip'; ?>">

    <?php
    // Canonical Mobile View Detection (SSOT)
    $is_mobile_view = !empty($isMobile) || (isset($_GET['view']) && $_GET['view'] === 'mobile');
    ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Montserrat:wght@600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300..400,0,0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js" defer></script>
    
    <!-- Base Stylesheets -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/modals.css?v=<?php echo time(); ?>">

    <!-- Mobile-Exclusive Stylesheets -->
    <?php if ($is_mobile_view): ?>
    <link rel="stylesheet" href="css/mobile-splash.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/mobile-views.css?v=<?php echo time(); ?>">
    <?php endif; ?>
</head>
<body class="<?php echo !empty($body_class) ? $body_class : (!empty($is_home_page) ? 'has-topbar' : 'no-topbar'); ?>">
    <?php
        require_once __DIR__ . '/../config/db.php';
        $site_logo_path = __DIR__ . '/../public/assets/img/leisure.png';
        $site_logo_url = 'assets/img/leisure.png';
        $has_site_logo = file_exists($site_logo_path);

        // Fetch dynamic navigation data for desktop header only
        $nav_themes = [];
        $nav_domestic = [];
        $nav_international = [];
        $grouped_packages = [];

        if ($pdo && !$is_mobile_view) {
            try {
                if (!isset($settings)) {
                    $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
                }
                
                $nav_themes = $pdo->query("SELECT name FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                $nav_domestic = $pdo->query("SELECT name, slug FROM destinations WHERE category = 'domestic' AND is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                $nav_international = $pdo->query("SELECT name, slug FROM destinations WHERE category = 'international' AND is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                
                $all_pkgs = $pdo->query("SELECT title, slug, destination FROM packages WHERE is_active = 1 ORDER BY title ASC")->fetchAll();
                foreach ($all_pkgs as $nav_pkg) {
                    $destKey = strtolower(trim($nav_pkg['destination']));
                    $grouped_packages[$destKey][] = $nav_pkg;
                }
            } catch (Exception $e) {}
        }
    ?>
    
    <?php if (!$is_mobile_view): ?>
        <?php
        $current_script = basename($_SERVER['SCRIPT_NAME']);
        $current_qs = $_SERVER['QUERY_STRING'] ?? '';
        
        $active_nav = 'home';
        if ($current_script == 'destinations.php') $active_nav = 'destinations';
        elseif (($current_script == 'packages.php' && strpos($current_qs, 'type=fixed') === false && strpos($current_qs, 'filter=offers') === false) || $current_script == 'all-tours.php') $active_nav = 'tours';
        elseif ($current_script == 'hotels.php' || $current_script == 'hotel-detail.php') $active_nav = 'hotels';
        elseif ($current_script == 'cabs.php' || $current_script == 'cab-detail.php') $active_nav = 'cabs';
        elseif ($current_script == 'packages.php' && strpos($current_qs, 'type=fixed') !== false) $active_nav = 'fixed';
        elseif ($current_script == 'events.php') $active_nav = 'events';
        elseif ($current_script == 'packages.php' && strpos($current_qs, 'filter=offers') !== false) $active_nav = 'offers';

        $show_top_bar = !empty($is_home_page);
        if ($show_top_bar): 
        ?>
        <!-- Top Navigation Bar Strip (Desktop Only) -->
        <div class="top-bar-strip">
            <div class="container top-bar-container">
                <div class="top-bar-left">
                    <a href="mailto:<?php echo htmlspecialchars($settings['contact_email'] ?? 'enquiry@leisurelooptrip.com'); ?>" class="top-bar-email">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span><?php echo htmlspecialchars($settings['contact_email'] ?? 'enquiry@leisurelooptrip.com'); ?></span>
                    </a>
                </div>
                <div class="top-bar-right">
                    <a href="about.php" class="top-bar-link" title="About Us">
                        <span class="material-symbols-outlined top-bar-icon">info</span>
                        <span>About Us</span>
                    </a>
                    <span class="top-bar-divider">|</span>
                    <a href="contact.php" class="top-bar-link" title="Contact Us">
                        <span class="material-symbols-outlined top-bar-icon">support_agent</span>
                        <span>Contact Us</span>
                    </a>
                    <span class="top-bar-divider">|</span>
                    <a href="privacy.php" class="top-bar-link" title="Privacy Policy">
                        <span class="material-symbols-outlined top-bar-icon">shield</span>
                        <span>Privacy Policy</span>
                    </a>
                    <span class="top-bar-divider">|</span>
                    <a href="refund.php" class="top-bar-link" title="Refund Policy">
                        <span class="material-symbols-outlined top-bar-icon">currency_exchange</span>
                        <span>Refund Policy</span>
                    </a>
                    <span class="top-bar-divider">|</span>
                    <a href="careers.php" class="top-bar-link" title="Careers">
                        <span class="material-symbols-outlined top-bar-icon">work</span>
                        <span>Careers</span>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Navigation (EaseMyTrip-Inspired Solid Header - Desktop Only) -->
        <nav class="glass-nav solid-nav-header option-c<?php echo !$show_top_bar ? ' nav-no-topbar' : ''; ?>">
            <div class="container nav-content">
                <div class="logo">
                    <a href="index.php" class="logo-link-reset">
                        <?php if ($has_site_logo): ?>
                        <img src="<?php echo $site_logo_url; ?>" alt="Leisure Loop Trip" class="site-logo">
                        <?php else: ?>
                        <span class="logo-text">LEISURE <span class="text-gold">LOOP</span></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <ul class="nav-links nav-ribbon">
                    <li class="nav-item-home">
                        <a href="index.php" class="nav-ribbon-btn home-ribbon-btn<?php echo $active_nav == 'home' ? ' active-nav-btn' : ''; ?>" title="Home">
                            <span class="material-symbols-outlined nav-ribbon-icon">home</span>
                            <span class="nav-ribbon-label">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="destinations.php" class="nav-ribbon-btn<?php echo $active_nav == 'destinations' ? ' active-nav-btn' : ''; ?>" title="Explore Destinations">
                            <span class="material-symbols-outlined nav-ribbon-icon">explore</span>
                            <span class="nav-ribbon-label">Destinations</span>
                        </a>
                    </li>
                    <li>
                        <a href="packages.php" class="nav-ribbon-btn<?php echo $active_nav == 'tours' ? ' active-nav-btn' : ''; ?>" title="Explore All Tours">
                            <span class="material-symbols-outlined nav-ribbon-icon">luggage</span>
                            <span class="nav-ribbon-label">Tours</span>
                        </a>
                    </li>
                    <li>
                        <a href="hotels.php" class="nav-ribbon-btn<?php echo $active_nav == 'hotels' ? ' active-nav-btn' : ''; ?>" title="Luxury Accommodations">
                            <span class="material-symbols-outlined nav-ribbon-icon">bed</span>
                            <span class="nav-ribbon-label">Hotels</span>
                        </a>
                    </li>
                    <li>
                        <a href="cabs.php" class="nav-ribbon-btn<?php echo $active_nav == 'cabs' ? ' active-nav-btn' : ''; ?>" title="Private Cab Fleet">
                            <span class="material-symbols-outlined nav-ribbon-icon">local_taxi</span>
                            <span class="nav-ribbon-label">Cabs</span>
                        </a>
                    </li>
                    <li>
                        <a href="packages.php?type=fixed" class="nav-ribbon-btn<?php echo $active_nav == 'fixed' ? ' active-nav-btn' : ''; ?>" title="Guaranteed Group Departures">
                            <span class="material-symbols-outlined nav-ribbon-icon">event_available</span>
                            <span class="nav-ribbon-label">Fixed Departure</span>
                        </a>
                    </li>
                    <li>
                        <a href="events.php" class="nav-ribbon-btn<?php echo $active_nav == 'events' ? ' active-nav-btn' : ''; ?>" title="Special & Festive Events">
                            <span class="material-symbols-outlined nav-ribbon-icon">celebration</span>
                            <span class="nav-ribbon-label">Events</span>
                        </a>
                    </li>
                    <li>
                        <a href="packages.php?filter=offers" class="nav-ribbon-btn offers-ribbon-btn<?php echo $active_nav == 'offers' ? ' active-nav-btn' : ''; ?>" title="Exclusive Offers">
                            <span class="material-symbols-outlined nav-ribbon-icon">local_offer</span>
                            <span class="nav-ribbon-label">Offers</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
