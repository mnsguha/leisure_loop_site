import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# --- Fix 1: Remove data-focus eval from heroDateInput ---
content = content.replace(
    ' data-focus="eval:(this.type=\'date\')"',
    ''
)

# --- Fix 2: Remove style="display: none;" from step2Modal ---
content = content.replace(
    '<div id="step2Modal" class="modal-overlay" style="display: none;">',
    '<div id="step2Modal" class="modal-overlay">'
)

# --- Fix 3a: Replace mountainBg inline style with class reference ---
content = content.replace(
    '<img src="images/parallax/vector_mountains.png" id="mountainBg" alt="Mountains" style="position: absolute; bottom: -5%; left: 0; width: 100%; height: auto; min-height: 40%; object-fit: cover; object-position: top; z-index: 0; pointer-events: none; opacity: 0.15; filter: grayscale(1) brightness(0.5);">',
    '<img src="images/parallax/vector_mountains.png" id="mountainBg" alt="Mountains" class="parallax-mountain">'
)

# --- Fix 3b: Replace silkContours inline style with class reference ---
content = content.replace(
    '<div id="silkContours" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; opacity: 0.3; overflow: hidden;">',
    '<div id="silkContours" class="silk-contours-layer">'
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('index.php: All 4 fixes applied.')
print('  - Removed data-focus eval from heroDateInput')
print('  - Removed style="display: none;" from step2Modal')
print('  - mountainBg: inline style -> .parallax-mountain class')
print('  - silkContours: inline style -> .silk-contours-layer class')
