import os

js_path = r'G:\Antigravity\leisure_loop_site\public\js\modules\packages.js'
out_dir = r'G:\Antigravity\leisure_loop_site\public\js\modules'

with open(js_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

current_file = 'packages.js'  # By default, assume the first block is for packages.php
file_contents = {
    'all-tours.js': [],
    'packages.js': [],
    'package.js': [],
    'package-detail.js': [],
    'pkg.js': []
}

for line in lines:
    if '/* Extracted from all-tours.php */' in line:
        current_file = 'all-tours.js'
    elif '/* Extracted from packages.php */' in line:
        current_file = 'packages.js'
    elif '/* Extracted from package.php */' in line:
        current_file = 'package.js'
    elif '/* Extracted from package-detail.php */' in line:
        current_file = 'package-detail.js'
    elif '/* Extracted from pkg.php */' in line:
        current_file = 'pkg.js'
    
    file_contents[current_file].append(line)

# Wait, the first block in packages.js is actually for packages.php (because of submitLandingSearch, heroCarouselSection, etc.)
# If all-tours JS logic is missing from packages.js (or if it doesn't need any JS), let's just write what we have.
for filename, content_lines in file_contents.items():
    if content_lines:
        with open(os.path.join(out_dir, filename), 'w', encoding='utf-8') as f:
            f.writelines(content_lines)

print("JS split completed.")
