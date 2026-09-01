import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'inner circle' in line.lower():
        print(f'Found at line {i+1}:')
        start = max(0, i-15)
        end = min(len(lines), i+30)
        for j in range(start, end):
            print(f"{j+1}: {lines[j].rstrip()}")
        break
