import os
import re

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

faq_html = """    <!-- ═══════════════════════════════════════════════════════ -->
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
    </script>"""

# Inject before the final inquiry section
pattern = re.compile(r'<!-- ═══════════════════════════════════════════════════════ -->\s*<!-- CONTACT / INQUIRY SECTION', re.DOTALL)
new_content = pattern.sub(faq_html + '\n\n    <!-- ═══════════════════════════════════════════════════════ -->\n    <!-- CONTACT / INQUIRY SECTION', content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("FAQ injection complete.")
