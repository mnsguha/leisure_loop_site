import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('<div class="container difference-container">')
if idx == -1:
    idx = content.find('difference-container')

print(content[max(0, idx-100):idx+800])
