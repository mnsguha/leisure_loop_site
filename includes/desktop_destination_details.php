<?php
$page_title = htmlspecialchars($destination['name']) . " | Elite Travel Experiences";
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/destination-details.css">
<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script src="js/modules/destination-details.js" defer></script>

<div class="destination-page">

<!-- Hero Section -->
<section class="parable-hero destination-hero">
    <?php if (isset($parallax_layers) && !empty($parallax_layers)): ?>
        <?php foreach ($parallax_layers as $index => $layer): ?>
            <?php 
                $speed = $layer['speed'] ?? (0.1 + ($index * 0.15));
                $scale = $layer['scale'] ?? (1.1 + ($index * 0.05));
                $zIndex = $layer['z_index'] ?? ($index + 1);
            ?>
            <img class="p-layer p-layer-hero-media" src="<?php echo htmlspecialchars($layer['image_url']); ?>" alt="Layer <?php echo $index; ?>" style="--layer-z: <?php echo $zIndex; ?>;" data-speed="<?php echo $speed; ?>" data-scale-base="<?php echo $scale; ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
        <?php endforeach; ?>
    <?php else: ?>
        <img class="p-layer p-layer-hero-media" src="<?php echo htmlspecialchars($destination['cover_image'] ?: 'images/placeholder.jpg'); ?>" alt="Hero" style="--layer-z: 1;" data-speed="0.3" data-scale-base="1.15" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
    <?php endif; ?>

    <div class="hero-overlay"></div>
    
    <div class="hero-content">
        <h1 class="hero-title reveal-text-up"><?php echo htmlspecialchars($destination['name']); ?></h1>
        <p class="hero-subtitle reveal-text-up stagger-1"><?php echo htmlspecialchars($destination['tagline']); ?></p>
        <button id="open-story-btn" class="hero-cta reveal-text-up stagger-2" data-action="open-story-modal">
            <span class="hero-cta-text">Read The Story</span>
            <div class="hero-cta-icon-wrap">
                <span class="material-symbols-outlined hero-cta-icon">arrow_downward</span>
            </div>
        </button>
    </div>
</section>

<!-- Quick Facts Bar -->
<div class="facts-bar">
<div class="glass-card facts-bar-inner">
    <div class="fact-item">
        <span class="material-symbols-outlined fact-icon">mountain_flag</span>
        <div class="fact-text-wrap">
            <span class="fact-label">Altitude</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['altitude'] ?: 'Varied'); ?></span>
        </div>
    </div>
    <div class="fact-item">
        <span class="material-symbols-outlined fact-icon">calendar_month</span>
        <div class="fact-text-wrap">
            <span class="fact-label">Best Time</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['best_time'] ?: 'Year Round'); ?></span>
        </div>
    </div>
    <div class="fact-item no-border-bottom-right">
        <span class="material-symbols-outlined fact-icon">schedule</span>
        <div class="fact-text-wrap">
            <span class="fact-label">Duration</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['duration'] ?: 'Custom'); ?></span>
        </div>
    </div>
</div>
</div>

<!-- The Narrative (Editorial Section) -->
<section class="narrative-section" id="narrative">
<div class="section-container">
<div class="narrative-grid">
<div class="narrative-col-left" id="narrative-left-col">
<div class="narrative-pin-target z-20 pb-12">
<div id="narrative-cascade-container">
<!-- Static Hit Zones to prevent animation looping -->
<!-- BUGFIX: Using hit-zone so JS works -->
<div class="hit-zone narrative-hit-zone" data-zone="0"></div>
<div class="hit-zone narrative-hit-zone" data-zone="1"></div>
<div class="hit-zone narrative-hit-zone" data-zone="2"></div>

<div data-pos="0" class="narrative-img-card reveal-scale-hidden stagger-2">
<img alt="Narrative Image 1" src="<?php echo htmlspecialchars($destination['story_narrative_image'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';"/>
</div>
<div data-pos="1" class="narrative-img-card reveal-scale-hidden stagger-3">
<img alt="Narrative Image 2" src="<?php echo htmlspecialchars($destination['story_narrative_image_2'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';"/>
</div>
<div data-pos="2" class="narrative-img-card reveal-scale-hidden stagger-4">
<img alt="Narrative Image 3" src="<?php echo htmlspecialchars($destination['story_narrative_image_3'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';"/>
</div>
</div>
</div>
</div>
<div class="narrative-col-right">
    <div class="narrative-content-wrap">
        <?php
            $dest_words      = preg_split('/[\s&]+/', $destination['name']);
            $watermark_word  = strtoupper(trim($dest_words[0]));
        ?>
        <div class="watermark-text" aria-hidden="true"><?php echo htmlspecialchars($watermark_word); ?></div>
        
        <div class="narrative-accent-line reveal-hidden stagger-1"></div>
        <span class="narrative-subheading reveal-hidden stagger-2">Editorial</span>
        <h2 class="narrative-heading reveal-hidden stagger-3">THE NARRATIVE</h2>
        
        <div class="narrative-body reveal-hidden stagger-4">
            <p class="narrative-dropcap">
                <?php echo nl2br(htmlspecialchars($destination['description_long'] ?: 'Discover ' . $destination['name'] . ' with our exclusive journeys.')); ?>
            </p>
        </div>
    </div>
</div>
</div>
</div>
</section>

<!-- Sightseeing Highlights -->
<?php if (!empty($sightseeing)): ?>
<section class="sightseeing-section" id="sightseeing">
    <div class="section-container">
        <div class="section-header reveal-hidden stagger-1">
            <span class="section-subheading">Must Visit</span>
            <h2 class="section-heading stagger-2">Sightseeing Highlights</h2>
        </div>
        
        <div class="sightseeing-list">
            <?php foreach ($sightseeing as $index => $spot): ?>
                <?php 
                    $isReverse = ($index % 2 !== 0); 
                    $delay = ($index % 3) + 1;
                ?>
                <div class="sightseeing-grid">
                    <div class="glass-card sightseeing-card reveal-hidden stagger-<?php echo $delay; ?> <?php echo $isReverse ? 'sightseeing-card--reverse' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($spot['image']); ?>" alt="<?php echo htmlspecialchars($spot['title']); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <div class="sightseeing-card-overlay"></div>
                    </div>
                    <div class="sightseeing-content reveal-hidden stagger-<?php echo $delay + 1; ?>">
                        <div class="sightseeing-accent"></div>
                        <h3 class="sightseeing-title"><?php echo htmlspecialchars($spot['title']); ?></h3>
                        <p class="sightseeing-desc"><?php echo nl2br(htmlspecialchars($spot['desc'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Signature Journeys (Tours) -->
<section class="journeys-section" id="journeys">
<div class="section-container">
<div class="section-header text-center reveal-hidden stagger-1">
    <span class="section-subheading">Curated Collections</span>
    <h2 class="section-heading stagger-2">SIGNATURE JOURNEYS</h2>
    <p class="section-desc stagger-2">Handpicked experiences designed for the discerning traveler seeking deep immersion and unparalleled comfort.</p>
</div>

<div class="journeys-grid">
<?php if (!empty($related_packages)): ?>
    <?php $delay = 1; ?>
    <?php foreach ($related_packages as $pkg): ?>
    <div class="glass-card journey-card reveal-hidden stagger-<?php echo $delay; ?>">
        <div class="journey-card-image">
            <?php 
                $img_src = !empty($pkg['image_url']) ? htmlspecialchars($pkg['image_url']) : 'assets/images/placeholder_tour.jpg';
            ?>
            <img alt="<?php echo htmlspecialchars($pkg['title']); ?>" src="<?php echo $img_src; ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';"/>
            <div class="journey-card-overlay"></div>
        </div>
        <div class="journey-card-content">
            <div class="journey-card-top">
                <h3 class="journey-card-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                <span class="journey-card-price">₹<?php echo number_format($pkg['price'], 0); ?></span>
            </div>
            <div class="journey-card-meta">
                <span class="journey-meta-item"><span class="material-symbols-outlined">schedule</span> <?php echo htmlspecialchars($pkg['days'] ?? ''); ?> Days</span>
                <span class="journey-meta-item"><span class="material-symbols-outlined"><?php echo (strtolower($pkg['tour_type'] ?? '') == 'wellness') ? 'spa' : ((strtolower($pkg['tour_type'] ?? '') == 'cultural') ? 'history_edu' : 'group'); ?></span> <?php echo htmlspecialchars($pkg['tour_type'] ?? 'Private'); ?></span>
            </div>
            <a href="package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" class="journey-card-cta">Plan This Journey</a>
        </div>
    </div>
    <?php $delay = ($delay % 4) + 1; ?>
    <?php endforeach; ?>
<?php else: ?>
    <div class="journeys-empty">
        <p>No exclusive journeys are currently available for this destination.</p>
    </div>
<?php endif; ?>
</div>
</div>
</section>

<!-- Local Experiences -->
<section class="experiences-section" id="local-experiences">
<div class="section-container">
<div class="section-header text-center reveal-hidden stagger-1">
    <span class="section-subheading">LOCAL EXPERIENCES</span>
    <h2 class="section-heading stagger-2">Immersive Encounters</h2>
</div>

<div class="experiences-grid">
<?php if (!empty($local_experiences)): ?>
    <?php $delay = 1; foreach ($local_experiences as $exp): ?>
    <div class="glass-card experience-card reveal-hidden stagger-<?php echo $delay; ?>">
        <div class="experience-icon-wrap">
            <span class="material-symbols-outlined text-secondary"><?php echo htmlspecialchars($exp['icon']); ?></span>
        </div>
        <h3 class="experience-card-title"><?php echo htmlspecialchars($exp['title']); ?></h3>
        <p class="experience-card-desc"><?php echo htmlspecialchars($exp['desc']); ?></p>
    </div>
    <?php $delay++; endforeach; ?>
<?php else: ?>
    <div class="experiences-empty">
        <p>Experiences coming soon.</p>
    </div>
<?php endif; ?>
</div>
</div>
</section>

<!-- Elite Enquiry Form Section -->
<section class="enquiry-section">
<div class="enquiry-glow">
<div class="enquiry-glow-orb-1"></div>
<div class="enquiry-glow-orb-2"></div>
</div>

<div class="enquiry-container reveal-hidden stagger-1">
<div class="glass-card enquiry-card shadow-2xl border-secondary">
<div class="enquiry-header">
<span class="enquiry-subheading">Bespoke Travel</span>
<h2 class="enquiry-title">Plan Your Elite Journey</h2>
</div>

<form class="enquiry-form" action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
<input type="hidden" name="enforce_recaptcha" value="1">
<div class="form-group">
<span class="material-symbols-outlined form-icon">person</span>
<label for="input_adec1cb7" class="sr-only">Your Full Name</label>
<input id="input_adec1cb7" class="form-input" placeholder="Your Full Name" type="text" required aria-required="true"/>
</div>

<div class="form-grid">
<div class="form-group">
<span class="material-symbols-outlined form-icon">call</span>
<label for="input_0279a427" class="sr-only">Phone Number</label>
<input id="input_0279a427" class="form-input" placeholder="Phone Number" type="tel" required aria-required="true"/>
</div>
<div class="form-group">
<span class="material-symbols-outlined form-icon">mail</span>
<label for="input_23cce6f8" class="sr-only">Email Address</label>
<input id="input_23cce6f8" class="form-input" placeholder="Email Address" type="email" required aria-required="true"/>
</div>
</div>

<div class="form-grid">
<div class="form-group">
<span class="material-symbols-outlined form-icon">calendar_today</span>
<label for="input_7ada7880" class="sr-only">Travel Date</label>
<input id="input_7ada7880" class="form-input" placeholder="Travel Date" type="text" aria-required="true"/>
</div>
<div class="form-group">
<span class="material-symbols-outlined form-icon">group</span>
<label for="guests_select" class="sr-only">Number of Guests</label>
<select id="guests_select" class="form-select appearance-none" aria-required="true">
<option disabled="" selected="">Number of Guests</option>
<option>1 Guest</option>
<option>2 Guests</option>
<option>3-5 Guests</option>
<option>5+ Guests</option>
</select>
</div>
</div>

<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
<div class="recaptcha-wrap">
    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
</div>
<?php endif; ?>

<button class="form-submit-btn uppercase tracking-widest" type="submit">Submit Enquiry</button>
</form>
<p class="enquiry-footer-text tracking-widest">A TRAVEL SPECIALIST WILL CONTACT YOU WITHIN 24 HOURS</p>
</div>
</div>
</section>
</div>

<!-- Full-Screen Cinematic Modal -->
<div id="story-modal" class="story-modal" role="dialog" aria-modal="true" aria-hidden="true">
    <!-- Backdrop -->
    <div class="story-modal-backdrop"></div>
    <!-- Modal Content -->
    <div class="story-modal-content" id="story-modal-content">
        <!-- Header / Close Button -->
        <div class="story-modal-header">
            <span class="story-modal-subheading">THE NARRATIVE</span>
            <button id="close-modal-btn" data-action="close-story-modal" class="story-modal-close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <!-- Scrollable Body -->
        <div class="story-modal-body custom-scrollbar">
            <!-- Hero Image for Modal -->
            <div class="story-modal-hero">
                <?php 
                    $modal_hero = $destination['cover_image'] ?: 'images/placeholder.jpg';
                ?>
                <img src="<?php echo htmlspecialchars($modal_hero); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                <div class="story-modal-hero-gradient"></div>
            </div>
            <!-- Modal Editorial Content -->
            <div class="story-modal-editorial">
                <h2 class="story-modal-title"><?php echo htmlspecialchars($destination['name']); ?> &mdash; The Story</h2>
                <div class="story-modal-text">
                    <p class="story-modal-lead">
                        <?php echo htmlspecialchars($destination['name']); ?> is a sanctuary where time moves at the pace of spinning prayer wheels and drifting clouds.
                    </p>
                    <p>
                        Beyond the mist-drenched valleys, <?php echo htmlspecialchars($destination['name']); ?> offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.
                    </p>
                    <div class="story-modal-gallery">
                        <?php 
                            $img2 = $destination['story_narrative_image_2'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg');
                            $img3 = $destination['story_narrative_image_3'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg');
                        ?>
                        <img src="<?php echo htmlspecialchars($img2); ?>" class="story-modal-img" alt="<?php echo htmlspecialchars($destination['name']); ?> gallery image 1" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                        <img src="<?php echo htmlspecialchars($img3); ?>" class="story-modal-img" alt="<?php echo htmlspecialchars($destination['name']); ?> gallery image 2" onerror="this.onerror=null; this.src='assets/img/pkg.jpg';">
                    </div>
                    <p>
                        Every element of your stay&mdash;from the thread count of your linens to the vintage of your evening wine&mdash;is selected to harmonize with the raw, untamed beauty outside your window. Here, luxury is defined not just by opulence, but by exclusive access to authentic, transformative experiences.
                    </p>
                </div>
                
                <div class="story-modal-footer">
                    <button id="close-modal-bottom" data-action="close-story-modal" class="story-modal-return">RETURN TO DESTINATION</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
