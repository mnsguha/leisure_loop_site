<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/fpdf/fpdf.php';

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$booking_id || !$pdo) {
    die("Invalid Inquiry ID");
}

if (!pdf_allowed('package', $booking_id)) {
    http_response_code(403);
    die("Access denied.");
}

$stmt = $pdo->prepare("
    SELECT * 
    FROM leads
    WHERE id = ?
");
$stmt->execute([$booking_id]);
$b = $stmt->fetch();

if (!$b) {
    die("Inquiry not found.");
}

// Ensure backward compatibility if they used legacy schema vs new
$name = isset($b['customer_name']) && $b['customer_name'] ? $b['customer_name'] : $b['name'];
$phone = isset($b['customer_phone']) && $b['customer_phone'] ? $b['customer_phone'] : $b['phone'];
$email = isset($b['customer_email']) && $b['customer_email'] ? $b['customer_email'] : $b['email'];

$notes = isset($b['notes']) ? $b['notes'] : $b['message'];
$details = json_decode($notes, true);

if (!$details) {
    // If not JSON, it might be a normal lead message. Fallback.
    $details = [
        'package_title' => $b['destination'] ?? 'Custom Package',
        'tour_code' => 'N/A',
        'selected_hotel' => 'Standard',
        'selected_cab' => 'Standard',
        'travel_date' => $b['travel_date'] ?? 'N/A',
        'adults' => $b['adults'] ?? 2
    ];
}

$package_title = $details['package_title'] ?? ($b['destination'] ?? 'Package Booking');
$tour_code = $details['tour_code'] ?? 'TBA';
$selected_hotel = $details['selected_hotel'] ?? 'Not Specified';
$selected_cab = $details['selected_cab'] ?? 'Not Specified';
$travel_date = $details['travel_date'] ?? ($b['travel_date'] ?? 'TBA');
$adults = $details['adults'] ?? ($b['adults'] ?? '2');

$package_id = $details['package_id'] ?? ($b['package_id'] ?? null);
$nights = 1;
$days = 2;

if ($package_id) {
    $p_stmt = $pdo->prepare("SELECT nights, days FROM packages WHERE id = ?");
    $p_stmt->execute([$package_id]);
    $p_data = $p_stmt->fetch();
    if ($p_data) {
        $nights = !empty($p_data['nights']) ? (int)$p_data['nights'] : 1;
        $days = !empty($p_data['days']) ? (int)$p_data['days'] : ($nights + 1);
    }
}

$start_ts = !empty($travel_date) && $travel_date !== 'TBA' ? strtotime($travel_date) : time();
$start_date_formatted = date('d M Y', $start_ts);
$end_date_formatted = date('d M Y', strtotime("+{$nights} days", $start_ts));

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
$pdf->Cell(80, 6, 'Package Inquiry Acknowledgment', 0, 1, 'R');

$pdf->SetXY(120, 21);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(80, 5, 'Ack No: ' . $b['id'], 0, 1, 'R');
$pdf->SetXY(120, 26);
$pdf->Cell(80, 5, 'Date: ' . date('d M Y'), 0, 1, 'R');

$pdf->SetY(45);

// ---------------------------------------------------------
// 2. PACKAGE HERO SECTION & STATUS BADGE
// ---------------------------------------------------------
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(15, 23, 42); // Dark slate
// MultiCell to allow wrapping for very long names
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->MultiCell(130, 8, htmlspecialchars_decode($package_title), 0, 'L');
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

// Tour Code
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(197, 160, 89); // Gold
$pdf->Cell(190, 6, 'Tour Code: ' . $tour_code, 0, 1, 'L');

$pdf->Ln(8);

// ---------------------------------------------------------
// 3. GUEST DETAILS BLOCK (Matches Hotel Slip)
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
// 4. STAY & ITINERARY DETAILS
// ---------------------------------------------------------

// Row 1: Duration & No. of Travellers
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100, 116, 139); // Slate Text
$pdf->Cell(95, 5, 'DURATION', 0, 0, 'L');
$pdf->Cell(95, 5, 'NO. OF TRAVELLERS', 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42); // Primary Values
$pdf->Cell(95, 6, "{$nights} Nights / {$days} Days", 0, 0, 'L');
$pdf->Cell(95, 6, "{$adults} Travellers", 0, 1, 'R');
$pdf->Ln(4);

// Row 2: Date Card Box (Keep unique package style but align colors)
$y = $pdf->GetY();
$pdf->SetDrawColor(197, 160, 89); // Brand Gold
$pdf->SetFillColor(248, 250, 252); // Light gray card fill
$pdf->Rect(10, $y, 190, 22, 'DF'); // Using Rect for container

// Start Date
$pdf->SetXY(15, $y + 3);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(40, 5, 'START DATE', 0, 1, 'L');
$pdf->SetXY(15, $y + 9);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(40, 6, $start_date_formatted, 0, 1, 'L');

// Center Badge
$pdf->SetDrawColor(197, 160, 89);
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(98, $y + 4, 14, 14, 'DF'); // Badge Box
$pdf->SetXY(98, $y + 8);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(197, 160, 89);
$pdf->Cell(14, 6, "{$nights}N", 0, 0, 'C');

// End Date
$pdf->SetXY(155, $y + 3);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(40, 5, 'END DATE', 0, 1, 'R');
$pdf->SetXY(155, $y + 9);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(40, 6, $end_date_formatted, 0, 1, 'R');

// Manually reset cursor
$pdf->SetY($y + 28);
$pdf->SetDrawColor(226, 232, 240);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Row 3: Hotel & Fleet
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(100, 116, 139);
$pdf->Cell(95, 5, 'SELECTED HOTEL TIER', 0, 0, 'L');
$pdf->Cell(95, 5, 'SELECTED PRIVATE CAB', 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(95, 6, $selected_hotel, 0, 0, 'L');
$pdf->Cell(95, 6, $selected_cab, 0, 1, 'R');
$pdf->Ln(8);

// ---------------------------------------------------------
// 5. IMPORTANT INFORMATION (Matches Hotel Slip)
// ---------------------------------------------------------
$pdf->SetFillColor(253, 242, 242); // Very light red/pink for attention
$pdf->SetTextColor(220, 38, 38); // Dark red
$pdf->SetDrawColor(252, 165, 165); // Red border
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 10, '   Note: This is an inquiry only, not a confirmed package booking.', 1, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 8, 'Next Steps & Important Information', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(80, 80, 80);

$info = [
    "Our destination specialists are reviewing your preferences and will contact you shortly.",
    "A detailed itinerary with the exact final pricing will be shared upon consultation.",
    "The package is fully customizable. You can request changes to hotels or cabs during the call.",
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

$pdf->Output('I', 'Package_Inquiry_' . $b['id'] . '.pdf');
?>
