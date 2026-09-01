import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Remove pointer-events-auto from the story-modal-content
content = content.replace('pointer-events-auto', '')

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Removed pointer-events-auto from the modal content.")
