import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target_marker = "<!-- ✦ Fixed Departures Section (Mobile) ✦ -->"
target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """
<!-- ✦ Mobile Genuine Moments Gallery ✦ -->
<?php
    $gallery_images_mobile = [];
    if (isset($pdo)) {
        try {
            $gallery_images_mobile = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY display_order ASC LIMIT 16")->fetchAll();
        } catch (Exception $e) {}
    }
    
    $default_gallery_mobile = [
        'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=800',
        'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?q=80&w=800',
        'https://images.unsplash.com/photo-1540960578613-2df67215f91e?q=80&w=800',
        'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?q=80&w=800',
        'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?q=80&w=800',
        'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?q=80&w=800'
    ];
    
    $top_track_mobile = [];
    $bottom_track_mobile = [];

    if (count($gallery_images_mobile) >= 4) {
        $half = ceil(count($gallery_images_mobile) / 2);
        $top_track_mobile = array_slice($gallery_images_mobile, 0, $half);
        $bottom_track_mobile = array_slice($gallery_images_mobile, $half);
    } else {
        foreach ($default_gallery_mobile as $k => $url) {
            if ($k < 3) $top_track_mobile[] = ['image_url' => $url];
            else $bottom_track_mobile[] = ['image_url' => $url];
        }
    }
?>

<style>
    .mobile-gallery-section {
        padding: 32px 0 40px 0;
        background-color: #050a14;
        position: relative;
        overflow: hidden;
        border-top: 1px solid rgba(197, 160, 89, 0.08);
    }
    .mobile-gallery-marquee-wrapper {
        width: 100%;
        display: flex;
        overflow: hidden;
        margin-bottom: 12px;
        position: relative;
        /* Vignette mask for seamless fade out on edges */
        -webkit-mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
        mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
    }
    .mobile-gallery-marquee {
        display: flex;
        width: max-content;
    }
    .mobile-gallery-content {
        display: flex;
        gap: 12px;
        padding-right: 12px;
    }
    .m-track-left { animation: mScrollLeft 20s linear infinite; }
    .m-track-right { animation: mScrollRight 20s linear infinite; }
    
    .mobile-gallery-marquee-wrapper:active .m-track-left,
    .mobile-gallery-marquee-wrapper:active .m-track-right {
        animation-play-state: paused;
    }

    @keyframes mScrollLeft {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    
    @keyframes mScrollRight {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }

    .m-smile-card {
        width: 140px;
        height: 100px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }
    .m-smile-img {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
    }
</style>

<section class="mobile-gallery-section">
    <!-- Header -->
    <div style="padding: 0 16px; margin-bottom: 24px;">
        <h2 style="font-size: 1.6rem; color: #fff; margin-bottom: 6px; font-family: var(--font-serif); line-height: 1.15;">
            Genuine Moments,<br>
            <span style="color: var(--gold); font-style: italic;">Unforgettable Journeys.</span>
        </h2>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; line-height: 1.5; margin: 0;">
            Glimpse into the extraordinary adventures of our travelers. Authentic memories crafted through bespoke itineraries.
        </p>
    </div>

    <!-- Gallery Marquees -->
    <!-- Top Track (Moves Left) -->
    <div class="mobile-gallery-marquee-wrapper">
        <div class="mobile-gallery-marquee m-track-left">
            <div class="mobile-gallery-content">
                <?php foreach($top_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
            <div class="mobile-gallery-content" aria-hidden="true">
                <?php foreach($top_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Bottom Track (Moves Right) -->
    <div class="mobile-gallery-marquee-wrapper">
        <div class="mobile-gallery-marquee m-track-right">
            <div class="mobile-gallery-content">
                <?php foreach($bottom_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
            <div class="mobile-gallery-content" aria-hidden="true">
                <?php foreach($bottom_track_mobile as $img): $url = htmlspecialchars(strpos($img['image_url'], 'http') === 0 ? $img['image_url'] : $img['image_url']); ?>
                <div class="m-smile-card"><div class="m-smile-img" style="background-image: url('<?php echo $url; ?>');"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div style="padding: 0 16px; margin-top: 24px; text-align: center;">
        <a href="gallery.php" style="display: inline-block; padding: 12px 28px; background: rgba(255,255,255,0.05); border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 30px; color: var(--gold); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; backdrop-filter: blur(8px);">Explore Gallery</a>
    </div>
</section>


"""
    
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Gallery Section successfully injected into mobile_home.php")
else:
    print("Target marker not found in mobile_home.php")
