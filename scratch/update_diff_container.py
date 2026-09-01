import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    '.difference-container {\n    position: relative;\n    z-index: 2;\n}',
    '.difference-container {\n    position: relative;\n    z-index: 2;\n    text-align: center;\n    margin-bottom: 4rem;\n}'
)

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated difference-container CSS.")
