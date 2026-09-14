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

// Calculate Date Range (7 days view)
$start_date_str = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$start_time = strtotime($start_date_str);
$dates = [];
for ($i = 0; $i < 7; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days", $start_time));
}
$prev_start = date('Y-m-d', strtotime('-7 days', $start_time));
$next_start = date('Y-m-d', strtotime('+7 days', $start_time));

// Fetch Rooms for this hotel
$stmt = $pdo->prepare("SELECT id, room_type_name, total_rooms FROM hotel_rooms WHERE hotel_id = ? ORDER BY id ASC");
$stmt->execute([$hotel_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Plans for these rooms
$room_ids = array_column($rooms, 'id');
$plans_by_room = [];
$plan_ids = [];
if (!empty($room_ids)) {
    $placeholders = implode(',', array_fill(0, count($room_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, room_id, plan_name FROM hotel_room_plans WHERE room_id IN ($placeholders) ORDER BY id ASC");
    $stmt->execute($room_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $plan) {
        $plans_by_room[$plan['room_id']][] = $plan;
        $plan_ids[] = $plan['id'];
    }
}

// Handle Bulk Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Generate dates based on from_date, to_date, and days
    $update_dates = [];
    if (in_array($_POST['action'], ['bulk_update_inventory', 'bulk_update_rates'])) {
        $from = strtotime($_POST['from_date']);
        $to = strtotime($_POST['to_date']);
        $selected_days = isset($_POST['days']) ? $_POST['days'] : [];
        
        if ($from && $to && $from <= $to && !empty($selected_days)) {
            for ($time = $from; $time <= $to; $time = strtotime('+1 day', $time)) {
                if (in_array(date('w', $time), $selected_days)) {
                    $update_dates[] = date('Y-m-d', $time);
                }
            }
        }
    }

    if ($_POST['action'] == 'bulk_update_inventory' && !empty($update_dates)) {
        if (isset($_POST['bulk_inv']) && is_array($_POST['bulk_inv'])) {
            foreach ($_POST['bulk_inv'] as $room_id => $qty) {
                if ($qty !== '') { // Leave empty to skip
                    $qty = (int)$qty;
                    foreach ($update_dates as $date) {
                        $stmt = $pdo->prepare("INSERT INTO room_inventory (room_id, inventory_date, available_rooms) 
                                               VALUES (?, ?, ?) 
                                               ON DUPLICATE KEY UPDATE available_rooms = VALUES(available_rooms)");
                        $stmt->execute([$room_id, $date, $qty]);
                    }
                }
            }
        }
        header("Location: hotel-inventory.php?hotel_id=$hotel_id&start_date=$start_date_str&saved=1");
        exit;
    }

    if ($_POST['action'] == 'bulk_update_rates' && !empty($update_dates)) {
        if (isset($_POST['bulk_rates']) && is_array($_POST['bulk_rates'])) {
            foreach ($_POST['bulk_rates'] as $plan_id => $rates) {
                // Check if at least one rate is provided
                if ($rates['base_rate_2_pax'] !== '' || $rates['base_rate_1_pax'] !== '' || $rates['extra_adult_rate'] !== '' || $rates['cnb_rate'] !== '') {
                    foreach ($update_dates as $date) {
                        // First, get existing rate for this date if we are only updating some fields
                        $stmt = $pdo->prepare("SELECT * FROM hotel_room_rates WHERE plan_id = ? AND rate_date = ?");
                        $stmt->execute([$plan_id, $date]);
                        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $b2 = $rates['base_rate_2_pax'] !== '' ? (float)$rates['base_rate_2_pax'] : ($existing ? $existing['base_rate_2_pax'] : 0);
                        $b1 = $rates['base_rate_1_pax'] !== '' ? (float)$rates['base_rate_1_pax'] : ($existing ? $existing['base_rate_1_pax'] : 0);
                        $ea = $rates['extra_adult_rate'] !== '' ? (float)$rates['extra_adult_rate'] : ($existing ? $existing['extra_adult_rate'] : 0);
                        $cnb = $rates['cnb_rate'] !== '' ? (float)$rates['cnb_rate'] : ($existing ? $existing['cnb_rate'] : 0);

                        $stmt = $pdo->prepare("INSERT INTO hotel_room_rates (plan_id, rate_date, base_rate_2_pax, base_rate_1_pax, extra_adult_rate, cnb_rate) 
                                               VALUES (?, ?, ?, ?, ?, ?) 
                                               ON DUPLICATE KEY UPDATE base_rate_2_pax = VALUES(base_rate_2_pax), base_rate_1_pax = VALUES(base_rate_1_pax), extra_adult_rate = VALUES(extra_adult_rate), cnb_rate = VALUES(cnb_rate)");
                        $stmt->execute([$plan_id, $date, $b2, $b1, $ea, $cnb]);
                    }
                }
            }
        }
        header("Location: hotel-inventory.php?hotel_id=$hotel_id&start_date=$start_date_str&saved=1");
        exit;
    }
}

// Handle Save Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_inventory') {
    // Save Inventory
    if (isset($_POST['inventory']) && is_array($_POST['inventory'])) {
        foreach ($_POST['inventory'] as $room_id => $date_values) {
            foreach ($date_values as $date => $qty) {
                $qty = (int)$qty;
                $stmt = $pdo->prepare("INSERT INTO room_inventory (room_id, inventory_date, available_rooms) 
                                       VALUES (?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE available_rooms = VALUES(available_rooms)");
                $stmt->execute([$room_id, $date, $qty]);
            }
        }
    }
    
    // Save Rates
    if (isset($_POST['rates']) && is_array($_POST['rates'])) {
        foreach ($_POST['rates'] as $plan_id => $date_values) {
            foreach ($date_values as $date => $rate_data) {
                $b2 = (float)$rate_data['base_rate_2_pax'];
                $b1 = (float)$rate_data['base_rate_1_pax'];
                $ea = (float)$rate_data['extra_adult_rate'];
                $cnb = (float)$rate_data['cnb_rate'];
                
                $stmt = $pdo->prepare("INSERT INTO hotel_room_rates (plan_id, rate_date, base_rate_2_pax, base_rate_1_pax, extra_adult_rate, cnb_rate) 
                                       VALUES (?, ?, ?, ?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE base_rate_2_pax = VALUES(base_rate_2_pax), base_rate_1_pax = VALUES(base_rate_1_pax), extra_adult_rate = VALUES(extra_adult_rate), cnb_rate = VALUES(cnb_rate)");
                $stmt->execute([$plan_id, $date, $b2, $b1, $ea, $cnb]);
            }
        }
    }
    
    // Refresh to show saved data
    header("Location: hotel-inventory.php?hotel_id=$hotel_id&start_date=$start_date_str&saved=1");
    exit;
}

// Fetch Overrides for the current view
$inventory_data = [];
$rate_data = [];
if (!empty($room_ids)) {
    $placeholders = implode(',', array_fill(0, count($room_ids), '?'));
    $sql = "SELECT room_id, inventory_date, available_rooms 
            FROM room_inventory 
            WHERE room_id IN ($placeholders) 
            AND inventory_date >= ? AND inventory_date <= ?";
    $params = $room_ids;
    $params[] = $dates[0];
    $params[] = $dates[6];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ov) {
        $inventory_data[$ov['room_id']][$ov['inventory_date']] = $ov['available_rooms'];
    }
}

if (!empty($plan_ids)) {
    $placeholders = implode(',', array_fill(0, count($plan_ids), '?'));
    $sql = "SELECT plan_id, rate_date, base_rate_2_pax, base_rate_1_pax, extra_adult_rate, cnb_rate 
            FROM hotel_room_rates 
            WHERE plan_id IN ($placeholders) 
            AND rate_date >= ? AND rate_date <= ?";
    $params = $plan_ids;
    $params[] = $dates[0];
    $params[] = $dates[6];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rt) {
        $rate_data[$rt['plan_id']][$rt['rate_date']] = $rt;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Inventory - <?php echo htmlspecialchars($hotel['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
    <link rel="stylesheet" href="../css/admin-overrides.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo" style="margin-bottom: 40px; text-align: center;">
                <img src="../assets/images/logo.png" alt="Leisure Loop" style="max-width: 150px;">
            </div>
            <nav style="display: flex; flex-direction: column; gap: 10px;">
                <a href="dashboard.php" style="color: var(--text-muted); text-decoration: none; padding: 10px; border-radius: 6px;">Dashboard</a>
                <a href="hotels.php" style="color: var(--gold); text-decoration: none; padding: 10px; border-radius: 6px; background: rgba(197,160,89,0.1);">Manage Hotels</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Rooms">←</a>
                    <div>
                        <h1>Manage <span>Inventory & Rates</span></h1>
                        <p style="color: var(--text-muted); margin-top: 5px; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($hotel['name']); ?> 
                        </p>
                        <div style="margin-top: 10px; display: inline-flex; background: rgba(255,255,255,0.05); padding: 5px; border-radius: 8px; align-items: center;">
                            <span style="color: var(--text-muted); font-size: 0.9rem; margin-right: 15px; margin-left: 5px;">Input Mode:</span>
                            <label style="cursor: pointer; padding: 5px 15px; border-radius: 6px; font-size: 0.9rem; background: rgba(255,255,255,0.1); color: #fff;" class="gst-toggle-label">
                                <input type="radio" name="ui_gst_toggle" value="net" checked style="display: none;"> Net Rate
                            </label>
                            <label style="cursor: pointer; padding: 5px 15px; border-radius: 6px; font-size: 0.9rem; color: #fff;" class="gst-toggle-label">
                                <input type="radio" name="ui_gst_toggle" value="gst_inc" style="display: none;"> GST Inc. Rate
                            </label>
                        </div>
                        <div id="gst-notice" style="display: none; margin-top: 10px; font-size: 0.85rem; color: #f1c40f; max-width: 600px; line-height: 1.4; background: rgba(241, 196, 15, 0.1); padding: 8px 12px; border-radius: 6px; border-left: 3px solid #f1c40f;">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> The GST Inc. preview here estimates tax per individual component. Actual GST slabs will be dynamically calculated at checkout based on the <em>total daily room bill</em> (Base Room + Extra Mattress + CNB).
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="position: relative;" id="bulkUpdateContainer">
                        <button type="button" class="btn btn-outline" data-action="toggle-bulk-dropdown"><i class="fas fa-bolt"></i> Bulk Update &#9662;</button>
                        <div class="bulk-dropdown" id="bulkDropdown">
                            <a href="#" data-action="open-bulk-modal" data-modal="inventory">Update Inventory</a>
                            <a href="#" data-action="open-bulk-modal" data-modal="rates">Update Rates</a>
                        </div>
                    </div>
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Rooms</a>
                </div>
            </div>

            <?php if (isset($_GET['saved'])): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> Inventory updated successfully!</div>
            <?php endif; ?>

            <?php if (empty($rooms)): ?>
                <div style="background: var(--secondary-dark); padding: 40px; text-align: center; border-radius: 8px;">
                    <i class="fas fa-bed fa-3x" style="color: rgba(255,255,255,0.1); margin-bottom: 15px;"></i>
                    <h3>No Rooms Found</h3>
                    <p style="color: var(--text-muted); margin-top: 10px;">Please add room categories first before managing inventory.</p>
                </div>
            <?php else: ?>
                <div class="nav-controls">
                    <a href="?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $prev_start; ?>" class="btn btn-outline"><i class="fas fa-chevron-left"></i> Previous 7 Days</a>
                    
                    <form method="GET" action="hotel-inventory.php" style="display: flex; align-items: center; gap: 10px;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                        <input type="date" name="start_date" value="<?php echo $start_date_str; ?>" class="inv-input" style="width: auto; padding: 8px 12px; font-weight: normal;">
                        <button type="submit" class="btn btn-outline" style="padding: 8px 15px;">Go</button>
                    </form>

                    <a href="?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $next_start; ?>" class="btn btn-outline">Next 7 Days <i class="fas fa-chevron-right"></i></a>
                </div>

                <form method="POST" action="hotel-inventory.php?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $start_date_str; ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="action" value="save_inventory">
                    
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th class="room-col">Room Category</th>
                                <?php foreach ($dates as $d): ?>
                                    <th>
                                        <div class="date-header">
                                            <span class="day"><?php echo date('D', strtotime($d)); ?></span>
                                            <span class="date"><?php echo date('d M', strtotime($d)); ?></span>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td class="room-col">
                                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff;"><?php echo htmlspecialchars($room['room_type_name']); ?></div>
                                        <div style="font-size: 0.85rem; color: var(--gold); margin-top: 4px;">Base Inv: <?php echo $room['total_rooms']; ?></div>
                                    </td>
                                    <?php foreach ($dates as $d): ?>
                                        <?php 
                                            // Determine current value: use override if exists, else fallback to base total_rooms
                                            $val = isset($inventory_data[$room['id']][$d]) ? $inventory_data[$room['id']][$d] : $room['total_rooms'];
                                        ?>
                                        <td style="vertical-align: middle;">
                                            <input type="number" 
                                                   name="inventory[<?php echo $room['id']; ?>][<?php echo $d; ?>]" 
                                                   class="inv-input" 
                                                   value="<?php echo $val; ?>" 
                                                   min="0"
                                                   autocomplete="off"
                                                   style="width: 70px; font-size: 1.1rem; border-color: rgba(46, 204, 113, 0.5); color: #2ecc71;">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                
                                <!-- Meal Plans for this room -->
                                <?php if(isset($plans_by_room[$room['id']])): ?>
                                    <?php foreach($plans_by_room[$room['id']] as $plan): ?>
                                    <tr class="plan-row">
                                        <td class="room-col" style="padding-left: 30px;">
                                            <div class="plan-name" data-action="toggle-plan" data-plan-id="<?php echo $plan['id']; ?>">
                                                <span><i class="fas fa-utensils" style="font-size: 0.8em; margin-right: 8px;"></i> <?php echo htmlspecialchars($plan['plan_name']); ?></span>
                                                <i class="fas fa-chevron-down" id="icon_plan_<?php echo $plan['id']; ?>" style="font-size: 0.8em; color: var(--text-muted);"></i>
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Click to toggle extra rates</div>
                                        </td>
                                        <?php foreach ($dates as $d): ?>
                                            <?php 
                                                $r = isset($rate_data[$plan['id']][$d]) ? $rate_data[$plan['id']][$d] : ['base_rate_2_pax'=>'', 'base_rate_1_pax'=>'', 'extra_adult_rate'=>'', 'cnb_rate'=>''];
                                            ?>
                                            <td style="vertical-align: top;">
                                                <div style="display: flex; gap: 8px; justify-content: center;">
                                                    <div style="text-align: left; width: 60px;">
                                                        <label style="font-size: 0.7rem; color: #aaa; display:block; margin-bottom: 3px;"><i class="fas fa-user-friends"></i> 2 Pax</label>
                                                        <input type="number" step="0.01" name="rates[<?php echo $plan['id']; ?>][<?php echo $d; ?>][base_rate_2_pax]" class="rate-input" value="<?php echo htmlspecialchars($r['base_rate_2_pax']); ?>" placeholder="₹">
                                                    </div>
                                                    <div style="text-align: left; width: 60px;">
                                                        <label style="font-size: 0.7rem; color: #aaa; display:block; margin-bottom: 3px;"><i class="fas fa-user"></i> 1 Pax</label>
                                                        <input type="number" step="0.01" name="rates[<?php echo $plan['id']; ?>][<?php echo $d; ?>][base_rate_1_pax]" class="rate-input" value="<?php echo htmlspecialchars($r['base_rate_1_pax']); ?>" placeholder="₹">
                                                    </div>
                                                </div>
                                                
                                                <div class="rate-details" id="details_plan_<?php echo $plan['id']; ?>_<?php echo $d; ?>">
                                                    <div class="rate-input-group">
                                                        <label>Ex. Mattress (>12y)</label>
                                                        <input type="number" step="0.01" name="rates[<?php echo $plan['id']; ?>][<?php echo $d; ?>][extra_adult_rate]" class="rate-input" value="<?php echo htmlspecialchars($r['extra_adult_rate']); ?>" placeholder="₹">
                                                    </div>
                                                    <div class="rate-input-group" style="margin-bottom: 0;">
                                                        <label>CNB (6-12y)</label>
                                                        <input type="number" step="0.01" name="rates[<?php echo $plan['id']; ?>][<?php echo $d; ?>][cnb_rate]" class="rate-input" value="<?php echo htmlspecialchars($r['cnb_rate']); ?>" placeholder="₹">
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 30px; text-align: right;">
                        <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Save Inventory Updates</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

        <!-- Bulk Update Inventory Modal -->
    <div id="bulkInventoryModal" class="admin-modal">
        <div style="background: #0b0f19; width: 800px; max-height: 90vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="padding: 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0;">Bulk Update <span style="color: var(--gold);">Inventory</span></h2>
                <button aria-label="Close" type="button" data-action="close-modal" data-target="bulkInventoryModal" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="hotel-inventory.php?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $start_date_str; ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="bulk_update_inventory">
                <div style="padding: 30px;">
                    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                        <div style="flex: 1;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">From Date</label>
                            <input type="date" name="from_date" class="inv-input" style="width: 100%;" required>
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">To Date</label>
                            <input type="date" name="to_date" class="inv-input" style="width: 100%;" required>
                        </div>
                        <div style="flex: 2;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">Selected Days</label>
                            <div style="display: flex; gap: 5px;">
                                <?php 
                                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                foreach($days as $idx => $d) {
                                    $val = ($idx + 1) % 7; // PHP date('w') maps 0=Sun, 1=Mon
                                    echo "<label style='background: rgba(255,255,255,0.05); padding: 5px 10px; border-radius: 4px; cursor: pointer;'><input type='checkbox' name='days[]' value='$val' checked style='margin-right: 5px;'>$d</label>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <h3 style="color: #fff; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Update Inventory Below</h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <?php foreach($rooms as $r): ?>
                        <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 6px; flex: 1 1 30%;">
                            <label style="display: block; color: #fff; font-weight: 600; margin-bottom: 8px;"><?php echo htmlspecialchars($r['room_type_name']); ?></label>
                            <input type="number" name="bulk_inv[<?php echo $r['id']; ?>]" class="inv-input" style="width: 100%; text-align: left;" placeholder="Leave empty to skip">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="padding: 20px 30px; border-top: 1px solid rgba(255,255,255,0.1); text-align: right; background: rgba(0,0,0,0.2);">
                    <button type="button" class="btn btn-outline" style="margin-right: 10px;" data-action="close-modal" data-target="bulkInventoryModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Inventory Updates</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Update Rates Modal -->
    <div id="bulkRatesModal" class="admin-modal">
        <div style="background: #0b0f19; width: 800px; max-height: 90vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="padding: 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0;">Bulk Update <span style="color: var(--gold);">Rates</span></h2>
                <button aria-label="Close" type="button" data-action="close-modal" data-target="bulkRatesModal" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="hotel-inventory.php?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $start_date_str; ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="bulk_update_rates">
                <div style="padding: 30px;">
                    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                        <div style="flex: 1;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">From Date</label>
                            <input type="date" name="from_date" class="inv-input" style="width: 100%;" required>
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">To Date</label>
                            <input type="date" name="to_date" class="inv-input" style="width: 100%;" required>
                        </div>
                        <div style="flex: 2;">
                            <label style="display: block; color: var(--text-muted); margin-bottom: 8px;">Selected Days</label>
                            <div style="display: flex; gap: 5px;">
                                <?php 
                                foreach($days as $idx => $d) {
                                    $val = ($idx + 1) % 7; // PHP date('w') maps 0=Sun, 1=Mon
                                    echo "<label style='background: rgba(255,255,255,0.05); padding: 5px 10px; border-radius: 4px; cursor: pointer;'><input type='checkbox' name='days[]' value='$val' checked style='margin-right: 5px;'>$d</label>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <h3 style="color: #fff; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Update Rates Below</h3>
                    
                    <?php foreach($rooms as $r): ?>
                        <div style="margin-bottom: 30px;">
                            <h4 style="color: var(--gold); margin-bottom: 10px; font-size: 1.2rem;"><?php echo htmlspecialchars($r['room_type_name']); ?></h4>
                            <?php if(isset($plans_by_room[$r['id']])): ?>
                                <?php foreach($plans_by_room[$r['id']] as $plan): ?>
                                    <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: 6px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.05);">
                                        <div style="font-weight: 600; color: #fff; margin-bottom: 15px;"><i class="fas fa-utensils" style="color: var(--text-muted); margin-right: 5px;"></i> <?php echo htmlspecialchars($plan['plan_name']); ?></div>
                                        <div style="display: flex; gap: 15px;">
                                            <div style="flex: 1;">
                                                <label style="display: block; color: var(--text-muted); font-size: 0.8rem; margin-bottom: 5px;">2 Pax</label>
                                                <input type="number" step="0.01" name="bulk_rates[<?php echo $plan['id']; ?>][base_rate_2_pax]" class="inv-input" style="width: 100%; text-align: left;" placeholder="₹ Leave empty to skip">
                                            </div>
                                            <div style="flex: 1;">
                                                <label style="display: block; color: var(--text-muted); font-size: 0.8rem; margin-bottom: 5px;">1 Pax</label>
                                                <input type="number" step="0.01" name="bulk_rates[<?php echo $plan['id']; ?>][base_rate_1_pax]" class="inv-input" style="width: 100%; text-align: left;" placeholder="₹">
                                            </div>
                                            <div style="flex: 1;">
                                                <label style="display: block; color: var(--text-muted); font-size: 0.8rem; margin-bottom: 5px;">Ex. Mattress</label>
                                                <input type="number" step="0.01" name="bulk_rates[<?php echo $plan['id']; ?>][extra_adult_rate]" class="inv-input" style="width: 100%; text-align: left;" placeholder="₹">
                                            </div>
                                            <div style="flex: 1;">
                                                <label style="display: block; color: var(--text-muted); font-size: 0.8rem; margin-bottom: 5px;">CNB (6-12y)</label>
                                                <input type="number" step="0.01" name="bulk_rates[<?php echo $plan['id']; ?>][cnb_rate]" class="inv-input" style="width: 100%; text-align: left;" placeholder="₹">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--text-muted); font-size: 0.9rem;">No meal plans assigned.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                </div>
                <div style="padding: 20px 30px; border-top: 1px solid rgba(255,255,255,0.1); text-align: right; background: rgba(0,0,0,0.2);">
                    <button type="button" class="btn btn-outline" style="margin-right: 10px;" data-action="close-modal" data-target="bulkRatesModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Rates Updates</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleRadios = document.querySelectorAll('input[name="ui_gst_toggle"]');
            const rateInputs = document.querySelectorAll('input[type="number"][name*="rate"]');
            
            function getGrossFromNet(net) {
                if (!net || isNaN(net)) return '';
                if (net <= 1000) return net;
                if (net <= 7500) return Math.round(net * 1.05);
                return Math.round(net * 1.18);
            }
            
            function getNetFromGross(gross) {
                if (!gross || isNaN(gross)) return '';
                if (gross <= 1000) return gross;
                if (gross <= 7875) return gross / 1.05; // 7500 * 1.05 = 7875
                return gross / 1.18;
            }
            
            toggleRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    document.querySelectorAll('.gst-toggle-label').forEach(lbl => lbl.style.background = 'transparent');
                    e.target.parentElement.style.background = 'rgba(255,255,255,0.1)';
                    
                    const isGstInc = e.target.value === 'gst_inc';
                    document.getElementById('gst-notice').style.display = isGstInc ? 'block' : 'none';
                    
                    rateInputs.forEach(input => {
                        const val = parseFloat(input.value);
                        if (!isNaN(val)) {
                            // Format cleanly to 2 decimals
                            input.value = isGstInc ? getGrossFromNet(val) : getNetFromGross(val).toFixed(2);
                        }
                    });
                });
            });
            
            // On form submit (both forms: main inventory and bulk updates), reverse calc if gst_inc is selected
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    const isGstInc = document.querySelector('input[name="ui_gst_toggle"]:checked').value === 'gst_inc';
                    if (isGstInc) {
                        const formInputs = form.querySelectorAll('input[type="number"][name*="rate"]');
                        formInputs.forEach(input => {
                            const val = parseFloat(input.value);
                            if (!isNaN(val)) {
                                input.value = getNetFromGross(val).toFixed(2);
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
