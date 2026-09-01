<?php

$isMobile = false;
$skipMobileOnboarding = isset($_GET['onboarding']) && $_GET['onboarding'] === 'skip';
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
    $isMobile = true;
}
require_once '../config/db.php';
    require_once '../config/recaptcha.php';
    $use_recaptcha = recaptchaIsConfigured();
    $recaptcha_site_key = recaptchaSiteKey();
    $hotel_partners = [];
    $fixed_departures = [];
    if (isset($pdo)) {
        try {
            $hotel_partners = $pdo->query("SELECT * FROM hotel_partners WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
            $fixed_departures = $pdo->query("
                SELECT fd.*, p.title as package_title, p.image_url, p.slug as package_slug, p.days, p.nights 
                FROM fixed_departures fd 
                JOIN packages p ON fd.package_id = p.id 
                WHERE fd.is_active = 1 AND fd.start_date >= CURDATE()
                ORDER BY fd.start_date ASC 
                LIMIT 6
            ")->fetchAll();
        } catch (PDOException $e) {}
    }

    require_once '../includes/data_faqs.php';

    require_once '../config/recaptcha.php';
    $is_home_page = true;
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
<link rel="stylesheet" href="css/home.css?v=1787330592">


<?php if (isset($_GET['lead_sent'])): ?>
<div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: #739d1b; color: white; padding: 16px 28px; border-radius: 10px; font-size: 0.9rem; box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
     Thank you! Our team will contact you shortly.
</div>

<?php endif; ?>

    <?php if (!$isMobile): ?>
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
            
            <?php
                $slide = $hero_slides[0] ?? [
                    'type' => ($settings['hero_type'] ?? 'video'),
                    'url' => ($settings['hero_url'] ?? 'https://cdn.pixabay.com/video/2025/05/06/277097_large.mp4')
                ];
                $slideUrl = $slide['url'];
                if (($slide['type'] ?? '') === 'video') {
                    $websafeHeroVideo = 'assets/img/hero/hero_websafe.mp4';
                    if (is_file(__DIR__ . '/assets/img/hero/hero_websafe.mp4')) {
                        $slideUrl = $websafeHeroVideo;
                    } elseif (
                        !empty($settings['hero_url']) &&
                        preg_match('/^https?:\\/\\/.+\\.mp4(?:\\?|$)/i', $settings['hero_url'])
                    ) {
                        $slideUrl = $settings['hero_url'];
                    } else {
                        $slideUrl = cacheHeroVideoLocally($slideUrl);
                    }
                }
            ?>
            <?php if (($slide['type'] ?? '') === 'video'): ?>
                <div class="hero-slide-fallback hero-bg-poster" style="background-image: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000');"></div>
                <video class="hero-bg-video" autoplay muted loop playsinline preload="auto" poster="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2000">
                    <source src="<?php echo htmlspecialchars($slideUrl); ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <div class="hero-slide-img hero-bg-poster" style="background-image: url('<?php echo htmlspecialchars($slideUrl); ?>');"></div>
            <?php endif; ?>
        </div>

        <div class="hero-content">
            <div class="hero-centered-content">
                <!-- Text Content -->
                <div class="hero-text-center">
                    <span class="tagline hero-reveal-up delay-150 mb-15 inline-block"><?php echo htmlspecialchars($settings['hero_text_sub'] ?? 'LEISURE LOOP TRIP'); ?></span>
                    <h1 class="hero-reveal-up delay-300 mb-20"><?php echo $settings['hero_text_main'] ?? 'Where Every Journey Begins'; ?></h1>
                    <p class="hero-reveal-up hero-subtitle delay-450"><?php echo htmlspecialchars($settings['hero_text_desc'] ?? 'Personalized tours crafted for every traveler.'); ?></p>
                </div>

                <!-- Hero Inline 2-Step Form -->
                <div class="hero-search-wrapper hero-reveal-up delay-600">
                    <div class="search-header text-center mb-20">
                        <span class="hero-card-label mx-auto mb-10 inline-block">Exclusive Offer</span>
                        <h3 class="search-title">Plan Your Perfect Journey</h3>
                        <p class="search-subtitle">Share your details and our travel curators will craft a bespoke itinerary for you.</p>
                        
                    </div>

                    <div class="hero-inline-form relative z-55">

                        <!-- STEP 1: Search bar row -->
                        <div class="hero-form-row--bar" id="heroStep1Row">
                            <div class="search-field">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 21s-6-5.686-6-11a6 6 0 1 1 12 0c0 5.314-6 11-6 11Zm0-8.5a2.5 2.5 0 1 0-2.5-2.5 2.5 2.5 0 0 0 2.5 2.5Z"/></svg></span>
                                
<label for="heroDestInput" class="sr-only">Destination</label>
<input type="text" id="heroDestInput" placeholder="Destination">
                            </div>
                            <div class="search-field">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M7 2h2v3H7Zm8 0h2v3h-2ZM4 6h16a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm0 5v9h16v-9Z"/></svg></span>
                                
<label for="heroDateInput" class="sr-only">Travel Date</label>
<input type="text" id="heroDateInput" placeholder="Travel Date">
                            </div>
                            <div class="search-field custom-select-wrapper" id="travelerDropdownTrigger">
                                <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                                
<label for="travelerInputDisplay" class="sr-only">2 Adults</label>
<input type="text" id="travelerInputDisplay" placeholder="2 Adults" readonly class="cursor-pointer">
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
                        <div class="hero-step2-reveal hidden" id="heroStep2">
                            <form id="heroLeadForm" action="/api/v1/leads" method="POST" class="js-lead-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                <input type="hidden" name="destination" id="hiddenDest">
                                <input type="hidden" name="date" id="hiddenDate">
                                <input type="hidden" name="adults" id="hiddenAdults" value="2">
                                <input type="hidden" name="children" id="hiddenChildren" value="0">

                                <!-- Name | Phone | Email row -->
                                <div class="hero-form-row--bar hero-step2-bar">
                                    <div class="search-field">
                                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                                        
<label for="hero_name" class="sr-only">Guest Name</label>
<input id="hero_name" type="text" name="name" placeholder="Guest Name" required>
                                    </div>
                                    <div class="search-field">
                                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.054 15.054 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01Z"/></svg></span>
                                        
<label for="hero_phone" class="sr-only">Phone Number</label>
<input id="hero_phone" type="tel" name="phone" placeholder="Phone Number" required>
                                    </div>
                                    <div class="search-field no-border-right">
                                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
                                        
<label for="hero_email" class="sr-only">Email Address</label>
<input id="hero_email" type="email" name="email" placeholder="Email Address" required>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="hero-form-row--submit">
                                    <div></div>
                                    <button type="submit" class="btn-search-submit">COMPLETE ENQUIRY &rarr;</button>
                                </div>
                            </form>
                        </div>

                    </div>
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
            $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 0 ORDER BY id DESC")->fetchAll();
        }
    ?>
    <section class="packages-section" id="featured" style="position: relative; overflow: hidden;">
        <!-- Minimalist Vector Mountains -->
        <img src="images/parallax/vector_mountains.png" id="mountainBg" alt="Mountains" class="parallax-mountain">
        
        <div class="watermark" style="position: absolute; z-index: 1;">Most Coveted</div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="packages-header" style="justify-content: center; position: relative; text-align: center; display: flex; align-items: center;">
                <button class="nav-arrow" aria-label="Previous" data-action="scroll-carousel" data-carousel="mostCovetedCarousel" data-dir="-1" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%);">&larr;</button>
                <div>
                    <span class="section-label-gold" style="margin-left: auto; margin-right: auto;">The Curated Collection</span>
                    <h2 class="section-title" style="margin-bottom: 0;">of the <span class="serif" style="color: var(--gold); font-style: italic;">Season.</span></h2>
                </div>
                <button class="nav-arrow" aria-label="Next" data-action="scroll-carousel" data-carousel="mostCovetedCarousel" data-dir="1" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); border-color: var(--gold); color: var(--gold);">&rarr;</button>
            </div>
            
            <div style="position: relative;">

                <div id="mostCovetedCarousel" class="packages-carousel" style="padding-top: 20px; gap: 30px !important;">
                <?php foreach ($featured_packages as $pkg): ?>
                <div class="package-card responsive-package-card">
                    <div class="pkg-img" style="background: transparent; position: relative; overflow: hidden; border-radius: 24px 24px 0 0;">
                        <div class="pkg-img-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>'); background-size: cover; background-position: center; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1); border-radius: 24px 24px 0 0;"></div>
                        <div class="pkg-badge" style="position: absolute; top: 16px; left: 16px; background: rgba(7, 12, 24, 0.75); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; padding: 6px 14px; border-radius: 50px; backdrop-filter: blur(10px); z-index: 2;">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($pkg['days'], 2, '0', STR_PAD_LEFT) . " DAYS";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo str_pad($nights, 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($days, 2, '0', STR_PAD_LEFT) . " DAYS";
                                } else {
                                    echo "CUSTOM DURATION";
                                }
                            }
                            ?>
                        </div>
                        <div class="pkg-overlay"></div>
                        <div class="pkg-overlay-content">
                            <span class="pkg-dest"><?php echo htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
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
    <section class="destinations-section" id="destinations" style="position: relative; overflow: hidden; background-color: #000;">
        <!-- Animated Silk Contour Lines -->
        <div id="silkContours" class="silk-contours-layer">
            
            <svg viewBox="0 0 1000 1000" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                <defs>
                    <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#c5a059" stop-opacity="0" />
                        <stop offset="50%" stop-color="#c5a059" stop-opacity="1" />
                        <stop offset="100%" stop-color="#c5a059" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path class="silk-line line1" d="M0,350 C300,250 700,550 1000,350" fill="none" stroke="url(#goldGradient)" stroke-width="1.5" />
                <path class="silk-line line2" d="M0,450 C400,650 600,250 1000,450" fill="none" stroke="url(#goldGradient)" stroke-width="1" />
                <path class="silk-line line3" d="M0,550 C200,450 800,750 1000,550" fill="none" stroke="url(#goldGradient)" stroke-width="0.5" />
            </svg>
        </div>
        <div class="watermark" style="position: absolute; z-index: 1;">DESTINATIONS</div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="destinations-header" style="position: relative; z-index: 3;">
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
            <?php
                $destinations_list = [];
                if ($pdo) {
                    $destinations_list = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE d.is_active = 1 ORDER BY d.display_order ASC, d.created_at DESC")->fetchAll();
                }
            ?>
            <div class="destinations-carousel-wrapper">
                <div class="destinations-carousel">
                    <?php foreach ($destinations_list as $dest): ?>
                    <div class="dest-card <?php echo strtolower($dest['category']) === 'international' ? 'hidden' : ''; ?>" data-category="<?php echo strtolower(htmlspecialchars($dest['category'] ?? 'domestic')); ?>" data-url="destination-details.php?slug=<?php echo urlencode($dest['slug']); ?>">
                        <div class="dest-img" style="background-image: url('<?php echo htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>');">
                            <div class="dest-badge"><?php echo strtoupper(htmlspecialchars($dest['category'] ?? 'DOMESTIC')); ?></div>
                            <div class="dest-overlay-content">
                                <div class="dest-main-info">
                                    <div class="dest-text">
                                        <h3><?php echo htmlspecialchars($dest['name']); ?></h3>
                                        <div class="dest-tours">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span><?php echo (int)$dest['tour_count']; ?> Tours</span>
                                        </div>
                                    </div>
                                    <div class="dest-card-arrow">&rarr;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
        </div>
    </section>

    <!-- Global Escapes (International Packages) -->
    <?php 
        $international_packages = [];
        if ($pdo) {
            $international_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 1")->fetchAll();
        }
    ?>
    <?php if (!empty($international_packages)): ?>
    <section class="packages-section" id="global-escapes" style="position: relative; overflow: hidden; background: #000; padding: 5rem 0; clip-path: inset(0);">
        
        <!-- CSS Fixed Pinned Parallax Wrapper (Bug-free alternative to GSAP pin) -->
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none;">
            <!-- Layer 1: Stardust (Base) -->
            <div id="ge-stardust" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; background-image: radial-gradient(circle at center, rgba(197, 160, 89, 0.05) 0%, transparent 70%); opacity: 0.8; pointer-events: none;">
                
                <?php for($i=0; $i<50; $i++): ?>
                    <div class="stardust-particle" style="width: <?php echo rand(1,3); ?>px; height: <?php echo rand(1,3); ?>px; top: <?php echo rand(0,100); ?>%; left: <?php echo rand(0,100); ?>%;"></div>
                <?php endfor; ?>
            </div>
            
            <!-- Layer 2: 3D Holographic Wireframe Globe (Background) -->
            <div id="ge-globe" style="position: absolute; top: 40%; left: 50%; width: 100%; max-width: 750px; transform: translate(-50%, -50%); z-index: 1; opacity: 0.25; pointer-events: none; display: flex; justify-content: center;">
                <svg viewBox="0 0 100 100" fill="none" stroke="#ffffff" stroke-width="0.15" stroke-dasharray="0.4 1.2" style="width: 100%; height: auto; max-height: 750px; display: block;">
                    <!-- Outer Sphere Boundary -->
                    <circle cx="50" cy="50" r="48" />
                    
                    <!-- Longitudes (Meridians distributed by sine) -->
                    <ellipse cx="50" cy="50" rx="12.3" ry="48" />
                    <ellipse cx="50" cy="50" rx="24" ry="48" />
                    <ellipse cx="50" cy="50" rx="33.9" ry="48" />
                    <ellipse cx="50" cy="50" rx="41.5" ry="48" />
                    <ellipse cx="50" cy="50" rx="46.3" ry="48" />
                    
                    <!-- Latitudes (Parallels distributed by sine) -->
                    <!-- Equator -->
                    <ellipse cx="50" cy="50" rx="48" ry="10" />
                    
                    <!-- Northern Hemisphere -->
                    <ellipse cx="50" cy="37.6" rx="46.3" ry="9.6" />
                    <ellipse cx="50" cy="26" rx="41.5" ry="8.6" />
                    <ellipse cx="50" cy="16.1" rx="33.9" ry="7.0" />
                    <ellipse cx="50" cy="8.5" rx="24" ry="5.0" />
                    <ellipse cx="50" cy="3.7" rx="12.3" ry="2.5" />

                    <!-- Southern Hemisphere -->
                    <ellipse cx="50" cy="62.4" rx="46.3" ry="9.6" />
                    <ellipse cx="50" cy="74" rx="41.5" ry="8.6" />
                    <ellipse cx="50" cy="83.9" rx="33.9" ry="7.0" />
                    <ellipse cx="50" cy="91.5" rx="24" ry="5.0" />
                    <ellipse cx="50" cy="96.3" rx="12.3" ry="2.5" />
                </svg>
            </div>

            <!-- Layer 3: Airplane Path (Foreground Bottom) -->
            <div id="ge-airplane" style="position: absolute; bottom: 40px; left: 0; width: 100%; height: 60px; z-index: 10; pointer-events: none;">
                <!-- Dotted flight path line -->
                <div style="position: absolute; top: 50%; left: 0; width: 100%; height: 1px; border-top: 1px dashed rgba(255,255,255,0.3); transform: translateY(-50%);"></div>
                <!-- Airplane Wrapper -->
                <div class="the-plane" style="position: absolute; top: 50%; left: 0; margin-top: -20px; width: 40px; height: 40px;">
                    <div style="transform: rotate(90deg); width: 100%; height: 100%; opacity: 0.8;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="#ffffff">
                            <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="watermark" style="position: absolute; z-index: 1;">Global Escapes</div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="packages-header" style="justify-content: center; position: relative; text-align: center; display: flex; align-items: center;">
                <button class="nav-arrow" aria-label="Previous" data-action="scroll-carousel" data-carousel="internationalCarousel" data-dir="-1" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%);">&larr;</button>
                <div>
                    <span class="section-label-gold" style="margin-left: auto; margin-right: auto;">World Collection</span>
                    <h2 class="section-title" style="margin-bottom: 0;">Global <span class="serif" style="color: var(--gold); font-style: italic;">Escapes.</span></h2>
                </div>
                <button class="nav-arrow" aria-label="Next" data-action="scroll-carousel" data-carousel="internationalCarousel" data-dir="1" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); border-color: var(--gold); color: var(--gold);">&rarr;</button>
            </div>
            
            <div style="position: relative;">
                <div id="internationalCarousel" class="packages-carousel" style="padding-top: 20px; gap: 30px !important;">
                <?php foreach ($international_packages as $pkg): ?>
                <div class="package-card responsive-package-card">
                    <div class="pkg-img" style="background: transparent; position: relative; overflow: hidden; border-radius: 24px 24px 0 0;">
                        <div class="pkg-img-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>'); background-size: cover; background-position: center; transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1); border-radius: 24px 24px 0 0;"></div>
                        <div class="pkg-badge" style="position: absolute; top: 16px; left: 16px; background: rgba(7, 12, 24, 0.75); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); font-size: 0.65rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; padding: 6px 14px; border-radius: 50px; backdrop-filter: blur(10px); z-index: 2;">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($pkg['days'], 2, '0', STR_PAD_LEFT) . " DAYS";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo str_pad($nights, 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($days, 2, '0', STR_PAD_LEFT) . " DAYS";
                                } else {
                                    echo "CUSTOM DURATION";
                                }
                            }
                            ?>
                        </div>
                        <div class="pkg-overlay"></div>
                        <div class="pkg-overlay-content">
                            <span class="pkg-dest"><?php echo htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
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
        </div>

    </section>
    <?php endif; ?>

    <!-- The Art of Discovery (How We Work) -->
    <section class="how-we-work-section" style="position: relative; padding: 6rem 0; background-color: var(--obsidian); border-top: 1px solid rgba(197, 160, 89, 0.1); clip-path: inset(0);">
        
        <!-- CSS-only Video Parallax -->
        <video autoplay loop muted playsinline style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; object-fit: cover; z-index: 0; opacity: 0.25; pointer-events: none;">
            <source src="assets/videos/Ocean.webm" type="video/webm">
        </video>

        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <span class="section-label-gold" style="letter-spacing: 0.2em; text-transform: uppercase; font-size: 0.8rem; font-weight: 600;">The Process</span>
                <h2 class="section-title" style="margin-top: 1rem;">The Art of <span style="color: var(--gold); font-style: italic; font-family: 'Playfair Display', serif;">Discovery.</span></h2>
                <p style="color: var(--text-muted); max-width: 600px; margin: 1rem auto 0; font-size: 1.1rem;">Curating your journey is the prelude to the adventure.</p>
            </div>
            
            <div class="process-carousel" style="display: flex; gap: 2rem; padding-top: 20px; padding-bottom: 2rem; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
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
                <div class="process-card" style="transition-delay: <?php echo $delay; ?>s; flex: 0 0 calc(33.333% - 1.333rem); min-width: 320px;">
                    <div class="process-img-wrapper">
                        <div class="process-img-canvas" style="background-image: url('<?php echo htmlspecialchars($img); ?>');"></div>
                    </div>
                    <div class="process-content">
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
            <div class="ad-banner" style="position: relative; min-height: 250px; border-radius: 20px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; background: url('<?php echo $ad_bg; ?>') no-repeat center center; background-size: cover; display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #050a14 0%, rgba(5, 10, 20, 0.9) 45%, rgba(5, 10, 20, 0.2) 100%); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; padding: 2.5rem 4rem; max-width: 60%; display: flex; flex-direction: column; align-items: flex-start; gap: 0.8rem;">
                    <?php if (file_exists(__DIR__ . '/assets/img/leisure.png')): ?>
                        <div style="margin-bottom: -0.5rem; display: inline-block;">
                            <img src="assets/img/leisure.png" alt="Leisure Loop Trip" style="max-height: 55px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                        </div>
                    <?php else: ?>
                        <span style="font-family: 'Inter', sans-serif; font-size: 1.2rem; font-weight: 700; color: #fff; letter-spacing: 0.1em; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.5); margin-bottom: -0.5rem;">LEISURE <span style="color: var(--gold);">LOOP</span></span>
                    <?php endif; ?>
                    <h3 class="serif" style="font-size: 2.1rem; color: #ffffff; font-weight: 600; line-height: 1.35; margin: 0; text-shadow: 0 4px 15px rgba(0,0,0,0.8); background: linear-gradient(90deg, #ffffff 0%, var(--gold) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo htmlspecialchars($ad['title']); ?>
                    </h3>
                    <a href="javascript:void(0);" data-action="open-modal" data-target="#noticePopup" class="btn-gold" style="padding: 1.2rem 3rem; font-size: 0.95rem; font-weight: 700; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.15em; box-shadow: 0 8px 25px rgba(197, 160, 89, 0.35); text-decoration: none; display: inline-flex; align-items: center; gap: 0.8rem; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); color: #000; background: linear-gradient(135deg, var(--gold) 0%, #f0d59e 100%); border: none; transform-origin: center;">
                        <span><?php echo htmlspecialchars($ad['btn_text']); ?></span>
                        <span style="font-size: 1.2rem;">&rarr;</span>
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

            // Load all active travel themes from the database and package counts
            $all_themes_list = [];
            try {
                $db_categories = $pdo->query("SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
                $seen_themes = [];
                foreach ($db_categories as $cat) {
                    $name = trim($cat['name']);
                    if ($name === '') continue;
                    $seen_themes[strtolower($name)] = true;
                    $count = isset($theme_counts[$name]) ? $theme_counts[$name] : 2;
                    
                    // Automatically resolve image from universal folder images/themes/ if it exists
                    $slug_name = strtolower(preg_replace('/[^a-zA-Z]/', '', $name));
                    if ($slug_name === 'teagardens') $slug_name = 'teagarden';
                    if ($slug_name === 'couples') $slug_name = 'couple';
                    if ($slug_name === 'roadtrips') $slug_name = 'roadtrip';
                    if ($slug_name === 'hillstations') $slug_name = 'hillstation';
                    if ($slug_name === 'trek') $slug_name = 'treks';
                    
                    $local_theme_path = 'images/themes/' . $slug_name . '.webp';
                    $bg = file_exists(__DIR__ . '/' . $local_theme_path) ? $local_theme_path : (!empty($cat['image_url']) ? $cat['image_url'] : $default_bg);

                    $all_themes_list[] = [
                        'name' => $name,
                        'count' => $count,
                        'bg' => $bg,
                        'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon,
                        'tagline' => !empty($cat['tagline']) ? $cat['tagline'] : 'Curated Holiday Experiences'
                    ];
                }

                // Append any extra themes found directly in active packages that were not in tour_categories table
                foreach ($theme_counts as $name => $count) {
                    if (!isset($seen_themes[strtolower($name)])) {
                        $slug_name = strtolower(preg_replace('/[^a-zA-Z]/', '', $name));
                        if ($slug_name === 'teagardens') $slug_name = 'teagarden';
                        if ($slug_name === 'couples') $slug_name = 'couple';
                        if ($slug_name === 'roadtrips') $slug_name = 'roadtrip';
                        if ($slug_name === 'hillstations') $slug_name = 'hillstation';
                        if ($slug_name === 'trek') $slug_name = 'treks';
                        
                        $local_theme_path = 'images/themes/' . $slug_name . '.webp';
                        $bg = file_exists(__DIR__ . '/' . $local_theme_path) ? $local_theme_path : $default_bg;

                        $all_themes_list[] = [
                            'name' => $name,
                            'count' => $count,
                            'bg' => $bg,
                            'icon' => $default_icon,
                            'tagline' => 'Curated Holiday Experiences'
                        ];
                        $seen_themes[strtolower($name)] = true;
                    }
                }
            } catch (Exception $e) {}

            // Ensure we have themes to display, slice into 2 rows (~8 items per row out of 16)
            if (!empty($all_themes_list)):
                $total_themes = count($all_themes_list);
                $mid_idx = (int) ceil($total_themes / 2);
                $row1_themes = array_slice($all_themes_list, 0, $mid_idx);
                $row2_themes = array_slice($all_themes_list, $mid_idx);
                if (empty($row2_themes)) {
                    $row2_themes = $row1_themes;
                }

                // Duplicate items 4 times to ensure flawless infinite scrolling across all display widths
                $row1_display = array_merge($row1_themes, $row1_themes, $row1_themes, $row1_themes);
                $row2_display = array_merge($row2_themes, $row2_themes, $row2_themes, $row2_themes);
    ?>
    <section class="themes-curation-section" style="padding: 2rem 0 6rem 0; background: #050a14; position: relative; overflow: hidden;">
        <!-- Mandala Parallax Watermark -->
        <img id="themeMandala" src="images/parallax/mandala.png" alt="Mandala">
        
        <div class="container" style="position: relative; z-index: 2; margin-bottom: 2rem;">
            <div style="margin-bottom: 3.5rem; text-align: center;">
                <span class="section-label-gold" style="letter-spacing: 0.25em;">CURATED THEMES</span>
                <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                    Bespoke Travel <span style="font-style: italic; color: var(--gold);">Experiences.</span>
                </h2>
            </div>

            <!-- Restored Dynamic Horizontal Pill Strip Above Circle Cards -->
            <div class="themes-pills-container">
                <div class="themes-pills-inner">
                    <?php foreach ($all_themes_list as $item): ?>
                    <a href="packages.php?theme=<?php echo urlencode($item['name']); ?>" class="theme-pill">
                        <span class="theme-pill-icon">
                            <span style="width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; color: var(--gold);">
                                <?php echo $item['icon']; ?>
                            </span>
                        </span>
                        <div class="theme-pill-info">
                            <span class="theme-pill-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="theme-pill-count"><?php echo $item['count'] . ($item['count'] === 1 ? ' Tour' : ' Tours'); ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Two Auto-Scrolling & Draggable Circular Theme Rows (Infinite Loop) -->
        <div class="themes-circle-marquee-container" style="position: relative; z-index: 2; width: 100%; overflow: hidden; display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Row 1: Left to Right Marquee -->
            <div id="circleMarqueeRow1" class="marquee-row draggable-marquee" style="width: 100%; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding: 25px 0; cursor: default; user-select: none;">
                <div class="marquee-track">
                    <?php foreach ($row1_display as $item): ?>
                    <a href="packages.php?theme=<?php echo urlencode($item['name']); ?>" class="theme-circle-card-item">
                        <div class="circle-card-photo">
                            <div class="t-circle-bg" style="background-image: url('<?php echo htmlspecialchars($item['bg']); ?>');"></div>
                            <div class="circle-overlay"></div>
                        </div>
                        <div class="circle-card-info">
                            <h3 class="circle-theme-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Row 2: Right to Left Marquee -->
            <div id="circleMarqueeRow2" class="marquee-row draggable-marquee" style="width: 100%; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding: 25px 0; cursor: default; user-select: none;">
                <div class="marquee-track">
                    <?php foreach ($row2_display as $item): ?>
                    <a href="packages.php?theme=<?php echo urlencode($item['name']); ?>" class="theme-circle-card-item">
                        <div class="circle-card-photo">
                            <div class="t-circle-bg" style="background-image: url('<?php echo htmlspecialchars($item['bg']); ?>');"></div>
                            <div class="circle-overlay"></div>
                        </div>
                        <div class="circle-card-info">
                            <h3 class="circle-theme-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        

        
    </section>
    <?php
            endif;
        } catch (Exception $e) {}
    }
    ?>

    <!-- ✦ The Loop Distinction ("Why Choose Us" - Unique Luxury Layout) ✦ -->
    

    <section class="distinction-section" style="background-color: #050a14;">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="distinction-layout">
                
                <!-- Left Editorial Showcase Column -->
                <div class="distinction-brand-col" style="background-image: linear-gradient(rgba(5, 10, 20, 0.88), rgba(5, 10, 20, 0.95)), url('images/parallax/traveler_vector.png'); background-size: cover; background-position: right center; padding: 3.5rem; border-radius: 24px; border: 1px solid rgba(197, 160, 89, 0.15); box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
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
    ?>
    <?php if (!empty($accreditations)): ?>
    
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
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ✦ Dynamic Middle Advertisement Banner (Above Journal) ✦ -->
    <?php
    if ($pdo) {
        try {
            $ad2 = $pdo->query("SELECT * FROM advertisements WHERE page_type = 'home_middle' AND is_active = 1")->fetch();
            if ($ad2):
                $ad2_bg = !empty($ad2['image_url']) ? htmlspecialchars($ad2['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=2000';
    ?>
    <section class="ad-banner-section" style="padding: 2rem 0 6rem 0; border-top: 1px solid rgba(197, 160, 89, 0.08); background: linear-gradient(180deg, #050a14 0%, #050505 100%);">
        <div class="container">
            <div class="ad-banner" style="position: relative; min-height: 250px; border-radius: 20px; border: 1px solid rgba(197, 160, 89, 0.2); overflow: hidden; background: url('<?php echo $ad2_bg; ?>') no-repeat center center; background-size: cover; display: flex; align-items: center; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, #050a14 0%, rgba(5, 10, 20, 0.85) 45%, rgba(5, 10, 20, 0.2) 100%); z-index: 1;"></div>
                <div style="position: relative; z-index: 2; padding: 2.5rem 4rem; max-width: 60%; display: flex; flex-direction: column; align-items: flex-start; gap: 0.8rem;">
                    <?php if (file_exists(__DIR__ . '/assets/img/leisure.png')): ?>
                        <div style="margin-bottom: -0.5rem; display: inline-block;">
                            <img src="assets/img/leisure.png" alt="Leisure Loop Trip" style="max-height: 55px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                        </div>
                    <?php else: ?>
                        <span style="font-family: 'Inter', sans-serif; font-size: 1.2rem; font-weight: 700; color: #fff; letter-spacing: 0.1em; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.5); margin-bottom: -0.5rem; display: inline-block;">LEISURE <span style="color: var(--gold);">LOOP</span></span>
                    <?php endif; ?>
                    
                    <h3 class="serif" style="font-size: 2.1rem; color: #ffffff; font-weight: 600; line-height: 1.35; margin: 0; text-shadow: 0 4px 15px rgba(0,0,0,0.8); background: linear-gradient(90deg, #ffffff 0%, var(--gold) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo htmlspecialchars($ad2['title']); ?>
                    </h3>
                    
                    <a href="javascript:void(0);" data-action="open-modal" data-target="#noticePopup" class="btn-gold" style="padding: 1.2rem 3rem; font-size: 0.95rem; font-weight: 700; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.15em; box-shadow: 0 8px 25px rgba(197, 160, 89, 0.35); text-decoration: none; display: inline-flex; align-items: center; gap: 0.8rem; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); color: #000; background: linear-gradient(135deg, var(--gold) 0%, #f0d59e 100%); border: none; transform-origin: center;">
                        <span><?php echo htmlspecialchars($ad2['btn_text']); ?></span>
                        <span style="font-size: 1.2rem;">&rarr;</span>
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
    <section class="home-journal-section" id="journal-insights" style="position: relative; overflow: hidden; padding: 6rem 0; background: #000000;">
        
        <!-- Black Water Flow Background -->
        <div id="journal-black-water" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden; background: transparent;">
            
            <div class="water-layer water-1"></div>
            <div class="water-layer water-2"></div>
            <div class="water-layer water-3"></div>
            
            <!-- Depth overlay gradient for seamless top and bottom edges -->
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(180deg, #000000 0%, rgba(0,0,0,0) 20%, rgba(0,0,0,0) 80%, #000000 100%);"></div>
        </div>

        <div class="container" style="position: relative; z-index: 2;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <span class="section-label" style="letter-spacing: 0.15em; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 0.5rem;">JOURNAL & INSIGHTS</span>
                <h2 class="serif" style="font-family: 'Playfair Display', serif; font-size: 3.2rem; color: #ffffff; margin: 0 auto; font-weight: 500; line-height: 1.25; text-align: center; max-width: 800px;">
                    The Art <span style="font-style: italic; color: var(--gold);">of Wandering.</span>
                </h2>
            </div>
            
            <div class="home-journal-carousel" style="display: flex; gap: 2rem; margin-bottom: 4rem; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-top: 20px; padding-bottom: 1rem;">
                <?php foreach ($recent_blogs as $post): 
                    $post_img = !empty($post['image_url']) ? $post['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
                    $post_img = strpos($post_img, 'http') === 0 ? $post_img : $post_img;
                ?>
                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="journal-card" style="text-decoration: none; display: flex; flex-direction: column; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 12px 30px rgba(0,0,0,0.3); height: 100%; flex: 0 0 calc(25% - 1.5rem); min-width: 280px;">
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
    
    <?php endif; ?>

    <!-- Inner Circle Newsletter Banner -->
    <section class="inner-circle-banner">
        <div id="inner-circle-bg" style="background-image: url('https://images.unsplash.com/photo-1778090887585-b27fae5b6f03?q=80&w=1471&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');"></div>
        <div class="inner-circle-overlay"></div>
        
        
        
        <div class="container newsletter-container">
            <span class="section-label-gold">Join The Inner Circle</span>
            <h2 class="newsletter-title">Curated travel insights, <br>delivered exclusively to you.</h2>
            <p class="newsletter-desc">Be the first to access our private journey invitations, seasonal curations, and extraordinary stories from around the globe.</p>
            
            <form id="subscribe-form" action="/api/v1/subscriptions" method="POST" style="max-width: 550px; margin: 0 auto; position: relative;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="newsletter-flex">
                    
<label for="subscribe-email" class="sr-only">Your Email Address</label>
<input type="email" id="subscribe-email" name="email" required placeholder="Your Email Address" class="newsletter-input">
                    <button type="submit" id="subscribe-btn" class="btn-primary newsletter-btn">Subscribe</button>
                </div>
            </form>
            <div id="subscribe-msg" style="margin-top: 15px; font-size: 0.9rem; min-height: 20px;"></div>
        </div>
    </section>

    
    <?php
    $gallery_images = [];
    if (isset($pdo)) {
        try {
            $gallery_images = $pdo->query("SELECT * FROM gallery_images WHERE is_active = 1 ORDER BY created_at DESC LIMIT 12")->fetchAll();
        } catch (PDOException $e) {
            $gallery_images = [];
        }
    }
    $default_images = [
        'https://images.unsplash.com/photo-1533587851505-d119e13bf0eb?q=80&w=800',
        'https://images.unsplash.com/photo-1517760444937-f6397edcbbcd?q=80&w=800',
        'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800',
        'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800',
        'https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=800',
        'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800',
        'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?q=80&w=800',
        'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=800'
    ];

    $top_track = [];
    $bottom_track = [];

    if (count($gallery_images) >= 4) {
        $half = ceil(count($gallery_images) / 2);
        $top_track = array_slice($gallery_images, 0, $half);
        $bottom_track = array_slice($gallery_images, $half);
    } else {
        // Fill with defaults
        foreach ($default_images as $k => $url) {
            if ($k < 4) $top_track[] = ['image_url' => $url, 'alt_text' => 'Guest Experience'];
            else $bottom_track[] = ['image_url' => $url, 'alt_text' => 'Guest Experience'];
        }
    }
    ?>


    



    <!-- Real Smiles Gallery -->
    <section class="real-smiles-section" style="padding: 8rem 0; background-color: var(--obsidian); border-top: 1px solid rgba(197, 160, 89, 0.1);">
        <div class="container">
            <div class="smiles-grid">
                <!-- Left Text Side -->
                <div class="smiles-text">
                    <h2 class="section-title">Genuine Moments, <br><span style="color: var(--gold); font-style: italic;">Unforgettable Journeys.</span></h2>
                    <p style="color: var(--text-muted); font-size: 1.15rem; line-height: 1.7; margin-bottom: 2.5rem; margin-top: 1.5rem;">
                        Glimpse into the extraordinary adventures of our travelers. These are authentic memories crafted through our bespoke itineraries, capturing the true essence of discovery.
                    </p>
                    <a href="gallery.php" class="btn-primary" style="padding: 1rem 2.5rem; border-radius: 50px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.1em; display: inline-block;">Explore Gallery</a>
                </div>
                
                <!-- Right Gallery Side -->
                <div class="smiles-gallery-container">
                    <!-- Top Track (Moves Left) -->
                    <div class="smiles-marquee-wrapper">
                        <div class="smiles-marquee track-left">
                            <div class="smiles-marquee-content">
                                <?php foreach($top_track as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                                <div class="smile-card"><div class="smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="smiles-marquee-content" aria-hidden="true">
                                <?php foreach($top_track as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                                <div class="smile-card"><div class="smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bottom Track (Moves Right) -->
                    <div class="smiles-marquee-wrapper">
                        <div class="smiles-marquee track-right">
                            <div class="smiles-marquee-content">
                                <?php foreach($bottom_track as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                                <div class="smile-card"><div class="smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="smiles-marquee-content" aria-hidden="true">
                                <?php foreach($bottom_track as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                                <div class="smile-card"><div class="smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($fixed_departures)): ?>
    <!-- FIXED DEPARTURE TOURS -->
    <section id="fixed-departures" class="fixed-departures-section" style="padding: 6rem 0; background-color: var(--bg-dark); border-top: 1px solid rgba(197, 160, 89, 0.1); position: relative; overflow: hidden;">
        <!-- Rotating Astrolabe Parallax Background -->
        <div class="fd-parallax-compass" style="position: absolute; left: 50%; top: 50%; margin-left: -400px; margin-top: -250px; width: 800px; height: 800px; opacity: 0.15; pointer-events: none; z-index: 0; color: var(--gold);">
            <svg viewBox="0 0 500 500" fill="none" stroke="currentColor">
                <g id="compass-ring-1">
                    <circle cx="250" cy="250" r="240" stroke-dasharray="2 6" stroke-width="2"/>
                    <circle cx="250" cy="250" r="225" stroke-width="1"/>
                    <path d="M250 0 L250 25 M250 475 L250 500 M0 250 L25 250 M475 250 L500 250" stroke-width="2"/>
                    <path d="M73 73 L90 90 M427 427 L410 410 M73 427 L90 410 M427 73 L410 90" stroke-width="1"/>
                    <circle cx="250" cy="250" r="215" stroke-dasharray="1 10" stroke-width="4"/>
                </g>
                <g id="compass-ring-2">
                    <circle cx="250" cy="250" r="180" stroke-width="0.5"/>
                    <circle cx="250" cy="250" r="165" stroke-dasharray="4 8 2 8" stroke-width="1.5"/>
                    <polygon points="250,50 265,160 350,250 265,340 250,450 235,340 150,250 235,160" stroke-width="1"/>
                    <circle cx="250" cy="250" r="120" stroke-dasharray="2 4"/>
                    <polygon points="250,110 260,170 310,250 260,330 250,390 240,330 190,250 240,170" stroke-width="0.5"/>
                </g>
                <g id="compass-ring-3">
                    <circle cx="250" cy="250" r="90" stroke-width="1"/>
                    <circle cx="250" cy="250" r="75" stroke-dasharray="1 5" stroke-width="2"/>
                    <circle cx="250" cy="250" r="15" fill="currentColor"/>
                    <ellipse cx="250" cy="250" rx="140" ry="35" transform="rotate(30 250 250)" stroke-width="0.5"/>
                    <ellipse cx="250" cy="250" rx="140" ry="35" transform="rotate(-30 250 250)" stroke-width="0.5"/>
                    <ellipse cx="250" cy="250" rx="140" ry="35" transform="rotate(90 250 250)" stroke-width="0.5"/>
                    <circle cx="250" cy="250" r="45" stroke-dasharray="2 2" stroke-width="0.5"/>
                </g>
            </svg>
        </div>
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <span class="section-label-gold" style="letter-spacing: 0.25em;">LIMITED AVAILABILITY</span>
                <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 1.5rem 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                    Upcoming Fixed <span style="font-style: italic; color: var(--gold);">Departures</span>
                </h2>
                <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 700px; margin: 0 0 2rem 0;">
                    Join a curated group of like-minded explorers on these specially scheduled journeys.
                </p>
            </div>

            <div class="fd-grid">
                <?php foreach ($fixed_departures as $fd): 
                    $img = preg_match('/^https?:\/\//i', $fd['image_url']) ? $fd['image_url'] : ltrim($fd['image_url'], '/');
                    $seats_left = $fd['available_seats'];
                    $status = $fd['status'];
                    $status_color = 'var(--gold)';
                    $status_bg = 'rgba(197, 160, 89, 0.1)';
                    if ($status == 'Sold Out') {
                        $status_color = '#ef4444';
                        $status_bg = 'rgba(239, 68, 68, 0.1)';
                    } elseif ($status == 'Filling Fast' || $seats_left <= 5) {
                        $status_color = '#f59e0b';
                        $status_bg = 'rgba(245, 158, 11, 0.1)';
                    }
                ?>
                <div class="fd-card">
                    <div class="fd-img-wrap">
                        <div class="fd-img" style="background-image: url('<?php echo htmlspecialchars($img); ?>');"></div>
                        <div class="fd-badge" style="background: <?php echo $status_bg; ?>; color: <?php echo $status_color; ?>; border: 1px solid <?php echo $status_color; ?>;">
                            <?php echo htmlspecialchars($status); ?> 
                            <?php if ($status != 'Sold Out') echo "- " . $seats_left . " Seats Left"; ?>
                        </div>
                    </div>
                    <div class="fd-content">
                        <div class="fd-dates">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <?php echo date('M d', strtotime($fd['start_date'])) . ' - ' . date('M d, Y', strtotime($fd['end_date'])); ?>
                        </div>
                        <h3 class="fd-title"><?php echo htmlspecialchars($fd['package_title']); ?></h3>
                        <div class="fd-details">
                            <span style="display: flex; align-items: center; gap: 0.3rem;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <?php echo $fd['days']; ?>D / <?php echo $fd['nights']; ?>N</span>
                            <div class="fd-price">
                                <span style="font-size: 0.8rem; color: var(--text-muted);">From</span> 
                                ₹<?php echo number_format($fd['price']); ?>
                            </div>
                        </div>
                        <a href="package-detail.php?slug=<?php echo $fd['package_slug']; ?>&fd=<?php echo $fd['id']; ?>" class="btn-primary" style="display: block; text-align: center; margin-top: 1.5rem; padding: 0.8rem; font-size: 0.9rem;">View Journey</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- THE LEISURE LOOP DIFFERENCE -->
    <section class="leisure-difference-section" id="difference">
        <div id="leisure-difference-bg"></div>
        <div class="difference-overlay"></div>
        <div class="container difference-container">
            <span class="section-label-gold" style="letter-spacing: 0.25em;">THE LEISURE LOOP DIFFERENCE</span>
            <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 1.5rem 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                The Art of Curated <span style="font-style: italic; color: var(--gold);">Travel.</span>
            </h2>
            <p style="color: var(--text-muted); font-size: 1.15rem; max-width: 700px; margin: 0 0 2rem 0;">
                We don't just book trips; we architect unforgettable experiences tailored exclusively to your desires.
            </p>
        </div>

        <div class="container difference-slider-container">
            <div class="difference-slider" id="differenceSlider">
                <!-- Card 1 -->
                <div class="difference-card">
                    <div class="difference-icon-wrapper">
                        <!-- Bespoke Itineraries Icon (Compass/Map) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                            <line x1="9" y1="3" x2="9" y2="18"></line>
                            <line x1="15" y1="6" x2="15" y2="21"></line>
                        </svg>
                    </div>
                    <h3>Bespoke Itineraries</h3>
                    <p>Your journey, entirely on your terms. We craft custom itineraries that adapt to your pace and preferences.</p>
                </div>
                <!-- Card 2 -->
                <div class="difference-card">
                    <div class="difference-icon-wrapper">
                        <!-- Immersive Encounters Icon (Eye/Compass) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                    <h3>Immersive Encounters</h3>
                    <p>Go beyond the guidebook. We provide exclusive access to hidden gems and authentic local cultures.</p>
                </div>
                <!-- Card 3 -->
                <div class="difference-card">
                    <div class="difference-icon-wrapper">
                        <!-- Uncompromising Quality Icon (Diamond/Star) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <h3>Uncompromising Quality</h3>
                    <p>From boutique stays to private transfers, we vet every detail to ensure absolute perfection.</p>
                </div>
                <!-- Card 4 -->
                <div class="difference-card">
                    <div class="difference-icon-wrapper">
                        <!-- 24/7 Concierge Support Icon (Headset/Clock) -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12A10.06 10.06 0 0 0 12 2a10 10 0 0 0-10 10c0 5.53 4.5 10 10 10a10 10 0 0 0 5-1.34"></path>
                            <path d="M12 22v-4"></path>
                            <path d="M12 12V7"></path>
                            <path d="M12 12l3.5 3.5"></path>
                        </svg>
                    </div>
                    <h3>24/7 Concierge Support</h3>
                    <p>Travel with complete peace of mind. Our dedicated team is at your service around the clock, anywhere in the world.</p>
                </div>
            </div>
        </div>
        
    </section>

    <?php
        $home_bottom_ad = null;
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM advertisements WHERE page_type = 'home_bottom' AND is_active = 1");
                $stmt->execute();
                $home_bottom_ad = $stmt->fetch();
            } catch (Exception $e) {}
        }
        
        if ($home_bottom_ad):
            $ad_bg = !empty($home_bottom_ad['image_url']) ? htmlspecialchars(strpos($home_bottom_ad['image_url'], 'http') === 0 ? $home_bottom_ad['image_url'] : $home_bottom_ad['image_url']) : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=2000';
    ?>
    <!-- PRE-FAQ BANNER -->
    <section class="pre-faq-banner" style="padding: 2rem 0 6rem 0; background: var(--obsidian);">
        <div class="container">
            <div style="background: linear-gradient(to right, rgba(5,10,20,0.95) 0%, rgba(5,10,20,0.7) 40%, rgba(5,10,20,0.2) 100%), url('<?php echo $ad_bg; ?>'); background-size: cover; background-position: center; border-radius: 20px; padding: 3rem 4rem; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
                <div style="max-width: 600px; position: relative; z-index: 2;">
                    <?php if (file_exists(__DIR__ . '/assets/img/leisure.png')): ?>
                        <div style="margin-bottom: 1.2rem; display: inline-block;">
                            <img src="assets/img/leisure.png" alt="Leisure Loop Trip" style="max-height: 55px; display: block; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                        </div>
                    <?php else: ?>
                        <span style="font-family: 'Inter', sans-serif; font-size: 1.2rem; font-weight: 700; color: #fff; letter-spacing: 0.1em; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.5); margin-bottom: 1.2rem; display: inline-block;">LEISURE <span style="color: var(--gold);">LOOP</span></span>
                    <?php endif; ?>
                    
                    <h2 class="serif-accent" style="font-size: 2.1rem; line-height: 1.35; margin-bottom: 1.5rem; font-weight: 600; font-family: 'Playfair Display', serif; color: #ffffff; text-shadow: 0 4px 15px rgba(0,0,0,0.8); background: linear-gradient(90deg, #ffffff 0%, var(--gold) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo nl2br(htmlspecialchars($home_bottom_ad['title'])); ?>
                    </h2>
                    
                    <a href="javascript:void(0);" data-action="open-modal" data-target="#noticePopup" class="btn-gold" style="padding: 1.2rem 3rem; font-size: 0.95rem; font-weight: 700; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.15em; box-shadow: 0 8px 25px rgba(197, 160, 89, 0.35); text-decoration: none; display: inline-flex; align-items: center; gap: 0.8rem; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); color: #000; background: linear-gradient(135deg, var(--gold) 0%, #f0d59e 100%); border: none; transform-origin: center;">
                        <span><?php echo htmlspecialchars($home_bottom_ad['btn_text']); ?></span>
                        <span style="font-size: 1.2rem;">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONIALS - EXPLORER'S MARQUEE -->
    <section class="testimonials-marquee-section" id="testimonials" style="position: relative; background: linear-gradient(to bottom, #000000 0%, #050a14 100%); padding: 6rem 0; overflow: hidden;">
        <div style="text-align: center; margin-bottom: 5rem; position: relative; z-index: 2;">
            <span class="section-label-gold" style="letter-spacing: 0.25em;">TRAVELER DIARIES</span>
            <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                Echoes from the <span style="font-style: italic; color: var(--gold);">Horizon.</span>
            </h2>
        </div>

        

        <div style="overflow: hidden; width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; padding-bottom: 3rem;">
            <?php
            // Fetch active testimonials
            $testimonials = [];
            if ($pdo) {
                $testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC")->fetchAll();
            }
            if (!empty($testimonials)): 
            ?>
            <div class="marquee-wrapper">
                <!-- Track 1 -->
                <div class="marquee-track">
                    <?php foreach ($testimonials as $t): ?>
                    <div class="marquee-item">
                        <img src="<?php echo htmlspecialchars($t['image_url']); ?>" alt="<?php echo htmlspecialchars($t['tour_name']); ?>" class="marquee-photo" style="transform: rotate(<?php echo (int)$t['rotation_angle']; ?>deg);">
                        <div class="marquee-quote">
                            <div style="color: var(--gold); font-size: 2.6rem; margin-bottom: 0.5rem; opacity: 0.4; line-height: 0.5; font-family: serif;">"</div>
                            <p style="font-family: 'Playfair Display', serif; font-size: 1.45rem; color: #fff; line-height: 1.5; margin-bottom: 1.5rem; font-style: italic;">
                                "<?php echo nl2br(htmlspecialchars($t['quote_text'])); ?>"
                            </p>
                            <div>
                                <h4 style="color: var(--gold); margin: 0 0 0.4rem 0; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;"><?php echo htmlspecialchars($t['client_name']); ?></h4>
                                <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem; letter-spacing: 0.05em;"><?php echo htmlspecialchars($t['tour_name']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Track 2 (Duplicate for seamless loop) -->
                <div class="marquee-track">
                    <?php foreach ($testimonials as $t): ?>
                    <div class="marquee-item">
                        <img src="<?php echo htmlspecialchars($t['image_url']); ?>" alt="<?php echo htmlspecialchars($t['tour_name']); ?>" class="marquee-photo" style="transform: rotate(<?php echo (int)$t['rotation_angle']; ?>deg);">
                        <div class="marquee-quote">
                            <div style="color: var(--gold); font-size: 2.6rem; margin-bottom: 0.5rem; opacity: 0.4; line-height: 0.5; font-family: serif;">"</div>
                            <p style="font-family: 'Playfair Display', serif; font-size: 1.45rem; color: #fff; line-height: 1.5; margin-bottom: 1.5rem; font-style: italic;">
                                "<?php echo nl2br(htmlspecialchars($t['quote_text'])); ?>"
                            </p>
                            <div>
                                <h4 style="color: var(--gold); margin: 0 0 0.4rem 0; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;"><?php echo htmlspecialchars($t['client_name']); ?></h4>
                                <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem; letter-spacing: 0.05em;"><?php echo htmlspecialchars($t['tour_name']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <p style="text-align: center; color: rgba(255,255,255,0.5);">No diaries entries found.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Editorial Divider (Golden Thread) -->
    <div class="editorial-divider" style="margin-top: -90px; margin-bottom: -90px; position: relative; z-index: 20;">
        <div class="golden-thread"></div>
        <div class="floating-emblem" style="background: #050a14;">✧</div>
    </div>

    <!-- OUR GROUP OF COMPANIES - THE GOLDEN DECK -->
    <section class="companies-deck-section" id="companies" style="position: relative; padding: 6rem 0; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        
        <!-- Ambient Aura Background -->
        <div class="aura-container">
            <div class="aura aura-gold"></div>
            <div class="aura aura-amber"></div>
        </div>

        <div style="text-align: center; margin-bottom: 3rem; position: relative; z-index: 10;">
            <span class="section-label-gold" style="letter-spacing: 0.25em;">COMPANIES</span>
            <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                Our Group Of <span style="font-style: italic; color: var(--gold);">Companies.</span>
            </h2>
        </div>

        

        <div class="companies-container">
            <!-- Card 2 (Bottom) -->
            <div class="company-card">
                <div class="company-logo-placeholder" style="border: none; background: transparent; box-shadow: none;">
                    <img src="images/tripoo-logo.jpg" alt="Tripoo Hotels Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h3 class="company-name">Tripoo Hotels & Resorts LLP.</h3>
                <p class="company-desc">Curating sanctuary-like retreats nestled within nature's most pristine landscapes. Experience luxury that honors the earth and elevates the spirit.</p>
                <a href="#" class="company-link">Visit Tripoo Hotels &rarr;</a>
            </div>
            
            <!-- Card 1 (Top) -->
            <div class="company-card">
                <div class="company-logo-placeholder" style="border: none; background: transparent; box-shadow: none;">
                    <img src="images/manaya-logo.png" alt="Manaya Hotels Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h3 class="company-name">Manaya Hotels</h3>
                <p class="company-desc">Boutique heritage properties offering unmatched hospitality and architectural grandeur across the Himalayas. A legacy of impeccable service.</p>
                <a href="#" class="company-link">Visit Manaya Hotels &rarr;</a>
            </div>
        </div>
    </section>

    <!-- HOTEL PARTNERS SECTION -->
    <?php if (!empty($hotel_partners)): ?>
    <section class="hotel-partners-section" id="partners">
        <div class="container" style="text-align: center; margin-bottom: 4rem; position: relative; z-index: 3;">
            <span class="section-label-gold" style="letter-spacing: 0.25em;">PRESTIGIOUS COLLABORATIONS</span>
            <h2 class="serif-accent" style="font-size: 2.8rem; margin: 0.5rem 0 0 0; font-weight: 500; font-family: 'Playfair Display', serif; color: white;">
                Our Hotel <span style="font-style: italic; color: var(--gold);">Partners.</span>
            </h2>
        </div>
        
        <div class="partner-marquee-wrapper">
            <div class="partner-marquee-track">
                <?php foreach ($hotel_partners as $partner): 
                    $img_src = $partner['logo_url'];
                    if (!str_starts_with($img_src, 'http')) {
                        if (str_contains($img_src, '/')) {
                            $img_src = $img_src; // If it contains path, leave it (e.g. if we store relative)
                        } else {
                            $img_src = 'assets/img/partners/' . $img_src;
                        }
                    }
                ?>
                    <div class="partner-card">
                        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($partner['name']); ?>" class="partner-logo">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Editorial Divider (Golden Thread) -->
    <div class="editorial-divider">
        <div class="golden-thread"></div>
        <div class="floating-emblem">✧</div>
    </div>

    <!-- FAQ -->
    <section class="faq-section" style="position: relative; overflow: hidden;">
        <div class="faq-watermark" id="faqWatermark">?</div>
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="section-label">Knowledge Base</span>
                <h2 class="section-title">Common <span style="color: var(--gold); font-style: italic;">Curiosities.</span></h2>
            </div>
            <div class="faq-grid">
                <?php
                    $half = ceil(count($faqs) / 2);
                    $left_col = array_slice($faqs, 0, $half);
                    $right_col = array_slice($faqs, $half);
                ?>
                <div class="faq-col">
                    <?php foreach ($left_col as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><h4><?php echo htmlspecialchars($faq['question']); ?></h4><div class="faq-icon">&#10010;</div></div>
                        <div class="faq-answer"><p><?php echo htmlspecialchars($faq['answer']); ?></p></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="faq-col">
                    <?php foreach ($right_col as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><h4><?php echo htmlspecialchars($faq['question']); ?></h4><div class="faq-icon">&#10010;</div></div>
                        <div class="faq-answer"><p><?php echo htmlspecialchars($faq['answer']); ?></p></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="section" id="contact" style="position: relative; overflow: hidden;">
        <div class="gsap-parallax-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 130%; background-image: url('https://images.unsplash.com/photo-1627628352368-7a68191f29e6?q=80&w=1476&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); background-size: cover; background-position: center; z-index: 0;"></div>
        <div style="position: absolute; inset: 0; background: linear-gradient(rgba(5,10,20,0.85), rgba(5,10,20,0.85)); z-index: 1;"></div>
        <div class="container" style="position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1.2fr; gap: 8rem; align-items: center;">
            <div>
                <span class="section-label">Start Your Story</span>
                <h2 class="section-title">Where to <br><span class="serif" style="color: var(--amber);">Next?</span></h2>
            </div>
            <div class="glass-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); padding: 4rem; border-radius: 8px;">
                <form action="/api/v1/leads" method="POST" class="js-lead-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <button type="submit" class="btn-card" style="width: 100%; padding: 1.2rem;">Begin Your Journey &nbsp; &rarr;</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Step 2 Modal for Hero Horizontal Form -->
    <div id="step2Modal" class="modal-overlay">
        <div class="planner-box" style="max-width: 500px;">
                <button type="button" class="close-modal" id="closeStep2Modal" aria-label="Close dialog">&times;</button>
            <div class="planner-header">
                <h2>Almost There!</h2>
                <p>Please provide your contact details so our curators can reach you.</p>
            </div>
            <div class="planner-body">
                <form id="heroFinalSubmitForm" action="/api/v1/leads" method="POST" class="js-lead-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="destination" id="hiddenHeroDest">
                    <input type="hidden" name="date" id="hiddenHeroDate">
                    <input type="hidden" name="adults" id="hiddenHeroAdults">
                    <input type="hidden" name="children" id="hiddenHeroChildren">
                    
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/></svg></span>
                        
<label for="lead_name" class="sr-only">Your Full Name</label>
<input id="lead_name" type="text" name="name" placeholder="Your Full Name" required>
                    </div>
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.054 15.054 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24 11.36 11.36 0 0 0 3.58.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.49a1 1 0 0 1 1 1 11.36 11.36 0 0 0 .57 3.58 1 1 0 0 1-.25 1.01Z"/></svg></span>
                        
<label for="lead_phone" class="sr-only">Phone Number</label>
<input id="lead_phone" type="tel" name="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="field-shell" style="margin-bottom: 20px;">
                        <span class="field-icon"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
                        
<label for="lead_email" class="sr-only">Email Address</label>
<input id="lead_email" type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <button type="submit" class="btn-card" style="width: 100%;">COMPLETE ENQUIRY &rarr;</button>
                </form>
            </div>
        </div>
    </div>


    <?php endif; ?>

    <?php if ($isMobile): ?>
    <?php if (!$skipMobileOnboarding): ?>
    <div class="block md:hidden mobile-splash-view" id="mobile-splash-view">
        <?php include '../includes/mobile_splash.php'; ?>
    </div>
    <?php endif; ?>
    <div class="block md:hidden pb-24" id="mobile-home-view">
        <?php include '../includes/mobile_home.php'; ?>
    </div>
    <?php endif; ?>

<?php
$extra_scripts = ($isMobile ? '<script src="js/modules/mobile-home.js?v=1" defer></script>' : '') . ($isMobile && !$skipMobileOnboarding ? '<script src="js/modules/mobile-splash.js?v=4" defer></script>' : '') . '<script src="js/modules/home.js?v=1787326054"></script><script src="js/modules/mobile-views.js" defer></script>';
$hideFooterOnMobile = true;
include '../includes/footer.php';
?>
