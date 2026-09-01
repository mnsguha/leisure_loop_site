import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target_marker = "<!-- 7. Hotel Partners -->"
target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """
<!-- ✦ Curated Themes Section (Mobile) ✦ -->
<?php
$theme_counts_mobile = [];
$theme_meta_mobile = [];
$default_bg_mobile = 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800';
$default_icon_mobile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';

if (isset($pdo)) {
    try {
        $all_pkgs_mobile = $pdo->query("SELECT tour_type FROM packages WHERE is_active = 1")->fetchAll();
        foreach ($all_pkgs_mobile as $row) {
            if (!empty($row['tour_type'])) {
                $tags = array_map('trim', explode(',', $row['tour_type']));
                foreach ($tags as $tag) {
                    $normalized = ucwords(strtolower($tag));
                    $normalized = preg_replace('/\b(tours|tour)\b/i', '', $normalized);
                    $normalized = trim($normalized);
                    if ($normalized !== '') {
                        if (!isset($theme_counts_mobile[$normalized])) $theme_counts_mobile[$normalized] = 0;
                        $theme_counts_mobile[$normalized]++;
                    }
                }
            }
        }
        
        $db_categories_mobile = $pdo->query("SELECT * FROM tour_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC")->fetchAll();
        foreach ($db_categories_mobile as $cat) {
            $theme_meta_mobile[$cat['name']] = [
                'bg' => !empty($cat['image_url']) ? $cat['image_url'] : $default_bg_mobile,
                'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon_mobile,
                'tagline' => $cat['tagline']
            ];
        }

        $ordered_theme_counts_mobile = [];
        foreach ($db_categories_mobile as $cat) {
            if (isset($theme_counts_mobile[$cat['name']])) {
                $ordered_theme_counts_mobile[$cat['name']] = $theme_counts_mobile[$cat['name']];
                unset($theme_counts_mobile[$cat['name']]);
            }
        }
        foreach ($theme_counts_mobile as $name => $count) {
            $ordered_theme_counts_mobile[$name] = $theme_counts_mobile[$name];
        }
        $theme_counts_mobile = $ordered_theme_counts_mobile;

    } catch (Exception $e) {}
}

if (!empty($theme_counts_mobile)):
?>
<section id="mobile-curated-themes" style="padding: 32px 16px 40px 16px; background-color: #050a14; position: relative; overflow: hidden; border-top: 1px solid rgba(197, 160, 89, 0.08);">
    
    <!-- Header -->
    <div style="margin-bottom: 24px; text-align: center;">
        <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; display: block; margin-bottom: 6px;">CURATED THEMES</span>
        <h2 style="font-size: 1.8rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.2; font-weight: 500;">Bespoke Travel <span style="color: var(--gold); font-style: italic;">Experiences.</span></h2>
    </div>

    <!-- Pill Menu (Horizontal Scroll) -->
    <div style="overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; margin-bottom: 24px; padding-bottom: 8px; scroll-snap-type: x mandatory;">
        <div style="display: inline-flex; gap: 12px; padding-left: 4px; padding-right: 16px;">
            <?php foreach ($theme_counts_mobile as $name => $count): 
                $meta = isset($theme_meta_mobile[$name]) ? $theme_meta_mobile[$name] : ['icon' => $default_icon_mobile];
            ?>
            <a href="packages.php?theme=<?php echo urlencode($name); ?>" style="scroll-snap-align: start; display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(197,160,89,0.2); border-radius: 50px; padding: 6px 16px 6px 6px; text-decoration: none; flex-shrink: 0;">
                <span style="width: 32px; height: 32px; border-radius: 50%; background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); display: flex; align-items: center; justify-content: center;">
                    <span style="width: 14px; height: 14px; display: flex; align-items: center; justify-content: center; color: var(--gold);">
                        <?php echo $meta['icon']; ?>
                    </span>
                </span>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 0.8rem; font-weight: 600; color: #ffffff; letter-spacing: 0.05em;"><?php echo htmlspecialchars($name); ?></span>
                    <span style="font-size: 0.6rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500; margin-top: 1px;"><?php echo $count . ($count === 1 ? ' Tour' : ' Tours'); ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Theme Cards Carousel -->
    <div style="display: flex; gap: 16px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 16px; scroll-snap-type: x mandatory; padding-left: 4px; padding-right: 24px;">
        <?php foreach ($theme_counts_mobile as $name => $count): 
            $meta = isset($theme_meta_mobile[$name]) ? $theme_meta_mobile[$name] : ['bg' => $default_bg_mobile, 'icon' => $default_icon_mobile];
        ?>
        <div onclick="window.location.href='packages.php?theme=<?php echo urlencode($name); ?>'" style="scroll-snap-align: center; flex: 0 0 82%; position: relative; height: 320px; border-radius: 24px; overflow: hidden; border: 1px solid rgba(197, 160, 89, 0.15); box-shadow: 0 10px 25px rgba(0,0,0,0.4); cursor: pointer;">
            <!-- Backdrop -->
            <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($meta['bg']); ?>') no-repeat center center; background-size: cover;"></div>
            <!-- Overlay -->
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5,10,20,0.1) 0%, rgba(5,10,20,0.5) 60%, rgba(5,10,20,0.95) 100%); z-index: 1;"></div>
            
            <!-- Badge -->
            <div style="position: absolute; top: 16px; left: 16px; background: rgba(5,10,20,0.7); border: 1px solid rgba(197,160,89,0.3); padding: 4px 12px; border-radius: 50px; color: var(--gold); font-size: 0.6rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; backdrop-filter: blur(8px); z-index: 2;">
                CURATED
            </div>

            <!-- Content -->
            <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px; z-index: 2; box-sizing: border-box; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <!-- Icon -->
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(197, 160, 89, 0.15); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; color: var(--gold); flex-shrink: 0;">
                        <span style="width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; color: var(--gold);">
                            <?php echo $meta['icon']; ?>
                        </span>
                    </div>
                    <!-- Text -->
                    <div style="display: flex; flex-direction: column;">
                        <h3 style="font-family: var(--font-serif); font-size: 1.4rem; font-weight: 500; color: #ffffff; margin: 0; line-height: 1.2;"><?php echo htmlspecialchars($name); ?></h3>
                        <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                            EXPLORE <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

"""
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Added curated themes correctly")
else:
    print("Could not find marker")
