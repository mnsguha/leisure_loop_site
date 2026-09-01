import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Check line 125
print('=== Around line 125 ===')
for i in range(122, 130):
    print(f'{i+1}: {lines[i].rstrip()}')

print()
print('=== Around line 735 ===')
for i in range(732, 742):
    print(f'{i+1}: {lines[i].rstrip()}')

print()
print('=== mountainBg and silkContours ===')
for i, line in enumerate(lines):
    if 'mountainBg' in line or 'silkContours' in line:
        print(f'{i+1}: {line.rstrip()[:120]}')
