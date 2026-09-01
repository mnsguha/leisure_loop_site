import os

path = 'g:/Antigravity/leisure_loop_site/public/css/style.css'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace .inquiry-card with .hero-card in the form styling section
# Only in the specific section related to the form (around lines 190-250)

# To be safe, just replace .inquiry-card with .hero-card globally, as there's no inquiry-card in index.php
content = content.replace('.inquiry-card', '.hero-card')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("CSS targeting fixed.")
