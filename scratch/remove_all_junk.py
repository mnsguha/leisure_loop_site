import os

directory = 'G:/Antigravity/leisure_loop_site/public/js/modules/'
for filename in os.listdir(directory):
    if filename.endswith('.js'):
        filepath = os.path.join(directory, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        idx = content.find('/* Extracted from ')
        if idx != -1:
            end_idx = content.find('/* Extracted from ', idx + 10)
            if end_idx != -1:
                # Find end of second comment
                end_comment = content.find('*/', end_idx) + 2
                
                # Delete block
                content = content[:idx] + content[end_comment:]
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Removed junk from {filename}")
            else:
                # Just remove from idx to the end of the file if there's no closing comment
                content = content[:idx]
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Removed trailing junk from {filename}")
