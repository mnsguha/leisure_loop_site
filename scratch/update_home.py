import re

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace initAnimations
old_init = '''function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
'''

new_init = '''function initHeroParallax() {
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
        const geSection = document.getElementById('global-escapes');
        if (!geSection) return;

        const geTl = gsap.timeline({
            scrollTrigger: {
                trigger: geSection,
                start: "top 80%",
                end: "bottom top",
                scrub: 1
            }
        });

        const globe = document.getElementById('ge-globe');
        if (globe) {
            geTl.to(globe.querySelector("svg"), { rotation: 90, ease: "none" }, 0);
        }

        const stardust = document.getElementById('ge-stardust');
        if (stardust) {
            gsap.to(stardust, {
                y: 40,
                ease: "none",
                scrollTrigger: {
                    trigger: geSection,
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
        const airplaneSection = document.getElementById('ge-airplane');
        const plane = airplaneSection ? airplaneSection.querySelector(".the-plane") : null;
        if (!plane) return;

        const geSection = document.getElementById('global-escapes');
        if (!geSection) return;

        const geTl = gsap.timeline({
            scrollTrigger: {
                trigger: geSection,
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
        const nextBtn = document.querySelector('.next-dest');
        const prevBtn = document.querySelector('.prev-dest');
        const destCards = document.querySelectorAll('.dest-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        if (destCarousel) {
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
        }
    } catch(e) { console.warn("initCardSliders error:", e); }
}

function initParallaxEffects() {
    try {
        // Just the other existing effects
        const smilesTextElements = document.querySelectorAll(".smiles-text > *");
        if (smilesTextElements.length > 0) {
            gsap.from(smilesTextElements, {
                scrollTrigger: {
                    trigger: ".real-smiles-section",
                    start: "top 75%",
                },
                y: 40,
                opacity: 0,
                duration: 0.8,
                stagger: 0.15,
                ease: "power3.out"
            });
        }
    } catch(e) { console.warn("initParallaxEffects error:", e); }
}

function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
    
    initHeroParallax();
    initGlobeAnimation();
    initAirplaneFlight();
    initCardSliders();
    initParallaxEffects();
'''

content = content.replace(old_init, new_init)

# Now, we need to wrap bindGlobalEvents
old_bind = '''function bindGlobalEvents() {
    // Generic Event Delegation'''
new_bind = '''function bindGlobalEvents() {
    try {
    // Generic Event Delegation'''

content = content.replace(old_bind, new_bind)

# Close try block in bindGlobalEvents
old_bind_end = '''    const leadForms = document.querySelectorAll('form[action*="/api/submit-lead"], form[action*="/api/v1/leads"], form.lead-form');
    leadForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            // Let the JS handle it or let standard submit happen if we don't preventDefault.
            // Requirement: "prepped to target POST /api/v1/leads without inline execution"
            // We just ensure action is correct.
        });
    });


}'''
new_bind_end = '''    const leadForms = document.querySelectorAll('form[action*="/api/submit-lead"], form[action*="/api/v1/leads"], form.lead-form');
    leadForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            // Let the JS handle it or let standard submit happen if we don't preventDefault.
            // Requirement: "prepped to target POST /api/v1/leads without inline execution"
            // We just ensure action is correct.
        });
    });

    } catch(e) { console.warn("bindGlobalEvents error:", e); }
}'''

content = content.replace(old_bind_end, new_bind_end)

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated home.js")
