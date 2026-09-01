import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# The section starts with <!-- Signature Terrains --> and ends before <!-- 7. Hotel Partners -->
start_marker = "<!-- Signature Terrains -->"
end_marker = "<!-- 7. Hotel Partners -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx != -1 and end_idx != -1:
    new_section = """<!-- Signature Terrains -->
    <section class="destinations-section" id="mobile-destinations" style="padding: 15px; background-color: #000;">
        <?php
            $destinations_list = [];
            if (isset($pdo)) {
                $destinations_list = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE d.is_active = 1 ORDER BY d.display_order ASC, d.created_at DESC")->fetchAll();
            }
            $domestic = array_filter($destinations_list, function($d) { return strtolower($d['category']) === 'domestic'; });
            $international = array_filter($destinations_list, function($d) { return strtolower($d['category']) === 'international'; });
        ?>
        
        <div style="background: linear-gradient(145deg, #1f1b13, #0a0805); border: 1px solid rgba(197,160,89,0.3); border-radius: 16px; padding: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Curated Escapes</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.1;">Signature <span style="color: var(--gold); font-style: italic;">Terrains</span></h2>
                </div>
                <div style="background: rgba(197,160,89,0.1); padding: 4px 8px; border-radius: 8px; border: 1px solid rgba(197,160,89,0.2);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                </div>
            </div>
            
            <!-- Domestic -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 0.85rem; color: #fff; margin: 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <div style="width: 4px; height: 12px; background: var(--gold); border-radius: 4px;"></div> Domestic
                </h3>
            </div>
            <div class="dest-mini-carousel" style="display: flex; overflow-x: auto; gap: 12px; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; margin-bottom: 24px; padding-bottom: 4px;">
                <?php foreach ($domestic as $dest): ?>
                    <div class="dest-mini-card" style="flex: 0 0 calc(50% - 6px); min-width: calc(50% - 6px); max-width: calc(50% - 6px); scroll-snap-align: start; position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.3); cursor: pointer;" onclick="window.location.href='destination-details.php?slug=<?php echo urlencode($dest['slug']); ?>'">
                        <div style="height: 130px; background-image: url('<?php echo htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>'); background-size: cover; background-position: center; transition: transform 0.3s ease;"></div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 12px 12px 12px; background: linear-gradient(transparent, rgba(0,0,0,0.95));">
                            <h4 style="color: #fff; font-size: 0.85rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: var(--font-serif); text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?php echo htmlspecialchars($dest['name']); ?></h4>
                            <span style="color: var(--gold); font-size: 0.6rem; font-weight: 500;"><?php echo (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- International -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 0.85rem; color: #fff; margin: 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                    <div style="width: 4px; height: 12px; background: var(--gold); border-radius: 4px;"></div> International
                </h3>
            </div>
            <div class="dest-mini-carousel" style="display: flex; overflow-x: auto; gap: 12px; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none;">
                <?php foreach ($international as $dest): ?>
                    <div class="dest-mini-card" style="flex: 0 0 calc(50% - 6px); min-width: calc(50% - 6px); max-width: calc(50% - 6px); scroll-snap-align: start; position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.3); cursor: pointer;" onclick="window.location.href='destination-details.php?slug=<?php echo urlencode($dest['slug']); ?>'">
                        <div style="height: 130px; background-image: url('<?php echo htmlspecialchars(!empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800')); ?>'); background-size: cover; background-position: center; transition: transform 0.3s ease;"></div>
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 12px 12px 12px; background: linear-gradient(transparent, rgba(0,0,0,0.95));">
                            <h4 style="color: #fff; font-size: 0.85rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: var(--font-serif); text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?php echo htmlspecialchars($dest['name']); ?></h4>
                            <span style="color: var(--gold); font-size: 0.6rem; font-weight: 500;"><?php echo (int)$dest['tour_count']; ?> Tours</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <style>
            .dest-mini-carousel::-webkit-scrollbar { display: none; }
            .dest-mini-card:active div:first-child { transform: scale(1.05); }
        </style>
        
        <script>
            // Enable mouse drag-to-scroll for the new carousels on desktop testing
            document.querySelectorAll('.dest-mini-carousel').forEach(carousel => {
                let isDown = false;
                let startX;
                let scrollLeft;
                let isDragging = false;

                carousel.addEventListener('mousedown', (e) => {
                    isDown = true;
                    isDragging = false;
                    startX = e.pageX - carousel.offsetLeft;
                    scrollLeft = carousel.scrollLeft;
                    e.preventDefault(); // Prevent text selection
                });
                carousel.addEventListener('mouseleave', () => { isDown = false; });
                carousel.addEventListener('mouseup', () => { isDown = false; });
                carousel.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - carousel.offsetLeft;
                    const walk = (x - startX) * 2;
                    if (Math.abs(walk) > 5) isDragging = true;
                    carousel.scrollLeft = scrollLeft - walk;
                });
                
                // Prevent click on drag
                carousel.querySelectorAll('.dest-mini-card').forEach(card => {
                    card.addEventListener('click', (e) => {
                        if (isDragging) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });
                });
            });
        </script>
    </section>

"""
    
    new_content = content[:start_idx] + new_section + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Redesigned Signature Terrains successfully.")
else:
    print("Could not find markers.")
