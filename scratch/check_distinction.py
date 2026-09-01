import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()

idx = js.find('function initDistinctionAnimation()')
if idx != -1:
    print('Found in home.js:')
    print(js[idx:idx+800])
else:
    print('Not found in home.js')

with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    js = f.read()

idx = js.find('function initDistinctionAnimation()')
if idx != -1:
    print('Found in backup:')
    print(js[idx:idx+800])
else:
    idx2 = js.find('leisure-difference-bg')
    if idx2 != -1:
        print('Found leisure-difference-bg in backup:')
        print(js[max(0, idx2-200):idx2+600])

