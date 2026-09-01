'use strict';

// 1. Carousel Scroll Helper
function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    const scrollAmount = (carousel.clientWidth * 0.75) * direction;
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

// 2. Parallax and GSAP Initialization
function initHeroParallax() {
    const firstVideo = document.querySelector('.hero-bg-video');
    if (firstVideo) firstVideo.play().catch(() => {});

    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const mountainBg = document.getElementById('mountainBg');
    const featured = document.getElementById('featured');
    if (!mountainBg || !featured) return;

    gsap.to(mountainBg, {
        y: -60,
        ease: "none",
        scrollTrigger: { trigger: featured, start: "top bottom", end: "bottom top", scrub: 1 }
    });
}

function initGlobeAnimation() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const globalEscapes = document.getElementById('global-escapes');
    if (!globalEscapes) return;

    const geGlobe = document.getElementById('ge-globe');
    const globeSvg = geGlobe?.querySelector('svg');
    if (globeSvg) {
        gsap.to(globeSvg, {
            rotation: 90, ease: "none",
            scrollTrigger: { trigger: globalEscapes, start: "top 80%", end: "bottom top", scrub: 1 }
        });
    }

    const geStardust = document.getElementById('ge-stardust');
    if (geStardust) {
        gsap.to(geStardust, {
            y: 40, ease: "none",
            scrollTrigger: { trigger: globalEscapes, start: "top bottom", end: "bottom top", scrub: 2 }
        });
    }
}

function initAirplaneFlight() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const globalEscapes = document.getElementById('global-escapes');
    const geAirplane = document.getElementById('ge-airplane');
    if (!globalEscapes || !geAirplane) return;

    const plane = geAirplane.querySelector('.the-plane');
    if (!plane) return;

    gsap.fromTo(plane,
        { left: "0%", xPercent: -100 },
        { left: "100%", xPercent: 100, ease: "none",
          scrollTrigger: { trigger: globalEscapes, start: "top 80%", end: "bottom top", scrub: 1 } }
    );
}

function initCardSliders() {
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
}

function initCompanyDeck() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
        const companyDeckSection = document.querySelector('.companies-deck-section');
        const companyCards = gsap.utils.toArray('.company-card').reverse();

        if (companyDeckSection && companyCards.length > 0) {
            gsap.set(companyCards, {
                transformOrigin: "bottom center",
                z: (i) => i * -80,
                y: (i) => i * -80,
                scale: (i) => 1 - (i * 0.06),
                opacity: (i) => 1 - (i * 0.3)
            });

            const companyDeckTl = gsap.timeline({
                scrollTrigger: {
                    trigger: companyDeckSection,
                    start: "center center",
                    end: "+=" + (companyCards.length * window.innerHeight * 0.7),
                    pin: true,
                    scrub: 1
                }
            });

            companyCards.forEach((card, i) => {
                if (i < companyCards.length - 1) {
                    companyDeckTl.to(card, {
                        yPercent: -140,
                        opacity: 0,
                        duration: 1,
                        ease: "power2.inOut"
                    }, i);

                    for (let j = i + 1; j < companyCards.length; j++) {
                        companyDeckTl.to(companyCards[j], {
                            z: "+=80",
                            y: "+=80",
                            scale: "+=0.06",
                            opacity: "+=0.3",
                            duration: 1,
                            ease: "power2.inOut"
                        }, i);
                    }
                }
            });
        }
}

function initThemeCircleMarquees() {
    function setupDraggableMarquee(rowId, direction) {
        const row = document.getElementById(rowId);
        if (!row) return;

        let isDown = false;
        let startX = 0;
        let startScrollLeft = 0;
        let hasDragged = false;
        let isHovered = false;
        const speed = 0.85; // Auto-scroll speed

        // Prevent native drag on images/anchors
        row.querySelectorAll('img, a').forEach(el => {
            el.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // Initial scroll position setup
        const halfWidth = row.scrollWidth / 2;
        if (direction === 'left' && halfWidth > 0) {
            row.scrollLeft = halfWidth;
        }

        row.addEventListener('mouseenter', () => isHovered = true);
        row.addEventListener('mouseleave', () => {
            isHovered = false;
            isDown = false;
        });

        row.addEventListener('mousedown', (e) => {
            isDown = true;
            hasDragged = false;
            startX = e.pageX - row.offsetLeft;
            startScrollLeft = row.scrollLeft;
        });

        row.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - row.offsetLeft;
            const walk = (x - startX);
            if (Math.abs(walk) > 6) hasDragged = true;
            row.scrollLeft = startScrollLeft - walk;
        });

        row.addEventListener('mouseup', () => { isDown = false; });

        // Prevent unwanted clicks during drag
        row.querySelectorAll('.theme-circle-card-item').forEach(card => {
            card.addEventListener('click', (e) => {
                if (hasDragged) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        // Infinite loop animation loop
        function step() {
            const trackHalf = row.scrollWidth / 2;
            if (!isDown && !isHovered && trackHalf > 0) {
                if (direction === 'left') {
                    row.scrollLeft -= speed;
                } else {
                    row.scrollLeft += speed;
                }
            }

            if (trackHalf > 0) {
                if (row.scrollLeft >= trackHalf) {
                    row.scrollLeft -= trackHalf;
                    if (isDown) startScrollLeft -= trackHalf;
                } else if (row.scrollLeft <= 0) {
                    row.scrollLeft += trackHalf;
                    if (isDown) startScrollLeft += trackHalf;
                }
            }
            requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // Row 1 moves to the left, Row 2 moves to the right
    setupDraggableMarquee('circleMarqueeRow1', 'right');
    setupDraggableMarquee('circleMarqueeRow2', 'left');
}

function initDistinctionAnimation() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
        const distinctionItems = gsap.utils.toArray('.distinction-item');
        if (distinctionItems.length > 0) {
            gsap.set(distinctionItems, { y: 60, opacity: 0 });
            ScrollTrigger.batch(distinctionItems, {
                start: "top 85%",
                onEnter: batch => gsap.to(batch, {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.15,
                    ease: "power2.out",
                    overwrite: true
                }),
                onLeaveBack: batch => gsap.set(batch, { opacity: 0, y: 60, overwrite: true })
            });
        }
}


function initProcessAnimation() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
    const processSection = document.querySelector('.how-we-work-section');
    if (!processSection) return;

    const processHeader = processSection.querySelectorAll('.section-label-gold, .section-title, p:not(.process-text)');
    const processCards = processSection.querySelectorAll('.process-card');
    const processImages = processSection.querySelectorAll('.process-img-canvas');

    gsap.set(processHeader, { opacity: 0, y: 30 });
    gsap.set(processCards, { opacity: 0, y: 50, scale: 0.95 });
    if (processImages.length) gsap.set(processImages, { scale: 1.25, filter: 'brightness(0.5)' });

    ScrollTrigger.create({
        trigger: processSection,
        start: 'top 75%',
        onEnter: () => {
            const tl = gsap.timeline();

            tl.to(processHeader, {
                opacity: 1, y: 0, duration: 0.8, stagger: 0.2, ease: 'power3.out'
            });

            tl.to(processCards, {
                opacity: 1, y: 0, scale: 1,
                duration: 0.8, stagger: 0.2, ease: 'power3.out',
                clearProps: 'transform'
            }, '-=0.4');

            if (processImages.length) {
                tl.to(processImages, {
                    scale: 1, filter: 'brightness(1)',
                    duration: 1.6, stagger: 0.2, ease: 'power3.out',
                    clearProps: 'transform'
                }, '-=0.8');
            }
        }
    });
}


function initNewsletterParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const banner = document.querySelector('.inner-circle-banner');
    const bg     = document.getElementById('inner-circle-bg');
    if (!banner || !bg) return;

    // Start at normal scale, zoom in as user scrolls through
    gsap.fromTo(bg,
        { scale: 1, transformOrigin: '50% 50%' },
        {
            scale: 1.18,
            ease: 'none',
            scrollTrigger: {
                trigger: banner,
                start: 'top bottom',
                end:   'bottom top',
                scrub: 1.5
            }
        }
    );

    // Content fade-up on enter
    const newsletterContent = banner.querySelector('.newsletter-container');
    if (newsletterContent) {
        gsap.from(newsletterContent, {
            opacity: 0,
            y: 40,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: banner,
                start: 'top 75%',
                once: true
            }
        });
    }
}


function initCompassParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const compassSection = document.querySelector('.fixed-departures-section');
    if (!compassSection) return;

    const ring1          = document.getElementById('compass-ring-1');
    const ring2          = document.getElementById('compass-ring-2');
    const ring3          = document.getElementById('compass-ring-3');
    const compassWrapper = document.querySelector('.fd-parallax-compass');

    const compassTl = gsap.timeline({
        scrollTrigger: {
            trigger: compassSection,
            start: 'top bottom',
            end:   'bottom top',
            scrub: 1
        }
    });

    if (compassWrapper) compassTl.to(compassWrapper, { y: -250, ease: 'none' }, 0);
    if (ring1) compassTl.to(ring1, { rotation:  180, ease: 'none', transformOrigin: '50% 50%' }, 0);
    if (ring2) compassTl.to(ring2, { rotation: -240, ease: 'none', transformOrigin: '50% 50%' }, 0);
    if (ring3) compassTl.to(ring3, { rotation:  360, ease: 'none', transformOrigin: '50% 50%' }, 0);
}

function initDifferenceParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const section = document.querySelector('.leisure-difference-section');
    const bg = document.getElementById('leisure-difference-bg');
    if (!section || !bg) return;

    gsap.fromTo(bg,
        { scale: 1, yPercent: -6, transformOrigin: '50% 50%' },
        {
            scale: 1.18,
            yPercent: 6,
            ease: 'none',
            scrollTrigger: {
                trigger: section,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.2
            }
        }
    );
}
function initAnimations() {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }
    
    initHeroParallax();
    initGlobeAnimation();
    initAirplaneFlight();
    initCardSliders();
    initDistinctionAnimation();
    initProcessAnimation();
    initNewsletterParallax();
    initCompassParallax();
    initDifferenceParallax();
    initCompanyDeck();
    initThemeCircleMarquees();
    
    const themeMandala = document.getElementById('themeMandala');
    if (themeMandala && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.set(themeMandala, { xPercent: -50, yPercent: -50, x: 0, y: 0 });
        gsap.to(themeMandala, {
            rotation: 90, ease: "none",
            scrollTrigger: { trigger: ".themes-curation-section", start: "top bottom", end: "bottom top", scrub: 1 }
        });
    }

    if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.refresh();
    }
}

// 3. Unified Global Event Delegation
function bindGlobalEvents() {
    document.addEventListener('click', (e) => {
        // Carousel Navigation Arrows
        const carouselArrow = e.target.closest('[data-action="scroll-carousel"]');
        if (carouselArrow) {
            e.preventDefault();
            const carouselId = carouselArrow.getAttribute('data-carousel');
            const dir = parseInt(carouselArrow.getAttribute('data-dir') || '1', 10);
            scrollCarousel(carouselId, dir);
            return;
        }

        // Modal Open
        const openModalBtn = e.target.closest('[data-action="open-modal"]');
        if (openModalBtn) {
            e.preventDefault();
            const targetSelector = openModalBtn.getAttribute('data-target');
            const modal = document.querySelector(targetSelector);
            if (modal) {
                modal.classList.add('is-active');
            }
            return;
        }

        // Modal Close
        const closeModalBtn = e.target.closest('[data-action="close-modal"], .close-modal');
        if (closeModalBtn) {
            e.preventDefault();
            const modal = closeModalBtn.closest('.modal-overlay, .modal');
            if (modal) {
                modal.classList.remove('is-active');
            }
            return;
        }

        // FAQ Toggle
        const faqHeader = e.target.closest('.faq-question');
        if (faqHeader) {
            const faqItem = faqHeader.closest('.faq-item');
            if (faqItem) {
                const wasActive = faqItem.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(item => item.classList.remove('active'));
                if (!wasActive) faqItem.classList.add('active');
            }
            return;
        }

        // Action routing for data-href
        const hrefEl = e.target.closest('[data-href]');
        if (hrefEl) {
            window.location.href = hrefEl.getAttribute('data-href');
        }
    });
}

// 4. Lifecycle
document.addEventListener('DOMContentLoaded', () => {
    bindGlobalEvents();
    initAnimations();
    // Date input type toggle (text <-> date) for UX
    const dateInput = document.getElementById('heroDateInput');
    if (dateInput) {
        dateInput.addEventListener('focus', () => { dateInput.type = 'date'; });
        dateInput.addEventListener('blur', () => { if (!dateInput.value) dateInput.type = 'text'; });
    }
});

window.addEventListener('load', () => {
    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
});
