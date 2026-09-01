import os
import re

dest_path = 'public/destinations.php'
details_path = 'public/destination-details.php'

# 1. Extract the <style> block from destination-details.php
with open(details_path, 'r', encoding='utf-8') as f:
    details_content = f.read()

style_start = details_content.find('<style>')
style_end = details_content.find('</style>', style_start) + len('</style>')

if style_start != -1 and style_end != -1:
    style_block = details_content[style_start:style_end]
else:
    print("Could not find style block.")
    exit(1)

# 2. Fix destinations.php
with open(dest_path, 'r', encoding='utf-8') as f:
    dest_content = f.read()

# Inject style block before <!-- Premium ParableVC-Style Multi-Layer Hero for Sikkim -->
if '<style>' not in dest_content or 'PREMIUM PARALLAX SCROLL' not in dest_content:
    marker = '<!-- Premium ParableVC-Style Multi-Layer Hero for Sikkim -->'
    if marker in dest_content:
        dest_content = dest_content.replace(marker, style_block + '\n' + marker)

# Fix the JS issue
js_marker_start = '// 1. Initial Hero Drop-In or ParableVC Initialization'
js_marker_end = '} else {\n        // --- Standard Hero Drop-In ---'

# Find the injected JS
idx_start = dest_content.find(js_marker_start)
idx_end = dest_content.find(js_marker_end)

if idx_start != -1:
    # Find the end of the block which is '}\n\nconst revealObserver'
    # Actually, we know I injected it right before 'const revealObserver'
    end_str = '\nconst revealObserver'
    idx_block_end = dest_content.find(end_str, idx_start)
    
    if idx_block_end != -1:
        # Extract the entire block
        injected_js = dest_content[idx_start:idx_block_end]
        
        # Check if it's already wrapped
        if 'window.addEventListener("load"' not in injected_js and 'window.addEventListener(\'load\'' not in injected_js:
            wrapped_js = f"window.addEventListener('load', () => {{\n{injected_js}\n}});"
            dest_content = dest_content[:idx_start] + wrapped_js + dest_content[idx_block_end:]

with open(dest_path, 'w', encoding='utf-8') as f:
    f.write(dest_content)

print("Successfully fixed CSS and JS.")
