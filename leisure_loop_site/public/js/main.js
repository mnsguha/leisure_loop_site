/**
 * Leisure Loop Trip - Elite Voyager JS
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Premium Navbar Scroll Effect
    const nav = document.querySelector('.glass-nav');
    const handleScroll = () => {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initial check

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
                    const heroStep2 = document.getElementById('heroStep2');
                    const btnStep1Next = document.getElementById('btnStep1Next');
                    if (heroStep2 && btnStep1Next && leadForm.id === 'heroLeadForm') {
                        heroStep2.style.display = 'none';
                        btnStep1Next.style.display = 'block';
                        
                        // Reset detached popover values
                        const adultsQty = document.getElementById('adultsQty');
                        const childrenQty = document.getElementById('childrenQty');
                        if (adultsQty) adultsQty.value = 2;
                        if (childrenQty) childrenQty.value = 0;
                        const travelerInputDisplay = document.getElementById('travelerInputDisplay');
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

    // 5. Signature Terrains Filtering & Carousel
    const destCarousel = document.querySelector('.destinations-carousel');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const destCards = document.querySelectorAll('.dest-card');
    const nextBtn = document.querySelector('.next-dest');
    const prevBtn = document.querySelector('.prev-dest');

    if (destCarousel) {
        // Filtering Logic
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                
                // Update buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Filter cards
                destCards.forEach(card => {
                    if (card.dataset.category === filter) {
                        card.classList.remove('hidden');
                        card.style.opacity = '1';
                        card.style.transform = card.matches(':nth-child(odd)') ? 'translateY(40px)' : 'translateY(-40px)';
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Reset scroll
                destCarousel.scrollTo({ left: 0, behavior: 'smooth' });
            });
        });

        // Carousel Navigation
        const scrollAmount = 372; // Card width + gap

        nextBtn?.addEventListener('click', () => {
            destCarousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        prevBtn?.addEventListener('click', () => {
            destCarousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        // Initialize view
        filterBtns[0]?.click();
    }

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
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            }
        }
    });

    // Close Modal Globally
    window.closePlanner = () => {
        if (plannerModal) {
            plannerModal.style.display = 'none';
            document.body.style.overflow = 'auto';
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
    const travelerInputDisplay    = document.getElementById('travelerInputDisplay');
    const travelerPopover         = document.getElementById('travelerPopover');
    const btnDoneTravelers        = document.getElementById('btnDoneTravelers');
    const adultsQty               = document.getElementById('adultsQty');
    const childrenQty             = document.getElementById('childrenQty');

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
    const btnStep1Next = document.getElementById('btnStep1Next');
    const heroStep2 = document.getElementById('heroStep2');
    const heroDestInput = document.getElementById('heroDestInput');
    const heroDateInput = document.getElementById('heroDateInput');

    if (btnStep1Next && heroStep2) {
        btnStep1Next.addEventListener('click', () => {
            const dest = heroDestInput?.value || '';
            const date = heroDateInput?.value || '';

            if (!dest) { alert('Please enter a destination.'); return; }

            // Sync all fields to the form
            document.getElementById('hiddenDest').value = dest;
            document.getElementById('hiddenDate').value = date;
            
            const hiddenAdults = document.getElementById('hiddenAdults');
            const hiddenChildren = document.getElementById('hiddenChildren');
            if (hiddenAdults) hiddenAdults.value = adultsQty?.value || 0;
            if (hiddenChildren) hiddenChildren.value = childrenQty?.value || 0;

            // Reveal step 2 and hide step 1 button
            heroStep2.style.display = 'block';
            btnStep1Next.style.display = 'none';
        });
    }

});
