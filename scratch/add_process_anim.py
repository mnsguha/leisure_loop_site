import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = '''
function initProcessAnimation() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const processSection = document.querySelector('.how-we-work-section');
    if (!processSection) return;

    const processHeader = processSection.querySelectorAll('.section-label-gold, .section-title, p:not(.process-text)');
    const processCards = processSection.querySelectorAll('.process-card');
    const processImages = processSection.querySelectorAll('.process-img-canvas');

    gsap.set(processHeader, { opacity: 0, y: 30 });
    gsap.set(processCards, { opacity: 0, y: 50, scale: 0.95 });
    if (processImages.length) gsap.set(processImages, { scale: 1.25, filter: 'brightness(0.5)' });

    ScrollTrigger.create({
        trigger: processSection,
        start: 'top 75%',
        onEnter: () => {
            const tl = gsap.timeline();

            tl.to(processHeader, {
                opacity: 1, y: 0, duration: 0.8, stagger: 0.2, ease: 'power3.out'
            });

            tl.to(processCards, {
                opacity: 1, y: 0, scale: 1,
                duration: 0.8, stagger: 0.2, ease: 'power3.out',
                clearProps: 'transform'
            }, '-=0.4');

            if (processImages.length) {
                tl.to(processImages, {
                    scale: 1, filter: 'brightness(1)',
                    duration: 1.6, stagger: 0.2, ease: 'power3.out',
                    clearProps: 'transform'
                }, '-=0.8');
            }
        }
    });
}
'''

# Inject function before initAnimations
content = content.replace('function initAnimations()', new_func + '\nfunction initAnimations()')

# Call it inside initAnimations, after initDistinctionAnimation
content = content.replace(
    'initDistinctionAnimation();',
    'initDistinctionAnimation();\n    initProcessAnimation();'
)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print('home.js: initProcessAnimation() added and wired.')
