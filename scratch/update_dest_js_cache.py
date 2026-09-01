import re
import time

php_path = 'G:/Antigravity/leisure_loop_site/public/destinations.php'
with open(php_path, 'r', encoding='utf-8') as f:
    php = f.read()

v = str(int(time.time()))
# Replace <script src="js/modules/destinations.js" defer></script>
# Or if it has ?v=, replace it.
php = re.sub(r'js/modules/destinations\.js(\?v=[0-9]+)?', f'js/modules/destinations.js?v={v}', php)

with open(php_path, 'w', encoding='utf-8') as f:
    f.write(php)
