with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

idx = content.find('// --- ParableVC Style 3D Parallax Hero ---')
if idx != -1:
    print(content[idx-100:idx+300])
else:
    print('Not found')
