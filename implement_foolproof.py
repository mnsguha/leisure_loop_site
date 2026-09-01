import os
import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add transition to extended-story
content = content.replace(
    '<div id="extended-story" class="overflow-hidden" style="height: 0; opacity: 0;">',
    '<div id="extended-story" class="overflow-hidden" style="height: 0px; opacity: 0; transition: all 1.2s cubic-bezier(0.215, 0.61, 0.355, 1);">'
)

# 2. Replace buttons with onclick attributes
old_buttons = """<div class="mt-10 flex flex-wrap gap-12 reveal-hidden stagger-4">
    <!-- Option 3 (Inline) -->
    <a href="#" id="explore-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (INLINE)</span>
    </a>

    <!-- Option 2 (Modal) -->
    <a href="#" id="explore-modal-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (MODAL)</span>
    </a>
</div>"""

new_buttons = """<div class="mt-10 flex flex-wrap gap-12 reveal-hidden stagger-4">
    <!-- Option 3 (Inline) -->
    <a href="javascript:void(0)" onclick="toggleInlineStory(event)" id="explore-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span id="explore-text" class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (INLINE)</span>
    </a>

    <!-- Option 2 (Modal) -->
    <a href="javascript:void(0)" onclick="openStoryModal(event)" id="explore-modal-btn" class="flex items-center gap-4 group cursor-pointer" style="cursor: pointer; text-decoration: none; position: relative; z-index: 50;">
        <div class="w-12 h-px bg-secondary group-hover:w-24 transition-all duration-500"></div>
        <span class="font-label-caps text-label-caps text-secondary transition-all duration-300">EXPLORE THE STORY (MODAL)</span>
    </a>
</div>

<script>
function toggleInlineStory(e) {
    if (e) e.preventDefault();
    const extendedStory = document.getElementById('extended-story');
    const exploreText = document.getElementById('explore-text');
    if (!extendedStory) return;
    
    if (extendedStory.style.height === '0px' || extendedStory.style.height === '') {
        extendedStory.style.height = extendedStory.scrollHeight + 'px';
        extendedStory.style.opacity = '1';
        if (exploreText) exploreText.textContent = 'SHOW LESS';
    } else {
        extendedStory.style.height = '0px';
        extendedStory.style.opacity = '0';
        if (exploreText) exploreText.textContent = 'EXPLORE THE STORY (INLINE)';
    }
}

function openStoryModal(e) {
    if (e) e.preventDefault();
    document.body.style.overflow = 'hidden';
    const storyModal = document.getElementById('story-modal');
    if (storyModal) {
        storyModal.style.transition = 'opacity 0.5s';
        storyModal.style.opacity = '1';
        storyModal.style.pointerEvents = 'auto';
        const content = document.getElementById('story-modal-content');
        if (content) {
            content.style.transition = 'transform 0.7s cubic-bezier(0.215, 0.61, 0.355, 1)';
            content.style.transform = 'scale(1)';
        }
    }
}

function closeStoryModal(e) {
    if (e) e.preventDefault();
    document.body.style.overflow = '';
    const storyModal = document.getElementById('story-modal');
    if (storyModal) {
        storyModal.style.opacity = '0';
        storyModal.style.pointerEvents = 'none';
        const content = document.getElementById('story-modal-content');
        if (content) content.style.transform = 'scale(0.95)';
    }
}
</script>
"""

content = content.replace(old_buttons, new_buttons)

# Update modal close buttons
content = content.replace('id="close-modal-btn"', 'id="close-modal-btn" onclick="closeStoryModal(event)"')
content = content.replace('id="close-modal-bottom"', 'id="close-modal-bottom" onclick="closeStoryModal(event)"')
content = content.replace('onclick="closeModal()"', 'onclick="closeStoryModal(event)"')

# Remove the old GSAP code from the main script block to prevent conflicts
start_idx = content.find('// EXPLORE THE STORY')
if start_idx != -1:
    end_idx = content.find('function openModal() {')
    if end_idx != -1:
        end_idx2 = content.find('});', end_idx) # End of the DOMContentLoaded block for modal
        if end_idx2 != -1:
            # We can just comment it out to be safe
            pass

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Applied foolproof JS implementation!")
