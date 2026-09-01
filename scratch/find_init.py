with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

index = content.find('if (document.readyState === \\'complete\\') {')
if index != -1:
    print(content[index-150:index+300])
else:
    print('Not found')
