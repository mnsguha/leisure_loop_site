import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Check how many lines init_body.js has and look at all animation-related sections
with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

print(f'Total lines: {len(lines)}')

# Look for any parallax or ScrollTrigger after line 480
for i in range(480, min(len(lines), 700)):
    l = lines[i].rstrip()
    if l.strip():
        print(f'{i+1}: {l}')
