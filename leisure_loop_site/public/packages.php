<?php 
    require_once '../config/db.php';
    require_once '../includes/functions.php';
    
    $theme_filter = isset($_GET['theme']) ? trim($_GET['theme']) : '';
    $dest_filter = isset($_GET['destination']) ? trim($_GET['destination']) : '';
    
    $page_title = "Curated Experiences | Leisure Loop Trip";
    if (!empty($theme_filter)) {
        $page_title = htmlspecialchars($theme_filter) . " Escapes | Leisure Loop Trip";
    } elseif (!empty($dest_filter)) {
        $page_title = htmlspecialchars($dest_filter) . " Signature Tours | Leisure Loop Trip";
    }
    
    include '../includes/header.php'; 
    
    // Fetch all active packages
    $packages = [];
    if ($pdo) {
        $packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
    }
    
    // Extract unique dynamic categories for filters
    $all_themes = [];
    $all_destinations = [];
    $all_durations = [];
    $max_price_in_db = 0;
    $min_price_in_db = 999999;
    
    foreach ($packages as $pkg) {
        // Themes
        if (!empty($pkg['tour_type'])) {
            $types = explode(',', $pkg['tour_type']);
            foreach ($types as $t) {
                $t = trim($t);
                if ($t !== '' && !in_array($t, $all_themes)) {
                    $all_themes[] = $t;
                }
            }
        }
        
        // Destinations
        if (!empty($pkg['destination'])) {
            $d = trim($pkg['destination']);
            if (!in_array($d, $all_destinations)) {
                $all_destinations[] = $d;
            }
        }
        
        // Durations
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
        
        // Price bounds
        $price = (float)$pkg['price'];
        if ($price > $max_price_in_db) {
            $max_price_in_db = $price;
        }
        if ($price < $min_price_in_db) {
            $min_price_in_db = $price;
        }
    }
    
    // Fallbacks if db is empty
    if (empty($packages)) {
        $min_price_in_db = 0;
        $max_price_in_db = 50000;
    }
    
    // Sort filters
    sort($all_themes);
    sort($all_destinations);
    
    uasort($all_durations, function($a, $b) {
        return $a['nights'] <=> $b['nights'];
    });
?>

    <!-- The Curation Portfolio Intro -->
    <header class="catalog-hero-banner">
        <div class="container">
            <span class="section-label"><?php echo !empty($theme_filter) ? 'Theme Curation' : 'The Portfolio'; ?></span>
            <h1 class="section-title">
                Extraordinary <br><span class="serif">Escapes.</span>
            </h1>
            <p class="section-subtitle">
                Explore our ultra-luxury signature collections curated specifically for high-altitude Himalayan retreats and coastal slow-travel.
            </p>
        </div>
    </header>

    <!-- Main Catalog Section -->
    <section class="section" style="padding-top: 2rem; padding-bottom: 8rem;">
        <div class="container">
            <div class="packages-catalog-layout">
                
                <!-- Left Sidebar Column: Glassmorphic Filter & Sort Panel -->
                <aside class="filters-sidebar-panel">
                    <div class="sidebar-sticky-shell">
                        <div class="sidebar-header">
                            <span class="sidebar-title">Filter by</span>
                            <button class="btn-clear-all" onclick="resetAllFilters();">Clear All</button>
                        </div>
                        
                        <!-- 1. Sort Section -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">Sort Collection</h4>
                            <div class="sort-options-list">
                                <label class="custom-radio-option">
                                    <input type="radio" name="catalog_sort" value="default" checked onchange="applyFiltersAndSort();">
                                    <span class="radio-label">Curated Selection</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="catalog_sort" value="price_asc" onchange="applyFiltersAndSort();">
                                    <span class="radio-label">Price: Low to High</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="catalog_sort" value="price_desc" onchange="applyFiltersAndSort();">
                                    <span class="radio-label">Price: High to Low</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="catalog_sort" value="duration_asc" onchange="applyFiltersAndSort();">
                                    <span class="radio-label">Duration: Shortest First</span>
                                </label>
                                <label class="custom-radio-option">
                                    <input type="radio" name="catalog_sort" value="duration_desc" onchange="applyFiltersAndSort();">
                                    <span class="radio-label">Duration: Longest First</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- 2. Theme Section -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">Theme</h4>
                            <div class="checkbox-options-list">
                                <?php foreach ($all_themes as $theme): 
                                    $isSelected = (strtolower($theme_filter) === strtolower($theme)) ? 'checked' : '';
                                ?>
                                    <label class="custom-checkbox-option">
                                        <input type="checkbox" class="theme-checkbox" value="<?php echo htmlspecialchars($theme); ?>" <?php echo $isSelected; ?> onchange="applyFiltersAndSort();">
                                        <span class="checkbox-label"><?php echo htmlspecialchars($theme); ?> Tours</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 3. Destination Section -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">Destination</h4>
                            <div class="checkbox-options-list">
                                <?php foreach ($all_destinations as $dest): 
                                    $isSelected = (strtolower($dest_filter) === strtolower($dest)) ? 'checked' : '';
                                ?>
                                    <label class="custom-checkbox-option">
                                        <input type="checkbox" class="dest-checkbox" value="<?php echo htmlspecialchars($dest); ?>" <?php echo $isSelected; ?> onchange="applyFiltersAndSort();">
                                        <span class="checkbox-label"><?php echo htmlspecialchars($dest); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- 4. Duration Section -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">Duration</h4>
                            <div class="checkbox-options-list">
                                <?php foreach ($all_durations as $key => $dur): ?>
                                    <label class="custom-checkbox-option">
                                        <input type="checkbox" class="duration-checkbox" value="<?php echo $key; ?>" onchange="applyFiltersAndSort();">
                                        <span class="checkbox-label"><?php echo htmlspecialchars($dur['label']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 5. Price Inputs Section -->
                        <div class="filter-group" style="border-bottom: none; padding-bottom: 0;">
                            <h4 class="filter-group-title">Price Range</h4>
                            <div class="price-input-row">
                                <div class="price-field">
                                    <span class="currency-symbol">&#8377;</span>
                                    <input type="number" id="price_min" placeholder="<?php echo (int)$min_price_in_db; ?>" min="0">
                                </div>
                                <div class="price-field-divider">&ndash;</div>
                                <div class="price-field">
                                    <span class="currency-symbol">&#8377;</span>
                                    <input type="number" id="price_max" placeholder="<?php echo (int)$max_price_in_db; ?>" min="0">
                                </div>
                            </div>
                            <div class="price-action-buttons">
                                <button class="btn-price-apply" onclick="applyFiltersAndSort();">Apply</button>
                                <button class="btn-price-clear" onclick="clearPriceFilter();">Clear</button>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Column: Package Cards Grid -->
                <main class="packages-grid-display">
                    <!-- Dynamic active filters chip banner -->
                    <div id="active-chips-container" class="active-chips-row" style="display: none;">
                        <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; font-weight: 500;">Active Filters:</span>
                        <div id="active-chips-list" class="chips-flex"></div>
                    </div>
                    
                    <div id="packages-grid-inner" class="catalog-grid">
                        <?php foreach ($packages as $pkg): 
                            $nights = (int)($pkg['nights'] ?? 0);
                            $days = (int)($pkg['days'] ?? 0);
                            $price = (float)$pkg['price'];
                            $origPrice = !empty($pkg['original_price']) ? (float)$pkg['original_price'] : null;
                            
                            // Map theme strings for DOM data elements
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
                        
                        <div class="catalog-card-item js-catalog-card"
                             data-id="<?php echo $pkg['id']; ?>"
                             data-title="<?php echo htmlspecialchars(strtolower($pkg['title'])); ?>"
                             data-price="<?php echo $price; ?>"
                             data-destination="<?php echo htmlspecialchars(strtolower(trim($pkg['destination']))); ?>"
                             data-themes="<?php echo htmlspecialchars($themes_data); ?>"
                             data-duration="<?php echo $duration_key; ?>"
                             data-days="<?php echo $days; ?>"
                             data-nights="<?php echo $nights; ?>"
                             data-created="<?php echo strtotime($pkg['created_at']); ?>">
                             
                            <a href="package-detail.php?slug=<?php echo $pkg['slug']; ?>" class="catalog-card-anchor">
                                <div class="catalog-card-image-shell">
                                    <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>" class="catalog-card-img">
                                    <div class="catalog-card-badge-overlay">
                                        <span class="days-nights-pill"><?php echo sprintf("%02d N / %02d D", $nights, $days); ?></span>
                                    </div>
                                </div>
                                <div class="catalog-card-body">
                                    <span class="catalog-card-destination"><?php echo htmlspecialchars($pkg['destination']); ?> Escapes</span>
                                    <h3 class="catalog-card-title"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                                    
                                    <div class="catalog-card-footer">
                                        <div class="catalog-card-pricing">
                                            <?php if ($origPrice && $origPrice > $price): ?>
                                                <span class="catalog-card-original-price">&#8377;<?php echo number_format($origPrice); ?></span>
                                            <?php endif; ?>
                                            <span class="catalog-card-selling-price">&#8377;<?php echo number_format($price); ?></span>
                                        </div>
                                        <div class="catalog-card-action-btn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- No Packages Placeholder -->
                    <div id="no-packages-placeholder" class="catalog-empty-state" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <p>No bespoke collections match your selected parameters.</p>
                        <button class="btn-gold" style="margin-top: 1.5rem; padding: 0.8rem 2rem; font-size: 0.8rem; letter-spacing: 0.1em; border-radius: 50px;" onclick="resetAllFilters();">Reset Filters</button>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Client-Side Reactive Filter & Sort Engine -->
    <script>
    function applyFiltersAndSort() {
        const activeThemes = Array.from(document.querySelectorAll('.theme-checkbox:checked')).map(cb => cb.value.toLowerCase());
        const activeDests = Array.from(document.querySelectorAll('.dest-checkbox:checked')).map(cb => cb.value.toLowerCase());
        const activeDurs = Array.from(document.querySelectorAll('.duration-checkbox:checked')).map(cb => cb.value);
        
        const priceMinInput = document.getElementById('price_min').value;
        const priceMaxInput = document.getElementById('price_max').value;
        const priceMin = priceMinInput !== '' ? parseFloat(priceMinInput) : 0;
        const priceMax = priceMaxInput !== '' ? parseFloat(priceMaxInput) : Infinity;
        
        const sortVal = document.querySelector('input[name="catalog_sort"]:checked').value;
        
        const cards = Array.from(document.querySelectorAll('.js-catalog-card'));
        let visibleCount = 0;
        
        // 1. Filter logic
        cards.forEach(card => {
            const cardPrice = parseFloat(card.getAttribute('data-price'));
            const cardDest = card.getAttribute('data-destination');
            const cardThemes = card.getAttribute('data-themes').split('|');
            const cardDur = card.getAttribute('data-duration');
            
            let matchTheme = true;
            if (activeThemes.length > 0) {
                matchTheme = cardThemes.some(t => activeThemes.includes(t));
            }
            
            let matchDest = true;
            if (activeDests.length > 0) {
                matchDest = activeDests.includes(cardDest);
            }
            
            let matchDur = true;
            if (activeDurs.length > 0) {
                matchDur = activeDurs.includes(cardDur);
            }
            
            let matchPrice = (cardPrice >= priceMin && cardPrice <= priceMax);
            
            if (matchTheme && matchDest && matchDur && matchPrice) {
                card.style.display = 'block';
                // Add a small delay for premium fade animation
                setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 20);
                visibleCount++;
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                setTimeout(() => { card.style.display = 'none'; }, 250);
            }
        });
        
        // 2. Sort logic (only reorders DOM elements among currently visible/enabled ones)
        const gridInner = document.getElementById('packages-grid-inner');
        
        cards.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price'));
            const priceB = parseFloat(b.getAttribute('data-price'));
            const daysA = parseInt(a.getAttribute('data-days'));
            const daysB = parseInt(b.getAttribute('data-days'));
            const createdA = parseInt(a.getAttribute('data-created'));
            const createdB = parseInt(b.getAttribute('data-created'));
            
            if (sortVal === 'price_asc') {
                return priceA - priceB;
            } else if (sortVal === 'price_desc') {
                return priceB - priceA;
            } else if (sortVal === 'duration_asc') {
                return daysA - daysB;
            } else if (sortVal === 'duration_desc') {
                return daysB - daysA;
            } else {
                // Default: newest first
                return createdB - createdA;
            }
        });
        
        // Re-append sorted elements in order
        cards.forEach(card => gridInner.appendChild(card));
        
        // 3. Render Empty placeholder
        const emptyState = document.getElementById('no-packages-placeholder');
        if (visibleCount === 0) {
            emptyState.style.display = 'flex';
        } else {
            emptyState.style.display = 'none';
        }
        
        // 4. Update dynamic filter chips
        updateFilterChips(activeThemes, activeDests, activeDurs, priceMinInput, priceMaxInput);
    }
    
    function updateFilterChips(themes, dests, durs, minPr, maxPr) {
        const container = document.getElementById('active-chips-container');
        const list = document.getElementById('active-chips-list');
        list.innerHTML = '';
        
        let hasFilters = false;
        
        themes.forEach(t => {
            createChip(t + ' Tours', () => {
                const cb = Array.from(document.querySelectorAll('.theme-checkbox')).find(c => c.value.toLowerCase() === t);
                if (cb) { cb.checked = false; applyFiltersAndSort(); }
            });
            hasFilters = true;
        });
        
        dests.forEach(d => {
            createChip(d.charAt(0).toUpperCase() + d.slice(1), () => {
                const cb = Array.from(document.querySelectorAll('.dest-checkbox')).find(c => c.value.toLowerCase() === d);
                if (cb) { cb.checked = false; applyFiltersAndSort(); }
            });
            hasFilters = true;
        });
        
        durs.forEach(key => {
            const cb = Array.from(document.querySelectorAll('.duration-checkbox')).find(c => c.value === key);
            if (cb) {
                const labelText = cb.nextElementSibling.textContent;
                createChip(labelText, () => {
                    cb.checked = false;
                    applyFiltersAndSort();
                });
                hasFilters = true;
            }
        });
        
        if (minPr !== '' || maxPr !== '') {
            const minLabel = minPr !== '' ? '₹' + minPr : '₹0';
            const maxLabel = maxPr !== '' ? '₹' + maxPr : 'Max';
            createChip(`${minLabel} - ${maxLabel}`, clearPriceFilter);
            hasFilters = true;
        }
        
        container.style.display = hasFilters ? 'flex' : 'none';
    }
    
    function createChip(text, onRemove) {
        const list = document.getElementById('active-chips-list');
        const chip = document.createElement('div');
        chip.className = 'active-filter-chip';
        chip.innerHTML = `
            <span>${text}</span>
            <button class="chip-remove-btn">&times;</button>
        `;
        chip.querySelector('.chip-remove-btn').addEventListener('click', onRemove);
        list.appendChild(chip);
    }
    
    function clearPriceFilter() {
        document.getElementById('price_min').value = '';
        document.getElementById('price_max').value = '';
        applyFiltersAndSort();
    }
    
    function resetAllFilters() {
        document.querySelectorAll('.theme-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.dest-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.duration-checkbox').forEach(cb => cb.checked = false);
        document.querySelector('input[name="catalog_sort"][value="default"]').checked = true;
        clearPriceFilter();
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        // Initial setup for cards opacity transition
        document.querySelectorAll('.js-catalog-card').forEach(c => {
            c.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            c.style.opacity = '1';
            c.style.transform = 'translateY(0)';
        });
        
        // Execute initial filter check (e.g. if arriving with ?theme= Honeymoon from navbar)
        applyFiltersAndSort();
    });
    </script>

<?php include '../includes/footer.php'; ?>
