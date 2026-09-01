import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Replace the initDifferenceParallax function
pattern = r'initDifferenceParallax\(\)\s*\{.*?(?=\nfunction initAnimations)'
match = re.search(pattern, content, re.DOTALL)
if match:
    new_js = '''initDifferenceParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const section = document.querySelector('.leisure-difference-section');
    const bg = document.getElementById('leisure-difference-bg');
    if (!section || !bg) return;

    gsap.fromTo(bg,
        { scale: 1, yPercent: -5, transformOrigin: '50% 50%' },
        {
            scale: 1.18,
            yPercent: 5,
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
    content = content[:match.start()] + new_js + content[match.end():]
    print("Replaced initDifferenceParallax in home.js")
else:
    print("Could not find initDifferenceParallax block in home.js")

# 2. Make sure it is called inside initAnimations
if 'initDifferenceParallax();' not in content:
    content = content.replace('initDistinctionAnimation();', 'initDistinctionAnimation();\n    initDifferenceParallax();')
    print("Added initDifferenceParallax() call to initAnimations")

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)

