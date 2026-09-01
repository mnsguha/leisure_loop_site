import os
import re

dest_path = 'public/destinations.php'

with open(dest_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Modify the EXPLORE THE STORY button to have an ID
explore_btn_html = '''<div id="explore-btn" class="mt-10 flex items-center gap-4 group cursor-pointer reveal-hidden stagger-4">
<div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
<span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY</span>
</div>'''

# Replace the existing button
old_btn_start = '<div class="mt-10 flex items-center gap-4 group cursor-pointer reveal-hidden stagger-4">'
old_btn_end = '</div>'
idx_start = content.find(old_btn_start)
if idx_start != -1:
    idx_span = content.find('<span class="font-label-caps text-label-caps text-secondary">EXPLORE THE STORY</span>', idx_start)
    if idx_span != -1:
        idx_end = content.find('</div>', idx_span) + len('</div>')
        
        # 2. Prepare the extended story HTML
        extended_story_html = '''
<div id="extended-story" class="overflow-hidden" style="height: 0; opacity: 0;">
    <div class="pt-8 space-y-6 text-on-surface-variant leading-relaxed text-lg">
        <p>
            Beyond the mist-drenched valleys, Sikkim offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.
        </p>
        <div class="w-full h-64 md:h-96 rounded-2xl overflow-hidden mt-8 mb-8 relative shadow-2xl border border-secondary/20">
            <img src="images/parallax/sunset_mountains.png" alt="Sikkim Landscape" class="w-full h-full object-cover object-center filter brightness-75 hover:brightness-100 transition-all duration-700 hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent pointer-events-none"></div>
            <div class="absolute bottom-6 left-6 text-white font-label-caps tracking-widest text-xs opacity-80">UNTOUCHED WILDERNESS</div>
        </div>
        <p>
            Every element of your stay—from the thread count of your linens to the vintage of your evening wine—is selected to harmonize with the raw, untamed beauty outside your window.
        </p>
    </div>
</div>
'''
        
        # Replace and inject the new HTML
        new_html = extended_story_html + explore_btn_html
        content = content[:idx_start] + new_html + content[idx_end:]

# 3. Add Javascript for the interaction
js_logic = '''
        // EXPLORE THE STORY Inline Expansion
        const exploreBtn = document.getElementById('explore-btn');
        const exploreText = document.getElementById('explore-text');
        const extendedStory = document.getElementById('extended-story');
        let isStoryExpanded = false;

        if (exploreBtn && extendedStory) {
            exploreBtn.addEventListener('click', () => {
                isStoryExpanded = !isStoryExpanded;
                if (isStoryExpanded) {
                    gsap.to(extendedStory, {
                        height: "auto",
                        opacity: 1,
                        duration: 1.2,
                        ease: "power3.inOut"
                    });
                    exploreText.style.opacity = 0;
                    setTimeout(() => {
                        exploreText.textContent = "SHOW LESS";
                        exploreText.style.opacity = 1;
                    }, 300);
                } else {
                    gsap.to(extendedStory, {
                        height: 0,
                        opacity: 0,
                        duration: 1.2,
                        ease: "power3.inOut"
                    });
                    exploreText.style.opacity = 0;
                    setTimeout(() => {
                        exploreText.textContent = "EXPLORE THE STORY";
                        exploreText.style.opacity = 1;
                    }, 300);
                    
                    // Scroll slightly back up to keep context
                    setTimeout(() => {
                        const navSec = document.getElementById('narrative');
                        if(navSec) {
                            window.scrollTo({
                                top: navSec.offsetTop - 50,
                                behavior: 'smooth'
                            });
                        }
                    }, 500);
                }
            });
        }
'''

# Inject JS before the closing script tag
idx_script_end = content.rfind('</script>')
if idx_script_end != -1:
    content = content[:idx_script_end] + js_logic + '\n' + content[idx_script_end:]

with open(dest_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Successfully implemented Explore The Story interaction.")
