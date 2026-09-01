<?php
$zipUrl = 'http://www.fpdf.org/en/dl.php?v=186&f=zip';
$zipFile = __DIR__ . '/fpdf.zip';
$extractPath = __DIR__ . '/../includes/fpdf/';

// Download
file_put_contents($zipFile, file_get_contents($zipUrl));

// Extract
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractPath);
    $zip->close();
    echo "FPDF downloaded and extracted successfully.";
} else {
    echo "Failed to extract FPDF.";
}

@unlink($zipFile);
?>
