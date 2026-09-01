import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find each orphan start and the next orphan or end
orphan_starts = []
for i, line in enumerate(lines):
    if 'Extracted from custom-typo-showcase' in line or 'Extracted from sikkim-static-backup' in line:
        orphan_starts.append(i)

# Determine ends: look for next /* Extracted from ... */ comment or end of file
extracted_markers = []
for i, line in enumerate(lines):
    if '/* Extracted from' in line and ('custom-typo-showcase' not in line) and ('sikkim-static-backup' not in line):
        extracted_markers.append(i)
    elif '/* Staggered' in line or '/* Marquee' in line or '/* ----' in line:
        extracted_markers.append(i)

# Print ranges
for s in orphan_starts:
    # find next section after s
    nexts = [m for m in range(len(lines)) if m > s and (
        '/* Extracted from' in lines[m] and 'sikkim-static-backup' not in lines[m] and 'custom-typo-showcase' not in lines[m]
        or lines[m].strip().startswith('/* Staggered')
        or lines[m].strip().startswith('/* Marquee')
        or lines[m].strip().startswith('/* ----')
    )]
    end = nexts[0] if nexts else len(lines)
    print(f'Orphan block: lines {s+1} to {end} ({end - s} lines)')
