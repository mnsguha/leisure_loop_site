<?php
    $site_logo_path = __DIR__ . '/../public/assets/img/leisure.png';
    $site_logo_url = 'assets/img/leisure.png';
    $has_site_logo = file_exists($site_logo_path);
?>
    <!-- Premium Footer -->
    <footer class="premium-footer <?php echo (isset($hideFooterOnMobile) && $hideFooterOnMobile) ? 'hidden md:block' : ''; ?>">
        
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
                        <?php if ($has_site_logo): ?>
                        <img src="<?php echo $site_logo_url; ?>" alt="Leisure Loop Trip" class="site-logo-footer">
                        <?php else: ?>
                        <span class="logo-text" style="display:block; margin-bottom:1.5rem;">LEISURE <span class="accent">LOOP</span></span>
                        <?php endif; ?>
                        
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
                            <li><a href="faq.php">Knowledge Base</a></li>
                            <li><a href="privacy.php">Privacy Policy</a></li>
                            <li><a href="refund.php">Refund Policy</a></li>
                            <li><a href="terms.php">Terms & Conditions</a></li>
                            <li><a href="corporate.php">Corporate Tours</a></li>
                            <li><a href="b2b.php">B2B Enquiry</a></li>
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

    <?php include 'whatsapp-btn.php'; ?>
    <?php include 'lead-modal.php'; ?>
    <?php include 'enquiry-modal.php'; ?>
    
    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- GSAP & ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    
    <!-- Lenis Smooth Scroll -->
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

    <script src="js/modals.js?v=<?php echo time(); ?>"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
