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
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_vehicle_date (vehicle_id, rate_date),
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (PDOException $e) {
    // ignore
}

// Fetch all vehicles
$stmt = $pdo->query("
    SELECT v.id, v.name, c.name as class_name, v.price_per_day 
    FROM vehicles v 
    LEFT JOIN cab_classes c ON v.cab_class_id = c.id 
    ORDER BY c.id, v.id
");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$messageType = '';

// Handle Bulk Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_update') {
    $vehicle_id = (int)$_POST['vehicle_id'];
    $start_date = $_POST['bulk_start_date'];
    $end_date = $_POST['bulk_end_date'];
    $price = (float)$_POST['bulk_price'];

    if ($vehicle_id && $start_date && $end_date && $price >= 0) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        if ($start <= $end) {
            $interval = new DateInterval('P1D');
            $end->add($interval); // Include end date
            $period = new DatePeriod($start, $interval, $end);

            $stmt = $pdo->prepare("
                INSERT INTO vehicle_rates (vehicle_id, rate_date, price) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE price = VALUES(price)
            ");

            foreach ($period as $date) {
                $stmt->execute([$vehicle_id, $date->format('Y-m-d'), $price]);
            }
            $message = "Rates successfully updated for the selected date range.";
            $messageType = "success";
        } else {
            $message = "End date must be after start date.";
            $messageType = "error";
        }
    } else {
        $message = "Please fill all fields correctly.";
        $messageType = "error";
    }
}

// Handle Grid Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_grid') {
    if (isset($_POST['rates']) && is_array($_POST['rates'])) {
        $stmt = $pdo->prepare("
            INSERT INTO vehicle_rates (vehicle_id, rate_date, price) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE price = VALUES(price)
        ");
        foreach ($_POST['rates'] as $v_id => $date_values) {
            foreach ($date_values as $date => $price) {
                if ($price !== '') {
                    $stmt->execute([$v_id, $date, (float)$price]);
                } else {
                    // If empty, delete the override
                    $delStmt = $pdo->prepare("DELETE FROM vehicle_rates WHERE vehicle_id = ? AND rate_date = ?");
                    $delStmt->execute([$v_id, $date]);
                }
            }
        }
        $message = "Grid rates saved successfully.";
        $messageType = "success";
    }
}

// Calculate Date Range (14 days view)
$start_date_str = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$start_time = strtotime($start_date_str);
$dates = [];
for ($i = 0; $i < 14; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days", $start_time));
}
$prev_start = date('Y-m-d', strtotime('-14 days', $start_time));
$next_start = date('Y-m-d', strtotime('+14 days', $start_time));

// Fetch Overrides for the current view
$vehicle_ids = array_column($vehicles, 'id');
$rates_data = [];
if (!empty($vehicle_ids)) {
    $placeholders = implode(',', array_fill(0, count($vehicle_ids), '?'));
    $sql = "SELECT vehicle_id, rate_date, price 
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
            <div class="header" style="margin-bottom: 2rem;">
                <h1>Manage <span style="color: var(--admin-gold);">Cab Rates</span></h1>
                <p style="color: var(--admin-text-muted); margin-top: 5px;">Set specific daily rates for vehicles. Leaves cells empty to use the default price.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Bulk Update Section -->
            <div class="bulk-card">
                <h3>Bulk Update Rates</h3>
                <form method="POST" action="cab-rates.php?start_date=<?php echo $start_date_str; ?>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
">
                    <input type="hidden" name="action" value="bulk_update">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Vehicle</label>
                            <select name="vehicle_id" class="form-control" required>
                                <option value="">Select a Vehicle...</option>
                                <?php foreach ($vehicles as $v): ?>
                                    <option value="<?php echo $v['id']; ?>">
                                        <?php echo htmlspecialchars($v['name'] . ' (' . $v['class_name'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="bulk_start_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="bulk_end_date" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                        </div>
                        <div class="form-group">
                            <label>Price / Day (₹)</label>
                            
<label for="input_cebca36e" class="sr-only">e.g. 4000</label>
<input id="input_cebca36e" type="number" name="bulk_price" class="form-control" required min="0" placeholder="e.g. 4000">
                        </div>
                        <div class="form-group" style="flex: 0; min-width: auto;">
                            <button type="submit" class="btn btn-gold">Apply</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Grid Navigation -->
            <div class="nav-controls">
                <a href="?start_date=<?php echo $prev_start; ?>" class="btn btn-outline"><i class="fas fa-chevron-left"></i> Prev 14 Days</a>
                
                <form method="GET" action="cab-rates.php" style="display: flex; align-items: center; gap: 10px;">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <input type="date" name="start_date" value="<?php echo $start_date_str; ?>" class="form-control" style="padding: 8px 12px; width: auto;">
                    <button type="submit" class="btn btn-outline" style="padding: 8px 15px;">Go</button>
                </form>

                <a href="?start_date=<?php echo $next_start; ?>" class="btn btn-outline">Next 14 Days <i class="fas fa-chevron-right"></i></a>
            </div>

            <!-- Pricing Grid -->
            <form method="POST" action="cab-rates.php?start_date=<?php echo $start_date_str; ?>
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
">
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
                                        <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 4px;">Default: ₹<?php echo number_format($v['price_per_day']); ?></div>
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
                                                   placeholder="<?php echo htmlspecialchars($v['price_per_day']); ?>"
                                                   autocomplete="off">
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
</body>
</html>
