import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target_marker = "<!-- 7. Hotel Partners -->"
target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """<!-- Auto Scrolling Promotional Banners -->
<?php
    $promos = [];
    if (isset($pdo)) {
        try {
            $promos = $pdo->query("SELECT * FROM advertisements WHERE page_type IN ('home', 'home_middle') AND is_active = 1")->fetchAll();
        } catch (Exception $e) {}
    }
?>
<?php if (!empty($promos)): ?>
<section class="promo-banner-section" style="padding: 16px; background-color: #080808; position: relative; overflow: hidden; border-top: 1px solid rgba(255,255,255,0.05);">
    <div class="promo-carousel-wrapper" style="position: relative;">
        <div id="promoCarousel" class="promo-carousel" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; gap: 12px;">
            <?php foreach ($promos as $index => $promo): 
                $bg_img = !empty($promo['image_url']) ? htmlspecialchars($promo['image_url']) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
            ?>
            <div class="promo-card" style="flex: 0 0 100%; scroll-snap-align: center; position: relative; border-radius: 16px; overflow: hidden; min-height: 160px; display: flex; align-items: center; box-shadow: 0 8px 20px rgba(0,0,0,0.5);">
                <div style="position: absolute; inset: 0; background: url('<?php echo $bg_img; ?>') no-repeat center center; background-size: cover; z-index: 1;"></div>
                <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(5,10,20,0.95) 0%, rgba(5,10,20,0.6) 70%, transparent 100%); z-index: 2;"></div>
                <div style="position: relative; z-index: 3; padding: 20px; width: 85%; display: flex; flex-direction: column; align-items: flex-start; gap: 8px;">
                    <?php if (file_exists(__DIR__ . '/assets/img/logo.png')): ?>
                        <div style="background: rgba(255,255,255,0.95); padding: 4px 12px; border-radius: 8px; margin-bottom: 4px;">
                            <img src="assets/img/logo.png" alt="Leisure Loop Trip" style="max-height: 20px; display: block;">
                        </div>
                    <?php else: ?>
                        <span style="font-size: 0.7rem; font-weight: 700; color: #fff; letter-spacing: 0.1em;">LEISURE <span style="color: var(--gold);">LOOP</span></span>
                    <?php endif; ?>
                    <h3 style="font-size: 1.1rem; color: #fff; font-family: var(--font-serif); line-height: 1.25; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.8); background: linear-gradient(90deg, #fff, var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?php echo htmlspecialchars($promo['title']); ?>
                    </h3>
                    <a href="contact.php" style="padding: 8px 16px; font-size: 0.7rem; font-weight: 700; border-radius: 50px; background: linear-gradient(135deg, var(--gold), #f0d59e); color: #000; text-decoration: none; margin-top: 4px; box-shadow: 0 4px 10px rgba(197,160,89,0.3);">
                        <?php echo htmlspecialchars($promo['btn_text'] ?: 'Book Now'); ?> &rarr;
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination Dots -->
        <div class="promo-dots" style="display: flex; justify-content: center; gap: 6px; margin-top: 12px;">
            <?php foreach ($promos as $index => $promo): ?>
            <div class="promo-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" style="width: <?php echo $index === 0 ? '16px' : '6px'; ?>; height: 6px; border-radius: 6px; background: <?php echo $index === 0 ? 'var(--gold)' : 'rgba(255,255,255,0.2)'; ?>; transition: all 0.3s ease;"></div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <style>
        .promo-carousel::-webkit-scrollbar { display: none; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('promoCarousel');
            const dots = document.querySelectorAll('.promo-dot');
            if (!carousel || dots.length <= 1) return;
            
            let currentIndex = 0;
            const totalSlides = dots.length;
            
            // Update dots on scroll
            carousel.addEventListener('scroll', function() {
                const scrollLeft = carousel.scrollLeft;
                const slideWidth = carousel.clientWidth;
                currentIndex = Math.round(scrollLeft / slideWidth);
                
                dots.forEach((dot, index) => {
                    if (index === currentIndex) {
                        dot.style.width = '16px';
                        dot.style.background = 'var(--gold)';
                    } else {
                        dot.style.width = '6px';
                        dot.style.background = 'rgba(255,255,255,0.2)';
                    }
                });
            });
            
            // Auto scroll every 4 seconds
            setInterval(() => {
                let nextIndex = currentIndex + 1;
                if (nextIndex >= totalSlides) nextIndex = 0;
                
                const slideWidth = carousel.clientWidth;
                carousel.scrollTo({
                    left: nextIndex * slideWidth,
                    behavior: 'smooth'
                });
            }, 4000);
        });
    </script>
</section>
<?php endif; ?>

"""
    
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Added promo banners successfully.")
else:
    print("Could not find marker.")
