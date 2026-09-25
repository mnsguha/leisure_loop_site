<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/fpdf/fpdf.php';

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$booking_id || !$pdo) {
    die("Invalid Inquiry ID");
}

if (!pdf_allowed('cab', $booking_id)) {
    http_response_code(403);
    die("Access denied.");
}

$stmt = $pdo->prepare("
    SELECT * 
    FROM cab_bookings
    WHERE id = ?
");
$stmt->execute([$booking_id]);
$b = $stmt->fetch();

if (!$b) {
    die("Inquiry not found.");
}

// Decode itinerary details to get vehicle info
$details = json_decode($b['itinerary_details'], true) ?: [];
$vehicle_name = isset($details['vehicle_name']) ? $details['vehicle_name'] : ($b['cab_type'] ? $b['cab_type'] : 'Unknown Vehicle');
$service_type = isset($details['service_type']) ? $details['service_type'] : $b['trip_type'];
$final_price = isset($details['final_price']) ? $details['final_price'] : 0;
$trip_type = $b['trip_type']; // oneway, hourly, itinerary
$duration = isset($details['duration']) ? $details['duration'] : '';
$search_itinerary_details = isset($details['search_itinerary_details']) ? $details['search_itinerary_details'] : '';

$pickup = $b['pickup_location'] ? $b['pickup_location'] : 'N/A';
$drop = $b['drop_location'] ? $b['drop_location'] : 'N/A';

$route = in_array($trip_type, ['hourly', 'itinerary'], true) ? $pickup : $pickup . ' to ' . $drop;

$dateStr = $b['travel_date'] ? date('D, d M Y', strtotime($b['travel_date'])) : 'TBD';
$timeStr = $b['travel_time'] ? date('h:i A', strtotime($b['travel_time'])) : 'TBD';
$endDateStr = $b['drop_location'] ? date('D, d M Y', strtotime($b['drop_location'])) : 'TBD';

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
$pdf->Cell(80, 6, 'Cab Inquiry Acknowledgement', 0, 1, 'R');

$pdf->SetXY(120, 21);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(80, 5, 'Ack No: ' . (isset($b['booking_id']) ? $b['booking_id'] : $b['id']), 0, 1, 'R');
$pdf->SetXY(120, 26);
$pdf->Cell(80, 5, 'Date: ' . date('d M Y'), 0, 1, 'R');

$pdf->SetY(45);

// ---------------------------------------------------------
// 2. CAB "HERO" SECTION
// ---------------------------------------------------------
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(15, 23, 42); // Dark slate
$y = $pdf->GetY();
$pdf->MultiCell(130, 8, htmlspecialchars_decode($vehicle_name), 0, 'L');
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
// Service type
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(197, 160, 89); // Gold
$pdf->Cell(190, 6, 'Service: ' . $service_type, 0, 1, 'L');

// Route subtitle
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(190, 6, 'Route: ' . htmlspecialchars_decode($route), 0, 1, 'L');

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
$pdf->Cell(60, 8, $b['name'], 0, 0, 'L');
$pdf->Cell(60, 8, $b['phone'], 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 8, $b['email'], 0, 1, 'L');

$pdf->Ln(8);

// ---------------------------------------------------------
// 4. TRIP DETAILS TABLE (Grid Style)
// ---------------------------------------------------------
$pdf->SetFillColor(235, 235, 235);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 9);

if ($trip_type === 'hourly') {
    $tPickup = strlen($pickup) > 48 ? substr($pickup, 0, 45) . '...' : $pickup;
    $tDuration = $duration ? $duration : 'N/A';
    if (strlen($tDuration) > 28) { $tDuration = substr($tDuration, 0, 25) . '...'; }

    // Table Header
    $pdf->Cell(8, 8, 'S.No', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Pick-up', 1, 0, 'C', true);
    $pdf->Cell(32, 8, 'Duration', 1, 0, 'C', true);
    $pdf->Cell(48, 8, 'Travel Date', 1, 0, 'C', true);
    $pdf->Cell(22, 8, 'Time', 1, 1, 'C', true);

    // Table Row
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->Cell(8, 10, '1', 1, 0, 'C');
    $pdf->Cell(80, 10, $tPickup, 1, 0, 'C');
    $pdf->Cell(32, 10, $tDuration, 1, 0, 'C');
    $pdf->Cell(48, 10, $dateStr, 1, 0, 'C');
    $pdf->Cell(22, 10, $timeStr, 1, 1, 'C');
} else if ($trip_type === 'itinerary') {
    $tPickup = strlen($pickup) > 48 ? substr($pickup, 0, 45) . '...' : $pickup;

    // Table Header
    $pdf->Cell(8, 8, 'S.No', 1, 0, 'C', true);
    $pdf->Cell(33, 8, 'Start Date', 1, 0, 'C', true);
    $pdf->Cell(33, 8, 'End Date', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Pick-up', 1, 0, 'C', true);
    $pdf->Cell(36, 8, 'Time', 1, 1, 'C', true);

    // Table Row
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->Cell(8, 10, '1', 1, 0, 'C');
    $pdf->Cell(33, 10, $dateStr, 1, 0, 'C');
    $pdf->Cell(33, 10, $endDateStr, 1, 0, 'C');
    $pdf->Cell(80, 10, $tPickup, 1, 0, 'C');
    $pdf->Cell(36, 10, $timeStr, 1, 1, 'C');
} else {
    $tPickup = strlen($pickup) > 40 ? substr($pickup, 0, 37) . '...' : $pickup;
    $tDrop = strlen($drop) > 40 ? substr($drop, 0, 37) . '...' : $drop;

    // Table Header
    $pdf->Cell(8, 8, 'S.No', 1, 0, 'C', true);
    $pdf->Cell(65, 8, 'Pick-up', 1, 0, 'C', true);
    $pdf->Cell(65, 8, 'Drop', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Travel Date', 1, 0, 'C', true);
    $pdf->Cell(22, 8, 'Time', 1, 1, 'C', true);

    // Table Row
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->Cell(8, 10, '1', 1, 0, 'C');
    $pdf->Cell(65, 10, $tPickup, 1, 0, 'C');
    $pdf->Cell(65, 10, $tDrop, 1, 0, 'C');
    $pdf->Cell(30, 10, $dateStr, 1, 0, 'C');
    $pdf->Cell(22, 10, $timeStr, 1, 1, 'C');
}

// Itinerary Details full width below the table
if ($trip_type === 'itinerary') {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->Cell(190, 6, 'Itinerary Details', 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(190, 6, $search_itinerary_details ? $search_itinerary_details : 'N/A', 0, 'L');
    $pdf->Ln(3);
}

// Pricing Totals
if ($final_price > 0) {
    $priceStr = 'Rs. ' . number_format($final_price, 2);
    if ($trip_type === 'hourly' || $trip_type === 'itinerary') {
        $priceStr .= ' / day (Base Fare)';
    }

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(120, 8, 'Total Estimated Amount', 'LR', 0, 'R');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(70, 8, $priceStr, 'LRB', 1, 'R');
} else {
    // Fill the empty line just to close the table nicely
    $pdf->Cell(190, 0, '', 'T', 1);
}

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(190, 5, 'Pricing is an estimate and subject to change based on final confirmation.', 0, 1, 'L');

$pdf->Ln(10);

// ---------------------------------------------------------
// 5. IMPORTANT INFORMATION
// ---------------------------------------------------------
$pdf->SetFillColor(253, 242, 242); // Very light red/pink for attention
$pdf->SetTextColor(220, 38, 38); // Dark red
$pdf->SetDrawColor(252, 165, 165); // Red border
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 10, '   Note: This is an inquiry only, not a confirmed cab booking.', 1, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 8, 'Next Steps & Important Information', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(80, 80, 80);

$info = [
    "Our transport curators are reviewing your request and will contact you shortly with confirmation.",
    "The estimated total provided is subject to dynamic vehicle availability and fuel pricing.",
    "Toll taxes and parking charges are generally excluded unless specified.",
    "A valid Govt. ID is required for verification during travel."
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

$ack = isset($b['booking_id']) ? $b['booking_id'] : $b['id'];
$pdf->Output('I', 'Cab_Inquiry_' . $ack . '.pdf');
?>
