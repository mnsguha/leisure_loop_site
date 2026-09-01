'use strict';

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Landing Search Functionality
    const submitLandingSearch = function() {
        const keywordInput = document.getElementById('search_keyword_input');
        const themeSelect = document.getElementById('search_theme_select');
        const durSelect = document.getElementById('search_dur_select');
        
        let q = keywordInput ? keywordInput.value.trim() : '';
        let theme = themeSelect ? themeSelect.value : '';
        let dur = durSelect ? durSelect.value : '';
        
        let url = 'all-tours.php?';
        const params = [];
        if (q) params.push('q=' + encodeURIComponent(q));
        if (theme) params.push('theme=' + encodeURIComponent(theme));
        if (dur) params.push('dur=' + encodeURIComponent(dur));
        
        window.location.href = url + params.join('&');
    };

    const searchBtn = document.querySelector('[data-action="submit-landing-search"]');
    if (searchBtn) {
        searchBtn.addEventListener('click', submitLandingSearch);
    }
    
    const searchInput = document.getElementById('search_keyword_input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') submitLandingSearch();
        });
    }

    // 2. Parallax background effect
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset || document.documentElement.scrollTop;
        const heroImg = document.getElementById('catalog-hero-parallax-img');
        if (heroImg && scrolled < 800) {
            heroImg.style.transform = `scale(1.05) translateY(${scrolled * 0.35}px)`;
        }
    });

    // 3. Trending & Offers Filtering
    const filterButtons = document.querySelectorAll('[data-action="filter-pkg"]');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.getAttribute('data-type'); // 'signature', 'trending', 'offers'
            const filterVal = this.getAttribute('data-val'); // 'all', 'domestic', 'international'
            
            // Remove active from siblings
            const group = this.closest('.trending-pills-group');
            if (group) {
                group.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            }
            this.classList.add('active');

            // Find target cards
            let cards = [];
            if (filterType === 'trending') {
                cards = document.querySelectorAll('.trending-tour-card');
            } else if (filterType === 'offers') {
                cards = document.querySelectorAll('.offer-tour-card');
            } else if (filterType === 'signature') {
                cards = document.querySelectorAll('.signature-dest-card');
            }

            
            if (filterType === 'signature') {
                const titleEl = document.getElementById('sanctuariesMainTitle');
                const subEl = document.getElementById('sanctuariesSubtitle');
                if (titleEl && subEl) {
                    if (filterVal === 'domestic') {
                        titleEl.innerHTML = '<span class="material-symbols-outlined gold-icon-badge">auto_awesome</span> Domestic Signature Destinations';
                        subEl.innerText = 'Immerse yourself in breathtaking mountain retreats, misty valley tea gardens, and timeless cultural realms across India.';
                    } else {
                        titleEl.innerHTML = '<span class="material-symbols-outlined gold-icon-badge">public</span> International Signature Destinations';
                        subEl.innerText = 'Discover the world\'s most breathtaking sanctuaries with all-inclusive luxury itineraries and exquisite escapes.';
                    }
                }
            }
            
            cards.forEach(card => {

                const scope = card.getAttribute('data-scope');
                if (filterVal === 'all' || filterVal === scope) {
                    card.style.display = 'block';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transition = 'opacity 0.3s ease';
                    }, 10);
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    
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

    const trendingTrackEl = document.getElementById('trendingTrack');
    if (trendingTrackEl && window.setupGSAPMomentumDrag) {
        window.setupGSAPMomentumDrag(trendingTrackEl);
    }

    const offersTrackEl = document.getElementById('offersTrack');
    if (offersTrackEl && window.setupGSAPMomentumDrag) {
        window.setupGSAPMomentumDrag(offersTrackEl);
    }

    // 5. Auto-Scrolling Multi-Image Saved Destinations Hero Slider
    let currentHeroSlide = 0;
    const heroBgSlides = document.querySelectorAll('.hero-slide-bg');
    const heroTextSlides = document.querySelectorAll('.hero-slide-text');
    const heroDots = document.querySelectorAll('.hero-slide-dot');
    const totalHeroSlides = heroBgSlides.length;
    let heroSliderTimer = null;

    function renderHeroSlide(index) {
        if (totalHeroSlides <= 1) return;
        heroBgSlides.forEach((el, i) => {
            if (i === index) el.classList.add('active');
            else el.classList.remove('active');
        });
        heroTextSlides.forEach((el, i) => {
            if (i === index) el.classList.add('active');
            else el.classList.remove('active');
        });
        heroDots.forEach((el, i) => {
            if (i === index) el.classList.add('active');
            else el.classList.remove('active');
        });
        currentHeroSlide = index;
    }

    function changeHeroSlide(dir) {
        if (totalHeroSlides <= 1) return;
        let newIdx = (currentHeroSlide + dir + totalHeroSlides) % totalHeroSlides;
        renderHeroSlide(newIdx);
        resetHeroSliderTimer();
    }

    function goToHeroSlide(idx) {
        if (totalHeroSlides <= 1) return;
        renderHeroSlide(idx);
        resetHeroSliderTimer();
    }

    function startHeroSliderTimer() {
        if (totalHeroSlides <= 1) return;
        heroSliderTimer = setInterval(() => {
            let nextIdx = (currentHeroSlide + 1) % totalHeroSlides;
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
            dot.addEventListener('click', () => goToHeroSlide(idx));
        });
    }

    // 6. Theme Carousel Step
    const themeTrack = document.getElementById('themeCarouselTrack');
    const btnPrev = document.getElementById('themeBtnPrev');
    const btnNext = document.getElementById('themeBtnNext');

    if (themeTrack && btnPrev && btnNext) {
        const cardGap = 25;
        btnNext.addEventListener('click', () => {
            const cards = themeTrack.querySelectorAll('.theme-circle-card');
            if(cards.length > 0) {
                const step = cards[0].offsetWidth + cardGap;
                themeTrack.scrollBy({ left: step * 5, behavior: 'smooth' });
            }
        });
        btnPrev.addEventListener('click', () => {
            const cards = themeTrack.querySelectorAll('.theme-circle-card');
            if(cards.length > 0) {
                const step = cards[0].offsetWidth + cardGap;
                themeTrack.scrollBy({ left: -step * 5, behavior: 'smooth' });
            }
        });
    }
});
