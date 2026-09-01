<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$subscribers = [];
if ($pdo) {
    try {
        $subscribers = $pdo->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC")->fetchAll();
    } catch (PDOException $e) {
        // Table might not exist yet if no one has subscribed
        $subscribers = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers | Leisure Loop Admin</title>
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
            <div class="header" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Newsletter <span class="accent">Subscribers</span></h1>
                    <p class="muted">Manage your Inner Circle mailing list.</p>
                </div>
                <?php 
                    $emailList = array_map(function($s) { return $s['email']; }, $subscribers);
                    $emailJson = htmlspecialchars(json_encode($emailList), ENT_QUOTES, 'UTF-8');
                ?>
                <a href="javascript:void(0);" data-action="copy-emails" data-emails="<?php echo $emailJson; ?>" class="export-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 17.929H6c-1.105 0-2-.895-2-2V3.929c0-1.105.895-2 2-2h9c1.105 0 2 .895 2 2v2"></path><path d="M10 22.929h9c1.105 0 2-.895 2-2v-12c0-1.105-.895-2-2-2h-9c-1.105 0-2 .895-2 2v12c0 1.105.895 2 2 2z"></path></svg>
                    Copy All Emails
                </a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60%;">Email Address</th>
                        <th>Subscribed Date</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $sub): ?>
                    <tr>
                        <td style="font-size: 1.1rem; color: #fff;">
                            <strong><?php echo htmlspecialchars($sub['email']); ?></strong>
                        </td>
                        <td style="color: var(--text-muted);">
                            <?php echo date('F j, Y', strtotime($sub['subscribed_at'])); ?><br>
                            <span style="font-size: 0.85rem;"><?php echo date('g:i A', strtotime($sub['subscribed_at'])); ?></span>
                        </td>
                        <td style="text-align: right;">
                            <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Active</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($subscribers)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                            No subscribers yet. They will appear here when they join the Inner Circle.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
