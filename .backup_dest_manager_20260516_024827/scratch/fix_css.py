path = 'g:/Antigravity/leisure_loop_site/public/css/style.css'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

missing_css = """
.packages-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 80px;
    position: relative;
    z-index: 10;
}

.section-label-gold {
    font-size: 0.7rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 15px;
    display: block;
}

.packages-carousel {
    display: flex;
    gap: 32px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 40px;
    position: relative;
    z-index: 10;
}
.packages-carousel::-webkit-scrollbar { display: none; }
"""

marker = '/* ─── PACKAGE CARDS (8:48 PM + OVERLAY STYLE) ─── */'
if marker in content:
    new_content = content.replace(marker, marker + missing_css)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
