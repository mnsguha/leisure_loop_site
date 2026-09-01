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
    'image_url' => '',
    'is_active' => 1,
    'itinerary' => [],
    'map_coords' => ''
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
    $image_url = trim($_POST['image_url'] ?? '');
    $map_coords = trim($_POST['map_coords'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

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

    $itinerary_data = [];
    if (isset($_POST['day_title']) && is_array($_POST['day_title'])) {
        foreach ($_POST['day_title'] as $key => $val) {
            $dayTitle = trim((string) $val);
            $dayDesc = trim((string) ($_POST['day_desc'][$key] ?? ''));
            if ($dayTitle === '' && $dayDesc === '') {
                continue;
            }

            $itinerary_data[] = [
                'day' => count($itinerary_data) + 1,
                'title' => $dayTitle,
                'desc' => $dayDesc
            ];
        }
    }
    $itinerary_json = json_encode($itinerary_data);

    $hasDestCol = $pdo ? tableHasColumn($pdo, 'packages', 'destination') : false;
    if ($id > 0) {
        if ($hasMapCoords && $hasDestCol) {
            $stmt = $pdo->prepare("UPDATE packages SET title=?, slug=?, destination=?, price=?, image_url=?, is_active=?, itinerary=?, map_coords=? WHERE id=?");
            $stmt->execute([$title, $slug, $destination, $price, $image_url, $is_active, $itinerary_json, $map_coords, $id]);
        } elseif ($hasDestCol) {
            $stmt = $pdo->prepare("UPDATE packages SET title=?, slug=?, destination=?, price=?, image_url=?, is_active=?, itinerary=? WHERE id=?");
            $stmt->execute([$title, $slug, $destination, $price, $image_url, $is_active, $itinerary_json, $id]);
        } elseif ($hasMapCoords) {
            $stmt = $pdo->prepare("UPDATE packages SET title=?, slug=?, price=?, image_url=?, is_active=?, itinerary=?, map_coords=? WHERE id=?");
            $stmt->execute([$title, $slug, $price, $image_url, $is_active, $itinerary_json, $map_coords, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE packages SET title=?, slug=?, price=?, image_url=?, is_active=?, itinerary=? WHERE id=?");
            $stmt->execute([$title, $slug, $price, $image_url, $is_active, $itinerary_json, $id]);
        }
    } else {
        if ($hasMapCoords && $hasDestCol) {
            $stmt = $pdo->prepare("INSERT INTO packages (title, slug, destination, price, image_url, is_active, itinerary, map_coords) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $destination, $price, $image_url, $is_active, $itinerary_json, $map_coords]);
        } elseif ($hasDestCol) {
            $stmt = $pdo->prepare("INSERT INTO packages (title, slug, destination, price, image_url, is_active, itinerary) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $destination, $price, $image_url, $is_active, $itinerary_json]);
        } elseif ($hasMapCoords) {
            $stmt = $pdo->prepare("INSERT INTO packages (title, slug, price, image_url, is_active, itinerary, map_coords) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $price, $image_url, $is_active, $itinerary_json, $map_coords]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO packages (title, slug, price, image_url, is_active, itinerary) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $price, $image_url, $is_active, $itinerary_json]);
        }
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

                <div class="form-row">
                    <div class="form-group">
                        <label>Main Image URL</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($pkg['image_url']); ?>" placeholder="https://...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Upload New Image</label>
                        <input type="file" name="package_image" accept="image/*">
                    </div>
                    <?php if ($hasMapCoords): ?>
                    <div class="form-group">
                        <label>Map Location (Lat, Long)</label>
                        <input type="text" name="map_coords" value="<?php echo htmlspecialchars($pkg['map_coords']); ?>" placeholder="27.3314, 88.6138">
                    </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
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
            `;
            container.appendChild(div);
        });
    </script>
</body>
</html>
