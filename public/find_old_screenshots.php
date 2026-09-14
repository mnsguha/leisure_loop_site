<?php
$files = glob('C:/Users/pc/.gemini/antigravity-ide/brain/20f64a04-66d7-4dfe-a52a-f82b132e2f96/.user_uploaded/*.png');
$output = [];
foreach ($files as $f) {
    $time = filemtime($f);
    $date = date('Y-m-d', $time);
    if ($date === '2026-09-06' || $date === '2026-09-07') {
        $output[] = basename($f) . ' - ' . date('Y-m-d H:i:s', $time);
    }
}
if (empty($output)) {
    echo "No files found for Sept 6-7.\n";
} else {
    echo implode("\n", $output);
}
