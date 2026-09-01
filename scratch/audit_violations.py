import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

print('=== 1. Modal close buttons (aria-label check) ===')
for i, line in enumerate(lines):
    if 'close-modal' in line or 'close_modal' in line or ('close' in line.lower() and 'modal' in line.lower() and ('button' in line.lower() or 'btn' in line.lower())):
        has_aria = 'aria-label' in line
        print(f'  L{i+1} [{"OK" if has_aria else "MISSING aria-label"}]: {line.strip()[:110]}')

print()
print('=== 2. CSRF tokens in forms ===')
in_form = False
form_start = 0
csrf_found = False
for i, line in enumerate(lines):
    if '<form' in line and ('method' in line.lower()):
        in_form = True
        form_start = i+1
        csrf_found = False
    if in_form and 'csrf' in line.lower():
        csrf_found = True
    if in_form and '</form>' in line:
        status = 'OK' if csrf_found else 'MISSING CSRF'
        print(f'  Form at L{form_start}: [{status}]')
        in_form = False

print()
print('=== 3. Theme pill touch target check ===')
for i, line in enumerate(lines):
    if 'theme-pill' in line and ('<a ' in line or '<button' in line):
        print(f'  L{i+1}: {line.strip()[:100]}')
