import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

idx = css.find('newsletter-section')
if idx != -1:
    print(css[max(0, idx-100):idx+800])
