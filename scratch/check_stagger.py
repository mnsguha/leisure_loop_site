with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

if 'function applyStaggering()' in content:
    print("Yes, applyStaggering() is present in home.js.")
else:
    print("NO, applyStaggering() is MISSING from home.js!")
