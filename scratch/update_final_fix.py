import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# 1. Update index.php
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the leisure-difference-section start tag
content = content.replace('<section class="leisure-difference-section">', '<section class="leisure-difference-section" id="difference">')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

# 2. Update home.css
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

pattern = r'\.leisure-difference-section\s*\{.*?(?=\.difference-slider\s*\{)'
match = re.search(pattern, css, re.DOTALL)

if match:
    new_css = '''/* ====================================
   The Leisure Loop Difference Section
   ==================================== */
.leisure-difference-section {
    position: relative;
    overflow: hidden;
    padding: 8rem 0;
    color: #ffffff;
    background: #050a14 !important;
    min-height: 600px;
}

#leisure-difference-bg {
    position: absolute;
    inset: -10%;
    width: 120%;
    height: 120%;
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    will-change: transform;
    pointer-events: none;
    z-index: 1;
    opacity: 0.65;
}

.difference-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(5, 10, 20, 0.85) 0%, rgba(5, 10, 20, 0.3) 50%, rgba(5, 10, 20, 0.95) 100%);
    z-index: 2;
    pointer-events: none;
}

.difference-container,
.difference-slider-container {
    position: relative;
    z-index: 5;
}

'''
    css = css[:match.start()] + new_css + css[match.end():]
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(css)

# 3. Update home.js
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()

pattern_js = r'initDifferenceParallax\(\)\s*\{.*?(?=\nfunction initAnimations)'
match_js = re.search(pattern_js, js, re.DOTALL)

if match_js:
    new_js = '''initDifferenceParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const section = document.querySelector('.leisure-difference-section');
    const bg = document.getElementById('leisure-difference-bg');
    if (!section || !bg) return;

    gsap.fromTo(bg,
        { scale: 1, yPercent: -8 },
        {
            scale: 1.15,
            yPercent: 8,
            ease: 'none',
            scrollTrigger: {
                trigger: section,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.2
            }
        }
    );
}'''
    js = js[:match_js.start()] + new_js + js[match_js.end():]
    with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
        f.write(js)

print("Updated PHP, CSS, and JS.")
