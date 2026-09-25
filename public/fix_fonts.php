<?php
$styleCssPath = __DIR__ . '/css/style.css';
$mobileCssPath = __DIR__ . '/css/mobile-views.css';

function replaceFonts($content) {
    // Replace hardcoded fonts with vars
    $content = str_replace("font-family: 'Playfair Display', serif;", 'font-family: var(--font-title);', $content);
    $content = str_replace("font-family: 'Inter', sans-serif;", 'font-family: var(--font-body);', $content);
    $content = str_replace("font-family: var(--font-serif);", 'font-family: var(--font-title);', $content);
    $content = str_replace("font-family: var(--font-sans);", 'font-family: var(--font-body);', $content);
    
    // Now specifically target headings to use --font-heading
    // We will do some targeted replacements based on known section title classes
    $headingClasses = [
        '.section-title',
        '.sidebar-title',
        'h2', 'h3', 'h4', 'h5', 'h6',
        '.m-section-title',
        '.mob-dest__section-title',
        '.m-pkg-section__title',
        '.m-cta-heading',
        '.m-guest-sheet-title',
        '.mhd-section-title',
        '.mhd-desc-modal-title',
        '.mhd-amenity-modal-title',
        '.mhd-gallery-title',
        '.faq-question',
        '.sheet-title'
    ];
    
    foreach ($headingClasses as $cls) {
        // Simple regex to replace var(--font-title) or var(--font-body) inside these selectors
        $pattern = '/(' . preg_quote($cls, '/') . '(?:\s*\{[^\}]*?|\s*,\s*[^\}]*?\{[^\}]*?))font-family:\s*var\(--font-(?:title|body)\);/is';
        $content = preg_replace($pattern, '$1font-family: var(--font-heading);', $content);
    }
    
    return $content;
}

if (file_exists($styleCssPath)) {
    $styleContent = file_get_contents($styleCssPath);
    // Remove old --font-serif or --font-sans from root if present
    $styleContent = preg_replace('/--font-serif:[^;]+;\s*/', '', $styleContent);
    $styleContent = preg_replace('/--font-sans:[^;]+;\s*/', '', $styleContent);
    
    // Add variables if not exist
    if (strpos($styleContent, '--font-title') === false) {
        $vars = "    --font-title: 'Playfair Display', Georgia, serif;\n    --font-heading: 'Montserrat', sans-serif;\n    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif;\n";
        $styleContent = preg_replace('/:root\s*\{/', ":root {\n" . $vars, $styleContent, 1);
    }
    $styleContent = replaceFonts($styleContent);
    file_put_contents($styleCssPath, $styleContent);
    echo "Updated style.css<br>";
}

if (file_exists($mobileCssPath)) {
    $mobileContent = file_get_contents($mobileCssPath);
    // Remove old --font-serif or --font-sans from root if present
    $mobileContent = preg_replace('/--font-serif:[^;]+;\s*/', '', $mobileContent);
    $mobileContent = preg_replace('/--font-sans:[^;]+;\s*/', '', $mobileContent);
    
    // Add variables if not exist
    if (strpos($mobileContent, '--font-title') === false) {
        $vars = "    --font-title: 'Playfair Display', Georgia, serif;\n    --font-heading: 'Montserrat', sans-serif;\n    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif;\n";
        $mobileContent = preg_replace('/:root\s*\{/', ":root {\n" . $vars, $mobileContent, 1);
    }
    $mobileContent = replaceFonts($mobileContent);
    file_put_contents($mobileCssPath, $mobileContent);
    echo "Updated mobile-views.css<br>";
}
echo "Done.";
?>
