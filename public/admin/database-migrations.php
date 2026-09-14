<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$page_title = "Database Migrations";
$master_password = 'LeisureLoop2026'; // Configured master password for database alterations

$message = '';
$messageType = '';

// 1. Ensure migrations_log table exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (PDOException $e) {
    die("Failed to initialize migrations table: " . $e->getMessage());
}

// 2. Fetch executed migrations from DB
$executed_migrations = [];
try {
    $stmt = $pdo->query("SELECT migration_name FROM migrations_log ORDER BY executed_at ASC");
    while ($row = $stmt->fetch()) {
        $executed_migrations[] = $row['migration_name'];
    }
} catch (PDOException $e) {
    die("Failed to fetch migrations: " . $e->getMessage());
}

// 3. Scan the migrations directory
$migrations_dir = __DIR__ . '/../../migrations';
$all_files = [];
if (is_dir($migrations_dir)) {
    $files = scandir($migrations_dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $all_files[] = $file;
        }
    }
    sort($all_files); // Execute in alphabetical order
}

$pending_migrations = array_diff($all_files, $executed_migrations);

// 4. Handle Migration Execution Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migrations'])) {
    
    // CSRF verification
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $message = "Invalid CSRF token.";
        $messageType = "error";
    } else {
        $entered_password = $_POST['admin_password'] ?? '';
        
        if ($entered_password !== $master_password) {
            $message = "Incorrect Master Password. Migrations aborted.";
            $messageType = "error";
        } else if (empty($pending_migrations)) {
            $message = "No pending migrations to run.";
            $messageType = "info";
        } else {
            // Run them!
            $success_count = 0;
            $error_occurred = false;
            
            foreach ($pending_migrations as $file) {
                $file_path = $migrations_dir . '/' . $file;
                
                try {
                    $pdo->beginTransaction();
                    
                    // Include the migration file. The file should execute its queries using $pdo.
                    require $file_path;
                    
                    // Log execution
                    $stmt = $pdo->prepare("INSERT INTO migrations_log (migration_name) VALUES (?)");
                    $stmt->execute([$file]);
                    
                    if ($pdo->inTransaction()) {
                        $pdo->commit();
                    }
                    $success_count++;
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $message = "Migration Failed on {$file}: " . $e->getMessage();
                    $messageType = "error";
                    $error_occurred = true;
                    break; // Stop running further migrations if one fails
                }
            }
            
            if (!$error_occurred) {
                $message = "Successfully ran {$success_count} migrations!";
                $messageType = "success";
                
                // Refresh lists
                $pending_migrations = [];
                $stmt = $pdo->query("SELECT migration_name FROM migrations_log ORDER BY executed_at ASC");
                $executed_migrations = [];
                while ($row = $stmt->fetch()) {
                    $executed_migrations[] = $row['migration_name'];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> | Leisure Loop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
    <style>
        .migration-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
        }
        .migration-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        .migration-item:last-child {
            border-bottom: none;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 12px;
            display: inline-block;
        }
        .dot-pending {
            background-color: #f59e0b; /* Amber */
        }
        .dot-executed {
            background-color: #10b981; /* Green */
        }
        .migration-name {
            font-family: monospace;
            font-size: 0.9rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert.error { background-color: #fee2e2; color: #b91c1c; }
        .alert.success { background-color: #d1fae5; color: #047857; }
        .alert.info { background-color: #dbeafe; color: #1d4ed8; }
        
        .security-box {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            max-width: 500px;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem;">
                <h1><?= $page_title ?></h1>
                <p class="muted">Securely apply database schema changes.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                
                <!-- Lists Column -->
                <div>
                    <div class="section-card">
                        <h2>Pending Migrations (<?= count($pending_migrations) ?>)</h2>
                        <?php if (empty($pending_migrations)): ?>
                            <p class="muted" style="margin-top: 1rem;">No pending migrations. Your database is fully up to date!</p>
                        <?php else: ?>
                            <ul class="migration-list" style="margin-top: 1rem;">
                                <?php foreach ($pending_migrations as $m): ?>
                                    <li class="migration-item">
                                        <span class="status-dot dot-pending"></span>
                                        <span class="migration-name"><?= htmlspecialchars($m) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="section-card" style="margin-top: 2rem;">
                        <h2>Executed History (<?= count($executed_migrations) ?>)</h2>
                        <?php if (empty($executed_migrations)): ?>
                            <p class="muted" style="margin-top: 1rem;">No migrations have been executed yet.</p>
                        <?php else: ?>
                            <ul class="migration-list" style="margin-top: 1rem;">
                                <?php foreach (array_reverse($executed_migrations) as $m): ?>
                                    <li class="migration-item">
                                        <span class="status-dot dot-executed"></span>
                                        <span class="migration-name"><?= htmlspecialchars($m) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Execution Form Column -->
                <div>
                    <?php if (!empty($pending_migrations)): ?>
                    <div class="security-box">
                        <h2 style="color: #b91c1c; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Security Check
                        </h2>
                        <p class="muted" style="margin-bottom: 1.5rem; font-size: 0.95rem;">
                            You are about to modify the live database schema. Please enter the Master Admin Password to verify authorization.
                        </p>
                        
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            
                            <div class="form-group">
                                <label for="admin_password">Master Password</label>
                                <input type="password" name="admin_password" id="admin_password" required placeholder="Enter password to confirm...">
                            </div>
                            
                            <button type="submit" name="run_migrations" class="btn-primary" style="width: 100%; background: #b91c1c; border-color: #b91c1c;">
                                Run <?= count($pending_migrations) ?> Pending Migration(s)
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div class="security-box" style="text-align: center; padding: 3rem 2rem;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <h3 style="margin-bottom: 0.5rem;">Database Up To Date</h3>
                        <p class="muted">There are no pending schema changes.</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </main>
    </div>
</body>
</html>
