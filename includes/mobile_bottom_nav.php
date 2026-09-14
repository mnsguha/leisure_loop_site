<?php 
$current_page = basename($_SERVER['PHP_SELF'] ?? ''); 
?>

<!-- Mobile Bottom Navigation (5-item) -->
<nav class="mobile-nav-pill">
    <a href="index.php?view=mobile" class="nav-item <?= ($current_page === 'index.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span class="nav-label">Home</span>
        <div class="nav-dot"></div>
    </a>
    
    <a href="destinations.php?view=mobile" class="nav-item <?= ($current_page === 'destinations.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"></path></svg>
        <span class="nav-label">Places</span>
        <div class="nav-dot"></div>
    </a>
    
    <div class="nav-fab-container">
        <button aria-label="Custom Trip Enquiry" type="button" class="nav-fab" data-action="open-enquiry-modal">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1-1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
        </button>
        <span class="m-nav-fab-label">Custom</span>
    </div>
    
    <a href="contact.php?view=mobile" class="nav-item <?= ($current_page === 'contact.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        <span class="nav-label">Chat</span>
        <div class="nav-dot"></div>
    </a>
    
    <button aria-label="Open Navigation Menu" type="button" class="nav-item inactive" data-action="open-mobile-menu">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        <span class="nav-label">Menu</span>
        <div class="nav-dot"></div>
    </button>
</nav>

<!-- Mobile Menu App Style Drawer -->
<div id="mobileMenuOverlay" data-action="close-mobile-menu" role="button" aria-label="Close menu"></div>

<div id="mobileMenuPanel">
    <div class="m-drawer-header">
        <button type="button" data-action="close-mobile-menu" class="m-drawer-close" aria-label="Close menu">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="m-drawer-avatar">
            <span class="material-symbols-outlined">account_circle</span>
        </div>
        <h2 class="m-drawer-title">Leisure Loop</h2>
        <p class="m-drawer-subtitle">Plan your elite journey</p>
    </div>
    
    <div class="m-drawer-body">
        <div class="m-drawer-group">
            <a href="about.php?view=mobile" class="m-drawer-item">
                <div class="m-drawer-item-left">
                    <span class="material-symbols-outlined m-drawer-item-icon">info</span>
                    <span class="m-drawer-item-label">About Us</span>
                </div>
                <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
            </a>
            <a href="b2b.php?view=mobile" class="m-drawer-item">
                <div class="m-drawer-item-left">
                    <span class="material-symbols-outlined m-drawer-item-icon">handshake</span>
                    <span class="m-drawer-item-label">B2B Partnerships</span>
                </div>
                <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
            </a>
            <a href="corporate.php?view=mobile" class="m-drawer-item">
                <div class="m-drawer-item-left">
                    <span class="material-symbols-outlined m-drawer-item-icon">groups</span>
                    <span class="m-drawer-item-label">Corporate Team</span>
                </div>
                <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
            </a>
            <a href="faq.php?view=mobile" class="m-drawer-item">
                <div class="m-drawer-item-left">
                    <span class="material-symbols-outlined m-drawer-item-icon">help</span>
                    <span class="m-drawer-item-label">FAQs</span>
                </div>
                <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
            </a>
            <a href="contact.php?view=mobile" class="m-drawer-item">
                <div class="m-drawer-item-left">
                    <span class="material-symbols-outlined m-drawer-item-icon">mail</span>
                    <span class="m-drawer-item-label">Contact Us</span>
                </div>
                <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
            </a>
        </div>

        <div>
            <h3 class="m-drawer-section-title">Stay Connected with us</h3>
            <div class="m-drawer-group">
                <a href="<?= htmlspecialchars($settings['social_instagram'] ?? '#'); ?>" class="m-drawer-item">
                    <div class="m-drawer-item-left">
                        <svg class="m-drawer-social-icon" viewBox="0 0 24 24" fill="none" stroke="url(#ig-grad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><defs><linearGradient id="ig-grad" x1="2" y1="22" x2="22" y2="2"><stop offset="0%" stop-color="#fd5949"/><stop offset="50%" stop-color="#d6249f"/><stop offset="100%" stop-color="#285AEB"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span class="m-drawer-item-label">Instagram</span>
                    </div>
                    <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
                </a>
                <a href="<?= htmlspecialchars($settings['social_twitter'] ?? '#'); ?>" class="m-drawer-item">
                    <div class="m-drawer-item-left">
                        <svg class="m-drawer-social-icon" viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        <span class="m-drawer-item-label">X (Twitter)</span>
                    </div>
                    <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
                </a>
                <a href="<?= htmlspecialchars($settings['social_linkedin'] ?? '#'); ?>" class="m-drawer-item">
                    <div class="m-drawer-item-left">
                        <svg class="m-drawer-social-icon" viewBox="0 0 24 24" fill="#0a66c2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        <span class="m-drawer-item-label">LinkedIn</span>
                    </div>
                    <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
                </a>
                <a href="<?= htmlspecialchars($settings['social_facebook'] ?? '#'); ?>" class="m-drawer-item">
                    <div class="m-drawer-item-left">
                        <svg class="m-drawer-social-icon" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        <span class="m-drawer-item-label">Facebook</span>
                    </div>
                    <span class="material-symbols-outlined m-drawer-item-arrow">chevron_right</span>
                </a>
            </div>
        </div>
    </div>

    <div class="m-drawer-footer">
        <a href="privacy.php?view=mobile" class="m-drawer-footer-link">Privacy Policy</a>
        <span class="m-drawer-footer-dot">•</span>
        <a href="terms.php?view=mobile" class="m-drawer-footer-link">Terms of Service</a>
        <span class="m-drawer-footer-dot">•</span>
        <a href="cancellation.php?view=mobile" class="m-drawer-footer-link">Cancellation</a>
    </div>
</div>

<script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
