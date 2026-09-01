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
                    <h1>Manage <span class="accent">Destinations</span></h1>
                    <p class="muted">Control editorial content and sightseeing spots.</p>
                </div>
                <a href="destination-form.php" class="btn-primary">+ Add New Destination</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Card</th>
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
                            <div style="width: 60px; height: 60px; border-radius: 12px; background: url('<?php 
                                $card_to_show = $dest['card_image'] ?? '';
                                // If the path doesn't start with http or /, we need to prepend ../ to make it relative to admin folder
                                if ($card_to_show && !preg_match('/^http|^\//', $card_to_show)) {
                                    $card_to_show = '../' . $card_to_show;
                                }
                                echo htmlspecialchars($card_to_show); 
                            ?>') center/cover;"></div>
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
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                <a href="destination-form.php?id=<?php echo $dest['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="destinations.php?delete=<?php echo $dest['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
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
