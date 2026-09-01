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
            <div class="hero-centered-content">
                <!-- Text Content -->
                <div class="hero-text-center">
                    <span class="tagline hero-reveal-up" style="animation-delay: 0.15s; margin-bottom: 15px; display: inline-block;"><?php echo htmlspecialchars($settings['hero_text_sub'] ?? 'Explore Without Limits'); ?></span>
                    <h1 class="hero-reveal-up" style="animation-delay: 0.3s; margin-bottom: 20px;"><?php echo $settings['hero_text_main'] ?? 'Explore <em>Without Limits</em> Your <br>Journey Begins Here.'; ?></h1>
                    <p class="hero-reveal-up" style="color: rgba(255,255,255,0.7); max-width: 500px; margin: 0 auto 50px auto; animation-delay: 0.45s;">Bespoke itineraries, luxury escapes, and world-class service curated for the modern explorer.</p>
                </div>

                <!-- Hero Inline 2-Step Form -->
                <div class="hero-search-wrapper hero-reveal-up" style="animation-delay: 0.6s;">
                    <div class="search-header" style="text-align: center; margin-bottom: 20px;">
                        <span class="hero-card-label" style="margin: 0 auto 10px auto; display: inline-block;">Exclusive Offer</span>
                        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #fff; margin-bottom: 5px;">Plan Your Perfect Journey</h3>
                        <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; margin-bottom: 0;">Share your details and our travel curators will craft a bespoke itinerary for you.</p>
                    </div>

                    <div class="hero-inline-form">

                        <!-- STEP 1: Search bar row -->
                        <div class="hero-form-row--bar" id="heroStep1Row">
                            <div class="search-field">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 21s-6-5.686-6-11a6 6 0 1 1 12 0c0 5.314-6 11-6 11Zm0-8.5a2.5 2.5 0 1 0-2.5-2.5 2.5 2.5 0 0 0 2.5 2.5Z"/></svg></span>
                                <input type="text" id="heroDestInput" placeholder="Destination">
                            </div>
                            <div class="search-field">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M7 2h2v3H7Zm8 0h2v3h-2ZM4 6h16a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm0 5v9h16v-9Z"/></svg></span>
                                <input type="text" id="heroDateInput" placeholder="Travel Date" onfocus="(this.type='date')">
                            </div>
                            <div class="search-field custom-select-wrapper" id="travelerDropdownTrigger">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                                <input type="text" id="travelerInputDisplay" placeholder="2 Adults" readonly style="cursor: pointer;">
                                <div class="traveler-popover" id="travelerPopover">
                                    <div class="popover-row">
                                        <div class="popover-label"><strong>Adults</strong><span>(Above 12 yrs)</span></div>
                                        <div class="popover-controls">
                                            <button type="button" class="btn-qty btn-minus" data-target="adultsQty">-</button>
                                            <input type="text" id="adultsQty" value="2" readonly>
                                            <button type="button" class="btn-qty btn-plus" data-target="adultsQty">+</button>
                                        </div>
                                    </div>
                                    <div class="popover-row">
                                        <div class="popover-label"><strong>Children</strong><span>(Age 6-12 yrs)</span></div>
                                        <div class="popover-controls">
                                            <button type="button" class="btn-qty btn-minus" data-target="childrenQty">-</button>
                                            <input type="text" id="childrenQty" value="0" readonly>
                                            <button type="button" class="btn-qty btn-plus" data-target="childrenQty">+</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-done-popover" id="btnDoneTravelers">APPLY</button>
                                </div>
                            </div>
                            <button type="button" class="btn-search-submit" id="btnStep1Next">SUBMIT</button>
                        </div>

                        <!-- STEP 2: Contact fields (hidden until Step 1 submitted) -->
                        <div class="hero-step2-reveal" id="heroStep2" style="display:none;">
                            <form id="heroLeadForm" action="api-submit-lead.php" method="POST" class="js-lead-form">
                                <input type="hidden" name="destination" id="hiddenDest">
                                <input type="hidden" name="date" id="hiddenDate">
                                <input type="hidden" name="adults" id="hiddenAdults" value="2">
                                <input type="hidden" name="children" id="hiddenChildren" value="0">
                                <input type="hidden" name="enforce_recaptcha" value="1">

                                <!-- Name | Phone | Email row -->
                                <div class="hero-form-row--bar hero-step2-bar">
                                    <div class="search-field">
                                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                                        <input type="text" name="name" placeholder="Guest Name" required>
                                    </div>
                                    <div class="search-field">
                                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.054 15.054 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01Z"/></svg></span>
                                        <input type="tel" name="phone" placeholder="Phone Number" required>
                                    </div>
                                    <div class="search-field" style="border-right: none;">
                                         <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
                                         <input type="email" name="email" placeholder="Email Address" required>
                                     </div>
                                </div>

                                <!-- reCAPTCHA + Submit -->
                                <div class="hero-form-row--submit">
                                    <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
                                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                                    <?php else: ?>
                                    <div></div>
                                    <?php endif; ?>
                                    <button type="submit" class="btn-search-submit">COMPLETE ENQUIRY &rarr;</button>
                                </div>
                            </form>
                        </div>

                    </div><!-- /.hero-inline-form -->
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
    </section>

    <!-- The Art of Discovery (How We Work) -->
    <section class="how-we-work-section" style="padding: 6rem 0; background-color: var(--obsidian); border-top: 1px solid rgba(197, 160, 89, 0.1);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <span class="section-label-gold" style="letter-spacing: 0.2em; text-transform: uppercase; font-size: 0.8rem; font-weight: 600;">The Process</span>
                <h2 class="section-title" style="margin-top: 1rem;">The Art of <span style="color: var(--gold); font-style: italic; font-family: 'Playfair Display', serif;">Discovery.</span></h2>
                <p style="color: var(--text-muted); max-width: 600px; margin: 1rem auto 0; font-size: 1.1rem;">Curating your journey is the prelude to the adventure.</p>
            </div>
            
            <div class="process-grid">
                <?php
                $process_steps = [];
                if ($pdo) {
                    try {
                        $res = $pdo->query("SELECT * FROM process_steps ORDER BY step_number ASC")->fetchAll();
                        foreach ($res as $row) {
                            $process_steps[$row['step_number']] = $row;
                        }
                    } catch (Exception $e) {}
                }

                // Default data fallbacks if DB is empty or fails
                $default_steps = [
                    1 => [
                        'title' => 'The Vision',
                        'description' => 'Share your destination, dates, and deepest travel desires. Our concierge listens intently to every nuance, ensuring no detail is overlooked.',
                        'image_url' => 'https://images.unsplash.com/photo-1572375992501-4b0892d50c69?q=80&w=800',
                        'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
                    ],
                    2 => [
                        'title' => 'The Curation',
                        'description' => 'Within 24 hours, our artisans craft a personalized itinerary. We handpick sanctuaries, orchestrate exclusive experiences, and ensure seamless transfers.',
                        'image_url' => 'https://images.unsplash.com/photo-1499678329028-101435549a4e?q=80&w=800',
                        'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
                    ],
                    3 => [
                        'title' => 'The Experience',
                        'description' => 'From departure to return, every moment is flawlessly orchestrated. You simply arrive and immerse yourself entirely in the journey.',
                        'image_url' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=800',
                        'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>'
                    ]
                ];

                foreach ([1, 2, 3] as $i):
                    $step = isset($process_steps[$i]) ? $process_steps[$i] : $default_steps[$i];
                    $img = !empty($step['image_url']) ? $step['image_url'] : $default_steps[$i]['image_url'];
                    $img = strpos($img, 'http') === 0 ? $img : $img;
                    $delay = ($i - 1) * 0.1;
                ?>
                <div class="process-card" style="transition-delay: <?php echo $delay; ?>s;">
                    <div class="process-img-canvas" style="background-image: url('<?php echo htmlspecialchars($img); ?>');"></div>
                    <div class="process-content">
                        <div class="process-watermark">0<?php echo $i; ?></div>
                        <div class="process-icon">
                            <?php echo $default_steps[$i]['icon']; ?>
                        </div>
                        <h3 class="process-title"><?php echo htmlspecialchars($step['title'] ?? $default_steps[$i]['title']); ?></h3>
                        <p class="process-text"><?php echo htmlspecialchars($step['description'] ?? $default_steps[$i]['description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ✦ Dynamic Advertisement Banner ✦ -->
    <?php
    if ($pdo) {
        try {
            $ad = $pdo->query("SELECT * FROM advertisements WHERE page_type = 'home' AND is_active = 1")->fetch();
            if ($ad):
                $ad_bg = !empty($ad['image_url']) ? htmlspecialchars($ad['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=2000';
    ?>
    <section class="ad-banner-section" style="padding: 2rem 0 6rem 0; background: linear-gradient(180deg, #050505 0%, #050a14 100%);">
        <div class="container">
            <div class="ad-banner" style="position: relative; min-height: 320px; border-radius: 20px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; background: url('<?php echo $ad_bg; ?>') no-repeat center center; background-size: cover; display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #050a14 0%, rgba(5, 10, 20, 0.85) 45%, rgba(5, 10, 20, 0.2) 100%); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; padding: 4rem; max-width: 60%; display: flex; flex-direction: column; align-items: flex-start; gap: 1.5rem;">
                    <h3 class="serif" style="font-size: 2.5rem; color: #ffffff; font-weight: 500; line-height: 1.3; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        <?php echo htmlspecialchars($ad['title']); ?>
                    </h3>
                    <a href="<?php echo htmlspecialchars($ad['btn_link']); ?>" class="btn-gold" style="padding: 1.1rem 2.8rem; font-size: 0.9rem; font-weight: 600; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 4px 20px rgba(197, 160, 89, 0.25); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; color: #000; background: var(--gold); border: none;">
                        <span><?php echo htmlspecialchars($ad['btn_text']); ?></span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>

    <!-- ✦ Theme Tours Section ("Bespoke Curation Themes") ✦ -->
    <?php
    if ($pdo) {
        try {
            // Fetch all active package tour types
            $theme_counts = [];
            $all_pkgs = $pdo->query("SELECT tour_type FROM packages WHERE is_active = 1")->fetchAll();
            foreach ($all_pkgs as $row) {
                if (!empty($row['tour_type'])) {
                    $tags = array_map('trim', explode(',', $row['tour_type']));
                    foreach ($tags as $tag) {
                        $normalized = ucwords(strtolower($tag));
                        // Clean up "Tour" or "Tours" suffixes to maintain ultra-clean visual presentation
                        $normalized = preg_replace('/\b(tours|tour)\b/i', '', $normalized);
                        $normalized = trim($normalized);
                        if ($normalized !== '') {
                            if (!isset($theme_counts[$normalized])) {
                                $theme_counts[$normalized] = 0;
                            }
                            $theme_counts[$normalized]++;
                        }
                    }
                }
            }

            $default_bg = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
            $default_icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';

            // Load active travel themes from the database
            $theme_meta = [];
            try {
                $db_categories = $pdo->query("SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                foreach ($db_categories as $cat) {
                    $theme_meta[$cat['name']] = [
                        'bg' => !empty($cat['image_url']) ? $cat['image_url'] : $default_bg,
                        'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon,
                        'tagline' => $cat['tagline']
                    ];
                }

                // Custom sort: align the theme list order to match the database display order
                $ordered_theme_counts = [];
                foreach ($db_categories as $cat) {
                    if (isset($theme_counts[$cat['name']])) {
                        $ordered_theme_counts[$cat['name']] = $theme_counts[$cat['name']];
                        unset($theme_counts[$cat['name']]);
                    }
                }
                // Append any remaining unregistered themes that appear in packages to the end
                foreach ($theme_counts as $name => $count) {
                    $ordered_theme_counts[$name] = $count;
                }
                $theme_counts = $ordered_theme_counts;

            } catch (Exception $e) {}

            if (!empty($theme_counts)):
    ?>
    <section class="themes-curation-section" style="padding: 2rem 0 6rem 0; background: #050a14;">
        <div class="container">
            <div style="margin-bottom: 4rem;">
                <span class="section-label-gold" style="letter-spacing: 0.25em;">CURATED THEMES</span>
                <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                    Bespoke Travel <span style="font-style: italic; color: var(--gold);">Experiences.</span>
                </h2>
            </div>

            <!-- Dynamic Horizontal Pill Strip (Inspired by Pic 3) -->
            <div class="themes-pills-container" style="overflow-x: auto; white-space: nowrap; padding-bottom: 1.5rem; margin-bottom: 3.5rem; scrollbar-width: none; -ms-overflow-style: none;">
                <div style="display: inline-flex; gap: 1.2rem; min-width: 100%;">
                    <?php foreach ($theme_counts as $name => $count): 
                        $meta = isset($theme_meta[$name]) ? $theme_meta[$name] : ['icon' => $default_icon];
                    ?>
                    <a href="packages.php?theme=<?php echo urlencode($name); ?>" class="theme-pill" style="display: flex; align-items: center; gap: 0.9rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(197,160,89,0.15); border-radius: 50px; padding: 0.6rem 1.6rem 0.6rem 0.6rem; text-decoration: none; transition: all 0.3s ease;">
                        <span class="theme-pill-icon" style="width: 38px; height: 38px; border-radius: 50%; background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                            <span style="width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; color: var(--gold);">
                                <?php echo $meta['icon']; ?>
                            </span>
                        </span>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-family: 'Inter', sans-serif; font-size: 0.85rem; font-weight: 600; color: #ffffff; letter-spacing: 0.05em;"><?php echo htmlspecialchars($name); ?></span>
                            <span style="font-size: 0.65rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500; margin-top: 1px;"><?php echo $count . ($count === 1 ? ' Tour' : ' Tours'); ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Themes Dashboard Cards Grid (Inspired by Pic 2) -->
            <div class="themes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.2rem;">
                <?php 
                $card_limit = 4;
                $card_count = 0;
                foreach ($theme_counts as $name => $count): 
                    if ($card_count >= $card_limit) break;
                    $meta = isset($theme_meta[$name]) ? $theme_meta[$name] : ['bg' => $default_bg, 'icon' => $default_icon];
                    $card_count++;
                ?>
                <div class="theme-card" style="position: relative; height: 380px; border-radius: 24px; overflow: hidden; border: 1px solid rgba(197, 160, 89, 0.12); cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 10px 30px rgba(0,0,0,0.3);" onclick="window.location.href='packages.php?theme=<?php echo urlencode($name); ?>'">
                    <!-- Backdrop image with zoom on hover -->
                    <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($meta['bg']); ?>') no-repeat center center; background-size: cover; transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);" class="t-bg"></div>
                    <!-- Luxury gradient overlay -->
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5, 10, 20, 0.1) 0%, rgba(5, 10, 20, 0.4) 60%, rgba(5, 10, 20, 0.95) 100%); z-index: 1;"></div>
                    
                    <!-- Top float overlay tag -->
                    <div style="position: absolute; top: 20px; left: 20px; background: rgba(5, 10, 20, 0.7); border: 1px solid rgba(197, 160, 89, 0.3); padding: 6px 16px; border-radius: 50px; color: var(--gold); font-size: 0.65rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; backdrop-filter: blur(8px); z-index: 2;">
                        CURATED
                    </div>

                    <!-- Footer Content Panel -->
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 2.2rem; z-index: 2; box-sizing: border-box; display: flex; flex-direction: column; gap: 0.8rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <!-- Circular gold glass icon badge -->
                            <div style="width: 46px; height: 46px; border-radius: 50%; background: rgba(197, 160, 89, 0.15); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; color: var(--gold);">
                                <span style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; color: var(--gold);">
                                    <?php echo $meta['icon']; ?>
                                </span>
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 500; color: #ffffff; margin: 0; line-height: 1.2;"><?php echo htmlspecialchars($name); ?></h3>
                                <?php if (!empty($meta['tagline'])): ?>
                                    <span style="font-family: 'Inter', sans-serif; font-size: 0.7rem; color: rgba(255,255,255,0.5); font-weight: 400; margin-top: 2px; text-transform: capitalize; letter-spacing: 0.02em;"><?php echo htmlspecialchars($meta['tagline']); ?></span>
                                <?php endif; ?>
                                <span style="font-family: 'Inter', sans-serif; font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-top: 6px; display: block;">
                                    <?php echo $count . ($count === 1 ? ' Elite Escape' : ' Elite Escapes'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
            .theme-pill:hover {
                background: rgba(197, 160, 89, 0.08) !important;
                border-color: var(--gold) !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(197, 160, 89, 0.15);
            }
            .theme-pill:hover .theme-pill-icon {
                background: var(--gold) !important;
                color: #000 !important;
            }
            .theme-card:hover {
                transform: translateY(-8px);
                border-color: var(--gold) !important;
                box-shadow: 0 15px 35px rgba(197, 160, 89, 0.2) !important;
            }
            .theme-card:hover .t-bg {
                transform: scale(1.06);
            }
            .themes-pills-container::-webkit-scrollbar {
                display: none;
            }
        </style>
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>

    <!-- Trusted & Accredited By -->
    <?php
    $accreditations = [];
    if ($pdo) {
        try {
            $accreditations = $pdo->query("SELECT * FROM accreditations WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
            $seenNames = [];
            $seenImages = [];
            $filteredAccreditations = [];

            foreach ($accreditations as $acc) {
                $normalizedName = strtolower(trim((string) ($acc['name'] ?? '')));
                $imageUrl = trim((string) ($acc['image_url'] ?? ''));
                $imageFingerprint = $imageUrl;

                if ($imageUrl !== '' && strpos($imageUrl, 'http') !== 0) {
                    $localImagePath = __DIR__ . '/' . ltrim(str_replace('\\', '/', $imageUrl), '/');
                    if (is_file($localImagePath)) {
                        $hash = @md5_file($localImagePath);
                        if ($hash) {
                            $imageFingerprint = $hash;
                        }
                    }
                }

                if (($normalizedName !== '' && isset($seenNames[$normalizedName])) || isset($seenImages[$imageFingerprint])) {
                    continue;
                }

                if ($normalizedName !== '') {
                    $seenNames[$normalizedName] = true;
                }
                $seenImages[$imageFingerprint] = true;
                $filteredAccreditations[] = $acc;
            }

            $accreditations = $filteredAccreditations;
        } catch (PDOException $e) { $accreditations = []; }
    }

    $accreditationsLoop = $accreditations;
    if (count($accreditationsLoop) > 1) {
        $firstAccreditation = array_shift($accreditationsLoop);
        $accreditationsLoop[] = $firstAccreditation;
    }
    ?>
    <?php if (!empty($accreditations)): ?>
    <style>
        .accreditations-section {
            padding: 4rem 0 5rem;
            background: #050a14;
            border-top: 1px solid rgba(197,160,89,0.1);
        }
        .accreditations-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            justify-content: center;
            margin-bottom: 3rem;
        }
        .accreditations-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--gold);
            white-space: nowrap;
            margin: 0;
        }
        .section-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(197,160,89,0.35), transparent);
            max-width: 200px;
        }
        .accreditations-track {
            overflow: hidden;
            display: flex;
            width: 100%;
            mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent);
        }
        .accreditations-scroll {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            flex-shrink: 0;
            animation: accred-marquee 42s linear infinite;
        }
        .accreditations-scroll[aria-hidden="true"] {
            margin-left: 2.5rem;
        }
        .accreditations-scroll:hover,
        .accreditations-track:hover .accreditations-scroll {
            animation-play-state: paused;
        }
        @keyframes accred-marquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-100%); }
        }
        .accreditation-logo {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(197,160,89,0.15) !important;
            border-radius: 12px !important;
            padding: 6px !important;
            width: 110px !important;
            height: 110px !important;
            min-width: 110px !important;
            max-width: 110px !important;
            flex: 0 0 110px !important;
            transition: border-color 0.3s, background 0.3s !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }
        .accreditation-logo:hover {
            border-color: rgba(197,160,89,0.45) !important;
            background: rgba(197,160,89,0.06) !important;
        }
        .accreditation-logo img {
            display: block !important;
            width: 98px !important;
            height: 98px !important;
            min-width: 98px !important;
            min-height: 98px !important;
            object-fit: contain !important;
            object-position: center !important;
            border-radius: inherit !important;
            filter: grayscale(30%) brightness(1.15) !important;
            transition: filter 0.3s, transform 0.3s !important;
        }
        .accreditation-logo:hover img {
            filter: grayscale(0%) brightness(1.25);
            transform: scale(1.05);
        }
    </style>
    <section class="accreditations-section">
        <div class="container">
            <div class="accreditations-header">
                <span class="section-line"></span>
                <h2 class="accreditations-title">Trusted &amp; Accredited By</h2>
                <span class="section-line"></span>
            </div>
            <div class="accreditations-track">
                <div class="accreditations-scroll">
                    <?php foreach ($accreditations as $acc): ?>
                    <div class="accreditation-logo">
                        <img src="<?php echo htmlspecialchars($acc['image_url']); ?>" alt="<?php echo htmlspecialchars($acc['name']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="accreditations-scroll" aria-hidden="true">
                    <?php foreach ($accreditationsLoop as $acc): ?>
                    <div class="accreditation-logo">
                        <img src="<?php echo htmlspecialchars($acc['image_url']); ?>" alt="">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ✦ The Loop Distinction ("Why Choose Us" - Unique Luxury Layout) ✦ -->
    <style>
        .distinction-section {
            padding: 8rem 0;
            background: #050a14;
            border-top: 1px solid rgba(197, 160, 89, 0.08);
            position: relative;
            overflow: hidden;
        }
        
        /* Subtle Golden Ambient Light Source */
        .distinction-section::after {
            content: '';
            position: absolute;
            top: 25%;
            right: -15%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(197, 160, 89, 0.035) 0%, transparent 75%);
            pointer-events: none;
            z-index: 1;
        }
        
        .distinction-layout {
            display: grid;
            grid-template-columns: 1.1fr 2fr;
            gap: 6rem;
            position: relative;
            z-index: 2;
        }
        
        /* Left Editorial Promo Block */
        .distinction-brand-col {
            position: sticky;
            top: 130px;
            align-self: start;
        }
        
        .distinction-brand-col h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.85rem;
            font-weight: 400;
            color: #ffffff;
            line-height: 1.25;
            margin: 0.8rem 0 1.8rem;
        }
        
        .distinction-brand-col p {
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.7;
            margin: 0 0 2.5rem 0;
        }
        
        .distinction-cta-link {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--gold);
            font-family: 'Inter', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            transition: gap 0.3s ease, color 0.3s ease;
        }
        
        .distinction-cta-link:hover {
            color: #ffffff;
            gap: 1.2rem;
        }
        
        .distinction-cta-link svg {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            transition: transform 0.3s;
        }
        
        /* Right Grid of Elite Pillars */
        .distinction-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4.5rem 3.5rem;
        }
        
        /* Frameless Sleek Pillar */
        .distinction-item {
            border-left: 2px solid rgba(197, 160, 89, 0.12);
            padding-left: 1.8rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .distinction-item:hover {
            border-color: var(--gold);
            padding-left: 2.4rem;
        }
        
        .distinction-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .distinction-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem;
            font-weight: 600;
            color: var(--gold);
            line-height: 1;
            opacity: 0.8;
            transition: transform 0.4s ease;
        }
        
        .distinction-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(197, 160, 89, 0.04);
            border: 1px solid rgba(197, 160, 89, 0.15);
            color: var(--gold);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .distinction-item:hover .distinction-num {
            transform: translateY(-2px);
        }
        
        .distinction-item:hover .distinction-icon {
            background: var(--gold);
            color: #050a14;
            border-color: var(--gold);
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 4px 15px rgba(197, 160, 89, 0.35);
        }
        
        .distinction-icon svg {
            width: 18px;
            height: 18px;
            stroke-width: 1.5;
        }
        
        .distinction-item h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 500;
            color: #ffffff;
            margin: 0.8rem 0 0.8rem;
            letter-spacing: 0.01em;
        }
        
        .distinction-item p {
            font-family: 'Inter', sans-serif;
            font-size: 0.86rem;
            color: rgba(255, 255, 255, 0.45);
            line-height: 1.68;
            margin: 0;
        }
        
        /* Responsive Adaptations */
        @media (max-width: 1024px) {
            .distinction-layout {
                grid-template-columns: 1fr;
                gap: 4.5rem;
            }
            .distinction-brand-col {
                position: relative;
                top: 0;
            }
            .distinction-brand-col h2 {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .distinction-section {
                padding: 5.5rem 0;
            }
            .distinction-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            .distinction-item {
                padding-left: 1.5rem;
            }
            .distinction-item:hover {
                padding-left: 1.8rem;
            }
        }
    </style>

    <section class="distinction-section">
        <div class="container">
            <div class="distinction-layout">
                
                <!-- Left Editorial Showcase Column -->
                <div class="distinction-brand-col">
                    <span class="section-label-gold" style="letter-spacing: 0.25em;">THE LOOP DISTINCTION</span>
                    <h2>Why Discerning Explorers Choose Us.</h2>
                    <p>We do not organize tours; we choreograph high-altitude symphonies. Every detail of our private loops is tailored to deliver uncompromising luxury, profound local connection, and absolute operational excellence.</p>
                    <a href="#contact" class="distinction-cta-link">
                        Begin your bespoke story
                        <svg viewBox="0 0 24 24">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
                
                <!-- Right Staggered Elite Pillars -->
                <div class="distinction-grid">
                    
                    <!-- 01. Definitive Transparency -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">01</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="6 2 18 2 22 8 12 22 2 8 6 2"></polygon>
                                </svg>
                            </div>
                        </div>
                        <h3>Definitive Transparency</h3>
                        <p>No estimated margins or surprise local surcharges. The elite quotation we deliver is all-inclusive and final—designed so you can focus entirely on the horizon ahead.</p>
                    </div>

                    <!-- 02. Private Travel Architects -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">02</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3>Private Travel Architects</h3>
                        <p>Never wait in a customer service queue. An elite travel designer oversees your entire itinerary, available around the clock to orchestrate adjustments live on the go.</p>
                    </div>

                    <!-- 03. Singular Curation -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">03</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                                </svg>
                            </div>
                        </div>
                        <h3>Singular Curation</h3>
                        <p>We reject rigid pre-packaged routes. Every single day of your journey is crafted strictly around your private rhythm, diet, companion preferences, and pace.</p>
                    </div>

                    <!-- 04. Off-Market Privileges -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">04</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="7.5" cy="15.5" r="5.5"></circle>
                                    <path d="M21 2l-6 6m-3-3l3 3m1-1l3 3"></path>
                                </svg>
                            </div>
                        </div>
                        <h3>Off-Market Privileges</h3>
                        <p>Our deep direct partnerships with boutique estates, private lodges, and high-altitude hosts unlock pricing and exclusive access unavailable on any public booking portals.</p>
                    </div>

                    <!-- 05. Prestige Circles -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">05</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7z"></path>
                                    <rect x="3" y="20" width="18" height="2" rx="1"></rect>
                                </svg>
                            </div>
                        </div>
                        <h3>Prestige Circles</h3>
                        <p>Each loop you journey with us elevates your status. Unlock tailored benefits, priority bookings for high-demand seasons, and invitations to private luxury escapes.</p>
                    </div>

                    <!-- 06. Absolute Safeguards -->
                    <div class="distinction-item">
                        <div class="distinction-meta">
                            <span class="distinction-num">06</span>
                            <div class="distinction-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3>Absolute Safeguards</h3>
                        <p>Comprehensive high-tier travel assistance and rapid medical priority handling are integrated into your booking. High-end journeys deserve absolute certainty.</p>
                    </div>

                </div>
                
            </div>
    </section>

    <!-- ✦ Dynamic Bottom Advertisement Banner ✦ -->
    <?php
    if ($pdo) {
        try {
            $ad2 = $pdo->query("SELECT * FROM advertisements WHERE page_type = 'home_bottom' AND is_active = 1")->fetch();
            if ($ad2):
                $ad2_bg = !empty($ad2['image_url']) ? htmlspecialchars($ad2['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=2000';
    ?>
    <section class="ad-banner-section" style="padding: 2rem 0 6rem 0; border-top: 1px solid rgba(197, 160, 89, 0.08); background: linear-gradient(180deg, #050a14 0%, #050505 100%);">
        <div class="container">
            <div class="ad-banner" style="position: relative; min-height: 320px; border-radius: 20px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; background: url('<?php echo $ad2_bg; ?>') no-repeat center center; background-size: cover; display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #050a14 0%, rgba(5, 10, 20, 0.85) 45%, rgba(5, 10, 20, 0.2) 100%); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; padding: 4rem; max-width: 60%; display: flex; flex-direction: column; align-items: flex-start; gap: 1.5rem;">
                    <h3 class="serif" style="font-size: 2.5rem; color: #ffffff; font-weight: 500; line-height: 1.3; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.5); font-family: 'Playfair Display', serif;">
                        <?php echo htmlspecialchars($ad2['title']); ?>
                    </h3>
                    <a href="<?php echo htmlspecialchars($ad2['btn_link']); ?>" class="btn-gold" style="padding: 1.1rem 2.8rem; font-size: 0.9rem; font-weight: 600; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 4px 20px rgba(197, 160, 89, 0.25); text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; color: #000; background: var(--gold); border: none;">
                        <span><?php echo htmlspecialchars($ad2['btn_text']); ?></span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>

    <!-- Journal & Insights Preview -->
    <?php
    $recent_blogs = [];
    if ($pdo) {
        try {
            $recent_blogs = $pdo->query("SELECT * FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
        } catch (PDOException $e) {}
    }
    if (!empty($recent_blogs)):
    ?>
    <section class="home-journal-section" style="padding: 6rem 0; background: linear-gradient(180deg, rgba(5,10,20,0) 0%, rgba(10,15,30,0.4) 100%); border-top: 1px solid rgba(197, 160, 89, 0.08);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <span class="section-label" style="letter-spacing: 0.15em; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 0.5rem;">JOURNAL & INSIGHTS</span>
                <h2 class="serif" style="font-family: 'Playfair Display', serif; font-size: 3.2rem; color: #ffffff; margin: 0 auto; font-weight: 500; line-height: 1.25; text-align: center; max-width: 800px;">
                    The Art <span style="font-style: italic; color: var(--gold);">of Wandering.</span>
                </h2>
            </div>
            
            <div class="home-journal-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
                <?php foreach ($recent_blogs as $post): 
                    $post_img = !empty($post['image_url']) ? $post['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
                    $post_img = strpos($post_img, 'http') === 0 ? $post_img : $post_img;
                ?>
                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="journal-card" style="text-decoration: none; display: flex; flex-direction: column; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 12px 30px rgba(0,0,0,0.3); height: 100%;">
                    <div style="position: relative; height: 180px; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($post_img); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, rgba(0,0,0,0) 60%, rgba(5,10,20,0.9) 100%); z-index: 1;"></div>
                        <span style="position: absolute; top: 12px; left: 12px; background: rgba(5,10,20,0.72); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(197,160,89,0.2); color: var(--gold); padding: 3px 10px; border-radius: 30px; font-size: 0.68rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; z-index: 2;">
                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                        </span>
                    </div>
                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1; justify-content: space-between;">
                        <div>
                            <span style="color: var(--gold); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; display: block; margin-bottom: 0.5rem;">BY <?php echo htmlspecialchars($post['author']); ?></span>
                            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: #ffffff; margin: 0 0 0.8rem 0; line-height: 1.45; transition: color 0.3s; font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3.5rem;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.55; margin: 0 0 1.2rem 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                        </div>
                        <span style="color: #e07e26 !important; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 5px; transition: color 0.3s;">
                            <span>Read More</span>
                            <span style="transition: transform 0.3s; display: inline-block;">&rarr;</span>
                        </span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center;">
                <a href="blog.php" class="btn-primary" style="padding: 0.8rem 2.4rem; border-radius: 50px; font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.08em; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                    <span>Explore Journal</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Hover interaction script/styles -->
    <style>
    .journal-card:hover {
        transform: translateY(-6px);
        border-color: rgba(197, 160, 89, 0.2) !important;
        box-shadow: 0 20px 40px rgba(197, 160, 89, 0.05), 0 12px 30px rgba(0, 0, 0, 0.5) !important;
        background: rgba(255,255,255,0.03) !important;
    }
    .journal-card:hover img {
        transform: scale(1.06) !important;
    }
    .journal-card:hover h3 {
        color: var(--gold) !important;
    }
    .journal-card:hover span span:last-child {
        transform: translateX(4px) !important;
    }
    </style>
    <?php endif; ?>

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
                <form action="api-submit-lead.php" method="POST" class="js-lead-form">
                    <button type="submit" class="btn-card" style="width: 100%; padding: 1.2rem;">Begin Your Journey &nbsp; &rarr;</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Step 2 Modal for Hero Horizontal Form -->
    <div id="step2Modal" class="modal-overlay" style="display: none;">
        <div class="planner-box" style="max-width: 500px;">
            <button type="button" class="close-modal" id="closeStep2Modal">&times;</button>
            <div class="planner-header">
                <h2>Almost There!</h2>
                <p>Please provide your contact details so our curators can reach you.</p>
            </div>
            <div class="planner-body">
                <form id="heroFinalSubmitForm" action="api-submit-lead.php" method="POST" class="js-lead-form">
                    <input type="hidden" name="destination" id="hiddenHeroDest">
                    <input type="hidden" name="date" id="hiddenHeroDate">
                    <input type="hidden" name="adults" id="hiddenHeroAdults">
                    <input type="hidden" name="children" id="hiddenHeroChildren">
                    <input type="hidden" name="enforce_recaptcha" value="1">
                    
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                        <input type="text" name="name" placeholder="Your Full Name" required>
                    </div>
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.054 15.054 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01Z"/></svg></span>
                        <input type="tel" name="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
                    <div class="recaptcha-shell" style="margin-bottom: 20px;">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn-card" style="width: 100%;">COMPLETE ENQUIRY &rarr;</button>
                </form>
            </div>
        </div>
    </div>

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
