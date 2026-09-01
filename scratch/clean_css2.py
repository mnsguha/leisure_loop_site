import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# ---- Step 3: Replace blunt pointer-events/z-index !important block ----
# Replace the gross 'ensure interactive controls have top stacking' block with a clean z-index layer approach
old_block = re.search(
    r'/\* Ensure all interactive controls have top stacking priority \*/'
    r'.*?'
    r'\[data-action\] \{\s*pointer-events: auto !important;\s*position: relative;\s*z-index: 50 !important;\s*\}',
    content, re.DOTALL
)

if old_block:
    new_block = '''/* Z-Index Layer System */
.flight-anim,
.globe-container,
.canvas-wrapper,
.parallax-layer,
.hero-bg,
.hero-clouds,
.hero-mountain,
svg.world-globe,
#airplane-tracker {
    pointer-events: none;
    z-index: 0;
}

.nav-pills,
.carousel-arrow,
.btn,
.tab-btn,
.floating-action,
.concierge-dock,
[data-action] {
    position: relative;
    z-index: 10;
}'''
    content = content[:old_block.start()] + new_block + content[old_block.end():]
    print('Replaced pointer-events block.')
else:
    print('Pointer-events block not found to replace.')

# ---- Step 4: Fix badly indented CSS (strip leading whitespace > 4 spaces per line) ----
lines = content.splitlines()
cleaned = []
for line in lines:
    stripped = line.lstrip()
    if stripped == '':
        cleaned.append('')
    else:
        # Normalize indentation: count original leading spaces, cap at appropriate levels
        original_indent = len(line) - len(line.lstrip(' '))
        # Keep block-level rules at 0, properties at 4
        if stripped.startswith('.') or stripped.startswith('#') or stripped.startswith('@') or stripped.startswith(':') or stripped.startswith('/*') or stripped.startswith('*'):
            # Likely a selector or comment - 0 indent
            cleaned.append(stripped)
        elif stripped.endswith('{') and not stripped.startswith('{'):
            cleaned.append(stripped)
        elif stripped == '}':
            cleaned.append('}')
        else:
            # Property - normalize to 4 spaces
            cleaned.append('    ' + stripped)
content = '\n'.join(cleaned)

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(content)
print('Indentation normalized.')
