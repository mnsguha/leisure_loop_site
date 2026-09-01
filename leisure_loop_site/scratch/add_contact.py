import os

file_path = 'g:/Antigravity/leisure_loop_site/public/index.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

contact_section = """
    <!-- =========================================================== -->
    <!-- CONTACT SECTION                                        -->
    <!-- =========================================================== -->
    <section class="section" id="contact" style="background: linear-gradient(rgba(5,10,20,0.85), rgba(5,10,20,0.85)), url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2000'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 8rem; align-items: center;">
            <div>
                <span class="section-label">Start Your Story</span>
                <h2 class="section-title">Where to <br><span class="serif" style="color: var(--amber);">Next?</span></h2>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Tell us about your dream escape, and our travel curators will craft a journey just for you.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                    <div>
                        <h4 style="color: var(--amber); margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.1em;">Email Us</h4>
                        <p style="font-size: 0.9rem;">curator@leisurelooptrip.in</p>
                    </div>
                    <div>
                        <h4 style="color: var(--amber); margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.1em;">WhatsApp</h4>
                        <p style="font-size: 0.9rem;">+91 89189 21629</p>
                    </div>
                </div>
            </div>

            <div class="glass-card" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); padding: 4rem; border-radius: 8px;">
                <form id="contactForm" action="api/submit-lead.php" method="POST">
                    <div style="margin-bottom: 2.5rem;">
                        <label style="display: block; font-size: 0.6rem; color: var(--amber); letter-spacing: 0.2em; margin-bottom: 0.5rem;">FULL NAME</label>
                        <input type="text" name="name" placeholder="E.g. Alexander Pierce" style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.5rem 0; color: white; outline: none; font-size: 1.1rem; font-family: 'Outfit';" required>
                    </div>
                    <div style="margin-bottom: 2.5rem;">
                        <label style="display: block; font-size: 0.6rem; color: var(--amber); letter-spacing: 0.2em; margin-bottom: 0.5rem;">PHONE NUMBER</label>
                        <input type="tel" name="phone" placeholder="+91 98765 43210" style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.5rem 0; color: white; outline: none; font-size: 1.1rem; font-family: 'Outfit';" required>
                    </div>
                    <div style="margin-bottom: 3rem;">
                        <label style="display: block; font-size: 0.6rem; color: var(--amber); letter-spacing: 0.2em; margin-bottom: 0.5rem;">TELL US YOUR DREAM</label>
                        <textarea name="destination" placeholder="Describe the experience you seek..." rows="3" style="width: 100%; background: transparent; border: none; border-bottom: 1px solid var(--glass-border); padding: 0.5rem 0; color: white; outline: none; font-size: 1.1rem; font-family: 'Outfit'; resize: none;"></textarea>
                    </div>
                    <button type="submit" class="btn-card" style="width: 100%; padding: 1.2rem;">Begin Your Journey &nbsp; &rarr;</button>
                </form>
            </div>
        </div>
    </section>
"""

if "<!-- CONTACT SECTION" not in content:
    new_content = content.replace("<?php include '../includes/footer.php'; ?>", contact_section + "\n<?php include '../includes/footer.php'; ?>")
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Contact section added.")
else:
    print("Contact section already exists.")
