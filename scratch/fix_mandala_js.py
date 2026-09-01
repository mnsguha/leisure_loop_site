with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'gsap.to(themeMandala, {' in line:
        lines.insert(i, '            gsap.set(themeMandala, { xPercent: -50, yPercent: -50, x: 0, y: 0 });\n')
        break

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.writelines(lines)
