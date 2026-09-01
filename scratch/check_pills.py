import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(825, 845):
    print(f'{i+1}: {repr(lines[i].rstrip()[:120])}')
