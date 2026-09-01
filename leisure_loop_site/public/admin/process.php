<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$msg = '';

if ($pdo) {
    try {
        // Auto-create table if not exists (Self-Healing)
        $pdo->exec("CREATE TABLE IF NOT EXISTS process_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            step_number INT UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed with default rows if empty
        $count = $pdo->query("SELECT COUNT(*) FROM process_steps")->fetchColumn();
        if ($count == 0) {
            $stmt = $pdo->prepare("INSERT INTO process_steps (step_number, title, description, image_url) VALUES (?, ?, ?, ?)");
            
            $stmt->execute([
                1, 
                'The Vision', 
                'Share your destination, dates, and deepest travel desires. Our concierge listens intently to every nuance, ensuring no detail is overlooked.', 
                'https://images.unsplash.com/photo-1572375992501-4b0892d50c69?q=80&w=800'
            ]);
            
            $stmt->execute([
                2, 
                'The Curation', 
                'Within 24 hours, our artisans craft a personalized itinerary. We handpick sanctuaries, orchestrate exclusive experiences, and ensure seamless transfers.', 
                'https://images.unsplash.com/photo-1499678329028-101435549a4e?q=80&w=800'
            ]);
            
            $stmt->execute([
                3, 
                'The Experience', 
                'From departure to return, every moment is flawlessly orchestrated. You simply arrive and immerse yourself entirely in the journey.', 
                'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=800'
            ]);
        }
    } catch (PDOException $e) {
        $msg = "Database Auto-Setup failed: " . $e->getMessage();
    }
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $step_num = intval($_POST['step_number'] ?? 0);
    
    if ($step_num >= 1 && $step_num <= 3) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        // Handle Image Upload
        $file_key = 'step_image_' . $step_num;
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$file_key]['tmp_name'];
            $file_name = time() . '_step_' . $step_num . '_' . basename($_FILES[$file_key]['name']);
            $upload_dir = '../assets/img/';
            $dest_path = $upload_dir . $file_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $dest_path)) {
                $image_url = 'assets/img/' . $file_name;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE process_steps SET title = ?, description = ?, image_url = ? WHERE step_number = ?");
            $stmt->execute([$title, $description, $image_url, $step_num]);
            $msg = "Step {$step_num} updated successfully.";
        } catch (Exception $e) {
            $msg = "Error updating Step {$step_num}: " . $e->getMessage();
        }
    }
}

// Fetch current settings
$steps = [];
if ($pdo) {
    try {
        $res = $pdo->query("SELECT * FROM process_steps ORDER BY step_number ASC")->fetchAll();
        foreach ($res as $row) {
            $steps[$row['step_number']] = $row;
        }
    } catch (Exception $e) {}
}

// Ensure defaults if missing in array
for ($i = 1; $i <= 3; $i++) {
    if (!isset($steps[$i])) {
        $steps[$i] = [
            'step_number' => $i,
            'title' => 'Step ' . $i,
            'description' => 'Description for step ' . $i,
            'image_url' => ''
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How We Work | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; max-width: 100vw; overflow: hidden; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; overflow-x: auto; overflow-y: auto; display: flex; flex-direction: column; }
        .grid-container { display: flex; gap: 2rem; padding-bottom: 2.5rem; width: max-content; }
        .form-card { flex: 0 0 420px; background: var(--glass); border: 1px solid var(--glass-border); padding: 2.5rem; border-radius: 24px; display: flex; flex-direction: column; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: white;
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.5;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--gold); background: rgba(255, 255, 255, 0.08); }
        .msg { padding: 1rem; background: rgba(197, 160, 89, 0.1); border: 1px solid var(--accent); color: var(--accent); border-radius: 8px; margin-bottom: 2rem; max-width: 1400px; font-family: 'Inter', sans-serif; }
        .img-preview {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-top: 1rem;
            border: 1px solid var(--glass-border);
        }
        .section-subtitle {
            color: var(--gold);
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(197,160,89,0.15);
            padding-bottom: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h1 style="margin-bottom: 2rem; font-family: 'Playfair Display', serif;">Manage <span class="accent">How We Work</span></h1>

            <?php if ($msg): ?>
                <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <div class="grid-container">
                
                <?php foreach([1, 2, 3] as $step_num): 
                    $step_data = $steps[$step_num];
                ?>
                <form method="POST" class="form-card" enctype="multipart/form-data">
                    <input type="hidden" name="step_number" value="<?php echo $step_num; ?>">
                    <h2 class="section-subtitle">Step 0<?php echo $step_num; ?></h2>
                    
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($step_data['title']); ?>" required placeholder="e.g. The Vision">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required placeholder="Enter description text..."><?php echo htmlspecialchars($step_data['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Current Image URL</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($step_data['image_url']); ?>" placeholder="Image URL">
                        <?php if (!empty($step_data['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars(strpos($step_data['image_url'], 'http') === 0 ? $step_data['image_url'] : '../' . $step_data['image_url']); ?>" class="img-preview" alt="Step <?php echo $step_num; ?> Preview">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Upload New Image <span style="color:#64748b;font-weight:400;font-size:0.8rem;">(Overrides direct URL path)</span></label>
                        <input type="file" name="step_image_<?php echo $step_num; ?>" accept="image/*">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 1rem 2rem; margin-top: auto; border-radius: 12px; font-weight: 600;">Save Step 0<?php echo $step_num; ?></button>
                </form>
                <?php endforeach; ?>

            </div>
        </main>
    </div>
</body>
</html>
