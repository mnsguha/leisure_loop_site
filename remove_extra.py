import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('}); // Close DOMContentLoaded\n    ', '')

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Removed extra closing tags.")
