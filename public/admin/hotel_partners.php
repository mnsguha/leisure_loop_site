<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

// --- HANDLE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = trim($_POST['name']);
            $logo_url = trim($_POST['logo_url']);
            
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['logo_file']['tmp_name'];
                $file_name = time() . '_' . basename($_FILES['logo_file']['name']);
                $upload_dir = '../assets/img/partners/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $logo_url = $file_name; // Just store filename, or relative path if you prefer. I'll store filename to match setup_db.
                }
            }
            
            if ($name && $logo_url) {
                $stmt = $pdo->prepare("INSERT INTO hotel_partners (name, logo_url, display_order, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $logo_url, (int)($_POST['display_order'] ?? 0), isset($_POST['is_active']) ? 1 : 0]);
                $msg = "New hotel partner added!";
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM hotel_partners WHERE id = ?");
            $stmt->execute([$id]);
            $msg = "Hotel partner removed.";
        } elseif ($_POST['action'] === 'toggle') {
            $id = (int)$_POST['id'];
            $current = $pdo->prepare("SELECT is_active FROM hotel_partners WHERE id = ?");
            $current->execute([$id]);
            $row = $current->fetch();
            if ($row) {
                $newVal = (1 - $row['is_active']);
                $stmt = $pdo->prepare("UPDATE hotel_partners SET is_active = ? WHERE id = ?");
                $stmt->execute([$newVal, $id]);
                $msg = "Status updated.";
            }
        }
    }
}

// --- FETCH ITEMS ---
$partners = [];
if ($pdo) {
    $partners = $pdo->query("SELECT * FROM hotel_partners ORDER BY display_order ASC, id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Partners | Admin</title>
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
                <h1>Hotel <span class="accent">Partners</span></h1>
                <p class="muted">Manage logos displayed in the "Our Hotel Partners" section on the homepage.</p>
            </div>

            <?php if ($msg): ?>
                <div style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>Add New Partner</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="hidden" name="action" value="add">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Hotel Name</label>
                            
<label for="input_4260e511" class="sr-only">e.g. Taj Hotels, Lemon Tree</label>
<input id="input_4260e511" type="text" name="name" placeholder="e.g. Taj Hotels, Lemon Tree" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Logo URL</label>
                        
<label for="input_8d0a28d7" class="sr-only">https://example.com/leisure.png</label>
<input id="input_8d0a28d7" type="text" name="logo_url" placeholder="https://example.com/leisure.png">
                    </div>
                    <div class="form-group">
                        <label>OR Upload Logo</label>
                        <input type="file" name="logo_file" accept="image/*">
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.6rem;">
                        <input type="checkbox" name="is_active" checked style="width: auto; cursor: pointer; transform: scale(1.15);">
                        <label style="margin: 0; cursor: pointer; color: white; font-size: 1rem;">Active (show on website)</label>
                    </div>
                    <button type="submit" class="btn-gold" style="width: 100%; padding: 1rem;">Add Partner</button>
                </form>
            </div>

            <div class="accred-grid">
                <?php foreach ($partners as $acc): ?>
                <div class="accred-card">
                    <div class="accred-thumb">
                        <?php 
                            $img_src = $acc['logo_url'];
                            if (!str_starts_with($img_src, 'http')) {
                                if (str_contains($img_src, '/')) {
                                    $img_src = '../' . $img_src;
                                } else {
                                    $img_src = '../assets/img/partners/' . $img_src;
                                }
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($acc['name']); ?>">
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
                <?php if (empty($partners)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                    No hotel partners added yet. Use the form above to add your first one.
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
