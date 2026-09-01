with open('G:/Antigravity/leisure_loop_site/public/js/main.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'const requiredCopies = Math.max(2, Math.ceil(window.innerWidth / originalTotalWidth) + 1);' in line:
        lines[i] = 'if (!originalTotalWidth || originalTotalWidth <= 0) return;\nconst requiredCopies = Math.min(4, Math.max(2, Math.ceil(window.innerWidth / originalTotalWidth) + 1));\n'
        print(f"Replaced width calculation at {i+1}")
    elif 'e.preventDefault();' in line and i > 750 and i < 770 and 'carousel.addEventListener(\'mousedown\'' in ''.join(lines[i-10:i]):
        lines[i] = '// e.preventDefault(); removed to allow click through\n'
        print(f"Removed preventDefault at {i+1}")

with open('G:/Antigravity/leisure_loop_site/public/js/main.js', 'w', encoding='utf-8') as f:
    f.writelines(lines)
