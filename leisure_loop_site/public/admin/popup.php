<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $title = trim($_POST['title'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    // Handle Image Upload
    if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['popup_image']['tmp_name'];
        $file_name = time() . '_popup_' . basename($_FILES['popup_image']['name']);
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
        $stmt = $pdo->prepare("UPDATE popup_settings SET is_active = ?, title = ?, image_url = ? WHERE id = 1");
        $stmt->execute([$is_active, $title, $image_url]);
        $msg = "Popup settings updated successfully.";
    } catch (Exception $e) {
        $msg = "Error updating settings: " . $e->getMessage();
    }
}

// Fetch current settings
$settings = [
    'is_active' => 0,
    'title' => 'Book Your Tour Now !!',
    'image_url' => ''
];

if ($pdo) {
    $res = $pdo->query("SELECT * FROM popup_settings WHERE id = 1")->fetch();
    if ($res) {
        $settings = $res;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Popup | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; }
        .form-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 2.5rem; border-radius: 24px; max-width: 800px; }
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
        .form-group input:focus, .form-group textarea:focus { border-color: var(--accent); }
        .msg { padding: 1rem; background: rgba(197, 160, 89, 0.1); border: 1px solid var(--accent); color: var(--accent); border-radius: 8px; margin-bottom: 1.5rem; }
        .img-preview {
            max-width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            margin-top: 1rem;
            border: 1px solid var(--glass-border);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem;">Manage <span class="accent">Notice Popup</span></h1>

            <?php if ($msg): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <form method="POST" class="form-card" enctype="multipart/form-data">
                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" <?php echo $settings['is_active'] ? 'checked' : ''; ?> style="width: auto; cursor: pointer;">
                    <label style="margin: 0; cursor: pointer; color: white; font-size: 1.1rem;">Enable Notice Popup (Show on frontend)</label>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0;">

                <div class="form-group">
                    <label>Popup Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($settings['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Advertisement Image URL</label>
                    <input type="text" name="image_url" value="<?php echo htmlspecialchars($settings['image_url']); ?>" placeholder="https://...">
                    <?php if (!empty($settings['image_url'])): ?>
                        <div style="margin-top: 10px;">
                            <img src="<?php echo htmlspecialchars(strpos($settings['image_url'], 'http') === 0 ? $settings['image_url'] : '../' . $settings['image_url']); ?>" class="img-preview" alt="Ad Preview">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Upload New Advertisement Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">— replaces URL above if uploaded</span></label>
                    <input type="file" name="popup_image" accept="image/*">
                </div>

                <button type="submit" class="btn-primary" style="padding: 1rem 2rem;">Save Settings</button>
            </form>
        </main>
    </div>
</body>
</html>
