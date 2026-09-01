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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; }
        .card { background: var(--glass); border: 1px solid var(--glass-border); padding: 2rem; border-radius: 24px; margin-bottom: 2rem; }
        .accred-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .accred-card { background: rgba(255,255,255,0.03); border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); }
        .accred-thumb { height: 100px; background: rgba(255,255,255,0.02); display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .accred-thumb img { max-height: 60px; max-width: 140px; object-fit: contain; filter: grayscale(100%) brightness(0.8); }
        .accred-info { padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
        .form-group input { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 12px; color: white; }
        .btn-delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; }
        .btn-toggle { background: rgba(197, 160, 89, 0.1); color: var(--gold); border: 1px solid rgba(197, 160, 89, 0.25); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; }
        .status-active { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
        .status-inactive { background: rgba(239, 68, 68, 0.15); color: #f87171; }
    </style>
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
                    <input type="hidden" name="action" value="add">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Organization Name</label>
                            <input type="text" name="name" placeholder="e.g. BNI, Ministry of MSME" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image_url" placeholder="https://example.com/leisure.png">
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
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?php echo $acc['id']; ?>">
                                <button type="submit" class="btn-toggle" style="width: 100%; font-size: 0.7rem;">
                                    <?php echo $acc['is_active'] ? 'Hide' : 'Show'; ?>
                                </button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Remove this accreditation?');">
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
