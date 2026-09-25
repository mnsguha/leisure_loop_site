<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = [
    'cab_class_id' => '',
    'name' => '',
    'pax_capacity' => 4,
    'luggage_capacity' => 2,
    'ac_type' => 'AC',
    'image' => '',
    'fuel_type' => 'Petrol',
    'cancellation_policy' => 'Free before 6 hours from the journey time',
    'part_payment' => 'Pay 25% now and rest to driver',
    'is_active' => 1
];

// Fetch cab classes for the dropdown
$cab_classes = $pdo ? $pdo->query("SELECT id, name FROM cab_classes ORDER BY name")->fetchAll() : [];

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch() ?: $vehicle;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cab_class_id = (int)$_POST['cab_class_id'];
    $name = $_POST['name'] ?? '';
    $pax_capacity = (int)($_POST['pax_capacity'] ?? 4);
    $luggage_capacity = (int)($_POST['luggage_capacity'] ?? 2);
    $ac_type = $_POST['ac_type'] ?? 'AC';
    $image = $_POST['image'] ?? '';
    $fuel_type = $_POST['fuel_type'] ?? 'Petrol';
    $cancellation_policy = $_POST['cancellation_policy'] ?? '';
    $part_payment = $_POST['part_payment'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . $file_name)) {
            $image = '/uploads/' . $file_name;
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE vehicles SET cab_class_id=?, name=?, pax_capacity=?, luggage_capacity=?, ac_type=?, image=?, fuel_type=?, cancellation_policy=?, part_payment=?, is_active=? WHERE id=?");
        $stmt->execute([$cab_class_id, $name, $pax_capacity, $luggage_capacity, $ac_type, $image, $fuel_type, $cancellation_policy, $part_payment, $is_active, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vehicles (cab_class_id, name, pax_capacity, luggage_capacity, ac_type, image, fuel_type, cancellation_policy, part_payment, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cab_class_id, $name, $pax_capacity, $luggage_capacity, $ac_type, $image, $fuel_type, $cancellation_policy, $part_payment, $is_active]);
    }
    
    header('Location: vehicles.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Vehicle | Leisure Loop Admin</title>
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
                    <a href="vehicles.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Vehicles">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Add New'; ?> <span class="accent">Vehicle</span></h1>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width: 800px; background: rgba(255,255,255,0.02); padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="form-grid">
                    
                    <div class="form-group full-width">
                        <label>Cab Class</label>
                        <select name="cab_class_id" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
                            <option value="">Select Cab Class</option>
                            <?php foreach ($cab_classes as $cc): ?>
                                <option value="<?php echo $cc['id']; ?>" <?php echo $vehicle['cab_class_id'] == $cc['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cc['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Vehicle Name (e.g. Maruti Swift)</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($vehicle['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Passenger Capacity</label>
                        <input type="number" name="pax_capacity" value="<?php echo htmlspecialchars($vehicle['pax_capacity']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Luggage Capacity (Bags)</label>
                        <input type="number" name="luggage_capacity" value="<?php echo htmlspecialchars($vehicle['luggage_capacity']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>AC Type</label>
                        <select name="ac_type" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
                            <option value="AC" <?php echo $vehicle['ac_type'] == 'AC' ? 'selected' : ''; ?>>AC</option>
                            <option value="Non-AC" <?php echo $vehicle['ac_type'] == 'Non-AC' ? 'selected' : ''; ?>>Non-AC</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Fuel Type</label>
                        <input type="text" name="fuel_type" value="<?php echo htmlspecialchars($vehicle['fuel_type']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Cancellation Policy</label>
                        <input type="text" name="cancellation_policy" value="<?php echo htmlspecialchars($vehicle['cancellation_policy']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Part Payment Terms</label>
                        <input type="text" name="part_payment" value="<?php echo htmlspecialchars($vehicle['part_payment']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Image URL (or Upload below)</label>
                        <input type="text" name="image" value="<?php echo htmlspecialchars($vehicle['image']); ?>" placeholder="https://...">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Upload Image File</label>
                        <input type="file" name="image_file" accept="image/*" style="background: rgba(0,0,0,0.2); padding: 10px;">
                        <?php if ($vehicle['image']): ?>
                            <div style="margin-top: 10px;">
                                <?php $img_path = preg_match('/^https?:\/\//i', $vehicle['image']) ? $vehicle['image'] : '../' . ltrim($vehicle['image'], '/'); ?>
                                <img src="<?php echo htmlspecialchars($img_path); ?>" style="max-height: 100px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group full-width" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <input type="checkbox" name="is_active" value="1" <?php echo $vehicle['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        <label style="margin: 0;">Active (Show on website)</label>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="submit" class="btn-primary">Save Vehicle</button>
                    <a href="vehicles.php" class="btn-primary" style="background: rgba(255,255,255,0.1); color: #fff;">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
