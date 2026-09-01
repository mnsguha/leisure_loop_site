import re

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

start_idx = content.find('function initAnimations() {')
end_idx = content.find('function bootHomePage() {', start_idx)

body = content[start_idx:end_idx]

# Let's write the body to a scratch file so I can easily analyze and refactor it
with open('G:\\Antigravity\\leisure_loop_site\\scratch\\init_body.js', 'w', encoding='utf-8') as f:
    f.write(body)
