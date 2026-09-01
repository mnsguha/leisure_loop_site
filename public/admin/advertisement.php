<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if ($pdo) {
    try {
        // Auto-create table if not exists (Self-Healing)
        $pdo->exec("CREATE TABLE IF NOT EXISTS advertisements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_type VARCHAR(50) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            btn_text VARCHAR(100) NOT NULL,
            btn_link VARCHAR(255) NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed with default rows if missing (Self-Healing & Extensible Seeding)
        $existing = $pdo->query("SELECT page_type FROM advertisements")->fetchAll(PDO::FETCH_COLUMN);
        
        $seeds = [
            'home' => ['Grab Exciting Offers on Pre-Booking', 'Book Now', '#', '', 1],
            'package' => ['Grab Exciting Offers on Pre-Booking', 'Book Now', '#', '', 1],
            'home_middle' => ['Unlock Bespoke Privileges on Premium Curations', 'Enquire Now', '#contact', '', 1],
            'home_bottom' => ['Unlock Bespoke Privileges on Premium Curations', 'Enquire Now', '#contact', '', 1]
        ];

        foreach ($seeds as $ptype => $data) {
            if (!in_array($ptype, $existing)) {
                $stmt = $pdo->prepare("INSERT INTO advertisements (page_type, title, btn_text, btn_link, image_url, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$ptype, $data[0], $data[1], $data[2], $data[3], $data[4]]);
            }
        }
    } catch (PDOException $e) {
        $msg = "Database Auto-Setup failed: " . $e->getMessage();
    }
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';
    $page_type = '';
    if ($action === 'save_home') {
        $page_type = 'home';
    } elseif ($action === 'save_package') {
        $page_type = 'package';
    } elseif ($action === 'save_home_middle') {
        $page_type = 'home_middle';
    } elseif ($action === 'save_home_bottom') {
        $page_type = 'home_bottom';
    }

    if ($page_type) {
        $title = trim($_POST['title'] ?? '');
        $btn_text = trim($_POST['btn_text'] ?? 'Book Now');
        $btn_link = trim($_POST['btn_link'] ?? '#');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $image_url = trim($_POST['image_url'] ?? '');

        // Handle Image Upload
        $file_key = 'banner_image_' . $page_type;
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$file_key]['tmp_name'];
            $file_name = time() . '_ad_' . $page_type . '_' . basename($_FILES[$file_key]['name']);
            $upload_dir = '../assets/img/';
            $dest_path = $upload_dir . $file_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $dest_path)) {
                $image_url = 'assets/img/' . $file_name;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE advertisements SET title = ?, btn_text = ?, btn_link = ?, image_url = ?, is_active = ? WHERE page_type = ?");
            $stmt->execute([$title, $btn_text, $btn_link, $image_url, $is_active, $page_type]);
            $msg = "Dynamic settings for " . str_replace('_', ' ', $page_type) . " updated successfully.";
        } catch (Exception $e) {
            $msg = "Error updating settings: " . $e->getMessage();
        }
    }
}

// Fetch current settings
$home_ad = [
    'title' => 'Grab Exciting Offers on Pre-Booking',
    'btn_text' => 'Book Now',
    'btn_link' => '#',
    'image_url' => '',
    'is_active' => 1
];
$package_ad = [
    'title' => 'Grab Exciting Offers on Pre-Booking',
    'btn_text' => 'Book Now',
    'btn_link' => '#',
    'image_url' => '',
    'is_active' => 1
];
$home_middle_ad = [
    'title' => 'Unlock Bespoke Privileges on Premium Curations',
    'btn_text' => 'Enquire Now',
    'btn_link' => '#contact',
    'image_url' => '',
    'is_active' => 1
];
$home_bottom_ad = [
    'title' => 'Unlock Bespoke Privileges on Premium Curations',
    'btn_text' => 'Enquire Now',
    'btn_link' => '#contact',
    'image_url' => '',
    'is_active' => 1
];

if ($pdo) {
    try {
        $res = $pdo->query("SELECT * FROM advertisements")->fetchAll();
        foreach ($res as $row) {
            if ($row['page_type'] === 'home') $home_ad = $row;
            if ($row['page_type'] === 'package') $package_ad = $row;
            if ($row['page_type'] === 'home_middle') $home_middle_ad = $row;
            if ($row['page_type'] === 'home_bottom') $home_bottom_ad = $row;
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Banners | Admin</title>
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
            <h1 style="margin-bottom: 2rem; font-family: 'Playfair Display', serif;">Manage <span class="accent">Advertisement Banners</span></h1>

            <?php if ($msg): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="grid-container">
                
                <!-- Home Page Banner Settings -->
                <form method="POST" class="form-card" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="save_home">
                    <h2 class="section-subtitle">Home Page Banner</h2>
                    <p style="color: #64748b; font-size: 0.82rem; margin-top: -1rem; margin-bottom: 2rem;">Shown below the "Signature Terrains" section on the home page.</p>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem;">
                        <input type="checkbox" name="is_active" <?php echo $home_ad['is_active'] ? 'checked' : ''; ?> style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Enable Banner on Home Page</label>
                    </div>

                    <div class="form-group">
                        <label>Main Headline</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($home_ad['title']); ?>" required placeholder="e.g. Grab Exciting Offers on Pre-Booking">
                    </div>

                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="btn_text" value="<?php echo htmlspecialchars($home_ad['btn_text']); ?>" required placeholder="e.g. Book Now">
                    </div>

                    <div class="form-group">
                        <label>Button Link Destination</label>
                        <input type="text" name="btn_link" value="<?php echo htmlspecialchars($home_ad['btn_link']); ?>" placeholder="e.g. #contact or custom page link">
                    </div>

                    <div class="form-group">
                        <label>Current Banner Background Image</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($home_ad['image_url']); ?>" placeholder="Image URL (falls back to premium default if empty)">
                        <?php if (!empty($home_ad['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(strpos($home_ad['image_url'], 'http') === 0 ? $home_ad['image_url'] : '../' . $home_ad['image_url']); ?>" class="img-preview" alt="Home Banner Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Upload New Background Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">(Overrides direct URL path)</span></label>
                        <input type="file" name="banner_image_home" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 1rem 2rem; margin-top: auto; border-radius: 12px; font-weight: 600;">Save Home Settings</button>
                </form>

                <!-- Package Detail Page Banner Settings -->
                <form method="POST" class="form-card" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="save_package">
                    <h2 class="section-subtitle">Package Page Banner</h2>
                    <p style="color: #64748b; font-size: 0.82rem; margin-top: -1rem; margin-bottom: 2rem;">Shown below the collapsible "Terms & Conditions" panel on all packages.</p>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem;">
                        <input type="checkbox" name="is_active" <?php echo $package_ad['is_active'] ? 'checked' : ''; ?> style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Enable Banner on Package Pages</label>
                    </div>

                    <div class="form-group">
                        <label>Main Headline</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($package_ad['title']); ?>" required placeholder="e.g. Grab Exciting Offers on Pre-Booking">
                    </div>

                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="btn_text" value="<?php echo htmlspecialchars($package_ad['btn_text']); ?>" required placeholder="e.g. Book Now">
                    </div>

                    <div class="form-group">
                        <label>Button Link Destination</label>
                        <input type="text" name="btn_link" value="<?php echo htmlspecialchars($package_ad['btn_link']); ?>" placeholder="e.g. #contact or custom page link">
                    </div>

                    <div class="form-group">
                        <label>Current Banner Background Image</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($package_ad['image_url']); ?>" placeholder="Image URL (falls back to premium default if empty)">
                        <?php if (!empty($package_ad['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(strpos($package_ad['image_url'], 'http') === 0 ? $package_ad['image_url'] : '../' . $package_ad['image_url']); ?>" class="img-preview" alt="Package Banner Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Upload New Background Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">(Overrides direct URL path)</span></label>
                        <input type="file" name="banner_image_package" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 1rem 2rem; margin-top: auto; border-radius: 12px; font-weight: 600;">Save Package Settings</button>
                </form>

                <!-- Home Page Middle Banner (Above Journal) -->
                <form method="POST" class="form-card" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="save_home_middle">
                    <h2 class="section-subtitle">Home Middle Banner</h2>
                    <p style="color: #64748b; font-size: 0.82rem; margin-top: -1rem; margin-bottom: 2rem;">Shown right above the "Journal &amp; Insights" blog section on the home page.</p>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem;">
                        <input type="checkbox" name="is_active" <?php echo $home_middle_ad['is_active'] ? 'checked' : ''; ?> style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Enable Banner above Journal</label>
                    </div>

                    <div class="form-group">
                        <label>Main Headline</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($home_middle_ad['title']); ?>" required placeholder="e.g. Unlock Bespoke Privileges on Premium Curations">
                    </div>

                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="btn_text" value="<?php echo htmlspecialchars($home_middle_ad['btn_text']); ?>" required placeholder="e.g. Enquire Now">
                    </div>

                    <div class="form-group">
                        <label>Button Link Destination</label>
                        <input type="text" name="btn_link" value="<?php echo htmlspecialchars($home_middle_ad['btn_link']); ?>" placeholder="e.g. #contact or custom page link">
                    </div>

                    <div class="form-group">
                        <label>Current Banner Background Image</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($home_middle_ad['image_url']); ?>" placeholder="Image URL (falls back to premium default if empty)">
                        <?php if (!empty($home_middle_ad['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(strpos($home_middle_ad['image_url'], 'http') === 0 ? $home_middle_ad['image_url'] : '../' . $home_middle_ad['image_url']); ?>" class="img-preview" alt="Home Middle Banner Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Upload New Background Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">(Overrides direct URL path)</span></label>
                        <input type="file" name="banner_image_home_middle" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 1rem 2rem; margin-top: auto; border-radius: 12px; font-weight: 600;">Save Home Middle Settings</button>
                </form>

                <!-- Home Page Bottom Banner (Above FAQ) -->
                <form method="POST" class="form-card" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="save_home_bottom">
                    <h2 class="section-subtitle">Home Bottom Banner</h2>
                    <p style="color: #64748b; font-size: 0.82rem; margin-top: -1rem; margin-bottom: 2rem;">Shown right above the "Knowledge Base" (FAQ) section on the home page.</p>

                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 2rem;">
                        <input type="checkbox" name="is_active" <?php echo $home_bottom_ad['is_active'] ? 'checked' : ''; ?> style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Enable Banner above FAQ</label>
                    </div>

                    <div class="form-group">
                        <label>Main Headline</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($home_bottom_ad['title']); ?>" required placeholder="e.g. Unlock Bespoke Privileges on Premium Curations">
                    </div>

                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" name="btn_text" value="<?php echo htmlspecialchars($home_bottom_ad['btn_text']); ?>" required placeholder="e.g. Enquire Now">
                    </div>

                    <div class="form-group">
                        <label>Button Link Destination</label>
                        <input type="text" name="btn_link" value="<?php echo htmlspecialchars($home_bottom_ad['btn_link']); ?>" placeholder="e.g. #contact or custom page link">
                    </div>

                    <div class="form-group">
                        <label>Current Banner Background Image</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($home_bottom_ad['image_url']); ?>" placeholder="Image URL (falls back to premium default if empty)">
                        <?php if (!empty($home_bottom_ad['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(strpos($home_bottom_ad['image_url'], 'http') === 0 ? $home_bottom_ad['image_url'] : '../' . $home_bottom_ad['image_url']); ?>" class="img-preview" alt="Home Bottom Banner Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Upload New Background Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">(Overrides direct URL path)</span></label>
                        <input type="file" name="banner_image_home_bottom" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 1rem 2rem; margin-top: auto; border-radius: 12px; font-weight: 600;">Save Home Bottom Settings</button>
                </form>

            </div>
        </main>
    </div>
</body>
</html>
