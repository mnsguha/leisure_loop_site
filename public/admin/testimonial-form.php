<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$t = [
    'client_name' => '',
    'tour_name' => '',
    'quote_text' => '',
    'image_url' => '',
    'rotation_angle' => rand(-4, 4),
    'status' => 'active',
    'sort_order' => 0
];

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $t = $stmt->fetch() ?: $t;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $tour_name = trim($_POST['tour_name'] ?? '');
    $quote_text = trim($_POST['quote_text'] ?? '');
    $rotation_angle = (int)($_POST['rotation_angle'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $image_url = $t['image_url'];

    // Handle Image Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../images/testimonials/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $image_url = 'images/testimonials/' . $fileName;
        }
    } elseif (!empty($_POST['image_url_direct'])) {
        $image_url = trim($_POST['image_url_direct']);
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE testimonials SET client_name=?, tour_name=?, quote_text=?, image_url=?, rotation_angle=?, status=?, sort_order=? WHERE id=?");
        $stmt->execute([$client_name, $tour_name, $quote_text, $image_url, $rotation_angle, $status, $sort_order, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, tour_name, quote_text, image_url, rotation_angle, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$client_name, $tour_name, $quote_text, $image_url, $rotation_angle, $status, $sort_order]);
    }

    header('Location: testimonials.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Testimonial | Leisure Loop Admin</title>
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
                    <a href="testimonials.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Testimonials">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Add New'; ?> <span class="accent">Testimonial</span></h1>
                </div>
            </div>

            <div class="form-card">
                <form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Client Name (e.g. Michael & Sarah T.)</label>
                            <input type="text" name="client_name" class="form-control" value="<?php echo htmlspecialchars($t['client_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tour Name (e.g. Bespoke Ladakh Expedition)</label>
                            <input type="text" name="tour_name" class="form-control" value="<?php echo htmlspecialchars($t['tour_name']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>The Quote</label>
                        <textarea name="quote_text" class="form-control" rows="5" required><?php echo htmlspecialchars($t['quote_text']); ?></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Upload Polaroid Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <?php if ($t['image_url']): ?>
                                <div style="margin-top: 1rem;">
                                    <img src="../<?php echo htmlspecialchars($t['image_url']); ?>" alt="Current" style="height: 100px; border-radius: 4px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            
                            <label style="margin-top: 1rem;">Or enter direct image URL (Unsplash, etc.)</label>
                            
<label for="input_d420274d" class="sr-only">https://...</label>
<input id="input_d420274d" type="text" name="image_url_direct" class="form-control" placeholder="https://..." value="<?php echo htmlspecialchars(strpos($t['image_url'], 'http') === 0 ? $t['image_url'] : ''); ?>">
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label>Rotation Angle (for authentic polaroid tilt, e.g., -3 to 3)</label>
                                <input type="number" name="rotation_angle" class="form-control" value="<?php echo (int)$t['rotation_angle']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" <?php echo $t['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $t['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?php echo (int)$t['sort_order']; ?>">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border: none; font-size: 1rem; cursor: pointer; border-radius: 50px;">Save Testimonial</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
