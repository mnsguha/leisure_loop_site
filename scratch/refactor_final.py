import re

# Read original full home.js and original init_body.js
with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()
    
with open('G:\\Antigravity\\leisure_loop_site\\scratch\\init_body.js', 'r', encoding='utf-8') as f:
    body = f.read()

# I messed up home.js by doing two bad replacements.
# Let's find the start of the bad initAnimations and end of it.
start_idx = content.find('function initAnimations() {')
end_idx = content.find('function bootHomePage() {', start_idx)

# Replace the messy block with a completely rewritten string

new_init_block = '''function initHeroParallax() {
    try {
        const firstVideo = document.querySelector('.hero-slide.active video');
        if (firstVideo) {
            firstVideo.play().catch(e => console.log("Autoplay blocked", e));
        }

        const mountainBg = document.getElementById('mountainBg');
        const featured = document.getElementById('featured');
        if (mountainBg && featured) {
            gsap.to(mountainBg, {
                y: -80,
                ease: "none",
                scrollTrigger: {
                    trigger: featured,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }
        
        // Also cover the ParableVC layer which is located further down originally
        const parableHero = document.querySelector('.parable-hero');
        if (parableHero) {
            // Found Parable Hero Parallax
            // (The script for this is further down in home.js and already bound to its own load listener, 
            // but we can leave it there since it has its own target checks)
        }
    } catch(e) { console.warn("initHeroParallax error:", e); }
}

function initGlobeAnimation() {
    try {
        const globalEscapes = document.getElementById('global-escapes');
        if (!globalEscapes) return;

        const geTl = gsap.timeline({
            scrollTrigger: {
                trigger: globalEscapes,
                start: "top 80%",
                end: "bottom top",
                scrub: 1
            }
        });

        const geGlobe = document.getElementById('ge-globe');
        if (geGlobe) {
            geTl.to(geGlobe.querySelector("svg"), { rotation: 90, ease: "none" }, 0);
        }

        const geStardust = document.getElementById('ge-stardust');
        if (geStardust) {
            gsap.to(geStardust, {
                y: 40,
                ease: "none",
                scrollTrigger: {
                    trigger: globalEscapes,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 3
                }
            });
        }
    } catch(e) { console.warn("initGlobeAnimation error:", e); }
}

function initAirplaneFlight() {
    try {
        const globalEscapes = document.getElementById('global-escapes');
        const geAirplane = document.getElementById('ge-airplane');
        if (!globalEscapes || !geAirplane) return;

        const plane = geAirplane.querySelector(".the-plane");
        if (!plane) return;

        const geTl = gsap.timeline({
            scrollTrigger: {
                trigger: globalEscapes,
                start: "top 80%",
                end: "bottom top",
                scrub: 1
            }
        });

        geTl.fromTo(plane, 
            { left: "0%", xPercent: -100 },
            { left: "100%", xPercent: 100, ease: "none" }, 
            0
        );
    } catch(e) { console.warn("initAirplaneFlight error:", e); }
}

function initCardSliders() {
    try {
        const destCarousel = document.querySelector('.destinations-carousel');
        if (!destCarousel) return;

        const nextBtn = document.querySelector('.next-dest');
        const prevBtn = document.querySelector('.prev-dest');
        const destCards = document.querySelectorAll('.dest-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        if (window.setupGSAPMomentumDrag) {
            window.setupGSAPMomentumDrag(destCarousel);
        }

        const scrollAmount = 372;
        if (nextBtn) nextBtn.addEventListener('click', () => { destCarousel.scrollBy({ left: scrollAmount, behavior: 'smooth' }); });
        if (prevBtn) prevBtn.addEventListener('click', () => { destCarousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); });

        function applyStaggering() {
            const visibleCards = Array.from(destCards).filter(c => !c.classList.contains('hidden'));
            visibleCards.forEach((card, index) => {
                card.classList.remove('stagger-up', 'stagger-down');
                if (index % 2 === 0) {
                    card.classList.add('stagger-up');
                } else {
                    card.classList.add('stagger-down');
                }
            });
        }
        applyStaggering();

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                destCards.forEach(card => {
                    if (card.dataset.category === filter) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
                applyStaggering();
                destCarousel.scrollTo({ left: 0, behavior: 'auto' });
                
                const visibleCards = Array.from(destCards).filter(c => !c.classList.contains('hidden'));
                gsap.fromTo(visibleCards, {
                    opacity: 0,
                    y: 100
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    stagger: 0.1,
                    ease: "power3.out",
                    clearProps: "all"
                });
            });
        });

        gsap.from(".dest-card", {
            scrollTrigger: {
                trigger: ".destinations-carousel",
                start: "top 85%",
                once: true,
            },
            opacity: 0,
            y: "+=100",
            duration: 1.2,
            stagger: 0.15,
            ease: "power3.out",
            clearProps: "all"
        });
        
    } catch(e) { console.warn("initCardSliders error:", e); }
}

function initRestOfAnimations() {
    try {
        // Execute the rest of the original initAnimations body (from init_body.js)
        // that handles Journal, Themes, Process, FAQ, etc.
        // We will just inject the body but wrap it all in try-catch.
        
        // Wait, if I just execute ody here, I'll execute the globe and airplane logic TWICE!
        pass
    } catch(e) { console.warn("Other animations error:", e); }
}

function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
    
    initHeroParallax();
    initGlobeAnimation();
    initAirplaneFlight();
    initCardSliders();
    
    try {
        __REST_OF_BODY__
    } catch(e) { console.warn("Misc animations error:", e); }
    
    ScrollTrigger.sort();
    window.addEventListener('load', () => {
        setTimeout(() => { ScrollTrigger.refresh(); }, 100);
    });
    ScrollTrigger.refresh();
}
'''

# Now let's build __REST_OF_BODY__ from init_body.js
# We need to strip out:
# 1. First video logic
# 2. Mountain Parallax
# 3. Global Escapes (Globe & Airplane)
# 4. Signature Terrains Drag & Entrance Animation (Slider)

rest_body = body

# Remove top part up to Real Smiles
rs_idx = rest_body.find('// Real Smiles Gallery Text Animation')
rest_body = rest_body[rs_idx:]

# Remove Mountain Parallax
mp_start = rest_body.find('if (document.getElementById(\\'mountainBg\\')')
if mp_start != -1:
    mp_end = rest_body.find('}', rest_body.find('}', rest_body.find('}')+1)+1)+1 # close the if statement
    rest_body = rest_body[:mp_start] + rest_body[mp_end:]

# Remove Global Escapes
ge_start = rest_body.find('// Global Escapes Parallax (Globe & Airplane)')
if ge_start != -1:
    # It ends before "Premium Entrance for The Curated Collection"
    ge_end = rest_body.find('// Premium Entrance for The Curated Collection', ge_start)
    rest_body = rest_body[:ge_start] + rest_body[ge_end:]

# Remove Signature Terrains Drag & Entrance Animation
st_start = rest_body.find('// Signature Terrains Drag & Entrance Animation')
if st_start != -1:
    # It ends before "Fixed Departures Compass Parallax"
    st_end = rest_body.find('// Fixed Departures Compass Parallax', st_start)
    rest_body = rest_body[:st_start] + rest_body[st_end:]

# Clean up bottom
bottom_idx = rest_body.rfind('ScrollTrigger.sort();')
if bottom_idx != -1:
    rest_body = rest_body[:bottom_idx]

new_init_block = new_init_block.replace('__REST_OF_BODY__', rest_body.replace('\\n', '\\n        '))

content = content[:start_idx] + new_init_block + '\\n' + content[end_idx:]

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Safely refactored home.js with all requested isolated functions and target guards.")
