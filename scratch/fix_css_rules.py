import os
import re

CSS_DIR = r'g:\Antigravity\leisure_loop_site\public\css'

for f in os.listdir(CSS_DIR):
    if f.endswith('.css'):
        fp = os.path.join(CSS_DIR, f)
        with open(fp, 'r', encoding='utf-8') as file:
            content = file.read()
        
        orig = content
        # Remove the global .is-active injections
        content = re.sub(r'^\.is-active\s*\{.*?\}\s*', '', content, flags=re.MULTILINE)
        content = re.sub(r'^\.is-active-flex\s*\{.*?\}\s*', '', content, flags=re.MULTILINE)

        # Fix paths url('assets/...') to url('../assets/...')
        content = re.sub(r"url\(['\"]?assets/", "url('../assets/", content)
        content = re.sub(r"url\(['\"]?images/", "url('../images/", content)
        
        # In case they were already ../assets/ and got doubled to ../../assets/
        content = re.sub(r"url\(['\"]?\.\./\.\./assets/", "url('../assets/", content)
        content = re.sub(r"url\(['\"]?\.\./\.\./images/", "url('../images/", content)

        if content != orig:
            with open(fp, 'w', encoding='utf-8') as file:
                file.write(content)
            print(f"Repaired {f}")

print("Done repairing CSS paths and specificity.")
