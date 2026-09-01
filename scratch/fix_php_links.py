import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'

files_to_check = []
for root, _, files in os.walk(PUBLIC_DIR):
    if 'leisure_loop_site' in root.replace(PUBLIC_DIR, ''): continue
    for f in files:
        if f.endswith('.php'):
            files_to_check.append(os.path.join(root, f))

for fp in files_to_check:
    with open(fp, 'r', encoding='utf-8') as f:
        content = f.read()

    orig = content

    # Find the link tags that were incorrectly placed
    # Pattern: (include '../includes/header.php';)\s*(<link rel="stylesheet"[^>]+>)\s*(\?>)
    # But there might be multiple links, or spaces.
    
    # We can just match the block: include ... \n <link ...> \n ?>
    # and change it to include ... \n ?> \n <link ...>
    # If there is other PHP code between <link> and ?>, we close PHP before link and reopen after.
    
    # Let's match: include '...header.php'; \s* <link ... > \s*
    # We will close the php tag right before the link: `?>\n<link ...>`
    # and then reopen it right after: `\n<?php` 
    # IF we are inside a PHP block.
    # But if the very next token is `?>`, we can just swap them.
    
    pattern = re.compile(r"(include[\s\(]+['\"]\.\./includes/header\.php['\"][\s\)]*;.*?)\n(<link rel=\"stylesheet\" href=\"css/[^\"]+\">\s*)+", re.DOTALL)
    
    def replacer(m):
        header_part = m.group(1)
        # the entire match string
        full_match = m.group(0)
        # the links
        links = full_match[len(header_part):].strip()
        
        return header_part + "\n?>\n" + links + "\n<?php\n"

    new_content = pattern.sub(replacer, content)
    
    # Clean up empty php blocks `<?php\s*?>`
    new_content = re.sub(r'<\?php\s*\?>', '', new_content)
    
    if new_content != orig:
        with open(fp, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Fixed {os.path.basename(fp)}")

print("Done.")
