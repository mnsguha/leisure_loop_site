with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

in_setup = False
for i, line in enumerate(lines):
    if 'function setupInfiniteCarousel' in line:
        in_setup = True
    if in_setup:
        print(f"{i+1}: {line.strip()}")
        if '}' in line and i > 1250:
            break
