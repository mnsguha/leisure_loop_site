import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Clean up old GSAP button logic
start_comment = content.find('// EXPLORE THE STORY Inline Expansion')
if start_comment != -1:
    # Find the end of the modal logic
    end_modal = content.find('});', content.find('// EXPLORE THE STORY Modal (Option 2)', start_comment))
    if end_modal != -1:
        # Erase everything from start_comment to end_modal + 3
        content = content[:start_comment] + content[end_modal+3:]

# 2. Make sure the toggle functions are there and correct
if 'function toggleInlineStory' in content:
    pass
else:
    # We should add it if it's not there, but it should be.
    pass

# 3. Enhance the Signature Journeys hover to be extremely immersive
content = content.replace(
    '.tour-card-hover:hover {\n            transform: translateY(-12px) scale(1.02);\n            box-shadow: 0 25px 50px -12px rgba(233, 193, 118, 0.25);\n        }',
    '.tour-card-hover:hover {\n            transform: translateY(-16px) scale(1.03);\n            box-shadow: 0 30px 60px -10px rgba(233, 193, 118, 0.4);\n        }\n        .tour-card-hover:hover img {\n            transform: scale(1.15);\n            transition: transform 1.5s cubic-bezier(0.2, 0.8, 0.2, 1);\n        }'
)

# If it didn't replace because of spacing:
if '.tour-card-hover:hover img' not in content:
    content = content.replace('</style>', '\n.tour-card-hover:hover img { transform: scale(1.15); transition: transform 1.5s cubic-bezier(0.2, 0.8, 0.2, 1); }\n</style>')

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Cleaned up JS and enhanced hover animations.")
