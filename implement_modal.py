import os
import re

dest_path = 'public/destinations.php'

with open(dest_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update Buttons
old_btn_block = '''<div id="explore-btn" class="mt-10 flex items-center gap-4 group cursor-pointer reveal-hidden stagger-4">
<div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
<span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY</span>
</div>'''

new_btn_block = '''<div class="mt-10 flex flex-wrap gap-12 reveal-hidden stagger-4">
    <!-- Option 3 (Inline) -->
    <div id="explore-btn" class="flex items-center gap-4 group cursor-pointer">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE INLINE</span>
    </div>

    <!-- Option 2 (Modal) -->
    <div id="explore-modal-btn" class="flex items-center gap-4 group cursor-pointer">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE MODAL</span>
    </div>
</div>'''

content = content.replace(old_btn_block, new_btn_block)

# 2. Add Modal HTML
modal_html = '''
<!-- Full-Screen Cinematic Modal (Option 2) -->
<div id="story-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8 pointer-events-none" style="opacity: 0;">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-background/90 backdrop-blur-xl transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-6xl h-full max-h-[90vh] bg-surface-container-low rounded-3xl overflow-hidden shadow-2xl flex flex-col pointer-events-auto transform scale-95 origin-center" id="story-modal-content">
        <!-- Header / Close Button -->
        <div class="absolute top-0 left-0 w-full p-6 flex justify-between items-center z-10 bg-gradient-to-b from-black/50 to-transparent">
            <span class="font-label-caps text-secondary tracking-widest text-sm">THE NARRATIVE</span>
            <button id="close-modal-btn" class="text-white hover:text-secondary transition-colors p-2 bg-black/30 rounded-full backdrop-blur-md">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Scrollable Body -->
        <div class="overflow-y-auto w-full h-full custom-scrollbar">
            <!-- Hero Image for Modal -->
            <div class="w-full h-64 md:h-96 relative">
                <img src="images/stitch/stitch_img_2.jpg" alt="Sikkim Culture" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low to-transparent"></div>
            </div>
            
            <!-- Modal Editorial Content -->
            <div class="p-8 md:p-16 max-w-4xl mx-auto -mt-32 relative z-10">
                <h2 class="font-headline-lg text-4xl md:text-6xl text-white italic mb-12 drop-shadow-lg">Whispers of the Himalayas</h2>
                
                <div class="space-y-8 text-on-surface-variant text-lg leading-relaxed font-body-md">
                    <p class="text-xl text-white/90 font-medium">
                        Sikkim is a sanctuary where time moves at the pace of spinning prayer wheels and drifting clouds.
                    </p>
                    <p>
                        Beyond the mist-drenched valleys, Sikkim offers an unprecedented communion with nature. Our elite itineraries ensure that your encounter with the Himalayas is untouched by the ordinary. Whether it is a private helicopter charter over the Yumthang Valley or a guided spiritual retreat in absolute seclusion, your journey is meticulously crafted.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 my-12">
                        <img src="images/stitch/stitch_img_4.jpg" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">
                        <img src="images/stitch/stitch_img_5.jpg" class="w-full h-48 md:h-64 object-cover rounded-2xl shadow-lg">
                    </div>

                    <p>
                        Every element of your stay—from the thread count of your linens to the vintage of your evening wine—is selected to harmonize with the raw, untamed beauty outside your window. Here, luxury is defined not just by opulence, but by exclusive access to authentic, transformative experiences.
                    </p>
                </div>
                
                <div class="mt-16 text-center border-t border-white/10 pt-12">
                    <button id="close-modal-bottom" class="font-label-caps text-secondary tracking-widest text-sm hover:text-white transition-colors">RETURN TO DESTINATION</button>
                </div>
            </div>
        </div>
    </div>
</div>
'''

# Inject Modal HTML before footer include
footer_inc = "<?php include '../includes/footer.php'; ?>"
content = content.replace(footer_inc, modal_html + '\n' + footer_inc)


# 3. Add Modal JS Logic
modal_js = '''
        // EXPLORE THE STORY Modal (Option 2)
        const exploreModalBtn = document.getElementById('explore-modal-btn');
        const storyModal = document.getElementById('story-modal');
        const storyModalContent = document.getElementById('story-modal-content');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const closeModalBottom = document.getElementById('close-modal-bottom');

        function openModal() {
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            gsap.to(storyModal, { opacity: 1, duration: 0.5, pointerEvents: 'auto' });
            gsap.to(storyModalContent, { scale: 1, duration: 0.7, ease: "power3.out", delay: 0.1 });
        }

        function closeModal() {
            document.body.style.overflow = ''; // Restore scrolling
            gsap.to(storyModalContent, { scale: 0.95, duration: 0.4, ease: "power2.in" });
            gsap.to(storyModal, { opacity: 0, duration: 0.5, pointerEvents: 'none', delay: 0.2 });
        }

        if (exploreModalBtn && storyModal) {
            exploreModalBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            closeModalBottom.addEventListener('click', closeModal);
            
            // Close on backdrop click
            storyModal.addEventListener('click', (e) => {
                if (e.target === storyModal) {
                    closeModal();
                }
            });
        }
'''

# Inject Modal JS before the closing script tag
idx_script_end = content.rfind('</script>')
if idx_script_end != -1:
    content = content[:idx_script_end] + modal_js + '\n' + content[idx_script_end:]

with open(dest_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Successfully implemented Explore Modal interaction.")
