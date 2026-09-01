<?php
// Requires $signature and $luxury to be set in the parent file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page_title ?? 'Luxury Hotels | Leisure Loop'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Header -->
    <div class="pt-12 pb-6 px-5 bg-gradient-to-b from-[#050a14] via-[#050a14] to-transparent sticky top-0 z-50 text-center">
        <span class="text-[var(--gold)] text-[10px] font-bold tracking-[0.2em] uppercase mb-1 block opacity-80 mx-auto" style="color: #C5A059;">Exclusive Stays</span>
        <h1 class="serif text-[32px] font-bold text-white leading-tight">Luxury Retreats</h1>
    </div>

    <main class="content-area px-4 pt-6">
        
        <?php if (!empty($signature)): ?>
        <section class="mb-10">
            <h2 class="serif text-xl font-bold mb-4 flex items-center gap-2 text-white/90">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Signature Properties
            </h2>
            <div class="flex flex-col gap-5">
                <?php foreach ($signature as $hotel): ?>
                <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>" class="glass-card rounded-[24px] overflow-hidden block active:scale-[0.98] transition-transform">
                    <div class="h-48 w-full relative">
                        <img src="<?php echo htmlspecialchars($hotel['main_image']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <h3 class="serif text-xl font-bold text-white"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                            <div class="flex items-center gap-1 text-white/80 text-sm mt-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <?php echo htmlspecialchars($hotel['place']); ?>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if (!empty($luxury)): ?>
        <section class="mb-10">
            <h2 class="serif text-xl font-bold mb-4 flex items-center gap-2 text-white/90">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Our Partner Brands
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <?php foreach ($luxury as $hotel): ?>
                <a href="hotel-detail.php?id=<?php echo $hotel['id']; ?>" class="glass-card rounded-2xl overflow-hidden block active:scale-[0.98] transition-transform">
                    <div class="h-32 w-full relative">
                        <img src="<?php echo htmlspecialchars($hotel['main_image']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/20"></div>
                    </div>
                    <div class="p-3">
                        <h3 class="serif text-[15px] font-bold text-white leading-tight mb-1 truncate"><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <p class="text-white/60 text-[11px] truncate"><?php echo htmlspecialchars($hotel['place']); ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <?php include "mobile_bottom_nav.php"; ?>
    <?php include "enquiry-modal.php"; ?>

</body>
</html>
