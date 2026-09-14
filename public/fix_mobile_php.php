<?php
$file_path = __DIR__ . '/../includes/mobile_packages.php';
$content = file_get_contents($file_path);

// 1. Fix Hero Title
$hero_pattern = '/(<h2 class="m-pkg-hero__title">\s*)<\?= htmlspecialchars\(\$h_dest\[\'name\'\]\) \?>\s*<span class="m-pkg-hero__title-sub">Tour Packages<\/span>/';
$hero_replacement = '$1<span class="m-pkg-hero__title-main" style="font-family: var(--font-sans); font-weight: 700;"><?= htmlspecialchars($h_dest[\'name\']) ?></span> <span class="m-pkg-hero__title-sub">Tour Packages</span>';
$content = preg_replace($hero_pattern, $hero_replacement, $content);

// 2. Remove emojis from section titles
$content = str_replace('<span aria-hidden="true">✨</span> ', '', $content);
$content = str_replace('<span aria-hidden="true">🔥</span> ', '', $content);
$content = str_replace('<span aria-hidden="true">🎁</span> ', '', $content);

// 3. Fix Trending Pills
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">star</span>All', '<span class="m-pill-icon" aria-hidden="true">☆</span> All', $content);
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">landscape</span>Domestic', '<span class="m-pill-icon" aria-hidden="true">△</span> Domestic', $content);
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">flight_takeoff</span>International', '<span class="m-pill-icon" aria-hidden="true">✈️</span> International', $content);

// 4. Extract and reorder sections
// We need to move Holiday Themes (starts at "<!-- 5. Holiday Themes") to before "<!-- 4. Top Trending Tours"
$themes_pattern = '/(\s*<!-- 5\. Holiday Themes.*?<\/section>\s*<\?php endif; \?>\s*)/s';
if (preg_match($themes_pattern, $content, $matches)) {
    $themes_block = $matches[1];
    // Remove it from its current position
    $content = str_replace($themes_block, '', $content);
    // Insert it before Top Trending Tours
    $content = str_replace('<!-- 4. Top Trending Tours', ltrim($themes_block) . "\n\n        <!-- 4. Top Trending Tours", $content);
}

file_put_contents($file_path, $content);
echo "SUCCESS: mobile_packages.php updated.";
