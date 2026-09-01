<?php
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
<link rel="stylesheet" href="css/destinations.css">


<!-- 1. Panoramic EaseMyTrip-Inspired Hero Banner -->
<section class="emt-dest-hero">
    <div class="emt-hero-content">
        <div class="hero-luxury-badge">
            <span>✨</span> PRIVATE CURATED SANCTUARIES
        </div>
        <h1><?php echo htmlspecialchars($hero_title); ?></h1>
        <p class="hero-tagline"><?php echo htmlspecialchars($hero_subtitle); ?></p>
        
        <!-- Interactive Capsule Search Dock -->
        <div class="emt-search-dock-wrapper">
            <div class="emt-search-dock">
                <div class="dock-field field-where">
                    <span class="material-symbols-outlined dock-field-icon">place</span>
                    <div class="dock-field-content">
                        <span class="dock-label">Where To?</span>
                        
<label for="destSearchInput" class="sr-only">Search destination, region or vibe...</label>
<input type="text" id="destSearchInput" class="dock-input" placeholder="Search destination, region or vibe...">
                    </div>
                </div>
                
                <div class="dock-divider"></div>
                
                <div class="dock-field field-when">
                    <span class="material-symbols-outlined dock-field-icon">calendar_month</span>
                    <div class="dock-field-content">
                        <span class="dock-label">Travel Season</span>
                        <select id="destSeasonSelect" class="dock-select" data-change="apply-filters">
                            <option value="">Any Season / All Year</option>
                            <?php foreach ($all_best_times as $time): ?>
                                <option value="<?php echo htmlspecialchars(strtolower(trim($time))); ?>"><?php echo htmlspecialchars($time); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="button" class="dock-search-btn" data-action="apply-filters" title="Explore Destinations">
                    <span class="material-symbols-outlined">search</span> Explore
                </button>
            </div>
        </div>
    </div>
</section>

<!-- 2. Quick-Discovery Theme Ribbon Bar -->
<section class="quick-discovery-section">
    <div class="quick-discovery-ribbon">
        <button type="button" class="discovery-chip active" data-filter="all" data-action="quick-chip" data-val="all">
            <span>🌟</span> All Collections
        </button>
        <button type="button" class="discovery-chip" data-filter="domestic" data-action="quick-chip" data-val="domestic">
            <span>🏔️</span> Domestic Sanctuaries
        </button>
        <button type="button" class="discovery-chip" data-filter="international" data-action="quick-chip" data-val="international">
            <span>🌴</span> International Paradises
        </button>
        <button type="button" class="discovery-chip" data-filter="summer" data-action="quick-chip" data-val="summer">
            <span>☀️</span> Summer & Monsoon
        </button>
        <button type="button" class="discovery-chip" data-filter="winter" data-action="quick-chip" data-val="winter">
            <span>❄️</span> Winter & Autumn
        </button>
    </div>
</section>

<!-- Breadcrumb Strip & Live Counter -->
<div class="catalog-breadcrumb catalog-breadcrumb-extended">
    <div class="catalog-breadcrumb-links">
        <a href="index.php">Home</a>
        <span>&gt;</span>
        <?php if (!empty($type)): ?>
            <a href="destinations.php">Destinations</a>
            <span>&gt;</span>
            <span class="catalog-breadcrumb-active"><?php echo htmlspecialchars($hero_title); ?></span>
        <?php else: ?>
            <span class="catalog-breadcrumb-active">Destinations</span>
        <?php endif; ?>
    </div>
    <span id="resultsCountBadge" class="results-count-pill"><?php echo count($destinations); ?> Sanctuaries</span>
</div>

<!-- 3. Main Catalog Layout -->
<div class="catalog-layout">
    <main class="packages-grid-display catalog-grid-main">
        <!-- Destination Cards Grid -->
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
                $dest_category_clean = strtolower(trim($dest['category']));
            ?>
            <div class="catalog-card-item js-dest-card" 
                 data-name="<?php echo htmlspecialchars(strtolower($dest['name'])); ?>"
                 data-category="<?php echo htmlspecialchars($dest_category_clean); ?>"
                 data-order="<?php echo $dest['display_order']; ?>"
                 data-tagline="<?php echo htmlspecialchars(strtolower($dest['tagline'] ?? '')); ?>"
                 data-times="<?php echo htmlspecialchars($times_str); ?>">
                <a href="destination-details.php?slug=<?php echo htmlspecialchars($dest['slug']); ?>" class="catalog-card-anchor">
                    <div class="catalog-card-image-shell">
                        <?php if (!empty($img)): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="catalog-card-img" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                        <?php else: ?>
                            <div class="catalog-card-img catalog-card-img-placeholder"></div>
                        <?php endif; ?>
                        <?php if (!empty($dest['altitude'])): ?>
                        <div class="days-nights-pill">📍 <?php echo htmlspecialchars($dest['altitude']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="catalog-card-body">
                        <span class="catalog-card-destination">
                            <span>✦</span> <?php echo htmlspecialchars($dest['category']); ?> SANCTUARY
                        </span>
                        <h3 class="catalog-card-title"><?php echo htmlspecialchars($dest['name']); ?></h3>
                        <div class="catalog-card-tagline"><?php echo htmlspecialchars($dest['tagline'] ?? 'Immerse yourself in world-class luxury and scenic Himalayan grandeur.'); ?></div>
                        <div class="card-footer-strip">
                            <div class="tours-count">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="var(--gold, #C5A059)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?php echo $dest['tour_count']; ?> SIGNATURE TOURS</span>
                            </div>
                            <span class="explore-link-btn">EXPLORE &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Empty State Placeholder -->
        <div id="no-packages-placeholder" class="catalog-empty-state is-hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold, #C5A059)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="catalog-empty-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
            <h3 class="catalog-empty-title">No matching destinations found</h3>
            <p class="catalog-empty-text">We couldn't find any journeys matching your exact search parameters. Please try broadening your filter criteria or search terms.</p>
            <button type="button" data-action="reset-filters" class="catalog-empty-btn">Reset Filters</button>
        </div>
    </main>
</div>

<script src="js/modules/destinations.js?v=1787348699" defer></script>

<?php include "../includes/footer.php"; ?>

