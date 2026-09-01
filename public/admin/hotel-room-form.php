<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
if (!$hotel_id) {
    header('Location: hotels.php');
    exit;
}

// Fetch Hotel Details to display in header
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch();
if (!$hotel) {
    header('Location: hotels.php');
    exit;
}

// Handle Add Room
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type_name = trim($_POST['room_type_name']);
    $room_image = trim($_POST['room_image']);
    $amenities = trim($_POST['amenities']);
    $room_size = trim($_POST['room_size']);
    $max_guests = (int)$_POST['max_guests'];
    $bed_type = trim($_POST['bed_type']);
    $smoking_policy = trim($_POST['smoking_policy']);
    $view_type = trim($_POST['view_type']);
    $total_rooms = isset($_POST['total_rooms']) ? (int)$_POST['total_rooms'] : 1;

    $additional_images = [];
    
    // Process URLs
    if (!empty($_POST['additional_images_urls'])) {
        $urls = array_filter(array_map('trim', explode("\n", $_POST['additional_images_urls'])));
        foreach ($urls as $url) {
            if (!empty($url)) {
                $additional_images[] = $url;
            }
        }
    }
    
    // Process File Uploads
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
                    // store relative path suitable for frontend
                    $additional_images[] = '../assets/images/rooms/' . $filename;
                }
            }
        }
    }
    
    $additional_images_json = json_encode($additional_images);

    $stmt = $pdo->prepare("INSERT INTO hotel_rooms (hotel_id, room_type_name, room_image, amenities, room_size, max_guests, bed_type, smoking_policy, view_type, additional_images, total_rooms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$hotel_id, $room_type_name, $room_image, $amenities, $room_size, $max_guests, $bed_type, $smoking_policy, $view_type, $additional_images_json, $total_rooms]);
    
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room Category | Leisure Loop Admin</title>
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
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Rooms">←</a>
                    <h1 style="margin: 0;">Add <span class="accent">Room Category</span></h1>
                </div>
            </div>

            <div class="admin-card">
                <h3 style="margin-top:0;">Room Category Details for <?php echo htmlspecialchars($hotel['name']); ?></h3>
                <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    
                    <div class="form-group">
                        <label>Room Category Name</label>
                        
<label for="input_608bbeb4" class="sr-only">e.g. Premium Room with Balcony</label>
<input id="input_608bbeb4" type="text" name="room_type_name" class="form-control" placeholder="e.g. Premium Room with Balcony" required>
                    </div>
                    <div class="form-group">
                        <label>Main Room Image URL</label>
                        
<label for="input_5e7f4157" class="sr-only">https://...</label>
<input id="input_5e7f4157" type="text" name="room_image" class="form-control" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label>Room Size (e.g. 270 FT²)</label>
                        
<label for="input_6d8b8134" class="sr-only">270 FT²</label>
<input id="input_6d8b8134" type="text" name="room_size" class="form-control" placeholder="270 FT²">
                    </div>
                    <div class="form-group">
                        <label>Max Total Guests</label>
                        
<label for="input_2c9bdcd7" class="sr-only">4</label>
<input id="input_2c9bdcd7" type="number" name="max_guests" class="form-control" placeholder="4">
                    </div>
                    
                    <div class="form-group">
                        <label>Bed Type</label>
                        
<label for="input_b3e0ca24" class="sr-only">e.g. King, Twin</label>
<input id="input_b3e0ca24" type="text" name="bed_type" class="form-control" placeholder="e.g. King, Twin">
                    </div>
                    <div class="form-group">
                        <label>Smoking Policy</label>
                        <select name="smoking_policy" class="form-control">
                            <option value="Non-Smoking">Non-Smoking</option>
                            <option value="Smoking Allowed">Smoking Allowed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>View Type</label>
                        
<label for="input_adfcf79c" class="sr-only">e.g. Mountain View, City View</label>
<input id="input_adfcf79c" type="text" name="view_type" class="form-control" placeholder="e.g. Mountain View, City View">
                    </div>
                    <div class="form-group">
                        <label>Total Rooms (Base Inventory) *</label>
                        <input type="number" name="total_rooms" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Amenities (Comma separated)</label>
                        
<label for="input_9de9b5d1" class="sr-only">WiFi, King Bed, Mountain View, Breakfast</label>
<input id="input_9de9b5d1" type="text" name="amenities" class="form-control" placeholder="WiFi, King Bed, Mountain View, Breakfast">
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Images (URLs, one per line)</label>
                        
<label for="input_83fce3c3" class="sr-only">https://...&#10;https://...</label>
<textarea id="input_83fce3c3" name="additional_images_urls" class="form-control" rows="4" placeholder="https://...&#10;https://..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Images (Upload Files)</label>
                        <input type="file" name="additional_images_files[]" class="form-control" multiple accept="image/*">
                        <small style="color: rgba(255,255,255,0.5); display: block; margin-top: 5px;">You can select multiple images to upload.</small>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <button type="submit" class="btn-primary">Save Room Category</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
