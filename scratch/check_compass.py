import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

print('=== Fixed Departures / Compass HTML ===')
for i, line in enumerate(lines):
    if 'compass' in line.lower() or 'fd-parallax' in line.lower() or 'fixed-departures' in line.lower():
        print(f'{i+1}: {line.rstrip()[:130]}')
