<?php
require_once '../config/db.php';
require_once '../includes/fpdf/fpdf.php';

$booking_id = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$booking_id || !$pdo) {
    die("Invalid Inquiry ID");
}

$stmt = $pdo->prepare("
    SELECT b.*, h.name as hotel_name, h.place, h.star_category, r.room_type_name, r.amenities 
    FROM hotel_bookings b 
    JOIN hotels h ON b.hotel_id = h.id 
    LEFT JOIN hotel_rooms r ON b.room_id = r.id 
    WHERE b.booking_id = ?
");
$stmt->execute([$booking_id]);
$b = $stmt->fetch();

if (!$b) {
    die("Inquiry not found");
}

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

    protected function _putlinks($n) {
        foreach($this->PageLinks[$n] as $pl) {
            $this->_newobj();
            $rect = sprintf('%.2F %.2F %.2F %.2F',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
            $s = '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
            if(is_string($pl[4])) {
                if (strpos($pl[4], 'javascript:') === 0) {
                    $js = substr($pl[4], 11);
                    $s .= '/A <</S /JavaScript /JS '.$this->_textstring($js).'>>>>';
                } else {
                    $s .= '/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
                }
            } else {
                $l = $this->links[$pl[4]];
                if(isset($this->PageInfo[$l[0]]['size']))
                    $h = $this->PageInfo[$l[0]]['size'][1];
                else
                    $h = ($this->DefOrientation=='P') ? $this->DefPageSize[1]*$this->k : $this->DefPageSize[0]*$this->k;
                $s .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>',$this->PageInfo[$l[0]]['n'],$h-$l[1]*$this->k);
            }
            $this->_put($s);
            $this->_put('endobj');
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
$pdf->Cell(100, 10, 'Inquiry Acknowledgement', 0, 0, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(90, 5, 'Ack No: ' . $b['booking_id'], 0, 1, 'R');
$pdf->Cell(190, 5, 'Date: ' . date('d M Y'), 0, 1, 'R');

$pdf->SetDrawColor(220, 220, 220);
$pdf->Line(10, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
$pdf->Ln(7);

// ---------------------------------------------------------
// 3. HOTEL "HERO" SECTION
// ---------------------------------------------------------
// Save Y for the badge later
$startY = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(135, 10, $b['hotel_name'], 0, 1, 'L');

// Stars
$starCount = (int)$b['star_category'];
$stars = str_repeat('* ', $starCount > 0 ? $starCount : 3); // Default to 3 if unknown
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(197, 160, 89); // Gold
$pdf->Cell(150, 6, $stars, 0, 1, 'L');

// Address
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(150, 6, 'Location: ' . $b['place'], 0, 1, 'L');

// Draw "INQUIRY RECEIVED" Badge on the right
$badgeX = 145;
$badgeY = $startY + 5;
$pdf->SetDrawColor(52, 152, 219); // Blue
$pdf->SetLineWidth(0.5);
// Let's use standard Rect since standard FPDF doesn't have RoundedRect
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

// Row 1: Dates
$checkIn = strtotime($b['check_in']);
$checkOut = strtotime($b['check_out']);
$nights = round(($checkOut - $checkIn) / (60 * 60 * 24));
$nightStr = $nights > 0 ? $nights . '-Night Stay' : 'Day Trip';

drawDetailRow(
    $pdf, 
    'Check-In', date('D, d M Y', $checkIn), 'Standard Time: 12:00 PM',
    'Check-Out', date('D, d M Y', $checkOut), 'Standard Time: 11:00 AM'
);

// Row 2: Guests
drawDetailRow(
    $pdf,
    'Total Guests', $b['adults'] . ' Guests', '',
    'Primary Guest', $b['guest_name'], $b['phone'] . ' | ' . $b['email']
);

// Row 3: Room Info
$roomStr = $b['rooms'] . ' Room' . ($b['rooms'] > 1 ? 's' : '');
drawDetailRow(
    $pdf,
    'Accommodation', $roomStr, '',
    'Room Type', $b['room_type_name'] ? $b['room_type_name'] : 'To Be Decided', 'Inclusions subject to availability'
);

// Row 4: Pricing
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(95, 6, 'Estimated Total', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
if ($b['total_amount'] > 0) {
    $pdf->Cell(190, 8, 'Rs. ' . number_format($b['total_amount'], 2), 0, 1, 'L');
} else {
    $pdf->Cell(190, 8, 'TBD', 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(190, 5, 'Pricing is an estimate and subject to change based on finalized dates and availability.', 0, 1, 'L');

$pdf->Ln(5);

// ---------------------------------------------------------
// 5. IMPORTANT INFORMATION
// ---------------------------------------------------------
$pdf->SetFillColor(250, 240, 240); // Very light red/pink for attention
$pdf->SetTextColor(192, 57, 43); // Dark red
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 8, '   Note: This is an inquiry only, not a confirmed booking.', 0, 1, 'L', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 8, 'Important Information', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(80, 80, 80);

$info = [
    "Our travel curators are reviewing your request and will contact you shortly with the best available quotes.",
    "The estimated total provided (if any) is subject to dynamic hotel pricing and availability.",
    "A valid Govt. ID (Passport, Aadhaar, Driving License, etc.) is required at the time of check-in.",
    "Standard check-in time is usually 2:00 PM and check-out is 11:00 AM, though this may vary by property."
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

$pdf->Output('I', 'Inquiry_' . $b['booking_id'] . '.pdf');
?>
