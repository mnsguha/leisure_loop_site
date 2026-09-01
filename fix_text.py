import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the text of the inline button
content = content.replace(
    '<span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE INLINE</span>',
    '<span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (INLINE)</span>'
)

# Replace the text of the modal button
content = content.replace(
    '<span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE MODAL</span>',
    '<span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (MODAL)</span>'
)

# Replace the JS toggle text for inline
content = content.replace(
    'exploreText.textContent = "EXPLORE INLINE";',
    'exploreText.textContent = "EXPLORE THE STORY (INLINE)";'
)

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated button text successfully")
