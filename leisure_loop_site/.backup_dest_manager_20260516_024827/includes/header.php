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
        $site_logo_path = __DIR__ . '/../public/assets/img/logo.png';
        $site_logo_url = 'assets/img/logo.png';
        $has_site_logo = file_exists($site_logo_path);
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
                <li><a href="index.php">Collection</a></li>
                <li><a href="packages.php">Journeys</a></li>
                <li><a href="about.php">Our Story</a></li>
                <li><a href="contact.php" class="btn-premium" style="padding: 12px 25px;">Inquire</a></li>
            </ul>
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
