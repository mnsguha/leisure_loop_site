<?php
require_once '../config/db.php';
$packages_mobile = [];
if (isset($pdo)) {
    $type_filter_val = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
    $active_tab = isset($_GET['package_type']) && $_GET['package_type'] === 'fixed' ? 'fixed' : (isset($_GET['package_type']) && $_GET['package_type'] === 'curated' ? 'curated' : '');
    $query = "SELECT * FROM packages WHERE is_active = 1";
    if ($type_filter_val === 'domestic') {
        $query .= " AND is_international = 0";
    } elseif ($type_filter_val === 'international') {
        $query .= " AND is_international = 1";
    }
    $query .= " ORDER BY created_at DESC";
    $packages_mobile = $pdo->query($query)->fetchAll();
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

if (!empty($dest_filter)) {
    $found = false;
    foreach ($all_destinations as $d) {
        if (strcasecmp($d, $dest_filter) === 0) { $found = true; break; }
    }
    if (!$found) $all_destinations[] = $dest_filter;
}
if (!empty($theme_filter)) {
    $found_t = false;
    foreach ($all_themes as $t) {
        if (strcasecmp($t, $theme_filter) === 0) { $found_t = true; break; }
    }
    if (!$found_t) $all_themes[] = $theme_filter;
}

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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/mobile-views.css">
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
        
        <div style="position: absolute; top: max(20px, env(safe-area-inset-top, 20px)); left: 16px; z-index: 20;">
            <a aria-label="Link" href="index.php" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); cursor: pointer; text-decoration: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
        </div>

        <div style="position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; width: 100%;">
            <h1>Explore Tours</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.95rem; margin-top: 8px;">Find your perfect luxury escape.</p>
        </div>
    </div>

    <!-- Themes Pill Menu -->
    <div style="margin: 24px 0 16px 0;">
        <?php
        $default_icon_mobile = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
        $theme_counts_mobile = [];
        $theme_meta_mobile = [];
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
                        'icon' => !empty($cat['icon_svg']) ? $cat['icon_svg'] : $default_icon_mobile
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
        
        $current_theme = isset($_GET['theme']) ? strtolower(trim($_GET['theme'])) : '';
        ?>
        <?php if (!empty($theme_counts_mobile)): ?>
        <div style="overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 8px; scroll-snap-type: x mandatory;">
            <div style="display: inline-flex; gap: 8px; padding-left: 16px; padding-right: 16px;">
                <?php foreach ($theme_counts_mobile as $name => $count): 
                    $meta = isset($theme_meta_mobile[$name]) ? $theme_meta_mobile[$name] : ['icon' => $default_icon_mobile];
                    $is_active = (strtolower($name) === $current_theme);
                ?>
                <a href="packages.php?theme=<?php echo urlencode($name); ?><?php echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : ''; ?>" 
                   style="scroll-snap-align: start; display: flex; align-items: center; gap: 6px; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 6px 14px; text-decoration: none; flex-shrink: 0; transition: background 0.3s; <?php echo $is_active ? 'background: var(--gold); color: #000;' : 'background: rgba(255,255,255,0.05); color: #fff;'; ?>">
                    <span style="width: 14px; height: 14px; display: flex; align-items: center; justify-content: center; <?php echo $is_active ? 'color: #000;' : 'color: var(--gold);'; ?>">
                        <?php echo $meta['icon']; ?>
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.03em;"><?php echo htmlspecialchars($name); ?></span>
                    <span style="font-size: 0.65rem; font-weight: 400; margin-left: 2px; <?php echo $is_active ? 'color: rgba(0,0,0,0.6);' : 'color: rgba(255,255,255,0.5);'; ?>">(<?php echo $count; ?>)</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
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
           data-days="<?php echo $days; ?>"
           data-package-type="<?php echo htmlspecialchars(strtolower(trim($pkg['package_type'] ?? 'curated'))); ?>">
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
        
        <div class="no-results" id="noResults" style="display: none; text-align: center; padding: 40px 16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 56px; height: 56px; margin: 0 auto 16px auto;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3 id="noResultsTitle" style="font-size: 1.15rem; font-weight: 600; color: #fff; margin-bottom: 8px;">No packages found</h3>
            <p id="noResultsSub" style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 20px;">Try adjusting your filters or request a custom itinerary from our specialists.</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button data-action="clear-filters" style="padding: 10px 20px; font-size: 0.8rem; font-weight: 700; border: none; border-radius: 50px; background: var(--gold); color: #000;">Reset Filters</button>
                <button data-href="contact.php" style="padding: 10px 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid var(--gold); border-radius: 50px; background: transparent; color: var(--gold);">Plan Custom Trip</button>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-overlay" id="modalOverlay" data-action="close-all" role="button" aria-label="Close filter panel"></div>
    
    <!-- Bottom Sheet: Sort -->
    <div class="bottom-sheet" id="sortSheet">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Sort By</h3>
        <label class="sort-option">
            <span>Curated Selection</span>
            <input type="radio" name="sort" value="default" checked data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Price: Low to High</span>
            <input type="radio" name="sort" value="price_asc" data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Price: High to Low</span>
            <input type="radio" name="sort" value="price_desc" data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Duration: Shortest First</span>
            <input type="radio" name="sort" value="duration_asc" data-change="apply-filters">
        </label>
        <label class="sort-option">
            <span>Duration: Longest First</span>
            <input type="radio" name="sort" value="duration_desc" data-change="apply-filters">
        </label>
    </div>
    
    <!-- Side/Full Sheet: Filter -->
    <div class="side-sheet" id="filterSheet">
        <!-- Header -->
        <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Filters</h3>
            <button data-action="close-all" style="background: none; border: none; color: #fff; font-size: 0.85rem; font-weight: 600; cursor: pointer;" aria-label="Close filter panel">Close</button>
        </div>
        
        <!-- Dual Pane Body -->
        <div class="filter-body">
            <div class="filter-left">
                <div class="filter-tab active" data-action="switch-filter" data-pane="pane-theme">Theme</div>
                <div class="filter-tab" data-action="switch-filter" data-pane="pane-dest">Destination</div>
                <div class="filter-tab" data-action="switch-filter" data-pane="pane-dur">Duration</div>
                <div class="filter-tab" data-action="switch-filter" data-pane="pane-price">Price</div>
                <div class="filter-tab" data-action="switch-filter" data-pane="pane-type">Package Type</div>
            </div>
            <div class="filter-right">
                <!-- Theme Pane -->
                <div class="filter-pane active" id="pane-theme">
                    <?php foreach ($all_themes as $t): 
                        $is_t_chk = (isset($theme_filter) && strcasecmp($theme_filter, $t) === 0) ? 'checked' : '';
                    ?>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-theme" value="<?php echo htmlspecialchars(strtolower(trim($t))); ?>" <?php echo $is_t_chk; ?>>
                        <span><?php echo htmlspecialchars($t); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <!-- Destination Pane -->
                <div class="filter-pane" id="pane-dest">
                    <?php foreach ($all_destinations as $d): 
                        $is_d_chk = (isset($dest_filter) && strcasecmp($dest_filter, $d) === 0) ? 'checked' : '';
                    ?>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-dest" value="<?php echo htmlspecialchars(strtolower(trim($d))); ?>" <?php echo $is_d_chk; ?>>
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
                        
<label for="price_min" class="sr-only">Min</label>
<input type="number" id="price_min" placeholder="Min" min="0">
                        <span style="color: rgba(255,255,255,0.5);">-</span>
                        
<label for="price_max" class="sr-only">Max</label>
<input type="number" id="price_max" placeholder="Max" min="0">
                    </div>
                </div>
                
                <!-- Package Type Pane -->
                <div class="filter-pane" id="pane-type">
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-type" value="curated" <?php echo ($active_tab === 'curated') ? 'checked' : ''; ?>>
                        <span>Curated</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" class="filter-type" value="fixed" <?php echo ($active_tab === 'fixed') ? 'checked' : ''; ?>>
                        <span>Fixed</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Footer Buttons -->
        <div style="padding: 16px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 12px; background: #0a0f1e;">
            <button data-action="clear-filters" style="flex: 1; padding: 12px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-weight: 600;">Clear Filters</button>
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
