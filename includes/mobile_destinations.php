<?php
require_once '../config/db.php';

// Type filtering
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
$where = "is_active = 1";
$params = [];
if ($type === 'domestic' || $type === 'international') {
    $where .= " AND category = ?";
    $params[] = $type;
    $hero_title = ucfirst($type) . " Destinations";
    $hero_subtitle = "Discover the finest " . $type . " luxury experiences.";
} else {
    $hero_title = "Explore Destinations";
    $hero_subtitle = "Discover the world's most exquisite luxury experiences.";
}

$destinations = [];
$all_best_times = [];

if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT d.*, (SELECT COUNT(*) FROM packages p WHERE p.destination = d.name AND p.is_active = 1) as tour_count FROM destinations d WHERE $where ORDER BY d.display_order ASC, d.name ASC");
    $stmt->execute($params);
    $destinations = $stmt->fetchAll();

    foreach ($destinations as $d) {
        if (!empty($d['best_time'])) {
            $times = explode(',', $d['best_time']);
            foreach ($times as $t) {
                $t = trim($t);
                if ($t !== '' && !in_array($t, $all_best_times)) {
                    $all_best_times[] = $t;
                }
            }
        }
    }
    usort($all_best_times, 'strcasecmp');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($hero_title); ?> | Leisure Loop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <div class="hero">
        <div style="position: absolute; top: max(20px, env(safe-area-inset-top, 20px)); left: 16px; z-index: 20;">
            <a aria-label="Link" href="index.php" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); cursor: pointer; text-decoration: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
        </div>
        
        <div style="position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; width: 100%;">
            <h1><?php echo htmlspecialchars($hero_title); ?></h1>
            <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-top: 8px;"><?php echo htmlspecialchars($hero_subtitle); ?></p>
            
            <div style="display: flex; gap: 12px; width: 100%; max-width: 320px; margin-top: 24px;">
                <a href="?type=domestic" 
                   style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: background 0.3s; <?php echo $type === 'domestic' ? 'background: var(--gold); color: #000;' : 'background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(8px);'; ?>">
                    Domestic
                </a>
                <a href="?type=international" 
                   style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: background 0.3s; <?php echo $type === 'international' ? 'background: var(--gold); color: #000;' : 'background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(8px);'; ?>">
                    International
                </a>
            </div>
        </div>
    </div>

    <!-- Grid -->
    <div class="grid-container" id="destGrid">
        <?php foreach ($destinations as $dest): 
            $img = !empty($dest['card_image']) ? $dest['card_image'] : (!empty($dest['cover_image']) ? $dest['cover_image'] : '');
            
            $times_data = [];
            if (!empty($dest['best_time'])) {
                $times = explode(',', $dest['best_time']);
                foreach ($times as $t) {
                    $times_data[] = strtolower(trim($t));
                }
            }
            $times_str = implode('|', $times_data);
        ?>
        <a href="destination-details.php?slug=<?php echo htmlspecialchars($dest['slug']); ?>" 
           class="card js-card"
           data-name="<?php echo htmlspecialchars(strtolower($dest['name'])); ?>"
           data-order="<?php echo $dest['display_order']; ?>"
           data-times="<?php echo htmlspecialchars($times_str); ?>">
            <?php if (!empty($img)): ?>
                <img src="<?php echo htmlspecialchars($img); ?>" class="card-img" alt="">
            <?php else: ?>
                <div class="card-img" style="background-color: #000;"></div>
            <?php endif; ?>
            <div class="card-body">
                <span class="card-theme"><?php echo htmlspecialchars($dest['category']); ?></span>
                <h3 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h3>
                <div class="card-tagline"><?php echo htmlspecialchars($dest['tagline']); ?></div>
                <div style="display: flex; align-items: center; gap: 6px; margin-top: auto; padding-top: 6px;">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="var(--gold, #C5A059)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em; color: rgba(255,255,255,0.7);"><?php echo $dest['tour_count']; ?> TOURS</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
        
        <div class="no-results" id="noResults">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3>No destinations found</h3>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-overlay" id="modalOverlay" data-action="close-all" role="button" aria-label="Close filter panel"></div>
    
    <!-- Sort -->
    <div class="bottom-sheet" id="sortSheet">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Sort By</h3>
        <label class="sort-option">
            <span>Curated Selection</span>
            <input type="radio" name="sort" value="default" checked data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Name: A-Z</span>
            <input type="radio" name="sort" value="name_asc" data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Name: Z-A</span>
            <input type="radio" name="sort" value="name_desc" data-change="apply-filters">
        </label>
    </div>
    
    <!-- Filter -->
    <div class="side-sheet" id="filterSheet">
        <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Filter</h3>
            <button data-action="close-all" style="background: none; border: none; color: #fff; font-size: 0.85rem; font-weight: 600;" aria-label="Close filter panel">Close</button>
        </div>
        <div style="flex: 1; overflow-y: auto;">
            <div style="padding: 16px; font-weight: 600; color: rgba(255,255,255,0.6); font-size: 0.85rem;">BEST TIME TO VISIT</div>
            <?php foreach ($all_best_times as $t): ?>
            <label class="checkbox-label">
                <input type="checkbox" class="filter-time" value="<?php echo htmlspecialchars(strtolower(trim($t))); ?>">
                <span><?php echo htmlspecialchars($t); ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 12px; background: #0a0f1e;">
            <button data-action="clear-filters" style="flex: 1; padding: 12px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-weight: 600;">Clear</button>
            <button data-action="apply-filters-close" style="flex: 1; padding: 12px; background: var(--gold); border: none; color: #000; font-weight: 700; border-radius: 8px;" aria-label="Apply filters and close">Apply</button>
        </div>
    </div>

    <div class="fixed-sort-filter" style="position: fixed; bottom: env(safe-area-inset-bottom, 16px); left: 16px; width: calc(100% - 32px); display: flex; gap: 12px; z-index: 90;">
        <button class="btn-sort-filter" data-action="toggle-sort" style="background: rgba(10, 15, 25, 0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>
            Sort
        </button>
        <button class="btn-sort-filter" data-action="toggle-filter" style="background: rgba(10, 15, 25, 0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filter
        </button>
    </div>

    
</body>
</html>
