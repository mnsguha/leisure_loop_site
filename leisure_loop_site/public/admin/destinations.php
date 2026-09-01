<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$destinations = [];
if ($pdo) {
    $destinations = $pdo->query("SELECT * FROM destinations ORDER BY display_order ASC, created_at DESC")->fetchAll();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: destinations.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations | Leisure Loop Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
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
                    <h1>Manage <span class="accent">Destinations</span></h1>
                    <p class="muted">Control editorial content and sightseeing spots.</p>
                </div>
                <a href="destination-form.php" class="btn-primary">+ Add New Destination</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Destination</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinations as $dest): ?>
                    <tr>
                        <td style="width: 80px;">
                            <div style="width: 60px; height: 60px; border-radius: 12px; background: url('<?php echo htmlspecialchars($dest['cover_image'] ?? ''); ?>') center/cover;"></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($dest['name']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($dest['slug']); ?></span>
                        </td>
                        <td style="text-transform: capitalize;"><?php echo htmlspecialchars($dest['category'] ?? 'domestic'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $dest['is_active'] ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo $dest['is_active'] ? 'Active' : 'Hidden'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="destination-form.php?id=<?php echo $dest['id']; ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="destinations.php?delete=<?php echo $dest['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($destinations)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No destinations found. Click "+ Add New Destination" to get started.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
