import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Final verification across all three files

print('=== home.css ===')
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()
print(f'  Lines: {len(css.splitlines())}')
print(f'  !important count: {len(re.findall(chr(33)+"important", css))}')
print(f'  Extracted comments: {len(re.findall(r"/[*] Extracted from", css))}')
for bad in ['custom-typo', 'sikkim-static', '.badge', '.concept-card']:
    status = 'BAD' if bad in css else 'OK'
    print(f'  {status}: {bad}')

print()
print('=== home.js ===')
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()
print(f'  Lines: {len(js.splitlines())}')
print(f'  console.warn remaining: {len(re.findall(r"console.warn", js))}')
print(f'  try-catch blocks: {len(re.findall(r"\} catch \(", js))}')
print(f'  DOMContentLoaded: {"YES" if "DOMContentLoaded" in js else "NO"}')

print()
print('=== index.php ===')
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    php = f.read()
old_ids_remaining = re.findall(r'id="input_[a-f0-9]+"', php)
print(f'  Auto-gen IDs remaining: {len(old_ids_remaining)}')
print(f'  /api/v1/leads: {php.count("/api/v1/leads")} occurrences')
print(f'  /api/v1/subscriptions: {php.count("/api/v1/subscriptions")} occurrences')
print(f'  Old api-submit-lead.php: {php.count("api-submit-lead.php")} occurrences')
