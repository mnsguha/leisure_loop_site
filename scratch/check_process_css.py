import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

import re
# Check if process-card styles exist
for sel in ['.process-card', '.process-img-canvas', '.how-we-work-section', '.process-carousel']:
    if sel in content:
        print(f'EXISTS: {sel}')
    else:
        print(f'MISSING: {sel}')
