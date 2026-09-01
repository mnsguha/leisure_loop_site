with open('G:/Antigravity/leisure_loop_site/public/js/main.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'mousedown' in line and i > 730:
        for j in range(i, i+15):
            print(f"{j+1}: {lines[j].strip()}")
        break
