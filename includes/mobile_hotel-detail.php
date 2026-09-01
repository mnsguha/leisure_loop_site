<?php
// Requires $hotel and $rooms to be set in the parent file (hotel-detail.php)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Hero Image & Back Button -->
    <a aria-label="Link" href="hotels.php" class="back-btn">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    
    <div class="hero-img-wrapper">
        <img src="<?php echo htmlspecialchars($hotel['main_image']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
    </div>

    <main class="content-area px-5">
        
        <!-- Header Info -->
        <div class="mb-8">
            <div class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase mb-3 border" style="background: rgba(197, 160, 89, 0.15); color: #C5A059; border-color: rgba(197, 160, 89, 0.3);">
                <?php echo $hotel['star_category']; ?> Star <?php echo ucfirst($hotel['type']); ?>
            </div>
            <h1 class="serif text-4xl font-bold text-white mb-2 leading-tight"><?php echo htmlspecialchars($hotel['name']); ?></h1>
            <div class="flex items-center gap-1.5 text-white/70 text-sm font-medium">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <?php echo htmlspecialchars($hotel['place']); ?>
            </div>
        </div>

        <!-- About Section -->
        <div class="mb-10">
            <h2 class="serif text-2xl font-bold mb-3">About the property</h2>
            <p class="text-white/70 leading-relaxed text-[15px]">
                <?php echo nl2br(htmlspecialchars($hotel['description'])); ?>
            </p>
        </div>

        <!-- Room Types (If Signature) -->
        <?php if (!empty($rooms)): ?>
        <div class="mb-10" id="roomsSection">
            <h2 class="serif text-2xl font-bold mb-4">Available Rooms</h2>
            <div class="flex flex-col gap-4">
                <?php foreach ($rooms as $room): 
                    $final_price = $room['base_tariff'];
                    if($room['discount_percent'] > 0) {
                        $final_price = $room['base_tariff'] - ($room['base_tariff'] * ($room['discount_percent'] / 100));
                    }
                ?>
                <div class="room-card" id']; ?>, '<?php echo htmlspecialchars(addslashes($room['room_type_name'])); ?>')">
                    <div class="h-40 w-full">
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="serif text-xl font-bold mb-1"><?php echo htmlspecialchars($room['room_type_name']); ?></h3>
                        <p class="text-xs text-white/60 mb-3 truncate">✓ <?php echo htmlspecialchars(str_replace(',', ' • ✓ ', $room['amenities'])); ?></p>
                        <div class="flex justify-between items-end border-t border-white/10 pt-3 mt-2">
                            <div>
                                <?php if($room['discount_percent'] > 0): ?>
                                    <div class="text-white/40 text-xs line-through">₹<?php echo number_format($room['base_tariff']); ?></div>
                                <?php endif; ?>
                                <div class="text-[var(--gold)] text-xl font-bold leading-none" style="color: #C5A059;">₹<?php echo number_format($final_price); ?></div>
                            </div>
                            <button class="bg-[var(--gold)] text-black text-sm font-semibold px-4 py-2 rounded-lg" style="background: #C5A059;">Select</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dedicated Booking Form -->
        <div class="glass-panel rounded-[32px] p-6 shadow-2xl relative overflow-hidden mt-6" id="bookingFormSection">
            <h2 class="serif text-2xl font-bold mb-2">Book Your Stay</h2>
            <p class="text-white/60 text-sm mb-6">
                <?php if ($hotel['type'] == 'signature'): ?>
                    Pay later at the property. Instant confirmation.
                <?php else: ?>
                    Tariff starts from ₹<?php echo number_format($hotel['starting_tariff']); ?>. Get the best quote.
                <?php endif; ?>
            </p>
            
            <form id="mobileHotelBookingForm" data-hotel-type="<?php echo $hotel['type'] ?? ''; ?>" class="flex flex-col gap-4">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $hotel['type']; ?>">
                
                <?php if ($hotel['type'] == 'signature'): ?>
                <!-- Selected Room Display -->
                <div id="mSelectedRoomDisplay" class="hidden border border-[#C5A059] bg-[#C5A059]/10 rounded-xl p-3 mb-2">
                    <div class="text-[10px] text-[#C5A059] font-bold tracking-widest uppercase mb-1">Selected Room</div>
                    <div id="mSelectedRoomName" class="font-semibold text-white"></div>
                    <input type="hidden" name="room_id" id="mFormRoomId">
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-2 gap-3">
                    
<label for="input_1cb533c1" class="sr-only">Guest Name *</label>
<input id="input_1cb533c1" type="text" name="guest_name" class="form-input" placeholder="Guest Name *" required>
                    
<label for="input_0d66a8b2" class="sr-only">Phone *</label>
<input id="input_0d66a8b2" type="tel" name="phone" class="form-input" placeholder="Phone *" required>
                </div>
                
<label for="input_bea0d04b" class="sr-only">Email * (For Voucher)</label>
<input id="input_bea0d04b" type="email" name="email" class="form-input" placeholder="Email * (For Voucher)" required>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-white/50 ml-1 mb-1 block">Check In</label>
                        <input type="date" name="check_in" class="form-input" required>
                    </div>
                    <div>
                        <label class="text-xs text-white/50 ml-1 mb-1 block">Check Out</label>
                        <input type="date" name="check_out" class="form-input" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    
<label for="input_e090b312" class="sr-only">Adults</label>
<input id="input_e090b312" type="number" name="adults" min="1" class="form-input" placeholder="Adults" required>
                    
<label for="input_02999b48" class="sr-only">Kids</label>
<input id="input_02999b48" type="number" name="kids" min="0" class="form-input" placeholder="Kids">
                </div>

                <button type="submit" class="w-full mt-4 py-4 rounded-xl font-bold text-black text-lg active:scale-95 transition-transform" style="background: linear-gradient(135deg, #e6c888, #b88a44); box-shadow: 0 10px 25px rgba(197,160,89,0.3);">
                    Confirm Booking
                </button>
                <div id="mHotelFormMsg" class="hidden text-center text-sm font-medium p-3 rounded-xl mt-2"></div>
            </form>
        </div>

    </main>

</body>
</html>
