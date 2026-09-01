<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$theme = [
    'name' => '',
    'tagline' => '',
    'image_url' => '',
    'icon_svg' => '',
    'display_order' => 10,
    'is_active' => 1
];

if ($id > 0 && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM tour_categories WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $theme = $res;
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $icon_svg = trim($_POST['icon_svg'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 10);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '') {
        $error = 'Theme / Category Name is required.';
    } else {
        // Handle Backdrop File Upload
        if (isset($_FILES['theme_image']) && $_FILES['theme_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['theme_image']['tmp_name'];
            $file_name = time() . '_theme_' . basename($_FILES['theme_image']['name']);
            $upload_dir = '../assets/img/packages/'; // store in unified package/theme assets folder
            $dest_path = $upload_dir . $file_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Save path relative to root directory (matches how image URLs are loaded)
                $image_url = 'assets/img/packages/' . $file_name;
            }
        }

        // If no image URL is provided and no file uploaded, assign a default scenic fallback
        if ($image_url === '') {
            $image_url = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
        }

        try {
            if ($id > 0) {
                // Update
                $stmt = $pdo->prepare("UPDATE tour_categories SET name = ?, tagline = ?, image_url = ?, icon_svg = ?, display_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $tagline, $image_url, $icon_svg, $display_order, $is_active, $id]);
                $success = 'Theme updated successfully!';
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $tagline, $image_url, $icon_svg, $display_order, $is_active]);
                $success = 'Theme created successfully!';
                header('Location: themes.php');
                exit;
            }
            
            // Reload updated values
            $stmt = $pdo->prepare("SELECT * FROM tour_categories WHERE id = ?");
            $stmt->execute([$id]);
            $theme = $stmt->fetch();
        } catch (Exception $e) {
            $error = 'Database Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit Theme' : 'Create Theme'; ?> | Leisure Loop Admin</title>
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
            <div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="themes.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Themes">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Create'; ?> Curated <span class="accent">Theme</span></h1>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="form-feedback feedback-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="form-feedback feedback-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-card" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Theme Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($theme['name']); ?>" placeholder="e.g. Honeymoon, Adventure" required>
                        <span style="color:#64748b; font-size:0.75rem;">Must match the tags specified in package editing tags.</span>
                    </div>
                    <div class="form-group">
                        <label>Branding Tagline</label>
                        <input type="text" name="tagline" value="<?php echo htmlspecialchars($theme['tagline'] ?? ''); ?>" placeholder="e.g. Infinite Tranquility, Alpine Lakes">
                        <span style="color:#64748b; font-size:0.75rem;">Premium luxury description tagline shown on home page theme cards.</span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Theme Backdrop URL</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($theme['image_url']); ?>" placeholder="https://images.unsplash.com/photo-...">
                        <span style="color:#64748b; font-size:0.75rem;">Paste any high-resolution image link directly.</span>
                    </div>
                    <div class="form-group">
                        <label>Upload Backdrop Image</label>
                        <input type="file" name="theme_image" accept="image/*" style="color:#94a3b8; font-size: 0.9rem;">
                        <span style="color:#64748b; font-size:0.75rem;">Optionally upload a picture from your local disk.</span>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label>Display Order Priority</label>
                        <input type="number" name="display_order" value="<?php echo htmlspecialchars((string)$theme['display_order']); ?>" placeholder="e.g. 1 (first), 10 (default)" required>
                        <span style="color:#64748b; font-size:0.75rem;">Lower values display first (e.g. Leisure set to 1 sits at the very beginning!).</span>
                    </div>
                    <div class="form-group" style="justify-content: center; align-items: flex-start; padding-top: 1.5rem;">
                        <label style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer; text-transform: none; color:#fff; font-size: 0.95rem;">
                            <input type="checkbox" name="is_active" <?php echo $theme['is_active'] ? 'checked' : ''; ?> style="width:18px; height:18px; accent-color: var(--gold);">
                            Enable Curation Card on Main Page
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Minimalist SVG Outline Icon Markup</label>
                    
<label for="input_bfd6f82d" class="sr-only">Paste your custom raw XML SVG markup here...</label>
<textarea id="input_bfd6f82d" name="icon_svg" rows="6" placeholder="Paste your custom raw XML SVG markup here..."><?php echo htmlspecialchars($theme['icon_svg'] ?? ''); ?></textarea>
                    <span style="color:#64748b; font-size:0.75rem; line-height: 1.4;">
                        Please paste standard vector markup containing only strokes (e.g., <code>&lt;svg viewBox="0 0 24 24" fill="none" stroke="currentColor"&gt;...&lt;/svg&gt;</code>).<br>
                        <em>Leave empty to fallback to our premium gold location compass pin!</em>
                    </span>
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-submit">
                        <?php echo $id ? 'Save Theme Settings' : 'Create Theme'; ?>
                    </button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
