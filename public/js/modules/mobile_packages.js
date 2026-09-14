'use strict';

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Hero Auto-Scroll Logic ---
    const heroTrack = document.getElementById('mobHeroTrack');
    const heroSlides = document.querySelectorAll('.m-pkg-hero__slide');
    const heroDots = document.querySelectorAll('.m-hero-dot');
    
    if (heroTrack && heroSlides.length > 1) {
        let currentSlide = 0;
        const totalSlides = heroSlides.length;
        
        setInterval(() => {
            heroSlides[currentSlide].classList.remove('active');
            if (heroDots.length > currentSlide) {
                heroDots[currentSlide].classList.remove('active');
            }
            
            currentSlide = (currentSlide + 1) % totalSlides;
            
            heroSlides[currentSlide].classList.add('active');
            if (heroDots.length > currentSlide) {
                heroDots[currentSlide].classList.add('active');
            }
        }, 4000);
    }
    
    // --- 1b. Hero Arrow Navigation ---
    document.addEventListener('click', (e) => {
        const heroBtn = e.target.closest('[data-action="hero-slide"]');
        if (!heroBtn || !heroSlides.length) return;
        
        const dir = parseInt(heroBtn.getAttribute('data-dir'), 10);
        let activeIdx = 0;
        heroSlides.forEach((s, i) => { if (s.classList.contains('active')) activeIdx = i; });
        
        heroSlides[activeIdx].classList.remove('active');
        if (heroDots.length > activeIdx) heroDots[activeIdx].classList.remove('active');
        
        activeIdx = (activeIdx + dir + heroSlides.length) % heroSlides.length;
        
        heroSlides[activeIdx].classList.add('active');
        if (heroDots.length > activeIdx) heroDots[activeIdx].classList.add('active');
    });

    // --- 1c. Hero Dot Navigation ---
    document.addEventListener('click', (e) => {
        const dot = e.target.closest('[data-dot]');
        if (!dot || !heroSlides.length) return;
        
        const idx = parseInt(dot.getAttribute('data-dot'), 10);
        heroSlides.forEach(s => s.classList.remove('active'));
        heroDots.forEach(d => d.classList.remove('active'));
        
        heroSlides[idx].classList.add('active');
        heroDots[idx].classList.add('active');
    });
    
    // --- 2. Search Dock Clear Logic ---
    const searchInput = document.getElementById('mobSearchInput');
    const searchClear = document.getElementById('mobSearchClear');
    
    if (searchInput && searchClear) {
        searchInput.addEventListener('input', () => {
            searchClear.classList.toggle('is-hidden', searchInput.value.trim().length === 0);
        });
        
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            searchClear.classList.add('is-hidden');
            searchInput.focus();
        });
    }
    
    // --- 3. Generic Tab Switching (Signature Destinations, Trending, Offers) ---
    function initTabGroup(tabSelector, trackSelector) {
        const tabs = document.querySelectorAll(tabSelector);
        const tracks = document.querySelectorAll(trackSelector);
        
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const targetId = e.currentTarget.getAttribute('data-target');
                if (!targetId) return;
                
                // Update all tabs in this group
                tabs.forEach(t => {
                    t.classList.remove('m-pill--active');
                    t.classList.add('m-pill--inactive');
                });
                e.currentTarget.classList.add('m-pill--active');
                e.currentTarget.classList.remove('m-pill--inactive');
                
                // Update all tracks in this group
                tracks.forEach(track => {
                    if (track.id === targetId) {
                        track.classList.remove('is-hidden');
                    } else {
                        track.classList.add('is-hidden');
                    }
                });

                // Update arrow targets to point to newly visible track
                const header = e.currentTarget.closest('.m-pkg-carousel-header');
                if (header) {
                    header.querySelectorAll('.m-pkg-nav-btn').forEach(btn => {
                        btn.setAttribute('data-target', targetId);
                    });
                }
            });
        });
    }

    initTabGroup('.js-sig-tab', '.js-sig-track');
    initTabGroup('.js-trend-tab', '.js-trend-track');
    initTabGroup('.js-offer-tab', '.js-offer-track');
    
    // --- 4. Carousel Scroll Navigation (Arrow Buttons) ---
    document.addEventListener('click', (e) => {
        const scrollBtn = e.target.closest('[data-action="carousel-scroll"]');
        if (!scrollBtn) return;
        
        const targetId = scrollBtn.getAttribute('data-target');
        const direction = parseInt(scrollBtn.getAttribute('data-direction'), 10);
        const track = document.getElementById(targetId);
        
        if (!track) return;
        
        const scrollAmount = track.clientWidth * 0.7;
        track.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    });

    // --- 5. Drag-to-Scroll for All Carousel Tracks ---
    document.querySelectorAll('.m-pkg-carousel-track').forEach(track => {
        let isDown = false;
        let startX;
        let scrollLeft;

        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.classList.add('active-drag');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('active-drag');
        });

        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('active-drag');
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        }, { passive: true });

        track.addEventListener('touchmove', (e) => {
            const x = e.touches[0].pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        }, { passive: true });
    });

});
