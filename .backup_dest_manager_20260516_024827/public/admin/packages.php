<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$packages = [];
if ($pdo) {
    $packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: packages.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages | Leisure Loop Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Reusing layout styles from index.php */
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .sidebar-nav { list-style: none; margin-top: 3rem; }
        .sidebar-nav a { text-decoration: none; color: var(--text-muted); padding: 0.75rem 1rem; border-radius: 12px; display: block; transition: 0.3s; }
        .sidebar-nav a.active, .sidebar-nav a:hover { background: var(--glass); color: white; }
        .main-content { padding: 3rem; background: #0f172a; }
        .data-table { width: 100%; border-collapse: collapse; background: var(--glass); border-radius: 16px; overflow: hidden; border: 1px solid var(--glass-border); }
        .data-table th, .data-table td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid var(--glass-border); }
        .data-table th { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); font-size: 0.85rem; }
        .btn-sm { padding: 0.4rem 1rem; font-size: 0.8rem; border-radius: 8px; }
        .btn-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); text-decoration: none; }
        .btn-edit { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); text-decoration: none; margin-right: 0.5rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1>Travel <span class="accent">Packages</span></h1>
                    <p class="muted">Create and manage your cinematic itineraries.</p>
                </div>
                <a href="package-form.php" class="btn-primary">+ Add New Package</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Package Title</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $pkg): ?>
                    <tr>
                        <td style="width: 80px;">
                            <div style="width: 60px; height: 60px; border-radius: 12px; background: url('<?php echo htmlspecialchars($pkg['image_url']); ?>') center/cover;"></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($pkg['title']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($pkg['slug']); ?></span>
                        </td>
                        <td>₹<?php echo number_format($pkg['price']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $pkg['is_active'] ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo $pkg['is_active'] ? 'Active' : 'Draft'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="package-form.php?id=<?php echo $pkg['id']; ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="packages.php?delete=<?php echo $pkg['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($packages)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No packages yet. Click "+ Add New Package" to get started.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
