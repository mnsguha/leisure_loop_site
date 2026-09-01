with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

import re

# Replace mountainBg scrollTrigger
content = re.sub(r'gsap\.to\(mountainBg, \{\s*y: -80,\s*ease: "none",\s*scrollTrigger: \{.*?\}.*?\}\);',
                 'gsap.to(mountainBg, {\n    y: 60,\n    ease: "none",\n    scrollTrigger: {\n        trigger: "#featured",\n        start: "top bottom",\n        end: "bottom top",\n        scrub: 1\n    }\n});', content, flags=re.DOTALL)

# Replace themeMandala scrollTrigger
content = re.sub(r'gsap\.to\(themeMandala, \{\s*rotation: 45,\s*y: "30%",\s*ease: "none",\s*scrollTrigger: \{.*?\}.*?\}\);',
                 'gsap.to(themeMandala, {\n    rotation: 360,\n    ease: "none",\n    scrollTrigger: {\n        trigger: ".themes-curation-section",\n        start: "top bottom",\n        end: "bottom top",\n        scrub: 2\n    }\n});', content, flags=re.DOTALL)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
