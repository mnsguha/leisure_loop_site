<?php 
    require_once '../config/db.php';
    require_once '../config/recaptcha.php';
    $page_title = "Leisure Loop Trip | The Art of Discovery";
    $use_recaptcha = recaptchaIsConfigured();
    $recaptcha_site_key = recaptchaSiteKey();

    if (!function_exists('cacheHeroVideoLocally')) {
        function cacheHeroVideoLocally(string $url): string
        {
            if (!preg_match('/^https?:\\/\\//i', $url) || !preg_match('/\\.mp4(?:\\?|$)/i', $url)) {
                return $url;
            }

            $cacheDir = __DIR__ . '/assets/img/hero/cache/';
            $publicPath = 'assets/img/hero/cache/' . md5($url) . '.mp4';
            $cacheFile = $cacheDir . md5($url) . '.mp4';

            if (is_file($cacheFile) && filesize($cacheFile) > 0) {
                return $publicPath;
            }

            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0777, true);
            }

            $downloaded = false;

            if (function_exists('curl_init')) {
                $fp = @fopen($cacheFile, 'wb');
                if ($fp) {
                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_FILE => $fp,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_TIMEOUT => 20,
                        CURLOPT_CONNECTTIMEOUT => 10,
                        CURLOPT_FAILONERROR => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_USERAGENT => 'LeisureLoopHero/1.0',
                    ]);
                    $downloaded = curl_exec($ch) !== false;
                    curl_close($ch);
                    fclose($fp);
                }
            }

            if (!$downloaded && ini_get('allow_url_fopen')) {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 20,
                        'follow_location' => 1,
                        'user_agent' => 'LeisureLoopHero/1.0',
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                $remote = @fopen($url, 'rb', false, $context);
                $local = @fopen($cacheFile, 'wb');
                if ($remote && $local) {
                    stream_copy_to_stream($remote, $local);
                    fclose($remote);
                    fclose($local);
                    $downloaded = is_file($cacheFile) && filesize($cacheFile) > 0;
                }
            }

            if (!$downloaded || !is_file($cacheFile) || filesize($cacheFile) === 0) {
                @unlink($cacheFile);
                return $url;
            }

            return $publicPath;
        }
    }

    include '../includes/header.php'; 
?>

<?php if (isset($_GET['lead_sent'])): ?>
<div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: #739d1b; color: white; padding: 16px 28px; border-radius: 10px; font-size: 0.9rem; box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
     Thank you! Our team will contact you shortly.
</div>
<script>setTimeout(() => document.querySelector('[style*="position: fixed"]').remove(), 5000);</script>
<?php endif; ?>

    <!-- Editorial Hero -->
    <header class="hero" id="home">
        <div class="hero-video-container">
            <?php 
                $hero_slides = [];
                if ($pdo) {
                    try {
                        $hero_slides = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
                    } catch (PDOException $e) { $hero_slides = []; }
                }
                
                // Fallback to original settings if no slides are defined in the new table
                if (empty($hero_slides)) {
                    $hero_slides = [[
                        'type' => ($settings['hero_type'] ?? 'video'),
                        'url' => ($settings['hero_url'] ?? 'https://cdn.pixabay.com/video/2025/05/06/277097_large.mp4')
                    ]];
                }
            ?>
            
            <div class="hero-slider-wrapper">
                <?php foreach ($hero_slides as $index => $slide): ?>
                    <?php
                        $slideUrl = $slide['url'];
                        if (($slide['type'] ?? '') === 'video') {
                            $slideUrl = cacheHeroVideoLocally($slideUrl);
                        }
                    ?>
                    <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-type="<?php echo $slide['type']; ?>">
                        <?php if ($slide['type'] === 'video'): ?>
                            <div class="hero-slide-fallback" style="background-image: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000');"></div>
                            <video class="hero-slide-video" <?php echo $index === 0 ? 'autoplay' : ''; ?> muted loop playsinline preload="metadata" poster="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000" style="width: 100%; height: 100%; object-fit: cover;">
                                <source src="<?php echo htmlspecialchars($slideUrl); ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <div class="hero-slide-img" style="background-image: url('<?php echo htmlspecialchars($slideUrl); ?>');"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Slider Controls (Dots) -->
            <?php if (count($hero_slides) > 1): ?>
            <div class="hero-dots">
                <?php foreach ($hero_slides as $index => $slide): ?>
                    <div class="hero-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="hero-content">
            <div class="hero-content-grid">
                <!-- Text Content -->
                <div class="hero-text-side" style="padding-top: 40px;">
                    <span class="tagline hero-reveal-left" style="animation-delay: 0.15s;"><?php echo htmlspecialchars($settings['hero_text_sub'] ?? 'Explore Without Limits'); ?></span>
                    <h1 class="hero-reveal-left" style="animation-delay: 0.3s;"><?php echo $settings['hero_text_main'] ?? 'Explore <em>Without Limits</em> Your <br>Journey Begins Here.'; ?></h1>
                    <p class="hero-reveal-left" style="color: rgba(255,255,255,0.7); max-width: 500px; margin-bottom: 40px; animation-delay: 0.45s;">Bespoke itineraries, luxury escapes, and world-class service curated for the modern explorer.</p>
                    <a href="#experiences" class="btn-premium hero-reveal-left" style="animation-delay: 0.6s;">Explore Collection</a>
                </div>

                <!-- Inquiry Card -->
                <div class="hero-card hero-reveal-right" style="animation-delay: 0.3s;">
                    <span class="hero-card-label">Exclusive Offer</span>
                    <h3>Plan Your Perfect Journey</h3>
                    <p>Share your details and our travel curators will craft a bespoke itinerary for you.</p>
                    <div class="card-divider"></div>
                    
                    <form action="../api/submit-lead.php" method="POST" class="js-lead-form">
                        <input type="hidden" name="enforce_recaptcha" value="1">
                        <div class="field-shell">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg>
                            </span>
                            <input type="text" name="name" placeholder="Your Full Name" required>
                        </div>
                        <div class="field-shell">
                            <span class="field-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.054 15.054 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01Z"/></svg>
                            </span>
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                        <div class="form-row">
                            <div class="field-shell">
                                <span class="field-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M12 21s-6-5.686-6-11a6 6 0 1 1 12 0c0 5.314-6 11-6 11Zm0-8.5a2.5 2.5 0 1 0-2.5-2.5 2.5 2.5 0 0 0 2.5 2.5Z"/></svg>
                                </span>
                                <input type="text" name="destination" placeholder="Destination">
                            </div>
                            <div class="field-shell">
                                <span class="field-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M7 2h2v3H7Zm8 0h2v3h-2ZM4 6h16a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm0 5v9h16v-9Z"/></svg>
                                </span>
                                <input type="text" name="date" placeholder="Travel Date" onfocus="(this.type='date')">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="field-shell field-shell-select">
                                <span class="field-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M12 2a3 3 0 0 1 3 3v4a3 3 0 1 1-6 0V5a3 3 0 0 1 3-3Zm-5 11h10l1.5 8h-3l-.7-4h-5.6l-.7 4h-3Z"/></svg>
                                </span>
                                <select name="adults">
                                    <option value="" disabled selected>Adults</option>
                                    <option value="1">1 Adult</option>
                                    <option value="2">2 Adults</option>
                                    <option value="3">3 Adults</option>
                                    <option value="4">4 Adults</option>
                                    <option value="5">5 Adults</option>
                                    <option value="6">6 Adults</option>
                                    <option value="7">7 Adults</option>
                                    <option value="8">8 Adults</option>
                                    <option value="9">9 Adults</option>
                                    <option value="10">10 Adults</option>
                                    <option value="11">11 Adults</option>
                                    <option value="12">12 Adults</option>
                                    <option value="13">13 Adults</option>
                                    <option value="14">14 Adults</option>
                                    <option value="15+">15+ Adults</option>
                                </select>
                            </div>
                            <div class="field-shell field-shell-select">
                                <span class="field-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M12 3a2.5 2.5 0 1 1-2.5 2.5A2.5 2.5 0 0 1 12 3Zm-4 7h8a2 2 0 0 1 2 2v9h-3v-4h-6v4H6v-9a2 2 0 0 1 2-2Z"/></svg>
                                </span>
                                <select name="children">
                                    <option value="" disabled selected>Children</option>
                                    <option value="1">1 Child</option>
                                    <option value="2">2 Children</option>
                                    <option value="3">3 Children</option>
                                    <option value="4">4 Children</option>
                                    <option value="5">5 Children</option>
                                    <option value="6">6 Children</option>
                                    <option value="7">7 Children</option>
                                    <option value="8">8 Children</option>
                                    <option value="9">9 Children</option>
                                    <option value="10">10 Children</option>
                                    <option value="11">11 Children</option>
                                    <option value="12">12 Children</option>
                                    <option value="13">13 Children</option>
                                    <option value="14">14 Children</option>
                                    <option value="15+">15+ Children</option>
                                </select>
                            </div>
                        </div>
                        <?php if ($use_recaptcha): ?>
                        <div class="recaptcha-shell">
                            <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                        </div>
                        <?php endif; ?>
                        <button type="submit" class="btn-card">SUBMIT ENQUIRY &rarr;</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Strip -->
    <section class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 2V22"/><path d="M2 12H22"/><path d="M12 2C14.5013 4.73835 15.9228 8.29203 15.9228 12C15.9228 15.708 14.5013 19.2616 12 22"/><path d="M12 2C9.49872 4.73835 8.07725 8.29203 8.07725 12C8.07725 15.708 9.49872 19.2616 12 22"/></svg></div>
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Bespoke Journeys</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M6 9H18L19 21H5L6 9Z"/><path d="M6 9C6 9 3 9 3 13C3 17 6 17 6 17"/><path d="M18 9C18 9 21 9 21 13C21 17 18 17 18 17"/><path d="M12 2V6"/></svg></div>
                    <span class="stat-number">12+</span>
                    <span class="stat-label">Years Excellence</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg></div>
                    <span class="stat-number">4.9</span>
                    <span class="stat-label">Client Rating</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 15L17 18V5H7V18L12 15Z"/><path d="M12 2V5"/></svg></div>
                    <span class="stat-number">Elite</span>
                    <span class="stat-label">Award Winner 2024</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61C20.3292 4.09904 19.7228 3.69363 19.0554 3.41708C18.388 3.14053 17.6725 2.99816 16.95 3C15.4812 3.0001 14.0728 3.5828 13.03 4.62L12 5.67L10.97 4.63C9.92723 3.58723 8.51278 3.00008 7.045 3C6.32247 2.99816 5.60703 3.14053 4.93963 3.41708C4.27222 3.69363 3.6658 4.09904 3.155 4.61C1.047 6.718 1.047 10.138 3.155 12.246L12 21.091L20.845 12.246C22.953 10.138 22.953 6.718 20.84 4.61Z"/></svg></div>
                    <span class="stat-number">99%</span>
                    <span class="stat-label">Happy Explorers</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M3 18H21"/><path d="M12 18V4"/><path d="M8 4H16"/><path d="M3 14C3 14 3 6 12 6C21 6 21 14 21 14"/></svg></div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Priority Support</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Most Coveted Journeys -->
    <?php 
        $featured_packages = [];
        if ($pdo) {
            $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 LIMIT 4")->fetchAll();
        }
    ?>
    <section class="packages-section" id="featured">
        <div class="watermark">Most Coveted</div>
        <div class="container">
            <div class="packages-header">
                <div>
                    <span class="section-label-gold">The Curated Collection</span>
                    <h2 class="section-title" style="margin-bottom: 0;">of the <span class="serif" style="color: var(--gold); font-style: italic;">Season.</span></h2>
                </div>
                <div class="nav-arrows-group">
                    <button class="nav-arrow prev-pkg" aria-label="Previous">&larr;</button>
                    <button class="nav-arrow next-pkg" aria-label="Next" style="border-color: var(--gold); color: var(--gold);">&rarr;</button>
                </div>
            </div>
            
            <div class="packages-carousel">
                <?php foreach ($featured_packages as $pkg): ?>
                <div class="package-card">
                    <div class="pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>');">
                        <div class="pkg-badge">
                            <?php 
                            $itinerary = json_decode($pkg['itinerary'], true);
                            if ($itinerary && is_array($itinerary)) {
                                $days = count($itinerary);
                                $nights = max(1, $days - 1);
                                echo $nights . " NIGHTS / " . $days . " DAYS";
                            } else {
                                echo "CUSTOM DURATION";
                            }
                            ?>
                        </div>
                        <div class="pkg-overlay"></div>
                        <div class="pkg-overlay-content">
                            <span class="pkg-dest"><?php echo htmlspecialchars(strtoupper(explode(' ', $pkg['title'])[0])); ?></span>
                            <h3 class="pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                        </div>
                    </div>
                    <div class="pkg-footer">
                        <div class="pkg-price">
                            FROM <b>&#8377;<?php echo number_format($pkg['price']); ?></b>
                        </div>
                        <a href="package-detail.php?slug=<?php echo $pkg['slug']; ?>" class="pkg-btn">&rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Film Strip Roll -->
    <?php 
        $marquee_items = [];
        if ($pdo) {
            try {
                $marquee_items = $pdo->query("SELECT * FROM marquee_items ORDER BY display_order ASC, id ASC LIMIT 15")->fetchAll();
            } catch (PDOException $e) {
                $marquee_items = [
                    ['image_url' => 'https://images.unsplash.com/photo-1581430873933-05b81a8ca93b?q=80&w=600', 'label' => 'Bespoke Journeys'],
                    ['image_url' => 'https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=600', 'label' => 'Luxury Reimagined'],
                    ['image_url' => 'https://images.unsplash.com/photo-1597233539235-56af97458197?q=80&w=600', 'label' => 'Elite Concierge']
                ];
            }
        }
    ?>
    <?php if (!empty($marquee_items)): ?>
    <div class="ticker-section">
        <div class="ticker-wrapper">
            <div class="ticker-content">
                <?php foreach ($marquee_items as $item): ?>
                <div class="ticker-frame">
                    <div class="film-img" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>');"></div>
                    <div class="ticker-item"><?php echo htmlspecialchars($item['label']); ?></div>
                </div>
                <span class="ticker-sep">◆</span>
                <?php endforeach; ?>
            </div>
            <div class="ticker-content">
                <?php foreach ($marquee_items as $item): ?>
                <div class="ticker-frame">
                    <div class="film-img" style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>');"></div>
                    <div class="ticker-item"><?php echo htmlspecialchars($item['label']); ?></div>
                </div>
                <span class="ticker-sep">◆</span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Signature Terrains -->
    <section class="destinations-section" id="destinations">
        <div class="container">
            <div class="destinations-header">
                <div class="header-left">
                    <span class="section-label-gold">Curated Escapes</span>
                    <h2 class="section-title">Signature <span class="serif" style="color: var(--gold); font-style: italic;">Terrains.</span></h2>
                </div>
                <div class="destinations-filter">
                    <button class="filter-btn active" data-filter="domestic">Domestic</button>
                    <button class="filter-btn" data-filter="international">International</button>
                </div>
                <div class="nav-arrows-group">
                    <button class="nav-arrow prev-dest" aria-label="Previous">&larr;</button>
                    <button class="nav-arrow next-dest" aria-label="Next" style="border-color: var(--gold); color: var(--gold);">&rarr;</button>
                </div>
            </div>
            <div class="destinations-carousel-wrapper">
                <div class="destinations-carousel">
                    <!-- Domestic -->
                    <div class="dest-card" data-category="domestic" onclick="window.location.href='destination-details.php?slug=sikkim'">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Sikkim</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>12 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card" data-category="domestic" onclick="window.location.href='destination-details.php?slug=ladakh'">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Ladakh</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>8 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card" data-category="domestic" onclick="window.location.href='destination-details.php?slug=kashmir'">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Kashmir</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>15 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card" data-category="domestic">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Meghalaya</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>6 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- International -->
                    <div class="dest-card hidden" data-category="international">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1578509335520-22f3066d482a?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Bhutan</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>10 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card hidden" data-category="international">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Bali</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>18 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card hidden" data-category="international">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Dubai</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>22 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dest-card hidden" data-category="international">
                        <div class="dest-img" style="background-image: url('https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?q=80&w=800');">
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3>Switzerland</h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span>14 Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="container">
            <span class="section-label">Knowledge Base</span>
            <h2 class="section-title">Common <span style="color: var(--gold); font-style: italic;">Curiosities.</span></h2>
            <div class="faq-grid">
                <div class="faq-col">
                    <div class="faq-item">
                        <div class="faq-question"><h4>What defines a 'Bespoke' Leisure Loop journey?</h4><div class="faq-icon">&#10010;</div></div>
                        <div class="faq-answer"><p>Every journey is crafted from scratch based on your personal preferences, pace, and interests.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="section" id="contact" style="background: linear-gradient(rgba(5,10,20,0.85), rgba(5,10,20,0.85)), url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2000'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 8rem; align-items: center;">
            <div>
                <span class="section-label">Start Your Story</span>
                <h2 class="section-title">Where to <br><span class="serif" style="color: var(--amber);">Next?</span></h2>
            </div>
            <div class="glass-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); padding: 4rem; border-radius: 8px;">
                <form action="../api/submit-lead.php" method="POST" class="js-lead-form">
                    <button type="submit" class="btn-card" style="width: 100%; padding: 1.2rem;">Begin Your Journey &nbsp; &rarr;</button>
                </form>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure first video plays
        const firstVideo = document.querySelector('.hero-slide.active video');
        if (firstVideo) {
            firstVideo.play().catch(e => console.log("Autoplay blocked", e));
        }
    });
</script>
