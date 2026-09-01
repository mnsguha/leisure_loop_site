# Fix 1: Remove inline style from #themeMandala in index.php
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

import re
# Strip the full style attribute from the themeMandala img tag
content = re.sub(
    r'(<img id="themeMandala"[^>]*?) style="[^"]*"',
    r'\1',
    content
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("index.php: Removed inline style from #themeMandala")
