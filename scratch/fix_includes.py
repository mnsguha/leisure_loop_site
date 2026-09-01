import os, re
INCLUDES = r'g:\Antigravity\leisure_loop_site\includes'
for f in os.listdir(INCLUDES):
    if f.endswith('.php'):
        path = os.path.join(INCLUDES, f)
        with open(path, 'r', encoding='utf-8') as file: content = file.read()
        new_content = re.sub(r'href=["\']/css/', 'href="css/', content, flags=re.IGNORECASE)
        new_content = re.sub(r'src=["\']/js/', 'src="js/', new_content, flags=re.IGNORECASE)
        if content != new_content:
            with open(path, 'w', encoding='utf-8') as file: file.write(new_content)
            print(f'Fixed {f}')
