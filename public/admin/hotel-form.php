<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hotel = [
    'name' => '', 'place' => '', 'star_category' => 3, 'type' => 'signature', 'main_image' => '', 'description' => '', 'amenities' => '',
    'google_rating' => '', 'google_review_count' => '', 'google_review_link' => ''
];

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) $hotel = $existing;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $place = trim($_POST['place']);
    $star = (int)$_POST['star_category'];
    $type = $_POST['type'];
    $img = trim($_POST['main_image']);
    $desc = trim($_POST['description']);
    $amenities = trim($_POST['amenities']);
    $google_rating = !empty($_POST['google_rating']) ? trim($_POST['google_rating']) : null;
    $google_review_count = !empty($_POST['google_review_count']) ? trim($_POST['google_review_count']) : null;
    $google_review_link = !empty($_POST['google_review_link']) ? trim($_POST['google_review_link']) : null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE hotels SET name=?, place=?, star_category=?, type=?, main_image=?, description=?, amenities=?, google_rating=?, google_review_count=?, google_review_link=? WHERE id=?");
        $stmt->execute([$name, $place, $star, $type, $img, $desc, $amenities, $google_rating, $google_review_count, $google_review_link, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, place, star_category, type, main_image, description, amenities, google_rating, google_review_count, google_review_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $place, $star, $type, $img, $desc, $amenities, $google_rating, $google_review_count, $google_review_link]);
    }
    header('Location: hotels.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Hotel | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="hotels.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Hotels">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Add New'; ?> <span class="accent">Hotel</span></h1>
                </div>
            </div>

            <div class="admin-card">
                <form method="POST" style="display: grid; gap: 20px;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="form-group">
                        <label>Hotel Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($hotel['name']); ?>">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Place / Destination</label>
                            <input type="text" name="place" class="form-control" required value="<?php echo htmlspecialchars($hotel['place']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Star Category</label>
                            <select name="star_category" class="form-control">
                                <option value="3" <?php echo $hotel['star_category']==3?'selected':''; ?>>3 Star</option>
                                <option value="4" <?php echo $hotel['star_category']==4?'selected':''; ?>>4 Star</option>
                                <option value="5" <?php echo $hotel['star_category']==5?'selected':''; ?>>5 Star</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="signature" <?php echo $hotel['type']=='signature'?'selected':''; ?>>Signature Hotel (Company Owned)</option>
                                <option value="luxury" <?php echo $hotel['type']=='luxury'?'selected':''; ?>>Other Brand (Inquiry Only)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Main Image URL</label>
                        <input type="text" name="main_image" class="form-control" value="<?php echo htmlspecialchars($hotel['main_image']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($hotel['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Amenities (Comma separated, e.g., WiFi, In-House Spa, Elevator)</label>
                        <input type="text" name="amenities" class="form-control" value="<?php echo htmlspecialchars($hotel['amenities'] ?? ''); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 20px;">
                        <div class="form-group">
                            <label>Google Rating (e.g. 4.8)</label>
                            <input type="number" step="0.1" max="5.0" min="0.0" name="google_rating" class="form-control" value="<?php echo htmlspecialchars($hotel['google_rating'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Review Count</label>
                            <input type="number" name="google_review_count" class="form-control" value="<?php echo htmlspecialchars($hotel['google_review_count'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Google Review Link (URL)</label>
                            
<label for="input_4999b608" class="sr-only">https://maps.google.com/...</label>
<input id="input_4999b608" type="url" name="google_review_link" class="form-control" placeholder="https://maps.google.com/..." value="<?php echo htmlspecialchars($hotel['google_review_link'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-primary">Save Hotel</button>
                        <a href="hotels.php" style="color: #fff; margin-left: 15px;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
