document.addEventListener('DOMContentLoaded', () => {
    // 1. Swiper Hero Coverflow
    if (typeof Swiper !== 'undefined') {
        new Swiper('.hero-coverflow', {
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            coverflowEffect: {
                rotate: 15,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    // 2. Saved City Hydration
    const locationText = document.getElementById('currentLocationText');
    let savedCity = null;
    try {
        savedCity = localStorage.getItem('user_city');
    } catch (e) {
        console.warn('Storage access blocked by browser:', e);
    }
    if (savedCity && locationText) {
        locationText.textContent = savedCity;
    }

    // 3. City Filter Input
    const cityInput = document.getElementById('citySearchInput');
    const cityItems = document.querySelectorAll('.city-picker-item');
    if (cityInput) {
        cityInput.addEventListener('input', () => {
            const query = cityInput.value.toLowerCase().trim();
            cityItems.forEach((item) => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        });
    }

    // 4. Promo Marquee Dot Sync
    const promoCarousel = document.getElementById('promoCarousel');
    const promoDots = document.querySelectorAll('.promo-dot');
    if (promoCarousel && promoDots.length > 0) {
        promoCarousel.addEventListener('scroll', () => {
            const scrollLeft = promoCarousel.scrollLeft;
            const width = promoCarousel.offsetWidth;
            const activeIndex = Math.round(scrollLeft / width);
            promoDots.forEach((dot, idx) => {
                dot.classList.toggle('active', idx === activeIndex);
            });
        });
    }
});
