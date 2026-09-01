import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# The section starts with <!-- Most Coveted Journeys --> and ends before <!-- Signature Terrains -->
start_marker = "<!-- Most Coveted Journeys -->"
end_marker = "<!-- Signature Terrains -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx != -1 and end_idx != -1:
    new_section = """<!-- Most Coveted Journeys -->
    <?php 
        $featured_packages = [];
        if (isset($pdo)) {
            $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 AND is_international = 0 ORDER BY id DESC")->fetchAll();
        }
    ?>
    <section id="featured" style="padding: 24px 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
        
        <div style="position: relative; z-index: 2;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; display: block; margin-bottom: 4px;">The Curated Collection</span>
                    <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.15;">of the <span style="color: var(--gold); font-style: italic;">Season.</span></h2>
                </div>
                <a href="packages.php" style="font-size: 0.75rem; color: var(--gold); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 4px; padding-bottom: 4px;">See All <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
            </div>
            
            <style>
                .mobile-pkg-carousel {
                    display: flex;
                    overflow-x: auto;
                    gap: 14px;
                    scroll-snap-type: x mandatory;
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                    padding-bottom: 8px;
                }
                .mobile-pkg-carousel::-webkit-scrollbar { display: none; }
                
                .app-pkg-card {
                    flex: 0 0 calc(45% - 7px);
                    min-width: 140px;
                    max-width: 160px;
                    scroll-snap-align: start;
                    border-radius: 16px;
                    overflow: hidden;
                    background: #111;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
                    position: relative;
                }
                .app-pkg-img {
                    height: 200px;
                    background-size: cover;
                    background-position: center;
                    position: relative;
                }
                .app-pkg-badge {
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    background: rgba(0,0,0,0.6);
                    border: 1px solid rgba(197,160,89,0.4);
                    color: var(--gold);
                    font-size: 0.55rem;
                    font-weight: 700;
                    padding: 4px 8px;
                    border-radius: 20px;
                    backdrop-filter: blur(4px);
                    letter-spacing: 0.05em;
                }
                .app-pkg-content {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    padding: 40px 12px 12px 12px;
                    background: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 40%, transparent 100%);
                }
                .app-pkg-dest {
                    font-size: 0.5rem;
                    color: #aaa;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    margin-bottom: 4px;
                    display: block;
                }
                .app-pkg-title {
                    font-size: 0.8rem;
                    color: #fff;
                    font-family: var(--font-serif);
                    margin: 0 0 6px 0;
                    line-height: 1.25;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .app-pkg-price {
                    font-size: 0.65rem;
                    color: var(--gold);
                    font-weight: 600;
                }
                .app-pkg-card:active .app-pkg-img {
                    transform: scale(1.02);
                    transition: transform 0.2s;
                }
            </style>
            
            <div class="mobile-pkg-carousel">
                <?php foreach ($featured_packages as $pkg): ?>
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
    
    new_content = content[:start_idx] + new_section + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Redesigned Curated Collection successfully.")
else:
    print("Could not find markers.")
