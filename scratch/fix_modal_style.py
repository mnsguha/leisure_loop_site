# Fix 2: Replace modal.style.display = 'flex'/'none' with classList in home.js
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace(
    "modal.style.display = 'flex';\n                modal.classList.add('is-active');",
    "modal.classList.add('is-active');"
)

content = content.replace(
    "modal.style.display = 'none';\n                modal.classList.remove('is-active');",
    "modal.classList.remove('is-active');"
)

# Catch any remaining direct style.display assignments
content = content.replace("modal.style.display = 'flex';", "modal.classList.add('is-active');")
content = content.replace("modal.style.display = 'none';", "modal.classList.remove('is-active');")

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("home.js: Replaced style.display with classList toggles")
