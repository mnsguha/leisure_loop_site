with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('function initAnimations() {')
print(content[idx:idx+2000])
