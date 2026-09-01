import re

fpath = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'

with open(fpath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update the CSS for the icon
css_old = """    .glass-acc-chevron {
        color: rgba(255, 255, 255, 0.6);
        transition: transform 0.3s ease;
    }
    .glass-acc-item.active .glass-acc-chevron {
        transform: rotate(180deg);
        color: var(--gold);
    }"""
css_new = """    .glass-acc-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(197,160,89,0.15);
        border: 1px solid rgba(197,160,89,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        transition: all 0.3s ease;
    }
    .glass-acc-item.active .glass-acc-icon {
        background: var(--gold);
        color: #000;
    }
    .glass-acc-item.active .icon-plus {
        display: none !important;
    }
    .glass-acc-item.active .icon-minus {
        display: block !important;
    }"""

content = content.replace(css_old, css_new)

# 2. Update the HTML for the icon and text structure
html_old = """                            <span class="glass-acc-chevron">
                                <svg style="width:24px;height:24px;fill:currentColor;" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                            </span>"""
html_new = """                            <span class="glass-acc-icon">
                                <svg class="icon-plus" style="width:18px;height:18px;fill:currentColor;display:block;" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                <svg class="icon-minus" style="width:18px;height:18px;fill:currentColor;display:none;" viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/></svg>
                            </span>"""
content = content.replace(html_old, html_new)

# 3. Update the text section styling to match pic1 (which uses h3 and p with specific font styling)
# In package-detail.php, we have glass-acc-title and glass-acc-subtitle.
# Let's adjust their CSS to match package.php text styling perfectly.
title_css_old = """    .glass-acc-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        color: white;
        font-weight: 600;
    }
    .glass-acc-subtitle {
        font-size: 0.85rem;
        color: #94a3b8;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }"""
title_css_new = """    .glass-acc-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.125rem; /* text-lg */
        color: white;
        font-weight: 700; /* font-bold */
    }
    .glass-acc-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 0.875rem; /* text-sm */
        color: #c4c7c5; /* text-on-surface-variant */
        margin-top: 0.1rem;
    }"""
content = content.replace(title_css_old, title_css_new)


with open(fpath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patch applied to package-detail.php")
