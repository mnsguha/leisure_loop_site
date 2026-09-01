with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('/* Extracted from sikkim-static-backup.php */')
if idx != -1:
    end_idx = content.find('/* Extracted from sikkim-static-backup.php */', idx + 10)
    print("Found block from", idx, "to", end_idx)
    if end_idx != -1:
        print(content[idx:end_idx+50])
