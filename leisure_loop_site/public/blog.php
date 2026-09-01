<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$blogs = [];
if ($pdo) {
    try {
        $blogs = $pdo->query("SELECT * FROM blogs WHERE is_published = 1 ORDER BY created_at DESC")->fetchAll();
    } catch (PDOException $e) {
        // Table might not exist yet if admin page hasn't been visited
    }
}
?>

<!-- Journal Hero -->
<section class="journal-hero" style="padding: 8rem 0 4rem; background: var(--obsidian); text-align: center; border-bottom: 1px solid rgba(197, 160, 89, 0.1);">
    <div class="container">
        <span class="section-label-gold" style="letter-spacing: 0.2em; text-transform: uppercase; font-size: 0.8rem; font-weight: 600;">The Loop</span>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 3.5rem; color: #fff; margin: 1rem 0;">Journal & <span style="color: var(--gold); font-style: italic;">Insights.</span></h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">Chronicles of exceptional travels, curated guides, and stories from the edge of the world.</p>
    </div>
</section>

<!-- Journal Grid -->
<section class="journal-grid-section" style="padding: 5rem 0; background-color: var(--obsidian);">
    <div class="container">
        <?php if (empty($blogs)): ?>
            <div style="text-align: center; padding: 5rem 0;">
                <p style="color: var(--text-muted); font-size: 1.2rem;">Our journal is currently being curated. Check back soon for extraordinary stories.</p>
            </div>
        <?php else: ?>
            <div class="journal-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 3rem;">
                <?php foreach ($blogs as $b): 
                    $img = !empty($b['image_url']) ? $b['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=800';
                    $img = strpos($img, 'http') === 0 ? $img : $img;
                ?>
                <a href="blog-detail.php?slug=<?php echo htmlspecialchars($b['slug']); ?>" class="journal-card" style="display: flex; flex-direction: column; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(197, 160, 89, 0.1); border-radius: 12px; overflow: hidden; text-decoration: none; transition: transform 0.4s var(--ease), border-color 0.4s var(--ease);">
                    <div class="journal-img-container" style="height: 240px; overflow: hidden;">
                        <div class="journal-img" style="width: 100%; height: 100%; background-image: url('<?php echo htmlspecialchars($img); ?>'); background-size: cover; background-position: center; transition: transform 0.6s var(--ease);"></div>
                    </div>
                    <div class="journal-content" style="padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;">
                        <div class="journal-meta" style="color: var(--gold); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem; font-weight: 600;">
                            <?php echo date('M j, Y', strtotime($b['created_at'])); ?> &bull; <?php echo htmlspecialchars($b['author']); ?>
                        </div>
                        <h2 class="journal-title" style="font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #fff; margin-bottom: 1rem; line-height: 1.3; transition: color 0.3s;">
                            <?php echo htmlspecialchars($b['title']); ?>
                        </h2>
                        <p class="journal-excerpt" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; flex-grow: 1;">
                            <?php echo htmlspecialchars($b['excerpt']); ?>
                        </p>
                        <div class="journal-read-more" style="color: var(--gold); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; display: flex; align-items: center; gap: 0.5rem; margin-top: auto;">
                            Read Article <span class="arrow" style="transition: transform 0.3s;">&rarr;</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.journal-card:hover {
    transform: translateY(-8px);
    border-color: rgba(197, 160, 89, 0.4);
}
.journal-card:hover .journal-img {
    transform: scale(1.05);
}
.journal-card:hover .journal-title {
    color: var(--gold);
}
.journal-card:hover .arrow {
    transform: translateX(5px);
}
</style>

<?php require_once '../includes/footer.php'; ?>
