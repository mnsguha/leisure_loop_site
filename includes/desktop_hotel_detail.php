<?php
// includes/desktop_hotel_detail.php
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/hotel-detail.css?v=2">

<!-- Top Search Bar -->
<div class="search-bar-container">
    <div class="search-bar">
        <input type="hidden" id="desktop-inputRooms" value="<?php echo (int)$rooms; ?>">
        <input type="hidden" id="desktop-inputAdults" value="<?php echo (int)$adults; ?>">
        <input type="hidden" id="desktop-inputChildren" value="<?php echo (int)$children; ?>">
        <input type="hidden" id="desktop-inputInfants" value="<?php echo (int)$infants; ?>">

        <div class="search-input-group search-group-large">
            <label class="group-label" for="desktop-searchCity">CITY, AREA OR PROPERTY</label>
            <input type="text" id="desktop-searchCity" value="<?php echo htmlspecialchars($hotel['name'] . ', ' . $hotel['place']); ?>" readonly>
        </div>
        <div class="search-input-group">
            <label class="group-label" for="desktop-inputCheckIn">CHECK-IN</label>
            <input type="date" id="desktop-inputCheckIn" value="<?php echo htmlspecialchars($check_in); ?>" min="<?php echo date('Y-m-d'); ?>" class="pointer-input">
        </div>
        <div class="search-input-group">
            <label class="group-label" for="desktop-inputCheckOut">CHECK-OUT</label>
            <input type="date" id="desktop-inputCheckOut" value="<?php echo htmlspecialchars($check_out); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="pointer-input">
        </div>
        <div class="search-input-group" id="desktop-guestsContainer">
            <label class="group-label" for="desktop-displayGuests">ROOMS & GUESTS</label>
            <input type="text" value="<?php echo $rooms; ?> Room<?php echo $rooms > 1 ? 's' : ''; ?>, <?php echo $adults; ?> Adult<?php echo $adults > 1 ? 's' : ''; ?><?php echo $children > 0 ? ', '.$children.' Child'.($children > 1 ? 'ren' : '') : ''; ?>" id="desktop-displayGuests" readonly class="pointer-input">
            
            <div class="guests-dropdown" id="desktop-guestsDropdown">
                <div class="guest-row">
                    <div class="guest-label">Rooms</div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="desktop-btnRoomsMinus" <?php echo $rooms <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="desktop-valRooms"><?php echo $rooms; ?></span>
                        <button type="button" class="btn-count" id="desktop-btnRoomsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Adults</div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="desktop-btnAdultsMinus" <?php echo $adults <= 1 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="desktop-valAdults"><?php echo $adults; ?></span>
                        <button type="button" class="btn-count" id="desktop-btnAdultsPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Children<span class="guest-sublabel">6 - 11 Years Old (CNB)</span></div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="desktop-btnChildrenMinus" <?php echo $children <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="desktop-valChildren"><?php echo $children; ?></span>
                        <button type="button" class="btn-count" id="desktop-btnChildrenPlus">+</button>
                    </div>
                </div>
                <div class="guest-row">
                    <div class="guest-label">Infants<span class="guest-sublabel">0 - 5 Years Old (Free)</span></div>
                    <div class="guest-counter">
                        <button type="button" class="btn-count" id="desktop-btnInfantsMinus" <?php echo $infants <= 0 ? 'disabled' : ''; ?>>-</button>
                        <span class="count-val" id="desktop-valInfants"><?php echo $infants; ?></span>
                        <button type="button" class="btn-count" id="desktop-btnInfantsPlus">+</button>
                    </div>
                </div>
                <button type="button" class="btn-apply-guests" id="desktop-btnApplyGuests">APPLY</button>
            </div>
        </div>
        <button type="button" class="btn-search" id="desktop-btnPerformSearch">SEARCH</button>
    </div>
</div>

<div class="detail-container">
    <div class="main-content">
        <div class="hotel-header-card">
            <h1 class="detail-title">
                <?php echo htmlspecialchars($hotel['name']); ?> 
                <span class="hotel-star-ratings">
                    <?php echo str_repeat('★', $hotel['star_category']); ?>
                </span>
            </h1>
            <div class="detail-meta">
                <?php echo htmlspecialchars($hotel['place']); ?>
            </div>
            
            <div class="rating-badges">
                <?php if (!empty($hotel['google_rating'])): ?>
                <div class="rating-badge-main" data-href="<?php echo htmlspecialchars($hotel['google_review_link'] ?? '#'); ?>">
                    <?php echo number_format($hotel['google_rating'], 1); ?> 
                    <span>Exceptional</span>
                </div>
                <div class="rating-count-label">
                    <?php echo (int)$hotel['google_review_count']; ?> reviews
                </div>
                <?php endif; ?>
                <div class="rating-badge">Cleanliness 9.6</div>
                <div class="rating-badge">Location 9.0</div>
                <div class="rating-badge">Service 9.5</div>
                <div class="rating-badge">Facilities 9.4</div>
            </div>
        </div>

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
            <img src="<?php echo htmlspecialchars($hotel_images[0]['image_url']); ?>" class="single-hero-preview" data-action="open-lightbox" data-idx="0">
        <?php endif; ?>

        <div class="about-section">
            <h2>About The Hotel</h2>
            <div class="hotel-desc-container truncated" id="desktop-hotelDesc">
                <p><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></p>
            </div>
            <button type="button" class="room-more-details" id="desktop-viewMoreDesc" data-action="toggle-desc">View More ∨</button>
            
            <?php 
            $amenities_raw = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
            $amenities = array_filter(array_map('trim', $amenities_raw));
            if (!empty($amenities)): 
            ?>
            <h2 class="mt-8">Amenities</h2>
            <div class="amenities-grid">
                <?php foreach($amenities as $am): ?>
                <div class="amenity-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php echo htmlspecialchars($am); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($rooms_with_plans)): ?>
            <h2 class="section-heading-gold">Select Your Room</h2>
            <?php foreach($rooms_with_plans as $room): ?>
            <div class="room-category">
                <div class="room-header">
                    <h3><?php echo htmlspecialchars($room['room_type_name']); ?></h3>
                    <?php if (isset($room['current_availability']) && $room['current_availability'] > 0): ?>
                        <div class="room-badge"><?php echo $room['current_availability']; ?> Available</div>
                    <?php else: ?>
                        <div class="room-badge room-badge-sold">Sold Out</div>
                    <?php endif; ?>
                </div>
                <div class="room-body">
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
                        
                        <div class="full-room-details" id="desktop-more-details-<?php echo $room['id']; ?>">
                            <div class="room-features">
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg> <?php echo !empty($room['room_size']) ? htmlspecialchars($room['room_size']) : 'Size Not Specified'; ?></span>
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"></path><path d="M2 8h18a2 2 0 0 1 2 2v10"></path><path d="M2 17h20"></path><path d="M6 8v9"></path></svg> <?php echo !empty($room['bed_type']) ? htmlspecialchars($room['bed_type']) : 'Bed Not Specified'; ?></span>
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <?php echo !empty($room['view_type']) ? htmlspecialchars($room['view_type']) : 'View Not Specified'; ?></span>
                                <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h3M18 14h3M18 18h3M7 10h4M7 14h4M7 18h4"></path><path d="M3 10h1M3 14h1M3 18h1"></path></svg> <?php echo !empty($room['smoking_policy']) ? htmlspecialchars($room['smoking_policy']) : 'Smoking Policy Not Specified'; ?></span>
                            </div>
                        </div>
                        
                        <button type="button" class="room-more-details" data-action="toggle-room-details" data-target="desktop-more-details-<?php echo $room['id']; ?>">+ More Details</button>
                    </div>
                    
                    <div class="room-plans-right">
                        <?php foreach($room['plans'] as $plan): 
                            $default_rate = !empty($plan['current_rate']) ? $plan['current_rate']['base_rate_2_pax'] : 0;
                            $tax_est = 0;
                            if ($default_rate <= 1000) {
                                $tax_est = 0;
                            } else if ($default_rate <= 7500) {
                                $tax_est = $default_rate * 0.05;
                            } else {
                                $tax_est = $default_rate * 0.18;
                            }
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
                                </ul>
                            </div>
                            <div class="plan-pricing">
                                <div class="pricing-col" id="desktop-price-container-<?php echo $plan['id']; ?>">
                                    <div class="price-final" id="desktop-final-<?php echo $plan['id']; ?>"><?php echo $default_rate > 0 ? '₹' . number_format($default_rate) : 'N/A'; ?></div>
                                    <div class="price-tax" id="desktop-tax-<?php echo $plan['id']; ?>">+ ₹<?php echo number_format($tax_est); ?> Taxes &amp; fees</div>
                                    <div class="price-per-night">Per night</div>
                                </div>
                                <button class="btn-add-cart" id="desktop-btn-add-<?php echo $plan['id']; ?>" data-action="select-plan" data-plan-id="<?php echo $plan['id']; ?>" data-room-id="<?php echo $room['id']; ?>" data-room-name="<?php echo htmlspecialchars($room['room_type_name'], ENT_QUOTES); ?>" data-plan-name="<?php echo htmlspecialchars($plan['plan_name'], ENT_QUOTES); ?>" data-room-image="<?php echo htmlspecialchars($main_img, ENT_QUOTES); ?>">SELECT</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="price-sidebar">
        <div class="price-widget">
            <div class="price-widget-header">Price Details</div>
            <div class="price-widget-body">
                <div id="desktop-cartEmpty" class="empty-cart-msg">
                    <?php if ($hotel['type'] == 'signature'): ?>
                        No room selected yet.<br>Select a room to view price details.
                    <?php else: ?>
                        Select a room and submit an inquiry for the best custom quotes
                    <?php endif; ?>
                </div>

                <div id="desktop-cartFull" class="hidden">
                    <div class="selected-hotel-name"><?php echo htmlspecialchars($hotel['name']); ?>, <?php echo htmlspecialchars($hotel['place']); ?></div>
                    
                    <div class="date-summary">
                        <?php 
                            $checkin_dt = new DateTime($check_in);
                            $checkout_dt = new DateTime($check_out);
                            $interval = $checkin_dt->diff($checkout_dt);
                            $nights = $interval->days > 0 ? $interval->days : 1;
                        ?>
                        <div class="date-box">
                            <div class="label">CHECK IN</div>
                            <div class="value" id="desktop-sidebarCheckIn"><?php echo date('M d', strtotime($check_in)); ?></div>
                        </div>
                        <div class="night-counter">
                            <span class="material-symbols-outlined night-icon">dark_mode</span>
                            <span class="night-text"><?php echo $nights; ?>N</span>
                        </div>
                        <div class="date-box">
                            <div class="label">CHECK OUT</div>
                            <div class="value" id="desktop-sidebarCheckOut"><?php echo date('M d', strtotime($check_out)); ?></div>
                        </div>
                    </div>

                    <div class="selected-room-name" id="desktop-dispRoomName">PREMIUM ROOM</div>
                    <div class="selected-plan-name" id="desktop-dispPlanName">Room Only</div>
                    
                    <div class="price-breakdown">
                        <div class="sidebar-row sidebar-row-net">
                            <span>Net Rate :</span>
                            <span id="desktop-sidebarNetRate">₹0.00</span>
                        </div>
                        <div class="sidebar-row sidebar-row-gst">
                            <span>GST :</span>
                            <span id="desktop-sidebarTotalTax">+ ₹0 Taxes &amp; fees</span>
                        </div>
                        <div class="sidebar-divider"></div>
                        <div class="sidebar-row total-row">
                            <span>TOTAL PAYABLE :</span>
                            <span id="desktop-sidebarTotalFinal">₹0.00</span>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-proceed" id="desktop-btnProceed" disabled data-action="open-checkout">
                    <?php echo $hotel['type'] == 'signature' ? 'PROCEED' : 'Submit Inquiry'; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="desktop-checkoutModal" class="modal-overlay" data-lenis-prevent>
    <div class="h-modal-content" data-lenis-prevent>
        <div class="h-modal-left">
            <div class="h-modal-header">
                <h2>Guest Details</h2>
                <span class="h-close-modal material-symbols-outlined" data-action="close-checkout" aria-label="Close">close</span>
            </div>
            
            <form id="desktop-checkoutForm">
                <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $hotel['type']; ?>">
                <input type="hidden" name="room_id" id="desktop-formRoomId">
                <input type="hidden" name="plan_id" id="desktop-formPlanId">
                <input type="hidden" name="final_price" id="desktop-formFinalPrice">
                <input type="hidden" name="rooms" id="desktop-formRooms" value="1">
                <input type="hidden" name="adults" id="desktop-formAdults" value="2">
                <input type="hidden" name="children" id="desktop-formChildren" value="0">
                
                <div class="h-form-section">
                    <h3>Guest Information</h3>
                    <div class="h-form-row">
                        <div class="h-form-group">
                            <label for="desktop-guestName">First Name *</label>
                            <input type="text" id="desktop-guestName" name="guest_name" class="h-form-control" required>
                        </div>
                        <div class="h-form-group">
                            <label for="desktop-guestLastName">Last Name *</label>
                            <input type="text" id="desktop-guestLastName" name="guest_last_name" class="h-form-control" required>
                        </div>
                    </div>
                    <div class="h-form-group">
                        <label for="desktop-specialRequest">Any special request (optional)</label>
                        <input type="text" id="desktop-specialRequest" name="special_request" class="h-form-control">
                    </div>
                </div>

                <div class="h-form-section">
                    <h3>Contact Details</h3>
                    <div class="h-form-row">
                        <div class="h-form-group">
                            <label for="desktop-guestPhone">Mobile number *</label>
                            <div class="h-phone-input-group">
                                <span class="h-phone-prefix">+91</span>
                                <input type="tel" id="desktop-guestPhone" name="phone" class="h-form-control h-phone-field" placeholder="xxxxx xxxxx" required>
                            </div>
                        </div>
                        <div class="h-form-group">
                            <label for="desktop-guestEmail">Email *</label>
                            <input type="email" id="desktop-guestEmail" name="email" class="h-form-control" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="h-btn-proceed mt-3" id="desktop-btnSubmitModal">
                    <?php echo $hotel['type'] == 'signature' ? 'Confirm & Book Now' : 'Submit Inquiry'; ?>
                </button>
                <div id="desktop-modalFormMsg" class="h-form-feedback"></div>
            </form>
        </div>
        
        <div class="h-modal-right">
            <h3 class="h-modal-summary-title">Enquiry Summary</h3>
            <p class="h-modal-pkg-title" id="desktop-modalSummaryHotel"><?php echo htmlspecialchars($hotel['name']); ?></p>
            
            <div class="h-modal-dates-card">
                <div class="h-modal-date-col">
                    <span class="h-modal-date-label">Check In <span class="material-symbols-outlined">calendar_month</span></span>
                    <span class="h-modal-date-val" id="desktop-modalCheckIn">--</span>
                </div>
                <div class="h-modal-nights-pill">
                    <span class="material-symbols-outlined">dark_mode</span>
                    <span id="desktop-modalNightsVal">1N</span>
                </div>
                <div class="h-modal-date-col h-modal-date-col--right">
                    <span class="h-modal-date-label">Check Out <span class="material-symbols-outlined">calendar_month</span></span>
                    <span class="h-modal-date-val" id="desktop-modalCheckOut">--</span>
                </div>
            </div>

            <div class="h-summary-room-head">
                <img id="desktop-modalRoomImg" class="h-summary-room-img" alt="" hidden>
                <div class="h-summary-room-text">
                    <div class="selected-room-name" id="desktop-modalRoomName">PREMIUM ROOM</div>
                    <div class="selected-plan-name" id="desktop-modalPlanName">Room Only</div>
                </div>
            </div>

            <div class="price-breakdown">
                <div class="sidebar-row sidebar-row-net">
                    <span>Net Rate :</span>
                    <span id="desktop-modalBasePrice">--</span>
                </div>
                <div class="sidebar-row sidebar-row-gst">
                    <span>GST :</span>
                    <span id="desktop-modalTaxes">Included</span>
                </div>
                <div class="sidebar-divider"></div>
                <div class="sidebar-row total-row">
                    <span>TOTAL PAYABLE :</span>
                    <span id="desktop-modalSummaryTotal">--</span>
                </div>
            </div>
            
            <div class="h-modal-amenities-strip">
                <div class="h-modal-amenity-item">
                    <span class="material-symbols-outlined">bed</span>
                    <span>STAY</span>
                </div>
                <div class="h-modal-amenity-item">
                    <span class="material-symbols-outlined">restaurant</span>
                    <span>MEALS</span>
                </div>
                <div class="h-modal-amenity-item">
                    <span class="material-symbols-outlined">wifi</span>
                    <span>WIFI</span>
                </div>
                <div class="h-modal-amenity-item">
                    <span class="material-symbols-outlined">pool</span>
                    <span>POOL</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Gallery -->
<div id="lightboxModal">
    <div class="lightbox-header">
        <span id="lightboxCounter">1 / 5</span>
        <span class="lightbox-close" data-action="close-lightbox">&times;</span>
    </div>
    <div class="lightbox-main">
        <span class="lightbox-prev" data-action="prev-lightbox">&#10094;</span>
        <img id="lightboxMainImg" src="" alt="Gallery">
        <span class="lightbox-next" data-action="next-lightbox">&#10095;</span>
    </div>
    <div class="lightbox-thumbnails">
        <?php if (!empty($hotel_images)): ?>
            <?php foreach($hotel_images as $idx => $img): ?>
                <img class="lightbox-thumbnail" data-idx="<?= $idx ?>" src="<?= htmlspecialchars($img['image_url']) ?>" alt="Thumb <?= $idx + 1 ?>">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="hotel-room-data-store" hidden data-rooms="<?php echo htmlspecialchars(json_encode($rooms_with_plans), ENT_QUOTES, 'UTF-8'); ?>"></div>
<script src="js/modules/hotel-detail.js?v=<?php echo time(); ?>" defer></script>

<?php include "../includes/footer.php"; ?>
