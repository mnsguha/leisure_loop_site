import os
import glob

directories = [
    r"g:\Antigravity\leisure_loop_site\includes\*.php",
    r"g:\Antigravity\leisure_loop_site\public\*.php",
    r"g:\Antigravity\leisure_loop_site\public\admin\*.php"
]

for directory in directories:
    for filepath in glob.glob(directory):
        with open(filepath, 'r', encoding='utf-8') as file:
            content = file.read()
            
        if 'logo.png' in content:
            new_content = content.replace('logo.png', 'logo.webp')
            with open(filepath, 'w', encoding='utf-8') as file:
                file.write(new_content)
            print(f"Updated {filepath}")

print("Replacement complete.")
