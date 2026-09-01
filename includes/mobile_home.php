<?php
// Find the first slide URL for the mobile background
$mobile_hero_bg = 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000'; // fallback
if (!empty($hero_slides)) {
    $firstSlide = $hero_slides[0];
    $slideUrl = $firstSlide['url'];
    if (strpos($slideUrl, 'http') !== 0) {
        $slideUrl = 'admin/uploads/' . $slideUrl;
    }
    $mobile_hero_bg = $slideUrl;
}
?>

<!-- Mobile Header -->
<!-- Mobile Header (Flipkart Style) -->
<div class="mobile-app-header bg-[#050a14] relative z-50 shadow-[0_4px_12px_rgba(0,0,0,0.5)] border-b border-white/5 sticky top-0">
    <!-- Subtle radial glow behind header -->
    <div class="absolute top-[-50px] left-1/2 -translate-x-1/2 w-[250px] h-[250px] bg-[#1e3a8a] opacity-20 blur-[80px] rounded-full pointer-events-none"></div>
    
    <!-- 1. Top Logo -->
    <div class="flex px-4 pt-6 pb-2 justify-center relative z-10">
        <a aria-label="Edit" href="index.php" class="flex flex-col items-center justify-center gap-1.5 no-underline">
            <img class="mobile-brand-icon" src="assets/img/mobile_icon.png" alt="Leisure Loop Trip Icon">
            <img class="mobile-brand-text" src="assets/img/mobile_text.png" alt="Leisure Loop Trip Text">
        </a>
    </div>

    <!-- 2. Location Bar -->
    <button type="button" class="mobile-location-trigger px-4 pt-3 pb-1 flex items-center gap-1.5 relative z-10" data-action="open-location">
        <span class="material-symbols-outlined text-white/50 mobile-icon--sm">location_on</span>
        <span class="text-white/50 text-[12px] pointer-events-none" id="currentLocationText">Location not set</span>
        <span class="text-[#c5a059] text-[12px] font-medium ml-0.5 pointer-events-none">Select your location &gt;</span>
    </button>

    <!-- 3. Search Bar -->
    <div class="px-3 pt-1.5 relative z-10">
        <button data-action="open-search" class="w-full bg-white rounded-[10px] py-2 px-3 flex items-center gap-2 shadow-sm border border-transparent">
            <span class="material-symbols-outlined text-[#050a14]/60 mobile-icon--md">search</span>
            <span class="text-[#050a14]/50 font-medium text-[14px] flex-1 text-left tracking-wide">Search for destinations...</span>
            <span class="material-symbols-outlined text-[#050a14]/40 mobile-icon--md">mic</span>
        </button>
    </div>

    <!-- 4. Category Tabs -->
    <div class="mobile-category-tabs px-2 pt-4 flex justify-start sm:justify-between items-end overflow-x-auto relative z-10 gap-3 sm:gap-2">
        <a href="packages.php?filter=offers" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] border-b-[3px] border-[#c5a059] pb-1.5 no-underline px-1">
            <div class="w-9 h-9 rounded bg-[#c5a059]/10 flex items-center justify-center">
               <span class="material-symbols-outlined text-[#c5a059] mobile-icon--lg">local_offer</span>
            </div>
            <span class="text-white font-semibold text-[11px] whitespace-nowrap">Offers</span>
        </a>
        <a href="packages.php" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] pb-1.5 border-b-[3px] border-transparent no-underline px-1">
            <div class="w-9 h-9 rounded flex items-center justify-center">
               <span class="material-symbols-outlined text-white/70 mobile-icon--lg">luggage</span>
            </div>
            <span class="text-white/70 font-medium text-[11px] whitespace-nowrap">Tours</span>
        </a>
        <a href="packages.php?type=fixed" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] pb-1.5 border-b-[3px] border-transparent no-underline px-1">
            <div class="w-9 h-9 rounded flex items-center justify-center">
               <span class="material-symbols-outlined text-white/70 mobile-icon--lg">event_available</span>
            </div>
            <span class="text-white/70 font-medium text-[11px] whitespace-nowrap">Fixed Departure</span>
        </a>
        <a href="hotels.php" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] pb-1.5 border-b-[3px] border-transparent no-underline px-1">
            <div class="w-9 h-9 rounded flex items-center justify-center">
               <span class="material-symbols-outlined text-white/70 mobile-icon--lg">bed</span>
            </div>
            <span class="text-white/70 font-medium text-[11px] whitespace-nowrap">Hotels</span>
        </a>
        <a href="cabs.php" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] pb-1.5 border-b-[3px] border-transparent no-underline px-1">
            <div class="w-9 h-9 rounded flex items-center justify-center">
               <span class="material-symbols-outlined text-white/70 mobile-icon--lg">local_taxi</span>
            </div>
            <span class="text-white/70 font-medium text-[11px] whitespace-nowrap">Cabs</span>
        </a>
        <a href="packages.php?theme=events" class="flex shrink-0 flex-col items-center gap-1 min-w-[60px] pb-1.5 border-b-[3px] border-transparent no-underline px-1">
            <div class="w-9 h-9 rounded flex items-center justify-center">
               <span class="material-symbols-outlined text-white/70 mobile-icon--lg">celebration</span>
            </div>
            <span class="text-white/70 font-medium text-[11px] whitespace-nowrap">Events</span>
        </a>
    </div>
</div>

<main class="bg-[#050a14] relative z-10">
<!-- 1. Hero Section -->
<section class="px-5 pt-4 pb-2">

    <!-- Carousel -->
    <div class="swiper hero-coverflow pb-6">
        <div class="swiper-wrapper">
            <?php 
            $carousel_packages = [];
            if (isset($pdo)) {
                $carousel_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
            }
            foreach ($carousel_packages as $pkg):
                $img = !empty($pkg['image_url']) ? $pkg['image_url'] : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800';
            ?>
            <!-- Using aspect-square to make the images perfectly square -->
            <div class="swiper-slide w-[70%] max-w-[280px] rounded-[32px] overflow-hidden shadow-2xl relative aspect-square">
                <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover" alt="Destination">
                <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>
                <!-- Link Overlay -->
                <a href="package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" class="absolute inset-0 z-10"></a>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>
    

</section>
</div><!-- End gradient wrapper -->







<!-- Most Coveted Journeys -->
    <?php 
        $featured_packages = [];
        if (isset($pdo)) {
            $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 0 ORDER BY id DESC")->fetchAll();
        }
    ?>
    <section id="featured" style="padding: 24px 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
        
        <div style="position: relative; z-index: 2;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; display: block; margin-bottom: 4px;">The Curated Collection</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.15;">of the <span style="color: var(--gold); font-style: italic;">Season.</span></h2>
                </div>
                <a href="packages.php?type=domestic" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; padding-bottom: 4px;">See All <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
            </div>
            
            
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($featured_packages as $pkg): ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug']) ?>" class="app-pkg-card">
                    <div class="app-pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>');">
                        <div class="app-pkg-badge">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo $nights . "N / " . $days . "D";
                                } else {
                                    echo "CUSTOM";
                                }
                            }
                            ?>
                        </div>
                        <div class="app-pkg-content">
                            <span class="app-pkg-dest"><?php echo htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
                            <h3 class="app-pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            <div class="app-pkg-price">FROM &#8377;<?php echo number_format($pkg['price']); ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<!-- Signature Terrains -->
    <section class="destinations-section" id="mobile-destinations" style="padding: 15px; background-color: #000;">
        <?php
            $destinations_list = [];
            if (isset($pdo)) {
                $destinations_list = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE d.is_active = 1 ORDER BY d.display_order ASC, d.created_at DESC")->fetchAll();
            }
            $domestic = array_filter($destinations_list, function($d) { return strtolower($d['category']) === 'domestic'; });
            $international = array_filter($destinations_list, function($d) { return strtolower($d['category']) === 'international'; });
        ?>
        
        <div style="background: linear-gradient(145deg, #1f1b13, #0a0805); border: 1px solid rgba(197,160,89,0.3); border-radius: 16px; padding: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Curated Escapes</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.1;">Signature <span style="color: var(--gold); font-style: italic;">Terrains</span></h2>
                </div>
                <div style="background: rgba(197,160,89,0.1); padding: 4px 8px; border-radius: 8px; border: 1px solid rgba(197,160,89,0.2);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
            </div>
            
            <!-- Domestic -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 0.85rem; color: #fff; margin: 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <div style="width: 4px; height: 12px; background: var(--gold); border-radius: 4px;"></div> Domestic
                </h3>
            </div>
            <div class="dest-mini-carousel" style="display: flex; overflow-x: auto; gap: 12px; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; margin-bottom: 24px; padding-bottom: 4px;">
                <?php foreach ($domestic as $dest): ?>
                    <a href="destination-details.php?slug=<?= urlencode($dest['slug']) ?>" class="dest-mini-card">
                        <div style="height: 130px; background-image: url('<?php echo htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>'); background-size: cover; background-position: center; transition: transform 0.3s ease;"></div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 12px 12px 12px; background: linear-gradient(transparent, rgba(0,0,0,0.95));">
                            <h4 style="color: #fff; font-size: 0.85rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: var(--font-serif); text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?php echo htmlspecialchars($dest['name']); ?></h4>
                            <span style="color: var(--gold); font-size: 0.6rem; font-weight: 500;"><?php echo (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- International -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 0.85rem; color: #fff; margin: 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <div style="width: 4px; height: 12px; background: var(--gold); border-radius: 4px;"></div> International
                </h3>
            </div>
            <div class="dest-mini-carousel" style="display: flex; overflow-x: auto; gap: 12px; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none;">
                <?php foreach ($international as $dest): ?>
                    <a href="destination-details.php?slug=<?= urlencode($dest['slug']) ?>" class="dest-mini-card">
                        <div style="height: 130px; background-image: url('<?php echo htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>'); background-size: cover; background-position: center; transition: transform 0.3s ease;"></div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 12px 12px 12px; background: linear-gradient(transparent, rgba(0,0,0,0.95));">
                            <h4 style="color: #fff; font-size: 0.85rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: var(--font-serif); text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?php echo htmlspecialchars($dest['name']); ?></h4>
                            <span style="color: var(--gold); font-size: 0.6rem; font-weight: 500;"><?php echo (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        
        
        
    </section>

<!-- Global Escapes -->
    <?php 
        $global_packages = [];
        if (isset($pdo)) {
            $global_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 1 ORDER BY id DESC")->fetchAll();
        }
    ?>
    <section id="global-escapes" style="padding: 24px 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
        
        <div style="position: relative; z-index: 2;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; display: block; margin-bottom: 4px;">World Collection</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.15;">Global <span style="color: var(--gold); font-style: italic;">Escapes.</span></h2>
                </div>
                <a href="packages.php?type=international" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; padding-bottom: 4px;">See All <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
            </div>
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($global_packages as $pkg): ?>
                <a href="package-detail.php?slug=<?= urlencode($pkg['slug']) ?>" class="app-pkg-card">
                    <div class="app-pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>');">
                        <div class="app-pkg-badge">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo $nights . "N / " . $days . "D";
                                } else {
                                    echo "CUSTOM";
                                }
                            }
                            ?>
                        </div>
                        <div class="app-pkg-content">
                            <span class="app-pkg-dest"><?php echo htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
                            <h3 class="app-pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            <div class="app-pkg-price">FROM &#8377;<?php echo number_format($pkg['price']); ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<!-- Auto Scrolling Promotional Banners -->
<?php
    $promos = [];
    if (isset($pdo)) {
        try {
            $promos = $pdo->query("SELECT * FROM advertisements WHERE page_type IN ('home', 'home_middle') AND is_active = 1")->fetchAll();
        } catch (Exception $e) {}
    }
?>
<?php if (!empty($promos)): ?>
<section class="promo-banner-section" style="padding: 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
    <div class="promo-carousel-wrapper" style="position: relative;">
        <div id="promoCarousel" class="promo-carousel" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; gap: 12px;">
            <?php foreach ($promos as $index => $promo): 
                $bg_img = !empty($promo['image_url']) ? htmlspecialchars($promo['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
            ?>
            <div class="promo-card" style="flex: 0 0 100%; scroll-snap-align: center; position: relative; border-radius: 16px; overflow: hidden; min-height: 160px; display: flex; align-items: center; box-shadow: 0 8px 20px rgba(0,0,0,0.5);">
                <div style="position: absolute; inset: 0; background: url('<?php echo $bg_img; ?>') no-repeat center center; background-size: cover; z-index: 1;"></div>
                <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(5,10,20,0.95) 0%, rgba(5,10,20,0.6) 70%, transparent 100%); z-index: 2;"></div>
                <div style="position: relative; z-index: 3; padding: 20px; width: 85%; display: flex; flex-direction: column; align-items: flex-start; gap: 8px;">
                    <div style="background: rgba(255,255,255,0.95); padding: 4px 12px; border-radius: 8px; margin-bottom: 4px; display: inline-block; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        <img src="assets/img/leisure.png" alt="Leisure Loop Trip" style="max-height: 24px; display: block;" onerror="this.outerHTML='<span style=\'font-size: 0.75rem; font-weight: 800; color: #000; letter-spacing: 0.1em; text-transform: uppercase;\'>LEISURE LOOP TRIP</span>'">
                    </div>
                    <h3 style="font-size: 1.1rem; color: #fff; font-family: var(--font-serif); line-height: 1.25; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.8); background: linear-gradient(90deg, #fff, var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo htmlspecialchars($promo['title']); ?>
                    </h3>
                    <a href="contact.php" style="padding: 8px 16px; font-size: 0.7rem; font-weight: 700; border-radius: 50px; background: linear-gradient(135deg, var(--gold), #f0d59e); color: #000; text-decoration: none; margin-top: 4px; box-shadow: 0 4px 10px rgba(197,160,89,0.3);">
                        <?php echo htmlspecialchars($promo['btn_text'] ?: 'Book Now'); ?> &rarr;
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination Dots -->
        <div class="promo-dots" style="display: flex; justify-content: center; gap: 6px; margin-top: 12px;">
            <?php foreach ($promos as $index => $promo): ?>
            <div class="promo-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" style="width: <?php echo $index === 0 ? '16px' : '6px'; ?>; height: 6px; border-radius: 6px; background: <?php echo $index === 0 ? 'var(--gold)' : 'rgba(255,255,255,0.2)'; ?>; transition: all 0.3s ease;"></div>
            <?php endforeach; ?>
        </div>
    </div>
    
    
    
</section>
<?php endif; ?>


<!-- ✦ Curated Themes Section (Mobile) ✦ -->
<?php
$theme_counts_mobile = [];
$theme_meta_mobile = [];
$default_bg_mobile = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
$default_icon_mobile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';

if (isset($pdo)) {
    try {
        $all_pkgs_mobile = $pdo->query("SELECT tour_type FROM packages WHERE is_active = 1")->fetchAll();
        foreach ($all_pkgs_mobile as $row) {
            if (!empty($row['tour_type'])) {
                $tags = array_map('trim', explode(',', $row['tour_type']));
                foreach ($tags as $tag) {
                    $normalized = ucwords(strtolower($tag));
                    $normalized = preg_replace('/\b(tours|tour)\b/i', '', $normalized);
                    $normalized = trim($normalized);
                    if ($normalized !== '') {
                        if (!isset($theme_counts_mobile[$normalized])) $theme_counts_mobile[$normalized] = 0;
                        $theme_counts_mobile[$normalized]++;
                    }
                }
            }
        }
        
        $db_categories_mobile = $pdo->query("SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
        foreach ($db_categories_mobile as $cat) {
            $theme_meta_mobile[$cat['name']] = [
                'bg' => !empty($cat['image_url']) ? $cat['image_url'] : $default_bg_mobile,
                'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon_mobile,
                'tagline' => $cat['tagline']
            ];
        }

        $ordered_theme_counts_mobile = [];
        foreach ($db_categories_mobile as $cat) {
            if (isset($theme_counts_mobile[$cat['name']])) {
                $ordered_theme_counts_mobile[$cat['name']] = $theme_counts_mobile[$cat['name']];
                unset($theme_counts_mobile[$cat['name']]);
            }
        }
        foreach ($theme_counts_mobile as $name => $count) {
            $ordered_theme_counts_mobile[$name] = $theme_counts_mobile[$name];
        }
        $theme_counts_mobile = $ordered_theme_counts_mobile;

    } catch (Exception $e) {}
}

if (!empty($theme_counts_mobile)):
?>
<section id="mobile-curated-themes" class="mobile-theme-section">
    
    <!-- Header -->
    <div class="mobile-theme-header">
        <div>
            <span class="mobile-theme-kicker">CURATED THEMES</span>
            <h2 class="mobile-theme-title">Bespoke Travel <span>Experiences.</span></h2>
        </div>
    </div>
    <!-- Pill Menu (Horizontal Scroll) -->
    <div class="mobile-theme-pills-wrap">
        <div class="mobile-theme-pills">
            <?php foreach ($theme_counts_mobile as $name => $count): 
                $meta = isset($theme_meta_mobile[$name]) ? $theme_meta_mobile[$name] : ['icon' => $default_icon_mobile];
            ?>
            <a href="packages.php?theme=<?php echo urlencode($name); ?>" class="mobile-theme-pill">
                <span class="mobile-theme-pill-icon">
                    <?php echo $meta['icon']; ?>
                </span>
                <span class="mobile-theme-pill-name"><?php echo htmlspecialchars($name); ?></span>
                <span class="mobile-theme-pill-count">(<?php echo $count; ?>)</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Theme Circular Cards Grid -->
    <div class="mobile-theme-grid">
        <?php foreach ($theme_counts_mobile as $name => $count): 
            $meta = isset($theme_meta_mobile[$name]) ? $theme_meta_mobile[$name] : ['bg' => $default_bg_mobile, 'icon' => $default_icon_mobile];
        ?>
        <a href="packages.php?theme=<?php echo urlencode($name); ?>" class="mobile-theme-card">
            <!-- Circular Image -->
            <div class="mobile-theme-card-image">
                <div class="mobile-theme-card-image-fill" style="background-image: url('<?php echo htmlspecialchars($meta['bg']); ?>');"></div>
            </div>
            <!-- Text -->
            <span class="mobile-theme-card-name"><?php echo htmlspecialchars($name); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>



<!-- ✦ Mobile Genuine Moments Gallery ✦ -->
<?php
    $gallery_images_mobile = [];
    if (isset($pdo)) {
        try {
            $gallery_images_mobile = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY display_order ASC LIMIT 16")->fetchAll();
        } catch (Exception $e) {}
    }
    
    $default_gallery_mobile = [
        'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800',
        'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800',
        'https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=800',
        'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800',
        'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?q=80&w=800',
        'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=800'
    ];
    
    $top_track_mobile = [];
    $bottom_track_mobile = [];

    if (count($gallery_images_mobile) >= 4) {
        $half = ceil(count($gallery_images_mobile) / 2);
        $top_track_mobile = array_slice($gallery_images_mobile, 0, $half);
        $bottom_track_mobile = array_slice($gallery_images_mobile, $half);
    } else {
        foreach ($default_gallery_mobile as $k => $url) {
            if ($k < 3) $top_track_mobile[] = ['image_url' => $url];
            else $bottom_track_mobile[] = ['image_url' => $url];
        }
    }
?>



<section class="mobile-gallery-section">
    <!-- Header -->
    <div style="padding: 0 16px; margin-bottom: 24px;">
        <h2 style="font-size: 1.6rem; color: #fff; margin-bottom: 6px; font-family: var(--font-serif); line-height: 1.15;">
            Genuine Moments,<br>
            <span style="color: var(--gold); font-style: italic;">Unforgettable Journeys.</span>
        </h2>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; line-height: 1.5; margin: 0;">
            Glimpse into the extraordinary adventures of our travelers. Authentic memories crafted through bespoke itineraries.
        </p>
    </div>

    <!-- Gallery Marquees -->
    <!-- Top Track (Moves Left) -->
    <div class="mobile-gallery-marquee-wrapper">
        <div class="mobile-gallery-marquee m-track-left">
            <div class="mobile-gallery-content">
                <?php foreach($top_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
            <div class="mobile-gallery-content" aria-hidden="true">
                <?php foreach($top_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Bottom Track (Moves Right) -->
    <div class="mobile-gallery-marquee-wrapper">
        <div class="mobile-gallery-marquee m-track-right">
            <div class="mobile-gallery-content">
                <?php foreach($bottom_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
            <div class="mobile-gallery-content" aria-hidden="true">
                <?php foreach($bottom_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div style="padding: 0 16px; margin-top: 24px; text-align: center;">
        <a href="gallery.php" style="display: inline-block; padding: 12px 28px; background: rgba(255,255,255,0.05); border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 30px; color: var(--gold); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; backdrop-filter: blur(8px);">Explore Gallery</a>
    </div>
</section>


<!-- ✦ Fixed Departures Section (Mobile) ✦ -->
<?php if (!empty($fixed_departures)): ?>
<section id="mobile-fixed-departures" style="padding: 16px;">
    <div style="background: rgba(11, 124, 74, 0.2); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(11, 124, 74, 0.4); border-radius: 12px; padding: 12px; position: relative; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 1.1rem; font-weight: 600; color: #ffffff; margin: 0;">Upcoming Fixed Departures</h2>
            <a aria-label="Link" href="javascript:void(0)" data-action="scroll-carousel" style="width: 24px; height: 24px; background: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#050a14" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        </div>

        <!-- Fixed Departures Carousel -->
        <div id="fd-mobile-carousel" style="display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 4px; scroll-snap-type: x mandatory;">
            <?php foreach ($fixed_departures as $fd): 
                $img = preg_match('/^https?:\/\//i', $fd['image_url']) ? $fd['image_url'] : ltrim($fd['image_url'], '/');
                $seats_left = $fd['available_seats'];
                $status = $fd['status'];
                $status_bg = 'rgba(245, 158, 11, 0.9)';
                if ($status == 'Sold Out') $status_bg = 'rgba(239, 68, 68, 0.9)';
                $date_str = date('M d', strtotime($fd['start_date'])) . ' - ' . date('M d', strtotime($fd['end_date']));
            ?>
            <div package-detail.php?slug=<?php echo $fd['package_slug']; ?>&fd=<?php echo $fd['id']; ?>'" style="scroll-snap-align: start; flex: 0 0 130px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; padding: 6px; display: flex; flex-direction: column; cursor: pointer; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <!-- Image Container -->
                <div style="position: relative; height: 130px; border-radius: 4px; overflow: hidden; background: rgba(255,255,255,0.05);">
                    <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($img); ?>') no-repeat center center; background-size: cover;"></div>
                    <!-- Small Badge overlay -->
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; background: <?php echo $status_bg; ?>; color: #fff; font-size: 0.55rem; font-weight: 700; text-align: center; padding: 3px 0; text-transform: uppercase;">
                        <?php echo htmlspecialchars($status); ?>
                    </div>
                </div>
                <!-- Content Container -->
                <div style="padding-top: 8px; text-align: center; display: flex; flex-direction: column; gap: 2px;">
                    <h3 style="font-family: 'Inter', sans-serif; font-size: 0.8rem; font-weight: 600; color: #ffffff; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-shadow: 0 1px 2px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($fd['package_title']); ?></h3>
                    <div style="font-size: 0.65rem; color: #e2e8f0; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.5);"><?php echo $date_str; ?></div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: #ffffff; margin-top: 2px; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                        <span style="font-weight: 400; font-size: 0.6rem; color: #cbd5e1;">From</span> &#8377;<?php echo number_format($fd['price']); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. Hotel Partners -->
<section class="py-16 bg-surface border-t border-outline-variant/30 overflow-hidden text-white">
    <div class="px-4 mb-8 text-center">
        <h3 class="font-headline-md text-xl text-white-variant uppercase tracking-wider text-sm">Our Luxury Partners</h3>
    </div>
    <div class="flex gap-8 px-4 items-center w-max animate-[marquee_20s_linear_infinite]" style="animation: marquee 20s linear infinite;">
        <?php 
        if (!empty($hotel_partners)):
            // Duplicate the array to ensure seamless looping on mobile
            $scroll_partners = array_merge($hotel_partners, $hotel_partners, $hotel_partners);
            foreach ($scroll_partners as $partner):
                if (!empty($partner['logo_url'])):
                    $img_src = $partner['logo_url'];
                    if (!str_starts_with($img_src, 'http')) {
                        if (!str_contains($img_src, '/')) {
                            $img_src = 'assets/img/partners/' . $img_src;
                        }
                    }
        ?>
        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="h-12 w-auto object-contain flex-shrink-0 opacity-60 grayscale">
        <?php 
                endif;
            endforeach;
        endif; 
        ?>
    </div>
    
</section>


<!-- ✦ Mobile Journal & Insights Section ✦ -->
<?php
    $recent_blogs_mobile = [];
    if (isset($pdo)) {
        try {
            $recent_blogs_mobile = $pdo->query("SELECT * FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
        } catch (Exception $e) {}
    }
?>
<?php if (!empty($recent_blogs_mobile)): ?>
<section class="mobile-journal-section" style="padding: 16px;">
    <div style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 12px; position: relative; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 1.1rem; font-weight: 600; color: #ffffff; margin: 0;">Journal & Insights</h2>
            <a aria-label="Link" href="blog.php" style="width: 24px; height: 24px; background: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#050a14" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        </div>

        <!-- Cards Carousel -->
        <div style="display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 4px; scroll-snap-type: x mandatory;">
            <?php foreach ($recent_blogs_mobile as $post): 
                $post_img = !empty($post['image_url']) ? $post['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
                $post_img = strpos($post_img, 'http') === 0 ? $post_img : $post_img;
            ?>
            <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" style="scroll-snap-align: start; flex: 0 0 140px; background: rgba(255, 255, 255, 0.08); border-radius: 8px; padding: 6px; display: flex; flex-direction: column; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                <div style="position: relative; height: 90px; border-radius: 4px; overflow: hidden;">
                    <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($post_img); ?>') no-repeat center center; background-size: cover;"></div>
                </div>
                <div style="padding-top: 8px;">
                    <div style="color: var(--gold); font-size: 0.55rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></div>
                    <h3 style="font-family: 'Inter', sans-serif; font-size: 0.75rem; color: #ffffff; margin: 0; font-weight: 600; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($post['title']); ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Stats Strip -->
<section class="stats-strip py-4 pl-4 bg-[#0a0a0a] border-y border-outline-variant/30 text-white overflow-hidden">
    
    <div class="stats-carousel flex overflow-x-auto gap-4 text-center pr-4" style="scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 2px;">
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 2V22"/><path d="M2 12H22"/><path d="M12 2C14.5013 4.73835 15.9228 8.29203 15.9228 12C15.9228 15.708 14.5013 19.2616 12 22"/><path d="M12 2C9.49872 4.73835 8.07725 8.29203 8.07725 12C8.07725 15.708 9.49872 19.2616 12 22"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">500+</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Bespoke Journeys</span>
        </div>
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9H18L19 21H5L6 9Z"/><path d="M6 9C6 9 3 9 3 13C3 17 6 17 6 17"/><path d="M18 9C18 9 21 9 21 13C21 17 18 17 18 17"/><path d="M12 2V6"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">12+</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Years Excellence</span>
        </div>
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">4.9</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Client Rating</span>
        </div>
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15L17 18V5H7V18L12 15Z"/><path d="M12 2V5"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">Elite</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Award Winner 2024</span>
        </div>
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20.84 4.61C20.3292 4.09904 19.7228 3.69363 19.0554 3.41708C18.388 3.14053 17.6725 2.99816 16.95 3C15.4812 3.0001 14.0728 3.5828 13.03 4.62L12 5.67L10.97 4.63C9.92723 3.58723 8.51278 3.00008 7.045 3C6.32247 2.99816 5.60703 3.14053 4.93963 3.41708C4.27222 3.69363 3.6658 4.09904 3.155 4.61C1.047 6.718 1.047 10.138 3.155 12.246L12 21.091L20.845 12.246C22.953 10.138 22.953 6.718 20.84 4.61Z"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">99%</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Happy Explorers</span>
        </div>
        <div class="flex flex-col items-center shrink-0" style="min-width: 90px; scroll-snap-align: start;">
            <div class="text-azure-deep mb-1 opacity-80"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 18H21"/><path d="M12 18V4"/><path d="M8 4H16"/><path d="M3 14C3 14 3 6 12 6C21 6 21 14 21 14"/></svg></div>
            <span class="font-serif text-lg font-bold mb-0.5">24/7</span>
            <span class="text-[8px] uppercase tracking-wider text-on-surface-variant font-semibold">Priority Support</span>
        </div>
    </div>
</section>

<?php include __DIR__ . '/mobile_bottom_nav.php'; ?>

<!-- Search Bottom Sheet Modal -->


<div class="search-modal-overlay" id="searchModalOverlay" data-action="close-search" role="button" aria-label="Close"></div>
<div class="search-modal-content pb-6" id="searchModal" aria-hidden="true">
    <div class="flex justify-between items-center p-6 border-b border-white/5">
        <div>
            <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold block mb-1">BESPOKE TRAVEL</span>
            <h2 class="font-serif italic text-2xl text-white m-0">Plan Your Journey</h2>
        </div>
        <button aria-label="Close" data-action="close-search" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white border-none">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto">
        <form action="/api/v1/leads" method="POST" class="space-y-4">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <input type="hidden" name="source" value="mobile_home_search">
            
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                
<label for="input_cc11b4fa" class="sr-only">Where do you want to go?</label>
<input id="input_cc11b4fa" class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Where do you want to go?" type="text" name="destination" required/>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                
<label for="input_b6f5af3f" class="sr-only">Travel Date</label>
<input id="input_b6f5af3f" class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Travel Date" type="text" data-focus="date" name="date" required/>
            </div>
            
            <button aria-label="Search trips" type="submit" class="mobile-search-submit w-full text-black py-4 rounded-xl font-bold text-[16px] shadow-[0_4px_15px_rgba(197,160,89,0.3)] active:scale-[0.98] transition-transform flex justify-center items-center gap-2 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Search Trips
            </button>
        </form>
    </div>
</div>

<!-- Location Bottom Sheet Modal -->
<div class="search-modal-overlay" id="locationModalOverlay" data-action="close-location" role="button" aria-label="Close"></div>
<div class="search-modal-content pb-6" id="locationModal" aria-hidden="true">
    <div class="flex justify-between items-center p-6 border-b border-white/5">
        <div>
            <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold block mb-1">CURRENT CITY</span>
            <h2 class="font-serif italic text-2xl text-white m-0">Select Location</h2>
        </div>
        <button data-action="close-location" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white border-none" aria-label="Close location picker">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto">
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-[#c5a059] mobile-icon--search">search</span>
            </div>
            
<label for="citySearchInput" class="sr-only">Search for your city...</label>
<input class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Search for your city..." type="text" id="citySearchInput">
        </div>
        
        <!-- Use my current location -->
        <button data-action="use-location" id="currentLocationBtn" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-3 mb-6 hover:bg-white/10 active:scale-[0.98] transition-all text-left">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-400 mobile-icon--location">my_location</span>
            </div>
            <div class="flex-1">
                <div class="text-blue-400 font-medium text-[15px] mb-0.5" id="currentLocationBtnText">Use my current location</div>
                <div class="text-white/40 text-[12px]">Allow access to location</div>
            </div>
        </button>

        <div class="space-y-3" id="cityList">
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" data-action="select-city" data-city="New Delhi">
                <span class="material-symbols-outlined text-white/50 mobile-icon--md">location_city</span>
                <span class="text-white text-[15px]">New Delhi, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" data-action="select-city" data-city="Mumbai">
                <span class="material-symbols-outlined text-white/50 mobile-icon--md">location_city</span>
                <span class="text-white text-[15px]">Mumbai, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" data-action="select-city" data-city="Bangalore">
                <span class="material-symbols-outlined text-white/50 mobile-icon--md">location_city</span>
                <span class="text-white text-[15px]">Bangalore, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" data-action="select-city" data-city="Kolkata">
                <span class="material-symbols-outlined text-white/50 mobile-icon--md">location_city</span>
                <span class="text-white text-[15px]">Kolkata, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" data-action="select-city" data-city="Chennai">
                <span class="material-symbols-outlined text-white/50 mobile-icon--md">location_city</span>
                <span class="text-white text-[15px]">Chennai, India</span>
            </div>
        </div>
    </div>
</div>



</main>
