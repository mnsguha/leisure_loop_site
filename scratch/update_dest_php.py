import re
import time

php_path = 'G:/Antigravity/leisure_loop_site/public/destinations.php'
with open(php_path, 'r', encoding='utf-8') as f:
    php = f.read()

v = str(int(time.time()))
php = re.sub(r'destinations\.css\?v=[0-9]+', f'destinations.css?v={v}', php)

with open(php_path, 'w', encoding='utf-8') as f:
    f.write(php)
