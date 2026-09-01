<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$dep = [
    'package_id' => '',
    'start_date' => '',
    'end_date' => '',
    'price' => '',
    'total_seats' => '20',
    'available_seats' => '20',
    'status' => 'Open',
    'is_active' => 1
];

if ($id > 0 && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM fixed_departures WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $dep = array_merge($dep, $res);
    }
}

$packages_list = [];
if ($pdo) {
    try {
        $pkg_id = (int)($dep['package_id'] ?? 0);
        $packages_list = $pdo->query("SELECT id, title FROM packages WHERE (is_active=1 AND package_type='fixed') OR id = $pkg_id ORDER BY title ASC")->fetchAll();
    } catch (Exception $e) { }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $package_id = (int)$_POST['package_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $price = (float)$_POST['price'];
    $total_seats = (int)$_POST['total_seats'];
    $available_seats = (int)$_POST['available_seats'];
    $status = trim($_POST['status']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $fields = "package_id=?, start_date=?, end_date=?, price=?, total_seats=?, available_seats=?, status=?, is_active=?";
    $values = [$package_id, $start_date, $end_date, $price, $total_seats, $available_seats, $status, $is_active];

    if ($id > 0) {
        $values[] = $id;
        $stmt = $pdo->prepare("UPDATE fixed_departures SET $fields WHERE id=?");
        $stmt->execute($values);
    } else {
        $stmt = $pdo->prepare("INSERT INTO fixed_departures (package_id, start_date, end_date, price, total_seats, available_seats, status, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($values);
    }

    header('Location: fixed_departures.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Fixed Departure | Admin</title>
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
            <div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="fixed_departures.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Departures">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Schedule'; ?> <span class="accent">Departure</span></h1>
                </div>
            </div>

            <form method="POST" class="form-card">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Select Package</label>
                        <select name="package_id" required>
                            <option value="">-- Select a Package --</option>
                            <?php foreach ($packages_list as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($dep['package_id'] == $p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['title']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($packages_list)): ?>
                            <p style="color: #fca5a5; font-size: 0.85rem; margin-top: 0.5rem;">You don't have any packages marked as "Fixed Departure". Go to <a href="packages.php" style="color: #ef4444; text-decoration: underline;">Manage Packages</a> and change a package's type to "Fixed Departure" first.</p>
                        <?php else: ?>
                            <p style="color: var(--admin-text-muted); font-size: 0.8rem; margin-top: 0.5rem;">
                                Note: This form only controls the Departure Date and Pricing. 
                                <?php if ($id > 0 && !empty($dep['package_id'])): ?>
                                    <a href="package-form.php?id=<?php echo $dep['package_id']; ?>" style="color: var(--accent); text-decoration: underline;">Click here to edit the Package's Itinerary, Hotel, and Meals.</a>
                                <?php else: ?>
                                    To edit Itinerary or Hotel details, use the "Manage Packages" menu.
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label>Departure Date</label>
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($dep['start_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Return Date</label>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($dep['end_date']); ?>" required>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr;">
                    <div class="form-group">
                        <label>Price per Person (INR)</label>
                        <input type="number" name="price" value="<?php echo htmlspecialchars((string)$dep['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Total Seats</label>
                        <input type="number" name="total_seats" value="<?php echo htmlspecialchars((string)$dep['total_seats']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Available Seats</label>
                        <input type="number" name="available_seats" value="<?php echo htmlspecialchars((string)$dep['available_seats']); ?>" required>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                    <div class="form-group">
                        <label>Status Label</label>
                        <select name="status">
                            <option value="Open" <?php echo $dep['status'] == 'Open' ? 'selected' : ''; ?>>Open</option>
                            <option value="Filling Fast" <?php echo $dep['status'] == 'Filling Fast' ? 'selected' : ''; ?>>Filling Fast</option>
                            <option value="Few Seats Left" <?php echo $dep['status'] == 'Few Seats Left' ? 'selected' : ''; ?>>Few Seats Left</option>
                            <option value="Sold Out" <?php echo $dep['status'] == 'Sold Out' ? 'selected' : ''; ?>>Sold Out</option>
                            <option value="Waitlist" <?php echo $dep['status'] == 'Waitlist' ? 'selected' : ''; ?>>Waitlist</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo $dep['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        Active (show on website)
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 1.25rem; margin-top: 2rem;">Save Departure</button>
            </form>
        </main>
    </div>
</body>
</html>
