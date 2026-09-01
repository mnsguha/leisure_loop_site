<?php
$file = 'g:\Antigravity\leisure_loop_site\public\admin\hotel-inventory.php';
$content = file_get_contents($file);

$modals = <<<'EOD'
    <!-- Bulk Update Inventory Modal -->
    <div id="bulkInventoryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: var(--secondary-dark); width: 800px; max-height: 90vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="padding: 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0;">Bulk Update <span style="color: var(--gold);">Inventory</span></h2>
                <button type="button" onclick="closeBulkModal('bulkInventoryModal')" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="hotel-inventory.php?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $start_date_str; ?>">
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
                    <button type="button" class="btn btn-outline" style="margin-right: 10px;" onclick="closeBulkModal('bulkInventoryModal')">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Inventory Updates</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Update Rates Modal -->
    <div id="bulkRatesModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: var(--secondary-dark); width: 800px; max-height: 90vh; overflow-y: auto; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="padding: 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0;">Bulk Update <span style="color: var(--gold);">Rates</span></h2>
                <button type="button" onclick="closeBulkModal('bulkRatesModal')" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="hotel-inventory.php?hotel_id=<?php echo $hotel_id; ?>&start_date=<?php echo $start_date_str; ?>">
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
                    <button type="button" class="btn btn-outline" style="margin-right: 10px;" onclick="closeBulkModal('bulkRatesModal')">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Rates Updates</button>
                </div>
            </form>
        </div>
    </div>
EOD;

$content = str_replace('<!-- Modals will be injected here later -->', $modals, $content);

// Add Backend logic to handle 'bulk_update_inventory' and 'bulk_update_rates'
$backend_logic = <<<'EOD'
// Handle Save Inventory Form Submission
EOD;

$new_backend_logic = <<<'EOD'
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
EOD;

$content = str_replace($backend_logic, $new_backend_logic, $content);

file_put_contents($file, $content);
echo "Injected modals into hotel-inventory.php\n";
