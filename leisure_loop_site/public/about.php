<?php 
    $page_title = "Our Story | Leisure Loop Trip";
    include '../includes/header.php'; 
?>

    <header class="section" style="padding-top: 12rem; text-align: center;">
        <div class="container">
            <span class="section-label">Legacy & Vision</span>
            <h1 class="section-title">The Art of <br><span class="serif">Bespoke Travel.</span></h1>
            <p style="max-width: 700px; margin: 0 auto; color: var(--text-muted); font-size: 1.1rem;">Founded on the principle that travel should be more than just a destination. It should be a masterpiece of experiences curated for the soul.</p>
        </div>
    </header>

    <section class="section" style="background: rgba(255,255,255,0.02);">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: center;">
            <div style="position: relative;">
                <img src="https://images.unsplash.com/photo-1519681393784-d120267973ba?q=80&w=1200&auto=format&fit=crop" alt="Mountain View" style="width: 100%; border-radius: 4px; filter: grayscale(0.3);">
                <div style="position: absolute; bottom: 2rem; right: 2rem; background: var(--amber); padding: 2rem; border-radius: 4px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
                    <span style="display: block; font-size: 2.5rem; font-weight: 800; line-height: 1;">10+</span>
                    <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Years of Excellence</span>
                </div>
            </div>
            <div>
                <span class="section-label">The Leisure Standard</span>
                <h2 class="serif" style="font-size: 3rem; margin-bottom: 2rem;">Crafted for the <br>Extraordinary.</h2>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">At Leisure Loop Trip, we don't just book tours; we design journeys. From the hidden monasteries of Sikkim to the pristine valleys of Kashmir, our curators scout every location to ensure it meets our elite standard of comfort and immersion.</p>
                <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Our mission is to bring a new level of cinematic professionalism to the travel industry in India, ensuring every customer feels like a protagonist in their own adventure.</p>
                <a href="contact.php" class="btn-gold">Begin Your Story</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container" style="text-align: center;">
            <span class="section-label">Why Choose Us</span>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; margin-top: 4rem;">
                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 3rem; border-radius: 4px;">
                    <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gold);">Expert Curation</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Every itinerary is hand-crafted by travel experts who have personally lived the experiences we offer.</p>
                </div>
                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 3rem; border-radius: 4px;">
                    <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gold);">Elite Service</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">24/7 on-ground support and dedicated travel curators to handle every detail of your journey.</p>
                </div>
                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 3rem; border-radius: 4px;">
                    <h3 class="serif" style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--gold);">Cinematic Stays</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">We partner exclusively with boutique hotels and heritage properties that offer breathtaking views and soul.</p>
                </div>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>

