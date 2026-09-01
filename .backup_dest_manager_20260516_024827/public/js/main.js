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

    // 4. Lead Form AJAX Submission
    document.querySelectorAll('.js-lead-form').forEach((leadForm) => {
        leadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = leadForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'PROCESSING...';
            submitBtn.disabled = true;

            const formData = new FormData(leadForm);

            try {
                const response = await fetch(leadForm.getAttribute('action') || '../api/submit-lead.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    leadForm.reset();
                    if (window.grecaptcha) {
                        const widget = leadForm.querySelector('.g-recaptcha');
                        if (widget) {
                            window.grecaptcha.reset();
                        }
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('We encountered a connection issue. Please reach us via WhatsApp.');
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
            heroSlides[currentSlide].classList.remove('active');
            heroDots[currentSlide].classList.remove('active');
            
            // Stop current video if exists
            const currentVideo = heroSlides[currentSlide].querySelector('video');
            if (currentVideo) currentVideo.pause();

            currentSlide = (currentSlide + 1) % heroSlides.length;
            
            heroSlides[currentSlide].classList.add('active');
            heroDots[currentSlide].classList.add('active');
            
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
                heroSlides[currentSlide].classList.remove('active');
                heroDots[currentSlide].classList.remove('active');
                
                // Stop video
                const v = heroSlides[currentSlide].querySelector('video');
                if (v) v.pause();

                currentSlide = index;
                
                heroSlides[currentSlide].classList.add('active');
                heroDots[currentSlide].classList.add('active');
                
                // Play video
                const nextV = heroSlides[currentSlide].querySelector('video');
                if (nextV) {
                    nextV.currentTime = 0;
                    tryPlayVideo(nextV);
                }
                
                startInterval();
            });
        });

        startInterval();
    }
});
