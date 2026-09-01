import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

print('compass in home.js:', 'compass' in content.lower())
print('fixed-departures in home.js:', 'fixed-departures' in content.lower())
print()

# Also check backup
with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    backup = f.read()

idx = backup.find('Fixed Departures Compass')
if idx >= 0:
    print('=== Backup compass animation ===')
    print(backup[idx:idx+700])
