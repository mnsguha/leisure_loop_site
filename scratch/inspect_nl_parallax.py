import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()

# Check how inner circle parallax is initialized
idx = js.find('initNewsletterParallax')
print("=== Newsletter Parallax init ===")
print(js[idx:idx+800])
