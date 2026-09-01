import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Get the full process section animation
for i in range(430, 510):
    print(f'{i+1}: {lines[i].rstrip()}')
