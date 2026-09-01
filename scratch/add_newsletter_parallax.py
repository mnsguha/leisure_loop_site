import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = '''
function initNewsletterParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const banner = document.querySelector('.inner-circle-banner');
    const bg     = document.getElementById('inner-circle-bg');
    if (!banner || !bg) return;

    // Scale the bg slightly larger than 100% so the parallax
    // translate never reveals edges
    gsap.set(bg, { scale: 1.15, transformOrigin: '50% 50%' });

    // Slow vertical drift: bg moves up 8% as user scrolls through section
    gsap.to(bg, {
        yPercent: -8,
        ease: 'none',
        scrollTrigger: {
            trigger: banner,
            start: 'top bottom',
            end:   'bottom top',
            scrub: 1.5
        }
    });

    // Content fade-up on enter
    const content = banner.querySelector('.newsletter-container');
    if (content) {
        gsap.from(content, {
            opacity: 0,
            y: 40,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: banner,
                start: 'top 75%',
                once: true
            }
        });
    }
}
'''

# Inject before initAnimations
content = content.replace(
    'function initAnimations()',
    new_func + '\nfunction initAnimations()'
)

# Wire into initAnimations after initProcessAnimation
content = content.replace(
    '    initProcessAnimation();',
    '    initProcessAnimation();\n    initNewsletterParallax();'
)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print('home.js: initNewsletterParallax() added and wired.')

# Verify
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()
print('Parallax function present:', 'initNewsletterParallax' in js)
print('Wired in initAnimations:', 'initNewsletterParallax();' in js)
print('Targets inner-circle-banner:', 'inner-circle-banner' in js)
print('Targets inner-circle-bg:', 'inner-circle-bg' in js)
