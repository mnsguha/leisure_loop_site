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
    'use_destination_terms' => 1,
    'itinerary' => [],
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
    'exclusions' => ''
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
    $image_url = trim($_POST['image_url'] ?? '');
    $banner_image_url = trim($_POST['banner_image_url'] ?? '');
    $map_coords = trim($_POST['map_coords'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $use_destination_terms = isset($_POST['use_destination_terms']) ? 1 : 0;
    
    $tour_code = trim($_POST['tour_code'] ?? '');
    $tour_type = trim($_POST['tour_type'] ?? '');
    $days = trim($_POST['days'] ?? '') !== '' ? (int) $_POST['days'] : null;
    $nights = trim($_POST['nights'] ?? '') !== '' ? (int) $_POST['nights'] : null;
    $highlights = trim($_POST['highlights'] ?? '');
    $description_rich = trim($_POST['description_rich'] ?? '');
    $photos = trim($_POST['photos'] ?? '');
    $rating_score = trim($_POST['rating_score'] ?? '') !== '' ? (float) $_POST['rating_score'] : 4.8;
    $rating_count = trim($_POST['rating_count'] ?? '') !== '' ? (int) $_POST['rating_count'] : 150;
    $inclusions = trim($_POST['inclusions'] ?? '');
    $exclusions = trim($_POST['exclusions'] ?? '');

    if (isset($_FILES['package_image']) && $_FILES['package_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['package_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['package_image']['name']);
        $upload_dir = '../assets/img/packages/';
        $dest_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $image_url = 'assets/img/packages/' . $file_name;
        }
    }

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['banner_image']['tmp_name'];
        $file_name = time() . '_banner_' . basename($_FILES['banner_image']['name']);
        $upload_dir = '../assets/img/packages/';
        $dest_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $banner_image_url = 'assets/img/packages/' . $file_name;
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

    $update_fields = "title=?, slug=?, destination=?, price=?, original_price=?, image_url=?, banner_image_url=?, is_active=?, itinerary=?, tour_code=?, tour_type=?, days=?, nights=?, highlights=?, description_rich=?, photos=?, rating_score=?, rating_count=?, inclusions=?, exclusions=?, use_destination_terms=?";
    $insert_cols = "title, slug, destination, price, original_price, image_url, banner_image_url, is_active, itinerary, tour_code, tour_type, days, nights, highlights, description_rich, photos, rating_score, rating_count, inclusions, exclusions, use_destination_terms";
    $insert_placeholders = "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
    
    $values = [$title, $slug, $destination, $price, $original_price, $image_url, $banner_image_url, $is_active, $itinerary_json, $tour_code, $tour_type, $days, $nights, $highlights, $description_rich, $photos, $rating_score, $rating_count, $inclusions, $exclusions, $use_destination_terms];

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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; }
        .form-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 2.5rem; border-radius: 24px; max-width: 800px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: white;
            outline: none;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--accent); }
        .itinerary-item { background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); padding: 1.5rem; border-radius: 16px; margin-bottom: 1rem; position: relative; }
        .btn-remove { position: absolute; top: 1rem; right: 1rem; color: #f87171; cursor: pointer; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem;">
                <a href="packages.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem;">&larr; Back to Packages</a>
                <h1 style="margin-top: 1rem;"><?php echo $id ? 'Edit' : 'Create'; ?> <span class="accent">Package</span></h1>
            </div>

            <form method="POST" class="form-card" enctype="multipart/form-data">
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
                        <label>Starting Price (INR)</label>
                        <input type="number" name="price" value="<?php echo htmlspecialchars((string) $pkg['price']); ?>" required>
                    </div>
                </div>


                <!-- Card / Thumbnail Image -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Card Image URL <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— shown on the packages listing grid</span></label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($pkg['image_url']); ?>" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Upload Card Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— replaces URL above if uploaded</span></label>
                        <input type="file" name="package_image" accept="image/*">
                    </div>
                </div>

                <!-- Hero Banner Image -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Banner Image URL <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— full-width hero at top of package detail page</span></label>
                        <input type="text" name="banner_image_url" value="<?php echo htmlspecialchars($pkg['banner_image_url'] ?? ''); ?>" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Upload Banner Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— replaces URL above if uploaded</span></label>
                        <input type="file" name="banner_image" accept="image/*">
                    </div>
                </div>


                <?php if ($hasMapCoords): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Map Location (Lat, Long)</label>
                        <input type="text" name="map_coords" value="<?php echo htmlspecialchars($pkg['map_coords']); ?>" placeholder="27.3314, 88.6138">
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tour Code</label>
                        <input type="text" name="tour_code" value="<?php echo htmlspecialchars($pkg['tour_code'] ?? ''); ?>" placeholder="e.g. VD-0334">
                    </div>
                    <div class="form-group">
                        <label>Tour Type / Category Tags</label>
                        <input type="text" name="tour_type" value="<?php echo htmlspecialchars($pkg['tour_type'] ?? ''); ?>" placeholder="e.g. Honeymoon Tours, Hill station Tours">
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label>Duration (Nights)</label>
                        <input type="number" name="nights" value="<?php echo htmlspecialchars((string) ($pkg['nights'] ?? '')); ?>" placeholder="e.g. 5">
                    </div>
                    <div class="form-group">
                        <label>Duration (Days)</label>
                        <input type="number" name="days" value="<?php echo htmlspecialchars((string) ($pkg['days'] ?? '')); ?>" placeholder="e.g. 6">
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr;">
                    <div class="form-group">
                        <label>Original Price (INR - crossed out)</label>
                        <input type="number" name="original_price" value="<?php echo htmlspecialchars((string) ($pkg['original_price'] ?? '')); ?>" placeholder="e.g. 14000">
                    </div>
                    <div class="form-group">
                        <label>Customer Rating Score</label>
                        <input type="number" step="0.1" max="5" name="rating_score" value="<?php echo htmlspecialchars((string) ($pkg['rating_score'] ?? '4.8')); ?>" placeholder="e.g. 4.8">
                    </div>
                    <div class="form-group">
                        <label>Customer Rating Count</label>
                        <input type="number" name="rating_count" value="<?php echo htmlspecialchars((string) ($pkg['rating_count'] ?? '150')); ?>" placeholder="e.g. 150">
                    </div>
                </div>

                                <div class="form-group">
                    <label>Overview <span style="color:#64748b;font-weight:400;font-size:0.8rem;">� shown at the top of the package detail page</span></label>
                    <textarea name="description_rich" rows="8" placeholder="Write a rich description of this package..."><?php echo htmlspecialchars($pkg['description_rich'] ?? ''); ?></textarea>

                <div class="form-group" style="margin-top:1.5rem;">
                    <label>Tour Highlights <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— one highlight per line (shown below trust badges)</span></label>
                    <textarea name="highlights" rows="5" placeholder="Jungle Jeep Safari&#10;Murti River, View Point Visit&#10;Khayerbari Interpretation Centre Visit&#10;Gorumara National Park&#10;Night stay at Lataguri"><?php echo htmlspecialchars($pkg['highlights'] ?? ''); ?></textarea>
                </div>
                </div>

                <div class="form-group">
                    <label>Visual Journal Photos (Comma-separated image URLs)</label>
                    <textarea name="photos" rows="3" placeholder="https://images.unsplash.com/photo-1..., https://images.unsplash.com/photo-2..."><?php echo htmlspecialchars($pkg['photos'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Luxury Inclusions (One per line)</label>
                        <textarea name="inclusions" rows="4" placeholder="Ultra-Premium Boutique Accommodations&#10;Private Luxury Saloon Transportation&#10;Bespoke Professional Experience Host&#10;Curated Dining Grid Permitted Options&#10;VIP Permits & Priority Entry Accents"><?php echo htmlspecialchars($pkg['inclusions'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Exclusions (One per line)</label>
                        <textarea name="exclusions" rows="4" placeholder="Inter-state Flight & Transit Tickets&#10;Personal Discretionary Expenses&#10;Gratuities and Driver Incentives"><?php echo htmlspecialchars($pkg['exclusions'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="use_destination_terms" <?php echo (!isset($pkg['use_destination_terms']) || $pkg['use_destination_terms']) ? 'checked' : ''; ?> style="width: auto;">
                        Show Destination Terms & Conditions on Package Detail Page
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo $pkg['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        Active (published on website)
                    </label>
                </div>

                <div style="margin-top: 3rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Day-wise Itinerary</h3>
                    <div id="itinerary-container">
                        <?php foreach ($pkg['itinerary'] as $index => $day): ?>
                        <div class="itinerary-item">
                            <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
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
                    <button type="button" id="add-day" class="btn-outline" style="width: 100%;">+ Add Day</button>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 1.25rem;">Save Package</button>
            </form>
        </main>
    </div>

    <script>
        document.getElementById('add-day').addEventListener('click', () => {
            const container = document.getElementById('itinerary-container');
            const dayNum = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'itinerary-item';
            div.innerHTML = `
                <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
                <div class="form-group">
                    <label>Day ${dayNum} Title</label>
                    <input type="text" name="day_title[]" required>
                </div>
                <div class="form-group">
                    <label>Activities / Description</label>
                    <textarea name="day_desc[]" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>📍 GPS Coordinates (Lat, Lng) — for animated map</label>
                    <input type="text" name="day_coords[]" placeholder="e.g. 27.3314, 88.6138">
                </div>
            `;
            container.appendChild(div);
        });
    </script>
</body>
</html>

