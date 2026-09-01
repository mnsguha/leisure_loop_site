function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
        // Ensure first video plays
        const firstVideo = document.querySelector('.hero-slide.active video');
        if (firstVideo) {
            firstVideo.play().catch(e => console.log("Autoplay blocked", e));
        }

        // Minimalist Vector Mountain Parallax
        gsap.registerPlugin(ScrollTrigger);
        
        // Real Smiles Gallery Text Animation
        const smilesTextElements = document.querySelectorAll(".smiles-text > *");
        if (smilesTextElements.length > 0) {
            gsap.from(smilesTextElements, {
                scrollTrigger: {
                    trigger: ".real-smiles-section",
                    start: "top 75%",
                },
                y: 40,
                opacity: 0,
                duration: 0.8,
                stagger: 0.15,
                ease: "power3.out"
            });
        }
        if (document.getElementById('mountainBg') && document.getElementById('featured')) {
            // As the user scrolls into Most Coveted, the mountains rise up slightly from the bottom
            gsap.to("#mountainBg", {
                y: -80, // Move up by 80px
                ease: "none",
                scrollTrigger: {
                    trigger: "#featured",
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }

        // Global Escapes Parallax (Globe & Airplane)
        if (document.getElementById('global-escapes')) {
            const geTl = gsap.timeline({
                scrollTrigger: {
                    trigger: "#global-escapes",
                    start: "top 80%", // Wait until section is clearly visible
                    end: "bottom top",
                    scrub: 1
                }
            });

            if (document.getElementById('ge-globe')) {
                geTl.to("#ge-globe svg", { rotation: 90, ease: "none" }, 0);
            }
            if (document.getElementById('ge-airplane')) {
                const plane = document.querySelector("#ge-airplane .the-plane");
                // Mathematically force the start position to the absolute left edge of the screen
                geTl.fromTo(plane, 
                    { left: "0%", xPercent: -100 },
                    { left: "100%", xPercent: 100, ease: "none" }, 
                    0
                );
            }
            
            if (document.getElementById('ge-stardust')) {
                gsap.to("#ge-stardust", {
                    y: 40, // slight inverse movement
                    ease: "none",
                    scrollTrigger: {
                        trigger: "#global-escapes",
                        start: "top bottom",
                        end: "bottom top",
                        scrub: 3
                    }
                });
            }
        }

        // Premium Entrance for The Curated Collection
        const featuredSection = document.getElementById('featured');
        if (featuredSection) {
            const featuredWatermark = featuredSection.querySelector('.watermark');
            const featuredGoldLabel = featuredSection.querySelector('.section-label-gold');
            const featuredSectionTitle = featuredSection.querySelector('.section-title');
            const packageCards = gsap.utils.toArray(featuredSection.querySelectorAll('.package-card'));

            // Initial State setup
            if (featuredWatermark) gsap.set(featuredWatermark, { opacity: 0, scale: 1.1 });
            if (featuredGoldLabel && featuredSectionTitle) gsap.set([featuredGoldLabel, featuredSectionTitle], { opacity: 0, y: 30 });
            if (packageCards.length > 0) gsap.set(packageCards, { opacity: 0, y: 50 });

            // Create ScrollTrigger Timeline
            ScrollTrigger.create({
                trigger: featuredSection,
                start: "top 75%",
                onEnter: () => {
                    const tl = gsap.timeline();
                    
                    if (featuredWatermark) {
                        tl.to(featuredWatermark, { opacity: 1, scale: 1, duration: 1.5, ease: "power2.out" });
                    }
                    if (featuredGoldLabel) {
                        tl.to(featuredGoldLabel, { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }, "-=1.0");
                    }
                    if (featuredSectionTitle) {
                        tl.to(featuredSectionTitle, { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }, "-=0.4");
                    }
                    if (packageCards.length > 0) {
                        tl.to(packageCards, { opacity: 1, y: 0, duration: 0.8, stagger: 0.15, ease: "back.out(1.2)" }, "-=0.4");
                    }
                },
                once: true
            });
        }

        // Premium Entrance for Global Escapes
        const globalEscapesSection = document.getElementById('global-escapes');
        if (globalEscapesSection) {
            const geWatermark = globalEscapesSection.querySelector('.watermark');
            const geGoldLabel = globalEscapesSection.querySelector('.section-label-gold');
            const geSectionTitle = globalEscapesSection.querySelector('.section-title');
            const gePackageCards = gsap.utils.toArray(globalEscapesSection.querySelectorAll('.package-card'));

            // Initial State setup
            if (geWatermark) gsap.set(geWatermark, { opacity: 0, scale: 1.1 });
            if (geGoldLabel && geSectionTitle) gsap.set([geGoldLabel, geSectionTitle], { opacity: 0, y: 30 });
            if (gePackageCards.length > 0) gsap.set(gePackageCards, { opacity: 0, y: 50 });

            // Create ScrollTrigger Timeline
            ScrollTrigger.create({
                trigger: globalEscapesSection,
                start: "top 90%",
                onEnter: () => {
                    const tl = gsap.timeline();
                    
                    if (geWatermark) {
                        tl.to(geWatermark, { opacity: 1, scale: 1, duration: 1.5, ease: "power2.out" });
                    }
                    if (geGoldLabel) {
                        tl.to(geGoldLabel, { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }, "<0.2");
                    }
                    if (geSectionTitle) {
                        tl.to(geSectionTitle, { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }, "<0.2");
                    }
                    if (gePackageCards.length > 0) {
                        tl.to(gePackageCards, { opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: "back.out(1.2)" }, "<0.2");
                    }
                },
                once: true
            });
        }

        // Entrance Animation for Journal & Insights
        const journalSection = document.querySelector('.home-journal-section');
        if (journalSection) {
            const journalLabel = journalSection.querySelector('.section-label');
            const journalTitle = journalSection.querySelector('h2.serif');
            const journalCards = gsap.utils.toArray(journalSection.querySelectorAll('.journal-card'));

            // Initial State setup: Texts appear from top (negative y)
            if (journalLabel && journalTitle) {
                gsap.set([journalLabel, journalTitle], { opacity: 0, y: -40 }); 
            }
            if (journalCards.length > 0) {
                // First 2 cards appear from left
                const leftCards = journalCards.slice(0, 2);
                if (leftCards.length) gsap.set(leftCards, { opacity: 0, x: -80 });
                
                // Next 2 cards appear from right
                const rightCards = journalCards.slice(2);
                if (rightCards.length) gsap.set(rightCards, { opacity: 0, x: 80 });
            }

            // Create ScrollTrigger Timeline
            ScrollTrigger.create({
                trigger: journalSection,
                start: "top 75%",
                onEnter: () => {
                    const tl = gsap.timeline();
                    
                    if (journalLabel) {
                        tl.to(journalLabel, { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" });
                    }
                    if (journalTitle) {
                        tl.to(journalTitle, { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" }, "-=0.4");
                    }
                    
                    if (journalCards.length > 0) {
                        const leftCards = journalCards.slice(0, 2);
                        const rightCards = journalCards.slice(2);
                        
                        // Slide left cards in
                        if (leftCards.length) {
                            tl.to(leftCards, { opacity: 1, x: 0, duration: 0.8, stagger: 0.15, ease: "back.out(1.2)" }, "-=0.4");
                        }
                        // Slide right cards in AT THE EXACT SAME TIME ("<")
                        if (rightCards.length) {
                            tl.to(rightCards, { opacity: 1, x: 0, duration: 0.8, stagger: 0.15, ease: "back.out(1.2)" }, "<");
                        }
                    }
                },
                once: true
            });
        }

        // Entrance Animation for Bespoke Travel Experiences
        const themesSection = document.querySelector('.themes-curation-section');
        if (themesSection) {
            const themesLabel = themesSection.querySelector('.section-label-gold');
            const themesTitle = themesSection.querySelector('h2.serif-accent');
            const themePills = gsap.utils.toArray(themesSection.querySelectorAll('.theme-pill'));
            const themeCards = gsap.utils.toArray(themesSection.querySelectorAll('.theme-card'));

            // Initial State setup
            if (themesLabel && themesTitle) {
                gsap.set([themesLabel, themesTitle], { opacity: 0, y: 40 }); 
            }
            if (themePills.length > 0) {
                gsap.set(themePills, { opacity: 0, scale: 0.95, y: 15 });
            }
            if (themeCards.length > 0) {
                const leftThemeCards = themeCards.slice(0, 2);
                const rightThemeCards = themeCards.slice(2, 4);
                if (leftThemeCards.length) gsap.set(leftThemeCards, { opacity: 0, x: -150, rotation: -10 });
                if (rightThemeCards.length) gsap.set(rightThemeCards, { opacity: 0, x: 150, rotation: 10 });
            }

            // Create ScrollTrigger Timeline
            ScrollTrigger.create({
                trigger: themesSection,
                start: "top 85%",
                onEnter: () => {
                    const tl = gsap.timeline();
                    
                    if (themesLabel) {
                        tl.to(themesLabel, { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" });
                    }
                    if (themesTitle) {
                        tl.to(themesTitle, { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" }, "-=0.6");
                    }
                    if (themePills.length > 0) {
                        tl.to(themePills, { opacity: 1, scale: 1, y: 0, duration: 0.8, stagger: 0.08, ease: "power2.out" }, "-=0.4");
                    }
                    if (themeCards.length > 0) {
                        const leftThemeCards = themeCards.slice(0, 2);
                        const rightThemeCards = themeCards.slice(2, 4);
                        
                        if (leftThemeCards.length) {
                            tl.to(leftThemeCards, { 
                                opacity: 1, 
                                x: 0, 
                                rotation: (i) => i === 0 ? -4 : -2, 
                                duration: 1.0, 
                                stagger: 0.1, 
                                ease: "back.out(1.4)" 
                            }, "<0.2");
                        }
                        if (rightThemeCards.length) {
                            tl.to(rightThemeCards, { 
                                opacity: 1, 
                                x: 0, 
                                rotation: (i) => i === 0 ? 2 : 4, 
                                duration: 1.0, 
                                stagger: 0.1, 
                                ease: "back.out(1.4)" 
                            }, "<");
                        }
                    }
                },
                once: true
            });
        }

        // Horizontal Scroll Hijack removed in favor of Natural Drag-to-Scroll Slider
        
        // Mandala Parallax for Bespoke Themes Section
        let themeMandala = document.getElementById('themeMandala');
        if (themeMandala) {
            // Center the image precisely
            gsap.set(themeMandala, { xPercent: -50, yPercent: -50 });
            
            gsap.to(themeMandala, {
                rotation: 45, // Slow rotation
                y: "30%", // Slight downward drift
                ease: "none",
                scrollTrigger: {
                    trigger: ".themes-curation-section",
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }
        
        // Staggered Editorial Reveal for Distinction Grid
        const distinctionItems = gsap.utils.toArray('.distinction-item');
        if (distinctionItems.length > 0) {
            // Initial state
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
        // Golden Deck for Companies
        const companyDeckSection = document.querySelector('.companies-deck-section');
        let companyCards = gsap.utils.toArray('.company-card').reverse(); // Reverse so DOM last child (Top Card) is first in array

        if (companyDeckSection && companyCards.length > 0) {
            // Interactive 3D tilt on hover for dynamic movement
            companyCards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = ((y - centerY) / centerY) * -5;
                    const rotateY = ((x - centerX) / centerX) * 5;
                    
                    gsap.to(card, {
                        rotateX: rotateX,
                        rotateY: rotateY,
                        duration: 0.4,
                        ease: "power1.out",
                        overwrite: "auto"
                    });
                });
                
                card.addEventListener('mouseleave', () => {
                    gsap.to(card, {
                        rotateX: 0,
                        rotateY: 0,
                        duration: 0.7,
                        ease: "power2.out",
                        overwrite: "auto"
                    });
                });
            });

            // Initial positioning: stagger them significantly so they peek out from top
            gsap.set(companyCards, {
                transformOrigin: "bottom center",
                z: (i) => i * -80,
                y: (i) => i * -80,
                scale: (i) => 1 - (i * 0.06),
                opacity: (i) => 1 - (i * 0.3)
            });

            // Create a timeline that pins the section and animates the cards
            const companyDeckTl = gsap.timeline({
                scrollTrigger: {
                    trigger: companyDeckSection,
                    start: "center center",
                    end: "+=" + (companyCards.length * window.innerHeight * 0.8),
                    pin: true,
                    scrub: 1
                }
            });

            // For each card (except the last one which stays at the bottom of the stack)
            companyCards.forEach((card, i) => {
                if (i < companyCards.length - 1) {
                    // Top card flies up and fades out
                    companyDeckTl.to(card, {
                        yPercent: -150,
                        opacity: 0,
                        duration: 1,
                        ease: "power2.inOut"
                    }, i);

                    // All cards below it move up and scale up
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

        // FAQ Accordion
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const item = question.closest('.faq-item');
                const isActive = item.classList.contains('active');
                
                // Close all other FAQs
                document.querySelectorAll('.faq-item').forEach(faq => {
                    faq.classList.remove('active');
                });
                
                // Open the clicked one if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

        // FAQ Watermark Parallax
        const faqWatermark = document.getElementById('faqWatermark');
        if (faqWatermark) {
            gsap.to(faqWatermark, {
                yPercent: -30,
                ease: "none",
                scrollTrigger: {
                    trigger: ".faq-section",
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });
        }

        // The Process Section Animation
        const processSection = document.querySelector('.how-we-work-section');
        if (processSection) {
            // Select header elements but explicitly exclude the paragraph inside the cards
            const processHeader = processSection.querySelectorAll('.section-label-gold, .section-title, p:not(.process-text)');
            const processCards = processSection.querySelectorAll('.process-card');
            const processImages = processSection.querySelectorAll('.process-img-canvas');
            
            // Initial states
            gsap.set(processHeader, { opacity: 0, y: 30 });
            gsap.set(processCards, { opacity: 0, y: 50, scale: 0.95 });
            if (processImages.length) gsap.set(processImages, { scale: 1.25, filter: 'brightness(0.5)' });

            ScrollTrigger.create({
                trigger: processSection,
                start: "top 75%",
                onEnter: () => {
                    const tl = gsap.timeline();
                    
                    // 1. Animate header
                    tl.to(processHeader, {
                        opacity: 1,
                        y: 0,
                        duration: 0.8,
                        stagger: 0.2,
                        ease: "power3.out"
                    });
                    
                    // 2. Animate cards seamlessly after header
                    tl.to(processCards, {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        duration: 0.8,
                        stagger: 0.2,
                        ease: "power3.out",
                        clearProps: "transform"
                    }, "-=0.4");
                    
                    // 3. Animate image zoom inside the cards
                    if (processImages.length) {
                        tl.to(processImages, {
                            scale: 1,
                            filter: 'brightness(1)',
                            duration: 1.6,
                            stagger: 0.2,
                            ease: "power3.out",
                            clearProps: "transform"
                        }, "-=0.8");
                    }
                }
            });
        }
        
        // CRITICAL FIX: Force GSAP to recalculate all ScrollTriggers in actual DOM order.
        // Because the "#destinations" section uses "pin: true", it adds a massive 
        // pin-spacer to the layout. Any animations defined before it (but located physically 
        // below it in the HTML) were calculating their start positions based on the 
        // un-pinned layout, causing them to fire invisibly off-screen!        
        // Signature Terrains Drag & Entrance Animation
        const destCarousel = document.querySelector('.destinations-carousel');
        const nextBtn = document.querySelector('.next-dest');
        const prevBtn = document.querySelector('.prev-dest');
        const destCards = document.querySelectorAll('.dest-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        if (destCarousel) {
            if (window.setupGSAPMomentumDrag) {
                window.setupGSAPMomentumDrag(destCarousel);
            }

            // Nav buttons
            const scrollAmount = 372;
            nextBtn?.addEventListener('click', () => { destCarousel.scrollBy({ left: scrollAmount, behavior: 'smooth' }); });
            prevBtn?.addEventListener('click', () => { destCarousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); });

            function applyStaggering() {
                const visibleCards = Array.from(destCards).filter(c => !c.classList.contains('hidden'));
                visibleCards.forEach((card, index) => {
                    card.classList.remove('stagger-up', 'stagger-down');
                    if (index % 2 === 0) {
                        card.classList.add('stagger-up'); // 1st, 3rd, 5th -> UP
                    } else {
                        card.classList.add('stagger-down'); // 2nd, 4th, 6th -> DOWN
                    }
                });
            }

            // Apply initially
            applyStaggering();

            // Filter logic
            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const filter = btn.dataset.filter;
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    destCards.forEach(card => {
                        if (card.dataset.category === filter) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                    applyStaggering();
                    destCarousel.scrollTo({ left: 0, behavior: 'auto' });
                    
                    // Re-run the entrance animation
                    const visibleCards = Array.from(destCards).filter(c => !c.classList.contains('hidden'));
                    gsap.fromTo(visibleCards, {
                        opacity: 0,
                        y: 100
                    }, {
                        opacity: 1,
                        y: 0,
                        duration: 0.8,
                        stagger: 0.1,
                        ease: "power3.out",
                        clearProps: "all"
                    });
                });
            });

            // GSAP Entrance Animation
            gsap.from(".dest-card", {
                scrollTrigger: {
                    trigger: ".destinations-carousel",
                    start: "top 85%",
                    once: true,
                },
                opacity: 0,
                y: "+=100",
                duration: 1.2,
                stagger: 0.15,
                ease: "power3.out",
                clearProps: "all"
            });
        }

        // Fixed Departures Compass Parallax
        const compassSection = document.querySelector('.fixed-departures-section');
        if (compassSection) {
            const ring1 = document.getElementById('compass-ring-1');
            const ring2 = document.getElementById('compass-ring-2');
            const ring3 = document.getElementById('compass-ring-3');
            const compassWrapper = document.querySelector('.fd-parallax-compass');

            const compassTl = gsap.timeline({
                scrollTrigger: {
                    trigger: compassSection,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: 1
                }
            });

            if (compassWrapper) {
                compassTl.to(compassWrapper, { y: -250, ease: "none" }, 0);
            }

            if (ring1) compassTl.to(ring1, { rotation: 180, ease: "none", transformOrigin: "50% 50%" }, 0);
            if (ring2) compassTl.to(ring2, { rotation: -240, ease: "none", transformOrigin: "50% 50%" }, 0);
            if (ring3) compassTl.to(ring3, { rotation: 360, ease: "none", transformOrigin: "50% 50%" }, 0);
        }
        
        // (GSAP vertical parallax for water removed to prevent moving background edge)
        
        ScrollTrigger.sort();
        
        // Recalculate precisely after all heavy images and fonts have fully loaded
        window.addEventListener('load', () => {
            setTimeout(() => {
                ScrollTrigger.refresh();
            }, 100);
        });
        // Generic drag-to-scroll for all packages-carousel via GSAP Momentum
        document.querySelectorAll('.packages-carousel').forEach(carousel => {
            if (window.setupGSAPMomentumDrag) window.setupGSAPMomentumDrag(carousel);
        });
        
    
    ScrollTrigger.refresh();
    console.log('GSAP initialized:', typeof gsap, typeof ScrollTrigger);
}

