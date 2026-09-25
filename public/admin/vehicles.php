<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$vehicles = [];
if ($pdo) {
    // Fetch vehicles with their associated cab class name
    $vehicles = $pdo->query("
        SELECT v.*, c.name as class_name 
        FROM vehicles v 
        LEFT JOIN cab_classes c ON v.cab_class_id = c.id 
        ORDER BY v.created_at DESC
    ")->fetchAll();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: vehicles.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vehicles | Leisure Loop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1>Manage <span class="accent">Vehicles</span></h1>
                    <p class="muted">Add and manage specific cars under each Cab Class.</p>
                </div>
                <a href="vehicle-form.php" class="btn-primary">+ Add New Vehicle</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Vehicle Name</th>
                        <th>Cab Class</th>
                        <th>Capacity / AC</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td style="width: 80px;">
                            <?php $img_path = preg_match('/^https?:\/\//i', $v['image']) ? $v['image'] : '../' . ltrim($v['image'], '/'); ?>
                            <img src="<?php echo htmlspecialchars($img_path); ?>" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;" alt="Vehicle Image">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($v['name']); ?></strong>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(255,255,255,0.1); color: #fff; border-radius: 4px; padding: 4px 8px;">
                                <?php echo htmlspecialchars($v['class_name'] ?: 'Unknown Class'); ?>
                            </span>
                        </td>
                        <td class="muted" style="font-size: 0.9rem;">
                            <?php echo (int)$v['pax_capacity']; ?> Pax, <?php echo (int)$v['luggage_capacity']; ?> Bags<br>
                            <?php echo htmlspecialchars($v['ac_type']); ?>
                        </td>
                        <td>
                            <span class="badge" style="padding: 4px 8px; border-radius: 4px; background: <?php echo $v['is_active'] ? 'rgba(46, 213, 115, 0.2)' : 'rgba(255, 71, 87, 0.2)'; ?>; color: <?php echo $v['is_active'] ? '#2ed573' : '#ff4757'; ?>;">
                                <?php echo $v['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="vehicle-form.php?id=<?php echo $v['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem;">Edit</a>
                                <a href="?delete=<?php echo $v['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem; background: rgba(255, 255, 255, 0.1); color: #fff;" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">No vehicles found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
