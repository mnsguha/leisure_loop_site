<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

// --- DATABASE INITIALIZATION (Self-Healing) ---
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS accreditations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        image_url VARCHAR(300) NOT NULL,
        display_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $count = $pdo->query("SELECT COUNT(*) FROM accreditations")->fetchColumn();
    if ($count == 0) {
        $items = [
            ['BNI', 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Business_Network_International_logo.svg/200px-Business_Network_International_logo.svg.png', 1, 1],
            ['Ministry of MSME', 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Emblem_of_India.svg/100px-Emblem_of_India.svg.png', 2, 1],
            ['Udaan Hotels', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Flag_of_India.svg/100px-Flag_of_India.svg.png', 3, 1],
        ];
        $stmt = $pdo->prepare("INSERT INTO accreditations (name, image_url, display_order, is_active) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) { $stmt->execute($item); }
    }
}

// --- HANDLE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = trim($_POST['name']);
            $image_url = trim($_POST['image_url']);
            
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image_file']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['image_file']['name']);
                $upload_dir = '../assets/img/accreditations/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $image_url = 'assets/img/accreditations/' . $file_name;
                }
            }
            
            if ($name && $image_url) {
                $stmt = $pdo->prepare("INSERT INTO accreditations (name, image_url, display_order, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $image_url, (int)($_POST['display_order'] ?? 0), isset($_POST['is_active']) ? 1 : 0]);
                $msg = "New accreditation added!";
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM accreditations WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "Accreditation removed.";
        } elseif ($_POST['action'] === 'toggle') {
            $id = (int)$_POST['id'];
            $current = $pdo->prepare("SELECT is_active FROM accreditations WHERE id = ?");
            $current->execute([$id]);
            $row = $current->fetch();
            $newVal = $row ? (1 - $row['is_active']) : 1;
            $stmt = $pdo->prepare("UPDATE accreditations SET is_active = ? WHERE id = ?");
            $stmt->execute([$newVal, $id]);
            $msg = "Status updated.";
        }
    }
}

// --- FETCH ITEMS ---
$accreditations = [];
if ($pdo) {
    $accreditations = $pdo->query("SELECT * FROM accreditations ORDER BY display_order ASC, id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trusted & Accredited | Admin</title>
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
                <h1>Trusted &amp; <span class="accent">Accredited</span></h1>
                <p class="muted">Manage logos displayed in the "Trusted & Accredited By" section on the homepage.</p>
            </div>

            <?php if ($msg): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>Add New Accreditation</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="add">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Organization Name</label>
                            
<label for="input_47de3931" class="sr-only">e.g. BNI, Ministry of MSME</label>
<input id="input_47de3931" type="text" name="name" placeholder="e.g. BNI, Ministry of MSME" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        
<label for="input_c99a5510" class="sr-only">https://example.com/leisure.png</label>
<input id="input_c99a5510" type="text" name="image_url" placeholder="https://example.com/leisure.png">
                    </div>
                    <div class="form-group">
                        <label>OR Upload Logo</label>
                        <input type="file" name="image_file" accept="image/*">
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem;">
                        <input type="checkbox" name="is_active" checked style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Active (show on website)</label>
                    </div>
                    <button type="submit" class="btn-gold" style="width: 100%; padding: 1rem;">Add Accreditation</button>
                </form>
            </div>

            <div class="accred-grid">
                <?php foreach ($accreditations as $acc): ?>
                <div class="accred-card">
                    <div class="accred-thumb">
                        <img src="<?php echo str_contains($acc['image_url'], 'http') ? $acc['image_url'] : '../'.$acc['image_url']; ?>" alt="<?php echo htmlspecialchars($acc['name']); ?>">
                    </div>
                    <div class="accred-info">
                        <div>
                            <div style="font-weight: 500; color: #fff;"><?php echo htmlspecialchars($acc['name']); ?></div>
                            <div class="muted" style="font-size: 0.8rem;">Order: <?php echo $acc['display_order']; ?></div>
                            <span class="status-badge <?php echo $acc['is_active'] ? 'status-active' : 'status-inactive'; ?>" style="font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 50px;">
                                <?php echo $acc['is_active'] ? 'Active' : 'Hidden'; ?>
                            </span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            <form method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?php echo $acc['id']; ?>">
                                <button type="submit" class="btn-toggle" style="width: 100%; font-size: 0.7rem;">
                                    <?php echo $acc['is_active'] ? 'Hide' : 'Show'; ?>
                                </button>
                            </form>
                            <form method="POST" data-action="confirm" data-confirm="'">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $acc['id']; ?>">
                                <button type="submit" class="btn-delete" style="width: 100%; font-size: 0.7rem;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($accreditations)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                    No accreditations added yet. Use the form above to add your first one.
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
