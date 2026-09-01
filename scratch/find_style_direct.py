with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Find all style. usages
import re
for m in re.finditer(r'.{0,20}style\..{0,50}', content):
    print(repr(m.group()))
