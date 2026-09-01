import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Extract Curated Collection
match = re.search(r'(<!-- Most Coveted Journeys -->.*?</section>)', content, re.DOTALL)
if match:
    curated_section = match.group(1)
    # Remove it from its current position
    content = content.replace(curated_section, '')
else:
    print("Could not find Most Coveted Journeys section.")
    exit(1)

# Fix watermark size for mobile
curated_section = curated_section.replace(
    '<div class="watermark" style="position: absolute; z-index: 1;">',
    '<div class="watermark" style="position: absolute; z-index: 1; font-size: 15vw !important; white-space: nowrap; top: 5%; left: 50%; transform: translateX(-50%); letter-spacing: 0.1em; opacity: 0.05;">'
)

# 2. Extract and remove The Art of Leisure
match = re.search(r'(<!-- 2\. The Art of Leisure -->.*?</section>)', content, re.DOTALL)
if match:
    art_of_leisure = match.group(1)
    # Replace The Art of Leisure with Curated Collection
    content = content.replace(art_of_leisure, curated_section)
else:
    print("Could not find The Art of Leisure section.")
    exit(1)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updates applied successfully.")
