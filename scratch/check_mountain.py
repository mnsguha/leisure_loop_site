with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'mountainBg' in line:
        for j in range(i-5, i+15):
            if j >= 0 and j < len(lines):
                print(f"{j+1}: {lines[j].strip()}")
        break
