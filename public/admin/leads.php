<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['lead_ids'])) {
    if ($pdo) {
        $ids = $_POST['lead_ids'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        header("Location: leads.php");
        exit;
    }
}


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
            <div class="header" style="margin-bottom: 3rem;">
                <h1>Customer <span class="accent">Inquiries</span></h1>
                <p class="muted">Monitor and manage leads captured from your marketing site.</p>
            </div>

            <form method="POST" action="leads.php" id="bulkDeleteForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="action" value="bulk_delete">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1rem;">
                    <div></div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" id="btnCancel" class="p-btn-proceed" style="background: transparent; color: var(--text-muted); border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; font-size: 0.9rem; width: auto; border-radius: 4px; display: none; cursor: pointer;">Cancel</button>
                        <button type="submit" id="btnBulkDelete" class="p-btn-proceed" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 8px 16px; font-size: 0.9rem; width: auto; border-radius: 4px; display: none; cursor: pointer;" data-action="confirm" data-confirm="Are you sure you want to delete selected inquiries?">Delete Selected</button>
                    </div>
                </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAll" style="accent-color: var(--gold); cursor: pointer; width: 16px; height: 16px;">
                        </th>
                        <th>Date</th>
                        <th>Customer Details</th>
                        <th>Destination</th>
                        <th>CRM Sync</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="lead_ids[]" value="<?php echo (int)$lead['id']; ?>" class="lead-checkbox" style="accent-color: var(--gold); cursor: pointer; width: 16px; height: 16px;">
                        </td>
                        <td style="font-size: 0.85rem; color: var(--text-muted);">
                            <?php echo date('M d, Y', strtotime($lead['created_at'])); ?><br>
                            <?php echo date('H:i', strtotime($lead['created_at'])); ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_name', 'name'])); ?></strong><br>
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars(firstFilledValue($lead, ['customer_phone', 'phone'])); ?></span><br>
                            <div style="font-size: 0.85rem; color: var(--gold); margin-top: 4px;">
                                <?php if (!empty($lead['travel_date'])): ?>
                                    <span title="Travel Date">📅 <?php echo htmlspecialchars($lead['travel_date']); ?></span><br>
                                <?php endif; ?>
                                <?php if (isset($lead['adults'])): ?>
                                    <span title="Travelers">👥 <?php echo (int)$lead['adults']; ?>A, <?php echo (int)$lead['children']; ?>C</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(firstFilledValue($lead, ['destination', 'package_name'], 'General')); ?>
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
                        <td colspan="6" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No inquiries captured yet.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </form>
        </main>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
