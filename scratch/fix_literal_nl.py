with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace('\\nfunction bootHomePage', '\nfunction bootHomePage')
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed literal newline in home.js")
