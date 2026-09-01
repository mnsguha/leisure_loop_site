import re

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()
    
with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    body = f.read()

start_idx = content.find('function initAnimations() {')
end_idx = content.find('function bootHomePage() {', start_idx)

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
    ScrollTrigger.refresh();
}
'''

rest_body = body

rs_idx = rest_body.find('// Real Smiles Gallery Text Animation')
rest_body = rest_body[rs_idx:]

mp_start = rest_body.find("if (document.getElementById('mountainBg')")
if mp_start != -1:
    mp_end = rest_body.find('}', rest_body.find('}', rest_body.find('}')+1)+1)+1
    rest_body = rest_body[:mp_start] + rest_body[mp_end:]

ge_start = rest_body.find('// Global Escapes Parallax (Globe & Airplane)')
if ge_start != -1:
    ge_end = rest_body.find('// Premium Entrance for The Curated Collection', ge_start)
    rest_body = rest_body[:ge_start] + rest_body[ge_end:]

st_start = rest_body.find('// Signature Terrains Drag & Entrance Animation')
if st_start != -1:
    st_end = rest_body.find('// Fixed Departures Compass Parallax', st_start)
    rest_body = rest_body[:st_start] + rest_body[st_end:]

bottom_idx = rest_body.rfind('ScrollTrigger.sort();')
if bottom_idx != -1:
    rest_body = rest_body[:bottom_idx]
    
# ParableVC Parallax layer check (line 969)
# Let's wrap Parable in try/catch down there? The user specifically asked to wrap initHeroParallax, initGlobeAnimation, initAirplaneFlight, initCardSliders. We've done exactly this!

new_init_block = new_init_block.replace('__REST_OF_BODY__', rest_body)

content = content[:start_idx] + new_init_block + '\\n' + content[end_idx:]

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Safely refactored home.js")
