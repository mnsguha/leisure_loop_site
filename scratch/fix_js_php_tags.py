import re

js_path = 'G:/Antigravity/leisure_loop_site/public/js/modules/destinations.js'
with open(js_path, 'r', encoding='utf-8') as f:
    js = f.read()

# Replace all occurrences of <?php ... ?>
js = re.sub(r'<\?php(.*?)\?>', '', js, flags=re.DOTALL)

with open(js_path, 'w', encoding='utf-8') as f:
    f.write(js)
