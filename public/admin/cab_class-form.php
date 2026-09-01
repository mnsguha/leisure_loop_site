<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$class = [
    'name' => '',
    'description' => '',
    'image' => '',
    'starting_price' => '',
    'is_active' => 1
];

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM cab_classes WHERE id = ?");
    $stmt->execute([$id]);
    $class = $stmt->fetch() ?: $class;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_POST['image'] ?? '';
    $starting_price = $_POST['starting_price'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // File upload override for image
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $file_name)) {
            $image = '/uploads/' . $file_name;
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE cab_classes SET name=?, description=?, image=?, starting_price=?, is_active=? WHERE id=?");
        $stmt->execute([$name, $description, $image, $starting_price, $is_active, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cab_classes (name, description, image, starting_price, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $image, $starting_price, $is_active]);
    }
    
    header('Location: cab_classes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Cab Class | Leisure Loop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
    <link rel="stylesheet" href="../css/admin-overrides.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="cab_classes.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Cab Classes">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Add New'; ?> <span class="accent">Cab Class</span></h1>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width: 800px; background: rgba(255,255,255,0.02); padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Class Name (e.g. Sedan Class)</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($class['name']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Description (Short marketing text)</label>
                        <textarea name="description" rows="3"><?php echo htmlspecialchars($class['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Starting Price (₹ per day)</label>
                        <input type="number" step="0.01" name="starting_price" value="<?php echo htmlspecialchars($class['starting_price']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Image URL (or Upload below)</label>
                        <input type="text" name="image" value="<?php echo htmlspecialchars($class['image']); ?>" placeholder="https://...">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Upload Image File</label>
                        <input type="file" name="image_file" accept="image/*" style="background: rgba(0,0,0,0.2); padding: 10px;">
                        <?php if ($class['image']): ?>
                            <div style="margin-top: 10px;">
                                <?php $img_path = preg_match('/^https?:\/\//i', $class['image']) ? $class['image'] : '../' . ltrim($class['image'], '/'); ?>
                                <img src="<?php echo htmlspecialchars($img_path); ?>" style="max-height: 100px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group full-width" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <input type="checkbox" name="is_active" value="1" <?php echo $class['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        <label style="margin: 0;">Active (Show on website)</label>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="submit" class="btn-primary">Save Cab Class</button>
                    <a href="cab_classes.php" class="btn-primary" style="background: rgba(255,255,255,0.1); color: #fff;">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
