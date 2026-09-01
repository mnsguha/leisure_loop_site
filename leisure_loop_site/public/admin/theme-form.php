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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .sidebar-nav { list-style: none; margin-top: 3rem; }
        .sidebar-nav a { text-decoration: none; color: var(--text-muted); padding: 0.75rem 1rem; border-radius: 12px; display: block; transition: 0.3s; }
        .sidebar-nav a.active, .sidebar-nav a:hover { background: var(--glass); color: white; }
        .main-content { padding: 3rem; background: #0f172a; }
        
        .form-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 800px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            padding: 0.9rem 1.2rem;
            color: white;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 10px rgba(197, 160, 89, 0.2);
        }
        .form-feedback {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .feedback-error {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .feedback-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .btn-submit {
            background: var(--gold);
            color: black;
            border: none;
            border-radius: 50px;
            padding: 1rem 3rem;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(197, 160, 89, 0.4);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2.5rem;">
                <a href="themes.php" style="color: var(--gold); text-decoration: none; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; margin-bottom: 1rem;">
                    <span>&larr;</span> Back to Themes
                </a>
                <h1><?php echo $id ? 'Edit' : 'Create'; ?> Curated <span class="accent">Theme</span></h1>
            </div>

            <?php if (!empty($error)): ?>
                <div class="form-feedback feedback-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="form-feedback feedback-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-card" enctype="multipart/form-data">
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
                    <textarea name="icon_svg" rows="6" placeholder="Paste your custom raw XML SVG markup here..."><?php echo htmlspecialchars($theme['icon_svg'] ?? ''); ?></textarea>
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
