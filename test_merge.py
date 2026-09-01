import re

fpath_detail = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'
fpath_static = r'g:\Antigravity\leisure_loop_site\public\package.php'

with open(fpath_detail, 'r', encoding='utf-8') as f:
    detail_html = f.read()

with open(fpath_static, 'r', encoding='utf-8') as f:
    static_html = f.read()

print(f"Read detail: {len(detail_html)} bytes")
print(f"Read static: {len(static_html)} bytes")

# 1. Extract PHP Header from detail
php_header = detail_html.split("<!-- Custom Premium Fonts and Leaflet map styling -->")[0]

# 2. Extract CSS/Leaflet from detail
detail_head_extras = """<!-- Custom Premium Fonts and Leaflet map styling -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
"""
style_match = re.search(r'<style>.*?</style>', detail_html, flags=re.DOTALL)
detail_css = style_match.group(0) if style_match else ""

json_ld = re.search(r'<script type="application/ld\+json">.*?</script>', detail_html, flags=re.DOTALL).group(0)

# 3. Extract Tailwind setup from static
tailwind_scripts = re.search(r'<script src="https://cdn.tailwindcss.com.*?</script>\s*<div class="dark', static_html, flags=re.DOTALL)
if tailwind_scripts:
    tailwind_head = tailwind_scripts.group(0).replace('<div class="dark', '')
else:
    tailwind_head = ""

# 4. Extract Trust Badges from detail
trust_badges = re.search(r'<!-- ✦ Trust Badges Strip ✦ -->.*?</div>\s*</div>', detail_html, flags=re.DOTALL).group(0)
# wait, the regex might be greedy or not match right. Let's use string find.

def get_block(text, start_str, end_str):
    start = text.find(start_str)
    if start == -1: return ""
    end = text.find(end_str, start)
    if end == -1: return ""
    return text[start:end+len(end_str)]

trust_badges = get_block(detail_html, "<!-- ✦ Trust Badges Strip ✦ -->", "<!-- ✦ Tour Highlights ✦ -->")
if trust_badges:
    trust_badges = trust_badges.replace("<!-- ✦ Tour Highlights ✦ -->", "")

itinerary = get_block(detail_html, "<!-- ✦ The Itinerary (Dynamic) ✦ -->", "<!-- ✦ Geographic Context (Map) ✦ -->")
if itinerary:
    itinerary = itinerary.replace("<!-- ✦ Geographic Context (Map) ✦ -->", "")

map_block = get_block(detail_html, "<!-- ✦ Geographic Context (Map) ✦ -->", "<!-- ✦ Legal & Policies ✦ -->")
if map_block:
    map_block = map_block.replace("<!-- ✦ Legal & Policies ✦ -->", "")

legal_block = get_block(detail_html, "<!-- ✦ Legal & Policies ✦ -->", "<!-- ✦ OTHER TOURS SECTION ✦ -->")
if legal_block:
    legal_block = legal_block.replace("<!-- ✦ OTHER TOURS SECTION ✦ -->", "")

other_tours = get_block(detail_html, "<!-- ✦ OTHER TOURS SECTION ✦ -->", "<!-- Full Screen Booking Form Overlay -->")
if other_tours:
    other_tours = other_tours.replace("<!-- Full Screen Booking Form Overlay -->", "")

modals = get_block(detail_html, "<!-- Full Screen Booking Form Overlay -->", "</body>")
if modals:
    modals = modals.replace("</body>", "")

print(f"Trust Badges: {len(trust_badges)} bytes")
print(f"Itinerary: {len(itinerary)} bytes")
print(f"Map: {len(map_block)} bytes")
print(f"Legal: {len(legal_block)} bytes")
print(f"Other Tours: {len(other_tours)} bytes")
print(f"Modals: {len(modals)} bytes")
