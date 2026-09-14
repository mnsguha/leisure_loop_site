<?php
$repo_dir = 'g:\Antigravity\leisure_loop_site';
$output = [];
exec("cd $repo_dir && git log -n 20 --oneline", $output);
echo "Recent commits:\n";
echo implode("\n", $output);
