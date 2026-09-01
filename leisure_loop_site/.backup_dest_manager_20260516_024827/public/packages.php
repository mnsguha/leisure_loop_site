<?php 
    require_once '../config/db.php';
    require_once '../includes/functions.php';
    
    $page_title = "Curated Experiences | Leisure Loop Trip";
    include '../includes/header.php'; 

    // Fetch all active packages
    $packages = [];
    if ($pdo) {
        $packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
    }
?>

    <header class="section" style="padding-top: 12rem; text-align: center;">
        <div class="container">
            <span class="section-label">The Portfolio</span>
            <h1 class="section-title">Extraordinary <br><span class="serif">Escapes.</span></h1>
            <p style="max-width: 600px; margin: 0 auto; color: var(--text-muted);">From the silent peaks of the North to the lush valleys of the East, explore our curated collection of journeys.</p>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div class="grid-reveal" style="grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));">
                <?php foreach ($packages as $pkg): ?>
                <a href="package-detail.php?slug=<?php echo $pkg['slug']; ?>" class="experience-card" style="text-decoration: none;">
                    <div class="exp-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url']); ?>');"></div>
                    <div class="exp-overlay">
                        <span style="font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 0.5rem; display: block;">Starting from &#8377;<?php echo number_format($pkg['price']); ?></span>
                        <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                        <p><?php 
                            $itinerary = json_decode($pkg['itinerary'], true);
                            $days = count($itinerary);
                            echo $days . " Days of Exploration";
                        ?></p>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #fff; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">
                            View Details <span style="font-size: 1.2rem; line-height: 1;">&rarr;</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>

                <?php if (empty($packages)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 6rem; background: var(--glass); border-radius: 4px;">
                    <p style="color: var(--text-muted);">Our curators are currently crafting new experiences. Please check back shortly.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
