<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

// Auto-create table if not exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS vehicle_rates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            vehicle_id INT NOT NULL,
            rate_date DATE NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            price_per_hour DECIMAL(10,2) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_vehicle_date (vehicle_id, rate_date),
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (PDOException $e) {
    // ignore
}

// Auto-migrate: replace 6 hourly columns with single price_per_hour
try {
    $existing = $pdo->query("SHOW COLUMNS FROM vehicle_rates")->fetchAll(PDO::FETCH_COLUMN);
    $old_cols = ['price_2h','price_4h','price_6h','price_8h','price_10h','price_12h'];
    foreach ($old_cols as $col) {
        if (in_array($col, $existing, true)) {
            $pdo->exec("ALTER TABLE vehicle_rates DROP COLUMN $col");
        }
    }
    if (!in_array('price_per_hour', $existing, true)) {
        $pdo->exec("ALTER TABLE vehicle_rates ADD COLUMN price_per_hour DECIMAL(10,2) NULL");
    }
} catch (PDOException $e) {
    // ignore — table may not exist yet
}

// Fetch all vehicles
$stmt = $pdo->query("
    SELECT v.id, v.name, v.cab_class_id, c.name as class_name
    FROM vehicles v 
    LEFT JOIN cab_classes c ON v.cab_class_id = c.id 
    ORDER BY c.id, v.id
");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cab_classes_list = $pdo ? $pdo->query("SELECT id, name FROM cab_classes WHERE is_active=1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC) : [];

$message = '';
$messageType = '';

// Rate Type whitelist
$allowed_rates = ['price', 'price_per_hour', 'extra_km_rate'];
$rate_type = in_array($_GET['rate_type'] ?? '', $allowed_rates, true) ? $_GET['rate_type'] : 'price';

// Calculate Date Range (14 days view) — must be before POST handlers so redirect can use $start_date_str
$start_date_str = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$start_time = strtotime($start_date_str);
$dates = [];
for ($i = 0; $i < 14; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days", $start_time));
}
$prev_start = date('Y-m-d', strtotime('-14 days', $start_time));
$next_start = date('Y-m-d', strtotime('+14 days', $start_time));

// Handle Bulk Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_update') {
    $from = strtotime($_POST['from_date'] ?? '');
    $to = strtotime($_POST['to_date'] ?? '');
    $selected_days = isset($_POST['days']) && is_array($_POST['days']) ? $_POST['days'] : [];
    $update_dates = [];

    if ($from && $to && $from <= $to && !empty($selected_days)) {
        for ($time = $from; $time <= $to; $time = strtotime('+1 day', $time)) {
            if (in_array(date('w', $time), $selected_days)) {
                $update_dates[] = date('Y-m-d', $time);
            }
        }
    }

    $has_rate = false;
    if (!empty($update_dates) && isset($_POST['bulk_rates']) && is_array($_POST['bulk_rates'])) {
        $stmt = $pdo->prepare("
            INSERT INTO vehicle_rates (vehicle_id, rate_date, $rate_type)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE $rate_type = VALUES($rate_type)
        ");
        foreach ($_POST['bulk_rates'] as $vehicle_id => $price) {
            $vehicle_id = (int)$vehicle_id;
            $price = (float)$price;
            if ($vehicle_id > 0 && $price > 0) {
                $has_rate = true;
                foreach ($update_dates as $date) {
                    $stmt->execute([$vehicle_id, $date, $price]);
                }
            }
        }
    }

    // Hourly mode: bulk-apply the additional-km charge (blank = leave unchanged)
    $has_ekm = false;
    if ($rate_type === 'price_per_hour' && !empty($update_dates) && isset($_POST['bulk_extra_km']) && is_array($_POST['bulk_extra_km'])) {
        $ekStmt = $pdo->prepare("
            INSERT INTO vehicle_rates (vehicle_id, rate_date, price, extra_km_rate)
            VALUES (?, ?, 0, ?)
            ON DUPLICATE KEY UPDATE extra_km_rate = VALUES(extra_km_rate)
        ");
        foreach ($_POST['bulk_extra_km'] as $ek_vid => $ek_rate) {
            $ek_vid = (int)$ek_vid;
            if ($ek_vid > 0 && $ek_rate !== '' && $ek_rate !== null) {
                $ek_rate = (float)$ek_rate;
                if ($ek_rate >= 0) {
                    $has_ekm = true;
                    foreach ($update_dates as $date) {
                        $ekStmt->execute([$ek_vid, $date, $ek_rate]);
                    }
                }
            }
        }
    }

    if (!empty($update_dates) && ($has_rate || $has_ekm)) {
        header("Location: cab-rates.php?start_date=$start_date_str&rate_type=$rate_type&saved=1");
        exit;
    } else {
        $message = $rate_type === 'price_per_hour'
            ? "Please select dates, days, and enter at least one rate or Additional Km charge."
            : "Please select dates, days, and enter at least one rate.";
        $messageType = "error";
    }
}

// Handle Grid Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_grid') {
    if (isset($_POST['rates']) && is_array($_POST['rates'])) {
        $stmt = $pdo->prepare("
            INSERT INTO vehicle_rates (vehicle_id, rate_date, $rate_type) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE $rate_type = VALUES($rate_type)
        ");
        foreach ($_POST['rates'] as $v_id => $date_values) {
            foreach ($date_values as $date => $price) {
                if ($price !== '') {
                    $stmt->execute([$v_id, $date, (float)$price]);
                } else {
                    // Empty value: if only this column exists in the row, delete the row; otherwise NULL it out
                    if ($rate_type === 'price') {
                        $delStmt = $pdo->prepare("DELETE FROM vehicle_rates WHERE vehicle_id = ? AND rate_date = ?");
                        $delStmt->execute([$v_id, $date]);
                    } else {
                        $nullStmt = $pdo->prepare("UPDATE vehicle_rates SET $rate_type = NULL WHERE vehicle_id = ? AND rate_date = ?");
                        $nullStmt->execute([$v_id, $date]);
                    }
                }
            }
        }

        // Hourly mode: persist the per-vehicle extra-km rate alongside the hourly rate
        if ($rate_type === 'price_per_hour' && isset($_POST['extra_km']) && is_array($_POST['extra_km'])) {
            $ekStmt = $pdo->prepare("
                INSERT INTO vehicle_rates (vehicle_id, rate_date, price, extra_km_rate)
                VALUES (?, ?, 0, ?)
                ON DUPLICATE KEY UPDATE extra_km_rate = VALUES(extra_km_rate)
            ");
            $ekNullStmt = $pdo->prepare("UPDATE vehicle_rates SET extra_km_rate = NULL WHERE vehicle_id = ? AND rate_date = ?");
            foreach ($_POST['extra_km'] as $ek_vid => $ek_date_values) {
                foreach ($ek_date_values as $ek_date => $ek_rate) {
                    if ($ek_rate !== '') {
                        $ekStmt->execute([(int)$ek_vid, $ek_date, (float)$ek_rate]);
                    } else {
                        $ekNullStmt->execute([(int)$ek_vid, $ek_date]);
                    }
                }
            }
        }

        $message = "Grid rates saved successfully.";
        $messageType = "success";
    }
}

// Fetch Overrides for the current view
$vehicle_ids = array_column($vehicles, 'id');
$rates_data = [];
$extra_km_data = [];
if (!empty($vehicle_ids)) {
    $placeholders = implode(',', array_fill(0, count($vehicle_ids), '?'));
    $sql = "SELECT vehicle_id, rate_date, $rate_type as price, extra_km_rate
            FROM vehicle_rates 
            WHERE vehicle_id IN ($placeholders) 
            AND rate_date >= ? AND rate_date <= ?";
    
    $params = $vehicle_ids;
    $params[] = $dates[0];
    $params[] = $dates[13];
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $overrides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($overrides as $ov) {
        $rates_data[$ov['vehicle_id']][$ov['rate_date']] = $ov['price'];
        $extra_km_data[$ov['vehicle_id']][$ov['rate_date']] = $ov['extra_km_rate'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cab Rates | Leisure Loop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
    <link rel="stylesheet" href="../css/admin-overrides.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>Manage <span style="color: var(--admin-gold);">Cab Rates</span></h1>
                    <p style="color: var(--admin-text-muted); margin-top: 5px;">Set specific daily rates for vehicles.</p>
                </div>
                <button type="button" class="btn btn-outline" data-action="open-bulk-modal" data-modal="cab-bulk">
                    <i class="fas fa-bolt"></i> BULK UPDATE
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>


            <!-- Grid Navigation -->
            <div class="nav-controls">
                <a href="?start_date=<?php echo $prev_start; ?>&rate_type=<?php echo $rate_type; ?>" class="btn btn-outline"><i class="fas fa-chevron-left"></i> Prev 14 Days</a>
                
                <form method="GET" action="cab-rates.php" style="display: flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    
                    <select name="rate_type" class="form-control" style="padding: 8px 12px; width: auto;">
                        <option value="price" <?= $rate_type === 'price' ? 'selected' : '' ?>>Disposal / Daily</option>
                        <option value="price_per_hour" <?= $rate_type === 'price_per_hour' ? 'selected' : '' ?>>Hourly: Per Hour Rate</option>
                    </select>

                    <input type="date" name="start_date" value="<?php echo $start_date_str; ?>" class="form-control" style="padding: 8px 12px; width: auto;">
                    <button type="submit" class="btn btn-outline" style="padding: 8px 15px;">Go</button>
                </form>

                <a href="?start_date=<?php echo $next_start; ?>&rate_type=<?php echo $rate_type; ?>" class="btn btn-outline">Next 14 Days <i class="fas fa-chevron-right"></i></a>
            </div>

            <!-- Pricing Grid -->
            <form method="POST" action="cab-rates.php?start_date=<?php echo $start_date_str; ?>&rate_type=<?= $rate_type ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="save_grid">
                
                <div style="overflow-x: auto; padding-bottom: 15px;">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th class="room-col">Vehicle</th>
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
                            <?php foreach ($vehicles as $v): ?>
                                <tr>
                                    <td class="room-col">
                                        <?php echo htmlspecialchars($v['name']); ?>
                                    </td>
                                    <?php foreach ($dates as $d): ?>
                                        <?php 
                                            // Check if there is an override
                                            $val = isset($rates_data[$v['id']][$d]) ? $rates_data[$v['id']][$d] : '';
                                        ?>
                                        <td>
                                            <input type="number" 
                                                   name="rates[<?php echo $v['id']; ?>][<?php echo $d; ?>]" 
                                                   class="inv-input" 
                                                   value="<?php echo $val; ?>" 
                                                   min="0"
                                                   placeholder="—"
                                                   autocomplete="off">
                                            <?php if ($rate_type === 'price_per_hour'): ?>
                                            <input type="number"
                                                   name="extra_km[<?php echo $v['id']; ?>][<?php echo $d; ?>]"
                                                   id="extra-km-<?php echo $v['id']; ?>-<?php echo $d; ?>"
                                                   class="inv-input"
                                                   value="<?php echo $extra_km_data[$v['id']][$d] ?? ''; ?>"
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="Extra ₹/km"
                                                   aria-label="Extra kilometer rate for <?php echo htmlspecialchars($v['name']); ?> on <?php echo $d; ?>"
                                                   autocomplete="off">
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Save Grid Rates</button>
                </div>
            </form>

        </main>
    </div>

    <!-- Bulk Update Modal -->
    <div id="cabBulkRatesModal" class="admin-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="admin-modal-inner">
            <div class="cab-bulk-modal-header">
                <h2>Bulk Update <span>Cab Rates<?= $rate_type === 'price_per_hour' ? ' - Per Hour' : '' ?></span></h2>
                <button aria-label="Close" type="button" data-action="close-modal" data-target="cabBulkRatesModal" class="cab-bulk-modal-close">&times;</button>
            </div>
            <form method="POST" action="cab-rates.php?start_date=<?= $start_date_str ?>&rate_type=<?= $rate_type ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="bulk_update">
                <div class="cab-bulk-modal-body">
                    <!-- Date Row -->
                    <div class="cab-bulk-date-row">
                        <div class="form-group">
                            <label for="cab-bulk-from">From Date</label>
                            <input type="date" id="cab-bulk-from" name="from_date" class="inv-input" required>
                        </div>
                        <div class="form-group">
                            <label for="cab-bulk-to">To Date</label>
                            <input type="date" id="cab-bulk-to" name="to_date" class="inv-input" required>
                        </div>
                        <div class="form-group days-group">
                            <label>Selected Days</label>
                            <div class="cab-bulk-day-chips">
                                <?php
                                $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                                foreach ($days as $idx => $d):
                                    $val = ($idx + 1) % 7;
                                ?>
                                <label class="cab-bulk-day-chip">
                                    <input type="checkbox" name="days[]" value="<?= $val ?>" checked> <?= $d ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle: By Class / By Vehicle -->
                    <div class="cab-bulk-toggle-row">
                        <label class="cab-bulk-toggle-label is-active">
                            <input type="radio" name="bulk_mode" value="class" class="cab-bulk-radio" checked> Update By Class
                        </label>
                        <label class="cab-bulk-toggle-label">
                            <input type="radio" name="bulk_mode" value="vehicle" class="cab-bulk-radio"> Update By Vehicle
                        </label>
                    </div>

                    <!-- Dropdowns -->
                    <div class="cab-bulk-select-row is-active" id="cab-bulk-class-select-row">
                        <label for="cab-bulk-class-select">Select Cab Class</label>
                        <select id="cab-bulk-class-select" name="bulk_class_id" class="inv-input">
                            <option value="">Choose a class...</option>
                            <?php foreach ($cab_classes_list as $cc): ?>
                            <option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cab-bulk-select-row" id="cab-bulk-vehicle-select-row">
                        <label for="cab-bulk-vehicle-select">Select Vehicle</label>
                        <select id="cab-bulk-vehicle-select" name="bulk_vehicle_id" class="inv-input">
                            <option value="">Choose a vehicle...</option>
                            <?php foreach ($vehicles as $v): ?>
                            <option value="<?= $v['id'] ?>" data-class-id="<?= $v['cab_class_id'] ?>"><?= htmlspecialchars($v['name'] . ' (' . $v['class_name'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dynamic Input Grid -->
                    <div id="cab-bulk-rates-container"></div>

                    <!-- Hidden Template Store -->
                    <div id="cab-bulk-template-store" class="cab-bulk-hidden" aria-hidden="true">
                        <?php foreach ($vehicles as $v): ?>
                        <div class="cab-bulk-input-card"
                             data-vehicle-id="<?= $v['id'] ?>"
                             data-class-id="<?= $v['cab_class_id'] ?>">
                            <label class="card-label" for="cab-bulk-rate-<?= $v['id'] ?>">
                                <?= htmlspecialchars($v['name']) ?>
                            </label>
                            <input type="number" step="0.01" min="0"
                                   name="bulk_rates[<?= $v['id'] ?>]"
                                   id="cab-bulk-rate-<?= $v['id'] ?>"
                                   class="inv-input"
                                   placeholder="<?= $rate_type === 'price_per_hour' ? '₹ Rate per hour' : '₹ Rate per day' ?>" disabled>
                            <?php if ($rate_type === 'price_per_hour'): ?>
                            <input type="number" step="0.01" min="0"
                                   name="bulk_extra_km[<?= $v['id'] ?>]"
                                   id="cab-bulk-ekm-<?= $v['id'] ?>"
                                   class="inv-input"
                                   placeholder="₹ Additional Km charge"
                                   aria-label="Additional kilometer charge for <?= htmlspecialchars($v['name']) ?>"
                                   disabled>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="cab-bulk-modal-footer">
                    <button type="button" class="btn btn-outline" data-action="close-modal" data-target="cabBulkRatesModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Bulk Rates</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
