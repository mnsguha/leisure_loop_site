import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

import re

# Find auto-generated IDs
auto_ids = re.findall(r'id="(input_[a-f0-9]+)"', content)
print('Auto-generated input IDs:')
for id_ in auto_ids:
    print(f'  {id_}')

# Find form action endpoints
form_actions = re.findall(r'action=["\']([^"\']+)["\']', content)
print('\nForm action endpoints:')
for a in form_actions[:10]:
    print(f'  {a}')
