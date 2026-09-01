import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add inline style to the bg div
content = content.replace(
    '<div id="leisure-difference-bg" class="leisure-difference-bg"></div>',
    '<div id="leisure-difference-bg" class="leisure-difference-bg" style="background-image: url(\'https://images.unsplash.com/photo-1506905925224-162d4666579b?q=80&w=2070&auto=format&fit=crop\'); background-size: cover; background-position: center;"></div>'
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Inline style added to leisure-difference-bg.')
