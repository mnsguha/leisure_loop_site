<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$departures = [];
if ($pdo) {
    $stmt = $pdo->query("
        SELECT fd.*, p.title as package_title, p.image_url 
        FROM fixed_departures fd 
        JOIN packages p ON fd.package_id = p.id 
        ORDER BY fd.start_date ASC
    ");
    if ($stmt) {
        $departures = $stmt->fetchAll();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM fixed_departures WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: fixed_departures.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Fixed Departures | Leisure Loop Admin</title>
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
                    <h1>Fixed <span class="accent">Departures</span></h1>
                    <p class="muted">Schedule group departures for your packages.</p>
                </div>
                <a href="fixed_departure-form.php" class="btn-primary">+ Add New Departure</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Dates</th>
                        <th>Price</th>
                        <th>Seats (Avail/Total)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departures as $dep): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($dep['package_title']); ?></strong>
                        </td>
                        <td>
                            <?php echo date('d M Y', strtotime($dep['start_date'])); ?> <br>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">to <?php echo date('d M Y', strtotime($dep['end_date'])); ?></span>
                        </td>
                        <td>
                            ₹<?php echo number_format($dep['price']); ?>
                        </td>
                        <td>
                            <strong><?php echo (int)$dep['available_seats']; ?></strong> / <?php echo (int)$dep['total_seats']; ?>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $dep['is_active'] ? 'status-synced' : 'status-failed'; ?>" style="margin-bottom: 5px; display: inline-block;">
                                <?php echo $dep['is_active'] ? 'Active' : 'Draft'; ?>
                            </span><br>
                            <span class="status-badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                                <?php echo htmlspecialchars($dep['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                <a href="fixed_departure-form.php?id=<?php echo $dep['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="fixed_departures.php?delete=<?php echo $dep['id']; ?>" class="btn-sm btn-danger" data-action="confirm" data-confirm="'">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($departures)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No fixed departures scheduled yet. Click "+ Add New Departure" to get started.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
