<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$bookings = [];
if ($pdo) {
    $bookings = $pdo->query("SELECT b.*, h.name as hotel_name, r.room_type_name 
                             FROM hotel_bookings b 
                             JOIN hotels h ON b.hotel_id = h.id 
                             LEFT JOIN hotel_rooms r ON b.room_id = r.id 
                             ORDER BY b.created_at DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Bookings (PMS) | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem;">
                <h1>Hotel <span class="accent">Bookings</span> (Mini-PMS)</h1>
                <p class="muted">Manage all hotel reservations and download vouchers.</p>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Details</th>
                        <th>Hotel & Room</th>
                        <th>Dates</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($b['booking_id']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($b['guest_name']); ?><br>
                            <span class="muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars($b['phone']); ?></span>
                        </td>
                        <td>
                            <strong style="color:var(--gold);"><?php echo htmlspecialchars($b['hotel_name']); ?></strong><br>
                            <span class="muted" style="font-size: 0.85rem;"><?php echo htmlspecialchars($b['room_type_name'] ?: 'Luxury Inquiry'); ?></span>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem;">
                            In: <?php echo date('d M Y', strtotime($b['check_in'])); ?><br>
                            Out: <?php echo date('d M Y', strtotime($b['check_out'])); ?>
                            </span>
                        </td>
                        <td>₹<?php echo number_format($b['total_amount'], 2); ?></td>
                        <td>
                            <span class="badge" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 4px 8px; border-radius: 4px;">
                                <?php echo ucfirst(htmlspecialchars($b['booking_status'])); ?>
                            </span>
                        </td>
                        <td>
                            <a href="../api/download-hotel-voucher.php?id=<?php echo urlencode($b['booking_id']); ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.85rem;" target="_blank">PDF Voucher</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($bookings)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: rgba(255,255,255,0.5);">No bookings found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
