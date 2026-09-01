import os

file_path = 'g:/Antigravity/leisure_loop_site/public/index.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# The content currently ends after the hero section, before the footer include.
# Let's rebuild the missing sections.

stats_strip = """
    <!-- =========================================================== -->
    <!-- STATS / TRUST STRIP                                    -->
    <!-- =========================================================== -->
    <section class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 2V22"/><path d="M2 12H22"/><path d="M12 2C14.5013 4.73835 15.9228 8.29203 15.9228 12C15.9228 15.708 14.5013 19.2616 12 22"/><path d="M12 2C9.49872 4.73835 8.07725 8.29203 8.07725 12C8.07725 15.708 9.49872 19.2616 12 22"/></svg>
                    </div>
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Bespoke Journeys</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M6 9H18L19 21H5L6 9Z"/><path d="M6 9C6 9 3 9 3 13C3 17 6 17 6 17"/><path d="M18 9C18 9 21 9 21 13C21 17 18 17 18 17"/><path d="M12 2V6"/></svg>
                    </div>
                    <span class="stat-number">12+</span>
                    <span class="stat-label">Years Excellence</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    </div>
                    <span class="stat-number">4.9</span>
                    <span class="stat-label">Client Rating</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 15L17 18V5H7V18L12 15Z"/><path d="M12 2V5"/></svg>
                    </div>
                    <span class="stat-number">Elite</span>
                    <span class="stat-label">Award Winner 2024</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61C20.3292 4.09904 19.7228 3.69363 19.0554 3.41708C18.388 3.14053 17.6725 2.99816 16.95 3C15.4812 3.0001 14.0728 3.5828 13.03 4.62L12 5.67L10.97 4.63C9.92723 3.58723 8.51278 3.00008 7.045 3C6.32247 2.99816 5.60703 3.14053 4.93963 3.41708C4.27222 3.69363 3.6658 4.09904 3.155 4.61C1.047 6.718 1.047 10.138 3.155 12.246L12 21.091L20.845 12.246C22.953 10.138 22.953 6.718 20.84 4.61Z"/></svg>
                    </div>
                    <span class="stat-number">99%</span>
                    <span class="stat-label">Happy Explorers</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M3 18H21"/><path d="M12 18V4"/><path d="M8 4H16"/><path d="M3 14C3 14 3 6 12 6C21 6 21 14 21 14"/></svg>
                    </div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Priority Support</span>
                </div>
            </div>
        </div>
    </section>
"""

packages_section = """
    <!-- =========================================================== -->
    <!-- MOST COVETED JOURNEYS                                  -->
    <!-- =========================================================== -->
    <?php 
        $featured_packages = [];
        if ($pdo) {
            $featured_packages = $pdo->query("SELECT * FROM packages WHERE is_active = 1 LIMIT 4")->fetchAll();
        }
    ?>
    <section class="section" id="experiences">
        <div class="container">
            <span class="section-label">Exclusive Offers</span>
            <h2 class="section-title">Most Coveted <span style="color: var(--gold); font-style: italic;">Journeys.</span></h2>
            
            <div class="grid-reveal" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <?php foreach ($featured_packages as $pkg): ?>
                <a href="package-detail.php?slug=<?php echo $pkg['slug']; ?>" class="experience-card" style="text-decoration: none;">
                    <div class="exp-badge" style="background: rgba(0,0,0,0.8); color: var(--gold); border: 1px solid var(--gold); border-radius: 50px; font-size: 0.6rem; letter-spacing: 0.1em; padding: 6px 12px; font-weight: 600; text-transform: uppercase;">
                        <?php 
                        $itinerary = json_decode($pkg['itinerary'], true);
                        if ($itinerary && is_array($itinerary)) {
                            $days = count($itinerary);
                            $nights = max(1, $days - 1);
                            echo $nights . " NIGHTS / " . $days . " DAYS";
                        } else {
                            echo "CUSTOM DURATION";
                        }
                        ?>
                    </div>
                    <div class="exp-img" style="background-image: url('<?php echo htmlspecialchars($pkg['image_url']); ?>');"></div>
                    <div class="exp-overlay" style="display: flex; flex-direction: column;">
                        <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 5px;"><?php echo htmlspecialchars(strtoupper(explode(' ', $pkg['title'])[0])); ?></span>
                        <h3 style="font-size: 1.25rem; font-family: 'Playfair Display', serif; margin-bottom: 15px; font-weight: 400;"><?php echo htmlspecialchars($pkg['title']); ?></h3>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                            <div style="font-size: 0.65rem; color: rgba(255,255,255,0.5); letter-spacing: 0.1em;">
                                FROM <span class="price-main" style="color: #fff; font-size: 1.1rem; font-weight: 600; margin-left: 5px;">&#8377;<?php echo number_format($pkg['price']); ?></span>
                                <?php if(!empty($pkg['original_price']) && $pkg['original_price'] > $pkg['price']): ?>
                                    <span class="price-original" style="text-decoration: line-through; color: rgba(255,255,255,0.3); margin-left: 5px;">&#8377;<?php echo number_format($pkg['original_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gold); color: #000; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: bold; transition: all 0.3s ease;">
                                &rarr;
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
"""

faq_section = """
    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- FAQ SECTION                                            -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="faq-section">
        <div class="container">
            <span class="section-label">Knowledge Base</span>
            <h2 class="section-title">Common <span style="color: var(--gold); font-style: italic;">Curiosities.</span></h2>

            <div class="faq-grid">
                <!-- Column 1 -->
                <div class="faq-col">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>What defines a 'Bespoke' Leisure Loop journey?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>Every journey is crafted from scratch based on your personal preferences, pace, and interests. No two itineraries are ever identical.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Do you provide a dedicated concierge during travel?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, all our elite packages include 24/7 priority concierge support to handle any spontaneous requests or changes during your trip.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Can I request exclusive off-grid experiences?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutely. We specialize in private access to remote locations, private villas, and unique cultural encounters not available to the public.</p>
                        </div>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="faq-col">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>How are flight and luxury transfers managed?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>We coordinate all logistics, from business class airfare to private chauffeur-driven vehicles, ensuring a seamless door-to-door experience.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>Is travel insurance included in the packages?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>While we don't include it by default, we highly recommend and can facilitate comprehensive premium travel insurance for all our guests.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4>What is your priority booking process?</h4>
                            <div class="faq-icon">&#10010;</div>
                        </div>
                        <div class="faq-answer">
                            <p>After your initial inquiry, a senior curator will contact you for a consultation. Once the design is approved, a secure deposit secures your dates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.querySelectorAll('.faq-question').forEach(q => {
        q.addEventListener('click', () => {
            const item = q.parentElement;
            item.classList.toggle('active');
        });
    });
    </script>
"""

# Reconstruct everything
new_content = content.replace("<?php include '../includes/footer.php'; ?>", stats_strip + packages_section + faq_section + "\n<?php include '../includes/footer.php'; ?>")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Index page reconstructed.")
