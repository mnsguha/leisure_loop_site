<?php
    $site_logo_path = __DIR__ . '/../public/assets/img/logo.png';
    $site_logo_url = 'assets/img/logo.png';
    $has_site_logo = file_exists($site_logo_path);
?>
    <!-- Footer -->
    <footer style="padding: 6rem 0 3rem; border-top: 1px solid var(--glass-border); background: #050a14;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 4rem; margin-bottom: 4rem; text-align: left;">
                <div>
                    <?php if ($has_site_logo): ?>
                    <img src="<?php echo $site_logo_url; ?>" alt="Leisure Loop Trip" class="site-logo site-logo-footer">
                    <?php else: ?>
                    <span class="logo-text">LEISURE <span class="accent">LOOP</span></span>
                    <?php endif; ?>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Crafting extraordinary journeys for the discerning traveler. Explore the world with Leisure Loop Trip.</p>
                </div>
                <div>
                    <h4 style="color: var(--gold); margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.2em;">Quick Links</h4>
                    <ul style="list-style: none; color: var(--text-muted); font-size: 0.9rem;">
                        <li style="margin-bottom: 0.75rem;"><a href="about.php" style="color: inherit; text-decoration: none;">About Us</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="contact.php" style="color: inherit; text-decoration: none;">Contact Us</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="privacy.php" style="color: inherit; text-decoration: none;">Privacy Policy</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="refund.php" style="color: inherit; text-decoration: none;">Refund Policy</a></li>
                        <li style="margin-bottom: 0.75rem;"><a href="terms.php" style="color: inherit; text-decoration: none;">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: var(--gold); margin-bottom: 1.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.2em;">The Concierge</h4>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">curator@leisurelooptrip.in</p>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">+91 89189 21629</p>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 3rem; border-top: 1px solid rgba(255,255,255,0.05);">
                <p style="color: var(--text-muted); font-size: 0.75rem; letter-spacing: 0.1em;">&copy; 2026 LEISURE LOOP TRIP. ALL RIGHTS RESERVED.</p>
            </div>
        </div>
    </footer>

    <!-- Organization Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "TravelAgency",
      "name": "Leisure Loop Trip",
      "image": "https://www.leisurelooptrip.in/assets/img/logo.png",
      "url": "https://www.leisurelooptrip.in",
      "telephone": "+918918921629",
      "email": "curator@leisurelooptrip.in",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Siliguri",
        "addressRegion": "West Bengal",
        "addressCountry": "IN"
      },
      "sameAs": [
        "https://www.instagram.com/leisurelooptrip",
        "https://www.facebook.com/leisurelooptrip"
      ]
    }
    </script>

    <?php include 'whatsapp-btn.php'; ?>
    <?php include 'lead-modal.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
