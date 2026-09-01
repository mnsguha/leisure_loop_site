<?php
require_once '../config/db.php';
require_once '../includes/fpdf/fpdf.php';

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$booking_id || !$pdo) {
    die("Invalid Inquiry ID");
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
    $pdf->Image($logo_path, 10, 10, 80); 
} else {
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetTextColor(197, 160, 89);
    $pdf->SetXY(10, 10);
    $pdf->Cell(60, 15, 'LEISURE LOOP', 0, 0, 'L');
}

// Company Info
$pdf->SetXY(10, 8);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 5, 'Leisure Loop Trip Pvt Ltd', 0, 1, 'R');
$pdf->Cell(0, 5, 'Email: enquiry@leisurelooptrip.com', 0, 1, 'R');
$pdf->Cell(0, 5, 'Phone: +91 89189 21629', 0, 1, 'R');

$pdf->SetY(40);

// ---------------------------------------------------------
// 2. SUB-HEADER (Title & Ack No)
// ---------------------------------------------------------
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(100, 10, 'Cab Inquiry Acknowledgement', 0, 0, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(90, 5, 'Ack No: ' . (isset($b['booking_id']) ? $b['booking_id'] : $b['id']), 0, 1, 'R');
$pdf->Cell(190, 5, 'Date: ' . date('d M Y'), 0, 1, 'R');

$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(10, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
$pdf->Ln(7);

// ---------------------------------------------------------
// 3. CAB "HERO" SECTION
// ---------------------------------------------------------
// Save Y for the badge later
$startY = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(135, 10, $vehicle_name, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(197, 160, 89); // Gold
$pdf->Cell(150, 6, 'Service: ' . $service_type, 0, 1, 'L');

// Draw "INQUIRY RECEIVED" Badge on the right
$badgeX = 145;
$badgeY = $startY + 5;
$pdf->SetDrawColor(52, 152, 219); // Blue
$pdf->SetLineWidth(0.5);
$pdf->Rect($badgeX, $badgeY, 55, 12, 'D');
$pdf->SetXY($badgeX, $badgeY + 3);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(52, 152, 219);
$pdf->Cell(55, 6, 'INQUIRY RECEIVED', 0, 0, 'C');
$pdf->SetLineWidth(0.2); // Reset line width

$pdf->SetY($startY + 30);
$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// ---------------------------------------------------------
// 4. DETAILS (List/Grid Style)
// ---------------------------------------------------------

function drawDetailRow($pdf, $title1, $val1, $sub1, $title2, $val2, $sub2) {
    // Titles
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->Cell(95, 6, $title1, 0, 0, 'L');
    $pdf->Cell(95, 6, $title2, 0, 1, 'L');
    
    // Values
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(95, 6, $val1, 0, 0, 'L');
    $pdf->Cell(95, 6, $val2, 0, 1, 'L');
    
    // Subtext
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(95, 5, $sub1, 0, 0, 'L');
    $pdf->Cell(95, 5, $sub2, 0, 1, 'L');
    
    $pdf->Ln(3);
    $pdf->SetDrawColor(230, 230, 230);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);
}

// Row 1: Route / Duration
$pickup = $b['pickup_location'] ? $b['pickup_location'] : 'N/A';
$drop = $b['drop_location'] ? $b['drop_location'] : 'N/A';
if ($trip_type === 'oneway') {
    drawDetailRow(
        $pdf, 
        'Pickup Location', $pickup, '',
        'Drop Location', $drop, ''
    );
} else if ($trip_type === 'hourly') {
    drawDetailRow(
        $pdf, 
        'Pickup Location', $pickup, '',
        'Duration', $duration ? $duration : 'N/A', ''
    );
} else if ($trip_type === 'itinerary') {
    $timeStr = $b['travel_time'] ? date('h:i A', strtotime($b['travel_time'])) : 'TBD';
    drawDetailRow(
        $pdf, 
        'Pick-up Location', $pickup, '',
        'Pick-up Time', $timeStr, ''
    );
    
    $dateStr = $b['travel_date'] ? date('D, d M Y', strtotime($b['travel_date'])) : 'TBD';
    $endDateStr = $drop ? date('D, d M Y', strtotime($drop)) : 'TBD';
    drawDetailRow(
        $pdf, 
        'Start Date', $dateStr, '',
        'End Date', $endDateStr, ''
    );
    
    // Draw Itinerary Details full width below
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->Cell(190, 6, 'Itinerary Details', 0, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(190, 6, $search_itinerary_details ? $search_itinerary_details : 'N/A', 0, 'L');
    
    $pdf->Ln(3);
    $pdf->SetDrawColor(230, 230, 230);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);
} else {
    drawDetailRow(
        $pdf, 
        'Pickup Location', $pickup, '',
        'Drop Location', $drop, ''
    );
}

// Row 2: Date & Time
if ($trip_type !== 'itinerary') {
    $dateStr = $b['travel_date'] ? date('D, d M Y', strtotime($b['travel_date'])) : 'TBD';
    $timeStr = $b['travel_time'] ? date('h:i A', strtotime($b['travel_time'])) : 'TBD';
    drawDetailRow(
        $pdf,
        'Travel Date', $dateStr, '',
        'Pick-up Time', $timeStr, ''
    );
}

// Row 3: Guests
drawDetailRow(
    $pdf,
    'Guest Name', $b['name'], '',
    'Contact Info', $b['phone'], $b['email']
);

// Row 4: Pricing
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(95, 6, 'Estimated Total', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
if ($final_price > 0) {
    if ($trip_type === 'hourly' || $trip_type === 'itinerary') {
        $pdf->Cell(190, 8, 'Rs. ' . number_format($final_price, 2) . ' / day (Base Fare)', 0, 1, 'L');
    } else {
        $pdf->Cell(190, 8, 'Rs. ' . number_format($final_price, 2), 0, 1, 'L');
    }
} else {
    $pdf->Cell(190, 8, 'TBD', 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(190, 5, 'Pricing is an estimate and subject to change based on final confirmation.', 0, 1, 'L');

$pdf->Ln(5);

// ---------------------------------------------------------
// 5. IMPORTANT INFORMATION
// ---------------------------------------------------------
$pdf->SetFillColor(250, 240, 240); // Very light red/pink for attention
$pdf->SetTextColor(192, 57, 43); // Dark red
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 8, '   Note: This is an inquiry only, not a confirmed cab booking.', 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 8, 'Important Information', 0, 1, 'L');

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

$pdf->Ln(10);
// Draw Print Button
$pdf->SetFillColor(52, 152, 219); // Blue Button
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 12, 'Click Here to Print', 0, 1, 'C', true, 'javascript:print(true);');

$ack = isset($b['booking_id']) ? $b['booking_id'] : $b['id'];
$pdf->Output('I', 'Cab_Inquiry_' . $ack . '.pdf');
?>