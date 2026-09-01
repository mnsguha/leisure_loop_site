with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'themeMandala' in line:
        print(f"{i+1}: {repr(lines[i])}")
