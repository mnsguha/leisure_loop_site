<?php
$output = shell_exec('git log --since="2026-09-06" --until="2026-09-08" --name-status');
echo $output;
