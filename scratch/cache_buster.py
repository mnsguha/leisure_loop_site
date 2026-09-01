import sys, io, time
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add cache buster to home.css
import re
new_content = re.sub(
    r'href="css/home\.css[^"]*"',
    f'href="css/home.css?v={int(time.time())}"',
    content
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print('Added cache buster to home.css in index.php.')
