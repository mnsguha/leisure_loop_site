import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

old_css = '''
.difference-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(197, 160, 89, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}'''

new_css = '''
.difference-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(197, 160, 89, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
}'''

if old_css in css:
    css = css.replace(old_css, new_css)
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(css)
    print("Replaced CSS.")
else:
    print("Could not find exact block, trying regex.")
    import re
    css = re.sub(r'\.difference-icon-wrapper\s*\{[^}]*\}', new_css.strip(), css)
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(css)
    print("Replaced CSS via regex.")

