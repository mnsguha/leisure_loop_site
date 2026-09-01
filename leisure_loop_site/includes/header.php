<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Leisure Loop Trip | Elite Travel Experiences'; ?></title>
    
    <meta name="description" content="<?php echo $meta_desc ?? 'Cinematic travel experiences for the modern explorer. Bespoke itineraries and world-class service by Leisure Loop Trip.'; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords ?? 'Sikkim tours, Luxury travel India, Himalayan escapes, Leisure Loop Trip'; ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">

    <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
</head>
<body>
    <?php
        $site_logo_path = __DIR__ . '/../public/assets/img/leisure.png';
        $site_logo_url = 'assets/img/leisure.png';
        $has_site_logo = file_exists($site_logo_path);

        // Fetch dynamic menu contents from database
        $nav_themes = [];
        $nav_domestic = [];
        $nav_international = [];
        $grouped_packages = [];

        if ($pdo) {
            try {
                if (!isset($settings)) {
                    $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
                }
                
                // Active themes
                $nav_themes = $pdo->query("SELECT name FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                
                // Active Domestic Destinations
                $nav_domestic = $pdo->query("SELECT name, slug FROM destinations WHERE category = 'domestic' AND is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                
                // Active International Destinations
                $nav_international = $pdo->query("SELECT name, slug FROM destinations WHERE category = 'international' AND is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                
                // Active Packages grouped by destination
                $all_pkgs = $pdo->query("SELECT title, slug, destination FROM packages WHERE is_active = 1 ORDER BY title ASC")->fetchAll();
                foreach ($all_pkgs as $nav_pkg) {
                    $destKey = strtolower(trim($nav_pkg['destination']));
                    $grouped_packages[$destKey][] = $nav_pkg;
                }
            } catch (Exception $e) {}
        }
    ?>
    
    <!-- Navigation -->
    <nav class="glass-nav">
        <div class="container nav-content">
            <div class="logo">
                <a href="index.php" style="text-decoration: none; color: inherit;">
                    <?php if ($has_site_logo): ?>
                    <img src="<?php echo $site_logo_url; ?>" alt="Leisure Loop Trip" class="site-logo">
                    <?php else: ?>
                    <span class="logo-text">LEISURE <span style="color: var(--gold);">LOOP</span></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <ul class="nav-links">
                <!-- 1. Home (Crimson glowing outline badge only) -->
                <li class="nav-item-home">
                    <a href="index.php" class="home-link" title="Home">
                        <span class="home-icon-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </span>
                    </a>
                </li>
                
                <!-- 2. Tour Packages -->
                <li>
                    <a href="packages.php">
                        Tour Packages
                    </a>
                </li>
                
                <!-- 3. Domestic Destinations (Mega Menu) -->
                <li class="nav-item-mega">
                    <a href="#" class="nav-link-trigger">
                        Domestic Destinations <span class="chevron-arrow"></span>
                    </a>
                    <div class="mega-menu">
                        <div class="mega-menu-grid">
                            <?php foreach ($nav_domestic as $dest): 
                                $destKey = strtolower(trim($dest['name']));
                                $tours = $grouped_packages[$destKey] ?? [];
                            ?>
                                <div class="mega-column">
                                    <h4 class="mega-title">
                                        <a href="packages.php?destination=<?php echo urlencode($dest['name']); ?>"><?php echo htmlspecialchars($dest['name']); ?></a>
                                    </h4>
                                    <ul class="mega-list">
                                        <?php foreach ($tours as $tour): ?>
                                            <li><a href="package-detail.php?slug=<?php echo htmlspecialchars($tour['slug']); ?>"><?php echo htmlspecialchars($tour['title']); ?></a></li>
                                        <?php endforeach; ?>
                                        <?php if (empty($tours)): ?>
                                            <li class="muted-item">Explore <?php echo htmlspecialchars($dest['name']); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
                
                <!-- 4. International Destinations (Mega Menu) -->
                <li class="nav-item-mega">
                    <a href="#" class="nav-link-trigger">
                        International Destinations <span class="chevron-arrow"></span>
                    </a>
                    <div class="mega-menu">
                        <div class="mega-menu-grid">
                            <?php foreach ($nav_international as $dest): 
                                $destKey = strtolower(trim($dest['name']));
                                $tours = $grouped_packages[$destKey] ?? [];
                            ?>
                                <div class="mega-column">
                                    <h4 class="mega-title">
                                        <a href="packages.php?destination=<?php echo urlencode($dest['name']); ?>"><?php echo htmlspecialchars($dest['name']); ?></a>
                                    </h4>
                                    <ul class="mega-list">
                                        <?php foreach ($tours as $tour): ?>
                                            <li><a href="package-detail.php?slug=<?php echo htmlspecialchars($tour['slug']); ?>"><?php echo htmlspecialchars($tour['title']); ?></a></li>
                                        <?php endforeach; ?>
                                        <?php if (empty($tours)): ?>
                                            <li class="muted-item">Explore <?php echo htmlspecialchars($dest['name']); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($nav_international)): ?>
                                <div class="mega-column" style="grid-column: span 4; text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Bespoke international itineraries launching soon.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            </ul>
            
            <div class="nav-actions">
                <!-- Phone Contact Pill -->
                <a href="tel:<?php echo htmlspecialchars($settings['contact_phone'] ?? '+918918921629'); ?>" class="nav-phone-pill">
                    <span class="phone-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </span>
                    <span class="phone-number"><?php echo htmlspecialchars($settings['contact_phone'] ?? '+91 89189 21629'); ?></span>
                </a>
                
                <!-- Enquiry Now Button -->
                <a href="#inquiry-form" class="btn-enquiry-nav">Enquiry Now</a>
            </div>
        </div>
    </nav>

    <script>
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.glass-nav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
