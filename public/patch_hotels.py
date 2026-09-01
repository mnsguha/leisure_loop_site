import re

with open('hotels.php', 'r', encoding='utf-8') as f:
    content = f.read()

# The HTML block for the card
card_html = """        <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>" class="hotel-card" data-category="<?php echo $hotel['type']; ?>">
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
                <?php if (strlen($hotel['description']) > 150): ?>
                <div class="read-more-btn" onclick="event.preventDefault(); openInfoModal('<?php echo addslashes(htmlspecialchars($hotel['name'])); ?>', 'ABOUT THE HOTEL', document.getElementById('desc-content-<?php echo $hotel['id']; ?>').innerHTML)">... Read More</div>
                <?php endif; ?>
                <div id="desc-content-<?php echo $hotel['id']; ?>" style="display:none;"><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></div>
                
                <div class="amenities-section">
                    <?php if (!empty($amenities)): ?>
                    <div class="amenities-title">Amenities</div>
                    <div class="amenities-grid">
                        <?php 
                        $visible_amenities = array_slice($amenities, 0, 6);
                        foreach($visible_amenities as $am): 
                        ?>
                        <div class="amenity-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <?php echo htmlspecialchars(trim($am)); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($amenities) > 6): ?>
                    <div class="view-more-btn" onclick="event.preventDefault(); openInfoModal('<?php echo addslashes(htmlspecialchars($hotel['name'])); ?>', 'AMENITIES', document.getElementById('amenities-content-<?php echo $hotel['id']; ?>').innerHTML)">View More <svg style="width:12px;height:12px;vertical-align:middle;margin-left:4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    <?php endif; ?>
                    <div id="amenities-content-<?php echo $hotel['id']; ?>" style="display:none;">
                        <div class="modal-amenities-grid">
                            <?php foreach($amenities as $am): ?>
                            <div class="modal-amenity-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <?php echo htmlspecialchars(trim($am)); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
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
        </a>"""

replacement = """<style>
/* Filter Buttons */
.hotel-filters {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}
.filter-btn {
    background: transparent;
    color: #e5e2e2;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 12px 24px;
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
}
.filter-btn:hover {
    border-color: rgba(255,255,255,0.5);
}
.filter-btn.active {
    background: #eb2026; /* Home button Red */
    color: #fff;
    border-color: #eb2026;
    box-shadow: 0 4px 15px rgba(235, 32, 38, 0.3);
}
</style>

<div class="section-container">
    <div class="hotel-filters">
        <button class="filter-btn active" data-filter="all">All Properties</button>
        <button class="filter-btn" data-filter="signature">Our Signature Properties</button>
        <button class="filter-btn" data-filter="luxury">Our Partner/Associate Brands</button>
    </div>

    <div class="hotel-list">
        <?php 
        // Combine arrays so we loop through all of them
        $all_hotels = array_merge($signature, $luxury);
        foreach ($all_hotels as $hotel): 
            $stmt = $pdo->prepare("SELECT image_url FROM hotel_images WHERE hotel_id = ? ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$hotel['id']]);
            $thumbs = $stmt->fetchAll();
            $amenities = !empty($hotel['amenities']) ? explode(',', $hotel['amenities']) : [];
        ?>
""" + card_html + """
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const hotelCards = document.querySelectorAll('.hotel-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            // Filter cards
            hotelCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>"""

# Find start of section-container
start_idx = content.find('<div class="section-container">')
# Find end of that huge block
# We know the block ends with <?php endif; ?> followed by </div> before the <!-- Info Modal -->
import re
new_content = re.sub(r'<div class="section-container">.*?</div>\s*<\?php endif; \?>\s*</div>', replacement, content, flags=re.DOTALL)

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.write(new_content)
    print("Updated via regex successfully")
