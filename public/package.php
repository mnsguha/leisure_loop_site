<?php
    require_once '../config/db.php';
    require_once '../config/recaptcha.php';
    $page_title = "North Sikkim Frozen Lake Tour | Leisure Loop";
    $use_recaptcha = recaptchaIsConfigured();
    $recaptcha_site_key = recaptchaSiteKey();
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/package.css">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&amp;family=Inter:wght@300;400;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

<script src="js/modules/package.js" defer></script>
<div class="dark bg-background text-on-background font-body-md selection:bg-secondary selection:text-on-secondary">
<!-- Hero Portal -->
<section class="relative h-[870px] w-full flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 bg-cover bg-center" data-alt="A cinematic, panoramic shot of a frozen Gurudongmar Lake in North Sikkim at sunset. The ice surface is cracked and crystalline, reflecting deep blue and golden hues from the sky. Massive snow-capped Himalayan peaks loom in the background under a gradient sky of indigo and orange. The atmosphere is quiet, cold, and profoundly majestic, capturing an elite, remote travel destination." style="background-image: url('images/pkg/yumthang.png')">
<div class="absolute inset-0 bg-gradient-to-b from-primary-container/40 via-primary-container/60 to-background"></div>
</div>
<div class="relative z-10 text-center px-gutter max-w-4xl">
<div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-lg border border-white/20 px-4 py-2 rounded-full mb-8">
<span class="text-secondary material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="font-label-caps text-on-surface tracking-widest">GUEST EXPERIENCES 4.8</span>
</div>
<h1 class="font-hero-title text-hero-title-mobile md:text-hero-title text-white mb-6">North Sikkim Frozen Lake Tour</h1>
<div class="flex flex-wrap justify-center gap-6 text-on-surface-variant mb-10 font-body-lg">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary">calendar_today</span>
                    05 Nights / 06 Days
                </div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary">location_on</span>
                    Sikkim, India
                </div>
</div>
<button class="bg-transparent border-2 border-secondary text-secondary px-10 py-4 rounded-full font-label-caps hover:bg-secondary hover:text-on-secondary transition-all duration-300 transform active:scale-95 shadow-[0_0_20px_rgba(233,193,118,0.2)]">
                UNLOCK BESPOKE PRICING
            </button>
</div>
</section>
<!-- Details Ribbon -->
<div class="relative z-20 max-w-container-max mx-auto px-gutter -mt-12">
<div class="glass-card rounded-full py-8 px-12 grid grid-cols-1 md:grid-cols-4 gap-8 items-center">
<div class="flex items-center gap-4 border-r border-white/10 last:border-0">
<div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined">qr_code</span>
</div>
<div>
<p class="font-label-caps text-on-surface-variant opacity-60">TOUR CODE</p>
<p class="font-bold text-white">VD-0001</p>
</div>
</div>
<div class="flex items-center gap-4 border-r border-white/10 last:border-0">
<div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined">explore</span>
</div>
<div>
<p class="font-label-caps text-on-surface-variant opacity-60">DESTINATION</p>
<p class="font-bold text-white">Sikkim</p>
</div>
</div>
<div class="flex items-center gap-4 border-r border-white/10 last:border-0">
<div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined">hiking</span>
</div>
<div>
<p class="font-label-caps text-on-surface-variant opacity-60">ESCAPE LEVEL</p>
<p class="font-bold text-white text-sm">Honeymoon, Alpine Lake</p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined">payments</span>
</div>
<div>
<p class="font-label-caps text-on-surface-variant opacity-60">DYNAMIC PRICING</p>
<p class="font-bold text-secondary">From ₹12,999 <span class="text-xs text-on-surface-variant line-through ml-1">₹18,999</span></p>
</div>
</div>
</div>
</div>
<!-- Main Content Grid -->
<main class="max-w-container-max mx-auto px-gutter py-20 flex flex-col md:flex-row gap-12">
<!-- Left: Narrative -->
<div class="flex-1 space-y-24">
<!-- Overview -->
<section>
<p class="font-label-caps text-secondary tracking-[0.3em] mb-4">THE CURATION</p>
<h2 class="font-display-lg italic text-secondary mb-8">Overview</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-on-surface-variant leading-relaxed">
<div class="flex gap-4">
<span class="material-symbols-outlined text-secondary text-sm mt-1">verified</span>
<p>Exclusive Curated Sightseeing Grid Highlights including Gurudongmar &amp; Zero Point.</p>
</div>
<div class="flex gap-4">
<span class="material-symbols-outlined text-secondary text-sm mt-1">verified</span>
<p>Premium Boutique Heritage Accommodations in Lachen &amp; Lachung.</p>
</div>
<div class="flex gap-4">
<span class="material-symbols-outlined text-secondary text-sm mt-1">verified</span>
<p>Private Luxury Chauffeur &amp; Logistics Support for the high-altitude terrain.</p>
</div>
<div class="flex gap-4">
<span class="material-symbols-outlined text-secondary text-sm mt-1">verified</span>
<p>Bespoke Local Experience Coordinator Access available 24/7.</p>
</div>
</div>
</section>
<!-- Visual Journal -->
<section>
<h2 class="font-display-md text-white mb-10">Visual Journal</h2>
<div class="grid grid-cols-2 gap-4 h-[600px]">
<div class="relative overflow-hidden rounded-2xl group mosaic-container h-full">
<img class="w-full h-full object-cover mosaic-img" data-alt="A wide-angle landscape shot of the snow-covered Lachen valley in North Sikkim. High mountains with jagged peaks surround a valley floor dusted in deep white snow. The lighting is crisp morning sun creating high contrast between the bright snow and deep shadows of the granite cliffs. The style is premium travel photography with a cold, pristine aesthetic." src="images/pkg/stitch_img_1.jpg"/>
<div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
<p class="font-accent-serif italic text-secondary text-xl">Lachen Valley Mist</p>
</div>
</div>
<div class="flex flex-col gap-4 h-full">
<div class="relative flex-1 overflow-hidden rounded-2xl group mosaic-container">
<img class="w-full h-full object-cover mosaic-img" data-alt="A close-up view of vibrant Buddhist prayer flags fluttering against a backdrop of the clear blue sky and snow-dusted Himalayan peaks in Sikkim. The colors of the flags are vivid reds, yellows, and blues. The lighting is bright and natural, evoking a sense of spirituality and cultural depth in a high-altitude setting." src="images/pkg/stitch_img_2.jpg"/>
<div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
<p class="font-accent-serif italic text-secondary text-lg">Sacred Passes</p>
</div>
</div>
<div class="relative flex-1 overflow-hidden rounded-2xl group mosaic-container">
<img class="w-full h-full object-cover mosaic-img" data-alt="A boutique heritage luxury hotel room in Sikkim with large windows overlooking the mountains. The interior is rich with dark wood, hand-woven textiles, and golden lamp lighting that creates a cozy, exclusive atmosphere against the cold mountain view outside. The focus is on the contrast between luxury comfort and wild nature." src="images/pkg/stitch_img_6.jpg"/>
<div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
<p class="font-accent-serif italic text-secondary text-lg">Heritage Stays</p>
</div>
</div>
</div>
</div>
</section>
<!-- Day-by-Day Journey -->
<section>
<h2 class="font-display-md text-white mb-10">Day-by-Day Journey</h2>
<div class="space-y-4">
<details class="group glass-card rounded-2xl p-6 open:bg-white/5 transition-all" open="">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">01</span>
<div>
<h3 class="font-bold text-lg text-white">Arrival &amp; Gangtok Transfer</h3>
<p class="text-sm text-on-surface-variant">The Capital City of Sikkim</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
A representative will be there to Welcome our guests on arrival at Bagdogra Airport/NJP Railway Station and he will be assisting for transfer our guest to Gangtok - Approx 135 kilometers 4 ½ - 5 hours drive. Gangtok is the capital city of Sikkim known for its natural beauty, exotic flora &amp; fauna, magnificent vistas, indo-tibetan food, mystic rituals at an height of 1670 meters / 5480 feet. On arrival check-in to hotel &amp; rest of the day enjoy the leisure activities of the hotel property or free to roam around famous MG Road(Shopping Arena) or satisfy taste buds by having local foods. Overnight stay at Gangtok.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">02</span>
<div>
<h3 class="font-bold text-lg text-white">Tsomgo Lake Excursion</h3>
<p class="text-sm text-on-surface-variant">Glacial Lakes &amp; Sacred Shrines</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast at hotel start for the Excursion to Tsomgo Lake nearly 40km from Gangtok. The beautiful Lake is oval shaped glacial lake, Surrounded by rugged mountains on all sides, this scenic lake is located at an altitude of 12313 ft, generally snow covered almost all year around, nearly about 50 feet deep &amp; more than 1km long &amp; home to many migratory birds. On the way to Tsomgo Lake take a halt at Kyongnosla waterfalls at Kyongnosla Alpine Sanctuary - the home to the red panda &amp; Tibetan wolf with scenic beauty of alpine trees. Nearby is the Baba Mandir (around 17 km from Tsomgo Lake)- a sacred site for all pilgrims - named after an army man Baba Harbhajan Singh who sacrificed his life for the nation. - situated at a height of 13123 ft. After visited all the locations back to Gangtok and overnight stay there. (Incase of Landslide or due to any other reasons if Tsomgo Lake is closed then an alternate sightseeing will be provided.)
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">03</span>
<div>
<h3 class="font-bold text-lg text-white">Journey to Lachung</h3>
<p class="text-sm text-on-surface-variant">Waterfalls &amp; Scenic Views</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast pick up from hotel &amp; transfer to Lachung (8,800 ft). Enroute visit Singhik View point, Seven Sister Water Fall, Naga Water Fall, and arrive Lachung by evening. Dinner at hotel. Overnight stay at Lachung.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">04</span>
<div>
<h3 class="font-bold text-lg text-white">Yumthang Valley Excursion</h3>
<p class="text-sm text-on-surface-variant">The Valley of Flowers</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After early breakfast drive up for Yumthang valley (11,800 ft. / 24 km / 2 hours) excursion tour. Yumthang, where the tree line ends and the rhododendron groves cover the landscape in a surreal shade. Yumthang also called the valley of flowers as in spring wild alpine flowers carpet the land. Yumthang is also known for its hot spring, which have healing medicinal properties. Overnight stay.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">05</span>
<div>
<h3 class="font-bold text-lg text-white">Return to Gangtok</h3>
<p class="text-sm text-on-surface-variant">Descending the Himalayas</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast drive to Gangtok. Overnight stay at Gangtok.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">06</span>
<div>
<h3 class="font-bold text-lg text-white">Departure</h3>
<p class="text-sm text-on-surface-variant">Onward Journey</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
Post breakfast at hotel, proceed for the Bagdogra Airport (IXB) / NJP Railway Station. Board your flight / train for your onward destination with cheerful memory of your holiday.
</div>
</details>
</div>
</section>
<!-- Inclusions/Exclusions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="bg-inclusion-green border border-green-500/20 rounded-3xl p-8">
<h3 class="text-green-400 font-bold text-xl mb-6 flex items-center gap-2">
<span class="material-symbols-outlined">check_circle</span>
                        Inclusions
                    </h3>
<ul class="space-y-4 text-on-surface-variant">
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                            Ultra-Premium Boutique Accommodations
                        </li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                            Private Luxury SUV (Innova Crysta/similar)
                        </li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                            Professional Experience Host
                        </li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                            VIP Permits &amp; Priority Entry Accents
                        </li>
</ul>
</div>
<div class="bg-exclusion-red border border-red-500/20 rounded-3xl p-8">
<h3 class="text-red-400 font-bold text-xl mb-6 flex items-center gap-2">
<span class="material-symbols-outlined">cancel</span>
                        Exclusions
                    </h3>
<ul class="space-y-4 text-on-surface-variant">
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-red-500 text-sm mt-1">close</span>
                            Inter-state Flight &amp; Transit Tickets
                        </li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-red-500 text-sm mt-1">close</span>
                            Personal Discretionary Expenses
                        </li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-red-500 text-sm mt-1">close</span>
                            Gratuities and Driver Incentives
                        </li>
</ul>
</div>
</section>
<!-- Terms -->
<section class="relative">
<h3 class="font-label-caps text-secondary mb-6">POLICIES &amp; TERMS</h3>
<div class="max-h-[150px] overflow-hidden relative transition-all duration-700" id="terms-container">
<div class="space-y-4 text-on-surface-variant text-sm">
<p>High Altitude Advisory: Traveling to North Sikkim involves heights above 15,000ft. Oxygen cylinders are provided in all vehicles as a standard safety measure.</p>
<p>Cancellation: Bookings cancelled 30 days prior to departure attract zero penalty. Within 15 days, 50% retention applies.</p>
<p>Permits: Restricted Area Permits (RAP) are required for foreign nationals and Protected Area Permits (PAP) for Indian citizens, processed by our concierge team 7 days in advance.</p>
</div>
<div class="absolute inset-x-0 bottom-0 h-24 bottom-fade-overlay" id="fade-overlay"></div>
</div>
<button class="appearance-none bg-transparent border-none cursor-pointer mt-4 text-secondary font-bold flex items-center gap-2 hover:gap-4 transition-all" id="terms-btn" data-action="expand-terms">
                    READ FULL POLICY <span class="material-symbols-outlined">trending_flat</span>
</button>
</section>
</div>
<!-- Right: Sticky Sidebar -->
<aside class="md:w-sidebar-width">
<div class="sticky top-28">
<div class="glass-card rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">
<div class="absolute top-0 right-0 p-8 opacity-5">
<span class="material-symbols-outlined text-9xl">support_agent</span>
</div>
<div class="relative z-10">
<p class="font-accent-serif italic text-secondary text-2xl mb-1">Private Concierge</p>
<p class="font-label-caps text-on-surface-variant text-[10px] tracking-widest mb-8">REQUEST CUSTOM QUOTATION</p>
<form class="space-y-6" id="concierge-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

<div class="space-y-1">
<label class="font-label-caps text-[10px] text-on-surface-variant">FULL GUEST NAME</label>

<label for="input_420653a2" class="sr-only">Alex Sterling</label>
<input id="input_420653a2" class="w-full bg-transparent border-0 border-b border-white/10 focus:ring-0 focus:border-secondary text-white py-2 px-0 transition-colors" placeholder="Alex Sterling" type="text"/>
</div>
<div class="space-y-1">
<label class="font-label-caps text-[10px] text-on-surface-variant">SECURE PHONE NUMBER</label>

<label for="input_40135314" class="sr-only">+44 20 7123 4567</label>
<input id="input_40135314" class="w-full bg-transparent border-0 border-b border-white/10 focus:ring-0 focus:border-secondary text-white py-2 px-0 transition-colors" placeholder="+44 20 7123 4567" type="tel"/>
</div>
<div class="space-y-1">
<label class="font-label-caps text-[10px] text-on-surface-variant">PRIVATE EMAIL ID</label>

<label for="input_ff0df1d3" class="sr-only">alex@residence.com</label>
<input id="input_ff0df1d3" class="w-full bg-transparent border-0 border-b border-white/10 focus:ring-0 focus:border-secondary text-white py-2 px-0 transition-colors" placeholder="alex@residence.com" type="email"/>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-1">
<label class="font-label-caps text-[10px] text-on-surface-variant">ADULTS</label>
<select class="w-full bg-transparent border-0 border-b border-white/10 focus:ring-0 focus:border-secondary text-white py-2 px-0 appearance-none">
<option class="bg-background">2 Guests</option>
<option class="bg-background">4 Guests</option>
<option class="bg-background">6+ Guests</option>
</select>
</div>
<div class="space-y-1">
<label class="font-label-caps text-[10px] text-on-surface-variant">DATE</label>
<input class="w-full bg-transparent border-0 border-b border-white/10 focus:ring-0 focus:border-secondary text-white py-2 px-0 [color-scheme:dark]" type="date"/>
</div>
</div>

<input type="hidden" name="enforce_recaptcha" value="1">
<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
<div class="space-y-1 mb-4" style="margin-top:1rem;">
    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
</div>
<?php endif; ?>

<button class="w-full bg-secondary text-on-secondary py-4 rounded-full font-label-caps mt-4 hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-secondary/20" type="submit">
                                REQUEST BESPOKE PRICING
                            </button>
</form>
<div class="hidden absolute inset-0 bg-background flex flex-col items-center justify-center text-center p-8 z-20" id="success-state">
<div class="w-20 h-20 bg-secondary/20 rounded-full flex items-center justify-center text-secondary mb-6 shadow-[0_0_40px_rgba(233,193,118,0.3)]">
<span class="material-symbols-outlined text-4xl">verified_user</span>
</div>
<h4 class="font-accent-serif italic text-secondary text-2xl mb-2">Connection Secure</h4>
<p class="text-on-surface-variant text-sm">A specialist will be in touch within the hour.</p>
</div>
</div>
<div class="mt-8 glass-card rounded-2xl p-6 flex items-center justify-between group cursor-pointer hover:border-secondary transition-colors">
<div class="flex items-center gap-4">
<div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
<span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">chat</span>
</div>
<p class="text-sm font-bold text-white">Direct WhatsApp</p>
</div>
<span class="material-symbols-outlined text-secondary opacity-0 group-hover:opacity-100 transition-opacity">arrow_forward_ios</span>
</div>
</div>
</aside>
</main>
<!-- Map Placeholder -->
<section class="max-w-container-max mx-auto px-gutter pb-24">
<div class="h-[500px] w-full rounded-[3rem] overflow-hidden relative glass-card">
<div class="absolute inset-0 grayscale opacity-40 bg-[url('https://images.unsplash.com/photo-1589519160732-57fc498494f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80')] bg-cover" data-alt="A dark-themed stylized map texture showing the mountainous topography of the Himalayan region in Sikkim. Topographic contour lines are faintly visible. The overall mood is mysterious and adventurous, fitting the Obsidian and Gold luxury theme."></div>
<div class="absolute inset-0 flex items-center justify-center">
<div class="text-center p-12 bg-background/80 backdrop-blur-xl border border-white/10 rounded-3xl max-w-md">
<span class="material-symbols-outlined text-secondary text-5xl mb-4">map</span>
<h3 class="text-2xl font-display-md text-white mb-2">Interactive Route Map</h3>
<p class="text-on-surface-variant">Exploring Gangtok → Lachen → Gurudongmar → Lachung → Yumthang Valley</p>
<button class="appearance-none bg-transparent border-x-0 border-t-0 mt-6 text-secondary font-label-caps border-b border-secondary pb-1 tracking-widest hover:text-white hover:border-white transition-colors cursor-pointer">LAUNCH EXPLORER</button>
</div>
</div>
</div>
</section>

</div>
<?php include '../includes/footer.php'; ?>