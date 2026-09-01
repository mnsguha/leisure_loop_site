path = 'g:/Antigravity/leisure_loop_site/public/css/style.css'
new_css = """
/* ─── PACKAGE CARDS (CAROUSEL) ─── */
.packages-section {
    padding: 120px 0 80px;
    background: #080808;
    position: relative;
    overflow: hidden;
}

.packages-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 60px;
    flex-wrap: wrap;
    gap: 30px;
    position: relative;
    z-index: 2;
}

.packages-carousel {
    display: flex;
    gap: 24px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: 40px;
    scrollbar-width: none;
    position: relative;
    z-index: 2;
}

.packages-carousel::-webkit-scrollbar { display: none; }

.package-card {
    flex: 0 0 320px;
    background: #111;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease;
    border: 1px solid rgba(255,255,255,0.05);
    scroll-snap-align: start;
    height: 100%;
}

.package-card:hover { transform: translateY(-10px); }

.pkg-img {
    position: relative;
    height: 400px;
    background-size: cover;
    background-position: center;
    background-color: #222;
}

.pkg-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(transparent 40%, rgba(0,0,0,0.9) 100%);
}

.pkg-info {
    position: absolute;
    bottom: 0;
    left: 0;
    padding: 24px;
    width: 100%;
}

.pkg-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 10px;
    display: block;
    line-height: 1.3;
    font-family: 'Playfair Display', serif;
}

.pkg-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: #C5A059;
    background: rgba(0,0,0,0.6);
    padding: 6px 12px;
    border-radius: 4px;
    border: 1px solid rgba(197,160,89,0.3);
}

.pkg-footer {
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #111;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.price-from {
    display: block;
    font-size: 0.8rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 4px;
}

.pkg-price-val {
    font-size: 1.35rem;
    font-weight: 700;
    color: #fff;
}

.pkg-btn {
    width: 48px;
    height: 48px;
    background: #739d1b;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.pkg-btn:hover { background: #86b81f; transform: scale(1.1); }
"""
with open(path, 'a', encoding='utf-8') as f:
    f.write(new_css)
