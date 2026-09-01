with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'function bindGlobalEvents' in line:
        for j in range(i, i+30):
            print(f"{j+1}: {lines[j].strip()}")
        break
