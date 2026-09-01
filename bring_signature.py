import re

# Read index.php to extract Signature Terrains
with open('public/index.php', 'r', encoding='utf-8') as f:
    index_content = f.read()

match = re.search(r'(<!-- Signature Terrains -->.*?</section>)', index_content, re.DOTALL)
if not match:
    print("Could not find Signature Terrains in index.php")
    exit(1)

signature_terrains = match.group(1)

# Modify Signature Terrains for mobile (adjust text sizes and IDs)
signature_terrains = signature_terrains.replace('id="destinations"', 'id="mobile-destinations"')
signature_terrains = signature_terrains.replace('<h2 class="section-title">', '<h2 class="section-title" style="font-size: 2.25rem !important; line-height: 1.15; margin-bottom: 0;">')
signature_terrains = signature_terrains.replace('font-size: 8rem;', 'font-size: 16vw !important; color: rgba(255, 255, 255, 0.06);')

# Also replace the watermark text to avoid the CSS conflict
signature_terrains = signature_terrains.replace('class="watermark"', 'class="mobile-watermark"')
# Replace inline style for watermark to fit mobile perfectly
signature_terrains = re.sub(
    r'<div class="mobile-watermark" style="position: absolute; z-index: 1;">',
    '<div class="mobile-watermark" style="position: absolute; z-index: 1; font-size: 16vw !important; font-weight: 900; white-space: nowrap; top: 10%; left: 50%; transform: translateX(-50%); letter-spacing: 0.1em; color: rgba(255, 255, 255, 0.06); pointer-events: none; text-transform: uppercase;">',
    signature_terrains
)

# Read mobile_home.php
with open('includes/mobile_home.php', 'r', encoding='utf-8') as f:
    mobile_content = f.read()

# Remove Featured Destinations
match = re.search(r'(<!-- 4\. Featured Destinations -->.*?</section>)', mobile_content, re.DOTALL)
if match:
    featured_dest = match.group(1)
    # Replace Featured Destinations with Signature Terrains
    mobile_content = mobile_content.replace(featured_dest, signature_terrains)
else:
    print("Could not find Featured Destinations in mobile_home.php")
    exit(1)

# Write back
with open('includes/mobile_home.php', 'w', encoding='utf-8') as f:
    f.write(mobile_content)

print("Updates applied successfully.")
