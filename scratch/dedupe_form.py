import re

path = 'g:/Antigravity/leisure_loop_site/public/index.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the duplicated Adults/Children row
# It was likely injected twice by the previous script
pattern = re.compile(r'(<div class="form-row">.*?</div>\s*){2,}', re.DOTALL)
content = pattern.sub(r'\1', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Form deduplicated.")
