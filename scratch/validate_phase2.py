import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
INCLUDES_DIR = r'g:\Antigravity\leisure_loop_site\includes'

files_to_check = []
for d in [PUBLIC_DIR, INCLUDES_DIR]:
    for root, _, files in os.walk(d):
        if 'leisure_loop_site' in root.replace(PUBLIC_DIR, ''):
            continue # skip the backup
        for f in files:
            if f.endswith(('.php', '.html')):
                files_to_check.append(os.path.join(root, f))

inline_count = 0
forms_total = 0
forms_with_csrf = 0

inline_pattern = re.compile(r'\bon(click|focus|blur|change|submit)\s*=', re.IGNORECASE)

for fp in files_to_check:
    with open(fp, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check inline
    matches = inline_pattern.findall(content)
    if matches:
        print(f"Inline found in {os.path.basename(fp)}: {len(matches)} occurrences")
        inline_count += len(matches)
        
    # Check forms
    forms = re.findall(r'<form[^>]*>(.*?)</form>', content, flags=re.IGNORECASE | re.DOTALL)
    for form in forms:
        forms_total += 1
        if 'name="csrf_token"' in form or "name='csrf_token'" in form:
            forms_with_csrf += 1
        else:
            print(f"Missing CSRF in {os.path.basename(fp)}")
            
print(f"\n--- AUDIT REPORT ---")
print(f"Total files checked: {len(files_to_check)}")
print(f"Remaining Inline Handlers: {inline_count}")
print(f"Forms checked: {forms_total}")
print(f"Forms with CSRF: {forms_with_csrf}")
print(f"Forms missing CSRF: {forms_total - forms_with_csrf}")
