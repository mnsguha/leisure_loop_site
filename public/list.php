<?php
$files = scandir(dirname(__DIR__) . '/includes');
foreach ($files as $file) {
    if (strpos($file, 'mobile_home') !== false) {
        echo $file . "\n";
    }
}
?>
