import os

content = """<?php
require_once '../config/db.php';
$packages_mobile = [];
if (isset($pdo)) {
    $packages_mobile = $pdo->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
}

$all_themes = [];
$all_destinations = [];
$all_durations = [];
$max_price_in_db = 0;
$min_price_in_db = 999999;

foreach ($packages_mobile as $pkg) {
    if (!empty($pkg['tour_type'])) {
        $types = explode(',', $pkg['tour_type']);
        foreach ($types as $t) {
            $t = trim($t);
            if ($t !== '' && !in_array($t, $all_themes)) {
                $all_themes[] = $t;
            }
        }
    }
    if (!empty($pkg['destination'])) {
        $d = trim($pkg['destination']);
        if (!in_array($d, $all_destinations)) {
            $all_destinations[] = $d;
        }
    }
    $days = (int)($pkg['days'] ?? 0);
    $nights = (int)($pkg['nights'] ?? 0);
    if ($days > 0 || $nights > 0) {
        $dur_label = sprintf("%02d Nights / %02d Days", $nights, $days);
        $key = $nights . '-' . $days;
        if (!isset($all_durations[$key])) {
            $all_durations[$key] = [
                'label' => $dur_label,
                'nights' => $nights,
                'days' => $days
            ];
        }
    }
    $price = (float)$pkg['price'];
    if ($price > $max_price_in_db) $max_price_in_db = $price;
    if ($price > 0 && $price < $min_price_in_db) $min_price_in_db = $price;
}
if ($min_price_in_db == 999999) $min_price_in_db = 0;

usort($all_themes, 'strcasecmp');
usort($all_destinations, 'strcasecmp');
ksort($all_durations);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Explore Tours | Leisure Loop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet"/>
    <style>
        :root { --gold: #C5A059; }
        body { background-color: #050a14; color: #fff; font-family: 'Inter', sans-serif; margin: 0; padding-bottom: 100px; -webkit-tap-highlight-color: transparent; }
        
        /* Hero Section */
        .hero { position: relative; height: 40vh; min-height: 250px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 20px; overflow: hidden; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 2.8rem; color: #fff; margin: 0; text-shadow: 0 2px 10px rgba(0,0,0,0.5); font-weight: 600; line-height: 1.1; letter-spacing: -0.02em; }
        
        .btn-sort-filter { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; color: #fff; cursor: pointer; transition: background 0.3s; }
        .btn-sort-filter:active { background: rgba(255,255,255,0.1); }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 16px; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: opacity 0.3s; }
        .card.hidden { display: none; }
        
        .card-img { width: 100%; aspect-ratio: 4/5; object-fit: cover; }
        .card-body { padding: 12px 10px; display: flex; flex-direction: column; gap: 4px; }
        .card-theme { font-size: 0.6rem; color: var(--gold); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; }
        .card-title { font-size: 0.85rem; font-weight: 600; color: #fff; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.7rem; }
        .card-price { font-size: 0.95rem; font-weight: 700; color: #fff; margin-top: 4px; }
        
        /* Modals */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        
        .bottom-sheet { position: fixed; bottom: -100%; left: 0; width: 100%; background: #0a0f1e; border-radius: 20px 20px 0 0; z-index: 101; transition: bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1); padding: 24px 16px; box-shadow: 0 -10px 40px rgba(0,0,0,0.5); }
        .bottom-sheet.active { bottom: 0; }
        
        .side-sheet { position: fixed; top: 0; right: -100%; width: 100%; height: 100%; background: #0a0f1e; z-index: 101; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; }
        .side-sheet.active { right: 0; }
        
        /* Custom Radio */
        .sort-option { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff; font-size: 0.95rem; font-weight: 500; cursor: pointer; }
        .sort-option:last-child { border-bottom: none; }
        
        input[type="radio"], input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--gold); }
        
        /* Dual Pane Filter */
        .filter-body { display: flex; flex: 1; overflow: hidden; }
        .filter-left { width: 35%; background: rgba(255,255,255,0.02); overflow-y: auto; border-right: 1px solid rgba(255,255,255,0.05); }
        .filter-right { width: 65%; overflow-y: auto; padding: 16px; position: relative; }
        .filter-tab { padding: 16px 12px; font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.6); border-left: 3px solid transparent; cursor: pointer; }
        .filter-tab.active { background: rgba(197, 160, 89, 0.1); color: var(--gold); border-left-color: var(--gold); }
        
        .filter-pane { display: none; }
        .filter-pane.active { display: block; }
        
        .checkbox-label { display: flex; align-items: center; gap: 12px; padding: 12px 0; font-size: 0.9rem; color: #fff; cursor: pointer; }
        
        .price-inputs { display: flex; align-items: center; gap: 8px; margin-top: 16px; }
        .price-inputs input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px; border-radius: 6px; width: 100%; outline: none; }
        
        /* Glass Pill Nav */
        .mobile-nav-pill {
            position: fixed;
            bottom: env(safe-area-inset-bottom, 16px);
            left: 16px;
            width: calc(100% - 32px);
            background: rgba(10, 15, 25, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            z-index: 90;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 8px 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .mobile-nav-pill { bottom: calc(env(safe-area-inset-bottom) + 16px); }
        }
        .nav-item { display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); padding: 6px; position: relative; }
        .nav-item.active .nav-icon { stroke: var(--gold); fill: rgba(197, 160, 89, 0.2); }
        .nav-item.inactive .nav-icon { stroke: rgba(255, 255, 255, 0.6); fill: none; }
        .nav-label { font-size: 0.6rem; font-family: 'Inter', sans-serif; font-weight: 600; }
        .nav-item.active .nav-label { color: var(--gold); }
        .nav-item.inactive .nav-label { color: rgba(255, 255, 255, 0.5); }
        .nav-dot { width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 2px; opacity: 0; position: absolute; bottom: -2px; }
        .nav-item.active .nav-dot { opacity: 1; }
        
        .no-results { display: none; text-align: center; padding: 40px 20px; color: rgba(255,255,255,0.6); grid-column: 1 / -1; }
    </style>
</head>
<body>

    <div class="hero">
        <!-- Background Image & Gradient Overlay -->
        <div style="position: absolute; inset: 0; z-index: 0;">
            <img style="width: 100%; height: 100%; object-fit: cover;" 
                 src="assets/img/pkg.jpg" 
                 alt="Explore Tours">
            <!-- Fade into background color at the bottom -->
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 0%, #050a14 100%); pointer-events: none;"></div>
            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); pointer-events: none;"></div>
        </div>
        <div style="position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; width: 100%;">
            <h1>Explore Tours</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.95rem; margin-top: 8px;">Find your perfect luxury escape.</p>
            
            <div style="display: flex; gap: 12px; width: 100%; max-width: 320px; margin-top: 32px;">
                <button class="btn-sort-filter" onclick="toggleSort()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>
                    Sort
                </button>
                <button class="btn-sort-filter" onclick="toggleFilter()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- 2-Column Grid -->
    <div class="grid-container" id="packagesGrid">
        <?php foreach ($packages_mobile as $pkg): 
            $nights = (int)($pkg['nights'] ?? 0);
            $days = (int)($pkg['days'] ?? 0);
            $price = (float)$pkg['price'];
            $img = !empty($pkg['image_url']) ? $pkg['image_url'] : '';
            $theme = explode(',', $pkg['tour_type'])[0] ?? 'Signature';
            
            $pkg_themes = [];
            if (!empty($pkg['tour_type'])) {
                $types = explode(',', $pkg['tour_type']);
                foreach ($types as $t) {
                    $pkg_themes[] = strtolower(trim($t));
                }
            }
            $themes_data = implode('|', $pkg_themes);
            $duration_key = $nights . '-' . $days;
        ?>
        <a href="package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" 
           class="card js-card"
           data-price="<?php echo $price; ?>"
           data-destination="<?php echo htmlspecialchars(strtolower(trim($pkg['destination']))); ?>"
           data-themes="<?php echo htmlspecialchars($themes_data); ?>"
           data-duration="<?php echo $duration_key; ?>"
           data-days="<?php echo $days; ?>">
            <?php if (!empty($img)): ?>
                <img src="<?php echo htmlspecialchars($img); ?>" class="card-img" alt="">
            <?php else: ?>
                <div class="card-img" style="background-color: #000;"></div>
            <?php endif; ?>
            <div class="card-body">
                <span class="card-theme"><?php echo htmlspecialchars(trim($theme)); ?></span>
                <h3 class="card-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                <div class="card-price">₹<?php echo number_format($pkg['price']); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
        
        <div class="no-results" id="noResults">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3>No packages found</h3>
            <p style="font-size: 0.85rem; margin-top: 8px;">Try adjusting your filters.</p>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeAll()"></div>
    
    <!-- Bottom Sheet: Sort -->
    <div class="bottom-sheet" id="sortSheet">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Sort By</h3>
        <label class="sort-option">
            <span>Curated Selection</span>
            <input type="radio" name="sort" value="default" checked onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Price: Low to High</span>
            <input type="radio" name="sort" value="price_asc" onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Price: High to Low</span>
            <input type="radio" name="sort" value="price_desc" onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Duration: Shortest First</span>
            <input type="radio" name="sort" value="duration_asc" onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Duration: Longest First</span>
            <input type="radio" name="sort" value="duration_desc" onchange="applyFilters()">
        </label>
    </div>
    
    <!-- Side/Full Sheet: Filter -->
    <div class="side-sheet" id="filterSheet">
        <!-- Header -->
        <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Filters</h3>
            <button onclick="closeAll()" style="background: none; border: none; color: #fff; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Close</button>
        </div>
        
        <!-- Dual Pane Body -->
        <div class="filter-body">
            <div class="filter-left">
                <div class="filter-tab active" onclick="switchFilterPane('pane-theme', this)">Theme</div>
                <div class="filter-tab" onclick="switchFilterPane('pane-dest', this)">Destination</div>
                <div class="filter-tab" onclick="switchFilterPane('pane-dur', this)">Duration</div>
                <div class="filter-tab" onclick="switchFilterPane('pane-price', this)">Price</div>
            </div>
            <div class="filter-right">
                <!-- Theme Pane -->
                <div class="filter-pane active" id="pane-theme">
                    <?php foreach ($all_themes as $t): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-theme" value="<?php echo htmlspecialchars(strtolower(trim($t))); ?>">
                        <span><?php echo htmlspecialchars($t); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <!-- Destination Pane -->
                <div class="filter-pane" id="pane-dest">
                    <?php foreach ($all_destinations as $d): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-dest" value="<?php echo htmlspecialchars(strtolower(trim($d))); ?>">
                        <span><?php echo htmlspecialchars($d); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <!-- Duration Pane -->
                <div class="filter-pane" id="pane-dur">
                    <?php foreach ($all_durations as $key => $dur): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-dur" value="<?php echo htmlspecialchars($key); ?>">
                        <span><?php echo htmlspecialchars($dur['label']); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <!-- Price Pane -->
                <div class="filter-pane" id="pane-price">
                    <p style="font-size: 0.9rem; margin: 0; color: rgba(255,255,255,0.8);">Enter Price Range (₹)</p>
                    <div class="price-inputs">
                        <input type="number" id="price_min" placeholder="Min" min="0">
                        <span style="color: rgba(255,255,255,0.5);">-</span>
                        <input type="number" id="price_max" placeholder="Max" min="0">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Buttons -->
        <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 12px; background: #0a0f1e;">
            <button onclick="clearFilters()" style="flex: 1; padding: 12px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-weight: 600;">Clear Filters</button>
            <button onclick="applyFiltersAndClose()" style="flex: 1; padding: 12px; background: var(--gold); border: none; color: #000; font-weight: 700; border-radius: 8px;">Apply</button>
        </div>
    </div>

    <!-- Mobile Bottom Navigation (5-item) -->
    <nav class="mobile-nav-pill">
        <a href="index.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span class="nav-label">Home</span>
            <div class="nav-dot"></div>
        </a>
        <a href="destinations.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"></path></svg>
            <span class="nav-label">Places</span>
            <div class="nav-dot"></div>
        </a>
        <a href="packages.php" class="nav-item active">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            <span class="nav-label">Tours</span>
            <div class="nav-dot"></div>
        </a>
        <a href="contact.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            <span class="nav-label">Chat</span>
            <div class="nav-dot"></div>
        </a>
        <button type="button" class="nav-item inactive" onclick="alert('Menu Modal')">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            <span class="nav-label">Menu</span>
            <div class="nav-dot"></div>
        </button>
    </nav>

    <script>
        function toggleSort() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('sortSheet').classList.add('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function toggleFilter() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('filterSheet').classList.add('active');
            document.getElementById('sortSheet').classList.remove('active');
        }
        function closeAll() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('sortSheet').classList.remove('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function switchFilterPane(paneId, tabEl) {
            document.querySelectorAll('.filter-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            document.getElementById(paneId).classList.add('active');
            tabEl.classList.add('active');
        }
        
        function clearFilters() {
            document.querySelectorAll('.filter-theme, .filter-dest, .filter-dur').forEach(el => el.checked = false);
            document.getElementById('price_min').value = '';
            document.getElementById('price_max').value = '';
            applyFilters();
        }
        
        function applyFiltersAndClose() {
            applyFilters();
            closeAll();
        }
        
        function applyFilters() {
            const themes = Array.from(document.querySelectorAll('.filter-theme:checked')).map(el => el.value);
            const dests = Array.from(document.querySelectorAll('.filter-dest:checked')).map(el => el.value);
            const durs = Array.from(document.querySelectorAll('.filter-dur:checked')).map(el => el.value);
            
            const minPrice = parseFloat(document.getElementById('price_min').value) || 0;
            const maxPrice = parseFloat(document.getElementById('price_max').value) || Infinity;
            
            const sortVal = document.querySelector('input[name="sort"]:checked').value;
            
            const cards = Array.from(document.querySelectorAll('.js-card'));
            let visibleCount = 0;
            
            cards.forEach(card => {
                const cardThemes = card.dataset.themes.split('|');
                const cardDest = card.dataset.destination;
                const cardDur = card.dataset.duration;
                const cardPrice = parseFloat(card.dataset.price);
                
                let themeMatch = themes.length === 0 || themes.some(t => cardThemes.includes(t));
                let destMatch = dests.length === 0 || dests.includes(cardDest);
                let durMatch = durs.length === 0 || durs.includes(cardDur);
                let priceMatch = cardPrice >= minPrice && cardPrice <= maxPrice;
                
                if (themeMatch && destMatch && durMatch && priceMatch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Sort
            const container = document.getElementById('packagesGrid');
            const noResults = document.getElementById('noResults');
            const visibleCards = cards.filter(c => !c.classList.contains('hidden'));
            
            visibleCards.sort((a, b) => {
                if (sortVal === 'price_asc') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                if (sortVal === 'price_desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                if (sortVal === 'duration_asc') return parseInt(a.dataset.days) - parseInt(b.dataset.days);
                if (sortVal === 'duration_desc') return parseInt(b.dataset.days) - parseInt(a.dataset.days);
                return 0; // Default
            });
            
            visibleCards.forEach(card => container.appendChild(card));
            container.appendChild(noResults); // keep no-results at the end
            
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
    </script>
</body>
</html>
"""
with open('includes/mobile_packages.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated includes/mobile_packages.php successfully with complete logic.")
