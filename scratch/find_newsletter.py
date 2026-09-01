import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'inner circle' in line.lower() or 'newsletter' in line.lower() or 'subscribe' in line.lower() and 'section' in line.lower():
        for j in range(max(0,i-2), min(len(lines), i+10)):
            print(f'{j+1}: {lines[j].rstrip()[:130]}')
        print('---')
        break
