<?php
declare(strict_types=1);

require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if ($pdo) {
    // Database self-healing for CRM & Meta settings
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN crm_api_key VARCHAR(255) DEFAULT NULL");
    } catch (PDOException $e) { /* column exists */ }
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN crm_url VARCHAR(255) DEFAULT 'http://localhost:8000/api/leads/create/'");
    } catch (PDOException $e) { /* column exists */ }
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN meta_verify_token VARCHAR(255) DEFAULT NULL");
    } catch (PDOException $e) { /* column exists */ }
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN meta_access_token TEXT DEFAULT NULL");
    } catch (PDOException $e) { /* column exists */ }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_crm') {
        $crm_api_key = $_POST['crm_api_key'] ?? null;
        $crm_url = $_POST['crm_url'] ?? null;
        $stmt = $pdo->prepare("UPDATE settings SET crm_api_key=?, crm_url=? WHERE id=1");
        $stmt->execute([$crm_api_key, $crm_url]);
        $msg = "CRM settings updated successfully!";
    } elseif ($action === 'save_meta') {
        $meta_verify_token = $_POST['meta_verify_token'] ?? null;
        $meta_access_token = $_POST['meta_access_token'] ?? null;
        $stmt = $pdo->prepare("UPDATE settings SET meta_verify_token=?, meta_access_token=? WHERE id=1");
        $stmt->execute([$meta_verify_token, $meta_access_token]);
        $msg = "Meta settings updated successfully!";
    }
}

$settings = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch() ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Webhook Settings | Admin</title>
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
            <div class="header card-section">
                <h1>CRM <span class="accent">Webhook API</span></h1>
                <p class="muted">Manage API credentials and endpoints for synchronizing leads with your CRM.</p>
            </div>

            <?php if ($msg): ?>
                <div class="msg-success">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="card card-section">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="save_crm">
                <h3>API Configuration</h3>
                <div class="form-grid-single">
                    
                    <div class="form-group">
                        <label>Webhook Endpoint URL</label>
                        <input type="url" name="crm_url" value="<?php echo htmlspecialchars($settings['crm_url'] ?? 'http://localhost:8000/api/leads/create/'); ?>" placeholder="e.g. http://localhost:8000/api/leads/create/" required>
                        <small class="form-help-text">The destination URL where lead data will be POSTed.</small>
                    </div>

                    <div class="form-group">
                        <label>CRM API Key (X-API-KEY)</label>
                        <input type="password" name="crm_api_key" value="<?php echo htmlspecialchars($settings['crm_api_key'] ?? ''); ?>" placeholder="Paste secret key here..." required>
                        <small class="form-help-text">The authentication key required by the CRM endpoint.</small>
                    </div>

                </div>
                
                <button type="submit" class="btn-gold btn-block">Save CRM Settings</button>
            </form>

            <form method="POST" class="card card-section">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="save_meta">
                <h3>Meta (Facebook/Instagram) Configuration</h3>
                <div class="form-grid-single">
                    
                    <div class="form-group">
                        <label>Meta Webhook URL (For Meta App Dashboard)</label>
                        <?php 
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                            $webhook_url = $protocol . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . '/api-meta-webhook.php';
                        ?>
                        <input type="text" value="<?php echo htmlspecialchars($webhook_url); ?>" readonly class="input-readonly">
                        <small class="form-help-text">Paste this URL into your Meta App Webhook configuration.</small>
                    </div>

                    <div class="form-group">
                        <label>Meta Verify Token</label>
                        <input type="text" name="meta_verify_token" value="<?php echo htmlspecialchars($settings['meta_verify_token'] ?? ''); ?>" placeholder="Create a secret string (e.g., my_secret_token_123)">
                        <small class="form-help-text">This must exactly match the verify token you enter in Meta.</small>
                    </div>

                    <div class="form-group">
                        <label>Meta Access Token</label>
                        <input type="password" name="meta_access_token" value="<?php echo htmlspecialchars($settings['meta_access_token'] ?? ''); ?>" placeholder="EAAI...">
                        <small class="form-help-text">System User Access Token used to fetch lead details.</small>
                    </div>

                </div>
                
                <button type="submit" class="btn-gold btn-block">Save Meta Settings</button>
            </form>
        </main>
    </div>
</body>
</html>
