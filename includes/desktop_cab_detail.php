<?php
// includes/desktop_cab_detail.php
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/cab-detail.css">
<link rel="stylesheet" href="css/cabs.css">

<!-- Hidden Rates Store -->
<div id="desktop-cab-rates-data-store" hidden data-rates="<?php echo htmlspecialchars(json_encode($all_vehicle_rates), ENT_QUOTES, 'UTF-8'); ?>"></div>

<!-- Top Search Bar Container (Pre-filled from GET parameters) -->
<div class="search-wrapper">
    <div class="search-tabs">
        <div class="search-tab <?php echo $s_type === 'oneway' ? 'is-active' : ''; ?>" data-target="oneway">Oneway/Airport Transfer</div>
        <div class="search-tab <?php echo $s_type === 'hourly' ? 'is-active' : ''; ?>" data-target="hourly">Hourly Car Rental</div>
        <div class="search-tab <?php echo $s_type === 'itinerary' ? 'is-active' : ''; ?>" data-target="itinerary">Itinerary Wise</div>
    </div>

    <!-- Oneway Form -->
    <div class="tab-pane <?php echo $s_type === 'oneway' ? 'is-active' : ''; ?>" id="desktop-tab-oneway">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="oneway" id="desktop-form-oneway">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="oneway">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label for="desktop-oneway-pickup">From (Pick-up)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-oneway-pickup" class="search-input" name="pickup_location" value="<?php echo htmlspecialchars($s_pickup); ?>" placeholder="Enter Airport or City" required>
                    </div>
                </div>
                <div class="swap-icon" data-action="swap-locations" aria-label="Swap pickup and drop locations">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L9 20 9 15M21 3l-7 7M3 21l7-7"/></svg>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-drop">To (Drop-off)</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-oneway-drop" class="search-input" name="drop_location" value="<?php echo htmlspecialchars($s_drop); ?>" placeholder="Destination or Hotel" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-date">Pick-up Date & Time</label>
                    <div class="cabs-datetime-row">
                        <div class="input-with-icon cabs-datetime-group">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" id="desktop-oneway-date" class="search-input" name="travel_date" min="<?= $today_date ?>" value="<?= htmlspecialchars($s_date) ?>" required>
                        </div>
                        <div class="input-with-icon cabs-datetime-group">
                            <label for="desktop-oneway-time" class="sr-only">Pick-up Time</label>
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" id="desktop-oneway-time" class="search-input" name="travel_time" value="<?php echo htmlspecialchars($s_time); ?>">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-oneway-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-oneway-type" class="search-input" name="cab_type">
                            <option value="" disabled <?php echo empty($s_cab_type) ? 'selected' : ''; ?>>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo ($s_cab_type === $cc['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
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
    <div class="tab-pane <?php echo $s_type === 'hourly' ? 'is-active' : ''; ?>" id="desktop-tab-hourly">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="hourly" id="desktop-form-hourly">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="hourly">
            <div class="search-form-row">
                <div class="search-form-group">
                    <label for="desktop-hourly-pickup">Pick-up Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-hourly-pickup" class="search-input" name="pickup_location" value="<?php echo htmlspecialchars($s_pickup); ?>" placeholder="Select pick-up location, hotel, etc." required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-date">Pick-up Date & Time</label>
                    <div class="cabs-datetime-row">
                        <div class="input-with-icon cabs-datetime-group">
                            <span class="material-symbols-outlined">calendar_month</span>
                            <input type="date" id="desktop-hourly-date" class="search-input" name="travel_date" min="<?= $today_date ?>" value="<?= htmlspecialchars($s_date) ?>" required>
                        </div>
                        <div class="input-with-icon cabs-datetime-group">
                            <label for="desktop-hourly-time" class="sr-only">Pick-up Time</label>
                            <span class="material-symbols-outlined">schedule</span>
                            <input type="time" id="desktop-hourly-time" class="search-input" name="travel_time" value="<?php echo htmlspecialchars($s_time); ?>">
                        </div>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-duration">Rent For</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">timelapse</span>
                        <select id="desktop-hourly-duration" class="search-input" name="duration" required>
                            <option value="" disabled <?php echo empty($s_duration) ? 'selected' : ''; ?>>Select duration</option>
                            <option value="4 Hrs / 40 Kms" <?php echo $s_duration === '4 Hrs / 40 Kms' ? 'selected' : ''; ?>>4 Hrs / 40 Kms</option>
                            <option value="8 Hrs / 80 Kms" <?php echo $s_duration === '8 Hrs / 80 Kms' ? 'selected' : ''; ?>>8 Hrs / 80 Kms</option>
                            <option value="12 Hrs / 120 Kms" <?php echo $s_duration === '12 Hrs / 120 Kms' ? 'selected' : ''; ?>>12 Hrs / 120 Kms</option>
                            <option value="24 Hrs (Full Day)" <?php echo $s_duration === '24 Hrs (Full Day)' ? 'selected' : ''; ?>>24 Hrs (Full Day)</option>
                        </select>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-hourly-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-hourly-type" class="search-input" name="cab_type">
                            <option value="" disabled <?php echo empty($s_cab_type) ? 'selected' : ''; ?>>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo ($s_cab_type === $cc['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
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
    <div class="tab-pane <?php echo $s_type === 'itinerary' ? 'is-active' : ''; ?>" id="desktop-tab-itinerary">
        <form action="cab-detail.php" method="GET" class="cab-search-form" data-type="itinerary" id="desktop-form-itinerary">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="type" value="itinerary">
            <div class="search-form-row cabs-itinerary-top">
                <div class="search-form-group">
                    <label for="desktop-itinerary-pickup">Start Location</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">location_on</span>
                        <input type="text" id="desktop-itinerary-pickup" class="search-input" name="pickup_location" value="<?php echo htmlspecialchars($s_pickup); ?>" placeholder="e.g. Bagdogra Airport" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-start">Start Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <input type="date" id="desktop-itinerary-start" class="search-input" name="travel_date" min="<?= $today_date ?>" value="<?= htmlspecialchars($s_date) ?>" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-end">End Date</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">event</span>
                        <input type="date" id="desktop-itinerary-end" class="search-input" name="return_date" min="<?= $s_date ?: $today_date ?>" value="<?= htmlspecialchars($s_drop_date) ?>" required>
                    </div>
                </div>
                <div class="search-form-group">
                    <label for="desktop-itinerary-type">Vehicle Type</label>
                    <div class="input-with-icon">
                        <span class="material-symbols-outlined">directions_car</span>
                        <select id="desktop-itinerary-type" class="search-input" name="cab_type">
                            <option value="" disabled <?php echo empty($s_cab_type) ? 'selected' : ''; ?>>Select cab class</option>
                            <?php if (!empty($cab_classes)): ?>
                                <?php foreach ($cab_classes as $cc): ?>
                                    <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo ($s_cab_type === $cc['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
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
                        <input type="text" id="desktop-itinerary-details" class="search-input" name="itinerary_details" value="<?php echo htmlspecialchars($s_itinerary); ?>" placeholder="e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-search cabs-search-btn">REQUEST QUOTE</button>
        </form>
    </div>
</div>

<div class="detail-container <?php echo $is_search ? '' : 'pre-search-state'; ?>" id="desktop-cabDetailContainer">
    <div class="main-column">
        <!-- Class Header -->
        <?php if (!$is_search && isset($cab_class) && $cab_class): ?>
        <div class="cab-header-card">
            <h1 class="detail-title"><?php echo htmlspecialchars($cab_class['name']); ?></h1>
            <p class="detail-desc"><?php echo nl2br(htmlspecialchars($cab_class['description'])); ?></p>
            <?php $hero_img = preg_match('/^https?:\/\//i', $cab_class['image']) ? $cab_class['image'] : ltrim($cab_class['image'], '/'); ?>
            <img src="<?php echo htmlspecialchars($hero_img); ?>" alt="Hero" class="cab-hero-img">
            <div style="color: var(--gold); font-size: 1.2rem; font-weight: 600;">
                Starting from &#8377;<?php echo number_format($cab_class['starting_price']); ?> / day
            </div>
        </div>
        <?php endif; ?>

        <!-- Vehicles List -->
        <?php if (empty($vehicles)): ?>
            <p style="color: rgba(255,255,255,0.6);">No specific vehicles listed under this class yet.</p>
        <?php endif; ?>

        <?php foreach ($vehicles as $v): ?>
        <div class="room-category" data-vid="<?php echo $v['id']; ?>" data-baseprice="<?php echo $v['price_per_day']; ?>" id="desktop-vehicle-card-<?php echo $v['id']; ?>">
            <!-- Left: Image & Features -->
            <div class="room-gallery-left">
                <?php $v_img = preg_match('/^https?:\/\//i', $v['image']) ? $v['image'] : ltrim($v['image'], '/'); ?>
                <img src="<?php echo htmlspecialchars($v_img); ?>" alt="Vehicle Image" class="room-main-img">
                <div class="room-features">
                    <span>&#128101; <?php echo (int)$v['pax_capacity']; ?> Seats</span>
                    <span>&#128092; <?php echo (int)$v['luggage_capacity']; ?> Bags</span>
                    <span>&#10052; <?php echo htmlspecialchars($v['ac_type']); ?></span>
                </div>
            </div>
            
            <!-- Right: Details & Pricing -->
            <div class="room-plans-right">
                <div class="plan-row">
                    <div class="plan-details">
                        <h4><?php echo htmlspecialchars($v['name']); ?></h4>
                        <div class="plan-occupancy">Perfect for local city tours, outstation trips, and airport transfers.</div>
                        
                        <div class="emt-fields">
                            <div class="emt-field">
                                <strong>Fuel Type:</strong>
                                <span><?php echo htmlspecialchars($v['fuel_type'] ?? 'Petrol'); ?></span>
                            </div>
                            <div class="emt-field">
                                <strong>Service Options:</strong>
                                <span>Disposal, Point to Point</span>
                            </div>
                            <div class="emt-field">
                                <strong>Cancellation Policy:</strong>
                                <span><?php echo htmlspecialchars($v['cancellation_policy'] ?? 'Free before 6 hours from journey'); ?></span>
                            </div>
                            <div class="emt-field">
                                <strong>Part Payment:</strong>
                                <span><?php echo htmlspecialchars($v['part_payment'] ?? 'Pay 25% now and rest to driver'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="plan-pricing">
                        <div class="price-final">&#8377;<?php echo number_format($v['price_per_day']); ?> <span style="font-size:0.9rem;color:rgba(255,255,255,0.5);">/ day</span></div>
                        <button type="button" class="btn-select vehicle-action-btn" data-action="select-vehicle" data-id="<?php echo $v['id']; ?>" data-name="<?php echo htmlspecialchars($v['name'], ENT_QUOTES); ?>" data-price="<?php echo $v['price_per_day']; ?>">
                            <?php echo $is_search ? 'SELECT' : 'ENQUIRY'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Right Sidebar -->
    <div class="price-sidebar">
        <div class="price-widget">
            <div class="price-widget-header">Price Details</div>
            <div class="price-widget-body" id="desktop-sidebar-cart-content">
                <div class="empty-cart-msg">Select a vehicle to view details.</div>
            </div>
        </div>
    </div>
</div>

<!-- Single Clean Checkout Modal -->
<div id="desktop-checkoutModal" class="modal-overlay">
    <div class="modal-content">
        <!-- Left: Form -->
        <div class="modal-left">
            <div class="modal-header">
                <h2>Guest Details</h2>
                <div class="close-modal" data-action="close-checkout">&times;</div>
            </div>
            
            <form id="desktop-checkoutForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="vehicle_id" id="desktop-formVehicleId">
                <input type="hidden" name="vehicle_name" id="desktop-formVehicleName">
                <input type="hidden" name="service_type" id="desktop-formServiceType">
                <input type="hidden" name="final_price" id="desktop-formFinalPrice">
                <input type="hidden" name="pickup_location" id="desktop-formPickup">
                <input type="hidden" name="drop_location" id="desktop-formDrop">
                <input type="hidden" name="travel_date" id="desktop-formDate">
                <input type="hidden" name="travel_time" id="desktop-formTime">
                <input type="hidden" name="trip_type" id="desktop-formTripType">
                <input type="hidden" name="duration" id="desktop-formDuration">
                <input type="hidden" name="search_itinerary_details" id="desktop-formSearchItinerary">
                
                <div class="form-section">
                    <h3>Guest Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="desktop-guest-fname">First Name *</label>
                            <input type="text" id="desktop-guest-fname" name="guest_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="desktop-guest-lname">Last Name *</label>
                            <input type="text" id="desktop-guest-lname" name="guest_last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="desktop-guest-special">Any special request (optional)</label>
                        <input type="text" id="desktop-guest-special" name="special_request" class="form-control">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Contact Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="desktop-guest-phone">Mobile number *</label>
                            <div style="display: flex;">
                                <input type="text" value="+91" class="form-control" style="width: 60px; border-right: none; border-radius: 4px 0 0 4px; background: rgba(0,0,0,0.5); color: rgba(255,255,255,0.5);" readonly>
                                <input type="tel" id="desktop-guest-phone" name="phone" class="form-control" style="border-radius: 0 4px 4px 0;" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="desktop-guest-email">Email *</label>
                            <input type="email" id="desktop-guest-email" name="email" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-proceed" id="desktop-btnSubmitModal">
                    Submit Inquiry
                </button>
                <div id="desktop-modalFormMsg" class="form-feedback-msg"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="modal-right">
            <h3 style="margin-top: 0; color: var(--gold);">Enquiry Summary</h3>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div id="desktop-modalSummaryVehicle" style="font-weight: 700; color: #fff; margin-bottom: 5px;"></div>
                <div id="desktop-modalSummaryService" style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 20px;"></div>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="desktop-modalLabelPickup">From:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="desktop-modalPickup"></strong>
                </div>
                <div id="desktop-modalDropContainer" style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="desktop-modalLabelDrop">To:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="desktop-modalDrop"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; color: rgba(255,255,255,0.8);">
                    <span>Date:</span>
                    <strong style="color: #fff;" id="desktop-modalDate"></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: #fff;">
                    <span>Total Estimated:</span>
                    <span id="desktop-modalSummaryTotal" style="color: var(--gold);"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/modules/cabs.js" defer></script>
<script src="js/modules/cab-detail.js" defer></script>

<?php include "../includes/footer.php"; ?>
