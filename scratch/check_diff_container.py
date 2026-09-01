import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

import re
pattern = r'\.difference-container\s*\{.*?\}'
match = re.search(pattern, content, re.DOTALL)
if match:
    print(match.group(0))
