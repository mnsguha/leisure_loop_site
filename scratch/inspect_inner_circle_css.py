import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

# Get inner-circle-banner CSS
idx = css.find('.inner-circle-banner')
print("=== Inner Circle CSS ===")
print(css[max(0,idx-50):idx+800])
