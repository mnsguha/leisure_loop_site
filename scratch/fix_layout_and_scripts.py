import os
import re

CSS_DIR = r'g:\Antigravity\leisure_loop_site\public\css'
PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'

# 1. Clean CSS bundles
exclude_css = ['style.css', 'modals.css', 'admin.css']
for f in os.listdir(CSS_DIR):
    if f.endswith('.css') and f not in exclude_css:
        fp = os.path.join(CSS_DIR, f)
        with open(fp, 'r', encoding='utf-8') as file:
            content = file.read()
        
        orig = content
        
        # Remove body, html, and * selectors (basic regex to catch them)
        # This regex looks for `selector { ... }` where selector contains body, html, or *
        # Because nested braces don't exist in standard CSS, we can use [^{]*\{[^}]*\}
        
        content = re.sub(r'(?i)^\s*(?:html|body|\*|html,\s*body)\s*\{[^}]*\}', '', content, flags=re.MULTILINE)
        content = re.sub(r'(?i)(?:html|body|\*|html,\s*body)\s*\{[^}]*\}', '', content)
        
        if content != orig:
            with open(fp, 'w', encoding='utf-8') as file:
                file.write(content)
            print(f"Cleaned rogue global CSS tags from {f}")

# 2. Fix Script tag placement in index.php
index_path = os.path.join(PUBLIC_DIR, 'index.php')
with open(index_path, 'r', encoding='utf-8') as file:
    content = file.read()

# We know it's trapped in <?php if (isset($_GET['lead_sent'])): ?>
# Let's extract <script src="js/modules/home.js" defer></script> and put it before include footer.php

script_tag = '<script src="js/modules/home.js" defer></script>'
if script_tag in content:
    # Check if it's trapped
    content = content.replace(script_tag, '')
    
    # Insert it right before include '../includes/footer.php';
    if "include '../includes/footer.php';" in content:
        content = content.replace("include '../includes/footer.php';", script_tag + "\n    include '../includes/footer.php';")
    else:
        # Just append it to the end
        content += "\n" + script_tag + "\n"
        
    with open(index_path, 'w', encoding='utf-8') as file:
        file.write(content)
    print("Moved home.js script in index.php")

print("Done fixing layout and script issues.")
