<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$classes = [];
if ($pdo) {
    $classes = $pdo->query("SELECT * FROM cab_classes ORDER BY created_at DESC")->fetchAll();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cab_classes WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: cab_classes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cab Classes | Leisure Loop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1>Manage <span class="accent">Cab Classes</span></h1>
                    <p class="muted">Add and manage vehicle categories.</p>
                </div>
                <a href="cab_class-form.php" class="btn-primary">+ Add New Cab Class</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Class Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $cls): ?>
                    <tr>
                        <td style="width: 80px;">
                            <?php $img_path = preg_match('/^https?:\/\//i', $cls['image']) ? $cls['image'] : '../' . ltrim($cls['image'], '/'); ?>
                            <img src="<?php echo htmlspecialchars($img_path); ?>" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;" alt="Class Image">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($cls['name']); ?></strong><br>
                            <span class="muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars(substr($cls['description'], 0, 50)) . '...'; ?></span>
                        </td>
                        <td>
                            <span class="badge" style="padding: 4px 8px; border-radius: 4px; background: <?php echo $cls['is_active'] ? 'rgba(46, 213, 115, 0.2)' : 'rgba(255, 71, 87, 0.2)'; ?>; color: <?php echo $cls['is_active'] ? '#2ed573' : '#ff4757'; ?>;">
                                <?php echo $cls['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="cab_class-form.php?id=<?php echo $cls['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem;">Edit</a>
                                <a href="?delete=<?php echo $cls['id']; ?>" class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem; background: rgba(255, 255, 255, 0.1); color: #fff;" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No cab classes found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
