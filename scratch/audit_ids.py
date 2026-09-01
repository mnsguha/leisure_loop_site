import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

import re
# Find all lines with auto-generated IDs and surrounding context
for i, line in enumerate(lines):
    if re.search(r'id="input_[a-f0-9]+"', line):
        print(f"Line {i+1}: {line.strip()[:100]}")
        # Check next few lines for label associations
        for j in range(max(0,i-3), min(len(lines),i+4)):
            if 'for=' in lines[j] or 'label' in lines[j].lower():
                print(f"  Related L{j+1}: {lines[j].strip()[:80]}")
