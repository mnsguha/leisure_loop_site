<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$themes = [];
$pkg_counts = [];

if ($pdo) {
    // Fetch all categories
    $themes = $pdo->query("SELECT * FROM tour_categories ORDER BY display_order ASC, name ASC")->fetchAll();

    // Fetch active package tour types to compute real-time package counts
    try {
        $all_pkgs = $pdo->query("SELECT tour_type FROM packages WHERE is_active = 1")->fetchAll();
        foreach ($all_pkgs as $pkg) {
            if (!empty($pkg['tour_type'])) {
                $tags = array_map('trim', explode(',', $pkg['tour_type']));
                foreach ($tags as $tag) {
                    $norm = ucwords(strtolower(preg_replace('/\b(tours|tour)\b/i', '', $tag)));
                    $norm = trim($norm);
                    if ($norm !== '') {
                        if (!isset($pkg_counts[$norm])) {
                            $pkg_counts[$norm] = 0;
                        }
                        $pkg_counts[$norm]++;
                    }
                }
            }
        }
    } catch (Exception $e) {}
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($pdo) {
        $stmt = $pdo->prepare("DELETE FROM tour_categories WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: themes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Themes | Leisure Loop Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=3">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1>Travel <span class="accent">Themes</span></h1>
                    <p class="muted">Manage circular category tags, backdrops, SVGs, and card priorities on your website.</p>
                </div>
                <a href="theme-form.php" class="btn-primary">+ Add New Theme</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Backdrop</th>
                        <th>Icon</th>
                        <th>Theme / Category Name</th>
                        <th>Branding Tagline</th>
                        <th style="text-align: center;">Display Order</th>
                        <th style="text-align: center;">Active Tours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($themes as $t): 
                        $count = $pkg_counts[$t['name']] ?? 0;
                    ?>
                    <tr>
                        <td style="width: 90px;">
                            <?php
                                $img_src = !empty($t['image_url']) ? $t['image_url'] : 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
                                if (!preg_match('/^https?:\/\//', $img_src)) {
                                    $img_src = '../' . ltrim($img_src, '/');
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="theme-backdrop-thumb">
                        </td>
                        <td style="width: 60px;">
                            <div class="icon-preview-circle">
                                <?php echo !empty($t['icon_svg']) ? $t['icon_svg'] : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>'; ?>
                            </div>
                        </td>
                        <td>
                            <strong style="color: #fff; font-size: 1.05rem;"><?php echo htmlspecialchars($t['name']); ?></strong>
                        </td>
                        <td>
                            <span style="color: rgba(255, 255, 255, 0.6); font-style: italic; font-size: 0.9rem;"><?php echo htmlspecialchars($t['tagline'] ?? '—'); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-family: monospace; font-size: 1rem; color: var(--gold); font-weight: bold;"><?php echo $t['display_order']; ?></span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-synced" style="background: rgba(197, 160, 89, 0.1); border-color: rgba(197, 160, 89, 0.2); color: var(--gold);">
                                <?php echo $count . ($count === 1 ? ' Tour' : ' Tours'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $t['is_active'] ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo $t['is_active'] ? 'Active' : 'Hidden'; ?>
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                <a href="theme-form.php?id=<?php echo $t['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="themes.php?delete=<?php echo $t['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($themes)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 5rem; color: var(--text-muted);">
                            No curated themes found in database. Click "+ Add New Theme" to start configuring travel categorizations!
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
