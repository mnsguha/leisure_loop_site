import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'newsletter' in line.lower() or 'inner circle' in line.lower() or 'subscribe' in line.lower() or 'newsletter-section' in line.lower():
        for j in range(max(0,i-3), min(len(lines), i+40)):
            print(f'{j+1}: {lines[j].rstrip()}')
        print('---')
        break
