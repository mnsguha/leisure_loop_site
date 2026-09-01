<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;

if (!$room_id || !$hotel_id) {
    header('Location: hotels.php');
    exit;
}

// Fetch Hotel Details
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();
if (!$hotel) {
    header('Location: hotels.php');
    exit;
}

// Fetch Room Details
$stmt = $pdo->prepare("SELECT * FROM hotel_rooms WHERE id = ? AND hotel_id = ?");
$stmt->execute([$room_id, $hotel_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$room) {
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}

// Handle Update Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit_room') {
    $room_type_name = trim($_POST['room_type_name']);
    $room_image = trim($_POST['room_image']);
    $amenities = trim($_POST['amenities']);
    $room_size = trim($_POST['room_size']);
    $max_guests = (int)$_POST['max_guests'];
    $bed_type = trim($_POST['bed_type']);
    $smoking_policy = trim($_POST['smoking_policy']);
    $view_type = trim($_POST['view_type']);
    $total_rooms = isset($_POST['total_rooms']) ? (int)$_POST['total_rooms'] : 1;

    $additional_images = $room['additional_images'] ? json_decode($room['additional_images'], true) : [];
    if (!is_array($additional_images)) {
        $additional_images = [];
    }
    
    // Check if we should clear existing images (if requested, though not in the simple form yet)
    if (isset($_POST['clear_images']) && $_POST['clear_images'] == '1') {
        $additional_images = [];
    }

    // Process new URLs
    if (!empty($_POST['additional_images_urls'])) {
        $urls = array_filter(array_map('trim', explode("\n", $_POST['additional_images_urls'])));
        foreach ($urls as $url) {
            if (!empty($url)) {
                $additional_images[] = $url;
            }
        }
    }
    
    // Process new File Uploads
    if (isset($_FILES['additional_images_files']) && !empty($_FILES['additional_images_files']['name'][0])) {
        $upload_dir = '../../assets/images/rooms/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        foreach ($_FILES['additional_images_files']['name'] as $key => $name) {
            if ($_FILES['additional_images_files']['error'][$key] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['additional_images_files']['tmp_name'][$key];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $filename = uniqid('room_') . '.' . $ext;
                $destination = $upload_dir . $filename;
                if (move_uploaded_file($tmp_name, $destination)) {
                    $additional_images[] = '../assets/images/rooms/' . $filename;
                }
            }
        }
    }
    
    $additional_images_json = json_encode($additional_images);

    $stmt = $pdo->prepare("UPDATE hotel_rooms SET room_type_name = ?, room_image = ?, amenities = ?, room_size = ?, max_guests = ?, bed_type = ?, smoking_policy = ?, view_type = ?, additional_images = ?, total_rooms = ? WHERE id = ? AND hotel_id = ?");
    $stmt->execute([$room_type_name, $room_image, $amenities, $room_size, $max_guests, $bed_type, $smoking_policy, $view_type, $additional_images_json, $total_rooms, $room_id, $hotel_id]);
    
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room | Leisure Loop Admin</title>
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
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1>Edit Room: <span class="accent"><?php echo htmlspecialchars($room['room_type_name']); ?></span></h1>
                    <p class="muted">Hotel: <?php echo htmlspecialchars($hotel['name']); ?></p>
                </div>
                <div>
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-outline">← Back to Rooms</a>
                </div>
            </div>

            <div class="admin-card">
                <form method="POST" action="hotel-room-edit.php?id=<?php echo $room_id; ?>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
&hotel_id=<?php echo $hotel_id; ?>" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <input type="hidden" name="action" value="edit_room">
                    
                    <div class="form-group">
                        <label>Room Category Name</label>
                        <input type="text" name="room_type_name" class="form-control" value="<?php echo htmlspecialchars($room['room_type_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Main Room Image URL</label>
                        <input type="text" name="room_image" class="form-control" value="<?php echo htmlspecialchars($room['room_image']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Room Size (e.g. 270 FT²)</label>
                        <input type="text" name="room_size" class="form-control" value="<?php echo htmlspecialchars($room['room_size']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Max Total Guests</label>
                        <input type="number" name="max_guests" class="form-control" value="<?php echo htmlspecialchars($room['max_guests']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Bed Type</label>
                        <input type="text" name="bed_type" class="form-control" value="<?php echo htmlspecialchars($room['bed_type']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Smoking Policy</label>
                        <select name="smoking_policy" class="form-control">
                            <option value="Non-Smoking" <?php echo $room['smoking_policy'] == 'Non-Smoking' ? 'selected' : ''; ?>>Non-Smoking</option>
                            <option value="Smoking Allowed" <?php echo $room['smoking_policy'] == 'Smoking Allowed' ? 'selected' : ''; ?>>Smoking Allowed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>View Type</label>
                        <input type="text" name="view_type" class="form-control" value="<?php echo htmlspecialchars($room['view_type']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Total Rooms (Base Inventory) *</label>
                        <input type="number" name="total_rooms" class="form-control" value="<?php echo (int)$room['total_rooms']; ?>" min="1" required>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Amenities (Comma separated)</label>
                        <input type="text" name="amenities" class="form-control" value="<?php echo htmlspecialchars($room['amenities']); ?>">
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Current Additional Images:</label>
                        <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <?php 
                            $images = $room['additional_images'] ? json_decode($room['additional_images'], true) : [];
                            if (is_array($images) && count($images) > 0) {
                                echo count($images) . " images attached.";
                            } else {
                                echo "No additional images.";
                            }
                            ?>
                        </div>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="clear_images" value="1">
                            Delete all current additional images
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>Add More Images (URLs, one per line)</label>
                        
<label for="input_50e79b8a" class="sr-only">https://...</label>
<textarea id="input_50e79b8a" name="additional_images_urls" class="form-control" rows="4" placeholder="https://..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Add More Images (Upload Files)</label>
                        <input type="file" name="additional_images_files[]" class="form-control" multiple accept="image/*">
                        <small style="color: rgba(255,255,255,0.5); display: block; margin-top: 5px;">You can select multiple images to upload. They will be added to the existing images.</small>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2; text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-gold" style="padding: 12px 30px; font-size: 1.1rem;">Save Changes</button>
                    </div>
                </form>
            </div>

        </main>
    </div>
</body>
</html>
