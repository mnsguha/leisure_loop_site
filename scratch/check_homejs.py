import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Check current home.js to find where to inject
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Check what's in initAnimations
idx = content.find('function initAnimations()')
print('initAnimations body:')
print(content[idx:idx+600])
