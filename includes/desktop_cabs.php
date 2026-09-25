<?php
// includes/desktop_cabs.php
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/cabs.css">

<section class="cabs-hero">
    <h1>Ride Anywhere, Anytime</h1>
    <p>From airport pickups to city-to-city travel and flexible hourly rentals, enjoy safe, convenient, and hassle-free cab services.</p>
</section>

<!-- Search Bar -->
<div class="search-wrapper">
    <div class="search-tabs">
        <div class="search-tab is-active" data-target="oneway">Oneway/Airport Transfer</div>
        <div class="search-tab" data-target="hourly">Hourly Car Rental</div>
        <div class="search-tab" data-target="itinerary">Itinerary Wise</div>
    </div>

    <!-- Oneway Form -->
    <div class="tab-pane is-active" id="desktop-tab-oneway">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="oneway" id="desktop-form-oneway">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="oneway">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label for="desktop-oneway-pickup">From (Pick-up)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-oneway-pickup" class="search-input" name="pickup_location" placeholder="Enter Airport or City" required aria-required="true">
                    </div>
                </div>
                <div class="swap-icon" data-action="swap-locations" aria-label="Swap pickup and drop locations">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L9 20 9 15M21 3l-7 7M3 21l7-7"/></svg>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-drop">To (Drop-off)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-oneway-drop" class="search-input" name="drop_location" placeholder="Destination or Hotel" required aria-required="true">
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-date">Pick-up Date & Time</label>
                    <div class="cabs-datetime-row">
                        <div class="input-with-icon cabs-datetime-group">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" id="desktop-oneway-date" class="search-input" name="travel_date" min="<?= $today_date ?>" required aria-required="true">
                        </div>
                        <div class="input-with-icon cabs-datetime-group">
                            <label for="desktop-oneway-time" class="sr-only">Pick-up Time</label>
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" id="desktop-oneway-time" class="search-input" name="travel_time">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-oneway-type" class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-search cabs-search-btn">SEARCH</button>
        </form>
    </div>

    <!-- Hourly Form -->
    <div class="tab-pane" id="desktop-tab-hourly">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="hourly" id="desktop-form-hourly">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="hourly">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label for="desktop-hourly-pickup">Pick-up Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-hourly-pickup" class="search-input" name="pickup_location" placeholder="Select pick-up location, hotel, etc." required aria-required="true">
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-date">Pick-up Date & Time</label>
                    <div class="cabs-datetime-row">
                        <div class="input-with-icon cabs-datetime-group">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" id="desktop-hourly-date" class="search-input" name="travel_date" min="<?= $today_date ?>" required aria-required="true">
                        </div>
                        <div class="input-with-icon cabs-datetime-group">
                            <label for="desktop-hourly-time" class="sr-only">Pick-up Time</label>
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" id="desktop-hourly-time" class="search-input" name="travel_time">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-duration">Rent For</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">timelapse</span>
                        <select id="desktop-hourly-duration" class="search-input" name="duration" required aria-required="true">
                            <option value="" disabled selected>Select duration</option>
                            <?php if (!empty($cab_durations)): ?>
                                <?php foreach ($cab_durations as $d): ?>
                                    <option value="<?php echo (int)$d['hours']; ?>"><?php echo htmlspecialchars($d['label']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-hourly-type" class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-search cabs-search-btn">SEARCH</button>
        </form>
    </div>

    <!-- Itinerary Wise Form -->
    <div class="tab-pane" id="desktop-tab-itinerary">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="itinerary" id="desktop-form-itinerary">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="itinerary">
            <div class="search-form-row cabs-itinerary-top">
                <div class="search-form-group">
                    <label for="desktop-itinerary-pickup">Start Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-itinerary-pickup" class="search-input" name="pickup_location" placeholder="e.g. Bagdogra Airport" required aria-required="true">
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-start">Start Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <input type="date" class="search-input" name="travel_date" id="desktop-itinerary-start" min="<?= $today_date ?>" required aria-required="true">
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-end">End Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">event</span>
                        <input type="date" class="search-input" name="return_date" id="desktop-itinerary-end" min="<?= $today_date ?>" required aria-required="true">
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-itinerary-type" class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="search-form-row">
                <div class="search-form-group cabs-itinerary-route">
                    <label for="desktop-itinerary-details">Itinerary Plan / Route Details</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">route</span>
                        <input type="text" id="desktop-itinerary-details" class="search-input" name="itinerary_details" placeholder="e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP" required aria-required="true">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-search cabs-search-btn">REQUEST QUOTE</button>
        </form>
    </div>
</div>

<!-- Fleet Showcase -->
<div class="fleet-section">
    <div class="section-title">
        <h2>Flexible Hourly Car Rentals & Outstation Cabs</h2>
        <p>Rent a car for convenient and affordable travel across major destinations. Enjoy the freedom to explore at your own pace.</p>
    </div>
    
    <div class="fleet-grid">
        <?php if (!empty($cab_classes)): ?>
            <?php foreach ($cab_classes as $cc): ?>
            <a href="cab-detail.php?id=<?php echo $cc['id']; ?>" class="fleet-card-link">
                <div class="fleet-card">
                    <?php $img_path = !empty($cc['image']) ? (preg_match('/^https?:\/\//i', $cc['image']) ? $cc['image'] : ltrim($cc['image'], '/')) : 'assets/img/pkg.jpg'; ?>
                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($cc['name']); ?>" class="fleet-img" onerror="if(!this.dataset.fallbackDone){this.dataset.fallbackDone='1';this.src='assets/img/pkg.jpg';}">
                    <div class="fleet-info">
                        <h3><?php echo htmlspecialchars($cc['name']); ?></h3>
                        <div class="meta"><?php echo htmlspecialchars($cc['description']); ?></div>
                        <div class="fleet-price">
                            <?php if ($cc['min_rate'] !== null): ?>
                            <div class="price-tag">&#8377;<?php echo number_format($cc['min_rate']); ?> <span>/ day onwards</span></div>
                            <?php else: ?>
                            <div class="price-tag"><span>Contact for pricing</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="cabs-empty-msg">No cab classes available at the moment.</p>
        <?php endif; ?>
    </div>
</div>
  
<!-- Popular Destinations -->
<div class="destinations-section">
    <div class="section-title">
        <h2>Popular Cab Routes</h2>
        <p>Book reliable cab services across top destinations.</p>
    </div>
    <div class="dest-grid">
        <div class="dest-card is-placeholder">
            <img src="assets/img/pkg.jpg" alt="Bagdogra to Darjeeling Route">
            <div class="dest-overlay"><h4>Bagdogra &rarr; Darjeeling</h4></div>
        </div>
        <div class="dest-card is-placeholder">
            <img src="assets/img/pkg.jpg" alt="NJP to Gangtok Route">
            <div class="dest-overlay"><h4>NJP &rarr; Gangtok</h4></div>
        </div>
        <div class="dest-card is-placeholder">
            <img src="assets/img/pkg.jpg" alt="Darjeeling to Pelling Route">
            <div class="dest-overlay"><h4>Darjeeling &rarr; Pelling</h4></div>
        </div>
        <div class="dest-card is-placeholder">
            <img src="assets/img/pkg.jpg" alt="Gangtok to Kalimpong Route">
            <div class="dest-overlay"><h4>Gangtok &rarr; Kalimpong</h4></div>
        </div>
    </div>
</div>

<!-- Benefits -->
<div class="benefits-section">
    <div class="benefits-container">
        <div class="benefit-item">
            <div class="icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h4>100% Safe & Secure</h4>
            <p>Sanitized vehicles and verified professional drivers.</p>
        </div>
        <div class="benefit-item">
            <div class="icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <h4>On-Time Guarantee</h4>
            <p>Punctual pickups and drop-offs to ensure you never miss a flight.</p>
        </div>
        <div class="benefit-item">
            <div class="icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <h4>Transparent Pricing</h4>
            <p>No hidden charges. Clear and upfront pricing for all routes.</p>
        </div>
        <div class="benefit-item">
            <div class="icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </div>
            <h4>24/7 Support</h4>
            <p>Dedicated customer support to assist you during your entire journey.</p>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="cabs-modal-overlay" id="desktop-contactModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="cabs-modal-content">
        <span class="cabs-modal-close" data-action="close-modal" data-target="#desktop-contactModal">&times;</span>
        <h3 class="modal-title">Almost there!</h3>
        <p class="modal-subtitle">Where should we send your booking confirmation and details?</p>
        
        <form id="desktop-finalCabSubmitForm">
            <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <div class="search-form-group cabs-modal-field">
                <label for="desktop-cab-guest-name">Guest Name *</label>
                <input type="text" id="desktop-cab-guest-name" class="search-input" name="name" required aria-required="true" placeholder="Enter your full name">
            </div>
            <div class="search-form-group cabs-modal-field">
                <label for="desktop-cab-guest-phone">Phone Number *</label>
                <input type="tel" id="desktop-cab-guest-phone" class="search-input" name="phone" required aria-required="true" placeholder="+91 xxxxx xxxxx">
            </div>
            <div class="search-form-group cabs-modal-field-lg">
                <label for="desktop-cab-guest-email">Email Address</label>
                <input type="email" id="desktop-cab-guest-email" class="search-input" name="email" placeholder="Optional but recommended">
            </div>
            
            <button type="submit" class="btn-search cabs-btn-full" id="desktop-modalSubmitBtn">CONFIRM & SUBMIT</button>
            <div id="desktop-modalMsg" class="form-feedback-msg"></div>
        </form>
    </div>
</div>

<script src="js/modules/cabs.js" defer></script>

<?php include "../includes/footer.php"; ?>
