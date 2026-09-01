<?php 
    $page_title = "Contact Our Curators | Leisure Loop Trip";
    include '../includes/header.php'; 
?>

    <header class="section" style="padding-top: 12rem;">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: center;">
            <div>
                <span class="section-label">Get in Touch</span>
                <h1 class="section-title">Design Your <br><span class="serif">Next Escape.</span></h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 3rem;">Ready to embark on a journey like no other? Our curators are standing by to craft your bespoke itinerary.</p>
                
                <div style="margin-bottom: 3rem;">
                    <h4 style="color: var(--gold); margin-bottom: 1rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.2em;">Direct Lines</h4>
                    <p style="font-size: 1.2rem; margin-bottom: 0.5rem;">+91 89189 21629</p>
                    <p style="color: var(--text-muted);">curator@leisurelooptrip.in</p>
                </div>

                <div>
                    <h4 style="color: var(--gold); margin-bottom: 1rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.2em;">Headquarters</h4>
                    <p style="color: var(--text-muted);">Siliguri, West Bengal, India<br>Gateways to the Eastern Himalayas.</p>
                </div>
            </div>

            <div class="glass-card" style="background: var(--glass); border: 1px solid var(--glass-border); padding: 4rem; border-radius: 4px;">
                <form id="contactForm" action="../api/submit-lead.php" method="POST" class="js-lead-form">
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Your Name</label>
                        <input type="text" name="name" required style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none;">
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Email Address</label>
                        <input type="email" name="email" required style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none;">
                    </div>
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Phone Number</label>
                        <input type="tel" name="phone" required style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none;">
                    </div>
                    <div style="margin-bottom: 3rem;">
                        <label style="display: block; font-size: 0.7rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">Message / Destinations</label>
                        <textarea name="message" rows="4" style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 0; color: white; outline: none; resize: none;"></textarea>
                    </div>
                    <button type="submit" class="btn-gold" style="width: 100%;">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </header>

<?php include '../includes/footer.php'; ?>
