<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if ($pdo) {
    try {
        // Auto-create table if not exists (Self-Healing)
        $pdo->exec("CREATE TABLE IF NOT EXISTS blogs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            excerpt TEXT,
            content LONGTEXT,
            image_url VARCHAR(255),
            author VARCHAR(100) DEFAULT 'Leisure Loop',
            is_published TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {
        $msg = "Database Auto-Setup failed: " . $e->getMessage();
    }
}

// Handle Delete
if (isset($_GET['delete']) && $pdo) {
    $id = intval($_GET['delete']);
    try {
        $pdo->prepare("DELETE FROM blogs WHERE id = ?")->execute([$id]);
        header("Location: blogs.php");
        exit;
    } catch (PDOException $e) {
        $msg = "Error deleting blog: " . $e->getMessage();
    }
}

// Fetch all blogs
$blogs = [];
if ($pdo) {
    try {
        $blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
    } catch (PDOException $e) {
        $msg = "Error fetching blogs: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal & Insights | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .data-table { width: 100%; border-collapse: collapse; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 12px; overflow: hidden; }
        .data-table th, .data-table td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid var(--glass-border); color: #fff; }
        .data-table th { background: rgba(0,0,0,0.2); font-weight: 500; color: var(--gold); }
        .data-table tbody tr:hover { background: rgba(255,255,255,0.02); }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 6px; text-decoration: none; display: inline-block; cursor: pointer; border: none; }
        .btn-edit { background: rgba(197, 160, 89, 0.1); color: var(--gold); border: 1px solid var(--gold); }
        .btn-delete { background: rgba(220, 38, 38, 0.1); color: #ef4444; border: 1px solid #ef4444; margin-left: 0.5rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .status-pub { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-draft { background: rgba(100, 116, 139, 0.1); color: #94a3b8; border: 1px solid rgba(100, 116, 139, 0.2); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="header-actions">
                <h1 style="font-family: 'Playfair Display', serif;">Manage <span class="accent">Journal</span></h1>
                <a href="blog-form.php" class="btn-primary" style="padding: 0.8rem 1.5rem; border-radius: 8px; font-weight: 500;">+ New Article</a>
            </div>

            <?php if ($msg): ?>
                <div style="padding: 1rem; background: rgba(220, 38, 38, 0.1); border: 1px solid #ef4444; color: #ef4444; border-radius: 8px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($blogs)): ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No articles found. Create one to get started!</td></tr>
                    <?php else: ?>
                        <?php foreach ($blogs as $b): ?>
                            <tr>
                                <td style="color: var(--text-muted);"><?php echo date('M j, Y', strtotime($b['created_at'])); ?></td>
                                <td style="font-weight: 500;"><?php echo htmlspecialchars($b['title']); ?></td>
                                <td>
                                    <?php if ($b['is_published']): ?>
                                        <span class="status-badge status-pub">Published</span>
                                    <?php else: ?>
                                        <span class="status-badge status-draft">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="blog-form.php?id=<?php echo $b['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="blogs.php?delete=<?php echo $b['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this article forever?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
