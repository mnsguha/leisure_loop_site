import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add transition to extended-story if not already there
content = re.sub(
    r'<div id="extended-story"[^>]*style="height:\s*0[^>]*>',
    '<div id="extended-story" class="overflow-hidden" style="height: 0px; opacity: 0; transition: all 1.2s cubic-bezier(0.215, 0.61, 0.355, 1);">',
    content
)

# 2. Add onclick to explore-btn
content = re.sub(
    r'<a\s+href="[^"]*"\s+id="explore-btn"',
    '<a href="javascript:void(0)" onclick="toggleInlineStory(event)" id="explore-btn"',
    content
)

# 3. Add onclick to explore-modal-btn
content = re.sub(
    r'<a\s+href="[^"]*"\s+id="explore-modal-btn"',
    '<a href="javascript:void(0)" onclick="openStoryModal(event)" id="explore-modal-btn"',
    content
)

# 4. Inject the script before the footer if not there
script_code = """
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

if 'function toggleInlineStory' not in content:
    footer_idx = content.find('<?php include "../includes/footer.php"; ?>')
    if footer_idx != -1:
        content = content[:footer_idx] + script_code + '\n' + content[footer_idx:]

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Injected script and onclick attributes successfully.")
