import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Check all modal-related buttons/links across modals.css, modals.js, and any include files
import os

files_to_check = [
    'G:/Antigravity/leisure_loop_site/public/index.php',
    'G:/Antigravity/leisure_loop_site/public/css/modals.css',
    'G:/Antigravity/leisure_loop_site/public/js/modals.js',
]

# Also find include files
includes_dir = 'G:/Antigravity/leisure_loop_site/includes'
for f in os.listdir(includes_dir):
    if f.endswith('.php'):
        files_to_check.append(f'G:/Antigravity/leisure_loop_site/includes/{f}')

print('=== All close/modal buttons across all files ===')
for filepath in files_to_check:
    try:
        with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
            lines = f.readlines()
        for i, line in enumerate(lines):
            if ('close' in line.lower() and ('button' in line.lower() or '<a ' in line.lower())) or 'data-action=\"close' in line:
                has_aria = 'aria-label' in line
                fname = os.path.basename(filepath)
                print(f'  {fname} L{i+1} [{"OK" if has_aria else "NO aria-label"}]: {line.strip()[:100]}')
    except:
        pass

print()
print('=== CSRF check in all forms across includes ===')
for filepath in files_to_check:
    try:
        with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
            content = f.read()
        forms = re.findall(r'<form[^>]*method[^>]*>.*?</form>', content, re.DOTALL | re.IGNORECASE)
        for form in forms:
            has_csrf = 'csrf' in form.lower()
            fname = os.path.basename(filepath)
            # get action if present
            action = re.search(r'action=["\']([^"\']*)["\']', form)
            act = action.group(1) if action else 'unknown'
            print(f'  {fname} action={act}: [{"OK" if has_csrf else "MISSING CSRF"}]')
    except:
        pass
