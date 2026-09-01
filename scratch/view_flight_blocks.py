import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Show both flight-anim blocks in context
for i, m in enumerate(re.finditer(r'\.flight-anim.*?\}', content, re.DOTALL)):
    print(f'--- Block {i+1} (char {m.start()}) ---')
    print(repr(content[m.start():m.end()]))
    print()
