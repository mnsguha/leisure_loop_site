import re

css_path = 'G:/Antigravity/leisure_loop_site/public/css/destinations.css'
with open(css_path, 'r', encoding='utf-8') as f:
    css = f.read()

# 1. Update .catalog-card-item
css = re.sub(
    r'\.catalog-card-item \{[^}]+\}',
    '.catalog-card-item {\n    aspect-ratio: 4/5;\n    min-height: 420px;\n    background: rgba(255,255,255,0.025);\n    border: 1px solid rgba(255,255,255,0.06);\n    border-radius: 16px;\n    overflow: hidden;\n    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);\n    position: relative;\n}',
    css
)

# 2. Update .catalog-card-anchor
css = re.sub(
    r'\.catalog-card-anchor \{[^}]+\}',
    '.catalog-card-anchor {\n    display: block;\n    width: 100%;\n    height: 100%;\n    text-decoration: none;\n    color: inherit;\n}',
    css
)

# 3. Update .catalog-card-image-shell
css = re.sub(
    r'\.catalog-card-image-shell \{[^}]+\}',
    '.catalog-card-image-shell {\n    position: absolute;\n    inset: 0;\n    width: 100%;\n    height: 100%;\n    z-index: 1;\n    overflow: hidden;\n}\n\n.catalog-card-image-shell::after {\n    content: \'\';\n    position: absolute;\n    inset: 0;\n    background: linear-gradient(to top, rgba(3,8,17,0.95) 0%, rgba(3,8,17,0.5) 40%, transparent 100%);\n    z-index: 2;\n}',
    css
)

# 4. Add .catalog-card-body block
if '.catalog-card-body {' in css:
    css = re.sub(
        r'\.catalog-card-body \{[^}]+\}',
        '.catalog-card-body {\n    position: relative;\n    z-index: 3;\n    display: flex;\n    flex-direction: column;\n    justify-content: flex-end;\n    padding: 30px 24px 24px;\n    height: 100%;\n}',
        css
    )
else:
    css = css.replace('.catalog-card-destination {', '.catalog-card-body {\n    position: relative;\n    z-index: 3;\n    display: flex;\n    flex-direction: column;\n    justify-content: flex-end;\n    padding: 30px 24px 24px;\n    height: 100%;\n}\n\n.catalog-card-destination {')

with open(css_path, 'w', encoding='utf-8') as f:
    f.write(css)

print("CSS updated successfully.")
