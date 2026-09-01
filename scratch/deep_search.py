import os, sys

def search_files(directory, query):
    for root, dirs, files in os.walk(directory):
        if '.git' in root or 'node_modules' in root:
            continue
        for file in files:
            if not file.endswith('.php') and not file.endswith('.html') and not file.endswith('.js'):
                continue
            path = os.path.join(root, file)
            try:
                with open(path, 'r', encoding='utf-8') as f:
                    content = f.read()
                    if query.lower() in content.lower():
                        print(f'Found "{query}" in: {path}')
            except:
                pass

search_files('G:/Antigravity/leisure_loop_site', 'inner circle')
