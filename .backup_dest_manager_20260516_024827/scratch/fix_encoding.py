import os

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

# Read with latin-1 to capture the exact bytes causing the issue
with open(file_path, 'r', encoding='latin-1') as f:
    content = f.read()

# Replace the common encoding artifacts
# The artifact 'â' followed by space or other symbols
content = content.replace('â\x80\x94', '&mdash;')
content = content.replace('â\x9c¦', '✦') # Fix diamond
content = content.replace('â\x82\xb9', '&#8377;') # Fix Rupee symbol
content = content.replace('â\x86\x92', '&rarr;') # Fix arrows
content = content.replace('â', '') # Final sweep for stray â

# Specifically target the placeholders and prices we saw in screenshots
content = content.replace('✦', '&#10022;') 

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Encoding rectification complete.")
