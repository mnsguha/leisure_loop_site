<?php
if (!$pdo) {
    die("Database connection failed.");
}

// Variables are already set by public/package-detail.php
// $pkg, $itinerary, $highlights_arr, $inclusions_arr, $exclusions_arr, $photos_arr, $destinations_list

$page_title = htmlspecialchars($pkg['title']) . " | Leisure Loop";
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- App Header -->
    <div class="app-header" id="appHeader">
        <a aria-label="Link" href="packages.php" class="header-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <button class="header-btn" data-action="share-package" data-title="<?php echo htmlspecialchars($pkg['title'] ?? 'Leisure Loop Package'); ?>">
            <span class="material-symbols-outlined" style="font-size: 20px;">share</span>
        </button>
    </div>

    <!-- Hero Carousel -->
    <div style="position: relative;">
        <div class="hero-carousel" id="heroCarousel">
            <?php foreach ($photos_arr as $photo): ?>
            <div class="hero-slide">
                <img src="<?php echo htmlspecialchars($photo); ?>" class="hero-img" alt="">
                <div class="hero-overlay"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="hero-dots" id="heroDots">
            <?php foreach ($photos_arr as $idx => $photo): ?>
            <div class="dot <?php echo $idx === 0 ? 'active' : ''; ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Overview Section -->
    <div class="section">
        <div class="badges-row">
            <span class="badge"><?php echo htmlspecialchars($pkg['days']); ?>N / <?php echo htmlspecialchars($pkg['days']+1); ?>D</span>
            <span class="badge"><?php echo htmlspecialchars($tour_type); ?></span>
        </div>
        <h1 class="pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h1>
        
        <div class="stats-row">
            <div class="rating">
                <span class="material-symbols-outlined star-icon">star</span>
                <?php echo $rating_score; ?>/5 <span style="font-weight:400; color: rgba(255,255,255,0.5);"> (<?php echo $rating_count; ?> reviews)</span>
            </div>
            <div>Code: <span style="color:#fff;"><?php echo $tour_code; ?></span></div>
        </div>
    </div>

    <!-- Highlights Scroll -->
    <div class="highlights-scroll">
        <?php 
        $icons = ['verified', 'hotel_class', 'directions_car', 'explore'];
        foreach ($highlights_arr as $i => $highlight): 
            $icon = $icons[$i % count($icons)];
        ?>
        <div class="highlight-chip">
            <span class="material-symbols-outlined highlight-icon"><?php echo $icon; ?></span>
            <span class="highlight-text"><?php echo htmlspecialchars($highlight); ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Itinerary Accordion -->
    <?php if (!empty($itinerary)): ?>
    <div class="section">
        <h2 class="section-title">Daily Itinerary</h2>
        <div class="accordion">
            <?php foreach ($itinerary as $i => $day): 
                $day_num = $day['day'] ?? ($i + 1);
            ?>
            <div class="accordion-item <?php echo $i == 0 ? 'active' : ''; ?>">
                <div class="accordion-header" data-action="toggle-accordion">
                    <div>
                        <span class="accordion-day">DAY <?php echo $day_num; ?></span>
                        <span><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                    </div>
                    <span class="material-symbols-outlined accordion-icon">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content" style="<?php echo $i == 0 ? 'max-height: 1000px;' : ''; ?>">
                    <div class="accordion-inner">
                        <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fixed Departure Details (Logistics & Stay) -->
    <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
    <div class="section">
        <h2 class="section-title" style="color: #4ade80;">Logistics & Stay</h2>
        
        <?php if (!empty($pkg['hotel_details'])): ?>
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="color: #60a5fa;">hotel</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Accommodations</h4>
            </div>
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5;">
                <?php echo nl2br($pkg['hotel_details']); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($pkg['vehicle_details'])): ?>
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="color: #c084fc;">directions_car</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Transportation</h4>
            </div>
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5;">
                <?php echo nl2br($pkg['vehicle_details']); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($pkg['meal_plan_details'])): ?>
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <span class="material-symbols-outlined" style="color: #fb923c;">restaurant</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Meal Plan</h4>
            </div>
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5;">
                <?php echo nl2br($pkg['meal_plan_details']); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Bespoke Stay & Fleet Options -->
    <div class="section">
        <h2 class="section-title" style="margin-bottom: 4px;">Choose Hotel Category</h2>
        <p style="font-size:0.75rem; color:rgba(255,255,255,0.6); margin: 0 0 12px 0;">Swipe & select your preferred luxury accommodation tier.</p>
        <div class="m-bespoke-scroll">
            <?php foreach ($hotel_categories as $idx => $stay): 
                $is_sel = !empty($stay['recommended']);
            ?>
            <div class="m-bespoke-card <?php echo $is_sel ? 'selected' : ''; ?>" id="m-stay-card-<?php echo $idx; ?>" <?php echo htmlspecialchars($stay['stars'] . ' ' . explode(' ', $stay['title'])[0]); ?>')">
                <img src="<?php echo htmlspecialchars($stay['img']); ?>" class="m-bespoke-img" alt="<?php echo htmlspecialchars($stay['title']); ?>">
                <span class="m-badge"><?php echo $stay['stars_display']; ?></span>
                <div class="m-bespoke-body">
                    <div>
                        <span style="font-size:0.65rem; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:0.05em;"><?php echo htmlspecialchars($stay['tagline']); ?></span>
                        <h3 class="m-bespoke-title"><?php echo htmlspecialchars($stay['title']); ?></h3>
                        <p style="font-size:0.75rem; color:rgba(255,255,255,0.7); margin:0 0 10px 0; line-height:1.4;"><?php echo htmlspecialchars($stay['desc']); ?></p>
                    </div>
                    <div>
                        <div style="border-top:1px solid rgba(255,255,255,0.05); padding-top:8px; margin-top:6px; display:grid; grid-template-columns:1fr 1fr; gap:4px;">
                            <?php foreach ($stay['features'] as $feat): ?>
                            <span style="font-size:0.7rem; color:rgba(255,255,255,0.8); display:flex; align-items:center; gap:4px;">
                                <span class="material-symbols-outlined" style="font-size:14px; color:var(--gold);">check</span>
                                <span><?php echo htmlspecialchars($feat); ?></span>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="m-bespoke-btn" id="m-stay-btn-<?php echo $idx; ?>">
                            <span class="material-symbols-outlined" style="font-size:16px;"><?php echo $is_sel ? 'check_circle' : 'radio_button_unchecked'; ?></span>
                            <span><?php echo $is_sel ? 'Selected Tier' : 'Select Category'; ?></span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section" style="margin-top:24px;">
        <h2 class="section-title" style="margin-bottom: 4px;">Choose Private Cab</h2>
        <p style="font-size:0.75rem; color:rgba(255,255,255,0.6); margin: 0 0 12px 0;">Dedicated mountain-specialist vehicle & chauffeur.</p>
        <div class="m-bespoke-scroll">
            <?php foreach ($cab_types as $idx => $cab): 
                $is_sel = !empty($cab['recommended']);
            ?>
            <div class="m-bespoke-card <?php echo $is_sel ? 'selected' : ''; ?>" id="m-cab-card-<?php echo $idx; ?>" <?php echo htmlspecialchars($cab['name']); ?>')">
                <img src="<?php echo htmlspecialchars($cab['img']); ?>" class="m-bespoke-img" alt="<?php echo htmlspecialchars($cab['name']); ?>">
                <span class="m-badge" style="color:#fff;"><span class="material-symbols-outlined" style="font-size:14px; color:var(--gold); vertical-align:middle;">group</span> <?php echo $cab['seats']; ?></span>
                <div class="m-bespoke-body">
                    <div>
                        <span style="font-size:0.65rem; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:0.05em;"><?php echo htmlspecialchars($cab['category']); ?></span>
                        <h3 class="m-bespoke-title"><?php echo htmlspecialchars($cab['name']); ?></h3>
                        <p style="font-size:0.75rem; color:rgba(255,255,255,0.7); margin:0 0 10px 0; line-height:1.4;"><?php echo htmlspecialchars($cab['desc']); ?></p>
                    </div>
                    <div>
                        <div style="background:rgba(255,255,255,0.05); padding:6px 10px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; margin-bottom:8px;">
                            <span style="color:rgba(255,255,255,0.7);"><span class="material-symbols-outlined" style="font-size:14px; color:var(--gold); vertical-align:middle;">luggage</span> Bag Capacity</span>
                            <strong style="color:#fff;"><?php echo $cab['bags']; ?></strong>
                        </div>
                        <button type="button" class="m-bespoke-btn" id="m-cab-btn-<?php echo $idx; ?>">
                            <span class="material-symbols-outlined" style="font-size:16px;"><?php echo $is_sel ? 'check_circle' : 'radio_button_unchecked'; ?></span>
                            <span><?php echo $is_sel ? 'Selected Cab' : 'Select Cab'; ?></span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Inclusions & Exclusions Accordion -->
    <div class="section">
        <h2 class="section-title">What's Included</h2>
        <div class="accordion">
            <div class="accordion-item active">
                <div class="accordion-header" data-action="toggle-accordion">
                    <span>Inclusions</span>
                    <span class="material-symbols-outlined accordion-icon">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content" style="max-height: 1000px;">
                    <div class="accordion-inner">
                        <ul class="check-list">
                            <?php foreach ($inclusions_arr as $inc): ?>
                            <li><?php echo htmlspecialchars($inc); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" data-action="toggle-accordion">
                    <span>Exclusions</span>
                    <span class="material-symbols-outlined accordion-icon">keyboard_arrow_down</span>
                </div>
                <div class="accordion-content">
                    <div class="accordion-inner">
                        <ul class="cross-list">
                            <?php foreach ($exclusions_arr as $exc): ?>
                            <li><?php echo htmlspecialchars($exc); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="section">
        <h2 class="section-title">Tour Route</h2>
        <div class="map-container" id="mapWrap">
            <div class="map-overlay" id="mapOverlay" data-action="enable-map">
                <div class="map-overlay-text">Tap to interact</div>
            </div>
            <?php
            $mc = $pkg['map_coords'] ?? '';
            $parts = explode(',', $mc);
            $fallback = (count($parts) === 2) ? ((float)trim($parts[0])) . ',' . ((float)trim($parts[1])) : '27.3314,88.6138';
            ?>
            <div id="tourMap" style="width: 100%; height: 100%;" data-itinerary='<?php echo htmlspecialchars(json_encode(array_values($itinerary)), ENT_QUOTES, 'UTF-8'); ?>' data-fallback-coords="<?php echo htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8'); ?>"></div>
        </div>
    </div>

    <div style="height: 140px;"></div> <!-- Spacer for Sticky CTA -->

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <div class="cta-price-col">
            <span class="cta-price-label">Starting From</span>
            <span class="cta-price-val">₹<?php echo number_format($pkg['price'] ?? 0); ?></span>
        </div>
        <button data-action="open-modal" class="btn-primary">Enquire Now</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <h2 class="modal-title">Plan Your Trip</h2>
            <button class="modal-close" data-action="close-modal" aria-label="Close modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="package" value="<?php echo htmlspecialchars($pkg['title']); ?>">
                <input type="hidden" name="source" value="mobile_package_details">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_faa864de" class="sr-only">Your Full Name</label>
<input id="input_faa864de" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_589b14c8" class="sr-only">Phone Number</label>
<input id="input_589b14c8" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_7f6c36ba" class="sr-only">Email Address</label>
<input id="input_7f6c36ba" type="email" name="email" class="form-input" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">calendar_today</span>
                    <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
                        <select name="travel_date" class="form-input form-select" required style="padding-left: 44px; color: #fff; appearance: none; background: transparent; border: none; width: 100%;">
                            <option value="" disabled selected style="color: #000;">Select Departure</option>
                            <?php foreach ($package_departures as $dep): ?>
                                <option value="<?php echo htmlspecialchars($dep['start_date']); ?>" style="color: #000;">
                                    <?php echo date('M d, Y', strtotime($dep['start_date'])); ?> (<?php echo $dep['available_seats']; ?> Seats)
                                </option>
                            <?php endforeach; ?>
                            <?php if (empty($package_departures)): ?>
                                <option value="" disabled style="color: #000;">No Upcoming Departures</option>
                            <?php endif; ?>
                        </select>
                        <span class="material-symbols-outlined select-arrow">keyboard_arrow_down</span>
                    <?php else: ?>
                        
<label for="input_28d06f15" class="sr-only">Travel Date</label>
<input id="input_28d06f15" type="text" name="travel_date" class="form-input" placeholder="Travel Date" data-focus="date">
                    <?php endif; ?>
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
                    <span class="material-symbols-outlined select-arrow">keyboard_arrow_down</span>
                </div>
                
                <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
                <div class="form-group" style="margin-top:1rem;">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>
                
                <!-- Mobile Selection Summary -->
                <input type="hidden" name="preferred_stay" id="m_preferred_stay_input" value="4 Star Luxury">
                <input type="hidden" name="preferred_cab" id="m_preferred_cab_input" value="Innova / Xylo / Scorpio">
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:12px; margin:14px 0;">
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:6px;">
                        <span style="color:rgba(255,255,255,0.6);">Selected Stay Tier:</span>
                        <strong id="m_summary_stay_val" style="color:var(--gold);">4 Star Luxury</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; border-top:1px solid rgba(255,255,255,0.05); padding-top:6px;">
                        <span style="color:rgba(255,255,255,0.6);">Selected Private Cab:</span>
                        <strong id="m_summary_cab_val" style="color:#fff;">Innova / Xylo / Scorpio</strong>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; display: block; margin-top: 8px;">SUBMIT ENQUIRY</button>
                <p style="text-align: center; margin-top: 16px; color: rgba(255,255,255,0.4); font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">A travel specialist will contact you within 24 hours</p>
            </form>
        </div>
    </div>

</body>
</html>
