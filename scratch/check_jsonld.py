import os

directory = 'G:/Antigravity/leisure_loop_site/public/js/modules/'
for filename in os.listdir(directory):
    if filename.endswith('.js'):
        filepath = os.path.join(directory, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        count = content.count('"@context"')
        if count > 0:
            print(f"{filename} has {count} JSON-LD payloads remaining")
