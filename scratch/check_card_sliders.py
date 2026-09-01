with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

import re
match = re.search(r'function initCardSliders\(\) \{.*?\n\}', content, flags=re.DOTALL)
if match:
    print(match.group(0))
else:
    print("Not found")
