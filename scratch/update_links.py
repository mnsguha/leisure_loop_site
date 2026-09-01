import os
import re

base_dir = r'G:\Antigravity\leisure_loop_site\public'
files_to_update = ['all-tours.php', 'package-detail.php', 'package.php', 'pkg.php']

for file in files_to_update:
    file_path = os.path.join(base_dir, file)
    if os.path.exists(file_path):
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace css
        content = re.sub(r'<link rel="stylesheet" href="css/packages\.css".*?>', f'<link rel="stylesheet" href="css/{file.replace(".php", ".css")}">', content)
        # Replace js
        content = re.sub(r'<script src="js/modules/packages\.js".*?></script>', f'<script src="js/modules/{file.replace(".php", ".js")}" defer></script>', content)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)

print("PHP links updated.")
