'use strict';

// data-bg Hydration (Rule 1: no inline style="background-image")
document.querySelectorAll('[data-bg]').forEach(function(el) {
    el.style.backgroundImage = 'url(' + el.dataset.bg + ')';
});

document.addEventListener('DOMContentLoaded', () => {
    // Landing Search Execution
    const submitLandingSearch = () => {
        const keywordInput = document.getElementById('desktopSearchKeyword');
        const themeSelect = document.getElementById('desktopSearchTheme');
        const durSelect = document.getElementById('desktopSearchDuration');

        const q = keywordInput ? keywordInput.value.trim() : '';
        const theme = themeSelect ? themeSelect.value : '';
        const dur = durSelect ? durSelect.value : '';

        const params = [];
        if (q) params.push('q=' + encodeURIComponent(q));
        if (theme) params.push('theme=' + encodeURIComponent(theme));
        if (dur) params.push('dur=' + encodeURIComponent(dur));

        window.location.href = 'all-tours.php' + (params.length ? '?' + params.join('&') : '');
    };

    const searchBtn = document.querySelector('[data-action="submit-landing-search"]');
    if (searchBtn) searchBtn.addEventListener('click', submitLandingSearch);

    const searchInput = document.getElementById('desktopSearchKeyword');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') submitLandingSearch();
        });
    }

    // Pointer Drag-to-Scroll Helper
    function enableDragToScroll(track) {
        if (!track) return;
        let isDown = false, startX = 0, scrollLeft = 0, isDragging = false;

        track.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            track.classList.add('active-drag');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('mouseleave', () => { if (!isDown) return; isDown = false; track.classList.remove('active-drag'); });
        track.addEventListener('mouseup', () => { if (!isDown) return; isDown = false; track.classList.remove('active-drag'); });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            if (Math.abs(walk) > 5) isDragging = true;
            track.scrollLeft = scrollLeft - walk;
        });

        track.addEventListener('click', (e) => {
            if (isDragging) { e.preventDefault(); e.stopPropagation(); isDragging = false; }
        }, true);
    }

    ['themeCarouselTrack', 'sanctuariesTrack', 'trendingTrack', 'offersTrack'].forEach(id => {
        const el = document.getElementById(id);
        if (el) enableDragToScroll(el);
    });

    // Unified Delegated Carousel Scroll Handler (Snap-Safe)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="carousel-scroll"]');
        if (!btn) return;

        const targetId = btn.getAttribute('data-target');
        const direction = parseInt(btn.getAttribute('data-direction'), 10);
        if (!targetId || !direction) return;

        const track = document.getElementById(targetId);
        if (!track) return;

        track.classList.add('is-scrolling');
        const visibleCard = Array.from(track.children).find(c => !c.classList.contains('is-hidden')) || track.firstElementChild;
        const cardWidth = visibleCard ? visibleCard.offsetWidth : 280;
        const computedGap = parseFloat(window.getComputedStyle(track).gap) || 20;
        const multiplier = (targetId === 'trendingTrack' || targetId === 'offersTrack') ? 2 : 3;

        track.scrollBy({ left: direction * (cardWidth + computedGap) * multiplier, behavior: 'smooth' });

        setTimeout(() => { track.classList.remove('is-scrolling'); }, 500);
    });

    // Scoped Section Filtering Handler
    const filterButtons = document.querySelectorAll('[data-action="filter-pkg"]');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.getAttribute('data-type');
            const filterVal = this.getAttribute('data-val');

            const group = this.closest('.trending-pills-group');
            if (group) group.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            let cards = [];
            if (filterType === 'trending') {
                cards = document.querySelectorAll('#trendingTrack .trending-tour-card');
                const tTrack = document.getElementById('trendingTrack');
                if (tTrack) tTrack.scrollTo({ left: 0, behavior: 'smooth' });
            } else if (filterType === 'offers') {
                cards = document.querySelectorAll('#offersTrack .offer-tour-card');
                const oTrack = document.getElementById('offersTrack');
                if (oTrack) oTrack.scrollTo({ left: 0, behavior: 'smooth' });
            } else if (filterType === 'signature') {
                cards = document.querySelectorAll('#sanctuariesTrack .signature-dest-card');
                const sTrack = document.getElementById('sanctuariesTrack');
                if (sTrack) sTrack.scrollTo({ left: 0, behavior: 'smooth' });

                const titleEl = document.getElementById('sanctuariesMainTitle');
                const subEl = document.getElementById('sanctuariesSubtitle');
                if (titleEl && subEl) {
                    if (filterVal === 'domestic') {
                        titleEl.textContent = '';
                        const icon1 = document.createElement('span');
                        icon1.className = 'material-symbols-outlined gold-icon-badge';
                        icon1.textContent = 'auto_awesome';
                        titleEl.appendChild(icon1);
                        titleEl.appendChild(document.createTextNode(' Domestic Signature Destinations'));
                        subEl.innerText = 'Immerse yourself in breathtaking mountain retreats, misty valley tea gardens, and timeless cultural realms across India.';
                    } else {
                        titleEl.textContent = '';
                        const icon2 = document.createElement('span');
                        icon2.className = 'material-symbols-outlined gold-icon-badge';
                        icon2.textContent = 'public';
                        titleEl.appendChild(icon2);
                        titleEl.appendChild(document.createTextNode(' International Signature Destinations'));
                        subEl.innerText = 'Discover the world\'s most breathtaking sanctuaries with all-inclusive luxury itineraries and exquisite escapes.';
                    }
                }
            }

            cards.forEach(card => {
                const scope = card.getAttribute('data-scope');
                if (filterVal === 'all' || filterVal === scope) {
                    card.classList.remove('is-hidden');
                } else {
                    card.classList.add('is-hidden');
                }
            });
        });
    });

    // Auto-Scrolling Hero Slider
    let currentHeroSlide = 0;
    const heroBgSlides = document.querySelectorAll('.hero-slide-bg');
    const heroTextSlides = document.querySelectorAll('.hero-slide-text');
    const heroDots = document.querySelectorAll('.hero-slide-dot');
    const totalHeroSlides = heroBgSlides.length;
    let heroSliderTimer = null;

    function renderHeroSlide(index) {
        if (totalHeroSlides <= 1) return;
        heroBgSlides.forEach((el, i) => el.classList.toggle('active', i === index));
        heroTextSlides.forEach((el, i) => el.classList.toggle('active', i === index));
        heroDots.forEach((el, i) => el.classList.toggle('active', i === index));
        currentHeroSlide = index;
    }

    function changeHeroSlide(dir) {
        if (totalHeroSlides <= 1) return;
        const newIdx = (currentHeroSlide + dir + totalHeroSlides) % totalHeroSlides;
        renderHeroSlide(newIdx);
        resetHeroSliderTimer();
    }

    function startHeroSliderTimer() {
        if (totalHeroSlides <= 1) return;
        heroSliderTimer = setInterval(() => {
            const nextIdx = (currentHeroSlide + 1) % totalHeroSlides;
            renderHeroSlide(nextIdx);
        }, 4500);
    }

    function resetHeroSliderTimer() {
        clearInterval(heroSliderTimer);
        startHeroSliderTimer();
    }

    if (totalHeroSlides > 1) {
        startHeroSliderTimer();
        const heroSectionEl = document.getElementById('heroCarouselSection');
        if (heroSectionEl) {
            heroSectionEl.addEventListener('mouseenter', () => clearInterval(heroSliderTimer));
            heroSectionEl.addEventListener('mouseleave', () => startHeroSliderTimer());
        }

        const arrowLeft = document.querySelector('.hero-arrow-left');
        const arrowRight = document.querySelector('.hero-arrow-right');
        if (arrowLeft) arrowLeft.addEventListener('click', () => changeHeroSlide(-1));
        if (arrowRight) arrowRight.addEventListener('click', () => changeHeroSlide(1));

        heroDots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                renderHeroSlide(idx);
                resetHeroSliderTimer();
            });
        });
    }
});
