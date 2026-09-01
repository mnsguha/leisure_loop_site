import sys, io, re, os
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

includes = 'G:/Antigravity/leisure_loop_site/includes'
fails = []

# Re-check aria-labels
for fname in os.listdir(includes):
    if not fname.endswith('.php'):
        continue
    fpath = f'{includes}/{fname}'
    with open(fpath, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()
    for i, line in enumerate(lines):
        if ('data-action="close' in line or 'data-action="apply-filters-close"' in line) and 'aria-label' not in line:
            fails.append(f'{fname} L{i+1}: {line.strip()[:80]}')

if fails:
    print(f'REMAINING aria-label issues ({len(fails)}):')
    for f in fails:
        print(f'  {f}')
else:
    print('OK: All modal close triggers have aria-label')

# Check endpoints
old_endpoints = []
for fname in os.listdir(includes):
    if not fname.endswith('.php'):
        continue
    fpath = f'{includes}/{fname}'
    with open(fpath, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()
    if 'submit-lead.php' in content or 'api-submit-lead' in content:
        old_endpoints.append(fname)

if old_endpoints:
    print(f'REMAINING old endpoints in: {old_endpoints}')
else:
    print('OK: All mobile forms point to /api/v1/leads')

# Check theme-pill CSS
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()
print('OK: min-height: 48px' if 'min-height: 48px' in css else 'FAIL: min-height missing')
print('OK: :active tap feedback' if '.theme-pill:active' in css else 'FAIL: :active missing')
