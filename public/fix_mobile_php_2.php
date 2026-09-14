<?php
$file_path = __DIR__ . '/../includes/mobile_packages.php';
$content = file_get_contents($file_path);

// --- 1. SIGNATURE DESTINATIONS ---
// Title: ✨ Signature Destinations
// Pills: All, Domestic, International
$sig_title_pattern = '/<h3 class="m-pkg-section__title">\s*<span class="material-symbols-outlined m-section-icon" aria-hidden="true">auto_awesome<\/span>\s*Signature Destinations\s*<\/h3>/s';
$sig_title_replacement = '<h3 class="m-pkg-section__title" style="font-family: var(--font-serif); font-size: 1.25rem;">✨ Signature Destinations</h3>';
$content = preg_replace($sig_title_pattern, $sig_title_replacement, $content);

// Remove icons from Signature pills if they exist
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">auto_awesome</span>All', 'All', $content);
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">landscape</span>Domestic', 'Domestic', $content);
$content = str_replace('<span class="material-symbols-outlined m-pill-icon" aria-hidden="true">flight_takeoff</span>International', 'International', $content);

// --- 2. HOLIDAY THEMES ---
// Arrows should be left-aligned under the title
$theme_header_pattern = '/<div class="m-pkg-carousel-header">\s*<h3 class="m-pkg-section__title">Explore Holiday Collections By Theme<\/h3>\s*<div class="m-pkg-nav-arrows">.*?<\/div>\s*<\/div>/s';
$theme_header_replacement = '<div class="m-pkg-carousel-header">
                <h3 class="m-pkg-section__title" style="font-family: var(--font-serif); font-size: 1.15rem; margin-bottom: 8px;">Explore Holiday Collections By Theme</h3>
                <div class="m-pkg-nav-arrows" style="justify-content: flex-start;">
                    <button type="button" class="m-pkg-nav-btn" data-action="carousel-scroll" data-target="mobThemeTrack" data-direction="-1" aria-label="Previous">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                    </button>
                    <button type="button" class="m-pkg-nav-btn" data-action="carousel-scroll" data-target="mobThemeTrack" data-direction="1" aria-label="Next">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                    </button>
                </div>
            </div>';
$content = preg_replace($theme_header_pattern, $theme_header_replacement, $content);

// --- 3. TOP TRENDING TOURS ---
// Title: Top Trending Tours (no emoji)
$content = str_replace('<h3 class="m-pkg-section__title">Top Trending Tours</h3>', '<h3 class="m-pkg-section__title" style="font-family: var(--font-serif); font-size: 1.25rem;">Top Trending Tours</h3>', $content);
// We might have removed emojis in previous step, so just search for the text.

// Pills: ⭐ All, △ Domestic, ✈️ International
$content = str_replace('<span class="m-pill-icon" aria-hidden="true">☆</span> All', '⭐ All', $content);
$content = str_replace('<span class="m-pill-icon" aria-hidden="true">△</span> Domestic', '△ Domestic', $content);
$content = str_replace('<span class="m-pill-icon" aria-hidden="true">✈️</span> International', '✈️ International', $content);

// --- 4. EXCLUSIVE OFFERS ---
// Title: Exclusive Holiday Offers (no emoji)
$content = str_replace('<h3 class="m-pkg-section__title">Exclusive Holiday Offers</h3>', '<h3 class="m-pkg-section__title" style="font-family: var(--font-serif); font-size: 1.25rem;">Exclusive Holiday Offers</h3>', $content);

// Need to update the Offers pills. They might currently use the same structure as Trending.
// Let's explicitly replace in the Offers section block.
// I will just use preg_replace with a limit or regex that targets the offers tablist.
$offers_pattern = '/(<section class="m-pkg-section m-offers-section" aria-label="Exclusive offers">.*?<div class="m-pills-row" role="tablist" aria-label="Offers filter">.*?)(⭐ All|☆ All|All)(.*?)(△ Domestic|Domestic)(.*?)(✈️ International|International)(.*?<\/section>)/s';
$offers_replacement = '$1⚡ All$3△ Domestic$5✈️ International$7';
$content = preg_replace($offers_pattern, $offers_replacement, $content);

// Add .m-pill--emerald class logic for the first pill in Offers if it isn't there
$content = str_replace('<button type="button" class="m-pill js-offer-tab m-pill--active"', '<button type="button" class="m-pill js-offer-tab m-pill--active m-pill--emerald"', $content);


// Let's also fix the Complete Tour Inventory
// Title should be "Ready to Find Your Dream Sanctuary?"
// We need to replace the CTA portfolio section.
$cta_pattern = '/<!-- 7\. CTA Portfolio .*?<\/section>/s';
$cta_replacement = '<!-- 7. CTA Portfolio -->
        <section class="m-pkg-section m-cta-portfolio" aria-label="Explore all tours">
            <div class="m-cta-card" style="padding: 30px 20px; text-align: center; border: 1px solid rgba(197,160,89,0.3); border-radius: 16px; background: rgba(5,10,20,0.6); margin: 0 16px;">
                <div style="font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(197,160,89,0.3); padding: 4px 12px; border-radius: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 1rem;">explore</span> COMPLETE TOUR INVENTORY
                </div>
                <h3 class="m-cta-card__title" style="font-family: var(--font-serif); font-size: 1.6rem; margin-bottom: 16px; line-height: 1.3;">
                    Ready to Find Your <span style="color: var(--gold); font-style: italic;">Dream Sanctuary?</span>
                </h3>
                <p class="m-cta-card__desc" style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 24px; line-height: 1.5;">
                    Browse our complete catalog of curated luxury holidays, customized itineraries, and guaranteed fixed group departures with interactive budget and duration filters.
                </p>
                <a href="all-tours.php?view=mobile" class="m-cta-btn" style="display: inline-flex; align-items: center; gap: 8px; background: var(--gold); color: #000; font-weight: 700; padding: 12px 24px; border-radius: 30px; font-size: 0.9rem; text-decoration: none;">
                    ✨ Explore All Available Tours
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                </a>
            </div>
        </section>';
$content = preg_replace($cta_pattern, $cta_replacement, $content);

file_put_contents($file_path, $content);
echo "SUCCESS: mobile_packages.php styles updated.";
