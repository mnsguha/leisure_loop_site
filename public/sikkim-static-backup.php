<?php 
require_once "../config/db.php";
require_once '../config/recaptcha.php';
$page_title = "Sikkim | Elite Travel Experiences";
$use_recaptcha = recaptchaIsConfigured();
$recaptcha_site_key = recaptchaSiteKey();
include "../includes/header.php";
?>
<link rel="stylesheet" href="css/home.css">


<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&amp;family=Inter:wght@400;500&amp;family=Montserrat:wght@600&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

<script src="js/modules/home.js" defer></script>



<div class="font-body-md text-body-md overflow-x-hidden bg-background w-full">



<!-- Premium ParableVC-Style Multi-Layer Hero for Sikkim -->

    <section class="parable-hero" style="position: relative; width: 100%; height: 100vh; overflow: hidden; background: #e0f0ff; z-index: 1;">

        <!-- Cloud Reveal Animation overlay -->

        <div class="cloud-reveal-container">

            <div class="cloud-door cloud-door-left"></div>

            <div class="cloud-door cloud-door-right"></div>

        </div>

        <!-- Stacked Layers for ParableVC Mouse-Move & Scroll Parallax -->

        <img src="images/parallax/sunset_sky.png" class="p-layer p-layer-sky" data-depth="0.05" alt="Sky" style="position: absolute; inset: -10%; width: 120%; height: 120%; object-fit: cover; z-index: 1; pointer-events: none;">

        

        <!-- Canvas for animated valley clouds (between sky and mountains) -->

        <canvas id="valleyClouds" class="p-layer" data-depth="0.10" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 1; pointer-events: none; mix-blend-mode: screen; opacity: 0.8;"></canvas>



        <img src="images/parallax/sunset_mountains.png" class="p-layer p-layer-back" data-depth="0.15" alt="Mountains" style="position: absolute; left: -5%; bottom: -10%; width: 110%; height: 120%; object-fit: cover; z-index: 2; pointer-events: none;">



        <!-- Typography sandwiched between layers -->

        <div class="p-layer p-layer-text" data-depth="0.2" style="position: absolute; top: 30%; left: 0; width: 100%; text-align: center; z-index: 3; pointer-events: none;">

            <span style="font-size: 1.5rem; color: #fff; letter-spacing: 0.3em; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 1rem; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">HIMALAYAN MAJESTY</span>

            <h1 style="font-family: var(--font-serif); font-size: clamp(6rem, 15vw, 12rem); line-height: 1; color: #fff; text-shadow: 0 10px 30px rgba(0,0,0,0.5); margin: 0;">SIKKIM</h1>

        </div>



        <img src="images/parallax/sunset_village.png" class="p-layer p-layer-mid" data-depth="0.4" alt="Village" style="position: absolute; left: -5%; bottom: -15%; width: 110%; height: 125%; object-fit: cover; z-index: 4; pointer-events: none; transform-origin: center bottom;">

        

        <!-- 3D Roaming Clouds (multiple for depth) -->

        <div class="p-layer" data-depth="0.5" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;">

            <img src="images/parallax/custom_cloud_1.png" alt="Cloud" class="cloud-img-1">

        </div>

        <div class="p-layer" data-depth="0.7" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 6; pointer-events: none;">

            <img src="images/parallax/custom_cloud_2.png" alt="Cloud" class="cloud-img-2">

        </div>

        <div class="p-layer" data-depth="0.6" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;">

            <img src="images/parallax/custom_cloud_3.png" alt="Cloud" class="cloud-img-3">

        </div>



        <img src="images/parallax/sunset_flags.png" class="p-layer p-layer-foremost" data-depth="0.9" alt="Prayer Flags" style="position: absolute; left: -10%; bottom: -30%; width: 120%; height: 150%; object-fit: cover; z-index: 7; pointer-events: none; transform-origin: center bottom;">

        

        <div class="scroll-indicator cinematic-indicator" style="z-index: 9;">

            <div class="mouse"></div>

            <p>Scroll to Explore</p>

        </div>

    </section>

    <!-- Spacer to allow parallax scrolling before the next section overlaps -->

    <div class="parable-spacer" style="height: 150vh; width: 100%; position: relative; z-index: 0; pointer-events: none;"></div>

<!-- Quick Facts Bar -->

<div class="relative max-w-6xl mx-auto px-margin-mobile z-40 -mt-16 mb-16" >

<div class="glass-card rounded-2xl p-6 flex flex-wrap justify-between items-center gap-8 md:gap-4 shadow-2xl border border-secondary/20">

<div class="flex flex-1 items-center justify-center gap-3 border-r border-secondary/10 px-4 last:border-0">

<span class="material-symbols-outlined text-secondary">mountain_flag</span>

<div class="text-left">

<span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Altitude</span>

<span class="font-body-md text-on-surface font-semibold">3500m+</span>

</div>

</div>

<div class="flex flex-1 items-center justify-center gap-3 border-r border-secondary/10 px-4 last:border-0">

<span class="material-symbols-outlined text-secondary">calendar_month</span>

<div class="text-left">

<span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Best Time</span>

<span class="font-body-md text-on-surface font-semibold">Oct-May</span>

</div>

</div>

<div class="flex flex-1 items-center justify-center gap-3 px-4 last:border-0">

<span class="material-symbols-outlined text-secondary">schedule</span>

<div class="text-left">

<span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Duration</span>

<span class="font-body-md text-on-surface font-semibold">7-10 Days</span>

</div>

</div>

</div>

</div>



<!-- The Narrative (Editorial Section) -->

<section class="relative py-section-padding bg-background overflow-hidden" id="narrative" style="z-index: 10;">

<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">

<div class="md:col-span-5 relative order-2 md:order-1 mt-12 md:mt-0">

<div class="relative aspect-[3/4] w-full">

<div class="absolute top-0 left-0 w-4/5 h-4/5 z-20 reveal-scale-hidden stagger-2 overflow-hidden rounded-2xl border border-glass-border shadow-2xl">

<img alt="Monastery" class="w-full h-full object-cover" src="images/stitch/stitch_img_5.jpg"/>

</div>

<div class="absolute bottom-0 right-0 w-3/4 h-3/4 z-10 reveal-scale-hidden stagger-4 overflow-hidden rounded-2xl border border-glass-border translate-y-8 -translate-x-4 shadow-2xl">

<img alt="Tea Garden" class="w-full h-full object-cover" src="images/stitch/stitch_img_3.jpg"/>

</div>

</div>

</div>

<div class="md:col-span-7 md:pl-12 order-1 md:order-2">

<span class="font-label-caps text-label-caps text-secondary mb-4 block reveal-hidden stagger-1 uppercase tracking-widest">Editorial</span>

<h2 class="font-headline-lg text-headline-lg text-on-surface mb-8 reveal-hidden stagger-2 italic">THE NARRATIVE</h2>

<div class="space-y-6 text-on-surface-variant leading-relaxed text-lg reveal-hidden stagger-3">

<p>

                        Sikkim is a symphony of Himalayan culture and ethereal landscapes. It is a place where the air is thinner but the soul feels fuller. From the ancient Rumtek Monastery, where the echoes of chanting resonate through ornate halls, to the pristine valleys of Lachung, every footstep tells a story of mountain heritage.

                    </p>

<p>

                        In this enclave of serenity, luxury is not just about the amenities; it's about the <span class="italic text-secondary">exclusive access</span> to the hidden corners of the world. Imagine waking up to a private view of Kanchenjunga, followed by a curated tea tasting session.

                    </p>

</div>



<div id="extended-story" class="overflow-hidden" style="height: 0px; opacity: 0; transition: all 1.2s cubic-bezier(0.215, 0.61, 0.355, 1);">

    <div class="pt-8 space-y-6 text-on-surface-variant leading-relaxed text-lg">

        <p>

            Beyond the mist-drenched valleys, Sikkim offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.

        </p>

        <div class="w-full h-64 md:h-96 rounded-2xl overflow-hidden mt-8 mb-8 relative shadow-2xl border border-secondary/20">

            <img src="images/stitch/stitch_img_1.jpg" alt="Gurudongmar Lake" class="w-full h-full object-cover object-center filter brightness-75 hover:brightness-100 transition-all duration-700 hover:scale-105" />

            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent pointer-events-none"></div>

            <div class="absolute bottom-6 left-6 text-white font-label-caps tracking-widest text-xs opacity-80">UNTOUCHED WILDERNESS</div>

        </div>

        <p>

            Every element of your stay—from the thread count of your linens to the vintage of your evening wine—is selected to harmonize with the raw, untamed beauty outside your window.

        </p>

    </div>

</div>

<div class="mt-10 flex flex-wrap gap-12 reveal-hidden stagger-4">

    <!-- Option 3 (Inline) -->

    <a href="javascript:void(0)" data-action="toggle-inline-story" id="explore-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">

        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>

        <span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (INLINE)</span>

    </a>



    <!-- Option 2 (Modal) -->

    <a href="javascript:void(0)" data-action="open-story-modal" id="explore-modal-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">

        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>

        <span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (MODAL)</span>

    </a>

</div>

</div>

</div>

</section>

<!-- Signature Journeys (Tours) -->

<section class="py-section-padding bg-gradient-to-b from-background via-surface-container-low to-background" id="journeys">

<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">

<div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-4">

<div class="reveal-hidden stagger-1">

<span class="font-label-caps text-label-caps text-secondary mb-2 block uppercase tracking-widest">Curated Collections</span>

<h2 class="font-headline-lg text-headline-lg text-on-surface italic">SIGNATURE JOURNEYS</h2>

</div>

<p class="max-w-md text-on-surface-variant reveal-hidden stagger-2">Handpicked experiences designed for the discerning traveler seeking deep immersion and unparalleled comfort.</p>

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">

<!-- Tour Card 1 -->

<div class="group relative overflow-hidden rounded-3xl glass-card tour-card-hover border-none shadow-xl reveal-hidden stagger-1">

<div class="aspect-[4/5] relative overflow-hidden">

<img alt="North Sikkim" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" src="images/stitch/stitch_img_1.jpg"/>

<div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>

</div>

<div class="absolute bottom-0 p-8 w-full">

<div class="flex justify-between items-start mb-4">

<h3 class="font-headline-accent text-headline-accent text-on-surface leading-tight italic">North Sikkim Frozen Lake</h3>

<span class="text-secondary font-bold">₹12,999</span>

</div>

<div class="flex items-center gap-6 mb-8 text-on-surface-variant text-sm">

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">schedule</span> 8 Days</span>

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">group</span> Private</span>

</div>

<a href="https://wa.me/918918921629?text=Hi!%20I%20am%20interested%20in%20planning%20the%20North%20Sikkim%20Frozen%20Lake%20journey." target="_blank" class="block text-center w-full bg-secondary text-on-secondary py-4 rounded-xl font-label-caps text-label-caps hover:bg-opacity-90 transition-all uppercase tracking-widest" style="text-decoration:none;">Plan This Journey</a>

</div>

</div>

<!-- Tour Card 2 -->

<div class="group relative overflow-hidden rounded-3xl glass-card tour-card-hover border-none shadow-xl reveal-hidden stagger-2">

<div class="aspect-[4/5] relative overflow-hidden">

<img alt="Gangtok Retreat" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" src="images/stitch/stitch_img_2.jpg"/>

<div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>

</div>

<div class="absolute bottom-0 p-8 w-full">

<div class="flex justify-between items-start mb-4">

<h3 class="font-headline-accent text-headline-accent text-on-surface leading-tight italic">Gangtok &amp; Darjeeling Retreat</h3>

<span class="text-secondary font-bold">₹15,499</span>

</div>

<div class="flex items-center gap-6 mb-8 text-on-surface-variant text-sm">

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">schedule</span> 6 Days</span>

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">spa</span> Wellness</span>

</div>

<a href="https://wa.me/918918921629?text=Hi!%20I%20am%20interested%20in%20planning%20the%20Gangtok%20%26%20Darjeeling%20Retreat%20journey." target="_blank" class="block text-center w-full bg-secondary text-on-secondary py-4 rounded-xl font-label-caps text-label-caps hover:bg-opacity-90 transition-all uppercase tracking-widest" style="text-decoration:none;">Plan This Journey</a>

</div>

</div>

<!-- Tour Card 3 -->

<div class="group relative overflow-hidden rounded-3xl glass-card tour-card-hover border-none shadow-xl reveal-hidden stagger-3">

<div class="aspect-[4/5] relative overflow-hidden">

<img alt="West Sikkim" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" src="images/stitch/stitch_img_6.jpg"/>

<div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>

</div>

<div class="absolute bottom-0 p-8 w-full">

<div class="flex justify-between items-start mb-4">

<h3 class="font-headline-accent text-headline-accent text-on-surface leading-tight italic">West Sikkim Monastery Trail</h3>

<span class="text-secondary font-bold">₹10,999</span>

</div>

<div class="flex items-center gap-6 mb-8 text-on-surface-variant text-sm">

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">schedule</span> 10 Days</span>

<span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">history_edu</span> Cultural</span>

</div>

<a href="https://wa.me/918918921629?text=Hi!%20I%20am%20interested%20in%20planning%20the%20West%20Sikkim%20Monastery%20Trail%20journey." target="_blank" class="block text-center w-full bg-secondary text-on-secondary py-4 rounded-xl font-label-caps text-label-caps hover:bg-opacity-90 transition-all uppercase tracking-widest" style="text-decoration:none;">Plan This Journey</a>

</div>

</div>

</div>

</div>

</section>

<!-- Local Experiences (preserved from SCREEN 25) -->

<section class="py-section-padding bg-background overflow-hidden" id="local-experiences">

<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">

<div class="text-center mb-16">

<span class="font-label-caps text-label-caps text-secondary mb-4 block reveal-hidden stagger-1 uppercase tracking-[0.4em]">LOCAL EXPERIENCES</span>

<h2 class="font-headline-lg text-on-surface italic uppercase reveal-hidden stagger-2">Immersive Encounters</h2>

</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">

<div class="glass-card p-10 rounded-[2rem] flex flex-col items-center text-center reveal-hidden stagger-1">

<div class="w-16 h-16 rounded-full bg-secondary/10 flex items-center justify-center mb-6">

<span class="material-symbols-outlined text-secondary text-3xl">local_cafe</span>

</div>

<h3 class="font-headline-accent text-headline-accent text-on-surface mb-4 italic">Private Tea Tasting</h3>

<p class="text-on-surface-variant leading-relaxed">Discover the heritage of Temi Tea Garden with a master sommelier.</p>

</div>

<div class="glass-card p-10 rounded-[2rem] flex flex-col items-center text-center reveal-hidden stagger-2">

<div class="w-16 h-16 rounded-full bg-secondary/10 flex items-center justify-center mb-6">

<span class="material-symbols-outlined text-secondary text-3xl">self_improvement</span>

</div>

<h3 class="font-headline-accent text-headline-accent text-on-surface mb-4 italic">Monastic Meditation</h3>

<p class="text-on-surface-variant leading-relaxed">Join a morning prayer session at Rumtek Monastery for spiritual clarity.</p>

</div>

<div class="glass-card p-10 rounded-[2rem] flex flex-col items-center text-center reveal-hidden stagger-3">

<div class="w-16 h-16 rounded-full bg-secondary/10 flex items-center justify-center mb-6">

<span class="material-symbols-outlined text-secondary text-3xl">palette</span>

</div>

<h3 class="font-headline-accent text-headline-accent text-on-surface mb-4 italic">Artisan Workshops</h3>

<p class="text-on-surface-variant leading-relaxed">Learn traditional Thangka painting from local masters in Gangtok.</p>

</div>

</div>

</div>

</section>



<!-- Elite Enquiry Form Section -->

<section class="relative py-section-padding bg-background flex items-center justify-center overflow-hidden">

<div class="absolute inset-0 opacity-20 pointer-events-none">

<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-secondary blur-[150px] rounded-full animate-pulse"></div>

<div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary blur-[150px] rounded-full animate-pulse" style="animation-delay: 2s"></div>

</div>

<div class="relative z-10 w-full max-w-2xl px-margin-mobile reveal-hidden stagger-1">

<div class="glass-card p-12 rounded-[2.5rem] shadow-2xl border border-secondary/20">

<div class="text-center mb-10">

<span class="font-label-caps text-label-caps text-secondary uppercase tracking-[0.4em] mb-4 block">Bespoke Travel</span>

<h2 class="font-headline-lg text-headline-lg text-on-surface italic">Plan Your Elite Journey</h2>

</div>

<form class="space-y-6">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">


<input type="hidden" name="enforce_recaptcha" value="1">
<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">person</span>


<label for="input_9abeb42a" class="sr-only">Your Full Name</label>
<input id="input_9abeb42a" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Your Full Name" type="text"/>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">call</span>


<label for="input_95fe636e" class="sr-only">Phone Number</label>
<input id="input_95fe636e" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Phone Number" type="tel"/>

</div>

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">mail</span>


<label for="input_ce847304" class="sr-only">Email Address</label>
<input id="input_ce847304" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" placeholder="Email Address" type="email"/>

</div>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<div class="relative group">

<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">calendar_today</span>


<label for="input_a0e6474b" class="sr-only">Travel Date</label>
<input id="input_a0e6474b" class="w-full bg-surface-container-low/50 border border-glass-border rounded-xl py-4 pl-12 pr-4 text-on-surface focus:border-secondary focus:ring-0 transition-all outline-none" data-focus="eval:(this.type='date')" placeholder="Travel Date" type="text"/>

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
<div class="space-y-1 mb-4" style="margin-top:1rem;">
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

<div id="story-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8 pointer-events-none" style="opacity: 0;">

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

                <img src="images/stitch/stitch_img_2.jpg" alt="Sikkim Culture" class="w-full h-full object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low to-transparent"></div>

            </div>

            

            <!-- Modal Editorial Content -->

            <div class="p-8 md:p-16 max-w-4xl mx-auto -mt-32 relative z-10">

                <h2 class="font-headline-lg text-4xl md:text-6xl text-white italic mb-12 drop-shadow-lg">Whispers of the Himalayas</h2>

                

                <div class="space-y-8 text-on-surface-variant text-lg leading-relaxed font-body-md">

                    <p class="text-xl text-white/90 font-medium">

                        Sikkim is a sanctuary where time moves at the pace of spinning prayer wheels and drifting clouds.

                    </p>

                    <p>

                        Beyond the mist-drenched valleys, Sikkim offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.

                    </p>

                    

                    <div class="grid grid-cols-2 gap-4 my-12">

                        <img src="images/stitch/stitch_img_4.jpg" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">

                        <img src="images/stitch/stitch_img_5.jpg" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">

                    </div>



                    <p>

                        Every element of your stay—from the thread count of your linens to the vintage of your evening wine—is selected to harmonize with the raw, untamed beauty outside your window. Here, luxury is defined not just by opulence, but by exclusive access to authentic, transformative experiences.

                    </p>

                </div>

                

                <div class="mt-16 text-center border-t border-white/10 pt-12">

                    <button id="close-modal-bottom" data-action="close-story-modal" class="font-label-caps text-secondary tracking-widest text-sm hover:text-white transition-colors">RETURN TO DESTINATION</button>

                </div>

            </div>

        </div>

    </div>

</div>











    <!-- Premium Footer -->
    <footer class="premium-footer">
        
        <!-- Concierge Banner -->
        <div class="footer-concierge-banner">
            <div class="container">
                <div class="concierge-grid">
                    
                    <div class="concierge-block">
                        <div class="concierge-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                        <div class="concierge-info">
                            <h5>To More Inquiry</h5>
                            <a href="contact.php">Don't hesitate Call to Leisure Loop</a>
                        </div>
                    </div>

                    <div class="concierge-block">
                        <div class="concierge-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="concierge-info">
                            <h5>WhatsApp</h5>
                            <p>+91 89189 21629</p>
                        </div>
                    </div>

                    <div class="concierge-block">
                        <div class="concierge-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div class="concierge-info">
                            <h5>Mail Us</h5>
                            <p>curator@leisurelooptrip.in</p>
                        </div>
                    </div>

                    <div class="concierge-block">
                        <div class="concierge-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="concierge-info">
                            <h5>Call Us</h5>
                            <p>+91 89189 21629</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid-4">
                    
                    <!-- Column 1: Brand -->
                    <div class="footer-brand">
                                                <img src="assets/img/leisure.png" alt="Leisure Loop Trip" class="site-logo-footer">
                                                
                        <div class="footer-tagline">Leisure Loop Trip Pvt Ltd</div>
                        <div class="footer-gstin">GSTIN : 19AATFT6367Q1ZS</div>
                        
                        <div class="social-rings">
                            <a aria-label="Link" href="#" class="social-ring">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            </a>
                            <a aria-label="Link" href="#" class="social-ring">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            </a>
                            <a aria-label="Link" href="#" class="social-ring">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Column 2: Offices (Sanctuaries) -->
                    <div class="footer-offices">
                        <h4 class="footer-heading">Contact Us</h4>
                        
                        <div class="office-block">
                            <span class="office-type">Registered Address</span>
                            <div class="office-address">
                                Shivmandir, Siliguri,<br>
                                Darjeeling – 734011
                            </div>
                        </div>

                        <div class="office-block">
                            <span class="office-type">Corporate Office</span>
                            <div class="office-address">
                                197, Jodhpur Gardens,<br>
                                Kolkata - 700045
                            </div>
                        </div>

                        <div class="office-block">
                            <span class="office-type">Branch Office</span>
                            <div class="office-address">
                                Kachari Basti Rd, opposite Barbeque Nation,<br>
                                South Sarania, Ulubari,<br>
                                Guwahati, Assam 781007
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Quick Links -->
                    <div class="footer-quick-links">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="#">Corporate Tours</a></li>
                            <li><a href="#">B2B Enquiry</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Explore -->
                    <div class="footer-explore">
                        <h4 class="footer-heading">Explore By Places</h4>
                        <ul class="footer-links">
                            <li><a href="#">Bhutan</a></li>
                            <li><a href="#">Darjeeling</a></li>
                            <li><a href="#">Sikkim</a></li>
                            <li><a href="#">Yumthang Valley</a></li>
                            <li><a href="#">Meghalaya</a></li>
                            <li><a href="#">Arunachal Pradesh</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2026 LEISURE LOOP TRIP PVT LTD. ALL RIGHTS RESERVED.
        </div>
    </footer>

    <!-- Organization Schema -->
    

    <!-- WhatsApp Concierge Button -->
<a href="https://wa.me/918918921629?text=Hello%20Leisure%20Loop%20Trip!%20I%20am%20interested%20in%20planning%20an%20elite%20escape." 
   class="whatsapp-concierge" 
   target="_blank" 
   aria-label="Chat with a Travel Curator">
    <div class="whatsapp-icon">
        <svg viewBox="0 0 448 512" width="24" height="24" fill="currentColor">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.6-2.8-23.6-8.7-45-27.7-16.6-14.8-27.8-33.1-31.1-38.6-3.2-5.6-.3-8.6 2.5-11.4 2.5-2.5 5.5-6.5 8.3-9.7 2.8-3.2 3.7-5.6 5.6-9.3 1.9-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.6 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
        </svg>
    </div>
    <span class="concierge-text">Travel Concierge</span>
</a>


    <!-- Bespoke Travel Planner Modal -->
<div id="plannerModal" class="modal-overlay" style="display: none;">
    <div class="modal-glass">
        <b aria-label="Close"utton class="modal-close" data-action="close-planner">&times;</button>
        
        <form id="travelPlannerForm">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="planner-steps">
                
                <!-- Step 1: Destination -->
                <div class="step active" data-step="1">
                    <span class="section-label">Step 01 / 04</span>
                    <h2 class="serif">Where does your <br>heart take you?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="destination" value="Sikkim" required>
                            <span>Sikkim</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Kashmir">
                            <span>Kashmir</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Ladakh">
                            <span>Ladakh</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Other">
                            <span>Other Destination</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Timeline -->
                <div class="step" data-step="2">
                    <span class="section-label">Step 02 / 04</span>
                    <h2 class="serif">When is the <br>escape?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="timeline" value="Next 30 Days" required>
                            <span>Next 30 Days</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="1-3 Months">
                            <span>1-3 Months</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="3-6 Months">
                            <span>3-6 Months</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="Just Planning">
                            <span>Just Planning</span>
                        </label>
                    </div>
                </div>

                <!-- Step 3: Travelers -->
                <div class="step" data-step="3">
                    <span class="section-label">Step 03 / 04</span>
                    <h2 class="serif">Who is joining <br>the journey?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Solo" required>
                            <span>Solo Traveler</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Couple">
                            <span>Bespoke Couple</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Family">
                            <span>Elite Family</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Group">
                            <span>Private Group</span>
                        </label>
                    </div>
                </div>

                <!-- Step 4: Contact -->
                <div class="step" data-step="4">
                    <span class="section-label">Final Step</span>
                    <h2 class="serif">How can our <br>curators reach you?</h2>
                    <div style="margin-top: 2rem;">
                        
<label for="input_79601cf8" class="sr-only">YOUR NAME</label>
<input id="input_79601cf8" type="text" name="name" placeholder="YOUR NAME" required class="planner-input">
                        
<label for="input_f09e2ea2" class="sr-only">PHONE NUMBER</label>
<input id="input_f09e2ea2" type="tel" name="phone" placeholder="PHONE NUMBER" required class="planner-input">
                        
<label for="input_a8bc71a2" class="sr-only">EMAIL ADDRESS</label>
<input id="input_a8bc71a2" type="email" name="email" placeholder="EMAIL ADDRESS" class="planner-input">
                    </div>
                </div>

            </div>

            <input type="hidden" name="enforce_recaptcha" value="1">
            <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
            <div style="margin-top:2rem; display:flex; justify-content:center;">
                <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
            </div>
            <?php endif; ?>

            <div class="planner-footer">
                <button type="button" id="prevBtn" class="btn-outline" style="display: none;">Back</button>
                <button type="button" id="nextBtn" class="btn-gold">Next Step</button>
                <button type="submit" id="submitBtn" class="btn-gold" style="display: none;">Request Consultation</button>
            </div>
        </form>
    </div>
</div>


    





<?php include "../includes/footer.php"; ?>

