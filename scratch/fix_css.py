import re

css_path = 'G:/Antigravity/leisure_loop_site/public/css/all-tours.css'
with open(css_path, 'r', encoding='utf-8') as f:
    css = f.read()

# We need to find `.package-switcher-tab {` and replace everything down to `.dock-icon-circle {`
start_idx = css.find('.package-switcher-tab {')
end_idx = css.find('.dock-icon-circle {')

if start_idx != -1 and end_idx != -1:
    correct_css = """.package-switcher-tab {
    padding: 11px 32px;
    border-radius: 99px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    font-size: 0.92rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.package-switcher-tab.active {
    background: var(--gold);
    color: #050a14;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(197, 160, 89, 0.4);
}

.package-switcher-tab.inactive {
    color: rgba(255, 255, 255, 0.75);
    background: transparent;
}

.package-switcher-tab.inactive:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
}

/* 5. Main Catalog Layout: Full-Width Grid */
.catalog-section {
    padding: 1rem 0 5rem;
}

.packages-catalog-layout {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 35px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 35px;
    align-items: start;
}

/* Empty State Notification */
.catalog-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 20px;
    background: rgba(15, 24, 42, 0.4);
    border: 1px dashed rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    margin-top: 20px;
}

.catalog-empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 20px;
    opacity: 0.8;
}

.empty-state-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 10px;
}

.empty-state-sub {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.6);
    max-width: 400px;
    margin: 0 0 25px;
    line-height: 1.5;
}

.empty-state-actions {
    display: flex;
    gap: 15px;
}

.btn-reset-empty {
    background: transparent;
    border: 1px solid var(--gold);
    color: var(--gold);
    padding: 10px 24px;
    border-radius: 50px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-reset-empty:hover {
    background: var(--gold);
    color: #050a14;
}

/* ── Floating Filter & Sorting Pill (always visible, desktop + mobile) ── */
.floating-filter-dock {
    position: fixed;
    left: 25px;
    bottom: 25px;
    z-index: 900;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-height: 48px;
    background: rgba(13, 22, 38, 0.95);
    backdrop-filter: blur(16px);
    border: 1.5px solid var(--gold);
    border-radius: 50px;
    padding: 11px 24px 11px 16px;
    color: #ffffff;
    font-family: 'Inter', sans-serif;
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    cursor: pointer;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.65), 0 0 20px rgba(197, 160, 89, 0.35);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}

.floating-filter-dock:hover {
    transform: translateY(-4px) scale(1.02);
    background: linear-gradient(145deg, rgba(20, 32, 55, 0.98), rgba(10, 18, 32, 0.98));
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.8), 0 0 30px rgba(197, 160, 89, 0.6);
}

"""
    new_css = css[:start_idx] + correct_css + css[end_idx:]
    with open(css_path, 'w', encoding='utf-8') as f:
        f.write(new_css)
    print("Fixed CSS file.")
else:
    print("Could not find start or end index.")
