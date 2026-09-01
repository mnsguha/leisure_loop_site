<?php
$fleet = [
    ['name' => 'Sedan', 'models' => 'Swift Dzire, Etios', 'capacity' => '4 Pax', 'img' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=600'],
    ['name' => 'SUV', 'models' => 'Innova, Xylo', 'capacity' => '6 Pax', 'img' => 'https://images.unsplash.com/photo-1563720223185-11003d516935?q=80&w=600'],
    ['name' => 'Premium SUV', 'models' => 'Innova Crysta', 'capacity' => '6-7 Pax', 'img' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=600']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Chauffeur Driven Cars | Leisure Loop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Header -->
    <div class="pt-12 pb-6 px-6 bg-gradient-to-b from-[#050a14] to-transparent sticky top-0 z-50">
        <h1 class="serif text-3xl font-bold text-[var(--gold)]" style="color: #C5A059;">Premium Fleet</h1>
        <p class="text-white/60 text-sm mt-1">Chauffeur driven cars for your journey</p>
    </div>

    <main class="content-area">
        
        <!-- Fleet Carousel -->
        <div class="swiper fleet-swiper px-6">
            <div class="swiper-wrapper">
                <?php foreach ($fleet as $car): ?>
                <div class="swiper-slide">
                    <div class="glass-panel rounded-3xl overflow-hidden p-4 h-full flex flex-col">
                        <div class="w-full h-40 rounded-2xl overflow-hidden mb-4 relative">
                            <img src="<?php echo $car['img']; ?>" class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold text-[var(--gold)]" style="color: #C5A059;">
                                <?php echo $car['capacity']; ?>
                            </div>
                        </div>
                        <h3 class="serif text-xl font-bold mb-1"><?php echo $car['name']; ?></h3>
                        <p class="text-sm text-white/50"><?php echo $car['models']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <!-- Custom Booking Form -->
        <div class="px-6 mt-2">
            <div class="glass-panel rounded-[32px] p-6 shadow-2xl relative overflow-hidden">
                <!-- Decorative element -->
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-[var(--gold)] rounded-full blur-[80px] opacity-20" style="background: #C5A059;"></div>
                
                <h2 class="serif text-2xl font-bold mb-6">Book Your Ride</h2>
                
                <form action="/api/v1/leads" method="POST" id="cabForm" class="flex flex-col gap-4 relative z-10">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <!-- Hidden field to identify the form type -->
                    <input type="hidden" name="form_type" value="cab_booking">
                    
                    <div class="grid grid-cols-2 gap-4">
                        
<label for="input_81b64d59" class="sr-only">Your Name</label>
<input id="input_81b64d59" type="text" name="name" class="form-input" placeholder="Your Name" required>
                        
<label for="input_bbc4070a" class="sr-only">Phone No.</label>
<input id="input_bbc4070a" type="tel" name="phone" class="form-input" placeholder="Phone No." required>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 flex flex-col items-center justify-center h-16 w-4 pointer-events-none">
                            <div class="w-3 h-3 rounded-full border-2 border-[#C5A059]"></div>
                            <div class="w-0.5 h-6 bg-white/20 my-1"></div>
                            <div class="w-3 h-3 bg-[#C5A059]"></div>
                        </div>
                        <div class="pl-10 flex flex-col gap-3">
                            
<label for="input_1f0e384b" class="sr-only">Pickup Location</label>
<input id="input_1f0e384b" type="text" name="pickup" class="form-input border-transparent bg-black/40" placeholder="Pickup Location" required>
                            
<label for="input_9db7130d" class="sr-only">Dropoff Location</label>
<input id="input_9db7130d" type="text" name="dropoff" class="form-input border-transparent bg-black/40" placeholder="Dropoff Location" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-2">
                        
<label for="input_287de2d2" class="sr-only">Date</label>
<input id="input_287de2d2" type="text" name="date" class="form-input" placeholder="Date" data-focus="date" required>
                        
<label for="input_6d59ef59" class="sr-only">Time</label>
<input id="input_6d59ef59" type="text" name="time" class="form-input" placeholder="Time" data-focus="time" required>
                    </div>

                    <select name="vehicle" class="form-input mt-2" required>
                        <option value="" disabled selected>Select Vehicle</option>
                        <option value="Sedan">Sedan (Dzire/Etios)</option>
                        <option value="SUV">SUV (Innova/Xylo)</option>
                        <option value="Premium SUV">Premium SUV (Innova Crysta)</option>
                    </select>

                    <button type="submit" class="w-full mt-4 py-4 rounded-xl font-bold text-black text-lg active:scale-95 transition-transform" style="background: linear-gradient(135deg, #e6c888, #b88a44); box-shadow: 0 10px 25px rgba(197,160,89,0.3);">
                        Confirm Booking
                    </button>
                    <div id="cabFormMsg" class="hidden text-center text-sm font-medium p-3 rounded-xl mt-2"></div>
                </form>
            </div>
        </div>

    </main>

    <?php include "mobile_bottom_nav.php"; ?>
    <?php include "enquiry-modal.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
</body>
</html>
