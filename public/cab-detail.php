<?php
$page_title = "Cab Details | Leisure Loop";
require_once "../config/db.php";

$is_search = isset($_GET['search']) && $_GET['search'] == '1';

if ($is_search) {
    $cab_type = isset($_GET['cab_type']) ? $_GET['cab_type'] : '';
    if (!empty($cab_type)) {
        $stmt = $pdo->prepare("SELECT * FROM cab_classes WHERE name = ? AND is_active=1");
        $stmt->execute([$cab_type]);
        $cab_class = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE cab_class_id = ? AND is_active=1 ORDER BY price_per_day ASC");
        $stmt->execute([$cab_class ? $cab_class['id'] : 0]);
        $vehicles = $stmt->fetchAll();
    } else {
        $cab_class = null;
        $stmt = $pdo->query("SELECT * FROM vehicles WHERE is_active=1 ORDER BY price_per_day ASC");
        $vehicles = $stmt->fetchAll();
    }
    $page_title = "Cab Search Results | Leisure Loop";
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id || !$pdo) { header("Location: cabs.php"); exit; }

    $stmt = $pdo->prepare("SELECT * FROM cab_classes WHERE id = ? AND is_active=1");
    $stmt->execute([$id]);
    $cab_class = $stmt->fetch();
    if (!$cab_class) { header("Location: cabs.php"); exit; }

    $page_title = $cab_class['name'] . " | Leisure Loop";

    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE cab_class_id = ? AND is_active=1 ORDER BY price_per_day ASC");
    $stmt->execute([$id]);
    $vehicles = $stmt->fetchAll();
}

// Fetch all future vehicle rates
$stmt = $pdo->query("SELECT vehicle_id, rate_date, price FROM vehicle_rates WHERE rate_date >= CURDATE()");
$all_vehicle_rates = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_vehicle_rates[$row['vehicle_id']][$row['rate_date']] = (float)$row['price'];
}

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

// Fetch all cab classes for the dropdown
$stmt = $pdo->query("SELECT * FROM cab_classes WHERE is_active=1 ORDER BY id ASC");
$cab_classes = $stmt->fetchAll();



if ($is_mobile) {
    include '../includes/mobile_cabs.php';
    exit;
}

include "../includes/header.php";
?>
<link rel="stylesheet" href="css/cabs.css">


<!-- Top Search Bar Container -->




<div class="search-wrapper">

    <div class="search-tabs">
        <div class="search-tab active" data-target="oneway">Oneway/Airport Transfer</div>
        <div class="search-tab" data-target="hourly">Hourly Car Rental</div>
        <div class="search-tab" data-target="itinerary">Itinerary Wise</div>
    </div>

    <!-- Oneway Form -->
    <div class="tab-pane active" id="tab-oneway">
        <form class="cab-search-form" data-type="oneway">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="search-form-row">
                <div class="search-form-group">
                    <label>From (Pick-up)</label>
                    
<label for="input_32a3797c" class="sr-only">Enter Airport or City</label>
<input id="input_32a3797c" type="text" class="search-input" name="pickup_location" placeholder="Enter Airport or City" required>
                </div>
                <div class="swap-icon" data-action="swap-locations">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L9 20 9 15M21 3l-7 7M3 21l7-7"/></svg>
                </div>
                <div class="search-form-group">
                    <label>To (Drop-off)</label>
                    
<label for="input_91750598" class="sr-only">Destination or Hotel</label>
<input id="input_91750598" type="text" class="search-input" name="drop_location" placeholder="Destination or Hotel" required>
                </div>
                <div class="search-form-group">
                    <label>Pick-up Date & Time</label>
                    <div style="display:flex; gap:10px;">
                        <input type="date" class="search-input" name="travel_date" required>
                        <input type="time" class="search-input" name="travel_time">
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <select class="search-input" name="cab_type">
                        <option value="" disabled <?php echo (!isset($cab_class) || empty($cab_class)) ? 'selected' : ''; ?>>Select cab class</option>
                        <?php foreach ($cab_classes as $cc): ?>
                            <?php $isSelected = (isset($cab_class) && $cab_class['name'] === $cc['name']) ? 'selected' : ''; ?>
                            <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <div class="search-form-group" >
                    <button type="button" class="btn-search" style="width: 100%;" data-action="perform-search">SEARCH</button>
                </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Hourly Form -->
    <div class="tab-pane" id="tab-hourly">
        <form class="cab-search-form" data-type="hourly">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="search-form-row">
                <div class="search-form-group">
                    <label>Pick-up Location</label>
                    
<label for="input_814a32ca" class="sr-only">Select pick-up location, hotel, etc.</label>
<input id="input_814a32ca" type="text" class="search-input" name="pickup_location" placeholder="Select pick-up location, hotel, etc." required>
                </div>
                <div class="search-form-group">
                    <label>Pick-up Date & Time</label>
                    <div style="display:flex; gap:10px;">
                        <input type="date" class="search-input" name="travel_date" required>
                        <input type="time" class="search-input" name="travel_time">
                    </div>
                </div>
                <div class="search-form-group">
                    <label>Rent For</label>
                    <select class="search-input" name="duration" required>
                        <option value="" disabled selected>Select duration</option>
                        <option value="4 Hrs / 40 Kms">4 Hrs / 40 Kms</option>
                        <option value="8 Hrs / 80 Kms">8 Hrs / 80 Kms</option>
                        <option value="12 Hrs / 120 Kms">12 Hrs / 120 Kms</option>
                        <option value="24 Hrs (Full Day)">24 Hrs (Full Day)</option>
                    </select>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <select class="search-input" name="cab_type">
                        <option value="" disabled <?php echo (!isset($cab_class) || empty($cab_class)) ? 'selected' : ''; ?>>Select cab class</option>
                        <?php foreach ($cab_classes as $cc): ?>
                            <?php $isSelected = (isset($cab_class) && $cab_class['name'] === $cc['name']) ? 'selected' : ''; ?>
                            <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <div class="search-form-group" >
                    <button type="button" class="btn-search" style="width: 100%;" data-action="perform-search">SEARCH</button>
                </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Itinerary Wise Form -->
    <div class="tab-pane" id="tab-itinerary">
        <form class="cab-search-form" data-type="itinerary">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="search-form-row" style="margin-bottom: 15px;">
                <div class="search-form-group">
                    <label>Start Location</label>
                    
<label for="input_a7f78eab" class="sr-only">e.g. Bagdogra Airport</label>
<input id="input_a7f78eab" type="text" class="search-input" name="pickup_location" placeholder="e.g. Bagdogra Airport" required>
                </div>
                <div class="search-form-group">
                    <label>Start Date</label>
                    <input type="date" class="search-input" name="travel_date" required>
                </div>
                <div class="search-form-group">
                    <label>End Date</label>
                    <input type="date" class="search-input" name="drop_location" required>
                </div>
                <div class="search-form-group">
                    <label>Vehicle Type</label>
                    <select class="search-input" name="cab_type">
                        <option value="" disabled <?php echo (!isset($cab_class) || empty($cab_class)) ? 'selected' : ''; ?>>Select cab class</option>
                        <?php foreach ($cab_classes as $cc): ?>
                            <?php $isSelected = (isset($cab_class) && $cab_class['name'] === $cc['name']) ? 'selected' : ''; ?>
                            <option value="<?php echo htmlspecialchars($cc['name']); ?>" <?php echo $isSelected; ?>><?php echo htmlspecialchars($cc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="search-form-row">
                <div class="search-form-group" style="flex: 1;">
                    <label>Itinerary Plan / Route Details</label>
                    
<label for="input_920a7419" class="sr-only">e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP</label>
<input id="input_920a7419" type="text" class="search-input" name="itinerary_details" placeholder="e.g. Day 1: Darjeeling, Day 2-3: Gangtok, Day 4: Drop at NJP" required>
                </div>
            </div>
            <div class="search-form-row" style="margin-top: 20px; justify-content: center;">
                <div class="search-form-group" style="flex: 0 0 250px;">
                    <button type="button" class="btn-search" data-action="perform-search" style="width: 100%;">REQUEST QUOTE</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal">
    <div class="modal-content">
        <!-- Left: Form -->
        <div class="modal-left">
            <div class="modal-header">
                <h2>Guest Details</h2>
                <div class="close-modal" data-action="close-checkout">&times;</div>
            </div>
            
            <form id="checkoutForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="vehicle_id" id="formVehicleId">
                <input type="hidden" name="vehicle_name" id="formVehicleName">
                <input type="hidden" name="service_type" id="formServiceType">
                <input type="hidden" name="final_price" id="formFinalPrice">
                <input type="hidden" name="pickup_location" id="formPickup">
                <input type="hidden" name="drop_location" id="formDrop">
                <input type="hidden" name="travel_date" id="formDate">
                <input type="hidden" name="travel_time" id="formTime">
                <input type="hidden" name="trip_type" id="formTripType">
                <input type="hidden" name="duration" id="formDuration">
                <input type="hidden" name="search_itinerary_details" id="formSearchItinerary">
                
                <div class="form-section">
                    <h3>Guest Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="guest_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="guest_last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Any special request (optional)</label>
                        <input type="text" name="special_request" class="form-control">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Contact Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile number *</label>
                            <div style="display: flex;">
                                <input type="text" value="+91" class="form-control" style="width: 60px; border-right: none; border-radius: 4px 0 0 4px; background: rgba(0,0,0,0.5); color: rgba(255,255,255,0.5);" readonly>
                                <input type="tel" name="phone" class="form-control" style="border-radius: 0 4px 4px 0;" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-proceed" id="btnSubmitModal" style="margin-top: 10px;">
                    Submit Inquiry
                </button>
                <div id="modalFormMsg" style="margin-top: 15px; font-weight: 600; text-align: center;"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="modal-right">
            <h3 style="margin-top: 0; color: var(--gold);">Booking Summary</h3>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div id="modalSummaryVehicle" style="font-weight: 700; color: #fff; margin-bottom: 5px;"></div>
                <div id="modalSummaryService" style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 20px;"></div>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="modalLabelPickup">From:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="modalPickup"></strong>
                </div>
                <div id="modalDropContainer" style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="modalLabelDrop">To:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="modalDrop"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; color: rgba(255,255,255,0.8);">
                    <span>Date:</span>
                    <strong style="color: #fff;" id="modalDate"></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: #fff;">
                    <span>Total Estimated:</span>
                    <span id="modalSummaryTotal" style="color: var(--gold);"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/modules/cabs.js" defer></script>

<div class="detail-container <?php echo $is_search ? '' : 'pre-search-state'; ?>" id="cabDetailContainer">
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
        <div class="room-category" data-vid="<?php echo $v['id']; ?>" data-baseprice="<?php echo $v['price_per_day']; ?>" id="vehicle-card-<?php echo $v['id']; ?>">
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
                        
                        <!-- Dynamic EMT style fields -->
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
                                <span><?php echo htmlspecialchars($v['cancellation_policy'] ?? 'Free before 6 hours from the journey time'); ?></span>
                            </div>
                            <div class="emt-field">
                                <strong>Part Payment:</strong>
                                <span><?php echo htmlspecialchars($v['part_payment'] ?? 'Pay 25% now and rest to driver'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="plan-pricing">
                        <div class="price-final">&#8377;<?php echo number_format($v['price_per_day']); ?> <span style="font-size:0.9rem;color:rgba(255,255,255,0.5);">/ day</span></div>
                        <a href="javascript:void(0)" class="btn-select vehicle-action-btn" data-action="eval:handleVehicleAction(this, <?php echo htmlspecialchars(json_encode([
                            'id' => $v['id'],
                            'name' => $v['name'],
                            'price' => $v['price_per_day']
                        ])); ?>)"><?php echo $is_search ? 'SELECT' : 'ENQUIRY'; ?></a>
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
            <div class="price-widget-body" id="sidebar-cart-content">
                <div class="empty-cart-msg">Select a vehicle to view details.</div>
            </div>
        </div>
    </div>
</div>


<!-- Checkout Modal -->
<div id="checkoutModal">
    <div class="modal-content">
        <!-- Left: Form -->
        <div class="modal-left">
            <div class="modal-header">
                <h2>Guest Details</h2>
                <div class="close-modal" data-action="close-checkout">&times;</div>
            </div>
            
            <form id="checkoutForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="vehicle_id" id="formVehicleId">
                <input type="hidden" name="vehicle_name" id="formVehicleName">
                <input type="hidden" name="service_type" id="formServiceType">
                <input type="hidden" name="final_price" id="formFinalPrice">
                <input type="hidden" name="pickup_location" id="formPickup">
                <input type="hidden" name="drop_location" id="formDrop">
                <input type="hidden" name="travel_date" id="formDate">
                <input type="hidden" name="travel_time" id="formTime">
                <input type="hidden" name="trip_type" id="formTripType">
                <input type="hidden" name="duration" id="formDuration">
                <input type="hidden" name="search_itinerary_details" id="formSearchItinerary">
                
                <div class="form-section">
                    <h3>Guest Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="guest_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="guest_last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Any special request (optional)</label>
                        <input type="text" name="special_request" class="form-control">
                    </div>
                </div>

                <div class="form-section">
                    <h3>Contact Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mobile number *</label>
                            <div style="display: flex;">
                                <input type="text" value="+91" class="form-control" style="width: 60px; border-right: none; border-radius: 4px 0 0 4px; background: rgba(0,0,0,0.5); color: rgba(255,255,255,0.5);" readonly>
                                <input type="tel" name="phone" class="form-control" style="border-radius: 0 4px 4px 0;" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-proceed" id="btnSubmitModal" style="margin-top: 10px;">
                    Submit Inquiry
                </button>
                <div id="modalFormMsg" style="margin-top: 15px; font-weight: 600; text-align: center;"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="modal-right">
            <h3 style="margin-top: 0; color: var(--gold);">Booking Summary</h3>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div id="modalSummaryVehicle" style="font-weight: 700; color: #fff; margin-bottom: 5px;"></div>
                <div id="modalSummaryService" style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 20px;"></div>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="modalLabelPickup">From:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="modalPickup"></strong>
                </div>
                <div id="modalDropContainer" style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span id="modalLabelDrop">To:</span>
                    <strong style="color: #fff; text-align: right; max-width: 70%;" id="modalDrop"></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; color: rgba(255,255,255,0.8);">
                    <span>Date:</span>
                    <strong style="color: #fff;" id="modalDate"></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: #fff;">
                    <span>Total Estimated:</span>
                    <span id="modalSummaryTotal" style="color: var(--gold);"></span>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include "../includes/footer.php"; ?>
