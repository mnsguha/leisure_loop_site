import sys, io, time, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Update CSS cache buster
content = re.sub(
    r'href="css/home\.css\?v=\d+"',
    f'href="css/home.css?v={int(time.time())}"',
    content
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Updated cache buster in index.php.')
