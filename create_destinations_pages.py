import os

destinations_php = """<?php
require_once "../config/db.php";
require_once '../config/recaptcha.php';

// Detect Mobile
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
             || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

if ($is_mobile) {
    include '../includes/mobile_destinations.php';
    exit;
}

$page_title = "Destinations | Leisure Loop";
$use_recaptcha = recaptchaIsConfigured();
$recaptcha_site_key = recaptchaSiteKey();

// Type filtering
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
$where = "is_active = 1";
$params = [];
if ($type === 'domestic' || $type === 'international') {
    $where .= " AND category = ?";
    $params[] = $type;
    $hero_title = ucfirst($type) . " Destinations";
    $hero_subtitle = "Discover the finest " . $type . " luxury experiences handpicked for you.";
} else {
    $hero_title = "Explore Destinations";
    $hero_subtitle = "Discover the world's most exquisite luxury experiences handpicked for you.";
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

include "../includes/header.php"; 
?>

<style>
/* Base Styles */
body {
    background-color: #050a14;
    color: #e5e2e2;
    font-family: 'Inter', sans-serif;
}
.catalog-hero {
    padding: 140px 0 60px;
    background: linear-gradient(to bottom, #0a0f1e 0%, #050a14 100%);
    position: relative;
    overflow: hidden;
}
.catalog-hero-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 40px;
    position: relative;
    z-index: 10;
}
.catalog-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5rem;
    color: var(--gold, #C5A059);
    margin: 0 0 16px 0;
    line-height: 1.1;
}
.catalog-hero p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.7);
    max-width: 600px;
    margin: 0;
}

/* Layout */
.catalog-layout {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 40px 80px;
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

/* Sidebar */
.catalog-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
    top: 100px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
}
.filter-group {
    padding: 24px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.filter-group:last-child {
    border-bottom: none;
}
.filter-group-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    color: var(--gold, #C5A059);
    margin: 0 0 16px 0;
}

/* Custom Checkbox / Radio */
.custom-radio-option, .custom-checkbox-option {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    cursor: pointer;
}
.custom-radio-option:last-child, .custom-checkbox-option:last-child {
    margin-bottom: 0;
}
.custom-radio-option input, .custom-checkbox-option input {
    appearance: none;
    width: 18px;
    height: 18px;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 4px;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
}
.custom-radio-option input {
    border-radius: 50%;
}
.custom-radio-option input:checked, .custom-checkbox-option input:checked {
    border-color: var(--gold, #C5A059);
    background: var(--gold, #C5A059);
}
.custom-radio-option input:checked::after, .custom-checkbox-option input:checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #000;
    border-radius: 50%;
}
.custom-radio-option input:checked::after {
    width: 8px;
    height: 8px;
}
.custom-checkbox-option input:checked::after {
    width: 10px;
    height: 10px;
    border-radius: 0;
    background: none;
    border-left: 2px solid #000;
    border-bottom: 2px solid #000;
    transform: translate(-50%, -60%) rotate(-45deg);
}
.radio-label, .checkbox-label {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
    transition: color 0.2s;
}
.custom-radio-option input:checked + .radio-label, 
.custom-checkbox-option input:checked + .checkbox-label {
    color: #fff;
    font-weight: 500;
}

/* Grid */
.packages-grid-display {
    flex: 1;
}
.catalog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}
.catalog-card-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.catalog-card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    border-color: rgba(197, 160, 89, 0.3);
}
.catalog-card-anchor {
    display: flex;
    flex-direction: column;
    height: 100%;
    text-decoration: none;
    color: inherit;
}
.catalog-card-image-shell {
    width: 100%;
    aspect-ratio: 4/3;
    position: relative;
    overflow: hidden;
}
.catalog-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.catalog-card-item:hover .catalog-card-img {
    transform: scale(1.05);
}
.days-nights-pill {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    color: var(--gold, #C5A059);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    border: 1px solid rgba(197, 160, 89, 0.3);
}
.catalog-card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.catalog-card-destination {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--gold, #C5A059);
    font-weight: 700;
    margin-bottom: 8px;
}
.catalog-card-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    margin: 0 0 12px 0;
    color: #fff;
}
.catalog-card-tagline {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.6);
    margin-bottom: 24px;
    line-height: 1.5;
    flex: 1;
}

.catalog-empty-state {
    text-align: center;
    padding: 60px 0;
    color: rgba(255,255,255,0.6);
}
</style>

<div class="catalog-hero">
    <div class="catalog-hero-content">
        <h1><?php echo htmlspecialchars($hero_title); ?></h1>
        <p><?php echo htmlspecialchars($hero_subtitle); ?></p>
    </div>
</div>

<div class="catalog-layout">
    <aside class="catalog-sidebar">
        <div class="filter-group">
            <h4 class="filter-group-title">Sort By</h4>
            <label class="custom-radio-option">
                <input type="radio" name="sort" value="default" checked onchange="applyFiltersAndSort()">
                <span class="radio-label">Curated Selection</span>
            </label>
            <label class="custom-radio-option">
                <input type="radio" name="sort" value="name_asc" onchange="applyFiltersAndSort()">
                <span class="radio-label">Name: A-Z</span>
            </label>
            <label class="custom-radio-option">
                <input type="radio" name="sort" value="name_desc" onchange="applyFiltersAndSort()">
                <span class="radio-label">Name: Z-A</span>
            </label>
        </div>

        <div class="filter-group">
            <h4 class="filter-group-title">Best Time</h4>
            <div class="checkbox-options-list">
                <?php foreach ($all_best_times as $time): ?>
                    <label class="custom-checkbox-option">
                        <input type="checkbox" class="time-checkbox" value="<?php echo htmlspecialchars(strtolower(trim($time))); ?>" onchange="applyFiltersAndSort()">
                        <span class="checkbox-label"><?php echo htmlspecialchars($time); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>

    <main class="packages-grid-display">
        <div id="packages-grid-inner" class="catalog-grid">
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
            <div class="catalog-card-item js-dest-card" 
                 data-name="<?php echo htmlspecialchars(strtolower($dest['name'])); ?>"
                 data-order="<?php echo $dest['display_order']; ?>"
                 data-times="<?php echo htmlspecialchars($times_str); ?>">
                <a href="destination-details.php?slug=<?php echo htmlspecialchars($dest['slug']); ?>" class="catalog-card-anchor">
                    <div class="catalog-card-image-shell">
                        <?php if (!empty($img)): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="catalog-card-img" alt="">
                        <?php else: ?>
                            <div class="catalog-card-img" style="background-color: #000;"></div>
                        <?php endif; ?>
                        <?php if (!empty($dest['altitude'])): ?>
                        <div class="days-nights-pill"><?php echo htmlspecialchars($dest['altitude']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="catalog-card-body">
                        <span class="catalog-card-destination"><?php echo htmlspecialchars($dest['category']); ?></span>
                        <h3 class="catalog-card-title"><?php echo htmlspecialchars($dest['name']); ?></h3>
                        <div class="catalog-card-tagline"><?php echo htmlspecialchars($dest['tagline']); ?></div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-top: auto; padding-top: 10px;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="var(--gold, #C5A059)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.05em; color: rgba(255,255,255,0.7);"><?php echo $dest['tour_count']; ?> TOURS</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div id="no-packages-placeholder" class="catalog-empty-state" style="display: none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3>No destinations found</h3>
            <p>Try adjusting your filters.</p>
        </div>
    </main>
</div>

<script>
function applyFiltersAndSort() {
    const times = Array.from(document.querySelectorAll('.time-checkbox:checked')).map(cb => cb.value);
    const sortVal = document.querySelector('input[name="sort"]:checked').value;
    
    const cards = Array.from(document.querySelectorAll('.js-dest-card'));
    let visibleCount = 0;
    
    cards.forEach(card => {
        const cardTimes = card.dataset.times.split('|');
        let timeMatch = times.length === 0 || times.some(t => cardTimes.includes(t));
        
        if (timeMatch) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    const container = document.getElementById('packages-grid-inner');
    const noResults = document.getElementById('no-packages-placeholder');
    const visibleCards = cards.filter(c => c.style.display !== 'none');
    
    visibleCards.sort((a, b) => {
        if (sortVal === 'name_asc') {
            return a.dataset.name.localeCompare(b.dataset.name);
        }
        if (sortVal === 'name_desc') {
            return b.dataset.name.localeCompare(a.dataset.name);
        }
        return parseInt(a.dataset.order) - parseInt(b.dataset.order);
    });
    
    visibleCards.forEach(card => container.appendChild(card));
    
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}
</script>

<?php include "../includes/footer.php"; ?>
"""

mobile_destinations_php = """<?php
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
    <style>
        :root { --gold: #C5A059; }
        body { background-color: #050a14; color: #fff; font-family: 'Inter', sans-serif; margin: 0; padding-bottom: 100px; -webkit-tap-highlight-color: transparent; }
        
        /* Hero Section */
        .hero { padding: 40px 16px 20px 16px; text-align: center; display: flex; flex-direction: column; align-items: center; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 2.2rem; color: #fff; margin: 0; }
        
        .btn-sort-filter { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.05); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); padding: 12px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; color: #fff; cursor: pointer; transition: background 0.3s; }
        .btn-sort-filter:active { background: rgba(255,255,255,0.1); }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 16px; }
        .card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: opacity 0.3s; }
        .card.hidden { display: none; }
        
        .card-img { width: 100%; aspect-ratio: 4/5; object-fit: cover; }
        .card-body { padding: 12px 10px; display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .card-theme { font-size: 0.6rem; color: var(--gold); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; }
        .card-title { font-size: 0.85rem; font-weight: 600; color: #fff; line-height: 1.35; margin: 0; }
        .card-tagline { font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* Modals */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        
        .bottom-sheet { position: fixed; bottom: -100%; left: 0; width: 100%; background: #0a0f1e; border-radius: 20px 20px 0 0; z-index: 101; transition: bottom 0.3s ease; padding: 24px 16px; box-shadow: 0 -10px 40px rgba(0,0,0,0.5); }
        .bottom-sheet.active { bottom: 0; }
        
        .side-sheet { position: fixed; top: 0; right: -100%; width: 100%; height: 100%; background: #0a0f1e; z-index: 101; transition: right 0.3s ease; display: flex; flex-direction: column; }
        .side-sheet.active { right: 0; }
        
        .sort-option { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff; font-size: 0.95rem; font-weight: 500; cursor: pointer; }
        .sort-option:last-child { border-bottom: none; }
        input[type="radio"], input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--gold); }
        
        .checkbox-label { display: flex; align-items: center; gap: 12px; padding: 16px; font-size: 0.95rem; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; }
        
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
        .nav-label { font-size: 0.6rem; font-weight: 600; }
        .nav-item.active .nav-label { color: var(--gold); }
        .nav-item.inactive .nav-label { color: rgba(255, 255, 255, 0.5); }
        .nav-dot { width: 4px; height: 4px; background: var(--gold); border-radius: 50%; margin-top: 2px; opacity: 0; position: absolute; bottom: -2px; }
        .nav-item.active .nav-dot { opacity: 1; }
        
        .no-results { display: none; text-align: center; padding: 40px 20px; color: rgba(255,255,255,0.6); grid-column: 1 / -1; }
    </style>
</head>
<body>

    <div class="hero">
        <h1><?php echo htmlspecialchars($hero_title); ?></h1>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin-top: 8px;"><?php echo htmlspecialchars($hero_subtitle); ?></p>
        
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
    <div class="modal-overlay" id="modalOverlay" onclick="closeAll()"></div>
    
    <!-- Sort -->
    <div class="bottom-sheet" id="sortSheet">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem; font-weight: 600;">Sort By</h3>
        <label class="sort-option">
            <span>Curated Selection</span>
            <input type="radio" name="sort" value="default" checked onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Name: A-Z</span>
            <input type="radio" name="sort" value="name_asc" onchange="applyFilters()">
        </label>
        <label class="sort-option">
            <span>Name: Z-A</span>
            <input type="radio" name="sort" value="name_desc" onchange="applyFilters()">
        </label>
    </div>
    
    <!-- Filter -->
    <div class="side-sheet" id="filterSheet">
        <div style="padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Filter</h3>
            <button onclick="closeAll()" style="background: none; border: none; color: #fff; font-size: 0.85rem; font-weight: 600;">Close</button>
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
            <button onclick="clearFilters()" style="flex: 1; padding: 12px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-weight: 600;">Clear</button>
            <button onclick="applyFiltersAndClose()" style="flex: 1; padding: 12px; background: var(--gold); border: none; color: #000; font-weight: 700; border-radius: 8px;">Apply</button>
        </div>
    </div>

    <!-- Nav -->
    <nav class="mobile-nav-pill">
        <a href="index.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span class="nav-label">Home</span>
            <div class="nav-dot"></div>
        </a>
        <a href="destinations.php" class="nav-item active">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"></path></svg>
            <span class="nav-label">Places</span>
            <div class="nav-dot"></div>
        </a>
        <a href="packages.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            <span class="nav-label">Tours</span>
            <div class="nav-dot"></div>
        </a>
        <a href="contact.php" class="nav-item inactive">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            <span class="nav-label">Chat</span>
            <div class="nav-dot"></div>
        </a>
        <button type="button" class="nav-item inactive" onclick="alert('Menu')">
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
        function clearFilters() {
            document.querySelectorAll('.filter-time').forEach(el => el.checked = false);
            applyFilters();
        }
        function applyFiltersAndClose() {
            applyFilters();
            closeAll();
        }
        function applyFilters() {
            const times = Array.from(document.querySelectorAll('.filter-time:checked')).map(el => el.value);
            const sortVal = document.querySelector('input[name="sort"]:checked').value;
            
            const cards = Array.from(document.querySelectorAll('.js-card'));
            let visibleCount = 0;
            
            cards.forEach(card => {
                const cardTimes = card.dataset.times.split('|');
                let timeMatch = times.length === 0 || times.some(t => cardTimes.includes(t));
                
                if (timeMatch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            const container = document.getElementById('destGrid');
            const noResults = document.getElementById('noResults');
            const visibleCards = cards.filter(c => !c.classList.contains('hidden'));
            
            visibleCards.sort((a, b) => {
                if (sortVal === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
                if (sortVal === 'name_desc') return b.dataset.name.localeCompare(a.dataset.name);
                return parseInt(a.dataset.order) - parseInt(b.dataset.order);
            });
            
            visibleCards.forEach(card => container.appendChild(card));
            container.appendChild(noResults);
            
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

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(destinations_php)

with open('includes/mobile_destinations.php', 'w', encoding='utf-8') as f:
    f.write(mobile_destinations_php)

print("Generated public/destinations.php and includes/mobile_destinations.php successfully.")
