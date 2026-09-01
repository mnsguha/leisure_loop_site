import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

old_func = '''function initNewsletterParallax() {
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
}'''

new_func = '''function initNewsletterParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const banner = document.querySelector('.inner-circle-banner');
    const bg     = document.getElementById('inner-circle-bg');
    if (!banner || !bg) return;

    // Start at normal scale, zoom in as user scrolls through
    gsap.fromTo(bg,
        { scale: 1, transformOrigin: '50% 50%' },
        {
            scale: 1.18,
            ease: 'none',
            scrollTrigger: {
                trigger: banner,
                start: 'top bottom',
                end:   'bottom top',
                scrub: 1.5
            }
        }
    );

    // Content fade-up on enter
    const newsletterContent = banner.querySelector('.newsletter-container');
    if (newsletterContent) {
        gsap.from(newsletterContent, {
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
}'''

content = content.replace(old_func, new_func)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print('home.js: Animation updated to scale/zoom.')
print('gsap.fromTo scale present:', 'scale: 1.18' in content)
