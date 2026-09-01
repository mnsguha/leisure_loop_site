import re

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = '''function initDistinctionAnimation() {
    try {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
        const distinctionItems = gsap.utils.toArray('.distinction-item');
        if (distinctionItems.length > 0) {
            gsap.set(distinctionItems, { y: 60, opacity: 0 });
            ScrollTrigger.batch(distinctionItems, {
                start: "top 85%",
                onEnter: batch => gsap.to(batch, {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.15,
                    ease: "power2.out",
                    overwrite: true
                }),
                onLeaveBack: batch => gsap.set(batch, { opacity: 0, y: 60, overwrite: true })
            });
        }
    } catch (e) {
        console.warn("initDistinctionAnimation error:", e);
    }
}
'''

if 'function initDistinctionAnimation()' not in content:
    content = content.replace('function initAnimations()', new_func + '\nfunction initAnimations()')
    
if 'initDistinctionAnimation();' not in content:
    content = content.replace('initCardSliders();', 'initCardSliders();\n    initDistinctionAnimation();')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
