<?php
if (!$pdo) {
    die("Database connection failed.");
}

// Fetch destination details
try {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE slug = :slug AND is_active = 1");
    $stmt->execute(['slug' => $slug]);
    $destination = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: destinations.php');
    exit;
}

if (!$destination) {
    header('Location: destinations.php');
    exit;
}

// Fetch related packages
$related_packages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE (destination = :name OR title LIKE :like_name) AND is_active = 1 GROUP BY title LIMIT 6");
    $stmt->execute(['name' => $destination['name'], 'like_name' => '%' . $destination['name'] . '%']);
    $related_packages = $stmt->fetchAll();
} catch (PDOException $e) {
    $related_packages = [];
}

$sightseeing = json_decode($destination['sightseeing_json'], true) ?: [];
$local_experiences = json_decode($destination['local_experiences_json'] ?? '[]', true) ?: [];

$page_title = htmlspecialchars($destination['name']) . " | Leisure Loop";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page_title; ?></title>
    <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Hero -->
    <div class="hero">
        <a aria-label="Link" href="destinations.php" class="back-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        
        <?php if (!empty($destination['hero_video_url'])): ?>
            <video autoplay loop muted playsinline class="hero-img" style="filter: brightness(0.8);">
                <source src="<?php echo htmlspecialchars($destination['hero_video_url']); ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <img src="<?php echo htmlspecialchars($destination['cover_image'] ?? ''); ?>" class="hero-img" alt="">
        <?php endif; ?>
        
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <span class="hero-tagline"><?php echo htmlspecialchars($destination['tagline']); ?></span>
            <h1><?php echo htmlspecialchars(strtoupper($destination['name'])); ?></h1>
        </div>
    </div>

    <!-- Quick Facts Grid -->
    <div class="facts-grid">
        <div class="fact-card">
            <div class="fact-label">Altitude</div>
            <div class="fact-value"><?php echo htmlspecialchars(!empty($destination['altitude']) ? $destination['altitude'] : 'Varied'); ?></div>
        </div>
        
        <div class="fact-card">
            <div class="fact-label">Best Time</div>
            <div class="fact-value"><?php echo htmlspecialchars(!empty($destination['best_time']) ? $destination['best_time'] : 'Year Round'); ?></div>
        </div>
        
        <?php if (!empty($destination['difficulty'])): ?>
        <div class="fact-card">
            <div class="fact-label">Difficulty</div>
            <div class="fact-value"><?php echo htmlspecialchars($destination['difficulty']); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($destination['ideal_for'])): ?>
        <div class="fact-card">
            <div class="fact-label">Ideal For</div>
            <div class="fact-value"><?php echo htmlspecialchars($destination['ideal_for']); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- About Section -->
    <div class="section">
        <h2 class="section-title" style="font-style: italic;">The Narrative</h2>
        <div class="section-text">
            <?php 
            $desc_text = !empty($destination['description_long']) ? $destination['description_long'] : (!empty($destination['description']) ? $destination['description'] : '');
            if (empty(trim($desc_text))) {
                $desc_text = 'Discover ' . ($destination['name'] ?? 'this destination') . ' with our exclusive journeys.';
            }
            echo nl2br(htmlspecialchars($desc_text)); 
            ?>
        </div>
    </div>

    <!-- Sightseeing Section -->
    <?php if (!empty($sightseeing)): ?>
    <div class="section">
        <h2 class="section-title">Must Visit</h2>
        <div class="carousel">
            <?php foreach ($sightseeing as $sight): ?>
            <div class="sight-card">
                <?php if (!empty($sight['image'])): ?>
                <img src="<?php echo htmlspecialchars($sight['image']); ?>" class="sight-img" alt="">
                <?php else: ?>
                <div class="sight-img" style="background: #1a1f2e;"></div>
                <?php endif; ?>
                <div class="sight-body">
                    <h3 class="sight-title"><?php echo htmlspecialchars($sight['title'] ?? ''); ?></h3>
                    <div class="sight-desc"><?php echo htmlspecialchars($sight['description'] ?? ''); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Local Experiences Section -->
    <?php if (!empty($local_experiences)): ?>
    <div class="section">
        <div style="text-align: center; margin-bottom: 20px;">
            <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">LOCAL EXPERIENCES</span>
            <h2 class="section-title" style="font-style: italic; margin-bottom: 0; font-size: 1.25rem;">Immersive Encounters</h2>
        </div>
        <div class="carousel">
            <?php foreach ($local_experiences as $exp): ?>
            <div class="exp-card">
                <div class="exp-icon-wrapper">
                    <span class="material-symbols-outlined exp-icon"><?php echo htmlspecialchars($exp['icon'] ?? 'star'); ?></span>
                </div>
                <h3 class="exp-title"><?php echo htmlspecialchars($exp['title'] ?? ''); ?></h3>
                <div class="exp-desc"><?php echo htmlspecialchars($exp['description'] ?? ''); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Related Packages -->
    <?php if (!empty($related_packages)): ?>
    <div class="section">
        <div style="text-align: center; margin-bottom: 20px;">
            <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">FEATURED JOURNEYS</span>
            <h2 class="section-title" style="font-style: italic; margin-bottom: 0; font-size: 1.25rem;">Curated Tours</h2>
        </div>
        <div class="carousel">
            <?php foreach ($related_packages as $pkg): ?>
            <a href="package-details.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" class="pkg-card">
                <div class="pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?? '../images/placeholder-tour.jpg'); ?>');">
                    <div class="pkg-badge"><?php echo htmlspecialchars($pkg['nights']); ?>N / <?php echo htmlspecialchars($pkg['days']); ?>D</div>
                    <div class="pkg-content">
                        <span class="pkg-theme"><?php echo htmlspecialchars($pkg['tour_type'] ?? 'SIKKIM'); ?></span>
                        <h3 class="pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                        <?php if (!empty($pkg['base_price'])): ?>
                        <div class="pkg-price">FROM ₹<?php echo number_format($pkg['base_price']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div style="height: 120px;"></div> <!-- Spacer for Sticky CTA -->

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Enquire Now</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <div>
                <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">BESPOKE TRAVEL</span>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0; font-style: italic;">Plan Your Elite Journey</h2>
            </div>
            <button aria-label="Close" class="modal-close" data-action="close-modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination['name']); ?>">
                <input type="hidden" name="source" value="mobile_destination_details">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_da80ca71" class="sr-only">Your Full Name</label>
<input id="input_da80ca71" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_8c2a9f30" class="sr-only">Phone Number</label>
<input id="input_8c2a9f30" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_5c9c0b4c" class="sr-only">Email Address</label>
<input id="input_5c9c0b4c" type="email" name="email" class="form-input" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">calendar_today</span>
                    
<label for="input_76638892" class="sr-only">Travel Date</label>
<input id="input_76638892" type="text" name="travel_date" class="form-input" placeholder="Travel Date" data-focus="date">
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">group</span>
                    <select name="guests" class="form-input form-select">
                        <option disabled selected>Number of Guests</option>
                        <option>1 Guest</option>
                        <option>2 Guests</option>
                        <option>3-5 Guests</option>
                        <option>5+ Guests</option>
                    </select>
                </div>
                
                <?php 
                global $use_recaptcha, $recaptcha_site_key;
                if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): 
                ?>
                <div class="form-group" style="margin-top:1rem;">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-primary" style="width: 100%; display: block; margin-top: 8px;">SUBMIT ENQUIRY</button>
                <p style="text-align: center; margin-top: 16px; color: rgba(255,255,255,0.4); font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">A travel specialist will contact you within 24 hours</p>
            </form>
        </div>
    </div>
    
</body>
</html>
