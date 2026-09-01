with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

import re

# Replace bindGlobalEvents
bind_global = '''function bindGlobalEvents() {
    try {
        document.addEventListener('click', (e) => {
            const arrow = e.target.closest('[data-action="scroll-carousel"]');
            if (arrow) {
                const targetId = arrow.getAttribute('data-carousel');
                const dir = parseInt(arrow.getAttribute('data-dir') || '1');
                const targetTrack = document.getElementById(targetId);
                if (targetTrack) {
                    targetTrack.scrollBy({ left: dir * 350, behavior: 'smooth' });
                }
                return;
            }

            const filterBtn = e.target.closest('.destinations-filter .filter-btn');
            if (filterBtn) {
                document.querySelectorAll('.destinations-filter .filter-btn').forEach(b => b.classList.remove('active'));
                filterBtn.classList.add('active');
                const filter = filterBtn.getAttribute('data-filter');
                document.querySelectorAll('.destinations-carousel .dest-card').forEach(card => {
                    const cat = card.getAttribute('data-category');
                    if (filter === 'domestic' && cat === 'international') card.classList.add('hidden');
                    else if (filter === 'international' && cat !== 'international') card.classList.add('hidden');
                    else card.classList.remove('hidden');
                });
                return;
            }

            const modalBtn = e.target.closest('[data-action="open-modal"]');
            if (modalBtn) {
                const target = document.querySelector(modalBtn.getAttribute('data-target'));
                if (target) target.style.display = 'flex';
                return;
            }
            
            const closeBtn = e.target.closest('[data-action="close-modal"], .close-modal');
            if (closeBtn) {
                const modal = closeBtn.closest('.modal, .step2-modal-overlay');
                if (modal) modal.style.display = 'none';
                return;
            }
        });
    } catch(e) { console.warn("bindGlobalEvents error:", e); }
}
'''

content = re.sub(r'function bindGlobalEvents\(\) \{.*?\n\}\n(?=\n?function|\Z)', bind_global, content, flags=re.DOTALL)

# Add ScrollTriggers to initAnimations if they aren't there
scroll_triggers = '''
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.to("#mountainBg", {
            y: 60,
            ease: "none",
            scrollTrigger: { trigger: "#featured", start: "top bottom", end: "bottom top", scrub: 1 }
        });
        gsap.to("#themeMandala", {
            rotation: 360,
            ease: "none",
            scrollTrigger: { trigger: ".themes-curation-section", start: "top bottom", end: "bottom top", scrub: 2 }
        });
    }
'''
if 'mountainBg' not in content:
    content = content.replace('ScrollTrigger.refresh();', scroll_triggers + '\n    ScrollTrigger.refresh();')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
