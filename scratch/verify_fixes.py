# Verify both fixes
print("=== Checking index.php for remaining themeMandala inline style ===")
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    for i, line in enumerate(f):
        if 'themeMandala' in line:
            print(f"  Line {i+1}: {line.strip()[:120]}")

print()
print("=== Checking home.js for remaining style.display ===")
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    for i, line in enumerate(f):
        if 'style.display' in line:
            print(f"  Line {i+1}: {line.strip()}")

print("Done.")
