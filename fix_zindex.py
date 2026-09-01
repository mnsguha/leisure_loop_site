import re

filepath = 'public/destinations.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Find the start of the Narrative section
narrative_start = content.find('<!-- The Narrative (Editorial Section) -->')

# Find the end of the footer
footer_end = content.find('</footer>') + len('</footer>')

# Extract the content to wrap
block_to_wrap = content[narrative_start:footer_end]

# Wrap it in the z-index wrapper
wrapped_block = f'<div class="relative z-10 bg-[#030811]">\n{block_to_wrap}\n</div>'

# Replace in content
new_content = content[:narrative_start] + wrapped_block + content[footer_end:]

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Wrapped sections in z-10 div")
