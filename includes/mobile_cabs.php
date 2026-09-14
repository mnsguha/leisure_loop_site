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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Chauffeur Driven Cars | Leisure Loop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Header -->
    <div class="mob-cabs__header">
        <h1 class="mob-cabs__header-title">Premium Fleet</h1>
        <p class="mob-cabs__header-sub">Chauffeur driven cars for your journey</p>
    </div>

    <main class="content-area">

        <!-- Fleet Carousel -->
        <div class="mob-cabs__fleet-wrap swiper fleet-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($fleet as $car): ?>
                <div class="swiper-slide">
                    <div class="mob-cabs__car-card">
                        <div class="mob-cabs__car-img-wrap">
                            <img src="<?php echo htmlspecialchars($car['img']); ?>" class="mob-cabs__car-img" alt="<?php echo htmlspecialchars($car['name']); ?>">
                            <div class="mob-cabs__car-badge"><?php echo htmlspecialchars($car['capacity']); ?></div>
                        </div>
                        <h3 class="mob-cabs__car-name"><?php echo htmlspecialchars($car['name']); ?></h3>
                        <p class="mob-cabs__car-models"><?php echo htmlspecialchars($car['models']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <!-- Booking Form -->
        <div class="mob-cabs__form-wrap">
            <div class="mob-cabs__form-panel">
                <div class="mob-cabs__form-glow" aria-hidden="true"></div>
                <h2 class="mob-cabs__form-title">Book Your Ride</h2>

                <form action="/api/v1/leads" method="POST" id="cabForm" class="mob-cabs__form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="form_type" value="cab_booking">

                    <div class="mob-cabs__form-row">
                        <label for="input_81b64d59" class="sr-only">Your Name</label>
                        <input id="input_81b64d59" type="text" name="name" class="form-input" placeholder="Your Name" required>
                        <label for="input_bbc4070a" class="sr-only">Phone No.</label>
                        <input id="input_bbc4070a" type="tel" name="phone" class="form-input" placeholder="Phone No." required>
                    </div>

                    <div class="mob-cabs__route">
                        <div class="mob-cabs__route-icon" aria-hidden="true">
                            <div class="mob-cabs__route-dot-top"></div>
                            <div class="mob-cabs__route-line"></div>
                            <div class="mob-cabs__route-dot-bottom"></div>
                        </div>
                        <div class="mob-cabs__route-inputs">
                            <label for="input_1f0e384b" class="sr-only">Pickup Location</label>
                            <input id="input_1f0e384b" type="text" name="pickup" class="form-input" placeholder="Pickup Location" required>
                            <label for="input_9db7130d" class="sr-only">Dropoff Location</label>
                            <input id="input_9db7130d" type="text" name="dropoff" class="form-input" placeholder="Dropoff Location" required>
                        </div>
                    </div>

                    <div class="mob-cabs__form-row">
                        <label for="input_287de2d2" class="sr-only">Date</label>
                        <input id="input_287de2d2" type="text" name="date" class="form-input" placeholder="Date" data-focus="date" required>
                        <label for="input_6d59ef59" class="sr-only">Time</label>
                        <input id="input_6d59ef59" type="text" name="time" class="form-input" placeholder="Time" data-focus="time" required>
                    </div>

                    <label for="select_cab_vehicle" class="sr-only">Select Vehicle</label>
                    <select id="select_cab_vehicle" name="vehicle" class="form-input form-select" required>
                        <option value="" disabled selected>Select Vehicle</option>
                        <option value="Sedan">Sedan (Dzire/Etios)</option>
                        <option value="SUV">SUV (Innova/Xylo)</option>
                        <option value="Premium SUV">Premium SUV (Innova Crysta)</option>
                    </select>

                    <button type="submit" class="mob-cabs__submit">Confirm Booking</button>
                    <div id="cabFormMsg" class="mob-cabs__msg" role="alert"></div>
                </form>
            </div>
        </div>

    </main>

    <?php 
    $extra_scripts = '<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="js/modules/cabs.js" defer></script>';
    include __DIR__ . '/mobile_footer.php'; 
    ?>
</body>
</html>
