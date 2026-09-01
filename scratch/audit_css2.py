with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Check for important count
import re
important_count = len(re.findall(r'!important', content))
print(f'!important uses: {important_count}')

# Check for orphaned/showcase selectors
orphans = re.findall(r'\.custom-typo|\.backup-|showcase', content)
print(f'Orphan patterns found: {len(orphans)}')

# Print all section-level comments for structure overview
comments = re.findall(r'/\*[^*]*\*/', content)
for c in comments[:30]:
    print(repr(c[:80]))
