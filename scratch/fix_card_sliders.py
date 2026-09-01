import re

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_init = '''function initCardSliders() {
    try {
        const destCarousel = document.querySelector('.destinations-carousel');
        if (!destCarousel) return;

        function applyStaggering() {
            const destCards = document.querySelectorAll('.destinations-carousel .dest-card');
            const visibleCards = Array.from(destCards).filter(c => !c.classList.contains('hidden'));
            visibleCards.forEach((card, index) => {
                card.classList.remove('stagger-up', 'stagger-down');
                if (index % 2 === 0) {
                    card.classList.add('stagger-up');
                } else {
                    card.classList.add('stagger-down');
                }
            });
        }
        
        applyStaggering();

        const nextBtn = document.querySelector('.next-dest');
        const prevBtn = document.querySelector('.prev-dest');
        const destCards = document.querySelectorAll('.dest-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        const scrollAmount = 372;
        if (nextBtn) nextBtn.addEventListener('click', () => { destCarousel.scrollBy({ left: scrollAmount, behavior: 'smooth' }); });
        if (prevBtn) prevBtn.addEventListener('click', () => { destCarousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); });

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                destCards.forEach(card => {
                    const cat = card.dataset.category || 'domestic';
                    if (filter === 'domestic' && cat === 'international') {
                        card.classList.add('hidden');
                    } else if (filter === 'international' && cat !== 'international') {
                        card.classList.add('hidden');
                    } else {
                        card.classList.remove('hidden');
                    }
                });
                
                applyStaggering();
                destCarousel.scrollTo({ left: 0, behavior: 'auto' });
            });
        });
    } catch (e) {
        console.warn("initCardSliders error:", e);
    }
}'''

content = re.sub(r'function initCardSliders\(\) \{.*?\n\}\n(?=\n?function|\Z)', new_init + '\n', content, flags=re.DOTALL)

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
