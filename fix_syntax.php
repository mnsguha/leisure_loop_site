<?php
$file = 'public/destination-details.php';
$lines = file($file);
$out = [];
foreach($lines as $line) {
    if (strpos($line, '<!-- Elite Enquiry Form Section -->') !== false) {
        $out[] = "    <?php endforeach; ?>\n";
        $out[] = "<?php endif; ?>\n";
    }
    $out[] = $line;
}
file_put_contents($file, implode("", $out));
echo "Fixed syntax error!";
?>
