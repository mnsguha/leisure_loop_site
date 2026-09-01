<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if (!function_exists('cacheAdminHeroVideoLocally')) {
    function cacheAdminHeroVideoLocally(string $url): string
    {
        if (!preg_match('/^https?:\\/\\//i', $url) || !preg_match('/\\.mp4(?:\\?|$)/i', $url)) {
            return $url;
        }

        $uploadDir = '../assets/img/hero/cache/';
        $publicPath = 'assets/img/hero/cache/' . md5($url) . '.mp4';
        $cacheFile = $uploadDir . md5($url) . '.mp4';

        if (is_file($cacheFile) && filesize($cacheFile) > 0) {
            return $publicPath;
        }

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $downloaded = false;

        if (function_exists('curl_init')) {
            $fp = @fopen($cacheFile, 'wb');
            if ($fp) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_FILE => $fp,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_USERAGENT => 'LeisureLoopHeroAdmin/1.0',
                ]);
                $downloaded = curl_exec($ch) !== false;
                curl_close($ch);
                fclose($fp);
            }
        }

        if (!$downloaded && ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 20,
                    'follow_location' => 1,
                    'user_agent' => 'LeisureLoopHeroAdmin/1.0',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $remote = @fopen($url, 'rb', false, $context);
            $local = @fopen($cacheFile, 'wb');
            if ($remote && $local) {
                stream_copy_to_stream($remote, $local);
                fclose($remote);
                fclose($local);
                $downloaded = is_file($cacheFile) && filesize($cacheFile) > 0;
            }
        }

        if (!$downloaded || !is_file($cacheFile) || filesize($cacheFile) === 0) {
            @unlink($cacheFile);
            return $url;
        }

        return $publicPath;
    }
}

// --- DATABASE INITIALIZATION ---
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('image', 'video') NOT NULL,
        url VARCHAR(1000) NOT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    try {
        $pdo->exec("ALTER TABLE hero_slides MODIFY url VARCHAR(1000) NOT NULL");
    } catch (PDOException $e) {
        // Keep the page usable even if the alter is unnecessary on some environments.
    }
}

// --- HANDLE FORM SUBMISSIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? 'save_branding';

    if ($action === 'save_branding') {
        $hero_text_main = $_POST['hero_text_main'];
        $hero_text_sub = $_POST['hero_text_sub'];
        $hero_text_desc = $_POST['hero_text_desc'] ?? '';
        $crm_api_key = $_POST['crm_api_key'] ?? null;
        $crm_url = $_POST['crm_url'] ?? null;

        $stmt = $pdo->prepare("UPDATE settings SET hero_text_main=?, hero_text_sub=?, hero_text_desc=?, crm_api_key=?, crm_url=? WHERE id=1");
        $stmt->execute([$hero_text_main, $hero_text_sub, $hero_text_desc, $crm_api_key, $crm_url]);
        $msg = "All settings updated successfully!";
    } elseif ($action === 'add_slide') {
        $type = $_POST['slide_type'];
        $url = $_POST['slide_url'];
        
        // Handle Upload
        if (isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['slide_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['slide_file']['name']));
            $upload_dir = '../assets/img/hero/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                $url = 'assets/img/hero/' . $file_name;
                $mime = mime_content_type($upload_dir . $file_name);
                $type = str_contains($mime, 'video') ? 'video' : 'image';
            }
        }
        
        if ($url && $type === 'video') {
            $url = cacheAdminHeroVideoLocally($url);
        }

        if ($url) {
            $stmt = $pdo->prepare("INSERT INTO hero_slides (type, url, display_order) VALUES (?, ?, ?)");
            $stmt->execute([$type, $url, (int)($_POST['display_order'] ?? 0)]);
            $msg = "New cinematic slide added!";
        }
    } elseif ($action === 'delete_slide') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Slide removed from hero rotation.";
    }

    // Refresh settings data for the form
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch();
} else {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch();
}

// Fetch all slides
$slides = [];
if ($pdo) {
    $slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero & Branding | Admin</title>
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
            <div class="header" style="margin-bottom: 3rem;">
                <h1>Hero & <span class="accent">Branding</span></h1>
                <p class="muted">Manage your cinematic first impression and global site text.</p>
            </div>

            <?php if ($msg): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <!-- Branding Section -->
            <form method="POST" class="card">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="action" value="save_branding">
                <h3 style="margin-bottom: 1.5rem;">Site Branding</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label>Hero Main Heading (HTML allowed)</label>
                        <input type="text" name="hero_text_main" value="<?php echo htmlspecialchars($settings['hero_text_main'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Hero Sub Tagline</label>
                        <input type="text" name="hero_text_sub" value="<?php echo htmlspecialchars($settings['hero_text_sub'] ?? ''); ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Hero Description</label>
                        <input type="text" name="hero_text_desc" value="<?php echo htmlspecialchars($settings['hero_text_desc'] ?? ''); ?>">
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 2rem 0;">
                
                <h3 style="margin-bottom: 1.5rem;">CRM Integration</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label>CRM API Key</label>
                        <input type="password" name="crm_api_key" value="<?php echo htmlspecialchars($settings['crm_api_key'] ?? ''); ?>" placeholder="Paste key from CRM settings">
                    </div>
                    <div class="form-group">
                        <label>CRM Endpoint URL</label>
                        <input type="text" name="crm_url" value="<?php echo htmlspecialchars($settings['crm_url'] ?? 'https://crm.leisurelooptrip.in/api/leads/create/'); ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn-gold" style="width: 100%; padding: 1rem; margin-top: 1rem;">Update All Settings</button>
            </form>

            <!-- Slide Management -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem;">Add Cinematic Slide</h3>
                <form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="add_slide">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Media Type</label>
                            <select name="slide_type">
                                <option value="image">Static Image</option>
                                <option value="video">Cinematic Video (MP4)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                        <div class="form-group">
                            <label>External URL (Optional)</label>
                            
<label for="input_24914aa2" class="sr-only">https://...</label>
<input id="input_24914aa2" type="text" name="slide_url" placeholder="https://...">
                            <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">For video slides, use a direct file URL ending in `.mp4`. Normal page links from YouTube, Pixabay, or Pexels will not play in the hero.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>OR Upload File (Image/Video)</label>
                        <input type="file" name="slide_file" accept="image/*,video/mp4">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem;">Add to Hero Gallery</button>
                </form>

                <div class="slide-grid">
                    <?php foreach ($slides as $slide): ?>
                    <div class="slide-card">
                        <?php if ($slide['type'] === 'video'): ?>
                            <div class="slide-preview">
                                <video muted style="width: 100%; height: 100%; object-fit: cover;">
                                    <source src="<?php echo str_contains($slide['url'], 'http') ? $slide['url'] : '../'.$slide['url']; ?>" type="video/mp4">
                                </video>
                                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white" style="opacity: 0.5;"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="slide-preview" style="background-image: url('<?php echo str_contains($slide['url'], 'http') ? $slide['url'] : '../'.$slide['url']; ?>');"></div>
                        <?php endif; ?>
                        <div class="slide-type-badge"><?php echo $slide['type']; ?></div>
                        <div class="slide-info">
                            <div>
                                <div class="muted" style="font-size: 0.8rem;">Order: <?php echo $slide['display_order']; ?></div>
                            </div>
                            <form method="POST" data-action="confirm" data-confirm="'">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                <input type="hidden" name="action" value="delete_slide">
                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
