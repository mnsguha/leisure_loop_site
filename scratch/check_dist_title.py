with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'Why Discerning Explorers' in line or 'Why<br>Discerning' in line or 'Why\nDiscerning' in line or 'Why Discerning' in line:
        for j in range(i-20, i+20):
            print(f"{j+1}: {lines[j].strip()}")
        break
