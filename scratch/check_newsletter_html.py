import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    html = f.read()
    
idx = html.find('JOIN THE INNER CIRCLE')
if idx != -1:
    print(html[max(0, idx-400):idx+500])
