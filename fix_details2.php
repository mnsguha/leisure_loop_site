<?php
$file = 'public/destination-details.php';
$content = file_get_contents($file);

// The exact string that got mangled is around here:
$search_regex = '/@keyframes roamCloudRightToLeft \{[\s\S]*?\}\s*\.darj-cloud-3 \{/m';

$replacement = <<<EOT
@keyframes roamCloudRightToLeft {
    0% { transform: translateX(120vw); opacity: 0; }
    10% { opacity: 0.9; }
    90% { opacity: 0.9; }
    100% { transform: translateX(-20vw); opacity: 0; }
}

.cloud-img-1, .cloud-img-2, .cloud-img-3, .darj-cloud-1, .darj-cloud-2, .darj-cloud-3 {
    position: absolute;
    object-fit: contain;
    opacity: 0;
    pointer-events: none;
    filter: drop-shadow(0 15px 15px rgba(0,0,0,0.1));
}

.darj-cloud-1 {
    bottom: 45%; width: 25vw;
    animation: roamCloudLeftToRight 30s linear infinite; animation-delay: -10s;
}

.darj-cloud-2 {
    bottom: 35%; width: 35vw;
    animation: roamCloudRightToLeft 25s linear infinite; animation-delay: -5s; animation-fill-mode: both;
}

.darj-cloud-3 {
EOT;

$content = preg_replace($search_regex, $replacement, $content);

// And now fix the sikkim clouds (cloud-img-1, 2, 3)
// They are currently in the file as:
// .cloud-img-1 { bottom: 25%; ... }
// .cloud-img-2 { bottom: 18%; ... }
// .cloud-img-3 { bottom: 30%; ... }

$sikkim_regex = '/\.cloud-img-1 \{[\s\S]*?\.cloud-img-2 \{[\s\S]*?\.cloud-img-3 \{[\s\S]*?\}/m';

$sikkim_replacement = <<<EOT
.cloud-img-1 {
    bottom: 45%; width: 25vw;
    animation: roamCloudLeftToRight 25s linear infinite; animation-delay: -5s;
}

.cloud-img-2 {
    bottom: 38%; width: 35vw;
    animation: roamCloudLeftToRight 20s linear infinite; animation-delay: -12s; animation-fill-mode: both;
}

.cloud-img-3 {
    bottom: 50%; width: 20vw;
    animation: roamCloudLeftToRight 35s linear infinite; animation-delay: -2s; animation-fill-mode: both;
}
EOT;

$content = preg_replace($sikkim_regex, $sikkim_replacement, $content);

file_put_contents($file, $content);
echo "Fix applied with Regex!";
?>
