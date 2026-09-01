<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

// Fetch summary data
$package_count = 0;
$lead_count = 0;
$recent_leads = [];

if ($pdo) {
    $package_count = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
    $lead_count = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    if (tableHasColumn($pdo, 'leads', 'package_id')) {
        $recent_leads = $pdo->query("SELECT l.*, p.title as package_name FROM leads l LEFT JOIN packages p ON l.package_id = p.id ORDER BY l.created_at DESC LIMIT 5")->fetchAll();
    } else {
        $recent_leads = $pdo->query("SELECT l.*, NULL as package_name FROM leads l ORDER BY l.created_at DESC LIMIT 5")->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Leisure Loop</title>
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
            <div class="header" style="margin-bottom: 2rem;">
                <h1>Welcome Back, <span class="accent">Admin</span></h1>
                <p class="muted">Here is what's happening with Leisure Loop Trip today.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Packages</h3>
                    <div class="value"><?php echo $package_count; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Leads</h3>
                    <div class="value"><?php echo $lead_count; ?></div>
                </div>
            </div>

            <div class="section-card">
                <h2 style="margin-bottom: 1.5rem;">Recent Inquiries</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Package</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_leads as $lead): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_name', 'name'])); ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_phone', 'phone'])); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars(firstFilledValue($lead, ['package_name', 'destination'], 'General Inquiry')); ?></td>
                            <td><?php echo date('M d, Y', strtotime($lead['created_at'])); ?></td>
                            <td>
                                <?php $status = firstFilledValue($lead, ['status'], 'new'); ?>
                                <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_leads)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted);">No inquiries yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
