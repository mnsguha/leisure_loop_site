<?php
require_once '../config/db.php';
require_once '../config/recaptcha.php';

$page_title = "B2B Partner Program | Leisure Loop";
$use_recaptcha = recaptchaIsConfigured();
$recaptcha_site_key = recaptchaSiteKey();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    
    <?php if ($use_recaptcha && $recaptcha_site_key): ?>
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
        <span class="mob-topbar__title">B2B Partner</span>
        <div class="mob-topbar__spacer"></div>
    </header>
    <div class="mob-topbar-offset"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-gradient"></div>
        
        <div class="z-10">
            <div class="section-label">Premium DMC</div>
            <h1>Partner With Us.<br><span class="text-gold italic">Grow Together.</span></h1>
            <p>At Leisure Loop Trip, we empower travel agents and tour operators worldwide by serving as their flawless, on-ground extension in Sikkim, Darjeeling, and the Himalayas. We understand that your clients' satisfaction reflects directly on your agency's reputation. That's why we offer strictly white-labeled services, highly competitive net B2B rates, and uncompromising luxury execution.</p>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="px-4 py-8">
        
        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div class="benefit-title">Net B2B Rates</div>
            <div class="benefit-desc">Maximize margins with highly competitive, direct DMC pricing and absolutely no hidden markups.</div>
        </div>

        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <div class="benefit-title">100% White-Label</div>
            <div class="benefit-desc">Your brand, our execution. Our local staff represent your agency to keep your brand equity intact.</div>
        </div>

        <div class="benefit-card">
            <div class="benefit-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </div>
            <div class="benefit-title">24/7 Ground Support</div>
            <div class="benefit-desc">We provide dedicated round-the-clock on-ground assistance to handle any requests or emergencies immediately.</div>
        </div>

    </section>

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Register Agency</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <div>
                <span style="font-size: 0.6rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 700; display: block; margin-bottom: 4px;">REGISTER AGENCY</span>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #fff; margin: 0; font-style: italic;">Become A Partner</h2>
            </div>
            <button aria-label="Close" class="modal-close" data-action="close-modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form id="b2bFormMobile" action="/api/v1/leads" method="POST">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                
                <div class="form-group">
                    <label class="form-label" for="b2b_agency_name">Agency Name</label>
                    <input type="text" id="b2b_agency_name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="b2b_contact_person">Contact Person Name</label>
                    <input type="text" name="name" id="b2b_contact_person" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="b2b_email">Official Email</label>
                    <input type="email" name="email" id="b2b_email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="b2b_phone">Phone Number</label>
                    <input type="tel" name="phone" id="b2b_phone" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="b2b_volume">Monthly Queries</label>
                    <select id="b2b_volume" class="form-input" required>
                        <option value="" disabled selected hidden>Select estimated volume</option>
                        <option value="1-5">1 - 5 Queries</option>
                        <option value="6-15">6 - 15 Queries</option>
                        <option value="16-30">16 - 30 Queries</option>
                        <option value="30+">30+ Queries</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="b2b_message">Specific Requirements</label>
                    <textarea id="b2b_message" class="form-input" rows="4" required></textarea>
                </div>

                <!-- Hidden inputs mirroring desktop functionality -->
                <input type="hidden" name="message" id="b2b_combined_message" value="">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
                <div class="form-group mt-4">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-gold">SUBMIT REGISTRATION</button>
            </form>
        </div>
    </div>

    

    
    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
</body>
</html>
