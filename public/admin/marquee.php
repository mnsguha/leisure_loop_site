<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

// --- DATABASE INITIALIZATION (Runs once) ---
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS marquee_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_url VARCHAR(255) NOT NULL,
        label VARCHAR(100) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $count = $pdo->query("SELECT COUNT(*) FROM marquee_items")->fetchColumn();
    if ($count == 0) {
        $items = [
            ['https://images.unsplash.com/photo-1581430873933-05b81a8ca93b?q=80&w=600', 'Bespoke Journeys', 1],
            ['https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=600', 'Luxury Reimagined', 2],
            ['https://images.unsplash.com/photo-1597233539235-56af97458197?q=80&w=600', 'Elite Concierge', 3],
            ['https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=600', 'Exotic Escapes', 4],
            ['https://images.unsplash.com/photo-1589982840456-a2281881b28b?q=80&w=600', 'Unforgettable Memories', 5]
        ];
        $stmt = $pdo->prepare("INSERT INTO marquee_items (image_url, label, display_order) VALUES (?, ?, ?)");
        foreach ($items as $item) { $stmt->execute($item); }
    }
}

// --- HANDLE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $label = $_POST['label'];
            $image_url = $_POST['image_url'];
            
            // Handle Upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image_file']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['image_file']['name']);
                $upload_dir = '../assets/img/marquee/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $image_url = 'assets/img/marquee/' . $file_name;
                }
            }
            
            if ($image_url) {
                $stmt = $pdo->prepare("INSERT INTO marquee_items (image_url, label, display_order) VALUES (?, ?, ?)");
                $stmt->execute([$image_url, $label, (int)($_POST['display_order'] ?? 0)]);
                $msg = "New frame added to the roll!";
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM marquee_items WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "Frame removed from the roll.";
        }
    }
}

// --- FETCH ITEMS ---
$marquee_items = [];
if ($pdo) {
    $marquee_items = $pdo->query("SELECT * FROM marquee_items ORDER BY display_order ASC, id DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marquee Management | Admin</title>
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
                <h1>Film Strip <span class="accent">Roll</span></h1>
                <p class="muted">Manage the cinematic running tags between sections.</p>
            </div>

            <?php if ($msg): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>Add New Frame</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="add">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Brand Pillar / Label</label>
                            
<label for="input_27e0bb15" class="sr-only">e.g. Bespoke Journeys</label>
<input id="input_27e0bb15" type="text" name="label" placeholder="e.g. Bespoke Journeys" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        
<label for="input_4797142b" class="sr-only">https://unsplash.com/...</label>
<input id="input_4797142b" type="text" name="image_url" placeholder="https://unsplash.com/...">
                    </div>
                    <div class="form-group">
                        <label>OR Upload Image</label>
                        <input type="file" name="image_file" accept="image/*">
                    </div>
                    <button type="submit" class="btn-gold" style="width: 100%; padding: 1rem;">Add to Roll</button>
                </form>
            </div>

            <div class="marquee-grid">
                <?php foreach ($marquee_items as $item): ?>
                <div class="marquee-card">
                    <div class="marquee-thumb" style="background-image: url('<?php echo str_contains($item['image_url'], 'http') ? $item['image_url'] : '../'.$item['image_url']; ?>');"></div>
                    <div class="marquee-info">
                        <div>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($item['label']); ?></div>
                            <div class="muted" style="font-size: 0.8rem;">Order: <?php echo $item['display_order']; ?></div>
                        </div>
                        <form method="POST" data-action="confirm" data-confirm="'">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>
