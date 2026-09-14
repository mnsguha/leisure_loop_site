<?php
require_once '../../config/db.php';
require_once '../../includes/functions.php';

// Check if user is logged in
requireAdmin();

$package_id = $_GET['id'] ?? null;
if (!$package_id) {
    header('Location: packages.php');
    exit();
}

// Fetch package
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->execute([$package_id]);
$package = $stmt->fetch();
if (!$package) {
    header('Location: packages.php');
    exit();
}

// Handle Image Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image_url FROM package_images WHERE id = ? AND package_id = ?");
    $stmt->execute([$delete_id, $package_id]);
    $img = $stmt->fetch();
    if ($img) {
        $file_path = '../../' . $img['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $pdo->prepare("DELETE FROM package_images WHERE id = ?")->execute([$delete_id]);
    }
    header('Location: package-gallery.php?id=' . $package_id);
    exit();
}

// Handle Image Uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_FILES['images']['name'][0])) {
        $target_dir = "../../assets/images/packages/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $db_path = "assets/images/packages/" . $file_name;
                    $stmt = $pdo->prepare("INSERT INTO package_images (package_id, image_url) VALUES (?, ?)");
                    $stmt->execute([$package_id, $db_path]);
                }
            }
        }
    }
    
    if (!empty($_POST['image_url'])) {
        $img_url = trim($_POST['image_url']);
        $stmt = $pdo->prepare("INSERT INTO package_images (package_id, image_url) VALUES (?, ?)");
        $stmt->execute([$package_id, $img_url]);
    }

    header('Location: package-gallery.php?id=' . $package_id);
    exit();
}

// Fetch existing images
$stmt = $pdo->prepare("SELECT * FROM package_images WHERE package_id = ? ORDER BY created_at DESC");
$stmt->execute([$package_id]);
$images = $stmt->fetchAll();

$page_title = "Manage Gallery - " . htmlspecialchars($package['title']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-overrides.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="header admin-page-header-split">
                <div class="header-title-wrapper">
                    <a href="packages.php" class="back-arrow" title="Back to Packages">←</a>
                    <div>
                        <h1 class="header-title">Manage <span class="accent">Gallery</span></h1>
                        <p class="muted header-subtitle"><?php echo htmlspecialchars($package['title']); ?></p>
                    </div>
                </div>
            </div>

            <div class="upload-panel">
                <form action="" method="POST" enctype="multipart/form-data" class="upload-form-row">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="flex-1">
                        <label class="upload-label">Upload Image Files</label>
                        <input type="file" name="images[]" class="upload-input" multiple accept="image/*">
                    </div>
                    <button type="submit" class="btn-primary btn-upload">Upload Files</button>
                </form>
                
                <div class="upload-divider">— OR —</div>

                <form action="" method="POST" class="upload-form-row">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="flex-1">
                        <label class="upload-label">Add Image URL</label>
                        
<label for="input_de9113bb" class="sr-only">https://...</label>
<input id="input_de9113bb" type="url" name="image_url" placeholder="https://..." class="upload-input">
                    </div>
                    <button type="submit" class="btn-primary btn-upload">Add URL</button>
                </form>
            </div>

            <h2 class="gallery-title">Existing Images (<?php echo count($images); ?>)</h2>
            
            <?php if(empty($images)): ?>
                <div class="empty-state">No images added to the gallery yet.</div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach($images as $img): 
                        $img_src = preg_match('/^https?:\/\//i', $img['image_url']) ? $img['image_url'] : '../../' . ltrim($img['image_url'], '/');
                    ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Package Image">
                            <a href="?package_id=<?php echo $package_id; ?>&delete_id=<?php echo $img['id']; ?>" class="delete-btn" data-action="confirm" data-confirm="Delete this image?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
