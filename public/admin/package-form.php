<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$hasMapCoords = $pdo ? tableHasColumn($pdo, 'packages', 'map_coords') : false;

$pkg = [
    'title' => '',
    'slug' => '',
    'destination' => '',
    'price' => '',
    'original_price' => '',
    'image_url' => '',
    'banner_image_url' => '',
    'is_active' => 1,
    'is_international' => 0,
    'is_trending' => 0,
    'use_destination_terms' => 1,
    'itinerary' => [],
    'itinerary_heading' => 'Day-by-Day Journey',
    'map_coords' => '',
    'tour_code' => '',
    'tour_type' => '',
    'days' => '',
    'nights' => '',
    'highlights' => '',
    'description_rich' => '',
    'photos' => '',
    'rating_score' => '4.8',
    'rating_count' => '150',
    'inclusions' => '',
    'exclusions' => '',
    'package_type' => 'curated',
    'hotel_details' => '',
    'vehicle_details' => '',
    'meal_plan_details' => ''
];

// Load destinations for dropdown
$destinations_list = [];
try {
    $destinations_list = $pdo ? $pdo->query("SELECT name FROM destinations WHERE is_active=1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_COLUMN) : [];
} catch (Exception $e) { $destinations_list = []; }

if ($id > 0 && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $pkg = array_merge($pkg, $res);
        $pkg['itinerary'] = json_decode($res['itinerary'], true) ?: [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $destination = trim($_POST['destination'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $original_price = trim($_POST['original_price'] ?? '') !== '' ? (float) $_POST['original_price'] : null;
    $image_url = str_replace('\\', '/', trim($_POST['image_url'] ?? ''));
    $map_coords = trim($_POST['map_coords'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_international = isset($_POST['is_international']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    $use_destination_terms = isset($_POST['use_destination_terms']) ? 1 : 0;
    
    $tour_code = trim($_POST['tour_code'] ?? '');
    $tour_type = trim($_POST['tour_type'] ?? '');
    $days = trim($_POST['days'] ?? '') !== '' ? (int) $_POST['days'] : null;
    $nights = trim($_POST['nights'] ?? '') !== '' ? (int) $_POST['nights'] : null;
    $highlights = trim($_POST['highlights'] ?? '');
    $itinerary_heading = trim($_POST['itinerary_heading'] ?? 'Day-by-Day Journey');
    $description_rich = trim($_POST['description_rich'] ?? '');
    $rating_score = trim($_POST['rating_score'] ?? '') !== '' ? (float) $_POST['rating_score'] : 4.8;
    $rating_count = trim($_POST['rating_count'] ?? '') !== '' ? (int) $_POST['rating_count'] : 150;
    $inclusions = trim($_POST['inclusions'] ?? '');
    $exclusions = trim($_POST['exclusions'] ?? '');
    $package_type = trim($_POST['package_type'] ?? 'curated');
    $hotel_details = trim($_POST['hotel_details'] ?? '');
    $vehicle_details = trim($_POST['vehicle_details'] ?? '');
    $meal_plan_details = trim($_POST['meal_plan_details'] ?? '');

    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['package_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['package_image']['name']);
        $upload_dir = '../images/pkg/';
        $dest_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $image_url = 'images/pkg/' . $file_name;
        }
    }

    $itinerary_data = [];
    if (isset($_POST['day_title']) && is_array($_POST['day_title'])) {
        foreach ($_POST['day_title'] as $key => $val) {
            $dayTitle = trim((string) $val);
                        $dayDesc = trim((string) ($_POST['day_desc'][$key] ?? ''));
            $dayCoords = trim((string) ($_POST['day_coords'][$key] ?? ''));
            if ($dayTitle === '' && $dayDesc === '') {
                continue;
            }

            $itinerary_data[] = [
                'day' => count($itinerary_data) + 1,
                'title' => $dayTitle,
                                'desc' => $dayDesc,
                'coords' => $dayCoords
            ];
        }
    }
    $itinerary_json = json_encode($itinerary_data);

    $update_fields = "title=?, slug=?, destination=?, price=?, original_price=?, image_url=?, is_active=?, is_international=?, is_trending=?, itinerary=?, tour_code=?, tour_type=?, days=?, nights=?, highlights=?, description_rich=?, rating_score=?, rating_count=?, inclusions=?, exclusions=?, use_destination_terms=?, itinerary_heading=?, package_type=?, hotel_details=?, vehicle_details=?, meal_plan_details=?";
    $insert_cols = "title, slug, destination, price, original_price, image_url, is_active, is_international, is_trending, itinerary, tour_code, tour_type, days, nights, highlights, description_rich, rating_score, rating_count, inclusions, exclusions, use_destination_terms, itinerary_heading, package_type, hotel_details, vehicle_details, meal_plan_details";
    $insert_placeholders = "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
    
    $values = [$title, $slug, $destination, $price, $original_price, $image_url, $is_active, $is_international, $is_trending, $itinerary_json, $tour_code, $tour_type, $days, $nights, $highlights, $description_rich, $rating_score, $rating_count, $inclusions, $exclusions, $use_destination_terms, $itinerary_heading, $package_type, $hotel_details, $vehicle_details, $meal_plan_details];

    if ($hasMapCoords) {
        $update_fields .= ", map_coords=?";
        $insert_cols .= ", map_coords";
        $insert_placeholders .= ", ?";
        $values[] = $map_coords;
    }

    if ($id > 0) {
        $values[] = $id;
        $stmt = $pdo->prepare("UPDATE packages SET $update_fields WHERE id=?");
        $stmt->execute($values);
    } else {
        $stmt = $pdo->prepare("INSERT INTO packages ($insert_cols) VALUES ($insert_placeholders)");
        $stmt->execute($values);
    }

    header('Location: packages.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Package | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header admin-page-header">
                <div class="header-title-wrapper">
                    <a href="packages.php" class="back-arrow" title="Back to Packages">←</a>
                    <h1 class="header-title"><?php echo $id ? 'Edit' : 'Create'; ?> <span class="accent">Package</span></h1>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="admin-form-layout">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">


                <!-- Panel 1: Basic Info -->
                <div class="form-card">
                    <h3 class="form-panel-title">1. Basic Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Package Title</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($pkg['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Slug (URL Identifier)</label>
                            <input type="text" name="slug" value="<?php echo htmlspecialchars($pkg['slug']); ?>" placeholder="auto-generated if empty">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Destination</label>
                            <select name="destination">
                                <option value="">-- Select Destination --</option>
                                <?php foreach ($destinations_list as $d): ?>
                                <option value="<?php echo htmlspecialchars($d); ?>" <?php echo ($pkg['destination'] ?? '') === $d ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($d); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Package Type</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="package_type" value="curated" <?php echo ($pkg['package_type'] === 'curated' || empty($pkg['package_type'])) ? 'checked' : ''; ?> class="input-radio"> Curated Experience
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="package_type" value="fixed" <?php echo ($pkg['package_type'] === 'fixed') ? 'checked' : ''; ?> class="input-radio"> Fixed Departure
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Package Card Settings -->
                <div class="form-card">
                    <h3 class="form-panel-title">2. Package Listing Card Settings</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Starting Price (INR)</label>
                            <input type="number" name="price" value="<?php echo htmlspecialchars((string) $pkg['price']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Original Price (INR - crossed out)</label>
                            <input type="number" name="original_price" value="<?php echo htmlspecialchars((string) ($pkg['original_price'] ?? '')); ?>" placeholder="e.g. 14000">
                        </div>
                    </div>
                    <div class="form-row form-row-2col">
                        <div class="form-group">
                            <label>Duration (Nights)</label>
                            <input type="number" name="nights" value="<?php echo htmlspecialchars((string) ($pkg['nights'] ?? '')); ?>" placeholder="e.g. 5">
                        </div>
                        <div class="form-group">
                            <label>Duration (Days)</label>
                            <input type="number" name="days" value="<?php echo htmlspecialchars((string) ($pkg['days'] ?? '')); ?>" placeholder="e.g. 6">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tour Type / Category Tags</label>
                            <input type="text" name="tour_type" value="<?php echo htmlspecialchars($pkg['tour_type'] ?? ''); ?>" placeholder="e.g. Honeymoon Tours, Hill station Tours">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Card Image URL <span class="label-hint">— shown on the packages listing grid</span></label>
                            <input type="text" name="image_url" value="<?php echo htmlspecialchars($pkg['image_url']); ?>" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label>Upload Card Image <span class="label-hint">— replaces URL above if uploaded</span></label>
                            <input type="file" name="package_image" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Overview & Map -->
                <div class="form-card">
                    <h3 class="form-panel-title">3. Detail Page: Overview & Map</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tour Code</label>
                            <input type="text" name="tour_code" value="<?php echo htmlspecialchars($pkg['tour_code'] ?? ''); ?>" placeholder="e.g. VD-0334">
                        </div>
                        <?php if ($hasMapCoords): ?>
                        <div class="form-group">
                            <label>Map Location (Lat, Long)</label>
                            <input type="text" name="map_coords" value="<?php echo htmlspecialchars($pkg['map_coords']); ?>" placeholder="27.3314, 88.6138">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Overview / Rich Description</label>
                        
<label for="input_410e830f" class="sr-only">Write a rich description of this package...</label>
<textarea id="input_410e830f" name="description_rich" rows="8" placeholder="Write a rich description of this package..."><?php echo htmlspecialchars($pkg['description_rich'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Panel 4: Highlights -->
                <div class="form-card">
                    <h3 class="form-panel-title">4. Detail Page: The Experience</h3>
                    <div class="form-group">
                        <label>Tour Highlights <span class="label-hint">— one highlight per line (shown below trust badges)</span></label>
                        
<label for="input_47e02269" class="sr-only">Jungle Jeep Safari&#10;Murti River, View Point Visit&#10;Khayerbari Interpretation Centre Visit&#10;Gorumara National Park&#10;Night stay at Lataguri</label>
<textarea id="input_47e02269" name="highlights" rows="5" placeholder="Jungle Jeep Safari&#10;Murti River, View Point Visit&#10;Khayerbari Interpretation Centre Visit&#10;Gorumara National Park&#10;Night stay at Lataguri"><?php echo htmlspecialchars($pkg['highlights'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Panel 5: Logistics & Fine Print -->
                <div class="form-card">
                    <h3 class="form-panel-title">5. Logistics, Fine Print & Ratings</h3>
                    <div class="form-row form-row-2col">
                        <div class="form-group">
                            <label>Customer Rating Score</label>
                            <input type="number" step="0.1" max="5" name="rating_score" value="<?php echo htmlspecialchars((string) ($pkg['rating_score'] ?? '4.8')); ?>" placeholder="e.g. 4.8">
                        </div>
                        <div class="form-group">
                            <label>Customer Rating Count</label>
                            <input type="number" name="rating_count" value="<?php echo htmlspecialchars((string) ($pkg['rating_count'] ?? '150')); ?>" placeholder="e.g. 150">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Inclusions (One per line)</label>
                            
<label for="input_893be59c" class="sr-only">Ultra-Premium Boutique Accommodations&#10;Private Luxury Saloon Transportation...</label>
<textarea id="input_893be59c" name="inclusions" rows="4" placeholder="Ultra-Premium Boutique Accommodations&#10;Private Luxury Saloon Transportation..."><?php echo htmlspecialchars($pkg['inclusions'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Exclusions (One per line)</label>
                            
<label for="input_19fd8fb6" class="sr-only">Inter-state Flight & Transit Tickets...</label>
<textarea id="input_19fd8fb6" name="exclusions" rows="4" placeholder="Inter-state Flight & Transit Tickets..."><?php echo htmlspecialchars($pkg['exclusions'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div id="fixed-departure-fields" class="conditional-fields">
                        <h4 class="conditional-fields-title">Fixed Departure Specifics</h4>
                        <div class="form-group">
                            <label>Hotel Details (HTML allowed) <span class="label-hint">(Used for Fixed Departures)</span></label>
                            
<label for="input_2d66f0cd" class="sr-only">e.g. <b>Le Meridien</b> - 3 Nights...</label>
<textarea id="input_2d66f0cd" name="hotel_details" rows="3" placeholder="e.g. <b>Le Meridien</b> - 3 Nights..."><?php echo htmlspecialchars($pkg['hotel_details'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Vehicle Details (HTML allowed)</label>
                            
<label for="input_7fccc540" class="sr-only">e.g. Luxury SUV for all transfers...</label>
<textarea id="input_7fccc540" name="vehicle_details" rows="3" placeholder="e.g. Luxury SUV for all transfers..."><?php echo htmlspecialchars($pkg['vehicle_details'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meal Plan Details (HTML allowed)</label>
                            
<label for="input_29e7ca65" class="sr-only">e.g. Breakfast and Dinner included...</label>
<textarea id="input_29e7ca65" name="meal_plan_details" rows="3" placeholder="e.g. Breakfast and Dinner included..."><?php echo htmlspecialchars($pkg['meal_plan_details'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Panel 6: Publishing Controls -->
                <div class="form-card">
                    <h3 class="form-panel-title">6. Publishing Controls</h3>
                    <div class="form-group checkbox-group-col">
                        <label class="checkbox-label">
                            <input type="checkbox" name="use_destination_terms" <?php echo (!isset($pkg['use_destination_terms']) || $pkg['use_destination_terms']) ? 'checked' : ''; ?> class="input-checkbox">
                            Show Destination Terms & Conditions on Package Detail Page
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" <?php echo $pkg['is_active'] ? 'checked' : ''; ?> class="input-checkbox">
                            Active (published on website)
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_international" <?php echo !empty($pkg['is_international']) ? 'checked' : ''; ?> class="input-checkbox">
                            Is International Package?
                        </label>
                        <label class="checkbox-label-highlight">
                            <input type="checkbox" name="is_trending" <?php echo !empty($pkg['is_trending']) ? 'checked' : ''; ?> class="input-checkbox">
                            🔥 Showcase in "Top Trending Tours" section
                        </label>
                    </div>
                </div>

                <!-- Panel 7: Itinerary Builder -->
                <div class="form-card">
                    <h3 class="form-panel-title">7. Itinerary Builder</h3>
                    <div class="form-group">
                        <label>Itinerary Section Heading</label>
                        <input type="text" name="itinerary_heading" value="<?php echo htmlspecialchars($pkg['itinerary_heading'] ?? 'Day-by-Day Journey'); ?>">
                    </div>
                    <div id="itinerary-container" class="mt-4">
                        <?php foreach ($pkg['itinerary'] as $index => $day): ?>
                        <div class="itinerary-item">
                            <span class="btn-remove" data-action="remove-parent">Remove</span>
                            <div class="form-group">
                                <label>Day <?php echo $index + 1; ?> Title</label>
                                <input type="text" name="day_title[]" value="<?php echo htmlspecialchars($day['title'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Activities / Description</label>
                                <textarea name="day_desc[]" rows="3"><?php echo htmlspecialchars($day['desc'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>📍 GPS Coordinates (Lat, Lng) — for animated map</label>
                                <input type="text" name="day_coords[]" value="<?php echo htmlspecialchars($day['coords'] ?? ''); ?>" placeholder="e.g. 27.3314, 88.6138">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-day" class="btn-outline w-full">+ Add Day</button>
                </div>

                <div class="form-card form-card-transparent">
                    <button type="submit" class="btn-primary btn-block-large">Save Package</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
    
</body>
</html>

