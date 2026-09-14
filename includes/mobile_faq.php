<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/data_faqs.php';
$page_title = "Knowledge Base | Leisure Loop Trip";
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

    <link rel="stylesheet" href="css/mobile-views.css">
</head>
<body>

    <!-- Top App Bar -->
    <header class="mob-topbar">
        <a href="index.php" class="mob-topbar__back" aria-label="Back">
            <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
        </a>
        <span class="mob-topbar__title">Knowledge Base</span>
        <div class="mob-topbar__spacer"></div>
    </header>
    <div class="mob-topbar-offset"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-gradient"></div>
        
        <div class="z-10">
            <div class="section-label">Knowledge Base</div>
            <h1>Common<br><span class="text-gold italic">Curiosities.</span></h1>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.6; margin: 0;">Everything you need to know about crafting your bespoke journey with Leisure Loop Trip.</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="px-4 py-4 pb-20">
        <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-item">
            <div class="faq-question">
                <span><?php echo htmlspecialchars($faq['question']); ?></span>
                <div class="faq-icon">&#10010;</div>
            </div>
            <div class="faq-answer">
                <p><?php echo htmlspecialchars($faq['answer']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Get Quote</button>
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

                <input type="hidden" name="destination" value="Knowledge Base / FAQ Enquiry">
                <input type="hidden" name="source" value="mobile_faq">
                <input type="hidden" name="enforce_recaptcha" value="1">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_1c484653" class="sr-only">Your Full Name</label>
<input id="input_1c484653" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_1ac323b4" class="sr-only">Phone Number</label>
<input id="input_1ac323b4" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_c14a311f" class="sr-only">Email Address</label>
<input id="input_c14a311f" type="email" name="email" class="form-input" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">calendar_today</span>
                    
<label for="input_6c2269fe" class="sr-only">Travel Date</label>
<input id="input_6c2269fe" type="text" name="travel_date" class="form-input" placeholder="Travel Date" data-focus="date">
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
    
    
    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
</body>
</html>
