import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

date_listener = '''
    // Date input type toggle (text <-> date) for UX
    const dateInput = document.getElementById('heroDateInput');
    if (dateInput) {
        dateInput.addEventListener('focus', () => { dateInput.type = 'date'; });
        dateInput.addEventListener('blur', () => { if (!dateInput.value) dateInput.type = 'text'; });
    }
'''

# Inject inside DOMContentLoaded, after bindGlobalEvents() and initAnimations()
content = content.replace(
    "document.addEventListener('DOMContentLoaded', () => {\n    bindGlobalEvents();\n    initAnimations();\n});",
    "document.addEventListener('DOMContentLoaded', () => {\n    bindGlobalEvents();\n    initAnimations();" + date_listener + "});"
)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print('home.js: Date input focus listener added.')
