with open('G:/Antigravity/leisure_loop_site/public/js/modules/mobile-views.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()
for i in range(230, 250):
    if i < len(lines):
        print(f"{i+1}: {lines[i].strip()}")
