

        // Parallax Effect Logic

        const parallaxLayers = document.querySelectorAll('.parallax-layer');

        window.addEventListener('scroll', () => {

            const scrolled = window.pageYOffset;

            parallaxLayers.forEach(layer => {

                const speed = layer.getAttribute('data-speed') || 0;

                const yPos = -(scrolled * speed);

                layer.style.transform = `translateY(${yPos}px)`;

            });

        });



        // Sophisticated Scroll Reveal Animation Logic

        const revealCallback = (entries, observer) => {

            entries.forEach(entry => {

                if (entry.isIntersecting) {

                    entry.target.classList.add('reveal-visible');

                    // Unobserve after animating once for better performance 

                    // and to maintain an editorial flow

                    observer.unobserve(entry.target);

                }

            });

        };



        document.addEventListener('DOMContentLoaded', () => {

            window.addEventListener('error', function(e) {

                let errDiv = document.getElementById('debug-error');

                if(!errDiv) {

                    errDiv = document.createElement('div');

                    errDiv.id = 'debug-error';

                    errDiv.style.cssText = 'position:fixed; top:0; left:0; width:100%; background:red; color:white; z-index:999999; padding:20px; font-size:16px;';

                    document.body.appendChild(errDiv);

                }

                errDiv.innerHTML += '<br>Error: ' + e.message + ' at ' + e.filename + ':' + e.lineno;

            });

// 1. Initial Hero Drop-In or ParableVC Initialization

    if (document.querySelector('.parable-hero')) {

        // --- ParableVC Style 3D Parallax Hero ---

        // Initial load animation: Text rises from behind the village

        gsap.from(".p-layer-text span, .p-layer-text h1", {

            y: "40vh", // Start very low (behind village)

            opacity: 0,

            duration: 1.8,

            stagger: 0.2,

            ease: "power3.out",

            delay: 0.3 // Wait for images to load

        });        // ParableVC Mouse-Move 3D Parallax Logic

        if (window.innerWidth > 768) {

            const vp = document.querySelector('.parable-hero');

            const layers = vp.querySelectorAll('.p-layer[data-depth]');

            let mx = 0, my = 0, tx = 0, ty = 0, raf = null;



            vp.addEventListener('mousemove', e => {

                mx = (e.clientX / window.innerWidth  - 0.5) * 2;

                my = (e.clientY / window.innerHeight - 0.5) * 2;

                if (!raf) raf = requestAnimationFrame(tick);

            });



            vp.addEventListener('mouseleave', () => { mx = 0; my = 0; });



            function tick() {

                raf = null;

                tx += (mx - tx) * 0.07;

                ty += (my - ty) * 0.07;

                layers.forEach(layer => {

                    const depth = parseFloat(layer.dataset.depth) || 0.5;

                    // Adjust multiplier for how drastically the layers shift

                    const sx = depth * 25;

                    const sy = depth * 15;

                    gsap.set(layer, { x: tx * sx, y: ty * sy });

                });

                if (Math.abs(mx - tx) > 0.001 || Math.abs(my - ty) > 0.001) {

                    raf = requestAnimationFrame(tick);

                }

            }

        }



        // ScrollTrigger Parallax (Subtle scroll + seamless overlap)

        let parableTl = gsap.timeline({

            scrollTrigger: {

                trigger: ".parable-hero",

                start: "top top",

                end: "+=150%", // Toned down pinning duration so the fog section comes up faster

                scrub: true,

                pin: true,

                pinSpacing: false

            }

        });



        // In ParableVC, layers move at different speeds on scroll to create true depth

        parableTl.to(".p-layer-text", { yPercent: -150, opacity: 0, scale: 0.9 }, 0); // Text moves UP and fades

        parableTl.to(".p-layer-sky", { yPercent: -2 }, 0); // Sky moves up very slow

        parableTl.to(".p-layer-back", { yPercent: -5 }, 0); // Mountains move up faster than sky

        parableTl.to(".p-layer-mid", { yPercent: -10 }, 0); // Village moves up faster

        parableTl.to(".p-layer-foremost", { yPercent: -20 }, 0); // Flags move up faster than village

    }



        // EXPLORE THE STORY Inline Expansion

        let isStoryExpanded = false;



        document.addEventListener('click', (e) => {

            // Inline Button Click

            const inlineBtn = e.target.closest('#explore-btn');

            if (inlineBtn) {

                e.preventDefault();

                const extendedStory = document.getElementById('extended-story');

                const exploreText = document.getElementById('explore-text');

                

                if (extendedStory && exploreText) {

                    isStoryExpanded = !isStoryExpanded;

                    if (isStoryExpanded) {

                        gsap.to(extendedStory, {

                            height: "auto",

                            opacity: 1,

                            duration: 1.2,

                            ease: "power3.inOut"

                        });

                        exploreText.style.opacity = 0;

                        setTimeout(() => {

                            exploreText.textContent = "SHOW LESS";

                            exploreText.style.opacity = 1;

                        }, 300);

                    } else {

                        gsap.to(extendedStory, {

                            height: 0,

                            opacity: 0,

                            duration: 1.2,

                            ease: "power3.inOut"

                        });

                        exploreText.style.opacity = 0;

                        setTimeout(() => {

                            exploreText.textContent = "EXPLORE THE STORY (INLINE)";

                            exploreText.style.opacity = 1;

                        }, 300);

                        

                        // Scroll slightly back up to keep context

                        setTimeout(() => {

                            const navSec = document.getElementById('narrative');

                            if(navSec) {

                                window.scrollTo({

                                    top: navSec.offsetTop - 50,

                                    behavior: 'smooth'

                                });

                            }

                        }, 500);

                    }

                }

            }



            // Modal Button Click

            const modalBtn = e.target.closest('#explore-modal-btn');

            if (modalBtn) {

                e.preventDefault();

                openModal();

            }



            // Modal Close Clicks

            if (e.target.closest('#close-modal-btn') || e.target.closest('#close-modal-bottom')) {

                e.preventDefault();

                closeModal();

            }

            

            // Backdrop click

            const storyModal = document.getElementById('story-modal');

            if (storyModal && e.target === storyModal) {

                closeModal();

            }

        });



        function openModal() {

            document.body.style.overflow = 'hidden'; // Prevent background scrolling

            const storyModal = document.getElementById('story-modal');

            const storyModalContent = document.getElementById('story-modal-content');

            gsap.to(storyModal, { opacity: 1, duration: 0.5, pointerEvents: 'auto' });

            gsap.to(storyModalContent, { scale: 1, duration: 0.7, ease: "power3.out", delay: 0.1 });

        }



        function closeModal() {

            document.body.style.overflow = ''; // Restore scrolling

            const storyModal = document.getElementById('story-modal');

            const storyModalContent = document.getElementById('story-modal-content');

            gsap.to(storyModalContent, { scale: 0.95, duration: 0.4, ease: "power2.in" });

            gsap.to(storyModal, { opacity: 0, duration: 0.5, pointerEvents: 'none', delay: 0.2 });

        }

});

const revealObserver = new IntersectionObserver(revealCallback, {

            threshold: 0.2,

            rootMargin: '0px 0px -50px 0px'

        });



        // Initialize observers for both fade-up and scale-up elements

        document.querySelectorAll('.reveal-hidden, .reveal-scale-hidden').forEach(el => {

            revealObserver.observe(el);

        });



        // Dynamic scale factor for hero layers on scroll

        window.addEventListener('scroll', () => {

            const fog = document.querySelector('.hero-fog');

            if (fog) {

                const scrolled = window.pageYOffset;

                const opacity = 0.4 + (scrolled / 1000);

                fog.style.opacity = Math.min(opacity, 0.95);

            }

        });

    

        
