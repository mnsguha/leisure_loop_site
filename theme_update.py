import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update tailwind config
content = content.replace('"surface": "#fcf9f8"', '"surface": "#050505"')
content = content.replace('"on-surface": "#1c1b1b"', '"on-surface": "#ffffff"')
content = content.replace('"on-surface-variant": "#424654"', '"on-surface-variant": "#9a9a9a"')
content = content.replace('"azure-deep": "#0056D2"', '"azure-deep": "#C5A059"')
content = content.replace('"surface-container-low": "#f6f3f2"', '"surface-container-low": "#0a0a0a"')
content = content.replace('"outline-variant": "#c3c6d6"', '"outline-variant": "rgba(255, 255, 255, 0.08)"')
content = content.replace('"outline": "#737785"', '"outline": "#888888"')

# 2. Update Fonts
content = content.replace('"headline-lg-mobile": ["Montserrat"]', '"headline-lg-mobile": ["Playfair Display", "serif"]')
content = content.replace('"headline-md": ["Montserrat"]', '"headline-md": ["Playfair Display", "serif"]')
content = content.replace('"headline-lg": ["Montserrat"]', '"headline-lg": ["Playfair Display", "serif"]')

# 3. HTML bg-white replacements
content = content.replace('bg-white/90', 'bg-[#0a0a0a]/90')
content = content.replace('bg-white p-6', 'bg-[#0a0a0a] border border-outline-variant p-6')
content = content.replace('bg-white p-5', 'bg-[#0a0a0a] border border-outline-variant p-5')
content = content.replace('bg-white text-on-surface', 'bg-[#050505] text-on-surface')
content = content.replace('bg-white overflow-hidden', 'bg-[#050505] overflow-hidden')
content = content.replace('bg-white w-[280px]', 'bg-[#0a0a0a] w-[280px]')
content = content.replace('bg-white', 'bg-[#0a0a0a]')

# Ensure text is light
content = content.replace('text-on-surface', 'text-white')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Theme updated successfully.")
