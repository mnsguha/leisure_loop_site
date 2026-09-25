<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
csrf_stamp_form();
$page_title = "Guest Gallery | Leisure Loop Trip";
$page_desc = "Real smiles, real experiences. Browse moments captured by our guests on their bespoke journeys.";

// Fetch images
$gallery_images = [];
if (isset($pdo)) {
    try {
        $gallery_images = $pdo->query("SELECT * FROM gallery_images WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
    } catch (PDOException $e) {
        $gallery_images = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_desc; ?>">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

</head>
<body class="dark-theme">
    
    <?php include '../includes/header.php';
?>
<link rel="stylesheet" href="css/content-pages.css">


    <section class="section" style="padding-top: 150px; background: var(--obsidian); min-height: 100vh;">
        <div class="container">
            <span class="section-label">Genuine Moments, Unforgettable Journeys</span>
            <h1 class="section-title">Guest <span style="color: var(--gold); font-style: italic;">Gallery.</span></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px;">
                Glimpse into the extraordinary adventures of our travelers. These are authentic memories crafted through our bespoke itineraries, capturing the true essence of discovery.
            </p>

            <div class="gallery-page-grid">
                <?php foreach ($gallery_images as $img): ?>
                <div class="gallery-page-item">
                    <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['alt_text'] ?: 'Guest Experience'); ?>" loading="lazy">
                    <?php if (!empty($img['alt_text'])): ?>
                    <div class="gallery-overlay">
                        <p style="color: #fff; font-size: 0.95rem; margin: 0; line-height: 1.4;"><?php echo htmlspecialchars($img['alt_text']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($gallery_images)): ?>
            <div style="text-align: center; padding: 6rem 0; border: 1px dashed rgba(255,255,255,0.1); border-radius: 16px; margin-top: 4rem;">
                <p style="color: var(--text-muted); font-size: 1.2rem;">More stories coming soon. Be the first to share your experience!</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
