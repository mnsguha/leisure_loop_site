<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$hotels = [];
if ($pdo) {
    $hotels = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC")->fetchAll();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: hotels.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hotels | Leisure Loop Admin</title>
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
                    <h1>Manage <span class="accent">Hotels</span></h1>
                    <p class="muted">Add and manage Signature and Luxury properties.</p>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                    <a href="hotel-form.php" class="btn-primary" style="width: 100%; text-align: center;">+ Add New Hotel</a>
                    <button data-action="open-hotel-modal" style="width: 100%; background: rgba(197,160,89,0.1); color: var(--gold); border: 1px solid var(--gold); padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s;"><i class="fas fa-calendar-alt"></i> Rates & Inventory</button>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Hotel Name</th>
                        <th>Place</th>
                        <th>Type</th>
                        <th>Starting Tariff</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hotels as $hotel): ?>
                    <tr>
                        <td style="width: 80px;">
                            <?php $img_path = preg_match('/^https?:\/\//i', $hotel['main_image']) ? $hotel['main_image'] : '../' . ltrim($hotel['main_image'], '/'); ?>
                            <img src="<?php echo htmlspecialchars($img_path); ?>" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;" alt="Hotel Image">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($hotel['name']); ?></strong><br>
                            <span class="muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars($hotel['star_category']); ?> Star</span>
                        </td>
                        <td><?php echo htmlspecialchars($hotel['place']); ?></td>
                        <td>
                            <span class="badge" style="padding: 4px 8px; border-radius: 4px; background: <?php echo $hotel['type'] == 'signature' ? 'rgba(212, 175, 55, 0.2)' : 'rgba(255, 255, 255, 0.1)'; ?>; color: <?php echo $hotel['type'] == 'signature' ? '#C5A059' : '#fff'; ?>;">
                                <?php echo ucfirst(htmlspecialchars($hotel['type'])); ?>
                            </span>
                        </td>
                        <td>₹<?php echo number_format($hotel['starting_tariff'], 2); ?></td>
                        <td style="width: 250px;">
                            <a href="hotel-rooms.php?hotel_id=<?php echo $hotel['id']; ?>" class="action-link" style="color: #3498db;">Rooms</a> | 
                            <a href="hotel-gallery.php?hotel_id=<?php echo $hotel['id']; ?>" class="action-link" style="color: #2ecc71;">Gallery</a> | 
                            <a href="hotel-form.php?id=<?php echo $hotel['id']; ?>" class="action-link" style="color: var(--gold);">Edit</a> | 
                            <a href="hotels.php?delete=<?php echo $hotel['id']; ?>" class="action-link" style="color: #e74c3c;" data-action="confirm" data-confirm="Are you sure you want to delete this hotel?">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($hotels)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: rgba(255,255,255,0.5);">No hotels found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Hotel Select Modal -->
    <div id="hotelSelectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: #0b0f19; width: 500px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); padding: 30px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0; font-size: 1.8rem;">Select <span style="color: var(--gold);">Hotel</span></h2>
                <b aria-label="Close"utton type="button" data-action="close-hotel-modal" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <div style="margin-bottom: 20px; position: relative;">
                
<label for="hotelSearchInput" class="sr-only">Search hotel...</label>
<input type="text" id="hotelSearchInput" placeholder="Search hotel..." style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); color: #fff; font-size: 1rem; margin-bottom: 10px; outline: none;">
                
                <div id="hotelList" style="max-height: 250px; overflow-y: auto; background: rgba(0,0,0,0.3); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                    <?php foreach ($hotels as $h): ?>
                        <div class="hotel-option" data-action="select-hotel" data-hotel-id="<?php echo $h['id']; ?>" style="padding: 12px 15px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff; transition: background 0.2s;">
                            <?php echo htmlspecialchars($h['name']); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($hotels)): ?>
                        <div style="padding: 12px 15px; color: var(--text-muted);">No hotels found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <form id="hotelSelectForm" action="hotel-inventory.php" method="GET">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="hotel_id" id="selectedHotelId" value="">
                <div style="text-align: right; margin-top: 30px;">
                    <button type="button" data-action="close-hotel-modal" style="padding: 12px 24px; border-radius: 6px; cursor: pointer; margin-right: 10px; background: transparent; border: 1px solid var(--text-muted); color: var(--text-muted); font-weight: 600;">Cancel</button>
                    <button type="submit" id="goBtn" style="padding: 12px 24px; border-radius: 6px; cursor: pointer; background: var(--gold); color: #000; border: none; font-weight: 600; transition: 0.3s;" disabled>Open Inventory & Rates</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
