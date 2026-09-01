with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'id="themeMandala"' in line:
        # insert transform: translate(-50%, -50%); after left: 50%;
        lines[i] = line.replace('left: 50%;', 'left: 50%; transform: translate(-50%, -50%);')
        print(f"Replaced line {i+1}")
        break

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.writelines(lines)
