with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Count "Extracted from" comments
import re
extracted = re.findall(r'/\* Extracted from[^*]*/\*?/', content)
print(f'Extracted comments: {len(re.findall(r"/\* Extracted from", content))}')

# Find duplicate selectors
selectors = re.findall(r'^([.#][a-zA-Z0-9_-]+)\s*\{', content, re.MULTILINE)
from collections import Counter
dupes = [(s, c) for s, c in Counter(selectors).items() if c > 1]
print('Duplicate selectors (top 10):')
for s, c in sorted(dupes, key=lambda x: -x[1])[:10]:
    print(f'  {s}: {c}x')
