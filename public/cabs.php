<?php
$page_title = "Cab Booking | Premium Chauffeur Driven Cars";

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

if ($is_mobile) {
    include '../includes/mobile_cabs.php';
    exit;
}

require_once '../config/db.php';
$cab_classes = [];
if ($pdo) {
    $cab_classes = $pdo->query("SELECT * FROM cab_classes WHERE is_active = 1 ORDER BY starting_price ASC")->fetchAll();
}

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
        <div class="search-tab active" data-target="oneway">Oneway/Airport Transfer</div>
        <div class="search-tab" data-target="hourly">Hourly Car Rental</div>
        <div class="search-tab" data-target="itinerary">Itinerary Wise</div>
    </div>

    <!-- Oneway Form -->
    <div class="tab-pane active" id="tab-oneway">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="oneway">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

              <input type="hidden" name="search" value="1">
              <input type="hidden" name="type" value="oneway">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label>From (Pick-up)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        
<label for="input_b01be82f" class="sr-only">Enter Airport or City</label>
<input id="input_b01be82f" type="text" class="search-input" name="pickup_location" placeholder="Enter Airport or City" required>
                    </div>
                </div>
                <div class="swap-icon" data-action="swap-locations">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L9 20 9 15M21 3l-7 7M3 21l7-7"/></svg>
                </div>
                <div class="search-form-group">
                    <label>To (Drop-off)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        
<label for="input_5fa21de2" class="sr-only">Destination or Hotel</label>
<input id="input_5fa21de2" type="text" class="search-input" name="drop_location" placeholder="Destination or Hotel" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Pick-up Date & Time</label>
                    <div style="display:flex; gap:10px;">
                        <div class="input-with-icon" style="flex:1;">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" class="search-input" name="travel_date" required>
                        </div>
                        <div class="input-with-icon" style="flex:1;">
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" class="search-input" name="travel_time">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php foreach ($cab_classes as $cc): ?>
                                <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <div class="search-form-group" >
                    <button type="submit" class="btn-search" style="width: 100%;">SEARCH</button>
                </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Hourly Form -->
    <div class="tab-pane" id="tab-hourly">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="hourly">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

              <input type="hidden" name="search" value="1">
              <input type="hidden" name="type" value="hourly">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label>Pick-up Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        
<label for="input_bb3281e4" class="sr-only">Select pick-up location, hotel, etc.</label>
<input id="input_bb3281e4" type="text" class="search-input" name="pickup_location" placeholder="Select pick-up location, hotel, etc." required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Pick-up Date & Time</label>
                    <div style="display:flex; gap:10px;">
                        <div class="input-with-icon" style="flex:1;">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" class="search-input" name="travel_date" required>
                        </div>
                        <div class="input-with-icon" style="flex:1;">
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" class="search-input" name="travel_time">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Rent For</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">timelapse</span>
                        <select class="search-input" name="duration" required>
                            <option value="" disabled selected>Select duration</option>
                            <option value="4 Hrs / 40 Kms">4 Hrs / 40 Kms</option>
                            <option value="8 Hrs / 80 Kms">8 Hrs / 80 Kms</option>
                            <option value="12 Hrs / 120 Kms">12 Hrs / 120 Kms</option>
                            <option value="24 Hrs (Full Day)">24 Hrs (Full Day)</option>
                        </select>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php foreach ($cab_classes as $cc): ?>
                                <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <div class="search-form-group" >
                    <button type="submit" class="btn-search" style="width: 100%;">SEARCH</button>
                </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Itinerary Wise Form -->
    <div class="tab-pane" id="tab-itinerary">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="itinerary">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

              <input type="hidden" name="search" value="1">
              <input type="hidden" name="type" value="itinerary">
            <div class="search-form-row" style="margin-bottom: 15px;">
                <div class="search-form-group">
                    <label>Start Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        
<label for="input_9a1bb481" class="sr-only">e.g. Bagdogra Airport</label>
<input id="input_9a1bb481" type="text" class="search-input" name="pickup_location" placeholder="e.g. Bagdogra Airport" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Start Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <input type="date" class="search-input" name="travel_date" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>End Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">event</span>
                        <input type="date" class="search-input" name="drop_location" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select class="search-input" name="cab_type">
                            <option value="" disabled selected>Select cab class</option>
                            <?php foreach ($cab_classes as $cc): ?>
                                <option value="<?php echo htmlspecialchars($cc['name']); ?>"><?php echo htmlspecialchars($cc['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="search-form-row">
                <div class="search-form-group" style="flex: 1;">
                    <label>Itinerary Plan / Route Details</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">route</span>
                        
<label for="input_ed7d501f" class="sr-only">e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP</label>
<input id="input_ed7d501f" type="text" class="search-input" name="itinerary_details" placeholder="e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP" required>
                    </div>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <button type="submit" class="btn-search" style="width: 100%;">REQUEST QUOTE</button>
                </div>
            </div>
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
        <?php foreach ($cab_classes as $cc): ?>
        <a href="cab-detail.php?id=<?php echo $cc['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
            <div class="fleet-card">
                <?php $img_path = preg_match('/^https?:\/\//i', $cc['image']) ? $cc['image'] : ltrim($cc['image'], '/'); ?>
                <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($cc['name']); ?>" class="fleet-img">
                <div class="fleet-info">
                    <h3><?php echo htmlspecialchars($cc['name']); ?></h3>
                    <div class="meta"><?php echo htmlspecialchars($cc['description']); ?></div>
                    <div class="fleet-price">
                        <div class="price-tag">&#8377;<?php echo number_format($cc['starting_price']); ?> <span>/ day onwards</span></div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($cab_classes)): ?>
            <p style="grid-column: 1/-1; text-align: center;">No cab classes available at the moment.</p>
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
          <div class="dest-card">
              <img src="https://images.unsplash.com/photo-1542281286-9e0a16bb7366?auto=format&fit=crop&q=80&w=800" alt="Darjeeling">
              <div class="dest-overlay"><h4>Bagdogra &rarr; Darjeeling</h4></div>
          </div>
          <div class="dest-card">
              <img src="https://images.unsplash.com/photo-1571536802807-3cab88c3a504?auto=format&fit=crop&q=80&w=800" alt="Gangtok">
              <div class="dest-overlay"><h4>NJP &rarr; Gangtok</h4></div>
          </div>
          <div class="dest-card">
              <img src="https://images.unsplash.com/photo-1627894483216-2138af692e32?auto=format&fit=crop&q=80&w=800" alt="Pelling">
              <div class="dest-overlay"><h4>Darjeeling &rarr; Pelling</h4></div>
          </div>
          <div class="dest-card">
              <img src="https://images.unsplash.com/photo-1589136777351-fdc9c9cb1655?auto=format&fit=crop&q=80&w=800" alt="Kalimpong">
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
<div class="cabs-modal-overlay" id="contactModal">
    <div class="cabs-modal-content">
        <span class="cabs-modal-close" data-action="close-modal" data-target="#contactModal">&times;</span>
        <h3 class="modal-title">Almost there!</h3>
        <p class="modal-subtitle">Where should we send your booking confirmation and details?</p>
        
        <form id="finalCabSubmitForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Hidden inputs will be injected here via JS -->
            <div class="search-form-group" style="margin-bottom:20px;">
                <label>Guest Name *</label>
                
<label for="input_b1cc4b36" class="sr-only">Enter your full name</label>
<input id="input_b1cc4b36" type="text" class="search-input" name="name" required placeholder="Enter your full name">
            </div>
            <div class="search-form-group" style="margin-bottom:20px;">
                <label>Phone Number *</label>
                
<label for="input_4730b06e" class="sr-only">+91 xxxxx xxxxx</label>
<input id="input_4730b06e" type="tel" class="search-input" name="phone" required placeholder="+91 xxxxx xxxxx">
            </div>
            <div class="search-form-group" style="margin-bottom:25px;">
                <label>Email Address</label>
                
<label for="input_0077c46f" class="sr-only">Optional but recommended</label>
<input id="input_0077c46f" type="email" class="search-input" name="email" placeholder="Optional but recommended">
            </div>
            
            <button type="submit" class="btn-search" style="width: 100%;" id="modalSubmitBtn">CONFIRM & SUBMIT</button>
            <div id="modalMsg" style="margin-top: 15px; display: none; padding: 12px; border-radius: 8px; font-size: 0.95rem;"></div>
        </form>
    </div>
</div>

<script src="js/modules/cabs.js" defer></script>

<?php include "../includes/footer.php"; ?>
