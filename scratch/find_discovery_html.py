import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'discovery' in line.lower() or 'art-of' in line.lower() or 'the-process' in line.lower():
        for j in range(max(0, i-1), min(len(lines), i+5)):
            print(f'{j+1}: {lines[j].rstrip()[:120]}')
        print('---')
