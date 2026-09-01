import sys, io, re, os
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

includes = 'G:/Antigravity/leisure_loop_site/includes'
fixes = 0

for fname in os.listdir(includes):
    if not fname.endswith('.php'):
        continue
    fpath = f'{includes}/{fname}'
    with open(fpath, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()
    original = content

    # Fix 1: overlay divs used as close trigger - add aria-label and role
    content = re.sub(
        r'(<div class="modal-overlay"[^>]*data-action="close-modal"[^>]*)(>)',
        lambda m: m.group(1) + ' role="button" aria-label="Close modal"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    content = re.sub(
        r'(<div class="search-modal-overlay"[^>]*data-action="close-[^"]*"[^>]*)(>)',
        lambda m: m.group(1) + ' role="button" aria-label="Close"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    # Fix the modal-overlay with id=modalOverlay (destinations/packages)
    content = re.sub(
        r'(<div class="modal-overlay"[^>]*data-action="close-all"[^>]*)(>)',
        lambda m: m.group(1) + ' role="button" aria-label="Close filter panel"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )

    # Fix 2: close buttons without aria-label
    content = re.sub(
        r'(<button data-action="close-modal"[^>]*)(>)',
        lambda m: m.group(1) + ' aria-label="Close modal"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    content = re.sub(
        r'(<button data-action="close-all"[^>]*)(>)',
        lambda m: m.group(1) + ' aria-label="Close filter panel"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    content = re.sub(
        r'(<button data-action="apply-filters-close"[^>]*)(>)',
        lambda m: m.group(1) + ' aria-label="Apply filters and close"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    content = re.sub(
        r'(<button data-action="close-location"[^>]*)(>)',
        lambda m: m.group(1) + ' aria-label="Close location picker"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    # mobile_bottom_nav close-mobile-menu
    content = re.sub(
        r'(<button data-action="close-mobile-menu"[^>]*)(>)',
        lambda m: m.group(1) + ' aria-label="Close menu"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )
    # mobile_bottom_nav mobileMenuOverlay overlay div
    content = re.sub(
        r'(<div id="mobileMenuOverlay"[^>]*)(>)',
        lambda m: m.group(1) + ' role="button" aria-label="Close menu"' + m.group(2)
        if 'aria-label' not in m.group(0) else m.group(0),
        content
    )

    # Fix 3: mobile package_details modal-close button without aria-label
    content = content.replace(
        '<button class="modal-close" data-action="close-modal">',
        '<button class="modal-close" data-action="close-modal" aria-label="Close modal">'
    )

    if content != original:
        with open(fpath, 'w', encoding='utf-8', errors='replace') as f:
            f.write(content)
        fixes += 1
        print(f'  Fixed: {fname}')

print(f'Total files updated: {fixes}')
