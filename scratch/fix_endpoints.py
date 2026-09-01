import sys, io, re, os
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

includes = 'G:/Antigravity/leisure_loop_site/includes'
fixes = 0

# Fix 3: Standardize old api/submit-lead.php endpoints to /api/v1/leads
for fname in os.listdir(includes):
    if not fname.endswith('.php'):
        continue
    fpath = f'{includes}/{fname}'
    with open(fpath, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()
    original = content

    content = content.replace('action="../api/submit-lead.php"', 'action="/api/v1/leads"')
    content = content.replace('action="api-submit-lead.php"', 'action="/api/v1/leads"')
    content = content.replace('action="../api/subscribe.php"', 'action="/api/v1/subscriptions"')

    if content != original:
        with open(fpath, 'w', encoding='utf-8', errors='replace') as f:
            f.write(content)
        fixes += 1
        print(f'  Standardized endpoint: {fname}')

print(f'Total files updated: {fixes}')
