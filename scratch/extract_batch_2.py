import os
import re

INCLUDE_DIR = r'g:\Antigravity\leisure_loop_site\includes'
CSS_FILE = r'g:\Antigravity\leisure_loop_site\public\css\mobile-views.css'
JS_FILE = r'g:\Antigravity\leisure_loop_site\public\js\modules\mobile-views.js'

os.makedirs(os.path.dirname(CSS_FILE), exist_ok=True)
os.makedirs(os.path.dirname(JS_FILE), exist_ok=True)

style_pattern = re.compile(r'<style[^>]*>(.*?)</style>', re.IGNORECASE | re.DOTALL)
script_pattern = re.compile(r'<script(?![^>]*\bsrc\s*=)[^>]*>(.*?)</script>', re.IGNORECASE | re.DOTALL)

# Also capture onclicks, onsubmits, etc. to see what needs data-actions.
event_pattern = re.compile(r'\bon(click|change|submit|focus|blur|load)\s*=\s*"([^"]*)"', re.IGNORECASE)

files = [
    'header.php', 'footer.php', 'mobile_about.php', 'mobile_b2b.php',
    'mobile_bottom_nav.php', 'mobile_cabs.php', 'mobile_contact.php',
    'mobile_corporate.php', 'mobile_destinations.php', 'mobile_destination_details.php',
    'mobile_faq.php', 'mobile_home.php', 'mobile_hotel-detail.php', 'mobile_hotels.php',
    'mobile_packages.php', 'mobile_package_details.php', 'mobile_splash.php'
]

css_content = ['.is-active { display: block !important; }\n']
css_content.append('/* Touch Target Compliance */\na, button, .mobile-menu-trigger, .nav-item, .pill, .floating-action { min-height: 48px; min-width: 48px; display: inline-flex; align-items: center; justify-content: center; }\n')

js_content = ["'use strict';\n\ndocument.addEventListener('DOMContentLoaded', () => {\n"]

all_events = []

for file in files:
    filepath = os.path.join(INCLUDE_DIR, file)
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
        content = style_pattern.sub(r'<link rel="stylesheet" href="/css/mobile-views.css">', content, count=1)
        content = style_pattern.sub('', content)

    # Process Scripts
    scripts = script_pattern.findall(content)
    scripts = [s for s in scripts if s.strip()]
    if scripts:
        has_script = True
        for s in scripts:
            js_content.append(f'/* Extracted from {file} */\n{s.strip()}\n')
            
        # Replace first occurrence with script src
        content = script_pattern.sub(r'<script src="/js/modules/mobile-views.js" defer></script>', content, count=1)
        content = script_pattern.sub('', content)

    # Scan for events
    events = event_pattern.findall(content)
    for evt in events:
        all_events.append((file, evt[0], evt[1]))

    if has_style or has_script:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Processed {file}")

with open(CSS_FILE, 'w', encoding='utf-8') as f:
    f.write('\n'.join(css_content))

with open(JS_FILE, 'w', encoding='utf-8') as f:
    f.write('\n'.join(js_content))

with open(r'g:\Antigravity\leisure_loop_site\scratch\events_batch2.txt', 'w', encoding='utf-8') as f:
    for e in all_events:
        f.write(f"{e[0]} - {e[1]} - {e[2]}\n")

print("Extraction complete. Events logged.")
