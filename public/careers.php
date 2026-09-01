<?php 
    $page_title = "Careers | Leisure Loop Trip";
    $meta_desc = "Join our passionate team at Leisure Loop Trip. We are looking for experienced travel curators, luxury itinerary designers, and hospitality professionals.";
    include '../includes/header.php'; 
?>

    <section class="section" style="padding-top: 12rem; padding-bottom: 8rem;">
        <div class="container" style="max-width: 900px;">
            <span class="section-label" style="color: var(--gold); letter-spacing: 0.2em; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Opportunities</span>
            <h1 class="serif" style="font-size: 3.2rem; margin-bottom: 2rem; color: #fff;">Build the Future of Travel</h1>
            
            <div style="color: var(--text-muted); line-height: 1.8; font-size: 1.1rem;">
                <p style="margin-bottom: 2.5rem;">At Leisure Loop Trip, our mission is crafting cinematic, soul-stirring travel adventures across India and premier global destinations. We believe exceptional journeys start with an exceptional team of storytellers, explorers, and service perfectionists.</p>

                <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 2.5rem; margin-bottom: 2.5rem;">
                    <h3 style="color: #fff; margin: 0 0 1rem; font-size: 1.5rem; font-family: 'Playfair Display', serif;">Why Work With Us?</h3>
                    <ul style="padding-left: 1.2rem; margin-bottom: 0; color: rgba(255, 255, 255, 0.8);">
                        <li style="margin-bottom: 0.8rem;"><strong>Bespoke Exploration:</strong> Create curated itineraries that redefine modern travel.</li>
                        <li style="margin-bottom: 0.8rem;"><strong>Growth & Continuous Learning:</strong> Work alongside industry experts with extensive firsthand knowledge of the Himalayas and beyond.</li>
                        <li style="margin-bottom: 0;"><strong>A Culture of Hospitality:</strong> Experience an environment built on trust, excellence, and genuine passion for discovery.</li>
                    </ul>
                </div>

                <h3 style="color: #fff; margin: 2.5rem 0 1rem; font-size: 1.8rem; font-family: 'Playfair Display', serif;">Open Positions & General Applications</h3>
                <p style="margin-bottom: 2rem;">We are always looking for dynamic individuals to enrich our dynamic growth in the following focus areas:</p>
                
                <div style="display: grid; gap: 1.5rem; margin-bottom: 3.5rem;">
                    <div style="padding: 1.5rem; border-left: 3px solid var(--gold, #c5a059); background: rgba(0,0,0,0.25);">
                        <h4 style="color: #fff; margin: 0 0 0.5rem; font-size: 1.25rem;">Luxury Travel Curators & Itinerary Specialists</h4>
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-muted);">Experienced professionals with deep knowledge of high-end travel logistics and customer rapport.</p>
                    </div>
                    
                    <div style="padding: 1.5rem; border-left: 3px solid var(--gold, #c5a059); background: rgba(0,0,0,0.25);">
                        <h4 style="color: #fff; margin: 0 0 0.5rem; font-size: 1.25rem;">Guest Relations & Tour Operations Managers</h4>
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-muted);">Dedicated problem solvers ensuring flawless ground operations, hotel partnerships, and 24/7 client happiness.</p>
                    </div>

                    <div style="padding: 1.5rem; border-left: 3px solid var(--gold, #c5a059); background: rgba(0,0,0,0.25);">
                        <h4 style="color: #fff; margin: 0 0 0.5rem; font-size: 1.25rem;">Content Creators & Travel Explorers</h4>
                        <p style="margin: 0; font-size: 0.95rem; color: var(--text-muted);">Visual storytellers, photographers, and writers capable of bringing destinations to life.</p>
                    </div>
                </div>

                <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 2.5rem; text-align: center;">
                    <h3 style="color: #fff; margin: 0 0 1rem;">Ready to Join the Loop?</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">Send your resume and a cover note detailing your favorite travel memory to our talent team.</p>
                    <a href="mailto:<?php echo htmlspecialchars($settings['contact_email'] ?? 'enquiry@leisurelooptrip.com'); ?>?subject=Career%20Inquiry%20-%20Leisure%20Loop%20Trip" class="btn btn-primary" style="display: inline-block; padding: 1rem 2.5rem; background: var(--gold, #c5a059); color: #030811; font-weight: 600; text-decoration: none; border-radius: 4px;">Send Your Application</a>
                </div>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
