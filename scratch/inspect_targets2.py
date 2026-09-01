import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Get full mountainBg line
print('=== mountainBg full line ===')
for i, line in enumerate(lines):
    if 'mountainBg' in line and 'img' in line:
        print(f'{i+1}: {line.rstrip()}')

print()
print('=== silkContours full line ===')
for i, line in enumerate(lines):
    if 'silkContours' in line and 'div' in line:
        print(f'{i+1}: {line.rstrip()}')

print()
print('=== step2Modal full line ===')
for i, line in enumerate(lines):
    if 'step2Modal' in line and 'modal-overlay' in line:
        print(f'{i+1}: {line.rstrip()[:150]}')

print()
print('=== data-focus eval line ===')
for i, line in enumerate(lines):
    if 'data-focus' in line or 'eval:' in line:
        print(f'{i+1}: {line.rstrip()[:150]}')

print()
print('=== heroDateInput ===')
for i, line in enumerate(lines):
    if 'date' in line.lower() and ('input' in line.lower() or 'hero' in line.lower()):
        if 'type' in line:
            print(f'{i+1}: {line.rstrip()[:150]}')
