<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Contact Our Curators | Leisure Loop Trip";
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
            <span class="material-symbols-outlined icon-20">arrow_back</span>
        </a>
        <span class="mob-topbar__title">Contact Us</span>
        <div class="mob-topbar__spacer"></div>
    </header>
    <div class="mob-topbar-offset"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-gradient"></div>
        
        <div class="z-10 mt-8">
            <div class="section-label">Curated Assistance</div>
            <h1>Design Your<br><span class="text-gold italic">Next Escape.</span></h1>
            <p class="hero-subtitle">Our expert curators are standing by to craft your bespoke, once-in-a-lifetime itinerary.</p>
            
            <div class="mt-6 flex items-center justify-center gap-2 bg-gold/10 border border-gold/20 py-2 px-4 rounded-full">
                <span class="material-symbols-outlined text-gold icon-16">schedule</span>
                <span class="text-gold text-xs font-medium tracking-wide">Expect consultation within 2-4 hours.</span>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="px-4 py-4 pb-24">
        
        <a href="tel:+918918921629" class="contact-card contact-card--no-decor">
            <div class="contact-icon">
                <span class="material-symbols-outlined">call</span>
            </div>
            <div class="contact-details">
                <h3>Direct Lines</h3>
                <p class="primary-text">+91 89189 21629</p>
                <p class="secondary-text">Tap to call our curators</p>
            </div>
        </a>
        
        <a href="mailto:curator@leisurelooptrip.in" class="contact-card contact-card--no-decor">
            <div class="contact-icon">
                <span class="material-symbols-outlined">mail</span>
            </div>
            <div class="contact-details">
                <h3>Email Address</h3>
                <p class="primary-text">curator@leisurelooptrip.in</p>
                <p class="secondary-text">Tap to send an email</p>
            </div>
        </a>
        
        <div class="contact-card">
            <div class="contact-icon">
                <span class="material-symbols-outlined">location_on</span>
            </div>
            <div class="contact-details">
                <h3>Headquarters</h3>
                <p class="primary-text">Siliguri, West Bengal</p>
                <p class="secondary-text">Gateways to the Eastern Himalayas</p>
            </div>
        </div>
        
        <div class="contact-card">
            <div class="contact-icon">
                <span class="material-symbols-outlined">schedule</span>
            </div>
            <div class="contact-details">
                <h3>Office Hours</h3>
                <p class="primary-text">Monday - Saturday</p>
                <p class="secondary-text">10:00 AM - 7:00 PM (IST)</p>
            </div>
        </div>
        
    </section>

    <!-- Sticky Bottom CTA -->
    <div class="sticky-cta">
        <button data-action="open-modal" class="btn-primary">Get In Touch</button>
    </div>

    <!-- Enquiry Modal Bottom Sheet -->
    <div class="modal-overlay" id="enquiryModalOverlay" data-action="close-modal" role="button" aria-label="Close modal"></div>
    <div class="modal-content" id="enquiryModal">
        <div class="modal-header">
            <div>
                <span class="modal-kicker">BESPOKE TRAVEL</span>
                <h2 class="modal-title-serif">Plan Your Elite Journey</h2>
            </div>
            <button aria-label="Close" class="modal-close" data-action="close-modal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form action="api-submit-lead.php" method="POST">
<input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <input type="hidden" name="destination" value="Contact Us Page Enquiry">
                <input type="hidden" name="source" value="mobile_contact">
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">person</span>
                    
<label for="input_47b4b317" class="sr-only">Your Full Name</label>
<input id="input_47b4b317" type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">call</span>
                    
<label for="input_1486d48b" class="sr-only">Phone Number</label>
<input id="input_1486d48b" type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                </div>
                
                <div class="form-group">
                    <span class="material-symbols-outlined form-icon">mail</span>
                    
<label for="input_b2821e84" class="sr-only">Email Address</label>
<input id="input_b2821e84" type="email" name="email" class="form-input" placeholder="Email Address" required>
                </div>
                
                <div class="form-group form-group--textarea">
                    <span class="material-symbols-outlined form-icon form-icon--textarea">description</span>
                    
<label for="input_71d66d9f" class="sr-only">Your Requirements / Destinations</label>
<textarea id="input_71d66d9f" name="message" class="form-input textarea--icon" placeholder="Your Requirements / Destinations" rows="3" required>                </textarea>
                </div>
                
                <button type="submit" class="btn-primary btn-primary--full">SUBMIT ENQUIRY</button>
                <p class="form-footnote">A travel specialist will contact you within 2-4 hours</p>
            </form>
        </div>
    </div>
    
    
    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
</body>
</html>
