import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# ---- index.php: Rename auto-generated IDs to semantic IDs ----
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Hero form (lines ~250): input_f41630b5=hero_name, input_3aaf90b2=hero_phone, input_5923e052=hero_email
# Footer/modal form (lines ~1729): input_02eb1c02=lead_name, input_4b1e566d=lead_phone, input_2f97f68e=lead_email
renames = {
    'input_f41630b5': 'hero_name',
    'input_3aaf90b2': 'hero_phone',
    'input_5923e052': 'hero_email',
    'input_02eb1c02': 'lead_name',
    'input_4b1e566d': 'lead_phone',
    'input_2f97f68e': 'lead_email',
}

for old, new in renames.items():
    content = content.replace(f'id="{old}"', f'id="{new}"')
    content = content.replace(f'for="{old}"', f'for="{new}"')
    print(f'  Renamed: {old} -> {new}')

# ---- Standardize form action endpoints ----
content = content.replace('action="api-submit-lead.php"', 'action="/api/v1/leads"')
content = content.replace('action="api-subscribe.php"', 'action="/api/v1/subscriptions"')
print('Form actions standardized.')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('index.php updated.')
