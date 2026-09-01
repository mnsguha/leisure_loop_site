import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the inner text wrapper div
content = content.replace(
    '<div style="display: flex; flex-direction: column;">',
    '<div class="theme-pill-info">'
)

# Fix the pill name span - read the full inline style
# Find and replace the name span
content = re.sub(
    r'<span style="font-family: .Inter., sans-serif; font-size: 0\.85rem; font-weight: 600; color: [^"]+">',
    '<span class="theme-pill-name">',
    content
)

# Fix the count span
content = re.sub(
    r'<span style="font-size: 0\.65rem; color: #64748b; text-transform: uppercase; letter-spacing: [^"]+">',
    '<span class="theme-pill-count">',
    content
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done.')
