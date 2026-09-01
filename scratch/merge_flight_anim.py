import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Block 1 has pointer-events: none !important; (old blunt version)
# Block 2 has pointer-events: none; z-index: 0; (new clean version)
# Merge: remove block 1 entirely, keep block 2 as canonical

old_block1 = '''.flight-anim, \n.globe-container, \n.canvas-wrapper, \n.parallax-layer, \n.hero-bg, \n.hero-clouds, \n.hero-mountain,\n    svg.world-globe,\n#airplane-tracker {\n    pointer-events: none !important;\n}'''

content = content.replace(old_block1, '')

# Also remove any stray blank lines that might result
content = re.sub(r'\n{3,}', '\n\n', content)

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(content)

# Verify
remaining = len(re.findall(r'\.flight-anim', content))
print(f'flight-anim occurrences after merge: {remaining} (expected: 1)')
print('home.css: Duplicate flight-anim block removed.')
