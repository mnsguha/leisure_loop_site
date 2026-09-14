/**
 * Leisure Loop Trip - Elite Voyager JS
 */

/**
 * Universal GSAP Momentum Drag-to-Scroll Helper
 * Provides luxury Lenis-like smooth easing physics (power2.out) when dragging and releasing horizontal carousels.
 */
window.setupGSAPMomentumDrag = function(track, options = {}) {
    if (!track || track._gsapDragInitialized) return;
    track._gsapDragInitialized = true;
    let isDown = false;
    let startX;
    let scrollLeft;
    let didDrag = false;
    let snapTimeout;
    let lastX = 0;
    let lastTime = 0;
    let velocity = 0;
    const activeClass = options.activeClass || 'active-drag';
    const multiplier = options.multiplier || 2;
    const duration = options.duration || 0.8;
    const ease = options.ease || "power2.out";

    track.addEventListener('mousedown', (e) => {
        isDown = true;
        didDrag = false;
        clearTimeout(snapTimeout);
        track.classList.add(activeClass);
        if (options.styleScrollBehavior) track.style.scrollBehavior = 'auto';
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
        lastX = e.pageX;
        lastTime = performance.now();
        velocity = 0;
        
        if (typeof gsap !== 'undefined') {
            gsap.killTweensOf(track);
            if (track._momentumProxy) gsap.killTweensOf(track._momentumProxy);
        }
        if (options.onDragStart) options.onDragStart();
        e.preventDefault();
    });

    const endDrag = () => {
        if (!isDown) return;
        isDown = false;
        clearTimeout(snapTimeout);
        
        if (didDrag && Math.abs(velocity) > 0.1 && typeof gsap !== 'undefined') {
            const coastDistance = -velocity * 300;
            const targetScroll = track.scrollLeft + coastDistance;
            track._momentumProxy = { x: track.scrollLeft };
            gsap.to(track._momentumProxy, {
                x: targetScroll,
                duration: duration,
                ease: ease,
                onUpdate: () => {
                    track.scrollLeft = track._momentumProxy.x;
                },
                onComplete: () => {
                    track.classList.remove(activeClass);
                    if (options.styleScrollBehavior) track.style.scrollBehavior = 'smooth';
                    if (options.onDragEnd) options.onDragEnd();
                }
            });
        } else {
            track.classList.remove(activeClass);
            if (options.styleScrollBehavior) track.style.scrollBehavior = 'smooth';
            if (options.onDragEnd) options.onDragEnd();
        }
    };

    track.addEventListener('mouseleave', endDrag);
    track.addEventListener('mouseup', endDrag);

    track.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const now = performance.now();
        const dt = now - lastTime;
        if (dt > 0) {
            velocity = (e.pageX - lastX) / dt;
            lastX = e.pageX;
            lastTime = now;
        }

        const walk = (x - startX) * multiplier;
        if (Math.abs(walk) > 5) didDrag = true;
        
        const targetScroll = scrollLeft - walk;
        track.scrollLeft = targetScroll;
    });

    track.querySelectorAll('a, [data-url]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (didDrag) {
                e.preventDefault();
                e.stopPropagation();
            } else if (!options.preventNavigate) {
                const url = el.getAttribute('href') || el.dataset.url;
                if (el.dataset.url && url) {
                    window.location.href = url;
                } else if (url && !url.startsWith('#') && url !== 'javascript:void(0)') {
                    window.location.href = url;
                }
            }
        });
    });

    track.querySelectorAll('img').forEach(el => {
        el.addEventListener('dragstart', (e) => e.preventDefault());
    });
};

document.addEventListener('DOMContentLoaded', function() {
    
    // --- Lenis Smooth Scroll Initialization ---
    if (typeof Lenis !== 'undefined') {
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // https://www.desmos.com/calculator/brs54l4xou
            direction: 'vertical',
            gestureDirection: 'vertical',
            smooth: true,
            mouseMultiplier: 1,
            smoothTouch: false,
            touchMultiplier: 2,
            infinite: false,
        });

        // Get scroll value
        if (typeof ScrollTrigger !== 'undefined') {
            lenis.on('scroll', ScrollTrigger.update);

            gsap.ticker.add((time) => {
                lenis.raf(time * 1000);
            });

            gsap.ticker.lagSmoothing(0);
        } else {
            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }
    }

    // --- Global GSAP Parallax Backgrounds ---
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.utils.toArray('.gsap-parallax-bg').forEach(bg => {
            // Set initial state
            gsap.set(bg, { yPercent: -15 });
            
            gsap.to(bg, {
                yPercent: 15,
                ease: "none",
                scrollTrigger: {
                    trigger: bg.parentElement,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: true
                }
            });
        });
    }
    // ------------------------------------------

    // 1. Premium Navbar Scroll Effect
    const nav = document.querySelector('.glass-nav');
    
    // Add 'scrolled' class once user scrolls past the top bar (38px), moving nav to top:0
    const handleScroll = () => {
        if (!nav) return;
        if (window.scrollY > 38) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initial check

    // If GSAP is loaded, use ScrollTrigger to ensure the navbar gets a dark background when reaching the white narrative section
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && nav) {
        ScrollTrigger.create({
            trigger: "body",
            start: "100px top",
            onEnter: () => nav.classList.add('scrolled'),
            onLeaveBack: () => nav.classList.remove('scrolled')
        });
        
        // Specific trigger for light sections (Narrative)
        const lightSections = document.querySelectorAll('.dest-story-section');
        lightSections.forEach(section => {
            ScrollTrigger.create({
                trigger: section,
                start: "top 100px", // When the section reaches the navbar
                end: "bottom top",
                onEnter: () => nav.classList.add('nav-light-theme'),
                onLeaveBack: () => nav.classList.remove('nav-light-theme'),
                onEnterBack: () => nav.classList.add('nav-light-theme'),
                onLeave: () => nav.classList.remove('nav-light-theme')
            });
        });
    }

    // 2. Smooth Scroll for Nav Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 3. Reveal Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                // We can add more complex reveal logic here if needed
            }
        });
    }, observerOptions);

    document.querySelectorAll('.experience-card, .section-title, .section-label').forEach(el => {
        observer.observe(el);
    });

    // 4. Custom Toast Notification System
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = type === 'success' ? '✓' : '✕';
        toast.innerHTML = `<span class="toast-icon">${icon}</span> <span>${message}</span>`;
        
        container.appendChild(toast);
        
        // Trigger reflow for animation
        toast.offsetHeight;
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400); // Wait for transition
        }, 4000);
    }

    // 5. Lead Form AJAX Submission
    document.querySelectorAll('.js-lead-form').forEach((leadForm) => {
        leadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = leadForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'PROCESSING...';
            submitBtn.disabled = true;

            const formData = new FormData(leadForm);

            try {
                const response = await fetch(leadForm.getAttribute('action') || 'api-submit-lead.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    leadForm.reset();
                    
                    // Revert Hero Form UI back to Step 1 (single row)
                    const heroStep2 = document.getElementById('desktop-heroStep2') || document.getElementById('heroStep2');
                    const btnStep1Next = document.getElementById('desktop-btnStep1Next') || document.getElementById('btnStep1Next');
                    if (heroStep2 && btnStep1Next && leadForm.id === 'heroLeadForm') {
                        heroStep2.style.display = 'none';
                        btnStep1Next.style.display = 'block';
                        
                        // Reset detached popover values
                        const adultsQty = document.getElementById('desktop-adultsQty') || document.getElementById('adultsQty');
                        const childrenQty = document.getElementById('desktop-childrenQty') || document.getElementById('childrenQty');
                        if (adultsQty) adultsQty.value = 2;
                        if (childrenQty) childrenQty.value = 0;
                        const travelerInputDisplay = document.getElementById('desktop-travelerInputDisplay') || document.getElementById('travelerInputDisplay');
                        if (travelerInputDisplay) travelerInputDisplay.value = '2 Adults';
                    }

                    if (window.grecaptcha) {
                        const widget = leadForm.querySelector('.g-recaptcha');
                        if (widget) {
                            window.grecaptcha.reset();
                        }
                    }
                } else {
                    showToast('Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Submission error:', error);
                showToast('We encountered a connection issue. Please reach us via WhatsApp.', 'error');
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });
    });

    // 5. Signature Terrains handled entirely in index.php to prevent animation conflicts

    // 6. Cinematic Hero Multi-Slider
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots = document.querySelectorAll('.hero-dot');
    const heroVideos = document.querySelectorAll('.hero-slide-video');
    let currentSlide = 0;
    let slideInterval;

    const tryPlayVideo = (video) => {
        if (!video) return;
        const playPromise = video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(() => {
                // Keep the fallback visible if the browser declines autoplay.
            });
        }
    };

    heroVideos.forEach((video) => {
        const slide = video.closest('.hero-slide');
        if (!slide) return;

        const markReady = () => slide.classList.add('video-ready');
        const markError = () => slide.classList.remove('video-ready');
        const syncActiveVideo = () => {
            if (slide.classList.contains('active')) {
                tryPlayVideo(video);
            }
        };

        video.addEventListener('loadedmetadata', syncActiveVideo);
        video.addEventListener('canplay', syncActiveVideo);
        video.addEventListener('playing', markReady);
        video.addEventListener('play', markReady);
        video.addEventListener('error', markError);

        if (video.readyState >= 2 && slide.classList.contains('active')) {
            tryPlayVideo(video);
        }
    });

    const activeHeroVideo = document.querySelector('.hero-slide.active .hero-slide-video');
    if (activeHeroVideo) {
        tryPlayVideo(activeHeroVideo);
    }

    if (heroSlides.length > 1) {
        const nextSlide = () => {
            if (heroSlides[currentSlide]) heroSlides[currentSlide].classList.remove('active');
            if (heroDots[currentSlide]) heroDots[currentSlide].classList.remove('active');
            
            // Stop current video if exists
            const currentVideo = heroSlides[currentSlide]?.querySelector('video');
            if (currentVideo) currentVideo.pause();

            currentSlide = (currentSlide + 1) % heroSlides.length;
            
            if (heroSlides[currentSlide]) heroSlides[currentSlide].classList.add('active');
            if (heroDots[currentSlide]) heroDots[currentSlide].classList.add('active');
            
            // Play next video if exists
            const nextVideo = heroSlides[currentSlide].querySelector('video');
            if (nextVideo) {
                nextVideo.currentTime = 0;
                tryPlayVideo(nextVideo);
            }
        };

        const startInterval = () => {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 8000); // 8 second rotation
        };

        heroDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                if (heroSlides[currentSlide]) heroSlides[currentSlide].classList.remove('active');
                if (heroDots[currentSlide]) heroDots[currentSlide].classList.remove('active');
                
                // Stop video
                const v = heroSlides[currentSlide]?.querySelector('video');
                if (v) v.pause();

                currentSlide = index;
                
                if (heroSlides[currentSlide]) heroSlides[currentSlide].classList.add('active');
                if (heroDots[currentSlide]) heroDots[currentSlide].classList.add('active');
                
                // Play video
                const nextV = heroSlides[currentSlide]?.querySelector('video');
                if (nextV) {
                    nextV.currentTime = 0;
                    tryPlayVideo(nextV);
                }
                
                startInterval();
            });
        });

        startInterval();
    }

    // 7. Planner Modal Logic
    const plannerModal = document.getElementById('plannerModal');
    
    // Open Modal via Event Delegation
    document.addEventListener('click', (e) => {
        const inquireBtn = e.target.closest('a[href="#inquire"]');
        if (inquireBtn) {
            e.preventDefault();
            if (plannerModal) {
                plannerModal.style.display = 'flex';
                document.body.classList.add('scroll-lock');
            }
        }
    });

    // Close Modal Globally
    window.closePlanner = () => {
        if (plannerModal) {
            plannerModal.style.display = 'none';
            document.body.classList.remove('scroll-lock');
        }
    };

    // Form Steps Logic
    const plannerSteps = document.querySelectorAll('.planner-steps .step');
    const nextBtnStep = document.getElementById('nextBtn');
    const prevBtnStep = document.getElementById('prevBtn');
    const submitBtnStep = document.getElementById('submitBtn');
    let currentPlannerStep = 0;

    const updatePlannerView = () => {
        plannerSteps.forEach((step, index) => {
            if (index === currentPlannerStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        if (currentPlannerStep === 0) {
            if (prevBtnStep) prevBtnStep.style.display = 'none';
        } else {
            if (prevBtnStep) prevBtnStep.style.display = 'inline-block';
        }

        if (currentPlannerStep === plannerSteps.length - 1) {
            if (nextBtnStep) nextBtnStep.style.display = 'none';
            if (submitBtnStep) submitBtnStep.style.display = 'inline-block';
        } else {
            if (nextBtnStep) nextBtnStep.style.display = 'inline-block';
            if (submitBtnStep) submitBtnStep.style.display = 'none';
        }
    };

    if (nextBtnStep) {
        nextBtnStep.addEventListener('click', () => {
            if (currentPlannerStep < plannerSteps.length - 1) {
                currentPlannerStep++;
                updatePlannerView();
            }
        });
    }

    if (prevBtnStep) {
        prevBtnStep.addEventListener('click', () => {
            if (currentPlannerStep > 0) {
                currentPlannerStep--;
                updatePlannerView();
            }
        });
    }

    // Handle AJAX Submission for Planner Modal
    const travelPlannerForm = document.getElementById('travelPlannerForm');
    if (travelPlannerForm) {
        travelPlannerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (submitBtnStep) {
                const originalText = submitBtnStep.innerText;
                submitBtnStep.innerText = 'SENDING...';
                submitBtnStep.disabled = true;
            }

            const formData = new FormData(travelPlannerForm);
            
            // Map fields for backend
            const payload = new FormData();
            payload.append('name', formData.get('name') || '');
            payload.append('email', formData.get('email') || '');
            payload.append('phone', formData.get('phone') || '');
            payload.append('destination', formData.get('destination') || '');
            
            const timeline = formData.get('timeline') || '';
            const travelers = formData.get('travelers') || '';
            payload.append('message', `Timeline: ${timeline}\nTravelers: ${travelers}`);
            
            try {
                const response = await fetch('api-submit-lead.php', {
                    method: 'POST',
                    body: payload
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Thank you! Our curators will reach out to you soon.', 'success');
                    travelPlannerForm.reset();
                    currentPlannerStep = 0;
                    updatePlannerView();
                    window.closePlanner();
                } else {
                    showToast('Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Submission error:', error);
                showToast('We encountered a connection issue. Please reach us via WhatsApp.', 'error');
            } finally {
                if (submitBtnStep) {
                    submitBtnStep.innerText = 'Request Consultation';
                    submitBtnStep.disabled = false;
                }
            }
        });
    }

    // --- HERO INLINE FORM: TRAVELER POPOVER LOGIC ---
    const travelerDropdownTrigger = document.getElementById('travelerDropdownTrigger');
    const travelerInputDisplay    = document.getElementById('desktop-travelerInputDisplay') || document.getElementById('travelerInputDisplay');
    const travelerPopover         = document.getElementById('desktop-travelerPopover') || document.getElementById('travelerPopover');
    const btnDoneTravelers        = document.getElementById('desktop-btnDoneTravelers') || document.getElementById('btnDoneTravelers');
    const adultsQty               = document.getElementById('desktop-adultsQty') || document.getElementById('adultsQty');
    const childrenQty             = document.getElementById('desktop-childrenQty') || document.getElementById('childrenQty');

    function updateTravelerDisplay() {
        if (!travelerInputDisplay) return;
        const a = parseInt(adultsQty?.value || 0);
        const c = parseInt(childrenQty?.value || 0);
        let parts = [];
        if (a > 0) parts.push(`${a} Adult${a > 1 ? 's' : ''}`);
        if (c > 0) parts.push(`${c} Child${c > 1 ? 'ren' : ''}`);
        travelerInputDisplay.value = parts.length ? parts.join(', ') : '0 Adults';
    }

    function positionPopover() {
        if (!travelerDropdownTrigger || !travelerPopover) return;
        const rect = travelerDropdownTrigger.getBoundingClientRect();
        const popoverWidth = 320;
        let left = rect.left + (rect.width / 2) - (popoverWidth / 2);
        if (left < 8) left = 8;
        if (left + popoverWidth > window.innerWidth - 8) left = window.innerWidth - popoverWidth - 8;
        travelerPopover.style.top  = (rect.bottom + 12) + 'px';
        travelerPopover.style.left = left + 'px';
    }

    // Detach popover to body so it escapes overflow:hidden
    if (travelerPopover) document.body.appendChild(travelerPopover);

    if (travelerDropdownTrigger && travelerPopover) {

        // Open/close on display input click
        travelerInputDisplay && travelerInputDisplay.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = travelerPopover.classList.contains('show');
            travelerPopover.classList.remove('show');
            if (!isOpen) {
                positionPopover();
                requestAnimationFrame(() => travelerPopover.classList.add('show'));
            }
        });

        travelerDropdownTrigger.addEventListener('click', (e) => {
            if (e.target === travelerInputDisplay) return;
            e.stopPropagation();
        });

        travelerPopover.addEventListener('click', (e) => e.stopPropagation());

        // APPLY: sync hidden fields, update display, close
        btnDoneTravelers && btnDoneTravelers.addEventListener('click', (e) => {
            e.stopPropagation();
            updateTravelerDisplay();
            travelerPopover.classList.remove('show');
        });

        document.addEventListener('click', () => travelerPopover.classList.remove('show'));

        window.addEventListener('scroll', () => {
            if (travelerPopover.classList.contains('show')) positionPopover();
        }, { passive: true });
        window.addEventListener('resize', () => {
            if (travelerPopover.classList.contains('show')) positionPopover();
        });

        // +/- qty buttons
        travelerPopover.querySelectorAll('.btn-qty').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const input = document.getElementById(this.getAttribute('data-target'));
                if (!input) return;
                let val = parseInt(input.value);
                const min = parseInt(input.getAttribute('min') || 0);
                const max = parseInt(input.getAttribute('max') || 20);
                if (this.classList.contains('btn-plus')) { if (val < max) val++; }
                else { if (val > min) val--; }
                input.value = val;
                updateTravelerDisplay();
            });
        });

        // Init
        updateTravelerDisplay();
    }

    // Step 1 -> Step 2 Reveal Logic
    const btnStep1Next = document.getElementById('desktop-btnStep1Next') || document.getElementById('btnStep1Next');
    const heroStep2 = document.getElementById('desktop-heroStep2') || document.getElementById('heroStep2');
    const heroDestInput = document.getElementById('desktop-heroDestInput') || document.getElementById('heroDestInput');
    const heroDateInput = document.getElementById('desktop-heroDateInput') || document.getElementById('heroDateInput');

    if (btnStep1Next && heroStep2) {
        btnStep1Next.addEventListener('click', () => {
            const dest = heroDestInput?.value || '';
            const date = heroDateInput?.value || '';

            if (!dest) { alert('Please enter a destination.'); return; }

            // Sync all fields to the form
            (document.getElementById('desktop-hiddenDest') || document.getElementById('hiddenDest')).value = dest;
            (document.getElementById('desktop-hiddenDate') || document.getElementById('hiddenDate')).value = date;
            
            const hiddenAdults = document.getElementById('desktop-hiddenAdults') || document.getElementById('hiddenAdults');
            const hiddenChildren = document.getElementById('desktop-hiddenChildren') || document.getElementById('hiddenChildren');
            if (hiddenAdults) hiddenAdults.value = adultsQty?.value || 0;
            if (hiddenChildren) hiddenChildren.value = childrenQty?.value || 0;

            // Reveal step 2 and hide step 1 button
            heroStep2.style.display = 'block';
            btnStep1Next.style.display = 'none';
        });
    }
    // 6. Leisure Difference Drag-to-Scroll Slider with GSAP Momentum Easing
    const slider = document.getElementById('differenceSlider');
    if (slider) {
        window.setupGSAPMomentumDrag(slider, { activeClass: 'active', styleScrollBehavior: true });
    }

    // 7. Universal Infinite Drag-to-Scroll (Curated Collection, Themes, Film Roll)
    function setupInfiniteCarousel(selector, autoScrollSpeed = 0) {
        const carousel = document.querySelector(selector);
        if (!carousel) return;

        // Force necessary CSS for dragging
        carousel.style.overflowX = 'auto';
        carousel.style.display = 'flex';
        carousel.style.flexWrap = 'nowrap';
        // Hide scrollbar and disable CSS snapping/animations that might interfere
        carousel.style.scrollbarWidth = 'none';
        carousel.style.msOverflowStyle = 'none';
        carousel.style.scrollSnapType = 'none';
        carousel.style.animation = 'none';
        carousel.style.transform = 'none'; // Prevent existing CSS transforms from shifting the scroll track off screen
        carousel.style.width = '100%';
        
        // Prevent text selection during drag
        carousel.style.userSelect = 'none';
        carousel.style.webkitUserSelect = 'none';

        Array.from(carousel.children).forEach(child => child.style.animation = 'none');

        // Identify the container holding the actual items
        let itemsContainer = carousel;
        if (carousel.children.length === 1 && carousel.firstElementChild.tagName === 'DIV') {
            itemsContainer = carousel.firstElementChild;
        }

        // Clone the items to enable infinite scrolling
        const originalItems = Array.from(itemsContainer.children);
        if (originalItems.length === 0) return;
        
        // Calculate how many clones we need to safely fill the screen
        const originalTotalWidth = itemsContainer.scrollWidth;
if (!originalTotalWidth || originalTotalWidth <= 0) return;
const requiredCopies = Math.min(4, Math.max(2, Math.ceil(window.innerWidth / originalTotalWidth) + 1));
        
        for (let i = 0; i < requiredCopies; i++) {
            originalItems.forEach(item => {
                const clone = item.cloneNode(true);
                if(clone.id) clone.removeAttribute('id'); // Prevent duplicate IDs
                
                // Clear GSAP inline animation styles that might have been cloned in their initial hidden state
                clone.style.opacity = '';
                clone.style.transform = '';
                clone.style.scale = '';
                clone.style.visibility = '';
                
                itemsContainer.appendChild(clone);
            });
        }

        let isDown = false;
        let startX;
        let scrollLeft;

        // Ensure smooth scrolling is off during setup
        carousel.style.scrollBehavior = 'auto';

        // Infinite Wrap Logic
        const checkWrap = () => {
            // Calculate exact shift distance based on rendered offset
            const firstClone = itemsContainer.children[originalItems.length];
            if (!firstClone) return;
            const shiftDistance = firstClone.offsetLeft - originalItems[0].offsetLeft;
            
            if (shiftDistance <= 0) return; // safeguard if not rendered yet
            
            if (carousel.scrollLeft >= shiftDistance) {
                carousel.style.scrollBehavior = 'auto';
                carousel.scrollLeft -= shiftDistance;
                if (isDown) scrollLeft -= shiftDistance; // Sync drag reference
            } else if (carousel.scrollLeft <= 0) {
                carousel.style.scrollBehavior = 'auto';
                carousel.scrollLeft += shiftDistance;
                if (isDown) scrollLeft += shiftDistance; // Sync drag reference
            }
        };

        carousel.addEventListener('scroll', checkWrap, { passive: true });

        // Drag Interaction (No cursor modification as requested)
        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
            carousel.style.scrollBehavior = 'auto'; // Disable smooth scroll while dragging
// e.preventDefault(); removed to allow click through
        });

        // Prevent native HTML5 image/link dragging
        carousel.addEventListener('dragstart', (e) => {
// e.preventDefault(); removed to allow click through
        });

        carousel.addEventListener('mouseleave', () => {
            isDown = false;
        });

        carousel.addEventListener('mouseup', () => {
            isDown = false;
        });

        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2; // Scroll multiplier
            
            let targetScroll = scrollLeft - walk;

            // Mathematically wrap before DOM assignment to prevent browser clamping at 0
            const firstClone = itemsContainer.children[originalItems.length];
            if (firstClone) {
                const shiftDistance = firstClone.offsetLeft - originalItems[0].offsetLeft;
                if (shiftDistance > 0) {
                    while (targetScroll <= 0) {
                        targetScroll += shiftDistance;
                        scrollLeft += shiftDistance; // Sync drag reference
                    }
                    while (targetScroll >= shiftDistance) {
                        targetScroll -= shiftDistance;
                        scrollLeft -= shiftDistance; // Sync drag reference
                    }
                }
            }

            carousel.scrollLeft = targetScroll;
        });

        // Initialize position slightly past 0 to allow immediate left-drag wrapping
        setTimeout(() => {
            carousel.scrollLeft = 1;
        }, 100);

        // Auto Scroll Logic
        let animationId;
        let isHovered = false;

        const autoScroll = () => {
            if (!isDown && !isHovered && autoScrollSpeed !== 0) {
                carousel.scrollLeft += autoScrollSpeed;
                checkWrap();
            }
            animationId = requestAnimationFrame(autoScroll);
        };

        if (autoScrollSpeed !== 0) {
            carousel.addEventListener('mouseenter', () => isHovered = true);
            carousel.addEventListener('mouseleave', () => {
                isHovered = false;
                isDown = false; // also clear drag flag
            });
            autoScroll();
        }
    }

    // Apply to all requested sections (Accreditations and Film Roll get auto-scroll)
    setupInfiniteCarousel('.packages-carousel', 0);
    setupInfiniteCarousel('.ticker-wrapper', 1);
    setupInfiniteCarousel('.themes-pills-container', 0);
    setupInfiniteCarousel('.themes-carousel', 0);
    setupInfiniteCarousel('.accreditations-track', 1);
    setupInfiniteCarousel('.process-carousel', 0);
    setupInfiniteCarousel('.home-journal-carousel', 0);
    setupInfiniteCarousel('.track-left', 1.5);
    setupInfiniteCarousel('.track-right', -1.5);
    setupInfiniteCarousel('.partner-marquee-track', 1);

});

// ==========================================
// 3D TILT & ZOOM EFFECT FOR PACKAGE CARDS
// ==========================================
document.addEventListener('mousemove', (e) => {
    const card = e.target.closest('.package-card');
    if (!card) return;

    // Calculate mouse position relative to the card's center
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    
    // Dynamic rotation: adjust the divisor to increase/decrease tilt amount
    const rotateX = ((y - centerY) / centerY) * -6; 
    const rotateY = ((x - centerX) / centerX) * 6;
    
    // Apply the 3D transform and zoom
    card.style.transition = 'transform 0.1s ease-out';
    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.04, 1.04, 1.04)`;
    card.style.zIndex = '50';
});

document.addEventListener('mouseout', (e) => {
    const card = e.target.closest('.package-card');
    if (!card) return;
    
    // Ensure we actually left the card, not just hovered over a child element
    if (!card.contains(e.relatedTarget)) {
        // Smoothly reset the card to its original position
        card.style.transition = 'transform 0.6s cubic-bezier(0.25, 1, 0.5, 1), z-index 0.6s ease';
        card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        card.style.zIndex = '1';
        
        // Remove inline styles after animation so CSS hover effects can take over again
        setTimeout(() => {
            if (!card.matches(':hover')) {
                card.style.transform = '';
                card.style.zIndex = '';
            }
        }, 600);
    }
});

    // --- Dual Pane Enquiry Modal Logic ---
    const enquiryModal = document.getElementById('enquiryModal');
    const btnEnquiryNavs = document.querySelectorAll('.btn-enquiry-nav, .emt-pill-orange, [href="#inquiry-form"]');
    
    // Prevent default anchor behavior and open modal
    btnEnquiryNavs.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof window.openEnquiryModal === 'function') {
                window.openEnquiryModal();
            }
        });
    });

    window.openEnquiryModal = function() {
        if (enquiryModal) {
            enquiryModal.style.display = 'flex';
            // Slight delay to allow display: flex to apply before adding active class for transition
            setTimeout(() => {
                enquiryModal.classList.add('active');
            }, 10);
            
            // If body has lenis scroll, might want to stop it, but standard overflow hidden works
            document.body.classList.add('scroll-lock');
        }
    };

    window.closeEnquiryModal = function() {
        if (enquiryModal) {
            enquiryModal.classList.remove('active');
            setTimeout(() => {
                enquiryModal.style.display = 'none';
            }, 400); // matches CSS transition duration
            
            document.body.classList.remove('scroll-lock');
        }
    };

    // Close on overlay click
    if (enquiryModal) {
        enquiryModal.addEventListener('click', function(e) {
            if (e.target === enquiryModal) {
                closeEnquiryModal();
            }
        });
    }

    // Success override for enquiryPopupForm to close modal after submission
    const enquiryPopupForm = document.getElementById('enquiryPopupForm');
    if (enquiryPopupForm) {
        const submitBtn = enquiryPopupForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "disabled") {
                        // If it became re-enabled and the form is empty (reset), close it
                        if (!submitBtn.disabled && !document.getElementById('enq_name').value) {
                            setTimeout(closeEnquiryModal, 1500);
                        }
                    }
                });
            });
            observer.observe(submitBtn, { attributes: true });
        }
    }





    // Signature Terrains Entrance Animation handled cleanly in index.php
