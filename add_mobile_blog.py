import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target_marker = "<!-- Mobile Bottom Navigation (5-item) -->"
target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """
<!-- ✦ Mobile Journal & Insights Section ✦ -->
<?php
    $recent_blogs_mobile = [];
    if (isset($pdo)) {
        try {
            $recent_blogs_mobile = $pdo->query("SELECT * FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 4")->fetchAll();
        } catch (Exception $e) {}
    }
?>
<?php if (!empty($recent_blogs_mobile)): ?>
<section class="mobile-journal-section" style="padding: 40px 0; background-color: #000000; position: relative; overflow: hidden;">
    <!-- Header -->
    <div style="padding: 0 16px; margin-bottom: 24px; text-align: center;">
        <span style="letter-spacing: 0.15em; font-size: 0.7rem; text-transform: uppercase; display: inline-block; margin-bottom: 8px; color: rgba(255,255,255,0.7);">JOURNAL & INSIGHTS</span>
        <h2 style="font-family: var(--font-serif); font-size: 2rem; color: #ffffff; margin: 0; line-height: 1.2;">
            The Art <span style="font-style: italic; color: var(--gold);">of Wandering.</span>
        </h2>
    </div>

    <!-- Horizontal Swipe Carousel -->
    <div style="display: flex; gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; padding: 0 16px 20px 16px;">
        <?php foreach ($recent_blogs_mobile as $post): 
            $post_img = !empty($post['image_url']) ? $post['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=1000';
            $post_img = strpos($post_img, 'http') === 0 ? $post_img : $post_img;
        ?>
        <a href="blog-detail.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" style="scroll-snap-align: start; flex: 0 0 260px; position: relative; height: 320px; border-radius: 16px; overflow: hidden; text-decoration: none; display: flex; flex-direction: column; justify-content: flex-end; box-shadow: 0 8px 24px rgba(0,0,0,0.5);">
            <!-- Background Image -->
            <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($post_img); ?>') no-repeat center center; background-size: cover; z-index: 0;"></div>
            <!-- Dark Gradient Overlay -->
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0) 10%, rgba(5,10,20,0.6) 50%, rgba(5,10,20,0.95) 100%); z-index: 1;"></div>
            
            <!-- Content Panel -->
            <div style="position: relative; z-index: 2; padding: 20px 16px;">
                <div style="margin-bottom: 10px;">
                    <span style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(197,160,89,0.3); color: var(--gold); padding: 4px 10px; border-radius: 20px; font-size: 0.6rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                        <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                    </span>
                </div>
                <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #ffffff; margin: 0 0 8px 0; line-height: 1.35; font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h3>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.75rem; line-height: 1.45; margin: 0 0 12px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo htmlspecialchars($post['excerpt']); ?>
                </p>
                <div style="color: #e07e26; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px;">
                    <span>Read More</span>
                    <span>&rarr;</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Action Button -->
    <div style="padding: 0 16px; margin-top: 16px; text-align: center;">
        <a href="blog.php" style="display: inline-block; padding: 12px 28px; background: rgba(255,255,255,0.05); border: 1px solid rgba(197, 160, 89, 0.4); border-radius: 30px; color: var(--gold); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; backdrop-filter: blur(8px);">View All Posts</a>
    </div>
</section>
<?php endif; ?>

"""
    
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Blog Section successfully injected into mobile_home.php")
else:
    print("Target marker not found in mobile_home.php")
