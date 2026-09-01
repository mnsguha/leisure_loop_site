<?php
require_once '../config/db.php';
require_once '../config/recaptcha.php';

$use_recaptcha = recaptchaIsConfigured();
$recaptcha_site_key = recaptchaSiteKey();

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Detect Mobile
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
             || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

if ($is_mobile) {
    include '../includes/mobile_destination_details.php';
    exit;
}

if (!$pdo) {
    die("Database connection failed.");
}


// Function to wrap each letter in a span for advanced GSAP split-text animation
function split_to_spans($string) {
    $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    $out = '';
    foreach ($chars as $char) {
        if (trim($char) === '') {
            $out .= ' ';
        } else {
            $out .= "<span class='char anim-char'>$char</span>";
        }
    }
    return $out;
}

// Fetch destination details
try {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE slug = :slug AND is_active = 1");
    $stmt->execute(['slug' => $slug]);
    $destination = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}

if (!$destination) {
    header('Location: index.php');
    exit;
}

// Fetch related packages
$related_packages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE (destination = :name OR title LIKE :like_name) AND is_active = 1 GROUP BY title LIMIT 6");
    $stmt->execute(['name' => $destination['name'], 'like_name' => '%' . $destination['name'] . '%']);
    $related_packages = $stmt->fetchAll();
} catch (PDOException $e) {
    $related_packages = [];
}

$sightseeing = json_decode($destination['sightseeing_json'], true) ?: [];
$parallax_layers = json_decode($destination['parallax_layers_json'] ?? '[]', true) ?: [];
$local_experiences = json_decode($destination['local_experiences_json'] ?? '[]', true) ?: [];

$page_title = htmlspecialchars($destination['name']) . " | Elite Travel Experiences";
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/destination-details.css">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="js/tailwind-config.js"></script>
<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script src="js/modules/destination-details.js" defer></script>



<div class="font-body-md text-body-md overflow-x-hidden bg-background w-full">



<!-- Premium ParableVC-Style Multi-Layer Hero -->
<section class="parable-hero">
    <?php if (empty($parallax_layers)): ?>
        <?php if (!empty($destination['hero_video_url'])): ?>
            <video autoplay loop muted playsinline class="p-layer p-layer-hero-media" data-depth="0.10">
                <source src="<?php echo htmlspecialchars($destination['hero_video_url']); ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <img src="<?php echo htmlspecialchars($destination['cover_image'] ?? 'images/placeholder.jpg'); ?>" class="p-layer p-layer-hero-media" data-depth="0.10">
        <?php endif; ?>
    <?php else: ?>
        <?php foreach ($parallax_layers as $layer): ?>
            <img src="<?php echo htmlspecialchars($layer['image']); ?>" 
                 class="p-layer <?php echo htmlspecialchars($layer['class'] ?? ''); ?>" 
                 data-depth="<?php echo htmlspecialchars($layer['depth'] ?? '0.2'); ?>" 
                 style="<?php echo htmlspecialchars($layer['style'] ?? ''); ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="p-layer p-layer-text">
        <span class="hero-tagline-text"><?php echo htmlspecialchars($destination['tagline'] ?: 'HIMALAYAN MAJESTY'); ?></span>
        <h1 class="hero-title-text"><?php echo strtoupper(htmlspecialchars($destination['name'])); ?></h1>
    </div>

    <!-- Darjeeling Toy Train Smoke Puffs -->
    <?php if (strtolower($slug) === 'darjeeling'): ?>
        <div class="p-layer-train-puff">
            <div class="train-puff puff-1"></div>
            <div class="train-puff puff-2"></div>
            <div class="train-puff puff-3"></div>
            <div class="train-puff puff-4"></div>
        </div>
    <?php endif; ?>

    <!-- Sikkim Valley Clouds Canvas & Doors -->
    <?php if (strtolower($slug) === 'sikkim'): ?>
        <div class="cloud-reveal-container">
            <div class="cloud-door cloud-door-left"></div>
            <div class="cloud-door cloud-door-right"></div>
        </div>
        <canvas id="valleyClouds" class="p-layer p-layer-valley-clouds" data-depth="0.10"></canvas>
    <?php endif; ?>

    <!-- Floating Custom Clouds for Sikkim and Darjeeling -->
    <?php if (in_array(strtolower($slug), ['sikkim', 'darjeeling'])): ?>
        <img src="images/parallax/custom_cloud_1.webp" class="floating-cloud fc-1" alt="cloud">
        <img src="images/parallax/custom_cloud_2.webp" class="floating-cloud fc-2" alt="cloud">
        <img src="images/parallax/custom_cloud_3.webp" class="floating-cloud fc-3" alt="cloud">
    <?php endif; ?>

    <div class="scroll-indicator cinematic-indicator">
        <div class="mouse"></div>
        <p>Scroll to Explore</p>
    </div>
</section>



<!-- Quick Facts Bar -->
<div class="facts-bar">
<div class="glass-card facts-bar-inner">

    <div class="fact-item">
        <span class="material-symbols-outlined fact-icon">mountain_flag</span>
        <div class="text-left">
            <span class="fact-label">Altitude</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['altitude'] ?: 'Varied'); ?></span>
        </div>
    </div>

    <div class="fact-item">
        <span class="material-symbols-outlined fact-icon">calendar_month</span>
        <div class="text-left">
            <span class="fact-label">Best Time</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['best_time'] ?: 'Year Round'); ?></span>
        </div>
    </div>

    <div class="fact-item no-border-bottom-right">
        <span class="material-symbols-outlined fact-icon">schedule</span>
        <div class="text-left">
            <span class="fact-label">Duration</span>
            <span class="fact-value"><?php echo htmlspecialchars($destination['duration'] ?: 'Custom'); ?></span>
        </div>
    </div>

</div>
</div>

<!-- The Narrative (Editorial Section) -->
<section class="relative py-section-padding bg-background" id="narrative" class="z-10">
<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-12 gap-gutter items-stretch">
<div class="md:col-span-5 relative order-2 md:order-1 mt-12 md:mt-0" id="narrative-left-col">
<div class="z-20 pb-12 narrative-pin-target">
<div class="relative aspect-[3/4] w-full" id="narrative-cascade-container">
<!-- Static Hit Zones to prevent animation looping -->
<div class="absolute w-2/3 h-2/3 z-50 cursor-pointer hit-zone top-0 left-0" data-zone="0"></div>
<div class="absolute w-2/3 h-2/3 z-40 cursor-pointer hit-zone top-[16.66%] left-[16.66%]" data-zone="1"></div>
<div class="absolute w-2/3 h-2/3 z-30 cursor-pointer hit-zone top-[33.33%] left-[33.33%]" data-zone="2"></div>

<div data-pos="0" class="narrative-img-card w-[80%] h-[80%] md:w-2/3 md:h-2/3 reveal-scale-hidden stagger-2 overflow-hidden rounded-2xl border border-glass-border shadow-2xl">
<img alt="Narrative Image 1" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($destination['story_narrative_image'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>"/>
</div>
<div data-pos="1" class="narrative-img-card w-[80%] h-[80%] md:w-2/3 md:h-2/3 reveal-scale-hidden stagger-3 overflow-hidden rounded-2xl border border-glass-border shadow-2xl">
<img alt="Narrative Image 2" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($destination['story_narrative_image_2'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>"/>
</div>
<div data-pos="2" class="narrative-img-card w-[80%] h-[80%] md:w-2/3 md:h-2/3 reveal-scale-hidden stagger-4 overflow-hidden rounded-2xl border border-glass-border shadow-2xl">
<img alt="Narrative Image 3" class="w-full h-full object-cover" src="<?php echo htmlspecialchars($destination['story_narrative_image_3'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg')); ?>"/>
</div>
</div>


</div>
</div>
<div class="md:col-span-7 md:pl-16 order-1 md:order-2 relative h-full flex flex-col pt-4 md:pt-0">
    
    <div class="relative z-10 w-full">
        <!-- Destination Name Watermark (Match "Most Coveted" style — first word only) -->
        <?php
            $dest_words      = preg_split('/[\s&]+/', $destination['name']);
            $watermark_word  = strtoupper(trim($dest_words[0]));
        ?>
        <div class="watermark-text" aria-hidden="true"><?php echo htmlspecialchars($watermark_word); ?></div>

        <!-- Gold Accent Line -->
        <div class="w-[2px] h-16 bg-gradient-to-b from-secondary to-transparent mb-6 reveal-hidden stagger-1 relative z-10"></div>
        
        <span class="font-label-caps text-label-caps text-secondary mb-4 block reveal-hidden stagger-2 uppercase tracking-[0.4em] relative z-10">Editorial</span>
        
        <!-- Enlarged Heading -->
        <h2 class="font-headline-lg text-5xl md:text-7xl text-on-surface mb-12 reveal-hidden stagger-3 italic tracking-tight drop-shadow-sm relative z-10">THE NARRATIVE</h2>
        
        <!-- Text with Drop Cap -->
        <div class="space-y-6 text-on-surface-variant leading-relaxed text-lg reveal-hidden stagger-4 relative z-10">
            <p class="first-letter:text-7xl first-letter:font-headline-accent first-letter:text-secondary first-letter:float-left first-letter:mr-4 first-letter:mt-1 first-letter:leading-[0.8] first-line:tracking-widest first-line:uppercase">
                <?php echo nl2br(htmlspecialchars($destination['description_long'] ?: 'Discover ' . $destination['name'] . ' with our exclusive journeys.')); ?>
            </p>
        </div>
    </div>
</div>
</div>
</section>

<!-- Sightseeing Highlights -->
<?php if (!empty($sightseeing)): ?>
<section class="py-section-padding bg-background relative overflow-hidden" id="sightseeing">
    <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="text-center mb-16 md:mb-24">
            <span class="font-label-caps text-label-caps text-secondary mb-4 block reveal-hidden stagger-1 uppercase tracking-[0.4em]">Must Visit</span>
            <h2 class="font-headline-lg text-4xl md:text-5xl text-on-surface italic reveal-hidden stagger-2 drop-shadow-sm">Sightseeing Highlights</h2>
        </div>
        
        <div class="flex flex-col gap-16 md:gap-24">
            <?php foreach ($sightseeing as $index => $spot): ?>
                <?php 
                    $isReverse = ($index % 2 !== 0); 
                    $delay = ($index % 3) + 1;
                ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
                    <div class="group relative overflow-hidden rounded-3xl shadow-2xl glass-card tour-card-hover border-none aspect-[4/3] reveal-hidden stagger-<?php echo $delay; ?> <?php echo $isReverse ? 'md:order-last' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($spot['image']); ?>" alt="<?php echo htmlspecialchars($spot['title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-background/30 to-transparent pointer-events-none"></div>
                    </div>
                    <div class="reveal-hidden stagger-<?php echo $delay + 1; ?> px-4 md:px-0">
                        <div class="w-12 h-1 bg-secondary mb-6 rounded-full opacity-70"></div>
                        <h3 class="font-headline-accent text-3xl md:text-4xl text-on-surface mb-6 italic leading-tight"><?php echo htmlspecialchars($spot['title']); ?></h3>
                        <p class="text-on-surface-variant leading-relaxed text-lg opacity-90"><?php echo nl2br(htmlspecialchars($spot['desc'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Signature Journeys (Tours) -->
<section class="py-section-padding bg-gradient-to-b from-background via-surface-container-low to-background" id="journeys">
<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">
<div class="flex flex-col items-center text-center justify-center mb-16 gap-4">
<div class="reveal-hidden stagger-1">
<span class="font-label-caps text-label-caps text-secondary mb-2 block uppercase tracking-widest">Curated Collections</span>
<h2 class="font-headline-lg text-4xl md:text-5xl text-on-surface italic drop-shadow-sm">SIGNATURE JOURNEYS</h2>
</div>
<p class="max-w-2xl text-on-surface-variant reveal-hidden stagger-2 mt-2">Handpicked experiences designed for the discerning traveler seeking deep immersion and unparalleled comfort.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<?php if (!empty($related_packages)): ?>
    <?php $delay = 1; ?>
    <?php foreach ($related_packages as $pkg): ?>
    <!-- Tour Card -->
    <div class="group relative overflow-hidden rounded-3xl glass-card tour-card-hover border-none shadow-xl reveal-hidden stagger-<?php echo $delay; ?>">
        <div class="aspect-[4/5] relative overflow-hidden">
            <?php 
                $img_src = !empty($pkg['image_url']) ? htmlspecialchars($pkg['image_url']) : 'assets/images/placeholder_tour.jpg';
            ?>
            <img alt="<?php echo htmlspecialchars($pkg['title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" src="<?php echo $img_src; ?>"/>
            <div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>
        </div>
        <div class="absolute bottom-0 p-8 w-full">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-headline-accent text-headline-accent text-on-surface leading-tight italic"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                <span class="text-secondary font-bold">₹<?php echo number_format($pkg['price'], 0); ?></span>
            </div>
            <div class="flex items-center gap-6 mb-8 text-on-surface-variant text-sm">
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">schedule</span> <?php echo htmlspecialchars($pkg['days'] ?? ''); ?> Days</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]"><?php echo (strtolower($pkg['tour_type'] ?? '') == 'wellness') ? 'spa' : ((strtolower($pkg['tour_type'] ?? '') == 'cultural') ? 'history_edu' : 'group'); ?></span> <?php echo htmlspecialchars($pkg['tour_type'] ?? 'Private'); ?></span>
            </div>
            <a href="package-detail.php?slug=<?php echo htmlspecialchars($pkg['slug']); ?>" class="block text-center w-full bg-secondary text-on-secondary py-4 rounded-xl font-label-caps text-label-caps hover:bg-opacity-90 transition-all uppercase tracking-widest no-underline">Plan This Journey</a>
        </div>
    </div>
    <?php $delay = ($delay % 4) + 1; ?>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-span-full text-center text-on-surface-variant py-12">
        <p>No exclusive journeys are currently available for this destination.</p>
    </div>
<?php endif; ?>
</div>
</div>
</section>

<!-- Local Experiences -->
<section class="py-section-padding bg-background overflow-hidden" id="local-experiences">
<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">
<div class="text-center mb-16">
<span class="font-label-caps text-label-caps text-secondary mb-4 block reveal-hidden stagger-1 uppercase tracking-[0.4em]">LOCAL EXPERIENCES</span>
<h2 class="font-headline-lg text-on-surface italic uppercase reveal-hidden stagger-2">Immersive Encounters</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<?php if (!empty($local_experiences)): ?>
    <?php $delay = 1; foreach ($local_experiences as $exp): ?>
    <div class="glass-card p-10 rounded-[2rem] flex flex-col items-center text-center reveal-hidden stagger-<?php echo $delay; ?>">
        <div class="w-16 h-16 rounded-full bg-secondary/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-secondary text-3xl"><?php echo htmlspecialchars($exp['icon']); ?></span>
        </div>
        <h3 class="font-headline-accent text-headline-accent text-on-surface mb-4 italic"><?php echo htmlspecialchars($exp['title']); ?></h3>
        <p class="text-on-surface-variant leading-relaxed"><?php echo htmlspecialchars($exp['desc']); ?></p>
    </div>
    <?php $delay++; endforeach; ?>
<?php else: ?>
    <div class="col-span-full text-center text-on-surface-variant py-8">
        <p>Experiences coming soon.</p>
    </div>
<?php endif; ?>
</div>
</div>
</section>
<!-- Elite Enquiry Form Section -->

<section class="relative py-section-padding bg-background flex items-center justify-center overflow-hidden">

<div class="absolute inset-0 opacity-20 pointer-events-none">

<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-secondary blur-[150px] rounded-full animate-pulse"></div>

<div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary blur-[150px] rounded-full animate-pulse pulse-circle"></div>

</div>

<div class="relative z-10 w-full max-w-2xl px-margin-mobile reveal-hidden stagger-1">

<div class="glass-card p-6 md:p-12 rounded-[2.5rem] shadow-2xl border border-secondary/20">

<div class="text-center mb-10">

<span class="font-label-caps text-label-caps text-secondary uppercase tracking-[0.4em] mb-4 block">Bespoke Travel</span>

<h2 class="font-headline-lg text-headline-lg text-on-surface italic">Plan Your Elite Journey</h2>

</div>

<form class="space-y-6">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">


<input type="hidden" name="enforce_recaptcha" value="1">
<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">person</span>


<label for="input_adec1cb7" class="sr-only">Your Full Name</label>
<input id="input_adec1cb7" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Your Full Name" type="text"/>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">call</span>


<label for="input_0279a427" class="sr-only">Phone Number</label>
<input id="input_0279a427" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Phone Number" type="tel"/>

</div>

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">mail</span>


<label for="input_23cce6f8" class="sr-only">Email Address</label>
<input id="input_23cce6f8" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Email Address" type="email"/>

</div>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">calendar_today</span>


<label for="input_7ada7880" class="sr-only">Travel Date</label>
<input id="input_7ada7880" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Travel Date" type="text"/>

</div>

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">group</span>

<select class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none appearance-none">

<option disabled="" selected="">Number of Guests</option>

<option>1 Guest</option>

<option>2 Guests</option>

<option>3-5 Guests</option>

<option>5+ Guests</option>

</select>

</div>

</div>

<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
<div class="space-y-1 mb-4 mt-4">
    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
</div>
<?php endif; ?>

<button class="w-full bg-gradient-to-r from-secondary to-[#A3864A] text-on-secondary font-bold py-5 rounded-xl text-lg hover:shadow-[0_0_30px_rgba(233,193,118,0.4)] transition-all transform active:scale-[0.98] uppercase tracking-widest" type="submit">

                        Submit Enquiry

                    </button>

</form>

<p class="text-center mt-6 text-on-surface-variant text-xs font-label-caps tracking-widest">A TRAVEL SPECIALIST WILL CONTACT YOU WITHIN 24 HOURS</p>

</div>

</div>

</section>

</div>



<!-- Full-Screen Cinematic Modal (Option 2) -->

<div id="story-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8 pointer-events-none opacity-0">

    <!-- Backdrop -->

    <div class="absolute inset-0 bg-background/90 backdrop-blur-xl transition-opacity"></div>

    

    <!-- Modal Content -->

    <div class="relative w-full max-w-6xl h-full max-h-[90vh] bg-surface-container-low rounded-3xl overflow-hidden shadow-2xl flex flex-col  transform scale-95 origin-center" id="story-modal-content">

        <!-- Header / Close Button -->

        <div class="absolute top-0 left-0 w-full p-6 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">

            <span class="font-label-caps text-secondary tracking-widest text-sm">THE NARRATIVE</span>

            <button id="close-modal-btn" data-action="close-story-modal" class="text-white hover:text-secondary transition-colors p-2 bg-black/30 rounded-full backdrop-blur-md">

                <span class="material-symbols-outlined">close</span>

            </button>

        </div>



        <!-- Scrollable Body -->

        <div class="overflow-y-auto w-full h-full custom-scrollbar">

            <!-- Hero Image for Modal -->

            <div class="w-full h-64 md:h-96 relative">
                <?php 
                    $modal_hero = $destination['cover_image'] ?: 'images/placeholder.jpg';
                ?>
                <img src="<?php echo htmlspecialchars($modal_hero); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low to-transparent"></div>
            </div>

            

            <!-- Modal Editorial Content -->

            <div class="p-8 md:p-16 max-w-4xl mx-auto -mt-32 relative z-10">

                <h2 class="font-headline-lg text-4xl md:text-6xl text-white italic mb-12 drop-shadow-lg"><?php echo htmlspecialchars($destination['name']); ?> &mdash; The Story</h2>

                

                <div class="space-y-8 text-on-surface-variant text-lg leading-relaxed font-body-md">

                    <p class="text-xl text-white/90 font-medium">

                        <?php echo htmlspecialchars($destination['name']); ?> is a sanctuary where time moves at the pace of spinning prayer wheels and drifting clouds.

                    </p>

                    <p>

                        Beyond the mist-drenched valleys, <?php echo htmlspecialchars($destination['name']); ?> offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.

                    </p>

                    

                    <div class="grid grid-cols-2 gap-4 my-12">
                        <?php 
                            $img2 = $destination['story_narrative_image_2'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg');
                            $img3 = $destination['story_narrative_image_3'] ?: ($destination['cover_image'] ?: 'images/placeholder.jpg');
                        ?>
                        <img src="<?php echo htmlspecialchars($img2); ?>" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">
                        <img src="<?php echo htmlspecialchars($img3); ?>" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">
                    </div>



                    <p>

                        Every element of your stay&mdash;from the thread count of your linens to the vintage of your evening wine&mdash;is selected to harmonize with the raw, untamed beauty outside your window. Here, luxury is defined not just by opulence, but by exclusive access to authentic, transformative experiences.

                    </p>

                </div>

                

                <div class="mt-16 text-center border-t border-white/10 pt-12">

                    <button id="close-modal-bottom" data-action="close-story-modal" class="font-label-caps text-secondary tracking-widest text-sm hover:text-white transition-colors">RETURN TO DESTINATION</button>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
