import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
exclude_css = ['style.css', 'modals.css', 'admin.css']

files_to_check = []
for root, _, files in os.walk(PUBLIC_DIR):
    if 'leisure_loop_site' in root.replace(PUBLIC_DIR, ''): continue # skip nested backup
    if 'admin' in root: continue # skip admin
    for f in files:
        if f.endswith('.php'):
            files_to_check.append(os.path.join(root, f))

for fp in files_to_check:
    with open(fp, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the header include line
    header_match = re.search(r'include[\s\(]+[\'"]\.\./includes/header\.php[\'"][\s\)]*;?', content)
    if not header_match:
        continue

    # Extract all CSS links that are NOT in exclude_css
    link_pattern = re.compile(r'^\s*<link rel="stylesheet" href="css/([^"]+)">\s*$', re.MULTILINE | re.IGNORECASE)
    
    extracted_links = []
    for match in link_pattern.finditer(content):
        css_file = match.group(1) + ".css"
        if match.group(1).endswith('.css'):
            css_file = match.group(1)
        if css_file not in exclude_css:
            extracted_links.append(match.group(0).strip())

    if not extracted_links:
        continue

    # Remove them from their original locations
    def replacer(m):
        css_file = m.group(1) + ".css"
        if m.group(1).endswith('.css'):
            css_file = m.group(1)
        if css_file not in exclude_css:
            return ""
        return m.group(0)

    new_content = link_pattern.sub(replacer, content)

    # Re-insert them immediately after header include
    header_str = header_match.group(0)
    links_str = "\n" + "\n".join(extracted_links) + "\n"
    new_content = new_content.replace(header_str, header_str + links_str, 1)

    if new_content != content:
        with open(fp, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Fixed cascade in {os.path.basename(fp)}")

print("Done fixing cascade.")
