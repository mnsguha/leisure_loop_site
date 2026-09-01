import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Check for duplicate flight-anim blocks
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Find all occurrences of flight-anim
matches = [m.start() for m in re.finditer(r'\.flight-anim', content)]
print(f'flight-anim occurrences: {len(matches)}')
for m in matches:
    print(f'  at char {m}: {repr(content[m:m+80])}')
