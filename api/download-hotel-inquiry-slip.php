<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/fpdf/fpdf.php';

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$booking_id || !$pdo) {
    die("Invalid Inquiry ID");
}

if (!pdf_allowed('hotel', $booking_id)) {
    http_response_code(403);
    die("Access denied.");
}

$stmt = $pdo->prepare("
    SELECT b.*, h.name as hotel_name, h.place as hotel_place, h.star_category as hotel_star,
           r.room_type_name, p.plan_name 
    FROM hotel_bookings b
    JOIN hotels h ON b.hotel_id = h.id
    LEFT JOIN hotel_rooms r ON b.room_id = r.id
    LEFT JOIN hotel_room_plans p ON b.plan_id = p.id
    WHERE b.booking_id = ?
");
$stmt->execute([$booking_id]);
$b = $stmt->fetch();

if (!$b) {
    die("Inquiry not found.");
}

$name = $b['guest_name'];
$phone = $b['phone'];
$email = $b['email'];
$hotel_name = $b['hotel_name'];
$hotel_place = $b['hotel_place'];
$hotel_star = $b['hotel_star'];
$check_in = $b['check_in'];
$check_out = $b['check_out'];
$rooms = (int)$b['rooms'];
$adults = (int)$b['adults'];

$start_ts = strtotime($check_in);
$end_ts = strtotime($check_out);
$nights = max(1, floor(($end_ts - $start_ts) / 86400));
$start_date_formatted = date('d M Y', $start_ts);
$end_date_formatted = date('d M Y', $end_ts);

class PDF_AutoPrint extends FPDF {
    protected $javascript;
    protected $n_js;

    function IncludeJS($script) {
        $this->javascript = $script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js = $this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) ' . ($this->n + 1) . ' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS ' . $this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript ' . ($this->n_js) . ' 0 R>>');
        }
    }
}

$pdf = new PDF_AutoPrint();
$pdf->IncludeJS('print(true);');
$pdf->AddPage();

// ---------------------------------------------------------
// 1. HEADER SECTION (Navy Blue Background)
// ---------------------------------------------------------
$pdf->SetFillColor(13, 26, 41); // Dark Navy Blue
$pdf->Rect(0, 0, 210, 35, 'F'); // A4 width is 210mm

// Brand Header with Logo
$logo_path = '../public/assets/img/leisure.png';
if (file_exists($logo_path)) {
    // Increased width to 85 and shifted X to 4 to compensate for transparent padding in the logo image
    $pdf->Image($logo_path, 4, 7, 85); 
} else {
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetTextColor(197, 160, 89);
    $pdf->SetXY(10, 12);
    $pdf->Cell(60, 15, 'LEISURE LOOP', 0, 0, 'L');
}

// Right aligned Ack No and Date
$pdf->SetXY(120, 13);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(80, 6, 'Hotel Inquiry Acknowledgment', 0, 1, 'R');

$pdf->SetXY(120, 21);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(80, 5, 'Ack No: ' . $booking_id, 0, 1, 'R');
$pdf->SetXY(120, 26);
$pdf->Cell(80, 5, 'Date: ' . date('d M Y'), 0, 1, 'R');

$pdf->SetY(45);

// ---------------------------------------------------------
// 2. HOTEL DETAILS & STATUS BADGE
// ---------------------------------------------------------
// Hotel Name
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(15, 23, 42); // Dark slate
// MultiCell to allow wrapping for very long hotel names
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(130, 8, htmlspecialchars_decode($hotel_name), 0, 'L');
$newY = $pdf->GetY();

// INQUIRY RECEIVED Badge (Top Right of this block)
$pdf->SetXY(145, $y);
$pdf->SetDrawColor(52, 152, 219); // Blue
$pdf->SetFillColor(240, 248, 255); // Alice blue
$pdf->SetTextColor(52, 152, 219);
$pdf->SetLineWidth(0.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(55, 8, 'INQUIRY RECEIVED', 1, 1, 'C', true);
$pdf->SetLineWidth(0.2);

$pdf->SetY($newY);
// Star & Location
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(197, 160, 89); // Gold
$starCount = (int)$hotel_star;
$stars = str_repeat('* ', $starCount > 0 ? $starCount : 3);
$pdf->Cell(190, 6, $stars, 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(190, 6, 'Location: ' . $hotel_place, 0, 1, 'L');

$pdf->Ln(6);

// ---------------------------------------------------------
// 3. GUEST DETAILS BLOCK
// ---------------------------------------------------------
$pdf->SetFillColor(248, 250, 252); // Light Slate
$pdf->SetDrawColor(226, 232, 240); // Border color
$pdf->Rect(10, $pdf->GetY(), 190, 22, 'DF');

$pdf->SetXY(15, $pdf->GetY() + 3);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(60, 5, 'GUEST NAME', 0, 0, 'L');
$pdf->Cell(60, 5, 'CONTACT NO.', 0, 0, 'L');
$pdf->Cell(60, 5, 'EMAIL', 0, 1, 'L');

$pdf->SetX(15);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(60, 8, $name, 0, 0, 'L');
$pdf->Cell(60, 8, $phone, 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, $email, 0, 1, 'L');

$pdf->Ln(8);

// ---------------------------------------------------------
// 4. STAY & ROOM DETAILS TABLE (Grid Style)
// ---------------------------------------------------------
$pdf->SetFillColor(235, 235, 235);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 9);

// Table Header
$pdf->Cell(8, 8, 'S.No', 1, 0, 'C', true);
$pdf->Cell(55, 8, 'Room Type', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Meal Plan', 1, 0, 'C', true);
$pdf->Cell(14, 8, 'Rooms', 1, 0, 'C', true);
$pdf->Cell(14, 8, 'Nights', 1, 0, 'C', true);
$pdf->Cell(19, 8, 'Guests', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Check-in', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Check-out', 1, 1, 'C', true);

// Table Row
$room_type = !empty($b['room_type_name']) ? htmlspecialchars_decode($b['room_type_name']) : 'Standard Room';
$meal_plan = !empty($b['plan_name']) ? htmlspecialchars_decode($b['plan_name']) : 'Room Only';
$guests = "{$adults} Adults";
if (isset($b['children']) && $b['children'] > 0) {
    $guests = "{$adults} A, {$b['children']} C";
}

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(40, 40, 40);

// Truncate long names to prevent breaking
if(strlen($room_type) > 35) { $room_type = substr($room_type, 0, 32) . '...'; }
if(strlen($meal_plan) > 20) { $meal_plan = substr($meal_plan, 0, 17) . '...'; }

$pdf->Cell(8, 10, '1', 1, 0, 'C');
$pdf->Cell(55, 10, $room_type, 1, 0, 'C');
$pdf->Cell(30, 10, $meal_plan, 1, 0, 'C');
$pdf->Cell(14, 10, $rooms, 1, 0, 'C');
$pdf->Cell(14, 10, $nights, 1, 0, 'C');
$pdf->Cell(19, 10, $guests, 1, 0, 'C');
$pdf->Cell(25, 10, $start_date_formatted, 1, 0, 'C');
$pdf->Cell(25, 10, $end_date_formatted, 1, 1, 'C');

// Pricing Totals (only if amount is present)
$total_amount = (float)$b['total_amount'];
if ($total_amount > 0) {
    // Reverse tax calculation based on GST slabs
    $daily_per_room = $total_amount / max(1, ($nights * $rooms));
    $tax_rate = 0;
    if ($daily_per_room <= 1000) {
        $tax_rate = 0;
    } else if ($daily_per_room <= 7875) {
        $tax_rate = 0.05;
    } else {
        $tax_rate = 0.18;
    }
    
    $net_rate = $total_amount / (1 + $tax_rate);
    $gst = $total_amount - $net_rate;
    
    // Net Rate Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 6, 'Net Rate', 'LR', 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 6, 'INR ' . number_format($net_rate, 2), 'LR', 1, 'R');
    
    // GST Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(140, 6, 'GST', 'LR', 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 6, 'INR ' . number_format($gst, 2), 'LR', 1, 'R');
    
    // Total Estimated Amount Row
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(140, 8, 'Total Estimated Amount', 'LRB', 0, 'R');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 8, 'INR ' . number_format($total_amount, 2), 'LRB', 1, 'R');
} else {
    // Fill the empty line just to close the table nicely
    $pdf->Cell(190, 0, '', 'T', 1);
}

$pdf->Ln(10);

// ---------------------------------------------------------
// 5. IMPORTANT INFORMATION
// ---------------------------------------------------------
$pdf->SetFillColor(253, 242, 242); // Very light red/pink for attention
$pdf->SetTextColor(220, 38, 38); // Dark red
$pdf->SetDrawColor(252, 165, 165); // Red border
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 10, '   Note: This is an inquiry only, not a confirmed hotel booking.', 1, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 8, 'Next Steps & Important Information', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(80, 80, 80);

$info = [
    "Our specialists are reviewing your inquiry and will contact you shortly.",
    "A detailed quote with the exact final pricing will be shared upon consultation.",
    "You can request changes to rooms or dates during the call, subject to availability.",
    "Booking will only be confirmed after an initial advance payment is successfully processed."
];

foreach ($info as $point) {
    $pdf->Cell(5, 5, chr(149), 0, 0, 'R'); // Bullet character
    $pdf->MultiCell(185, 5, $point, 0, 'L');
}

$pdf->Ln(15);
// Draw Print Button
$pdf->SetFillColor(197, 160, 89); // Gold Button
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 12, 'Click Here to Print', 0, 1, 'C', true, 'javascript:print(true);');

$pdf->Output('I', 'Hotel_Inquiry_' . $booking_id . '.pdf');
?>
