import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the margin: 0 auto with margin: 0 0 2rem 0
old_p = '<p style="color: var(--text-muted); font-size: 1.15rem; max-width: 700px; margin: 0 auto;">'
new_p = '<p style="color: var(--text-muted); font-size: 1.15rem; max-width: 700px; margin: 0 0 2rem 0;">'

if old_p in content:
    content = content.replace(old_p, new_p)
    with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully left-aligned the paragraph.")
else:
    print("Could not find the paragraph HTML.")
