<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$packages = [];
if ($pdo) {
    $packages = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC")->fetchAll();
}

// Handle 1-Click Trending Toggle
if (isset($_GET['toggle_trending'])) {
    $id = (int)$_GET['toggle_trending'];
    $stmt = $pdo->prepare("UPDATE packages SET is_trending = 1 - IFNULL(is_trending, 0) WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: packages.php');
    exit;
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
                            <?php $img_path = preg_match('/^https?:\/\//i', $pkg['image_url']) ? $pkg['image_url'] : '../' . ltrim($pkg['image_url'], '/'); ?>
                            <div style="width: 60px; height: 60px; border-radius: 12px; background: url('<?php echo htmlspecialchars($img_path); ?>') center/cover;"></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($pkg['title']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($pkg['slug']); ?></span><br>
                            <span style="font-size: 0.75rem; color: var(--accent);">Code: <?php echo htmlspecialchars($pkg['tour_code'] ?? 'N/A'); ?></span>
                        </td>
                        <td>
                            ₹<?php echo number_format($pkg['price']); ?>
                            <?php if (!empty($pkg['original_price'])): ?>
                            <br><span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.8rem;">₹<?php echo number_format($pkg['original_price']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $pkg['is_active'] ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo $pkg['is_active'] ? 'Active' : 'Draft'; ?>
                            </span>
                            <?php if (!empty($pkg['is_international'])): ?>
                                <span class="status-badge" style="background: rgba(197, 160, 89, 0.2); color: var(--gold); border: 1px solid rgba(197, 160, 89, 0.3); margin-top: 5px; display: inline-block;">International</span>
                            <?php endif; ?>
                            <?php if (!empty($pkg['is_trending'])): ?>
                                <span class="status-badge" style="background: rgba(249, 115, 22, 0.25); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.45); margin-top: 5px; display: inline-block;">🔥 Trending Tour</span>
                            <?php endif; ?>
                            <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
                                <span class="status-badge" style="background: rgba(11, 124, 74, 0.2); color: #4ade80; border: 1px solid rgba(11, 124, 74, 0.4); margin-top: 5px; display: inline-block;">Fixed Departure</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                <a href="package-form.php?id=<?php echo $pkg['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="package-gallery.php?id=<?php echo $pkg['id']; ?>" class="btn-sm" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.4);">📸 Gallery</a>
                                <?php if (!empty($pkg['is_trending'])): ?>
                                    <a href="packages.php?toggle_trending=<?php echo $pkg['id']; ?>" class="btn-sm" style="background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4);" title="Click to remove from Top Trending">🔥 Trending (On)</a>
                                <?php else: ?>
                                    <a href="packages.php?toggle_trending=<?php echo $pkg['id']; ?>" class="btn-sm" style="background: rgba(255, 255, 255, 0.06); color: var(--text-muted); border: 1px solid rgba(255, 255, 255, 0.15);" title="Click to showcase in Top Trending">✨ Make Trending</a>
                                <?php endif; ?>
                                <a href="packages.php?delete=<?php echo $pkg['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
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
