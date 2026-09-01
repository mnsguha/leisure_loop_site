import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Search across full file for inner-circle or newsletter
import re
for m in re.finditer(r'inner.circle|newsletter|inner_circle', content, re.IGNORECASE):
    start = max(0, m.start() - 50)
    end = min(len(content), m.start() + 500)
    print(f'Found at char {m.start()}:')
    print(content[start:end])
    print('---')
