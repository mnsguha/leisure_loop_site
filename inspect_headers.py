import glob
import re

files = glob.glob('public/admin/*-form.php') + ['public/admin/hotel-gallery.php', 'public/admin/hotel-inventory.php']

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Extract the header div
    # We look for <div class="header"...>...</div>
    match = re.search(r'(<div class="header"[^>]*>)(.*?)(</div>\s*<(?:div|form|table|main|!--))', content, re.DOTALL)
    if match:
        print(f"--- {f} ---")
        print(match.group(2).strip())
