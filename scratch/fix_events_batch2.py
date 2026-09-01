import os
import re

INCLUDE_DIR = r'g:\Antigravity\leisure_loop_site\includes'

files = [
    'header.php', 'footer.php', 'mobile_about.php', 'mobile_b2b.php',
    'mobile_bottom_nav.php', 'mobile_cabs.php', 'mobile_contact.php',
    'mobile_corporate.php', 'mobile_destinations.php', 'mobile_destination_details.php',
    'mobile_faq.php', 'mobile_home.php', 'mobile_hotel-detail.php', 'mobile_hotels.php',
    'mobile_packages.php', 'mobile_package_details.php', 'mobile_splash.php'
]

replacements = [
    (r"onclick=\"openModal\(\)\"", r'data-action="open-modal"'),
    (r"onclick=\"closeModal\(\)\"", r'data-action="close-modal"'),
    (r"onclick=\"openEnquiryModal\(\)\"", r'data-action="open-enquiry-modal"'),
    (r"onclick=\"openMobileMenu\(\)\"", r'data-action="open-mobile-menu"'),
    (r"onclick=\"closeMobileMenu\(\)\"", r'data-action="close-mobile-menu"'),
    (r"onfocus=\"\(this\.type='date'\)\"", r'data-focus="date"'),
    (r"onfocus=\"\(this\.type='time'\)\"", r'data-focus="time"'),
    (r"onchange=\"applyFilters\(\)\"", r'data-change="apply-filters"'),
    (r"onclick=\"closeAll\(\)\"", r'data-action="close-all"'),
    (r"onclick=\"clearFilters\(\);?\"", r'data-action="clear-filters"'),
    (r"onclick=\"applyFiltersAndClose\(\)\"", r'data-action="apply-filters-close"'),
    (r"onclick=\"toggleSort\(\)\"", r'data-action="toggle-sort"'),
    (r"onclick=\"toggleFilter\(\)\"", r'data-action="toggle-filter"'),
    (r"onclick=\"openLocationModal\(\)\"", r'data-action="open-location"'),
    (r"onclick=\"openSearchModal\(\)\"", r'data-action="open-search"'),
    (r"onclick=\"closeSearchModal\(\)\"", r'data-action="close-search"'),
    (r"onclick=\"closeLocationModal\(\)\"", r'data-action="close-location"'),
    (r"onclick=\"useCurrentLocation\(\)\"", r'data-action="use-location"'),
    (r"onclick=\"selectCity\('([^']*)'\)\"", r'data-action="select-city" data-city="\1"'),
    (r"onclick=\"window\.location\.href='([^']*)';?\"", r'data-href="\1"'),
    (r"onclick=\"document\.getElementById\('fd-mobile-carousel'\)\.scrollBy\(\{left: 140, behavior: 'smooth'\}\)\"", r'data-action="scroll-carousel"'),
    (r"onclick=\"switchFilterPane\('([^']*)', this\)\"", r'data-action="switch-filter" data-pane="\1"'),
    (r"onclick=\"sharePackage\(\)\"", r'data-action="share-package"'),
    (r"onclick=\"toggleAccordion\(this\)\"", r'data-action="toggle-accordion"'),
    (r"onclick=\"mSelectStay\(([^,]*),\s*'([^']*)'\)\"", r'data-action="select-stay" data-idx="\1" data-title="\2"'),
    (r"onclick=\"mSelectCab\(([^,]*),\s*'([^']*)'\)\"", r'data-action="select-cab" data-idx="\1" data-name="\2"'),
    (r"onclick=\"enableMap\(\)\"", r'data-action="enable-map"'),
    (r"onclick=\"selectMobileRoom\(([^,]*),\s*'([^']*)'\)\"", r'data-action="select-room" data-idx="\1" data-title="\2"'),
]

for file in files:
    filepath = os.path.join(INCLUDE_DIR, file)
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original_content = content
    for pattern, repl in replacements:
        content = re.sub(pattern, repl, content)
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated events in {file}")

print("Done patching events.")
