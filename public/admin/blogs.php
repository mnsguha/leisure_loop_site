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
                                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">
                                        <a href="blog-form.php?id=<?php echo $b['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                        <a href="blogs.php?delete=<?php echo $b['id']; ?>" class="btn-sm btn-delete" data-action="confirm" data-confirm="'">Delete</a>
                                    </div>
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
