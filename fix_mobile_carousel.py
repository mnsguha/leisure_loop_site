import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the watermark
content = content.replace(
    '<div class="watermark" style="position: absolute; z-index: 1; font-size: 15vw !important; white-space: nowrap; top: 5%; left: 50%; transform: translateX(-50%); letter-spacing: 0.1em; opacity: 0.05;">Most Coveted</div>',
    '<div class="mobile-watermark" style="position: absolute; z-index: 1; font-size: 16vw; font-weight: 900; white-space: nowrap; top: 10%; left: 50%; transform: translateX(-50%); letter-spacing: 0.1em; color: rgba(255, 255, 255, 0.06); pointer-events: none; text-transform: uppercase;">Most Coveted</div>'
)

# 2. Remove the vector_mountains.png
content = re.sub(r'<!-- Minimalist Vector Mountains -->\s*<img[^>]*vector_mountains\.png[^>]*>', '', content)

# 3. Remove nav-arrows (Previous and Next buttons)
content = re.sub(r'<button class="nav-arrow"[^>]*>&larr;</button>', '', content)
content = re.sub(r'<button class="nav-arrow"[^>]*>&rarr;</button>', '', content)

# 4. Write back
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updates applied successfully.")
