import re

with open('hotels.php', 'r', encoding='utf-8') as f:
    content = f.read()

# For the description, we want to add Read More and hidden div
desc_pattern = r'<div class="hotel-desc">\s*<\?php echo htmlspecialchars\(\$hotel\[\'description\'\]\); \?>\s*</div>'
desc_replacement = r"""<div class="hotel-desc">
                    <?php echo htmlspecialchars($hotel['description']); ?>
                </div>
                <?php if (strlen($hotel['description']) > 150): ?>
                <div class="read-more-btn" onclick="event.preventDefault(); openInfoModal('<?php echo addslashes(htmlspecialchars($hotel['name'])); ?>', 'ABOUT THE HOTEL', document.getElementById('desc-content-<?php echo $hotel['id']; ?>').innerHTML)">... Read More</div>
                <?php endif; ?>
                <div id="desc-content-<?php echo $hotel['id']; ?>" style="display:none;"><?php echo nl2br(htmlspecialchars($hotel['description'])); ?></div>"""

content = re.sub(desc_pattern, desc_replacement, content)

# For the amenities, we want to limit the visible ones and add View More
amenities_pattern = r"""<div class="amenities-grid">\s*<\?php foreach\(\$amenities as \$am\): \?>\s*<div class="amenity-item">\s*<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>\s*<\?php echo htmlspecialchars\(trim\(\$am\)\); \?>\s*</div>\s*<\?php endforeach; \?>\s*</div>"""

amenities_replacement = r"""<div class="amenities-grid">
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
                    </div>"""

content = re.sub(amenities_pattern, amenities_replacement, content)

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Modal integrations applied!")
