<?php
$index_path = __DIR__ . '/index.php';
$desktop_path = dirname(__DIR__) . '/includes/desktop_home.php';

$lines = file($index_path);
if ($lines === false) {
    echo "Failed to read index.php";
    exit;
}

$desktop_lines = array_slice($lines, 121, 1750 - 121);
$result = file_put_contents($desktop_path, implode("", $desktop_lines));

if ($result !== false) {
    echo "SUCCESS: extracted " . count($desktop_lines) . " lines.";
} else {
    echo "ERROR: failed to write to desktop_home.php";
}
?>
