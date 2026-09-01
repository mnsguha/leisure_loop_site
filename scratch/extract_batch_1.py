import os
import re

ADMIN_DIR = r'g:\Antigravity\leisure_loop_site\public\admin'
CSS_FILE = r'g:\Antigravity\leisure_loop_site\public\css\admin-overrides.css'
JS_FILE = r'g:\Antigravity\leisure_loop_site\public\js\modules\admin-scripts.js'

os.makedirs(os.path.dirname(CSS_FILE), exist_ok=True)
os.makedirs(os.path.dirname(JS_FILE), exist_ok=True)

style_pattern = re.compile(r'<style[^>]*>(.*?)</style>', re.IGNORECASE | re.DOTALL)
script_pattern = re.compile(r'<script(?![^>]*\bsrc\s*=)[^>]*>(.*?)</script>', re.IGNORECASE | re.DOTALL)

files = [
    'blog-form.php', 'cab-rates.php', 'cab_class-form.php', 'destination-form.php',
    'hotel-gallery.php', 'hotel-inventory.php', 'hotel-rooms.php', 'hotels.php',
    'leads.php', 'package-form.php', 'package-gallery.php', 'sidebar.php',
    'subscribers.php', 'vehicle-form.php'
]

css_content = ['.is-active { display: block !important; }\n']
js_content = [
    "'use strict';\n\ndocument.addEventListener('DOMContentLoaded', (event) => {\n"
]

for file in files:
    filepath = os.path.join(ADMIN_DIR, file)
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    has_style = False
    has_script = False
    
    # Process Styles
    styles = style_pattern.findall(content)
    if styles:
        has_style = True
        for s in styles:
            css_content.append(f'/* Extracted from {file} */\n{s.strip()}\n')
        
        # Replace first occurrence with link
        content = style_pattern.sub(r'<link rel="stylesheet" href="../css/admin-overrides.css">', content, count=1)
        # Remove subsequent occurrences
        content = style_pattern.sub('', content)

    # Process Scripts
    scripts = script_pattern.findall(content)
    scripts = [s for s in scripts if s.strip()] # filter out empty scripts
    if scripts:
        has_script = True
        for s in scripts:
            # Basic fixes for style.display
            # These are simple replaces, we'll manually check the final JS for complex patterns
            s = re.sub(r'\.style\.display\s*=\s*["\']block["\']', '.classList.add("is-active")', s)
            s = re.sub(r'\.style\.display\s*=\s*["\']none["\']', '.classList.remove("is-active")', s)
            
            # Simple fix for window. - we will manually review the JS file later
            # Removing window. blindly might break things like window.location
            # Let's not remove window. indiscriminately here, better to fix manually.
            
            js_content.append(f'/* Extracted from {file} */\n{s.strip()}\n')
            
        # Replace first occurrence with script src
        content = script_pattern.sub(r'<script src="../js/modules/admin-scripts.js" defer></script>', content, count=1)
        # Remove subsequent occurrences
        content = script_pattern.sub('', content)

    if has_style or has_script:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Processed {file}")

js_content.append("});\n")

with open(CSS_FILE, 'w', encoding='utf-8') as f:
    f.write('\n'.join(css_content))

with open(JS_FILE, 'w', encoding='utf-8') as f:
    f.write('\n'.join(js_content))

print("Extraction complete.")
