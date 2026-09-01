<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_url TEXT NOT NULL,
        alt_text TEXT,
        is_active TINYINT DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_image'])) {
    $altText = filter_input(INPUT_POST, 'alt_text', FILTER_SANITIZE_STRING) ?: '';
    
    $uploadDir = '../assets/img/gallery/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['gallery_image']['name']);
    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
    $targetFilePath = $uploadDir . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');

    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $targetFilePath)) {
            $imageUrl = 'assets/img/gallery/' . $fileName;
            
            $stmt = $pdo->prepare("INSERT INTO gallery_images (image_url, alt_text) VALUES (?, ?)");
            if ($stmt->execute([$imageUrl, $altText])) {
                $msg = "<div class='alert success'>Image uploaded successfully.</div>";
            } else {
                $msg = "<div class='alert error'>Database insertion failed.</div>";
            }
        } else {
            $msg = "<div class='alert error'>Sorry, there was an error uploading your file.</div>";
        }
    } else {
        $msg = "<div class='alert error'>Sorry, only JPG, JPEG, PNG, GIF, & WEBP files are allowed.</div>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_url FROM gallery_images WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    if ($img) {
        $filePath = '../' . $img['image_url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $pdo->prepare("DELETE FROM gallery_images WHERE id = ?")->execute([$id]);
        $msg = "<div class='alert success'>Image deleted successfully.</div>";
    }
}

// Fetch all images
$images = $pdo->query("SELECT * FROM gallery_images ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Gallery | Leisure Loop Admin</title>
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
                <h1>Guest <span class="accent">Gallery</span></h1>
                <p class="muted">Upload and manage 'Real Smiles' images from guests.</p>
            </div>

            <?php echo $msg; ?>

            <div class="upload-card">
                <h3 style="margin-bottom: 1.5rem; color: white;">Upload New Photo</h3>
                <form action="gallery.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="form-group">
                        <label>Select Image (JPG, PNG, WEBP)</label>
                        <input type="file" name="gallery_image" class="form-control" required accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Caption / Alt Text (Optional)</label>
                        
<label for="input_0e01e607" class="sr-only">E.g., Guests enjoying the view at Tsomgo Lake</label>
<input id="input_0e01e607" type="text" name="alt_text" class="form-control" placeholder="E.g., Guests enjoying the view at Tsomgo Lake">
                    </div>
                    <button type="submit" class="btn-submit">Upload Photo</button>
                </form>
            </div>

            <div class="gallery-grid">
                <?php foreach ($images as $img): ?>
                <div class="gallery-item">
                    <img src="../<?php echo htmlspecialchars($img['image_url']); ?>" alt="Gallery Image" class="gallery-img">
                    <div class="gallery-actions">
                        <span style="color: var(--text-muted); font-size: 0.8rem;"><?php echo date('M d, Y', strtotime($img['created_at'])); ?></span>
                        <a href="gallery.php?delete=<?php echo $img['id']; ?>" class="btn-delete" data-action="confirm" data-confirm="'">Delete</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($images)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted); background: var(--glass); border-radius: 12px;">
                        No gallery images uploaded yet.
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
