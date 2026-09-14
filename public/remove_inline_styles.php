<?php
$file_path = __DIR__ . '/../includes/mobile_packages.php';
$content = file_get_contents($file_path);

// 1. Hero title main
$content = str_replace('style="font-family: var(--font-sans); font-weight: 700;"', '', $content);

// 2. Section titles
$content = str_replace('style="font-family: var(--font-serif); font-size: 1.25rem;"', '', $content);
$content = str_replace('style="font-family: var(--font-serif); font-size: 1.15rem; margin-bottom: 8px;"', '', $content);

// 3. Theme nav arrows
$content = str_replace('style="justify-content: flex-start;"', 'class="m-pkg-nav-arrows m-pkg-nav-arrows--start"', $content);
// Clean up double class declaration if it happened
$content = str_replace('class="m-pkg-nav-arrows" class="m-pkg-nav-arrows m-pkg-nav-arrows--start"', 'class="m-pkg-nav-arrows m-pkg-nav-arrows--start"', $content);

// 4. CTA Card
$content = preg_replace(
    '/<div class="m-cta-card" style="[^"]+">/',
    '<div class="m-cta-card">',
    $content
);

// 5. CTA Badge
$content = preg_replace(
    '/<div style="font-size: 0.7rem; color: var\(--gold\); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba\(197,160,89,0.3\); padding: 4px 12px; border-radius: 20px;">/',
    '<div class="m-cta-card__badge">',
    $content
);
$content = preg_replace(
    '/<span class="material-symbols-outlined" style="font-size: 1rem;">/',
    '<span class="material-symbols-outlined">',
    $content
);

// 6. CTA Title
$content = preg_replace(
    '/<h3 class="m-cta-card__title" style="[^"]+">/',
    '<h3 class="m-cta-card__title">',
    $content
);

// 7. CTA Title Span
$content = preg_replace(
    '/<span style="color: var\(--gold\); font-style: italic;">/',
    '<span class="m-cta-card__title-highlight">',
    $content
);

// 8. CTA Desc
$content = preg_replace(
    '/<p class="m-cta-card__desc" style="[^"]+">/',
    '<p class="m-cta-card__desc">',
    $content
);

// 9. CTA Btn
$content = preg_replace(
    '/<a href="([^"]+)" class="m-cta-btn" style="[^"]+">/',
    '<a href="$1" class="m-cta-btn">',
    $content
);

// Remove any lingering empty style attributes
$content = str_replace(' style=""', '', $content);
$content = str_replace(' style=" "', '', $content);

file_put_contents($file_path, $content);
echo "SUCCESS: Removed all inline styles from mobile_packages.php.";
