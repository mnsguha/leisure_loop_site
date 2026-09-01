import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    js = f.read()

idx2 = js.find('leisure-difference-bg')
if idx2 != -1:
    print('Found leisure-difference-bg in backup:')
    print(js[max(0, idx2-400):idx2+600])
else:
    print('Not found')
