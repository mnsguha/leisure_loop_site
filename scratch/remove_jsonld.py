with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('/* Extracted from sikkim-static-backup.php */')
if idx != -1:
    end_idx = content.find('/* Extracted from sikkim-static-backup.php */', idx + 10)
    if end_idx != -1:
        # Delete block including the closing comment
        content = content[:idx] + content[end_idx + len('/* Extracted from sikkim-static-backup.php */'):]
        with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
            f.write(content)
        print("Removed JSON-LD block from home.js")
