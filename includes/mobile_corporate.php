<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Corporate Tours & MICE | Leisure Loop Trip";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- CSS Dependencies -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Top App Bar -->
    <header class="mob-topbar">
        <a href="index.php" class="mob-topbar__back" aria-label="Back">
            <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
        </a>
        <span class="mob-topbar__title">Corporate</span>
        <div class="mob-topbar__spacer"></div>
    </header>
    <div class="mob-topbar-offset"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-gradient"></div>
        
        <div class="z-10">
            <div class="section-label">Premium Business Travel</div>
            <h1>Elevate Your<br><span class="text-gold italic">Corporate Retreats.</span></h1>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.6; margin: 0;">At Leisure Loop Trip, we understand that corporate travel is an investment in your people. From high-stakes board meetings in serene Himalayan estates to thrilling team-building expeditions, we architect flawless, bespoke MICE experiences.</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="px-4 py-8">
        
        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            </div>
            <div class="benefit-title">MICE Solutions</div>
            <div class="benefit-desc">Comprehensive management for Meetings, Incentives, Conferences, and Exhibitions. We source premium venues and coordinate complex travel logistics.</div>
        </div>

        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
            <div class="benefit-title">Executive Retreats</div>
            <div class="benefit-desc">Highly exclusive and strictly private offsites for C-level executives. Foster strategic thinking in handpicked luxury estates with private chefs.</div>
        </div>

        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="benefit-title">Team Bonding</div>
            <div class="benefit-desc">Strengthen corporate culture with curated group experiences. From light trekking to cultural immersions, our escapes improve morale and foster deep connections.</div>
        </div>
        
        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
            </div>
            <div class="benefit-title">Reward & Recognition</div>
            <div class="benefit-desc">Incentivize your top performers with the ultimate reward: an unforgettable, all-expenses-paid luxury journey tailored to their exact preferences.</div>
        </div>

    </section>

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Request Proposal</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <div>
                <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">CORPORATE ENQUIRY</span>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0; font-style: italic;">Plan Your Retreat</h2>
            </div>
            <button aria-label="Close" class="modal-close" data-action="close-modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_cc377238" class="sr-only">Contact Person Name</label>
<input id="input_cc377238" type="text" name="name" class="form-input" placeholder="Contact Person Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">business</span>
                    
<label for="input_ae325c1e" class="sr-only">Company Name</label>
<input id="input_ae325c1e" type="text" name="company" class="form-input" placeholder="Company Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_8cfefb58" class="sr-only">Work Email</label>
<input id="input_8cfefb58" type="email" name="email" class="form-input" placeholder="Work Email" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_cf18ee68" class="sr-only">Phone Number</label>
<input id="input_cf18ee68" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">group</span>
                    
<label for="input_b43be5ae" class="sr-only">Estimated Group Size</label>
<input id="input_b43be5ae" type="number" name="size" class="form-input" placeholder="Estimated Group Size" min="1" required>
                </div>

                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">description</span>
                    
<label for="input_60601ee3" class="sr-only">Brief requirements...</label>
<textarea id="input_60601ee3" name="requirements" class="form-input" rows="3" placeholder="Brief requirements..." style="padding-left: 48px;" required></textarea>
                </div>

                <input type="hidden" name="source" value="mobile_corporate">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <?php 
                global $use_recaptcha, $recaptcha_site_key;
                if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): 
                ?>
                <div class="form-group" style="margin-top:1rem;">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-primary" style="width: 100%; display: block; margin-top: 8px;">SUBMIT ENQUIRY</button>
            </form>
        </div>
    </div>

    
</body>
</html>
