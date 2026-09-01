import re

with open('hotels.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_css = """<style>
body { background-color: #f4f7f6; color: #333; font-family: 'Inter', sans-serif; }
.hotel-hero {
    padding: 180px 0 100px;
    background: linear-gradient(rgba(5, 10, 20, 0.7), rgba(5, 10, 20, 0.9)), url('https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070') center/cover;
    text-align: center;
}
.hotel-hero h1 {
    font-family: 'Playfair Display', serif; font-size: 4rem; color: var(--gold, #C5A059); margin: 0 0 20px; line-height: 1.1;
}
.section-container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
.section-title { font-family: 'Inter', sans-serif; font-size: 2rem; font-weight: 700; color: #1a365d; margin-bottom: 30px; }

@media (min-width: 900px) {
    .hotel-list { display: flex; flex-direction: column; gap: 30px; }
    .hotel-card {
        display: flex;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        transition: box-shadow 0.3s ease;
        height: 360px; /* Identical fixed height for all cards */
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .hotel-gallery-col { width: 320px; flex-shrink: 0; display: flex; flex-direction: column; }
    .hotel-info-col { flex: 1; padding: 25px 35px; display: flex; flex-direction: column; justify-content: space-between; }
}

@media (max-width: 899px) {
    .hotel-list { display: flex; flex-direction: column; gap: 20px; }
    .hotel-card {
        display: flex; flex-direction: column;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
    }
    .hotel-gallery-col { width: 100%; display: flex; flex-direction: column; }
    .hotel-info-col { width: 100%; padding: 20px; display: flex; flex-direction: column; }
}

.hotel-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.main-img-wrap { flex: 1; position: relative; }
.main-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hotel-badge {
    position: absolute; top: 12px; left: 12px;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    color: #fff; padding: 4px 10px; border-radius: 4px;
    font-size: 0.8rem; font-weight: 600;
}

.thumb-row { display: flex; height: 80px; flex-shrink: 0; }
.thumb-wrap { flex: 1; position: relative; border-right: 2px solid #fff; }
.thumb-wrap:last-child { border-right: none; }
.thumb-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.thumb-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 0.9rem; font-weight: 600;
}

.hotel-name { font-family: 'Inter', sans-serif; font-size: 1.6rem; font-weight: 700; color: #1a365d; margin: 0 0 8px; line-height: 1.2; }
.hotel-location { color: #718096; font-size: 0.9rem; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
.hotel-desc {
    color: #4a5568; font-size: 0.9rem; line-height: 1.6; margin: 0;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}

.read-more-btn { color: #d69e2e; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-block; margin-top: 5px; margin-bottom: 12px; }
.read-more-btn:hover { text-decoration: underline; }

.amenities-section { margin-bottom: auto; margin-top: 10px; }
.amenities-title { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; color: #1a365d; text-transform: uppercase; margin-bottom: 12px; }
.amenities-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px 15px; }
.amenity-item { display: flex; align-items: center; gap: 8px; color: #4a5568; font-size: 0.85rem; }
.amenity-item svg { color: #a0aec0; width: 16px; height: 16px; flex-shrink: 0; }

.view-more-btn { color: #d69e2e; cursor: pointer; font-size: 0.85rem; font-weight: 600; margin-top: 10px; display: inline-block; text-align: left; }
.view-more-btn:hover { text-decoration: underline; }

.hotel-footer {
    display: flex; justify-content: flex-end; align-items: center;
    border-top: 1px solid #edf2f7; padding-top: 15px; margin-top: 15px;
}

/* Action Buttons */
.action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-outline { padding: 10px 20px; border: 1px solid #d69e2e; color: #d69e2e; border-radius: 6px; font-weight: 600; font-size: 0.9rem; transition: 0.2s; background: transparent; }
.btn-outline:hover { background: #fffaf0; }
.btn-solid { padding: 10px 20px; background: #d69e2e; color: #fff; border-radius: 6px; font-weight: 600; font-size: 0.9rem; transition: 0.2s; border: 1px solid #d69e2e; }
.btn-solid:hover { background: #b7791f; border-color: #b7791f; }

/* Info Modal */
.info-modal {
    position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 10000;
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.info-modal-content {
    background: #fff; border-radius: 12px; width: 100%; max-width: 600px; max-height: 85vh;
    padding: 30px; position: relative; overflow-y: auto; color: #333;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
}
.info-modal-close {
    position: absolute; top: 15px; right: 20px; font-size: 1.8rem; cursor: pointer;
    color: #a0aec0; background: transparent; width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; line-height: 1; transition: 0.2s;
}
.info-modal-close:hover { background: #edf2f7; color: #4a5568; }
.info-modal-title { font-family: 'Inter', sans-serif; font-size: 1.6rem; font-weight: 700; color: #1a365d; margin: 0 0 20px 0; padding-right: 40px; border-bottom: 1px solid #edf2f7; padding-bottom: 15px; }
.info-modal-subtitle { font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; color: #1a365d; text-transform: uppercase; margin-bottom: 15px; }
.info-modal-body { font-size: 0.95rem; line-height: 1.6; color: #4a5568; }

.modal-amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
.modal-amenity-item { display: flex; align-items: center; gap: 10px; color: #4a5568; font-size: 0.9rem;}
.modal-amenity-item svg { color: #a0aec0; width: 18px; height: 18px; flex-shrink: 0; }
</style>"""

content = re.sub(r'<style>.*?</style>', new_css, content, flags=re.DOTALL)

# In pic2 the price is not there (or we can hide it for a cleaner look).
# Or we can just keep the price. Wait, the user said "all the hotels cards details should be identital in size and also make it more sleek like pic2".
# Let's remove the inline style in the luxury section that sets color to #fff for prices, so that it matches the light theme.
content = content.replace('style="color: #fff;"', '')
content = content.replace('style="background: rgba(255,255,255,0.1); color: #fff;"', '')
content = content.replace('style="border-color: rgba(255,255,255,0.3); color: #fff;"', '')
content = content.replace('style="background: rgba(255,255,255,0.9);"', '')

# Also change "Amenities" to "WE OFFER" to match pic2
content = content.replace('<div class="amenities-title">Amenities</div>', '<div class="amenities-title">WE OFFER</div>')

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Sleek identical-size CSS applied!")
