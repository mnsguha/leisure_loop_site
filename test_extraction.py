import re

fpath_detail = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'

with open(fpath_detail, 'r', encoding='utf-8') as f:
    detail_html = f.read()

def get_block(text, start_str, end_str):
    start = text.find(start_str)
    if start == -1: return ""
    end = text.find(end_str, start)
    if end == -1: return ""
    return text[start:end]

# 1. Trust Badges
trust_badges = get_block(detail_html, "<!-- ✦ Trust Badges Strip ✦ -->", "<!-- ✦ Tour Highlights ✦ -->")

# 2. Itinerary
itinerary = get_block(detail_html, "<!-- ✦ Interactive Accordion Itinerary ✦ -->", "<!-- ✦ Animated Journey Map ✦ -->")

# 3. Map
map_block = get_block(detail_html, "<!-- ✦ Animated Journey Map ✦ -->", "<!-- Accordion + Map JS -->")

# 4. Accordion + Map JS
map_js = get_block(detail_html, "<!-- Accordion + Map JS -->", "<!-- Exquisite Inclusions / Exclusions -->")

# 5. Terms
terms = get_block(detail_html, "<!-- ✦ Terms & Conditions ✦ -->", "<!-- ✦ Dynamic Advertisement Banner ✦ -->")

# 6. Other Tours
other_tours = get_block(detail_html, "<!-- ✦ Related Packages Section (\"Maybe you like\") ✦ -->", "<!-- Lead Capture Modal Overlay -->")

# 7. Modals
modals = get_block(detail_html, "<!-- Lead Capture Modal Overlay -->", "<?php include '../includes/whatsapp-btn.php'; ?>")

print(f"Trust Badges: {len(trust_badges)}")
print(f"Itinerary: {len(itinerary)}")
print(f"Map Block: {len(map_block)}")
print(f"Map JS: {len(map_js)}")
print(f"Terms: {len(terms)}")
print(f"Other Tours: {len(other_tours)}")
print(f"Modals: {len(modals)}")

# Create a merged layout mock
# We need to inject these back into the package.php layout
