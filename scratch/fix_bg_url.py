import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# The URL got triple-escaped. Extract the actual URL and fix the div.
# Pattern: find the inner-circle-bg div with any broken style
bad_bg = re.search(r'<div id="inner-circle-bg" style="background-image: url\([^)]+\);">', content)
if bad_bg:
    # Extract URL from the broken string
    url_match = re.search(r'https://[^)\\\'\"]+', bad_bg.group(0))
    if url_match:
        url = url_match.group(0).rstrip("'\\")
        print(f'Extracted URL: {url}')
        fixed_div = f'<div id="inner-circle-bg" style="background-image: url(\'{url}\');">'
        content = content[:bad_bg.start()] + fixed_div + content[bad_bg.end():]
        print('Fixed inner-circle-bg background-image.')
    else:
        print('Could not extract URL.')
else:
    print('Pattern not found - checking manually...')
    idx = content.find('inner-circle-bg')
    print(repr(content[idx:idx+200]))

# Fix remaining section inline style
content = content.replace(
    '<section class="inner-circle-banner" style="position: relative; overflow: hidden; padding: 8rem 0; text-align: center; border-top: 1px solid rgba(197, 160, 89, 0.15); border-bottom: 1px solid rgba(197, 160, 89, 0.15);">',
    '<section class="inner-circle-banner">'
)

# Fix overlay div
content = content.replace(
    '<div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(5,10,20,0.9), rgba(5,10,20,0.6), rgba(5,10,20,0.9)); z-index: 1;"></div>',
    '<div class="inner-circle-overlay"></div>'
)

# Fix label span
content = re.sub(
    r'<span style="color: var\(--gold\);[^"]+">Join The Inner Circle</span>',
    '<span class="section-label-gold">Join The Inner Circle</span>',
    content
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('index.php saved.')
