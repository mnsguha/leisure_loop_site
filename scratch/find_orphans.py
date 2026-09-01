with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find the line range of the custom-typo-showcase and sikkim-static-backup sections
for i, line in enumerate(lines):
    if 'custom-typo-showcase' in line or 'sikkim-static-backup' in line:
        print(f"Line {i+1}: {line.strip()}")
