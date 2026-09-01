import os
import re

js_path = r'G:\Antigravity\leisure_loop_site\public\js\modules\packages.js'
with open(js_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the signature card selector
content = content.replace("cards = document.querySelectorAll('.sanctuaries-grid .sanctuary-card-link');", "cards = document.querySelectorAll('.signature-dest-card');")

# Add Title update logic
title_logic = """
            if (filterType === 'signature') {
                const titleEl = document.getElementById('sanctuariesMainTitle');
                const subEl = document.getElementById('sanctuariesSubtitle');
                if (titleEl && subEl) {
                    if (filterVal === 'domestic') {
                        titleEl.innerHTML = '<span class="material-symbols-outlined gold-icon-badge">auto_awesome</span> Domestic Signature Destinations';
                        subEl.innerText = 'Immerse yourself in breathtaking mountain retreats, misty valley tea gardens, and timeless cultural realms across India.';
                    } else {
                        titleEl.innerHTML = '<span class="material-symbols-outlined gold-icon-badge">public</span> International Signature Destinations';
                        subEl.innerText = 'Discover the world\\'s most breathtaking sanctuaries with all-inclusive luxury itineraries and exquisite escapes.';
                    }
                }
            }
            
            cards.forEach(card => {
"""
content = content.replace("cards.forEach(card => {", title_logic, 1)


# Add arrows logic
arrows_logic = """
    // Sanctuaries Navigation
    const sanctuariesTrackEl = document.getElementById('sanctuariesTrack');
    if (sanctuariesTrackEl && window.setupGSAPMomentumDrag) {
        window.setupGSAPMomentumDrag(sanctuariesTrackEl);
    }
    
    const sancBtnPrev = document.getElementById('sanctuariesBtnPrev');
    const sancBtnNext = document.getElementById('sanctuariesBtnNext');
    if (sanctuariesTrackEl && sancBtnPrev && sancBtnNext) {
        const cardGap = 20;
        sancBtnNext.addEventListener('click', () => {
            const cards = sanctuariesTrackEl.querySelectorAll('.signature-dest-card');
            if(cards.length > 0) {
                const step = cards[0].offsetWidth + cardGap;
                sanctuariesTrackEl.scrollBy({ left: step * 3, behavior: 'smooth' });
            }
        });
        sancBtnPrev.addEventListener('click', () => {
            const cards = sanctuariesTrackEl.querySelectorAll('.signature-dest-card');
            if(cards.length > 0) {
                const step = cards[0].offsetWidth + cardGap;
                sanctuariesTrackEl.scrollBy({ left: -step * 3, behavior: 'smooth' });
            }
        });
    }

    // 4. GSAP Momentum Drag
"""
content = content.replace("// 4. GSAP Momentum Drag", arrows_logic)

with open(js_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("packages.js patched for sanctuaries.")
