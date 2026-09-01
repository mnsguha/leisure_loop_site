with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    lines = f.readlines()
print(f'home.css: {len(lines)} lines')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    lines2 = f.readlines()
print(f'home.js: {len(lines2)} lines')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines3 = f.readlines()
print(f'index.php: {len(lines3)} lines')
