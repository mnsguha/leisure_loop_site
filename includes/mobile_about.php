<?php 
require_once '../config/db.php';
$page_title = "Our Story | Leisure Loop";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body class="bg-obsidian">

<!-- Top App Bar -->
<header class="mob-topbar" id="topAppBar">
    <a href="index.php" class="mob-topbar__back" aria-label="Back">
        <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
    </a>
    <span class="mob-topbar__title">Our Story</span>
    <div class="mob-topbar__spacer"></div>
</header>
<div class="mob-topbar-offset"></div>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-gradient"></div>
    <div class="mob-about__hero">
        <span class="mob-about__tagline">Our Manifesto</span>
        <h1 class="mob-about__heading">Beyond the <br><em>Horizon.</em></h1>
        <p class="mob-about__subtext">We do not sell vacations. We architect transformative journeys for the world's most discerning explorers.</p>
    </div>
</div>

<!-- Editorial Content -->
<div class="mob-about__prose">
    <p class="mob-about__para mob-about__para--dropcap">
        Travel, in its purest form, is not merely the act of traversing geography; it is the profound art of arriving at a new understanding of oneself. At Leisure Loop Trip, we were born from a singular, uncompromising vision: to resurrect the golden age of exploration, marrying untamed wilderness with unprecedented luxury.
    </p>
    <p class="mob-about__para">
        We observed an industry saturated with generic itineraries and transactional experiences. The modern explorer, however, seeks something far more elusive—authenticity, exclusivity, and silence. We realized that true luxury is no longer just high-thread-count linens; it is the luxury of untouched landscapes, private access to hidden heritage, and time standing still in the shadow of the Himalayas.
    </p>

    <div class="mob-about__pullquote">
        <p class="mob-about__pullquote-text">
            "To travel with us is to step into a cinematic narrative where you are the protagonist."
        </p>
        <span class="mob-about__pullquote-label">The Leisure Loop Promise</span>
    </div>

    <p class="mob-about__para">
        Headquartered at the gateway to the Eastern Himalayas in Siliguri, our roots are deeply intertwined with the alpine air. Our curators are not agents; they are connoisseurs of geography. They spend months in the field, navigating unmapped terrains, tasting local delicacies, and sleeping in heritage estates before a destination ever makes it to our portfolio.
    </p>

    <div class="mob-about__signature">
        <p class="mob-about__sig-name">Nanda Barman</p>
        <span class="mob-about__sig-role">Co-founder</span>
    </div>
</div>

<!-- Philosophy Swipeable Cards -->
<div class="mob-about__philosophy">
    <div class="mob-about__section-header">
        <span class="mob-about__section-label">Our Philosophy</span>
        <h2 class="mob-about__section-title">The Three Pillars</h2>
    </div>

    <div class="mob-about__card-strip">
        <!-- Card 1 -->
        <div class="mob-about__card">
            <div class="mob-about__card-glow"></div>
            <div class="mob-about__card-header">
                <div class="mob-about__card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <span class="mob-about__card-num">01</span>
            </div>
            <h3 class="mob-about__card-title">Hyper-Personalization</h3>
            <p class="mob-about__card-desc">No two travelers are alike, and neither are our journeys. We discard the template, crafting every itinerary from a blank canvas based on your distinct desires, pace, and palate.</p>
        </div>

        <!-- Card 2 -->
        <div class="mob-about__card">
            <div class="mob-about__card-glow"></div>
            <div class="mob-about__card-header">
                <div class="mob-about__card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <span class="mob-about__card-num">02</span>
            </div>
            <h3 class="mob-about__card-title">Unseen Access</h3>
            <p class="mob-about__card-desc">We open doors that remain closed to the public. Private dining in heritage forts, after-hours museum tours, and secret Himalayan trails known only to our expert guides.</p>
        </div>

        <!-- Card 3 -->
        <div class="mob-about__card">
            <div class="mob-about__card-glow"></div>
            <div class="mob-about__card-header">
                <div class="mob-about__card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <span class="mob-about__card-num">03</span>
            </div>
            <h3 class="mob-about__card-title">Invisible Concierge</h3>
            <p class="mob-about__card-desc">True service is felt, not seen. Our elite concierge team orchestrates your travel behind the scenes, ensuring flawless transitions and anticipating your needs before you do.</p>
        </div>

        <!-- End spacer -->
        <div class="mob-about__card-strip-end"></div>
    </div>
</div>

<div class="mob-about__spacer"></div>

<!-- Sticky Bottom CTA -->
<div class="sticky-cta">
    <button data-action="open-modal" class="btn-primary">Enquire Now</button>
</div>

<!-- Enquiry Modal Bottom Sheet -->
<div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
<div class="modal-content" id="enquiryModal">
    <div class="modal-header">
        <div>
            <span class="section-label">BESPOKE TRAVEL</span>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0; font-style: italic;">Plan Your Elite Journey</h2>
        </div>
        <button aria-label="Close" class="modal-close" data-action="close-modal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="modal-body">
        <form action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="destination" value="About Us Page Enquiry">
            <input type="hidden" name="source" value="mobile_about">
            <input type="hidden" name="enforce_recaptcha" value="1">
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">person</span>
<label for="input_70172fac" class="sr-only">Your Full Name</label>
<input id="input_70172fac" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
            </div>
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">call</span>
<label for="input_798d6c17" class="sr-only">Phone Number</label>
<input id="input_798d6c17" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
            </div>
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">mail</span>
<label for="input_ad20ecc7" class="sr-only">Email Address</label>
<input id="input_ad20ecc7" type="email" name="email" class="form-input" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">calendar_today</span>
<label for="input_161a56a3" class="sr-only">Travel Date</label>
<input id="input_161a56a3" type="text" name="travel_date" class="form-input" placeholder="Travel Date" data-focus="date">
            </div>
            <div class="form-group">
                <span class="material-symbols-outlined form-icon">group</span>
                <select name="guests" class="form-input form-select">
                    <option disabled selected>Number of Guests</option>
                    <option>1 Guest</option>
                    <option>2 Guests</option>
                    <option>3-5 Guests</option>
                    <option>5+ Guests</option>
                </select>
            </div>
            <?php 
            global $use_recaptcha, $recaptcha_site_key;
            if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
            <div class="form-group" style="margin-top:1rem;">
                <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn-primary" style="width:100%;display:block;margin-top:8px;">SUBMIT ENQUIRY</button>
            <p class="section-label" style="text-align:center;margin-top:1rem;color:rgba(255,255,255,0.4);">A travel specialist will contact you within 24 hours</p>
        </form>
    </div>
</div>


<body class="bg-obsidian">

<!-- Top App Bar -->
<div class="fixed top-0 left-0 w-full z-50 bg-obsidian/80 backdrop-blur-lg border-b border-white/5 transition-all duration-300" id="topAppBar">
    <div class="flex items-center justify-between px-4 h-16">
        <a href="index.php" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-white" style="font-size:20px;">arrow_back</span>
        </a>
        <h1 class="font-serif italic text-white text-lg absolute left-1/2 -translate-x-1/2">Our Story</h1>
        <div class="w-10"></div>
    </div>
</div>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-gradient"></div>
    <div class="container px-5 relative z-10">
        <span class="text-gold text-xs font-bold tracking-widest uppercase mb-3 block">Our Manifesto</span>
        <h1 class="font-serif text-[2.75rem] leading-[1.1] mb-5 text-white">Beyond the <br><span class="text-gold italic">Horizon.</span></h1>
        <p class="text-white/70 text-[0.95rem] font-light leading-relaxed max-w-[90%]">We do not sell vacations. We architect transformative journeys for the world's most discerning explorers.</p>
    </div>
</div>

<!-- Editorial Content -->
<div class="px-5 py-8">
    <p class="dropcap text-white/80 text-[15px] leading-relaxed mb-6 font-light">
        Travel, in its purest form, is not merely the act of traversing geography; it is the profound art of arriving at a new understanding of oneself. At Leisure Loop Trip, we were born from a singular, uncompromising vision: to resurrect the golden age of exploration, marrying untamed wilderness with unprecedented luxury.
    </p>
    <p class="text-white/80 text-[15px] leading-relaxed mb-6 font-light">
        We observed an industry saturated with generic itineraries and transactional experiences. The modern explorer, however, seeks something far more elusive—authenticity, exclusivity, and silence. We realized that true luxury is no longer just high-thread-count linens; it is the luxury of untouched landscapes, private access to hidden heritage, and time standing still in the shadow of the Himalayas.
    </p>
    
    <div class="my-10 py-8 border-y border-gold/20 text-center">
        <p class="font-serif italic text-[1.35rem] text-white leading-snug mb-4">
            "To travel with us is to step into a cinematic narrative where you are the protagonist."
        </p>
        <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold">The Leisure Loop Promise</span>
    </div>

    <p class="text-white/80 text-[15px] leading-relaxed mb-6 font-light">
        Headquartered at the gateway to the Eastern Himalayas in Siliguri, our roots are deeply intertwined with the alpine air. Our curators are not agents; they are connoisseurs of geography. They spend months in the field, navigating unmapped terrains, tasting local delicacies, and sleeping in heritage estates before a destination ever makes it to our portfolio.
    </p>
    
    <div class="mt-12 text-right pt-6 border-t border-white/10">
        <p class="text-gold font-serif text-2xl italic mb-1">Nanda Barman</p>
        <span class="text-white/40 text-[10px] uppercase tracking-widest block">Co-founder</span>
    </div>
</div>

<!-- Philosophy Swipeable Cards -->
<div class="py-10 bg-surface-container/30 border-t border-white/5 relative overflow-hidden">
    <div class="px-5 mb-8">
        <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold block mb-2">Our Philosophy</span>
        <h2 class="font-serif italic text-[2rem] text-white">The Three Pillars</h2>
    </div>

    <div class="flex overflow-x-auto hide-scrollbar snap-x snap-mandatory px-5 pb-8 gap-5">
        <!-- Card 1 -->
        <div class="snap-center shrink-0 w-[78vw] max-w-[300px] bg-gradient-to-b from-white/[0.08] to-transparent backdrop-blur-2xl rounded-[2rem] p-7 border border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Glow effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="w-12 h-12 rounded-full bg-gold/10 border border-gold/20 flex items-center justify-center shadow-[inset_0_0_10px_rgba(197,160,89,0.2)]">
                    <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <span class="font-serif italic text-[2.5rem] text-white/20 font-light tracking-tighter leading-none">01</span>
            </div>
            
            <h3 class="font-serif text-2xl text-white mb-3 relative z-10">Hyper-Personalization</h3>
            <p class="text-white/60 text-[13px] leading-relaxed relative z-10 font-light">No two travelers are alike, and neither are our journeys. We discard the template, crafting every itinerary from a blank canvas based on your distinct desires, pace, and palate.</p>
        </div>

        <!-- Card 2 -->
        <div class="snap-center shrink-0 w-[78vw] max-w-[300px] bg-gradient-to-b from-white/[0.08] to-transparent backdrop-blur-2xl rounded-[2rem] p-7 border border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Glow effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="w-12 h-12 rounded-full bg-gold/10 border border-gold/20 flex items-center justify-center shadow-[inset_0_0_10px_rgba(197,160,89,0.2)]">
                    <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
                <span class="font-serif italic text-[2.5rem] text-white/20 font-light tracking-tighter leading-none">02</span>
            </div>
            
            <h3 class="font-serif text-2xl text-white mb-3 relative z-10">Unseen Access</h3>
            <p class="text-white/60 text-[13px] leading-relaxed relative z-10 font-light">We open doors that remain closed to the public. Private dining in heritage forts, after-hours museum tours, and secret Himalayan trails known only to our expert guides.</p>
        </div>

        <!-- Card 3 -->
        <div class="snap-center shrink-0 w-[78vw] max-w-[300px] bg-gradient-to-b from-white/[0.08] to-transparent backdrop-blur-2xl rounded-[2rem] p-7 border border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Glow effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="w-12 h-12 rounded-full bg-gold/10 border border-gold/20 flex items-center justify-center shadow-[inset_0_0_10px_rgba(197,160,89,0.2)]">
                    <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <span class="font-serif italic text-[2.5rem] text-white/20 font-light tracking-tighter leading-none">03</span>
            </div>
            
            <h3 class="font-serif text-2xl text-white mb-3 relative z-10">Invisible Concierge</h3>
            <p class="text-white/60 text-[13px] leading-relaxed relative z-10 font-light">True service is felt, not seen. Our elite concierge team orchestrates your travel behind the scenes, ensuring flawless transitions and anticipating your needs before you do.</p>
        </div>
        
        <!-- Spacer for end of scroll -->
        <div class="shrink-0 w-4"></div>
    </div>
</div>

<div class="h-8"></div> <!-- Extra padding -->

<!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Enquire Now</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <div>
                <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">BESPOKE TRAVEL</span>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0; font-style: italic;">Plan Your Elite Journey</h2>
            </div>
            <button aria-label="Close" class="modal-close" data-action="close-modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="destination" value="About Us Page Enquiry">
                <input type="hidden" name="source" value="mobile_about">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_70172fac" class="sr-only">Your Full Name</label>
<input id="input_70172fac" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_798d6c17" class="sr-only">Phone Number</label>
<input id="input_798d6c17" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_ad20ecc7" class="sr-only">Email Address</label>
<input id="input_ad20ecc7" type="email" name="email" class="form-input" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">calendar_today</span>
                    
<label for="input_161a56a3" class="sr-only">Travel Date</label>
<input id="input_161a56a3" type="text" name="travel_date" class="form-input" placeholder="Travel Date" data-focus="date">
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">group</span>
                    <select name="guests" class="form-input form-select">
                        <option disabled selected>Number of Guests</option>
                        <option>1 Guest</option>
                        <option>2 Guests</option>
                        <option>3-5 Guests</option>
                        <option>5+ Guests</option>
                    </select>
                </div>
                
                <?php 
                global $use_recaptcha, $recaptcha_site_key;
                if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): 
                ?>
                <div class="form-group" style="margin-top:1rem;">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-primary" style="width: 100%; display: block; margin-top: 8px;">SUBMIT ENQUIRY</button>
                <p style="text-align: center; margin-top: 16px; color: rgba(255,255,255,0.4); font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;">A travel specialist will contact you within 24 hours</p>
            </form>
        </div>
    </div>
    
    

<!-- Scroll Behavior Script -->


    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
</body>
</html>
