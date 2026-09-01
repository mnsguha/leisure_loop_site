import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
JS_DIR = os.path.join(PUBLIC_DIR, 'js', 'modules')

files = []
for f in os.listdir(PUBLIC_DIR):
    if f.endswith('.php') or f.endswith('.html'):
        files.append(os.path.join(PUBLIC_DIR, f))

# A list of regex replacements to run on the PHP/HTML files
replacements = [
    # General
    (r'data-action="eval:document\.getElementById\(\'([^\']+)\'\)\.classList\.add\(\'active\'\);?"', r'data-action="open-modal" data-target="#\1"'),
    (r'data-(action|change)="eval:applyFiltersAndSort\(\);?"', r'data-\1="apply-filters"'),
    (r'data-action="eval:resetAllFilters\(\);?"', r'data-action="reset-filters"'),
    (r'data-change="eval:setBudgetRange\(\'([^\']*)\',\s*\'([^\']*)\'\);?"', r'data-change="set-budget" data-min="\1" data-max="\2"'),
    (r'data-(action|change)="eval:submitLandingSearch\(\);?"', r'data-\1="submit-landing-search"'),
    
    # Home
    (r'data-action="eval:scrollCarousel\(\'([^\']+)\',\s*(-?\d+)\);?"', r'data-action="scroll-carousel" data-carousel="\1" data-dir="\2"'),
    (r'data-action="eval:toggleTheme\(\'([^\']+)\',\s*this\);?"', r'data-action="toggle-theme" data-box="\1"'),
    (r'data-action="eval:toggleInlineStory\(event\);?"', r'data-action="toggle-inline-story"'),
    (r'data-action="eval:openStoryModal\(event\);?"', r'data-action="open-story-modal"'),
    (r'data-action="eval:closeStoryModal\(event\);?"', r'data-action="close-story-modal"'),
    (r'data-action="eval:closePlanner\(\);?"', r'data-action="close-planner"'),
    
    # Packages
    (r'data-change="eval:onSearchDockThemeChange\(\);?"', r'data-change="search-theme-change"'),
    (r'data-action="eval:scrollToCatalogGrid\(\);?"', r'data-action="scroll-catalog"'),
    (r'data-action="eval:openFilterDrawer\(\);?"', r'data-action="open-filter-drawer"'),
    (r'data-action="eval:closeFilterDrawer\(\);?"', r'data-action="close-filter-drawer"'),
    (r'data-change="eval:onSidebarThemeChange\(\);?"', r'data-change="sidebar-theme-change"'),
    (r'data-action="eval:changeHeroSlide\((-?\d+)\);?"', r'data-action="hero-slide" data-dir="\1"'),
    (r'data-action="eval:goToHeroSlide\(<\?php echo \$idx; \?>\);?"', r'data-action="hero-slide-to" data-idx="<?php echo $idx; ?>"'),
    (r'data-action="eval:filterSignatureDest\(\'([^\']+)\',\s*this\);?"', r'data-action="filter-pkg" data-type="signature" data-val="\1"'),
    (r'data-action="eval:filterTrending\(\'([^\']+)\',\s*this\);?"', r'data-action="filter-pkg" data-type="trending" data-val="\1"'),
    (r'data-action="eval:filterOffers\(\'([^\']+)\',\s*this\);?"', r'data-action="filter-pkg" data-type="offers" data-val="\1"'),
    (r'data-action="eval:expandTerms\(\);?"', r'data-action="expand-terms"'),
    (r'data-action="eval:openLightbox\(([^)]+)\);?"', r'data-action="open-lightbox" data-idx="\1"'),
    (r'data-action="eval:closeLightbox\(\);?"', r'data-action="close-lightbox"'),
    (r'data-action="eval:prevLightboxImage\(\);?"', r'data-action="prev-lightbox"'),
    (r'data-action="eval:nextLightboxImage\(\);?"', r'data-action="next-lightbox"'),
    (r'data-action="eval:selectStay\(([^,]+),\s*\'([^\']+)\'\);?"', r'data-action="select-stay" data-idx="\1" data-title="\2"'),
    (r'data-action="eval:selectCab\(([^,]+),\s*\'([^\']+)\'\);?"', r'data-action="select-cab" data-idx="\1" data-name="\2"'),
    (r'data-action="eval:openPackageCheckoutModal\(\);?"', r'data-action="open-checkout"'),
    (r'data-action="eval:closePackageCheckoutModal\(\);?"', r'data-action="close-checkout"'),
    (r'data-action="eval:scrollRelated\((-?\d+)\);?"', r'data-action="scroll-related" data-dir="\1"'),
    (r'data-change="eval:updateModalSummaryPrice\(\);?"', r'data-change="update-price"'),
    (r'data-action="eval:window\.location\.href=\'package-detail\.php\?slug=<\?php echo urlencode\(\$pkg\[\'slug\'\]\); \?>&check_in=\'\+document\.getElementById\(\'inputCheckIn\'\)\.value\+\'&check_out=\'\+document\.getElementById\(\'inputCheckOut\'\)\.value\+\'&adults=\'\+document\.getElementById\(\'valAdults\'\)\.innerText\+\'&children=\'\+document\.getElementById\(\'valChildren\'\)\.innerText"', 
     r'data-action="custom-package-redirect" data-slug="<?php echo urlencode($pkg[\'slug\']); ?>"'),
     
    # Cabs
    (r'data-action="eval:swapLocations\(this\);?"', r'data-action="swap-locations"'),
    (r'data-action="eval:closeContactModal\(\);?"', r'data-action="close-modal" data-target="#contactModal"'),
    (r'data-action="eval:performSearch\(this\);?"', r'data-action="perform-search"'),
    (r'data-action="eval:closeCheckoutModal\(\);?"', r'data-action="close-checkout"'),
    (r'data-action="eval:handleVehicleAction\(this,\s*(<\?php[^>]+>)\);?"', r'data-action="handle-vehicle" data-vehicle=\'\1\''),
    
    # Destinations
    (r'data-action="eval:selectQuickChip\(this,\s*\'([^\']+)\'\);?"', r'data-action="quick-chip" data-val="\1"'),
    
    # Hotels
    (r'data-action="eval:event\.preventDefault\(\); openInfoModal\(\'(<\?php[^\']+\?>)\',\s*\'([^\']+)\',\s*document\.getElementById\(\'([^\']+)\'\)\.innerHTML\);?"', r'data-action="open-info-modal" data-hotel="\1" data-title="\2" data-content-id="\3"'),
    (r'data-action="eval:if\(event\.target === this\) closeInfoModal\(\);?"', r'data-action="close-info-modal-if-self"'),
    (r'data-action="eval:closeInfoModal\(\);?"', r'data-action="close-info-modal"'),
    (r'data-action="eval:event\.preventDefault\(\);\s*event\.stopPropagation\(\);\s*window\.open\([^,]+,\s*\'GoogleReviews\',\s*\'[^\']+\'\);?"', r'data-action="open-review"'),
    (r'data-action="eval:toggleDesc\(\);?"', r'data-action="toggle-desc"'),
    (r'data-action="eval:\s*var details = document\.getElementById\(\'([^\']+)\'\);[^"]+"', r'data-action="toggle-more-details" data-target="\1"'),
    (r'data-action="eval:addToCart\(this,\s*([^,]+),\s*([^,]+),\s*\'([^\']+)\',\s*\'([^\']+)\',\s*\'([^\']+)\'\);?"', r'data-action="add-to-cart" data-plan="\1" data-room="\2" data-hotel="\3" data-room-name="\4" data-plan-name="\5"'),
    (r'data-action="eval:openCheckoutModal\(\);?"', r'data-action="open-checkout"'),
    
    # Content Pages
    (r'data-action="eval:toggleTOC\(\);?"', r'data-action="toggle-toc"'),
    (r'data-action="eval:goToStep1\(\);?"', r'data-action="go-step" data-step="1"'),
    (r'data-action="eval:goToStep2\(\);?"', r'data-action="go-step" data-step="2"'),
    
    # Clean up the contact.php fallback in all-tours
    (r'data-action="eval:if\(typeof openEnquiryModal === \'function\'\)\{\s*openEnquiryModal\(\);\s*\}\s*else\s*\{\s*window\.location\.href=\'contact\.php\';\s*\}"', r'data-action="open-enquiry-modal"'),
]

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original_content = content
    for pattern, repl in replacements:
        content = re.sub(pattern, repl, content)
    
    if content != original_content:
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Patched evals in {os.path.basename(file)}")

print("Done patching evals in PHP/HTML.")
