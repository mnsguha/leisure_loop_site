import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix section inline style -> class
content = content.replace(
    '<section class="inner-circle-banner" style="position: relative; overflow: hidden; padding: 8rem 0; text-align: center; border-top: 1px solid rgba(197,160,89,0.08);">',
    '<section class="inner-circle-banner">'
)

# Fix bg div inline style -> id only (CSS will handle it)
content = re.sub(
    r'<div id="inner-circle-bg" style="position: absolute; inset: 0; width: 100%; height: 100%; background-image: url\(\'([^\']+)\'\);[^"]*">',
    r'<div id="inner-circle-bg" style="background-image: url(\'\1\');">',
    content
)

# Fix overlay div inline style -> class
content = content.replace(
    '<div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(5,10,20,0.9), rgba(5,10,20,0.6), rgba(5,10,20,0.8));">',
    '<div class="inner-circle-overlay">'
)

# Fix label inline style -> class
content = content.replace(
    '<span style="color: var(--gold); font-size: 0.85rem; letter-spacing: 0.25em; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 1rem;">JOIN THE INNER CIRCLE</span>',
    '<span class="section-label-gold">JOIN THE INNER CIRCLE</span>'
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('index.php: newsletter section inline styles cleaned.')
