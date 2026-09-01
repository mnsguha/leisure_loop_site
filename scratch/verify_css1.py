import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

print(f'Lines after orphan removal: {len(content.splitlines())}')
print(f'!important remaining: {len(re.findall(chr(33)+"important", content))}')

# Check for any leftover showcase/sikkim markers
for marker in ['custom-typo', 'sikkim-static', '--navy:', '.badge', '.concept-card', '.glass-card']:
    if marker in content:
        print(f'WARNING: still found: {marker}')
    else:
        print(f'OK: {marker} removed')
