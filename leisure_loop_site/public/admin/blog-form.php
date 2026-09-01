<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = '';
$b = [
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'image_url' => '',
    'author' => 'Leisure Loop',
    'is_published' => 1
];

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $b = $res;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    $author = trim($_POST['author']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // File Upload handling for Cover Image
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['cover_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['cover_image']['name']);
        $upload_dir = '../assets/img/blog/';
        $dest_path = $upload_dir . $file_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file_tmp, $dest_path)) {
            $image_url = 'assets/img/blog/' . $file_name;
        }
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE blogs SET title=?, slug=?, excerpt=?, content=?, image_url=?, author=?, is_published=? WHERE id=?");
            $stmt->execute([$title, $slug, $excerpt, $content, $image_url, $author, $is_published, $id]);
            $msg = "Article updated successfully.";
            $b = array_merge($b, $_POST);
            $b['image_url'] = $image_url;
        } else {
            $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, excerpt, content, image_url, author, is_published) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $excerpt, $content, $image_url, $author, $is_published]);
            $new_id = $pdo->lastInsertId();
            header("Location: blog-form.php?id=$new_id&success=1");
            exit;
        }
    } catch (PDOException $e) {
        $msg = "Error saving article: " . $e->getMessage();
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $msg = "Article created successfully.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Include tinyMCE for rich text editing (open-source cdnjs to avoid cloud key warnings) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'lists link image table code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
            skin: 'oxide-dark',
            content_css: 'dark',
            height: 500,
            menubar: false
        });
    </script>
    <style>
        .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #070c18; border-right: 1px solid var(--glass-border); padding: 2rem; }
        .main-content { padding: 3rem; background: #0f172a; overflow-y: auto; }
        .form-container { max-width: 900px; background: var(--glass); border: 1px solid var(--glass-border); padding: 2.5rem; border-radius: 20px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }
        .form-group input[type="text"], .form-group textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input[type="text"]:focus, .form-group textarea:focus { border-color: var(--gold); outline: none; }
        .form-group textarea { resize: vertical; }
        .checkbox-label { display: flex; align-items: center; gap: 0.5rem; color: white; cursor: pointer; }
        .img-preview { width: 100%; max-height: 300px; object-fit: cover; border-radius: 12px; margin-top: 1rem; border: 1px solid var(--glass-border); }
        .split-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; max-width: 900px; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header-actions">
                <h1 style="font-family: 'Playfair Display', serif;"><?php echo $id ? 'Edit Article' : 'New Article'; ?></h1>
                <a href="blogs.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Journal</a>
            </div>

            <?php if ($msg): ?>
                <div style="padding: 1rem; background: rgba(197, 160, 89, 0.1); border: 1px solid var(--gold); color: var(--gold); border-radius: 8px; margin-bottom: 2rem; max-width: 900px;">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-container">
                <div class="form-group">
                    <label>Article Title *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($b['title']); ?>" required>
                </div>

                <div class="split-grid">
                    <div class="form-group">
                        <label>URL Slug (leave empty to auto-generate)</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($b['slug']); ?>" placeholder="e.g. my-first-article">
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" value="<?php echo htmlspecialchars($b['author']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Cover Image URL (or upload below)</label>
                    <input type="text" name="image_url" value="<?php echo htmlspecialchars($b['image_url']); ?>" placeholder="https://...">
                    <?php if (!empty($b['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars(strpos($b['image_url'], 'http') === 0 ? $b['image_url'] : '../' . $b['image_url']); ?>" class="img-preview" alt="Cover Preview">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Upload Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" style="color: white;">
                </div>

                <div class="form-group">
                    <label>Short Excerpt (appears on the catalog card)</label>
                    <textarea name="excerpt" rows="3" required><?php echo htmlspecialchars($b['excerpt']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Full Content</label>
                    <textarea name="content" id="content"><?php echo htmlspecialchars($b['content']); ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 2rem;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" value="1" <?php echo $b['is_published'] ? 'checked' : ''; ?> style="width: 18px; height: 18px;">
                        <span>Publish this article</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="padding: 1rem 3rem; border-radius: 8px; font-weight: 600; margin-top: 1rem; border: none; cursor: pointer; font-size: 1rem;">
                    <?php echo $id ? 'Save Changes' : 'Create Article'; ?>
                </button>
            </form>
        </main>
    </div>
</body>
</html>
