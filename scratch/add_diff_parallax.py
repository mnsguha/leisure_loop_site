import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = '''function initDifferenceParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const section = document.querySelector('.leisure-difference-section');
    const bg      = document.getElementById('leisure-difference-bg');
    if (!section || !bg) return;

    // Zoom the background image slowly as user scrolls through the section
    gsap.fromTo(bg,
        { scale: 1, transformOrigin: '50% 50%' },
        {
            scale: 1.25, // zooming in
            ease: 'none',
            scrollTrigger: {
                trigger: section,
                start: 'top bottom',
                end:   'bottom top',
                scrub: 1.5
            }
        }
    );

    // Fade up the difference cards sequentially
    const cards = section.querySelectorAll('.difference-card');
    if (cards.length > 0) {
        gsap.from(cards, {
            opacity: 0,
            y: 50,
            duration: 1,
            stagger: 0.2,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.difference-slider-container',
                start: 'top 85%',
                once: true
            }
        });
    }
}'''

content = content.replace(
    'function initAnimations()',
    new_func + '\n\nfunction initAnimations()'
)

content = content.replace(
    '    initCompassParallax();',
    '    initCompassParallax();\n    initDifferenceParallax();'
)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print('home.js: initDifferenceParallax added and wired.')
