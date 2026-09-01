import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# ---- JS: Remove boilerplate try-catch from functions that only have guards ----
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Clean up initHeroParallax - remove outer try-catch, keep the logic
content = content.replace(
    '''function initHeroParallax() {
    try {
        const firstVideo = document.querySelector('.hero-bg-video');
        if (firstVideo) {
            firstVideo.play().catch(() => {});
        }

        const mountainBg = document.getElementById('mountainBg');
        const featured = document.getElementById('featured');
        if (mountainBg && featured && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.to(mountainBg, {
                y: -60,
                ease: "none",
                scrollTrigger: {
                    trigger: featured,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }
    } catch (e) {
        console.warn("initHeroParallax error:", e);
    }
}''',
    '''function initHeroParallax() {
    const firstVideo = document.querySelector('.hero-bg-video');
    if (firstVideo) firstVideo.play().catch(() => {});

    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const mountainBg = document.getElementById('mountainBg');
    const featured = document.getElementById('featured');
    if (!mountainBg || !featured) return;

    gsap.to(mountainBg, {
        y: -60,
        ease: "none",
        scrollTrigger: { trigger: featured, start: "top bottom", end: "bottom top", scrub: 1 }
    });
}'''
)

# Clean initGlobeAnimation
content = content.replace(
    '''function initGlobeAnimation() {
    try {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
        const globalEscapes = document.getElementById('global-escapes');
        if (!globalEscapes) return;

        const geGlobe = document.getElementById('ge-globe');
        if (geGlobe) {
            gsap.to(geGlobe.querySelector("svg"), {
                rotation: 90,
                ease: "none",
                scrollTrigger: {
                    trigger: globalEscapes,
                    start: "top 80%",
                    end: "bottom top",
                    scrub: 1
                }
            });
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
                    scrub: 2
                }
            });
        }
    } catch (e) {
        console.warn("initGlobeAnimation error:", e);
    }
}''',
    '''function initGlobeAnimation() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const globalEscapes = document.getElementById('global-escapes');
    if (!globalEscapes) return;

    const geGlobe = document.getElementById('ge-globe');
    const globeSvg = geGlobe?.querySelector('svg');
    if (globeSvg) {
        gsap.to(globeSvg, {
            rotation: 90, ease: "none",
            scrollTrigger: { trigger: globalEscapes, start: "top 80%", end: "bottom top", scrub: 1 }
        });
    }

    const geStardust = document.getElementById('ge-stardust');
    if (geStardust) {
        gsap.to(geStardust, {
            y: 40, ease: "none",
            scrollTrigger: { trigger: globalEscapes, start: "top bottom", end: "bottom top", scrub: 2 }
        });
    }
}'''
)

# Clean initAirplaneFlight
content = content.replace(
    '''function initAirplaneFlight() {
    try {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
        const globalEscapes = document.getElementById('global-escapes');
        const geAirplane = document.getElementById('ge-airplane');
        if (!globalEscapes || !geAirplane) return;

        const plane = geAirplane.querySelector(".the-plane");
        if (!plane) return;

        gsap.fromTo(plane, 
            { left: "0%", xPercent: -100 },
            { 
                left: "100%", 
                xPercent: 100, 
                ease: "none",
                scrollTrigger: {
                    trigger: globalEscapes,
                    start: "top 80%",
                    end: "bottom top",
                    scrub: 1
                }
            }
        );
    } catch (e) {
        console.warn("initAirplaneFlight error:", e);
    }
}''',
    '''function initAirplaneFlight() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const globalEscapes = document.getElementById('global-escapes');
    const geAirplane = document.getElementById('ge-airplane');
    if (!globalEscapes || !geAirplane) return;

    const plane = geAirplane.querySelector('.the-plane');
    if (!plane) return;

    gsap.fromTo(plane,
        { left: "0%", xPercent: -100 },
        { left: "100%", xPercent: 100, ease: "none",
          scrollTrigger: { trigger: globalEscapes, start: "top 80%", end: "bottom top", scrub: 1 } }
    );
}'''
)

# Clean initCardSliders outer try-catch
content = content.replace(
    '    } catch (e) {\n        console.warn("initCardSliders error:", e);\n    }\n}',
    '}'
).replace('function initCardSliders() {\n    try {', 'function initCardSliders() {')

# Clean initCompanyDeck outer try-catch
content = content.replace(
    '    } catch (e) {\n        console.warn("initCompanyDeck error:", e);\n    }\n}',
    '}'
).replace('function initCompanyDeck() {\n    try {', 'function initCompanyDeck() {')

# Clean initDistinctionAnimation outer try-catch
content = content.replace(
    '    } catch (e) {\n        console.warn("initDistinctionAnimation error:", e);\n    }\n}',
    '}'
).replace('function initDistinctionAnimation() {\n    try {', 'function initDistinctionAnimation() {')

# Clean the mandala try-catch inside initAnimations
content = content.replace(
    '''    try {
        const themeMandala = document.getElementById('themeMandala');
        if (themeMandala && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.set(themeMandala, { xPercent: -50, yPercent: -50, x: 0, y: 0 });
            gsap.to(themeMandala, {
                rotation: 90,
                ease: "none",
                scrollTrigger: {
                    trigger: ".themes-curation-section",
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }
    } catch (e) {
        console.warn("Mandala animation error:", e);
    }''',
    '''    const themeMandala = document.getElementById('themeMandala');
    if (themeMandala && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.set(themeMandala, { xPercent: -50, yPercent: -50, x: 0, y: 0 });
        gsap.to(themeMandala, {
            rotation: 90, ease: "none",
            scrollTrigger: { trigger: ".themes-curation-section", start: "top bottom", end: "bottom top", scrub: 1 }
        });
    }'''
)

# Consolidate lifecycle: replace dual readyState check + window.load with single DOMContentLoaded
old_lifecycle = '''// 4. Lifecycle Trigger
function bootHomePage() {
    bindGlobalEvents();
    initAnimations();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootHomePage);
} else {
    bootHomePage();
}

window.addEventListener('load', () => {
    if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.refresh();
    }
});'''

new_lifecycle = '''// 4. Lifecycle
document.addEventListener('DOMContentLoaded', () => {
    bindGlobalEvents();
    initAnimations();
});

window.addEventListener('load', () => {
    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
});'''

content = content.replace(old_lifecycle, new_lifecycle)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
print('home.js cleaned.')
