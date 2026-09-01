with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'id="closeStep2Modal"' in line:
        lines[i] = '                <button type="button" class="close-modal" id="closeStep2Modal" aria-label="Close dialog">&times;</button>\n'
        print(f"Replaced line {i+1}")

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.writelines(lines)
