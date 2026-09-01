<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$leads = [];
if ($pdo) {
    if (tableHasColumn($pdo, 'leads', 'package_id')) {
        $leads = $pdo->query("SELECT l.*, p.title as package_name FROM leads l LEFT JOIN packages p ON l.package_id = p.id ORDER BY l.created_at DESC")->fetchAll();
    } else {
        $leads = $pdo->query("SELECT l.*, NULL as package_name FROM leads l ORDER BY l.created_at DESC")->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries | Leisure Loop Admin</title>
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
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-synced { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .status-contacted { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .status-pending { background: rgba(234, 179, 8, 0.2); color: #facc15; }
        .status-new { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .status-failed { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .status-lost { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .status-converted { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 3rem;">
                <h1>Customer <span class="accent">Inquiries</span></h1>
                <p class="muted">Monitor and manage leads captured from your marketing site.</p>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer Details</th>
                        <th>Interested In</th>
                        <th>CRM Sync</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td style="font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo date('M d, Y', strtotime($lead['created_at'])); ?><br>
                            <?php echo date('H:i', strtotime($lead['created_at'])); ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_name', 'name'])); ?></strong><br>
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_phone', 'phone'])); ?></span><br>
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_email', 'email'])); ?></span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(firstFilledValue($lead, ['package_name', 'destination'], 'General')); ?>
                        </td>
                        <td>
                            <?php $status = firstFilledValue($lead, ['status'], 'new'); ?>
                            <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td>
                            <div style="max-width: 300px; font-size: 0.85rem; color: var(--text-muted); line-height: 1.4;">
                                <?php echo nl2br(htmlspecialchars(firstFilledValue($lead, ['message', 'notes']))); ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No inquiries captured yet.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
