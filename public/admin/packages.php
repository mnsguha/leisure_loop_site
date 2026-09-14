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
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header admin-page-header-flex">
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
                        <td class="col-thumb">
                            <?php $img_path = preg_match('/^https?:\/\//i', $pkg['image_url']) ? $pkg['image_url'] : '../' . ltrim($pkg['image_url'], '/'); ?>
                            <div class="table-thumb" style="background-image: url('<?php echo htmlspecialchars($img_path); ?>');"></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($pkg['title']); ?></strong><br>
                            <span class="text-muted-sm"><?php echo htmlspecialchars($pkg['slug']); ?></span><br>
                            <span class="text-accent-xs">Code: <?php echo htmlspecialchars($pkg['tour_code'] ?? 'N/A'); ?></span>
                        </td>
                        <td>
                            ₹<?php echo number_format($pkg['price']); ?>
                            <?php if (!empty($pkg['original_price'])): ?>
                            <br><span class="price-crossed">₹<?php echo number_format($pkg['original_price']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $pkg['is_active'] ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo $pkg['is_active'] ? 'Active' : 'Draft'; ?>
                            </span>
                            <?php if (!empty($pkg['is_international'])): ?>
                                <span class="status-badge badge-block status-international">International</span>
                            <?php endif; ?>
                            <?php if (!empty($pkg['is_trending'])): ?>
                                <span class="status-badge badge-block badge-trending">🔥 Trending Tour</span>
                            <?php endif; ?>
                            <?php if (($pkg['package_type'] ?? '') === 'fixed'): ?>
                                <span class="status-badge badge-block badge-fixed">Fixed Departure</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons-col">
                                <a href="package-form.php?id=<?php echo $pkg['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="package-gallery.php?id=<?php echo $pkg['id']; ?>" class="btn-sm btn-gallery">📸 Gallery</a>
                                <?php if (!empty($pkg['is_trending'])): ?>
                                    <a href="packages.php?toggle_trending=<?php echo $pkg['id']; ?>" class="btn-sm btn-trending-on" title="Click to remove from Top Trending">🔥 Trending (On)</a>
                                <?php else: ?>
                                    <a href="packages.php?toggle_trending=<?php echo $pkg['id']; ?>" class="btn-sm btn-trending-off" title="Click to showcase in Top Trending">✨ Make Trending</a>
                                <?php endif; ?>
                                <a href="packages.php?delete=<?php echo $pkg['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($packages)): ?>
                    <tr>
                        <td colspan="5" class="table-empty">
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
