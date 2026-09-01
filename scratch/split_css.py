import os

css_path = r'G:\Antigravity\leisure_loop_site\public\css\packages.css'
out_dir = r'G:\Antigravity\leisure_loop_site\public\css'

with open(css_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

current_file = 'all-tours.css'
file_contents = {
    'all-tours.css': [],
    'packages.css': [],
    'package.css': [],
    'package-detail.css': [],
    'pkg.css': []
}

for line in lines:
    if '/* Extracted from all-tours.php */' in line:
        current_file = 'all-tours.css'
    elif '/* Extracted from packages.php */' in line:
        current_file = 'packages.css'
    elif '/* Extracted from package.php */' in line:
        current_file = 'package.css'
    elif '/* Extracted from package-detail.php */' in line:
        current_file = 'package-detail.css'
    elif '/* Extracted from pkg.php */' in line:
        current_file = 'pkg.css'
    
    file_contents[current_file].append(line)

for filename, content_lines in file_contents.items():
    if content_lines:
        with open(os.path.join(out_dir, filename), 'w', encoding='utf-8') as f:
            f.writelines(content_lines)

print("CSS split completed.")
