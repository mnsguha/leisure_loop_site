import os

files = [
    'g:/Antigravity/leisure_loop_site/public/index_853pm.php',
    'g:/Antigravity/leisure_loop_site/public/index_final_restore.php',
    'g:/Antigravity/leisure_loop_site/public/index_restored.php',
    'g:/Antigravity/leisure_loop_site/public/index_restored_v2.php'
]

for f in files:
    if os.path.exists(f):
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
        if content.startswith('"<?php'):
            content = content[1:]
            if content.endswith('"'):
                content = content[:-1]
            with open(f, 'w', encoding='utf-8') as file:
                file.write(content)
            print(f'Fixed quotes in {f}')
