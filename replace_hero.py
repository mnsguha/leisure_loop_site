import os

details_path = 'public/destination-details.php'
dest_path = 'public/destinations.php'

with open(details_path, 'r', encoding='utf-8') as f:
    details_content = f.read()

# Extract HTML (Lines 96 to 138)
# We can find it by specific string markers
start_marker_html = '<!-- Premium ParableVC-Style Multi-Layer Hero for Sikkim -->'
end_marker_html = '<!-- Spacer to allow parallax scrolling before the next section overlaps -->\n    <div class="parable-spacer" style="height: 150vh; width: 100%; position: relative; z-index: 0; pointer-events: none;"></div>'

html_start = details_content.find(start_marker_html)
html_end = details_content.find(end_marker_html) + len(end_marker_html)

if html_start != -1 and html_end != -1:
    extracted_html = details_content[html_start:html_end]
    # Replace PHP vars with static text
    extracted_html = extracted_html.replace("<?php echo htmlspecialchars($destination['tagline']); ?>", "HIMALAYAN MAJESTY")
    extracted_html = extracted_html.replace("<?php echo htmlspecialchars($destination['name']); ?>", "SIKKIM")
else:
    print("Could not find HTML block in destination-details.php")
    exit(1)

# Extract JS (Lines 729 to 789)
start_marker_js = '// 1. Initial Hero Drop-In or ParableVC Initialization\n    if (document.querySelector(\'.parable-hero\')) {'
end_marker_js = '} else {\n        // --- Standard Hero Drop-In ---'

js_start = details_content.find(start_marker_js)
js_end = details_content.find(end_marker_js)

if js_start != -1 and js_end != -1:
    extracted_js = details_content[js_start:js_end] + "}\n"
else:
    print("Could not find JS block in destination-details.php")
    exit(1)

# Now modify destinations.php
with open(dest_path, 'r', encoding='utf-8') as f:
    dest_content = f.read()

# Replace Hero HTML
dest_html_start = dest_content.find('<!-- Hero Section -->')
dest_html_end = dest_content.find('</section>', dest_html_start) + len('</section>')

if dest_html_start != -1 and dest_html_end != -1:
    dest_content = dest_content[:dest_html_start] + extracted_html + dest_content[dest_html_end:]
else:
    print("Could not find Hero Section in destinations.php")
    exit(1)

# Inject JS
dest_js_marker = 'const revealObserver = new IntersectionObserver(revealCallback'
if dest_js_marker in dest_content:
    # Check if we already injected it
    if 'Initial Hero Drop-In' not in dest_content:
        dest_content = dest_content.replace(dest_js_marker, extracted_js + '\n' + dest_js_marker)
else:
    print("Could not find JS marker in destinations.php")
    exit(1)

with open(dest_path, 'w', encoding='utf-8') as f:
    f.write(dest_content)

print("Successfully replaced Hero Section and injected JS.")
