path = 'g:/Antigravity/leisure_loop_site/public/css/style.css'

# Remove the previous package card CSS (which was appended at the end)
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# We'll just replace the entire block we added earlier
# Or we'll just overwrite it by finding the comment
if '/* ─── PACKAGE CARDS (CAROUSEL) ─── */' in content:
    content = content.split('/* ─── PACKAGE CARDS (CAROUSEL) ─── */')[0]

new_css = """
/* ─── PACKAGE CARDS (QUIET LUXURY 8:48 PM) ─── */
.packages-section {
    padding: 100px 0;
    background: #080808;
    position: relative;
    overflow: hidden;
}

.packages-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 60px;
    position: relative;
    z-index: 2;
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
    gap: 30px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 30px;
}
.packages-carousel::-webkit-scrollbar { display: none; }

.package-card {
    flex: 0 0 320px;
    background: #0d0d0d;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.package-card:hover {
    transform: translateY(-10px);
    border-color: rgba(197,160,89,0.3);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.pkg-img {
    height: 200px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.pkg-content {
    padding: 30px;
}

.pkg-badge {
    display: inline-block;
    padding: 5px 12px;
    background: rgba(197,160,89,0.05);
    border: 1px solid rgba(197,160,89,0.2);
    color: var(--gold);
    font-size: 0.65rem;
    border-radius: 20px;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.pkg-dest {
    display: block;
    font-size: 0.65rem;
    color: var(--gold);
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.pkg-title {
    font-size: 1.3rem;
    font-family: 'Playfair Display', serif;
    color: #fff;
    line-height: 1.4;
    margin-bottom: 25px;
    font-weight: 400;
}

.pkg-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding-top: 20px;
}

.pkg-price {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
    letter-spacing: 0.05em;
}

.pkg-price b {
    color: #fff;
    font-size: 1.2rem;
    margin-left: 8px;
    font-family: 'Inter', sans-serif;
}

.pkg-btn {
    width: 36px;
    height: 36px;
    background: var(--gold);
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.pkg-btn:hover {
    transform: scale(1.1);
    background: #fff;
}

.watermark {
    position: absolute;
    top: 50px;
    left: 0;
    font-size: 12rem;
    font-weight: 900;
    color: rgba(255,255,255,0.02);
    white-space: nowrap;
    pointer-events: none;
    text-transform: uppercase;
    z-index: 1;
    font-family: 'Inter', sans-serif;
}
"""

with open(path, 'w', encoding='utf-8') as f:
    f.write(content + new_css)
