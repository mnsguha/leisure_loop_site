<?php
$file = 'g:\Antigravity\leisure_loop_site\public\admin\hotel-inventory.php';
$content = file_get_contents($file);

// Replace Room Fetch
$old1 = <<<'EOD'
// Fetch Rooms for this hotel
$stmt = $pdo->prepare("SELECT id, room_type_name, total_rooms FROM hotel_rooms WHERE hotel_id = ? ORDER BY id ASC");
$stmt->execute([$hotel_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Save Inventory Form Submission
EOD;

$new1 = <<<'EOD'
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

// Handle Save Inventory Form Submission
EOD;
$content = str_replace($old1, $new1, $content);

// Replace Save Logic
$old2 = <<<'EOD'
// Handle Save Inventory Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_inventory') {
    if (isset($_POST['inventory']) && is_array($_POST['inventory'])) {
        foreach ($_POST['inventory'] as $room_id => $date_values) {
            foreach ($date_values as $date => $qty) {
                $qty = (int)$qty;
                // Insert or Update inventory (MariaDB/MySQL syntax)
                $stmt = $pdo->prepare("INSERT INTO room_inventory (room_id, inventory_date, available_rooms) 
                                       VALUES (?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE available_rooms = VALUES(available_rooms)");
                $stmt->execute([$room_id, $date, $qty]);
            }
        }
    }
    // Refresh to show saved data
    header("Location: hotel-inventory.php?hotel_id=$hotel_id&start_date=$start_date_str&saved=1");
    exit;
}

// Fetch Overrides for the current view
EOD;

$new2 = <<<'EOD'
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
EOD;
$content = str_replace($old2, $new2, $content);

// Replace Fetch Overrides
$old3 = <<<'EOD'
// Fetch Overrides for the current view
$room_ids = array_column($rooms, 'id');
$inventory_data = [];
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
    $overrides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($overrides as $ov) {
        $inventory_data[$ov['room_id']][$ov['inventory_date']] = $ov['available_rooms'];
    }
}
?>
EOD;

$new3 = <<<'EOD'
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
EOD;
$content = str_replace($old3, $new3, $content);

// Replace CSS
$old4 = <<<'EOD'
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; }
    </style>
EOD;

$new4 = <<<'EOD'
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; }
        .plan-row td { background: rgba(0,0,0,0.1); border-top: 1px solid rgba(255,255,255,0.05); }
        .plan-name { font-weight: 600; color: #3498db; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; }
        .rate-details { display: none; margin-top: 15px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 4px; border: 1px solid rgba(255,255,255,0.05); }
        .rate-input-group { margin-bottom: 8px; text-align: left; }
        .rate-input-group label { display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px; }
        .rate-input { width: 100%; padding: 4px 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 3px; font-size: 0.85rem; }
        .rate-input:focus { outline: none; border-color: #3498db; }
        
        /* Bulk Dropdown */
        .bulk-dropdown { position: absolute; top: 100%; right: 0; background: var(--secondary-dark); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.5); display: none; z-index: 100; min-width: 150px; margin-top: 5px; }
        .bulk-dropdown a { display: block; padding: 10px 15px; color: #fff; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .bulk-dropdown a:last-child { border-bottom: none; }
        .bulk-dropdown a:hover { background: rgba(255,255,255,0.05); color: var(--gold); }
    </style>
EOD;
$content = str_replace($old4, $new4, $content);

// Replace Header actions
$old5 = <<<'EOD'
            <div class="header">
                <div>
                    <h1>Manage <span>Inventory</span></h1>
                    <p style="color: var(--text-muted); margin-top: 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($hotel['name']); ?></p>
                </div>
                <div>
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Rooms</a>
                </div>
            </div>
EOD;

$new5 = <<<'EOD'
            <div class="header">
                <div>
                    <h1>Manage <span>Inventory & Rates</span></h1>
                    <p style="color: var(--text-muted); margin-top: 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($hotel['name']); ?></p>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="position: relative;" id="bulkUpdateContainer">
                        <button type="button" class="btn btn-outline" onclick="toggleBulkDropdown()"><i class="fas fa-bolt"></i> Bulk Update &#9662;</button>
                        <div class="bulk-dropdown" id="bulkDropdown">
                            <a href="#" onclick="openBulkModal('inventory')">Update Inventory</a>
                            <a href="#" onclick="openBulkModal('rates')">Update Rates</a>
                        </div>
                    </div>
                    <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Rooms</a>
                </div>
            </div>
EOD;
$content = str_replace($old5, $new5, $content);

// Replace tbody
$old6 = <<<'EOD'
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td class="room-col">
                                        <?php echo htmlspecialchars($room['room_type_name']); ?>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Base Inv: <?php echo $room['total_rooms']; ?></div>
                                    </td>
                                    <?php foreach ($dates as $d): ?>
                                        <?php 
                                            // Determine current value: use override if exists, else fallback to base total_rooms
                                            $val = isset($inventory_data[$room['id']][$d]) ? $inventory_data[$room['id']][$d] : $room['total_rooms'];
                                        ?>
                                        <td>
                                            <input type="number" 
                                                   name="inventory[<?php echo $room['id']; ?>][<?php echo $d; ?>]" 
                                                   class="inv-input" 
                                                   value="<?php echo $val; ?>" 
                                                   min="0"
                                                   autocomplete="off">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
EOD;

$new6 = <<<'EOD'
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
                                            <div class="plan-name" onclick="togglePlanDetails(<?php echo $plan['id']; ?>)">
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
EOD;
$content = str_replace($old6, $new6, $content);

// Add JS
$js = <<<'EOD'
    </div>

    <!-- Modals will be injected here later -->

    <script>
    function togglePlanDetails(planId) {
        var icon = document.getElementById('icon_plan_' + planId);
        if (icon.classList.contains('fa-chevron-down')) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
        
        // Find all detail boxes for this plan
        var details = document.querySelectorAll('[id^="details_plan_' + planId + '_"]');
        details.forEach(function(el) {
            if (el.style.display === 'block') {
                el.style.display = 'none';
            } else {
                el.style.display = 'block';
            }
        });
    }

    function toggleBulkDropdown() {
        var dropdown = document.getElementById('bulkDropdown');
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    }

    document.addEventListener('click', function(event) {
        var container = document.getElementById('bulkUpdateContainer');
        if (container && !container.contains(event.target)) {
            document.getElementById('bulkDropdown').style.display = 'none';
        }
    });

    function openBulkModal(type) {
        event.preventDefault();
        document.getElementById('bulkDropdown').style.display = 'none';
        
        if (type === 'inventory') {
            document.getElementById('bulkInventoryModal').style.display = 'flex';
        } else if (type === 'rates') {
            document.getElementById('bulkRatesModal').style.display = 'flex';
        }
    }
    
    function closeBulkModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    </script>
</body>
EOD;

$content = str_replace('    </div>
</body>', $js, $content);

file_put_contents($file, $content);
echo "Updated hotel-inventory.php\n";
