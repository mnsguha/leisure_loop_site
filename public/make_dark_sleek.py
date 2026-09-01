import re

with open('hotels.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_css = """<style>
body { background-color: #050a14; color: #e5e2e2; font-family: 'Inter', sans-serif; }
.hotel-hero {
    padding: 180px 0 100px;
    background: linear-gradient(rgba(5, 10, 20, 0.7), rgba(5, 10, 20, 0.9)), url('https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070') center/cover;
    text-align: center;
}
.hotel-hero h1 {
    font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--gold, #C5A059); margin: 0 0 20px; line-height: 1.1;
}
.section-container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
.section-title { font-family: 'Playfair Display', serif; font-size: 2.2rem; color: #fff; margin-bottom: 30px; }

@media (min-width: 900px) {
    .hotel-list { display: flex; flex-direction: column; gap: 30px; }
    .hotel-card {
        display: flex;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 290px; /* Identical fixed height for all cards, highly sleek */
    }
    .hotel-gallery-col { width: 320px; flex-shrink: 0; display: flex; flex-direction: column; }
    .hotel-info-col { flex: 1; padding: 20px 30px; display: flex; flex-direction: column; justify-content: space-between; }
}

@media (max-width: 899px) {
    .hotel-list { display: flex; flex-direction: column; gap: 20px; }
    .hotel-card {
        display: flex; flex-direction: column;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
    }
    .hotel-gallery-col { width: 100%; display: flex; flex-direction: column; }
    .hotel-info-col { width: 100%; padding: 20px; display: flex; flex-direction: column; }
}

.hotel-card:hover {
    border-color: rgba(197, 160, 89, 0.4);
    background: rgba(255, 255, 255, 0.04);
}

.main-img-wrap { flex: 1; position: relative; }
.main-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hotel-badge {
    position: absolute; top: 10px; left: 10px;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    color: var(--gold); padding: 4px 10px; border-radius: 4px;
    font-size: 0.8rem; font-weight: 600;
}

.thumb-row { display: flex; height: 75px; flex-shrink: 0; }
.thumb-wrap { flex: 1; position: relative; border-right: 1px solid rgba(255,255,255,0.05); }
.thumb-wrap:last-child { border-right: none; }
.thumb-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.thumb-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 0.85rem; font-weight: 600;
}

.hotel-name { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0 0 8px; line-height: 1.2; }
.hotel-location { color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
.hotel-desc {
    color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.5; margin: 0;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.read-more-btn { color: var(--gold); font-size: 0.8rem; font-weight: 500; cursor: pointer; display: inline-block; margin-top: 5px; margin-bottom: 10px; }
.read-more-btn:hover { text-decoration: underline; }

.amenities-section { margin-bottom: auto; margin-top: 5px; }
.amenities-title { font-size: 0.8rem; font-weight: 600; letter-spacing: 1px; color: var(--gold); text-transform: uppercase; margin-bottom: 8px; }
.amenities-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px 15px; }
.amenity-item { display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); font-size: 0.85rem; }
.amenity-item svg { color: #28a745; width: 14px; height: 14px; flex-shrink: 0; }

.view-more-btn { color: var(--gold); cursor: pointer; font-size: 0.8rem; font-weight: 500; margin-top: 8px; display: inline-block; text-align: left; }
.view-more-btn:hover { text-decoration: underline; }

.hotel-footer {
    display: flex; justify-content: space-between; align-items: center;
    border-top: 1px solid rgba(255,255,255,0.05); padding-top: 12px; margin-top: 12px;
}

.price-label { font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-bottom: 2px; }
.price-value { font-size: 1.3rem; font-weight: 700; color: #fff; line-height: 1; }
.price-value span { font-size: 0.85rem; font-weight: 400; color: rgba(255,255,255,0.5); }

/* Action Buttons */
.action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-outline { padding: 8px 16px; border: 1px solid var(--gold); color: var(--gold); border-radius: 6px; font-weight: 500; font-size: 0.85rem; transition: 0.2s; background: transparent; }
.btn-outline:hover { background: rgba(197, 160, 89, 0.1); }
.btn-solid { padding: 8px 16px; background: var(--gold); color: #000; border-radius: 6px; font-weight: 600; font-size: 0.85rem; transition: 0.2s; border: 1px solid var(--gold); }
.btn-solid:hover { background: #b08d48; border-color: #b08d48; }

/* Info Modal */
.info-modal {
    position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 10000;
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.info-modal-content {
    background: #0f1626; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; width: 100%; max-width: 600px; max-height: 85vh;
    padding: 30px; position: relative; overflow-y: auto; color: #e5e2e2;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);
}
.info-modal-close {
    position: absolute; top: 15px; right: 20px; font-size: 1.8rem; cursor: pointer;
    color: #999; background: rgba(255,255,255,0.05); width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; line-height: 1; transition: 0.2s;
}
.info-modal-close:hover { background: rgba(255,255,255,0.1); color: #fff; }
.info-modal-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #fff; margin: 0 0 20px 0; padding-right: 40px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
.info-modal-subtitle { font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; color: var(--gold); text-transform: uppercase; margin-bottom: 15px; }
.info-modal-body { font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.8); }

.modal-amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
.modal-amenity-item { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.8); font-size: 0.9rem;}
.modal-amenity-item svg { color: #28a745; width: 18px; height: 18px; flex-shrink: 0; }
</style>"""

content = re.sub(r'<style>.*?</style>', new_css, content, flags=re.DOTALL)

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Dark Sleek CSS applied!")
