import re

with open('hotels.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace CSS
css_old = r"""\.hotel-grid \{ display: grid; grid-template-columns: repeat\(auto-fill, minmax\(350px, 1fr\)\); gap: 40px; \}

\.hotel-card \{
    background: rgba\(20, 25, 35, 0\.6\); border: 1px solid rgba\(255, 255, 255, 0\.05\); border-radius: 16px; overflow: hidden; transition: all 0\.3s ease; text-decoration: none; display: block;
\}
\.hotel-card:hover \{ transform: translateY\(-5px\); border-color: rgba\(197, 160, 89, 0\.3\); box-shadow: 0 15px 40px rgba\(0,0,0,0\.4\); \}
\.hotel-img-wrapper \{ position: relative; width: 100%; height: 250px; \}
\.hotel-img \{ width: 100%; height: 100%; object-fit: cover; \}
\.hotel-badge \{ position: absolute; top: 15px; right: 15px; background: rgba\(0,0,0,0\.7\); backdrop-filter: blur\(10px\); color: var\(--gold\); padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0\.9rem; \}
\.hotel-info \{ padding: 25px; \}
\.hotel-name \{ font-family: 'Playfair Display', serif; font-size: 1\.5rem; color: #fff; margin: 0 0 10px; \}
\.hotel-location \{ color: rgba\(255,255,255,0\.6\); font-size: 0\.95rem; margin-bottom: 15px; display: flex; align-items: center; gap: 5px; \}
\.hotel-price-row \{ display: flex; justify-content: space-between; align-items: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba\(255,255,255,0\.05\); \}
\.hotel-price \{ color: var\(--gold\); font-size: 1\.4rem; font-weight: 700; \}
\.hotel-price span \{ font-size: 0\.85rem; color: rgba\(255,255,255,0\.5\); font-weight: 400; \}
\.btn-view \{ padding: 10px 20px; background: rgba\(197, 160, 89, 0\.1\); color: var\(--gold\); border: 1px solid var\(--gold\); border-radius: 8px; font-weight: 500; transition: all 0\.3s; \}
\.hotel-card:hover \.btn-view \{ background: var\(--gold\); color: #000; \}"""

css_new = """@media (min-width: 900px) {
    .hotel-list { display: flex; flex-direction: column; gap: 40px; }
    .hotel-card {
        display: flex;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .hotel-gallery-col { width: 40%; display: flex; flex-direction: column; border-right: 1px solid rgba(255,255,255,0.05); }
    .hotel-info-col { width: 60%; padding: 30px 40px; display: flex; flex-direction: column; }
}
@media (max-width: 899px) {
    .hotel-list { display: flex; flex-direction: column; gap: 30px; }
    .hotel-card {
        display: flex; flex-direction: column;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
    }
    .hotel-gallery-col { width: 100%; display: flex; flex-direction: column; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .hotel-info-col { width: 100%; padding: 25px; display: flex; flex-direction: column; }
}

.hotel-card:hover {
    border-color: rgba(197, 160, 89, 0.4);
    background: rgba(255, 255, 255, 0.04);
}

.main-img-wrap { height: 280px; position: relative; }
.main-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hotel-badge {
    position: absolute; top: 15px; left: 15px;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    color: var(--gold); padding: 5px 12px; border-radius: 6px;
    font-size: 0.85rem; font-weight: 600;
}
.thumb-row { display: flex; height: 90px; }
.thumb-wrap { flex: 1; position: relative; border-right: 1px solid rgba(255,255,255,0.05); }
.thumb-wrap:last-child { border-right: none; }
.thumb-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.thumb-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,0.7);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 0.95rem; font-weight: 500;
}

.hotel-name { font-family: 'Playfair Display', serif; font-size: 2rem; color: #fff; margin: 0 0 10px; line-height: 1.2; }
.hotel-location { color: rgba(255,255,255,0.6); font-size: 0.95rem; margin-bottom: 20px; display: flex; align-items: center; gap: 6px; }
.hotel-desc {
    color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.6; margin: 0 0 25px;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}

.amenities-section { margin-bottom: 30px; flex: 1; }
.amenities-title { font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; color: var(--gold); text-transform: uppercase; margin-bottom: 15px; }
.amenities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; }
.amenity-item { display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.8); font-size: 0.9rem; }
.amenity-item svg { color: #28a745; width: 16px; height: 16px; flex-shrink: 0; }

.hotel-footer {
    display: flex; justify-content: space-between; align-items: flex-end;
    border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;
    flex-wrap: wrap; gap: 15px;
}
.price-label { font-size: 0.85rem; color: rgba(255,255,255,0.5); margin-bottom: 5px; }
.price-value { font-size: 1.6rem; font-weight: 700; color: #fff; line-height: 1; }
.price-value span { font-size: 0.9rem; font-weight: 400; color: rgba(255,255,255,0.5); }

.action-buttons { display: flex; gap: 15px; flex-wrap: wrap; }
.btn-outline { padding: 12px 24px; border: 1px solid var(--gold); color: var(--gold); border-radius: 8px; font-weight: 500; transition: 0.3s; }
.btn-outline:hover { background: rgba(197, 160, 89, 0.1); }
.btn-solid { padding: 12px 24px; background: var(--gold); color: #000; border-radius: 8px; font-weight: 600; transition: 0.3s; }
.btn-solid:hover { background: #b08d48; }"""

content = re.sub(css_old, css_new, content)

html_signature_old = r"""    <div class="hotel-grid">
        <\?php foreach \(\$signature as \$hotel\): \?>
        <a href="hotel-detail\.php\?id=<\?php echo \$hotel\['id'\]; \?>" class="hotel-card">
            <div class="hotel-img-wrapper">
                <img src="<\?php echo htmlspecialchars\(\$hotel\['main_image'\]\); \?>" class="hotel-img" alt="<\?php echo htmlspecialchars\(\$hotel\['name'\]\); \?>">
                <div class="hotel-badge"><\?php echo \$hotel\['star_category'\]; \?> Star</div>
            </div>
            <div class="hotel-info">
                <h3 class="hotel-name"><\?php echo htmlspecialchars\(\$hotel\['name'\]\); \?></h3>
                <div class="hotel-location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <\?php echo htmlspecialchars\(\$hotel\['place'\]\); \?>
                </div>
                <p style="color: rgba\(255,255,255,0\.7\); font-size: 0\.95rem; line-height: 1\.5; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <\?php echo htmlspecialchars\(\$hotel\['description'\]\); \?>
                </p>
                <div class="hotel-price-row">
                    <div>
                        <div style="font-size: 0\.85rem; color: rgba\(255,255,255,0\.5\);">Starting from</div>
                        <div class="hotel-price">₹<\?php echo number_format\(\$hotel\['starting_tariff'\]\); \?> <span>/ night</span></div>
                    </div>
                    <div class="btn-view">Book Now</div>
                </div>
            </div>
        </a>
        <\?php endforeach; \?>
    </div>"""

html_signature_new = """    <div class="hotel-list">
        <?php foreach ($signature as $hotel): 
            $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$hotel['id']]);
            $thumbs = $stmt->fetchAll();
            $amenities = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
        ?>
        <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>" class="hotel-card">
            <div class="hotel-gallery-col">
                <div class="main-img-wrap">
                    <?php $main_img = preg_match('/^https?:\\/\\//i', $hotel['main_image']) ? $hotel['main_image'] : (strpos($hotel['main_image'], '../') === 0 ? ltrim($hotel['main_image'], '../') : $hotel['main_image']); ?>
                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                    <div class="hotel-badge"><?php echo $hotel['star_category']; ?> Star</div>
                </div>
                <?php if (!empty($thumbs)): ?>
                <div class="thumb-row">
                    <?php foreach($thumbs as $index => $th): 
                        $th_src = preg_match('/^https?:\\/\\//i', $th['image_url']) ? $th['image_url'] : $th['image_url'];
                    ?>
                    <div class="thumb-wrap">
                        <img src="<?php echo htmlspecialchars($th_src); ?>" alt="Thumb">
                        <?php if($index == 2): ?>
                        <div class="thumb-overlay">More</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="hotel-info-col">
                <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                <div class="hotel-location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo htmlspecialchars($hotel['place']); ?>
                </div>
                <div class="hotel-desc">
                    <?php echo htmlspecialchars($hotel['description']); ?>
                </div>
                
                <div class="amenities-section">
                    <?php if (!empty($amenities)): ?>
                    <div class="amenities-title">Amenities</div>
                    <div class="amenities-grid">
                        <?php foreach($amenities as $am): ?>
                        <div class="amenity-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php echo htmlspecialchars(trim($am)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hotel-footer">
                    <div>
                        <div class="price-label">Starting from</div>
                        <div class="price-value">₹<?php echo number_format($hotel['starting_tariff']); ?> <span>/ night</span></div>
                    </div>
                    <div class="action-buttons">
                        <div class="btn-outline">Quick Details</div>
                        <div class="btn-solid">Check Availability</div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>"""

content = re.sub(html_signature_old, html_signature_new, content)

html_luxury_old = r"""    <div class="hotel-grid">
        <\?php foreach \(\$luxury as \$hotel\): \?>
        <a href="hotel-detail\.php\?id=<\?php echo \$hotel\['id'\]; \?>" class="hotel-card">
            <div class="hotel-img-wrapper">
                <img src="<\?php echo htmlspecialchars\(\$hotel\['main_image'\]\); \?>" class="hotel-img" alt="<\?php echo htmlspecialchars\(\$hotel\['name'\]\); \?>">
                <div class="hotel-badge" style="background: rgba\(255,255,255,0\.1\); color: #fff;"><\?php echo \$hotel\['star_category'\]; \?> Star</div>
            </div>
            <div class="hotel-info">
                <h3 class="hotel-name"><\?php echo htmlspecialchars\(\$hotel\['name'\]\); \?></h3>
                <div class="hotel-location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <\?php echo htmlspecialchars\(\$hotel\['place'\]\); \?>
                </div>
                <div class="hotel-price-row">
                    <div>
                        <div style="font-size: 0\.85rem; color: rgba\(255,255,255,0\.5\);">Starting from</div>
                        <div class="hotel-price" style="color:#fff;">₹<\?php echo number_format\(\$hotel\['starting_tariff'\]\); \?> <span>/ night</span></div>
                    </div>
                    <div class="btn-view" style="border-color: rgba\(255,255,255,0\.3\); color: #fff;">Inquire</div>
                </div>
            </div>
        </a>
        <\?php endforeach; \?>
    </div>"""

html_luxury_new = """    <div class="hotel-list">
        <?php foreach ($luxury as $hotel): 
            $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$hotel['id']]);
            $thumbs = $stmt->fetchAll();
            $amenities = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
        ?>
        <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>" class="hotel-card">
            <div class="hotel-gallery-col">
                <div class="main-img-wrap">
                    <?php $main_img = preg_match('/^https?:\\/\\//i', $hotel['main_image']) ? $hotel['main_image'] : (strpos($hotel['main_image'], '../') === 0 ? ltrim($hotel['main_image'], '../') : $hotel['main_image']); ?>
                    <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                    <div class="hotel-badge" style="background: rgba(255,255,255,0.1); color: #fff;"><?php echo $hotel['star_category']; ?> Star</div>
                </div>
                <?php if (!empty($thumbs)): ?>
                <div class="thumb-row">
                    <?php foreach($thumbs as $index => $th): 
                        $th_src = preg_match('/^https?:\\/\\//i', $th['image_url']) ? $th['image_url'] : $th['image_url'];
                    ?>
                    <div class="thumb-wrap">
                        <img src="<?php echo htmlspecialchars($th_src); ?>" alt="Thumb">
                        <?php if($index == 2): ?>
                        <div class="thumb-overlay">More</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="hotel-info-col">
                <h3 class="hotel-name"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                <div class="hotel-location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo htmlspecialchars($hotel['place']); ?>
                </div>
                <div class="hotel-desc">
                    <?php echo htmlspecialchars($hotel['description']); ?>
                </div>
                
                <div class="amenities-section">
                    <?php if (!empty($amenities)): ?>
                    <div class="amenities-title">Amenities</div>
                    <div class="amenities-grid">
                        <?php foreach($amenities as $am): ?>
                        <div class="amenity-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php echo htmlspecialchars(trim($am)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hotel-footer">
                    <div>
                        <div class="price-label">Starting from</div>
                        <div class="price-value" style="color: #fff;">₹<?php echo number_format($hotel['starting_tariff']); ?> <span>/ night</span></div>
                    </div>
                    <div class="action-buttons">
                        <div class="btn-outline" style="border-color: rgba(255,255,255,0.3); color: #fff;">Quick Details</div>
                        <div class="btn-solid" style="background: rgba(255,255,255,0.9);">Inquire</div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>"""

content = re.sub(html_luxury_old, html_luxury_new, content)

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated hotels.php successfully")
