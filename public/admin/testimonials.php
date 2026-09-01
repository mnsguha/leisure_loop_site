<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$testimonials = [];
if ($pdo) {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC")->fetchAll();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: testimonials.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials | Leisure Loop Admin</title>
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
                    <h1>Manage <span class="accent">Testimonials</span></h1>
                    <p class="muted">Manage the Traveler Diaries scrolling marquee.</p>
                </div>
                <a href="testimonial-form.php" class="btn-primary">+ Add New Testimonial</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Client</th>
                        <th>Quote Preview</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $t): ?>
                    <tr>
                        <td style="width: 80px;">
                            <div style="width: 60px; height: 60px; border-radius: 4px; background: url('<?php echo htmlspecialchars($t['image_url']); ?>') center/cover;"></div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($t['client_name']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($t['tour_name']); ?></span>
                        </td>
                        <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-style: italic; color: #cbd5e1;">
                            "<?php echo htmlspecialchars($t['quote_text']); ?>"
                        </td>
                        <td>
                            <span class="status-badge <?php echo $t['status'] === 'active' ? 'status-synced' : 'status-failed'; ?>">
                                <?php echo ucfirst($t['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                <a href="testimonial-form.php?id=<?php echo $t['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="testimonials.php?delete=<?php echo $t['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($testimonials)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No testimonials found. Click "+ Add New Testimonial" to get started.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
