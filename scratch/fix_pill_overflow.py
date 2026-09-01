import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix 1: Remove inline style from themes-pills-container
content = content.replace(
    '<div class="themes-pills-container" style="overflow-x: auto; white-space: nowrap; padding-bottom: 1.5rem; margin-bottom: 3rem; scrollbar-width: none; -ms-overflow-style: none;">',
    '<div class="themes-pills-container">'
)

# Fix 2: Remove inline style from the inner flex wrapper
content = content.replace(
    '<div style="display: inline-flex; gap: 1.2rem; min-width: 100%;">',
    '<div class="themes-pills-inner">'
)

# Fix 3: Remove inline styles from .theme-pill anchor
content = content.replace(
    'class="theme-pill" style="display: flex; align-items: center; gap: 0.9rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(197,160,89,0.15); border-radius: 50px; padding: 0.6rem 1.6rem 0.6rem 0.6rem; text-decoration: none; transition: background 0.3s, border-color 0.3s;"',
    'class="theme-pill"'
)

# Fix 4: Remove inline style from .theme-pill-icon
content = content.replace(
    'class="theme-pill-icon" style="width: 38px; height: 38px; border-radius: 50%; background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); display: flex; align-items: center; justify-content: center; transition: all 0.3s;"',
    'class="theme-pill-icon"'
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('index.php: Removed inline styles from theme pills.')
