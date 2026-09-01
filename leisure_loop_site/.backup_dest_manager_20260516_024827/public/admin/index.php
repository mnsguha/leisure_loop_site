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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #070c18;
            border-right: 1px solid var(--glass-border);
            padding: 2rem;
        }
        .sidebar-nav {
            list-style: none;
            margin-top: 3rem;
        }
        .sidebar-nav li {
            margin-bottom: 1rem;
        }
        .sidebar-nav a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .sidebar-nav a.active, .sidebar-nav a:hover {
            background: var(--glass);
            color: white;
        }
        .main-content {
            padding: 3rem;
            background: #0f172a;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            border-radius: 20px;
        }
        .stat-card h3 {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: 800;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--glass);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        .data-table th, .data-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        .data-table th {
            background: rgba(255, 255, 255, 0.05);
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
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
