import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
INCLUDES_DIR = r'g:\Antigravity\leisure_loop_site\includes'

files_to_check = []
for d in [PUBLIC_DIR, INCLUDES_DIR]:
    for root, _, files in os.walk(d):
        if 'leisure_loop_site' in root.replace(PUBLIC_DIR, ''):
            continue # skip the backup
        for f in files:
            if f.endswith(('.php', '.html')):
                files_to_check.append(os.path.join(root, f))

for fp in files_to_check:
    with open(fp, 'r', encoding='utf-8') as f:
        content = f.read()
    
    orig = content
    content = re.sub(r'href=["\']/css/', 'href="css/', content, flags=re.IGNORECASE)
    content = re.sub(r'src=["\']/js/', 'src="js/', content, flags=re.IGNORECASE)
    
    if content != orig:
        with open(fp, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Fixed paths in {os.path.basename(fp)}")

print("Done fixing paths.")
