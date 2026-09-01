# -*- coding: utf-8 -*-
import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = (
    '\nfunction initCompassParallax() {\n'
    "    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;\n"
    "\n"
    "    const compassSection = document.querySelector('.fixed-departures-section');\n"
    "    if (!compassSection) return;\n"
    "\n"
    "    const ring1          = document.getElementById('compass-ring-1');\n"
    "    const ring2          = document.getElementById('compass-ring-2');\n"
    "    const ring3          = document.getElementById('compass-ring-3');\n"
    "    const compassWrapper = document.querySelector('.fd-parallax-compass');\n"
    "\n"
    "    const compassTl = gsap.timeline({\n"
    "        scrollTrigger: {\n"
    "            trigger: compassSection,\n"
    "            start: 'top bottom',\n"
    "            end:   'bottom top',\n"
    "            scrub: 1\n"
    "        }\n"
    "    });\n"
    "\n"
    "    if (compassWrapper) compassTl.to(compassWrapper, { y: -250, ease: 'none' }, 0);\n"
    "    if (ring1) compassTl.to(ring1, { rotation:  180, ease: 'none', transformOrigin: '50% 50%' }, 0);\n"
    "    if (ring2) compassTl.to(ring2, { rotation: -240, ease: 'none', transformOrigin: '50% 50%' }, 0);\n"
    "    if (ring3) compassTl.to(ring3, { rotation:  360, ease: 'none', transformOrigin: '50% 50%' }, 0);\n"
    '}\n'
)

content = content.replace(
    'function initAnimations()',
    new_func + '\nfunction initAnimations()'
)

content = content.replace(
    '    initNewsletterParallax();',
    '    initNewsletterParallax();\n    initCompassParallax();'
)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()

print('initCompassParallax defined:', 'function initCompassParallax()' in js)
print('wired in initAnimations:', 'initCompassParallax();' in js)
print('ring1 targeted:', 'compass-ring-1' in js)
print('compassWrapper targeted:', 'fd-parallax-compass' in js)
