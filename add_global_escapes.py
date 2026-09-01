import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# The new section goes right before <!-- 7. Hotel Partners -->
target_marker = "<!-- 7. Hotel Partners -->"

target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """<!-- Global Escapes -->
    <?php 
        $global_packages = [];
        if (isset($pdo)) {
            $global_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 1 ORDER BY id DESC")->fetchAll();
        }
    ?>
    <section id="global-escapes" style="padding: 24px 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
        
        <div style="position: relative; z-index: 2;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; display: block; margin-bottom: 4px;">World Collection</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.15;">Global <span style="color: var(--gold); font-style: italic;">Escapes.</span></h2>
                </div>
                <a href="packages.php?type=international" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; padding-bottom: 4px;">See All <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
            </div>
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($global_packages as $pkg): ?>
                <div class="app-pkg-card" onclick="window.location.href='package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>'">
                    <div class="app-pkg-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url'] ?: 'assets/images/placeholder.jpg'); ?>');">
                        <div class="app-pkg-badge">
                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo $nights . "N / " . $days . "D";
                                } else {
                                    echo "CUSTOM";
                                }
                            }
                            ?>
                        </div>
                        <div class="app-pkg-content">
                            <span class="app-pkg-dest"><?php echo htmlspecialchars(strtoupper($pkg['destination'] ?? '')); ?></span>
                            <h3 class="app-pkg-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                            <div class="app-pkg-price">FROM &#8377;<?php echo number_format($pkg['price']); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

"""
    
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Added Global Escapes successfully.")
else:
    print("Could not find marker.")
