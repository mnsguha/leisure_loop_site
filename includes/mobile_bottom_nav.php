<link rel="stylesheet" href="css/mobile-views.css">

<?php 
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<!-- Mobile Bottom Navigation (5-item) -->
<nav class="mobile-nav-pill">
    <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span class="nav-label">Home</span>
        <div class="nav-dot"></div>
    </a>
    
    <a href="destinations.php" class="nav-item <?php echo ($current_page == 'destinations.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"></path></svg>
        <span class="nav-label">Places</span>
        <div class="nav-dot"></div>
    </a>
    
    <div class="nav-fab-container">
        <button aria-label="Edit" type="button" class="nav-fab" data-action="open-enquiry-modal">
            <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
        </button>
        <!-- The label can be omitted or placed below if needed, but FABs usually stand alone -->
        <span class="nav-label" style="position: absolute; bottom: 4px; color: rgba(255, 255, 255, 0.5);">Custom</span>
    </div>
    
    <a href="contact.php" class="nav-item <?php echo ($current_page == 'contact.php') ? 'active' : 'inactive'; ?>">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        <span class="nav-label">Chat</span>
        <div class="nav-dot"></div>
    </a>
    
    <button aria-label="Close" type="button" class="nav-item inactive" data-action="open-mobile-menu">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        <span class="nav-label">Menu</span>
        <div class="nav-dot"></div>
    </button>
</nav>

<!-- Mobile Menu App Style Drawer -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[110] opacity-0 pointer-events-none transition-opacity duration-300" data-action="close-mobile-menu" role="button" aria-label="Close menu"></div>

<div id="mobileMenuPanel" data-lenis-prevent class="fixed top-0 left-0 h-full w-[85%] max-w-[320px] bg-[#0a0e17] z-[111] shadow-2xl transform -translate-x-full transition-transform duration-300 flex flex-col overflow-y-auto border-r border-white/10 rounded-r-3xl">
    
    <!-- Header Box -->
    <div class="bg-surface border-b border-surface-container-high p-6 relative">
        <button data-action="close-mobile-menu" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white" aria-label="Close menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="w-12 h-12 rounded-full bg-gold/20 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-gold" style="font-size:24px;">account_circle</span>
        </div>
        <h2 class="font-headline-md text-xl text-white">Leisure Loop</h2>
        <p class="text-xs text-white/50 mt-1">Plan your elite journey</p>
    </div>
    
    <!-- Menu Links Group -->
    <div class="p-4 pt-6">
        <div class="bg-surface/50 border border-surface-container rounded-3xl overflow-hidden shadow-lg backdrop-blur-sm">
            <a href="about.php" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-gold" style="font-size:22px;">info</span>
                    <span class="text-[0.95rem] font-medium text-white/90">About Us</span>
                </div>
                <span class="material-symbols-outlined text-gold" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="b2b.php" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-gold" style="font-size:22px;">handshake</span>
                    <span class="text-[0.95rem] font-medium text-white/90">B2B Partnerships</span>
                </div>
                <span class="material-symbols-outlined text-gold" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="corporate.php" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-gold" style="font-size:22px;">groups</span>
                    <span class="text-[0.95rem] font-medium text-white/90">Corporate Team</span>
                </div>
                <span class="material-symbols-outlined text-gold" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="faq.php" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-gold" style="font-size:22px;">help</span>
                    <span class="text-[0.95rem] font-medium text-white/90">FAQs</span>
                </div>
                <span class="material-symbols-outlined text-gold" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="contact.php" class="!no-underline flex items-center justify-between p-4 active:bg-surface-container transition-colors">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-gold" style="font-size:22px;">mail</span>
                    <span class="text-[0.95rem] font-medium text-white/90">Contact Us</span>
                </div>
                <span class="material-symbols-outlined text-gold" style="font-size:20px;">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Social Links (Stay Connected) -->
    <div class="px-4 pb-4">
        <h3 class="font-serif italic text-white/70 text-[0.9rem] mb-3 px-2 relative z-10">Stay Connected with us</h3>
        <div class="bg-surface/50 border border-surface-container rounded-3xl overflow-hidden shadow-lg backdrop-blur-sm">
            <a href="<?php echo htmlspecialchars($settings['social_instagram'] ?? '#'); ?>" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors min-h-[48px]">
                <div class="flex items-center gap-4">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="url(#ig-grad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><defs><linearGradient id="ig-grad" x1="2" y1="22" x2="22" y2="2"><stop offset="0%" stop-color="#fd5949"/><stop offset="50%" stop-color="#d6249f"/><stop offset="100%" stop-color="#285AEB"/></linearGradient></defs><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    <span class="text-[0.95rem] font-medium text-white/90">Instagram</span>
                </div>
                <span class="material-symbols-outlined text-gold/50" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="<?php echo htmlspecialchars($settings['social_twitter'] ?? '#'); ?>" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors min-h-[48px]">
                <div class="flex items-center gap-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <span class="text-[0.95rem] font-medium text-white/90">X (Twitter)</span>
                </div>
                <span class="material-symbols-outlined text-gold/50" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="<?php echo htmlspecialchars($settings['social_linkedin'] ?? '#'); ?>" class="!no-underline flex items-center justify-between p-4 border-b border-white/5 active:bg-surface-container transition-colors min-h-[48px]">
                <div class="flex items-center gap-4">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#0a66c2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    <span class="text-[0.95rem] font-medium text-white/90">LinkedIn</span>
                </div>
                <span class="material-symbols-outlined text-gold/50" style="font-size:20px;">chevron_right</span>
            </a>
            <a href="<?php echo htmlspecialchars($settings['social_facebook'] ?? '#'); ?>" class="!no-underline flex items-center justify-between p-4 active:bg-surface-container transition-colors min-h-[48px]">
                <div class="flex items-center gap-4">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span class="text-[0.95rem] font-medium text-white/90">Facebook</span>
                </div>
                <span class="material-symbols-outlined text-gold/50" style="font-size:20px;">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Footer Links -->
    <div class="mt-auto px-6 py-8 flex flex-wrap justify-center items-center gap-x-4 gap-y-2">
        <a href="privacy.php" class="flex items-center justify-center min-h-[48px] text-[0.7rem] font-medium text-gold/60 hover:text-gold uppercase tracking-wider transition-colors !no-underline">Privacy Policy</a>
        <span class="text-white/20 text-[0.7rem]">•</span>
        <a href="terms.php" class="flex items-center justify-center min-h-[48px] text-[0.7rem] font-medium text-gold/60 hover:text-gold uppercase tracking-wider transition-colors !no-underline">Terms of Service</a>
        <span class="text-white/20 text-[0.7rem]">•</span>
        <a href="cancellation.php" class="flex items-center justify-center min-h-[48px] text-[0.7rem] font-medium text-gold/60 hover:text-gold uppercase tracking-wider transition-colors !no-underline">Cancellation</a>
    </div>
</div>

