<?php
$output = [];
$return_var = 0;
exec("git checkout -- " . dirname(__DIR__) . "/includes/mobile_home.php", $output, $return_var);
echo "Return: $return_var\n";
print_r($output);
?>
