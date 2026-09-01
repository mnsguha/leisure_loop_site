<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$dest = [
    'name' => '',
    'slug' => '',
    'tagline' => '',
    'category' => 'domestic',
    'cover_image' => '',
    'description_long' => '',
    'display_order' => 0,
    'is_active' => 1,
    'sightseeing' => [],
    'terms_conditions' => ''
];

if ($id > 0 && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $dest = array_merge($dest, $res);
        $dest['sightseeing'] = json_decode($res['sightseeing_json'], true) ?: [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
    $tagline = trim($_POST['tagline'] ?? '');
    $category = trim($_POST['category'] ?? 'domestic');
    $cover_image = trim($_POST['cover_image'] ?? '');
    $description_long = trim($_POST['description_long'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $terms_conditions = trim($_POST['terms_conditions'] ?? '');

    $sightseeing_data = [];
    if (isset($_POST['spot_title']) && is_array($_POST['spot_title'])) {
        foreach ($_POST['spot_title'] as $key => $val) {
            $spotTitle = trim((string) $val);
            $spotDesc = trim((string) ($_POST['spot_desc'][$key] ?? ''));
            $spotImage = trim((string) ($_POST['spot_image'][$key] ?? ''));
            
            if ($spotTitle === '' && $spotDesc === '') continue;

            $sightseeing_data[] = [
                'title' => $spotTitle,
                'desc' => $spotDesc,
                'image' => $spotImage
            ];
        }
    }
    $sightseeing_json = json_encode($sightseeing_data);

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE destinations SET name=?, slug=?, tagline=?, category=?, cover_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, terms_conditions=? WHERE id=?");
        $stmt->execute([$name, $slug, $tagline, $category, $cover_image, $description_long, $display_order, $is_active, $sightseeing_json, $terms_conditions, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO destinations (name, slug, tagline, category, cover_image, description_long, display_order, is_active, sightseeing_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $tagline, $category, $cover_image, $description_long, $display_order, $is_active, $sightseeing_json, $terms_conditions]);
    }

    header('Location: destinations.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Destination | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; }
        .form-card { background: var(--glass); border: 1px solid var(--glass-border); padding: 2.5rem; border-radius: 24px; max-width: 900px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: white;
            outline: none;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--accent); }
        .spot-item { background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); padding: 1.5rem; border-radius: 16px; margin-bottom: 1rem; position: relative; }
        .btn-remove { position: absolute; top: 1rem; right: 1rem; color: #f87171; cursor: pointer; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem;">
                <a href="destinations.php" style="color: var(--accent); text-decoration: none; font-size: 0.9rem;">&larr; Back to Destinations</a>
                <h1 style="margin-top: 1rem;"><?php echo $id ? 'Edit' : 'Create'; ?> <span class="accent">Destination</span></h1>
            </div>

            <form method="POST" class="form-card">
                <div class="form-row">
                    <div class="form-group">
                        <label>Destination Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($dest['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug (URL Identifier)</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($dest['slug']); ?>" placeholder="auto-generated if empty">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tagline (e.g. Himalayan Majesty)</label>
                        <input type="text" name="tagline" value="<?php echo htmlspecialchars($dest['tagline']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="domestic" <?php echo $dest['category'] == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
                            <option value="international" <?php echo $dest['category'] == 'international' ? 'selected' : ''; ?>>International</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cover Image URL</label>
                        <input type="text" name="cover_image" value="<?php echo htmlspecialchars($dest['cover_image']); ?>" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Display Order (Lower = First)</label>
                        <input type="number" name="display_order" value="<?php echo (int)$dest['display_order']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Editorial Description (Long Text)</label>
                    <textarea name="description_long" rows="6"><?php echo htmlspecialchars($dest['description_long']); ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Terms &amp; Conditions <span style="color:#64748b;font-weight:400;font-size:0.8rem;">&#8212; shown on the package detail page. Use ##text## to highlight a word or sentence in gold.</span></label>
                    <textarea name="terms_conditions" rows="8" placeholder="##Terms &amp; Conditions##&#10;&#10;##Payment Policy##&#10;An advance payment of 30% of the total tour cost + 5% GST..."><?php echo htmlspecialchars($dest['terms_conditions'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo $dest['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        Active (published on website)
                    </label>
                </div>

                <div style="margin-top: 3rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Sightseeing Highlights</h3>
                    <div id="spots-container">
                        <?php foreach ($dest['sightseeing'] as $index => $spot): ?>
                        <div class="spot-item">
                            <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
                            <div class="form-group">
                                <label>Spot Title</label>
                                <input type="text" name="spot_title[]" value="<?php echo htmlspecialchars($spot['title'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="text" name="spot_image[]" value="<?php echo htmlspecialchars($spot['image'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="spot_desc[]" rows="2"><?php echo htmlspecialchars($spot['desc'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-spot" class="btn-outline" style="width: 100%;">+ Add Sightseeing Spot</button>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 1.25rem;">Save Destination</button>
            </form>
        </main>
    </div>

    <script>
        document.getElementById('add-spot').addEventListener('click', () => {
            const container = document.getElementById('spots-container');
            const div = document.createElement('div');
            div.className = 'spot-item';
            div.innerHTML = `
                <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
                <div class="form-group">
                    <label>Spot Title</label>
                    <input type="text" name="spot_title[]" required>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="spot_image[]" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="spot_desc[]" rows="2"></textarea>
                </div>
            `;
            container.appendChild(div);
        });
    </script>
</body>
</html>
