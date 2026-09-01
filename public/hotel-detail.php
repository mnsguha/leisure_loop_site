<?php
$page_title = "Hotel Details | Leisure Loop";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id || !$pdo) { header("Location: hotels.php"); exit; }

$stmt = $pdo->prepare("SELECT h.*, (SELECT MIN(rr.base_rate_2_pax) FROM hotel_room_rates rr JOIN hotel_room_plans rp ON rr.plan_id = rp.id JOIN hotel_rooms r ON rp.room_id = r.id WHERE r.hotel_id = h.id AND rr.base_rate_2_pax > 0) as dynamic_starting_tariff FROM hotels h WHERE h.id = ? AND h.is_active=1");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) { header("Location: hotels.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$hotel_images = $stmt->fetchAll();

$page_title = $hotel['name'] . " | Leisure Loop";

$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent);

// Fetch Rooms and Plans
$rooms_with_plans = [];
$stmt = $pdo->prepare("SELECT * FROM hotel_rooms WHERE hotel_id = ?");
$stmt->execute([$id]);
$db_rooms = $stmt->fetchAll();

$check_in = isset($_GET['check_in']) ? trim($_GET['check_in']) : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? trim($_GET['check_out']) : date('Y-m-d', strtotime('+1 day'));
$rooms = isset($_GET['rooms']) ? (int)$_GET['rooms'] : 1;
$adults = isset($_GET['adults']) ? (int)$_GET['adults'] : 2;
$children = isset($_GET['children']) ? (int)$_GET['children'] : 0;
$infants = isset($_GET['infants']) ? (int)$_GET['infants'] : 0;

$check_date = $check_in;

foreach ($db_rooms as $room) {
    // Check inventory for the date
    $inv_stmt = $pdo->prepare("SELECT available_rooms FROM room_inventory WHERE room_id = ? AND inventory_date = ?");
    $inv_stmt->execute([$room['id'], $check_date]);
    $inv_res = $inv_stmt->fetch();
    $room['current_availability'] = $inv_res ? (int)$inv_res['available_rooms'] : (int)$room['total_rooms'];

    $p_stmt = $pdo->prepare("SELECT * FROM hotel_room_plans WHERE room_id = ? ORDER BY id ASC");
    $p_stmt->execute([$room['id']]);
    $plans = $p_stmt->fetchAll();
    
    foreach ($plans as &$plan) {
        $dr_stmt = $pdo->prepare("SELECT * FROM hotel_room_rates WHERE plan_id = ? ORDER BY rate_date ASC");
        $dr_stmt->execute([$plan['id']]);
        $plan['date_rates'] = $dr_stmt->fetchAll();
    }
    unset($plan);
    
    $room['plans'] = $plans;
    $rooms_with_plans[] = $room;
}

if ($is_mobile) {
    include '../includes/mobile_hotel-detail.php';
    exit;
}

include "../includes/header.php";
?>
<link rel="stylesheet" href="css/hotels.css">

<!-- Flatpickr for Date Range -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Top Search Bar -->
<div class="search-bar-container">
    <div class="search-bar">
        <input type="hidden" id="inputCheckIn" value="<?php echo htmlspecialchars($check_in); ?>">
        <input type="hidden" id="inputCheckOut" value="<?php echo htmlspecialchars($check_out); ?>">
        <input type="hidden" id="inputRooms" value="<?php echo (int)$rooms; ?>">
        <input type="hidden" id="inputAdults" value="<?php echo (int)$adults; ?>">
        <input type="hidden" id="inputChildren" value="<?php echo (int)$children; ?>">
        <input type="hidden" id="inputInfants" value="<?php echo (int)$infants; ?>">

        <div class="search-input-group" style="flex: 1.5;">
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">CITY, AREA OR PROPERTY</div>
            <input type="text" value="<?php echo htmlspecialchars($hotel['name'] . ', ' . $hotel['place']); ?>" readonly>
        </div>
        <div class="search-input-group" style="flex: 1;">
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">CHECK-IN / CHECK-OUT</div>
            <input type="text" value="<?php echo date('M d, Y', strtotime($check_in)); ?> - <?php echo date('M d, Y', strtotime($check_out)); ?>" id="displayDates" readonly style="cursor: pointer;">
        </div>
        <div class="search-input-group" style="flex: 1; position: relative;" id="guestsContainer">
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.5);">ROOMS & GUESTS</div>
            <input type="text" value="<?php echo $rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?>, <?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?>" id="displayGuests" readonly style="cursor: pointer;">
            
            <!-- Guests Dropdown -->
            <div class="guests-dropdown" id="guestsDropdown">
                <div class="guest-row">
                    <div class="guest-label">Rooms</div>
                    <div class="guest-counter">
                        <button class="btn-count" id="btnRoomsMinus" <?php echo $rooms <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valRooms"><?php echo $rooms; ?></span>
                        <button class="btn-count" id="btnRoomsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Adults</div>
                    <div class="guest-counter">
                        <button class="btn-count" id="btnAdultsMinus" <?php echo $adults <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valAdults"><?php echo $adults; ?></span>
                        <button class="btn-count" id="btnAdultsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">
                        Children
                        <span class="guest-sublabel">6 - 11 Years Old (CNB)</span>
                    </div>
                    <div class="guest-counter">
                        <button class="btn-count" id="btnChildrenMinus" <?php echo $children <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valChildren"><?php echo $children; ?></span>
                        <button class="btn-count" id="btnChildrenPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">
                        Infants
                        <span class="guest-sublabel">0 - 5 Years Old (Free)</span>
                    </div>
                    <div class="guest-counter">
                        <button class="btn-count" id="btnInfantsMinus" <?php echo $infants <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="valInfants"><?php echo $infants; ?></span>
                        <button class="btn-count" id="btnInfantsPlus">+</button>
                    </div>
                </div>
                <button class="btn-apply-guests" id="btnApplyGuests">APPLY</button>
            </div>
        </div>
        <button class="btn-search" id="btnPerformSearch">SEARCH</button>
    </div>
</div>

<div class="detail-container">
    <div class="main-content">
        <!-- Header Info -->
        <div class="hotel-header-card">
            <h1 class="detail-title">
                <?php echo htmlspecialchars($hotel['name']); ?> 
                <span style="color: #f5a623; font-size: 1.2rem;">
                    <?php echo str_repeat('★', $hotel['star_category']); ?>
                </span>
            </h1>
            <div class="detail-meta">
                <?php echo htmlspecialchars($hotel['place']); ?>
            </div>
            
            <div class="rating-badges">
                <?php if (!empty($hotel['google_rating'])): ?>
                <div class="rating-badge-main" <?php if (!empty($hotel['google_review_link'])) echo 'style="cursor:pointer;" data-action="eval:event.preventDefault(); event.stopPropagation(); window.open(\'' . addslashes(htmlspecialchars($hotel['google_review_link'])) . '\', \'GoogleReviews\', \'width=1000,height=800,top=100,left=200,scrollbars=yes,resizable=yes\');"'; ?>>
                    <?php echo number_format($hotel['google_rating'], 1); ?> 
                    <span style="font-weight: 400; font-size: 0.85rem;">Exceptional</span>
                </div>
                <div style="color: #666; font-size: 0.85rem; display: flex; align-items: center;">
                    <?php echo (int)$hotel['google_review_count']; ?> reviews
                </div>
                <?php endif; ?>
                <div class="rating-badge">Cleanliness 9.6</div>
                <div class="rating-badge">Location 9.0</div>
                <div class="rating-badge">Service 9.5</div>
                <div class="rating-badge">Facilities 9.4</div>
            </div>
        </div>

        <!-- Bento Box Gallery -->
        <?php if(!empty($hotel_images) && count($hotel_images) >= 3): ?>
        <div class="bento-gallery">
            <img src="<?php echo htmlspecialchars($hotel_images[0]['image_url']); ?>" class="bento-item bento-main" data-action="open-lightbox" data-idx="0">
            <img src="<?php echo htmlspecialchars($hotel_images[1]['image_url']); ?>" class="bento-item" data-action="open-lightbox" data-idx="1">
            <img src="<?php echo htmlspecialchars($hotel_images[2]['image_url']); ?>" class="bento-item" data-action="open-lightbox" data-idx="2">
            
            <?php if(isset($hotel_images[3])): ?>
                <img src="<?php echo htmlspecialchars($hotel_images[3]['image_url']); ?>" class="bento-item" data-action="open-lightbox" data-idx="3">
            <?php endif; ?>
            
            <?php if(isset($hotel_images[4])): ?>
                <div class="bento-overlay-container" data-action="open-lightbox" data-idx="4">
                    <img src="<?php echo htmlspecialchars($hotel_images[4]['image_url']); ?>" class="bento-item">
                    <?php if(count($hotel_images) > 5): ?>
                        <div class="bento-overlay">+<?php echo count($hotel_images) - 5; ?> More</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php elseif(!empty($hotel_images)): ?>
            <img src="<?php echo htmlspecialchars($hotel_images[0]['image_url']); ?>" style="width:100%; height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 30px; cursor: pointer;" data-action="open-lightbox" data-idx="0">
        <?php endif; ?>

        <!-- About Section -->
        <div class="about-section">
            <h2>About The Hotel</h2>
            <div class="hotel-desc-container truncated" id="hotelDesc">
                <p><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
            </div>
            <a href="javascript:void(0)" class="room-more-details" id="viewMoreDesc" data-action="toggle-desc" style="display: inline-block; margin-top: 10px;">View More ∨</a>
            
            <?php 
            $amenities_raw = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
            $amenities = array_filter(array_map('trim', $amenities_raw));
            if (!empty($amenities)): 
            ?>
            <h2 style="margin-top: 30px;">Amenities</h2>
            <div class="amenities-grid">
                <?php foreach($amenities as $am): ?>
                <div class="amenity-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="3" style="width: 16px; height: 16px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php echo htmlspecialchars($am); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Rooms Section -->
        <?php if (!empty($rooms_with_plans)): ?>
            <h2 style="font-size: 1.4rem; color: var(--gold); margin: 40px 0 20px;">Select Your Room</h2>
            <?php foreach($rooms_with_plans as $room): ?>
            <div class="room-category">
                <div class="room-header">
                    <h3><?php echo htmlspecialchars($room['room_type_name']); ?></h3>
                    <?php if (isset($room['current_availability']) && $room['current_availability'] > 0): ?>
                        <div class="room-badge"><?php echo $room['current_availability']; ?> Available</div>
                    <?php else: ?>
                        <div class="room-badge" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; border-color: rgba(231, 76, 60, 0.3);">Sold Out</div>
                    <?php endif; ?>
                </div>
                <div class="room-body">
                    <!-- Left: Room Info -->
                    <div class="room-info-left">
                        <div class="room-gallery-grid">
                            <?php 
                                $main_img = !empty($room['room_image']) ? $room['room_image'] : '../assets/images/placeholder.jpg';
                                $additional_images = [];
                                if (!empty($room['additional_images'])) {
                                    $additional_images = json_decode($room['additional_images'], true);
                                    if (!is_array($additional_images)) $additional_images = [];
                                }
                            ?>
                            <div class="room-gallery-main">
                                <a href="<?php echo htmlspecialchars($main_img); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="Room">
                                </a>
                            </div>
                            
                            <?php if (count($additional_images) > 0): ?>
                            <div class="room-gallery-thumbs">
                                <?php 
                                $max_thumbs = 3;
                                $thumb_count = min(count($additional_images), $max_thumbs);
                                for ($i = 0; $i < $thumb_count; $i++): 
                                    $img = $additional_images[$i];
                                    $is_last = ($i === $max_thumbs - 1);
                                    $remaining = count($additional_images) - $max_thumbs;
                                ?>
                                    <?php if ($is_last && $remaining > 0): ?>
                                    <div class="thumb-more">
                                        <a href="<?php echo htmlspecialchars($img); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Room Thumb">
                                            <div class="thumb-overlay">+<?php echo $remaining; ?> More</div>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($img); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Room Thumb">
                                    </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="full-room-details" id="more-details-<?php echo $room['id']; ?>" style="display: none; margin-top: 15px; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.05);">
                            <div class="room-features" style="display: flex; flex-direction: column; gap: 10px;">
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg> <?php echo !empty($room['room_size']) ? htmlspecialchars($room['room_size']) : 'Size Not Specified'; ?></span>
                                
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"></path><path d="M2 8h18a2 2 0 0 1 2 2v10"></path><path d="M2 17h20"></path><path d="M6 8v9"></path></svg> <?php echo !empty($room['bed_type']) ? htmlspecialchars($room['bed_type']) : 'Bed Not Specified'; ?></span>
                                
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <?php echo !empty($room['view_type']) ? htmlspecialchars($room['view_type']) : 'View Not Specified'; ?></span>
                                
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h3M18 14h3M18 18h3M7 10h4M7 14h4M7 18h4"></path><path d="M3 10h1M3 14h1M3 18h1"></path></svg> <?php echo !empty($room['smoking_policy']) ? htmlspecialchars($room['smoking_policy']) : 'Smoking Policy Not Specified'; ?></span>
                            </div>
                        </div>
                        
                        <a href="javascript:void(0)" class="room-more-details" data-action="eval:
                            var details = document.getElementById('more-details-<?php echo $room['id']; ?>');
                            if (details.style.display === 'none') {
                                details.style.display = 'block';
                                this.innerText = '- Less Details';
                            } else {
                                details.style.display = 'none';
                                this.innerText = '+ More Details';
                            }
                        ">+ More Details</a>
                    </div>
                    
                    <!-- Right: Rate Plans -->
                    <div class="room-plans-right">
                        <?php foreach($room['plans'] as $plan): 
                            $default_rate = !empty($plan['date_rates']) ? $plan['date_rates'][0]['base_rate_2_pax'] : 0;
                            $final_price = $default_rate;
                        ?>
                        <div class="plan-row">
                            <div class="plan-details">
                                <h4><?php echo htmlspecialchars($plan['plan_name']); ?></h4>
                                <div class="plan-occupancy">Maximum occupancy <?php echo (int)($plan['max_adults'] ?? 0); ?> Adults | <?php echo (int)($plan['max_children'] ?? 0); ?> Children</div>
                                <ul class="plan-inclusions">
                                    <?php 
                                    $inclusions_list = array_filter(array_map('trim', explode(',', $plan['inclusions'] ?? '')));
                                    foreach($inclusions_list as $inc): ?>
                                        <li><?php echo htmlspecialchars($inc); ?></li>
                                    <?php endforeach; ?>
                                    <li id="ea-inc-<?php echo $plan['id']; ?>" style="display:none; color: var(--gold);">Extra Mattress (Adult)</li>
                                    <li id="ec-inc-<?php echo $plan['id']; ?>" style="display:none; color: var(--gold);">Child Without Bed (CNB)</li>
                                </ul>
                            </div>
                            <div class="plan-pricing">
                                <div style="text-align: right;" id="price-container-<?php echo $plan['id']; ?>">
                                    <div class="price-strike" id="strike-<?php echo $plan['id']; ?>" style="display:none;"></div>
                                    <div class="price-final" id="final-<?php echo $plan['id']; ?>"><?php echo $final_price > 0 ? '₹' . number_format($final_price) : 'N/A'; ?></div>
                                    <div class="price-tax">Avg Per Night<br>(Incl Taxes)</div>
                                </div>
                                <button class="btn-add-cart" id="btn-add-<?php echo $plan['id']; ?>" 
                                    data-action="eval:addToCart(this, <?php echo $plan['id']; ?>, <?php echo $room['id']; ?>, '<?php echo htmlspecialchars(addslashes($hotel['name'])); ?>', '<?php echo htmlspecialchars(addslashes($room['room_type_name'])); ?>', '<?php echo htmlspecialchars(addslashes($plan['plan_name'])); ?>')">
                                    SELECT
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Room Amenities below plans -->
                        <?php 
                        $room_amenities_list = !empty($room['amenities']) ? array_filter(array_map('trim', explode(',', $room['amenities']))) : [];
                        if (!empty($room_amenities_list)): 
                        ?>
                        <div style="padding: 15px 25px;">
                            <div style="font-size: 0.95rem; color: #fff; font-weight: 600; margin-bottom: 15px;">Room Amenities</div>
                            <ul class="plan-inclusions" style="column-count: 2; column-gap: 20px; margin: 0;">
                                <?php foreach($room_amenities_list as $am): ?>
                                    <li><?php echo htmlspecialchars($am); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar (Sticky) -->
    <div class="price-sidebar">
        <div class="price-widget">
            <div class="price-widget-header">
                Price Details
            </div>
            <div class="price-widget-body">
                
                <div id="cartEmpty" class="empty-cart-msg">
                    <?php if ($hotel['type'] == 'signature'): ?>
                        No room selected yet.<br>Select a room to view price details.
                    <?php else: ?>
                        Select a room and submit an inquiry for the best custom quotes
                    <?php endif; ?>
                </div>

                <div id="cartFull" style="display: none;">
                    <div class="selected-hotel-name"><?php echo htmlspecialchars($hotel['name']); ?>, <?php echo htmlspecialchars($hotel['place']); ?></div>
                    <div class="selected-room-name" id="dispRoomName">PREMIUM ROOM</div>
                    <div class="selected-plan-name" id="dispPlanName">Room Only</div>
                    
                    <div class="date-summary">
                        <div class="date-box">
                            <div class="label">CHECK IN</div>
                            <div class="value" id="sidebarCheckIn"><?php echo date('M d', strtotime($check_in)); ?></div>
                        </div>
                        <div class="date-box">
                            <div class="label">CHECK OUT</div>
                            <div class="value" id="sidebarCheckOut"><?php echo date('M d', strtotime($check_out)); ?></div>
                        </div>
                    </div>
                    
                    <div class="guest-summary">
                        <span id="sidebarRoomsText"><?php echo $rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?></span>
                        <span id="sidebarGuestsText"><?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?></span>
                    </div>
                    
                    <div class="payment-type">
                        <input type="radio" checked> Pay Online (Later)
                    </div>
                    
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Taxes</span>
                            <span id="dispTaxes">₹0.00</span>
                        </div>
                        <div class="total-row">
                            <span>TOTAL PAYABLE :</span>
                            <span id="sidebarTotalFinal">₹0.00</span>
                        </div>
                    </div>
                </div>
                
                <button class="btn-proceed" id="btnProceed" disabled data-action="open-checkout">
                    <?php echo $hotel['type'] == 'signature' ? 'PROCEED' : 'Submit Inquiry'; ?>
                </button>
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

                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $hotel['type']; ?>">
                <input type="hidden" name="room_id" id="formRoomId">
                <input type="hidden" name="plan_id" id="formPlanId">
                <input type="hidden" name="final_price" id="formFinalPrice">
                <input type="hidden" name="rooms" value="1">
                <input type="hidden" name="adults" value="2">
                
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
                    <?php echo $hotel['type'] == 'signature' ? 'Confirm & Book Now' : 'Submit Inquiry'; ?>
                </button>
                <div id="modalFormMsg" style="margin-top: 15px; font-weight: 600; text-align: center;"></div>
            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="modal-right">
            <h3 style="margin-top: 0; color: var(--gold);">Booking Summary</h3>
            <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-weight: 700; color: #fff; margin-bottom: 5px;"><?php echo htmlspecialchars($hotel['name']); ?></div>
                <div id="modalSummaryRoom" style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 20px;"></div>
                
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; color: rgba(255,255,255,0.8);">
                    <span>Check In:</span>
                    <strong style="color: #fff;" id="modalCheckIn"><?php echo date('D, d M'); ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 20px; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; color: rgba(255,255,255,0.8);">
                    <span>Check Out:</span>
                    <strong style="color: #fff;" id="modalCheckOut"><?php echo date('D, d M', strtotime('+1 day')); ?></strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 700; color: #fff;">
                    <span>Total Payable:</span>
                    <span id="modalSummaryTotal" style="color: var(--gold);"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Lightbox Modal HTML -->
<div id="lightboxModal">
    <div class="lightbox-header">
        <span id="lightboxCounter">1 / <?php echo count($hotel_images); ?></span>
        <span class="lightbox-close" data-action="close-lightbox">&times;</span>
    </div>
    <div class="lightbox-main">
        <div class="lightbox-prev" data-action="prev-lightbox">&#10094;</div>
        <img id="lightboxMainImg" src="">
        <div class="lightbox-next" data-action="next-lightbox">&#10095;</div>
    </div>
    <div class="lightbox-thumbnails" id="lightboxThumbnails">
        <?php foreach($hotel_images as $index => $img): ?>
            <img src="<?php echo htmlspecialchars($img['image_url']); ?>" class="lightbox-thumbnail" data-action="open-lightbox" data-idx="<?php echo $index; ?>" id="lb-thumb-<?php echo $index; ?>">
        <?php endforeach; ?>
    </div>
</div>

<script src="js/modules/hotels.js" defer></script>

<?php include "../includes/footer.php"; ?>
