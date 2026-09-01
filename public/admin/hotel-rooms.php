<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
if (!$hotel_id) {
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

function updateStartingTariff($pdo, $hotel_id) {
    $pdo->query("UPDATE hotels SET starting_tariff = 0 WHERE id = $hotel_id");
}


// Handle Add Plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_plan') {
    $room_id = (int)$_POST['room_id'];
    $plan_name = trim($_POST['plan_name']);
    $max_adults = (int)$_POST['max_adults'];
    $max_children = (int)$_POST['max_children'];
    $inclusions = trim($_POST['inclusions']);

    $stmt = $pdo->prepare("INSERT INTO hotel_room_plans (room_id, plan_name, max_adults, max_children, inclusions) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$room_id, $plan_name, $max_adults, $max_children, $inclusions]);
    
    updateStartingTariff($pdo, $hotel_id);
    
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}

// Handle Delete Room
if (isset($_GET['delete_room'])) {
    $id = (int)$_GET['delete_room'];
    $stmt = $pdo->prepare("DELETE FROM hotel_rooms WHERE id = ? AND hotel_id = ?");
    $stmt->execute([$id, $hotel_id]);
    
    updateStartingTariff($pdo, $hotel_id);
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}

// Handle Delete Plan
if (isset($_GET['delete_plan'])) {
    $id = (int)$_GET['delete_plan'];
    // ensure plan belongs to this hotel
    $stmt = $pdo->prepare("SELECT r.hotel_id FROM hotel_room_plans p JOIN hotel_rooms r ON p.room_id = r.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res && $res['hotel_id'] == $hotel_id) {
        $stmt = $pdo->prepare("DELETE FROM hotel_room_plans WHERE id = ?");
        $stmt->execute([$id]);
        updateStartingTariff($pdo, $hotel_id);
    }
    header("Location: hotel-rooms.php?hotel_id=$hotel_id");
    exit;
}



// Fetch Rooms
$stmt = $pdo->prepare("SELECT * FROM hotel_rooms WHERE hotel_id = ?");
$stmt->execute([$hotel_id]);
$rooms = $stmt->fetchAll();

// Fetch Plans for each room
$rooms_with_plans = [];
foreach ($rooms as $room) {
    $stmt = $pdo->prepare("SELECT * FROM hotel_room_plans WHERE room_id = ? ORDER BY id ASC");
    $stmt->execute([$room['id']]);
    $plans = $stmt->fetchAll();
    
    $room['plans'] = $plans;
    $rooms_with_plans[] = $room;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms & Plans | Leisure Loop Admin</title>
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
            <div class="header" style="margin-bottom: 3rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <a href="hotels.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Hotels">←</a>
                    <h1 style="margin: 0;">Rooms & Plans for <span class="accent"><?php echo htmlspecialchars($hotel['name']); ?></span></h1>
                </div>
                <div style="display: flex; gap: 15px; padding-left: 35px;">
                    <a href="hotel-room-form.php?hotel_id=<?php echo $hotel_id; ?>" class="btn-primary">+ Add Room Category</a>
                    <button data-action="open-modal" data-target="addPlanModal" class="btn-primary" style="background: transparent; border: 1px solid var(--gold); color: var(--gold);">+ Add Meal Plan</button>
                </div>
            </div>


            <?php foreach ($rooms_with_plans as $room): ?>
            <div class="admin-card" style="margin-bottom: 2rem; border-left: 4px solid var(--gold);">
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; gap: 20px; align-items: flex-start;">
                        <?php if($room['room_image']): ?>
                            <img src="<?php echo htmlspecialchars($room['room_image']); ?>" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; border-radius: 8px; background: rgba(255,255,255,0.1); flex-shrink: 0;"></div>
                        <?php endif; ?>
                        <div style="flex-grow: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 5px;">
                                        <h3 style="margin: 0; color: #fff;"><?php echo htmlspecialchars($room['room_type_name']); ?> <span style="font-size: 0.9rem; color: #a0aec0;">(<?php echo (int)$room['total_rooms']; ?> Rooms)</span></h3>
                                        <div style="display: flex; gap: 10px;">
                                            <a href="hotel-room-edit.php?id=<?php echo $room['id']; ?>&hotel_id=<?php echo $hotel_id; ?>" class="action-link" style="color: var(--gold); padding: 4px 12px; font-size: 0.8rem; border: 1px solid var(--gold); border-radius: 4px; text-decoration: none;">Edit Room</a>
                                            <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>&delete_room=<?php echo $room['id']; ?>" class="action-link" style="color: #e74c3c; padding: 4px 12px; font-size: 0.8rem; border: 1px solid #e74c3c; border-radius: 4px; text-decoration: none;" data-action="confirm" data-confirm="Delete this entire room and all its plans?">Delete Room</a>
                                        </div>
                                    </div>
                                    <p class="muted" style="font-size: 0.9rem; margin: 0; line-height: 1.5;"><?php echo htmlspecialchars($room['amenities']); ?></p>
                                </div>
                                <button data-action="toggle-meal-plans" data-room-id="<?php echo $room['id']; ?>" style="color: #3498db; padding: 8px 16px; border: 1px solid #3498db; border-radius: 4px; background: transparent; cursor: pointer; white-space: nowrap; margin-left: 15px;">Meal Plans ▼</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="mealPlans_<?php echo $room['id']; ?>" style="display: none; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--gold);">Meal Plans</h4>
                    
                    <table class="data-table" style="margin-bottom: 20px; font-size: 0.9rem;">
                        <thead>
                            <tr>
                                <th>Meal Plan Name</th>
                                <th>Max Adults</th>
                                <th>Max Children</th>
                                <th>Inclusions</th>
                                <th style="width: 200px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($room['plans'] as $plan): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($plan['plan_name']); ?></strong></td>
                                <td style="color: rgba(255,255,255,0.6);"><?php echo (int)$plan['max_adults']; ?></td>
                                <td style="color: rgba(255,255,255,0.6);"><?php echo (int)$plan['max_children']; ?></td>
                                <td style="color: rgba(255,255,255,0.6);"><?php echo htmlspecialchars($plan['inclusions']); ?></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>&delete_plan=<?php echo $plan['id']; ?>" style="color: #e74c3c; text-decoration: none; display: inline-block;" data-action="confirm" data-confirm="Delete this plan?">Remove</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($room['plans'])): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: rgba(255,255,255,0.5);">No rate plans added.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    </table>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($rooms_with_plans)): ?>
                <div style="text-align: center; padding: 3rem; background: rgba(255,255,255,0.02); border-radius: 12px; color: rgba(255,255,255,0.5);">
                    No rooms configured yet. Add a room category above.
                </div>
            <?php endif; ?>
        </main>
    </div>

    <div id="addPlanModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:#111; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 600px; max-width: 90%; padding: 25px; position: relative;">
            <b aria-label="Close"utton type="button" data-action="close-modal" data-target="addPlanModal" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; color: rgba(255,255,255,0.6); font-size: 1.2rem; cursor: pointer; line-height: 1;">&times;</button>
            <h4 style="margin-top:0; margin-bottom: 20px; color:var(--gold); font-size: 1.1rem; font-weight: 600;">Add Meal Plan</h4>
            
            <form method="POST" action="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="action" value="add_plan">
                
                <div>
                    <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: rgba(255,255,255,0.6);">Select Room Category</label>
                    <select name="room_id" class="form-control" required style="width: 100%; font-size: 0.9rem; padding: 10px;">
                        <option value="">-- Select Room Category --</option>
                        <?php foreach($rooms_with_plans as $r): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['room_type_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 2;">
                        <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: rgba(255,255,255,0.6);">Meal Plan Name</label>
                        
<label for="input_45c151bb" class="sr-only">e.g. Breakfast Included</label>
<input id="input_45c151bb" type="text" name="plan_name" class="form-control" placeholder="e.g. Breakfast Included" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: rgba(255,255,255,0.6);">Max Adults</label>
                        <input type="number" name="max_adults" class="form-control" value="2">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: rgba(255,255,255,0.6);">Max Children</label>
                        <input type="number" name="max_children" class="form-control" value="1">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; margin-bottom: 5px; color: rgba(255,255,255,0.6);">Inclusions (Comma separated)</label>
                    
<label for="input_ef4c2476" class="sr-only">e.g. Free Breakfast, Welcome Drink</label>
<input id="input_ef4c2476" type="text" name="inclusions" class="form-control" placeholder="e.g. Free Breakfast, Welcome Drink">
                </div>

                <div style="text-align: center; margin-top: 10px;">
                    <button type="submit" class="btn-primary" style="padding: 10px 30px; border-radius: 4px;">Save Plan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
