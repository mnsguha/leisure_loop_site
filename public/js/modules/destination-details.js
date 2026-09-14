'use strict';
(function () {

// Rule 1: Centralized image fallback (replaces inline onerror)
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG' && !e.target.dataset.fallbackDone) {
        e.target.dataset.fallbackDone = '1';
        e.target.src = 'assets/img/pkg.jpg';
    }
}, true);

// Rule 1: Apply z-index from data-layer-z attributes (replaces inline style)
document.querySelectorAll('[data-layer-z]').forEach(function(el) {
    el.style.zIndex = el.dataset.layerZ;
});

function initDestinationHeroParallax() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    const hero = document.querySelector('.parable-hero');
    if (!hero) return;

    const layers = hero.querySelectorAll('.p-layer[data-depth]');
    const cloudLeft = hero.querySelector('.cloud-door-left');
    const cloudRight = hero.querySelector('.cloud-door-right');

    // 1. Initial Load Entrance: Text rises from below using yPercent (same coordinate
    //    space as the ScrollTrigger scrub) so the two systems never fight each other.
    gsap.fromTo(".p-layer-text span, .p-layer-text h1",
        { yPercent: 80, opacity: 0 },
        { yPercent: 0, opacity: 1, duration: 1.8, stagger: 0.2, ease: "power3.out", delay: 0.2 }
    );

    // 2. Mousemove 3D Depth logic (Desktop)
    if (window.innerWidth > 768) {
        let mx = 0, my = 0, tx = 0, ty = 0, raf = null;

        hero.addEventListener('mousemove', (e) => {
            mx = (e.clientX / window.innerWidth - 0.5) * 2;
            my = (e.clientY / window.innerHeight - 0.5) * 2;
            if (!raf) raf = requestAnimationFrame(tick);
        });

        hero.addEventListener('mouseleave', () => { mx = 0; my = 0; });

        function tick() {
            raf = null;
            tx += (mx - tx) * 0.07;
            ty += (my - ty) * 0.07;
            layers.forEach(layer => {
                const depth = parseFloat(layer.getAttribute('data-depth')) || 0.2;
                gsap.set(layer, { 
                    xPercent: tx * (depth * 4), 
                    yPercent: ty * (depth * 2.5) 
                });
            });
            if (Math.abs(mx - tx) > 0.001 || Math.abs(my - ty) > 0.001) {
                raf = requestAnimationFrame(tick);
            }
        }
    }

    // 3. ScrollTrigger Parallax Multi-Layer Timeline
    const parableTl = gsap.timeline({
        scrollTrigger: {
            trigger: hero,
            start: "top top",
            end: "+=120%",
            scrub: true,
            pin: true,
            pinSpacing: true
        }
    });

    parableTl.to(".p-layer-text", { scale: 0.3, yPercent: -10, opacity: 0, ease: "power2.inOut" }, 0);
    parableTl.to(".p-layer-sky", { yPercent: -1 }, 0);
    parableTl.to(".p-layer-back", { yPercent: -3 }, 0);
    parableTl.to(".p-layer-mid", { yPercent: -7 }, 0);
    parableTl.to(".p-layer-foremost", { yPercent: -12 }, 0);

    if (cloudLeft && cloudRight) {
        parableTl.to(cloudLeft, { xPercent: -100, opacity: 0 }, 0);
        parableTl.to(cloudRight, { xPercent: 100, opacity: 0 }, 0);
    }

    // Zoom the full composition on scroll (Start explicitly at 1.15 to preserve the CSS edge buffer)
    parableTl.fromTo(".parable-hero > img.p-layer, .parable-hero > video.p-layer", 
        { scale: 1.15 }, 
        {
            scale: 1.30,
            transformOrigin: "center center",
            ease: "none"
        }, 
        0
    );
}


function initNarrativeCardsCascade() {
    const container = document.getElementById('narrative-cascade-container');
    if (!container) return;

    const cards    = Array.from(container.querySelectorAll('.narrative-img-card'));
    const hitZones = Array.from(container.querySelectorAll('.hit-zone'));
    if (!cards.length) return;

    function bringPosToTop(targetPos) {
        // Find whichever card is currently sitting at that visual position
        const targetCard = cards.find(c => c.getAttribute('data-pos') === String(targetPos));
        if (!targetCard) return;
        const hoveredIndex = cards.indexOf(targetCard);
        cards.forEach((card, index) => {
            const relPos = (index - hoveredIndex + cards.length) % cards.length;
            card.setAttribute('data-pos', String(relPos));
        });
    }

    // Hit-zones sit above the cards (z-50/40/30) so they receive mouse events
    hitZones.forEach(zone => {
        zone.addEventListener('mouseenter', () => {
            const zonePos = parseInt(zone.getAttribute('data-zone'), 10);
            bringPosToTop(zonePos);
        });
    });

    // Pin the left column while the right text scrolls
    if (typeof ScrollTrigger !== 'undefined' && window.innerWidth > 768) {
        const pinTarget = document.querySelector('.narrative-pin-target');
        ScrollTrigger.create({
            trigger: '#narrative-left-col',
            pin: pinTarget,
            start: 'top 85px',
            // Release pin when the bottom of the column hits the bottom of the pinned element
            end: () => `bottom ${85 + pinTarget.offsetHeight}px`,
            pinSpacing: false
        });
    }
}

function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal-hidden, .reveal-scale-hidden');
    if (!revealElements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealElements.forEach(el => observer.observe(el));
}

function initPlannerSteps() {
    const form = document.getElementById('travelPlannerForm');
    if (!form) return;

    const steps = form.querySelectorAll('.planner-steps .step');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => step.classList.toggle('active', i === index));
        if (prevBtn) prevBtn.classList.toggle('planner-step-hidden', index === 0);
        if (nextBtn) nextBtn.classList.toggle('planner-step-hidden', index === steps.length - 1);
        if (submitBtn) submitBtn.classList.toggle('planner-step-hidden', index !== steps.length - 1);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    showStep(0);
}

function bindDestinationEvents() {
    document.addEventListener('click', (e) => {
        // Open Modal (generic)
        const openBtn = e.target.closest('[data-action="open-modal"]');
        if (openBtn) {
            e.preventDefault();
            const targetSelector = openBtn.getAttribute('data-target');
            if (targetSelector) {
                const target = document.querySelector(targetSelector);
                if (target) {
                    target.classList.add('is-active');
                    target.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('scroll-lock');
                }
            }
            return;
        }

        // Open story-modal
        const openStoryBtn = e.target.closest('[data-action="open-story-modal"]');
        if (openStoryBtn) {
            e.preventDefault();
            const storyModal = document.getElementById('story-modal');
            if (storyModal) {
                storyModal.classList.add('is-active');
                storyModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('scroll-lock');
            }
            return;
        }

        // Close Modal (any variant)
        const closeBtn = e.target.closest('[data-action="close-modal"], [data-action="close-planner"], .modal-close');
        if (closeBtn) {
            e.preventDefault();
            const modal = closeBtn.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('is-active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }

        // Close story-modal
        const closeStoryBtn = e.target.closest('[data-action="close-story-modal"]');
        if (closeStoryBtn) {
            e.preventDefault();
            const storyModal = document.getElementById('story-modal');
            if (storyModal) {
                storyModal.classList.remove('is-active');
                storyModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
        }
    });

    // Close modals on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('is-active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.is-active').forEach(m => {
                m.classList.remove('is-active');
                m.setAttribute('aria-hidden', 'true');
            });
            document.body.classList.remove('scroll-lock');
            const storyModal = document.getElementById('story-modal');
            if (storyModal && storyModal.classList.contains('is-active')) {
                storyModal.classList.remove('is-active');
                storyModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
        }
    });

    // Travel Date UX — convert text to date on focus
    const dateInputs = document.querySelectorAll('input[placeholder*="Date"][type="text"]');
    dateInputs.forEach(input => {
        input.addEventListener('focus', () => { input.type = 'date'; });
        input.addEventListener('blur', () => { if (!input.value) input.type = 'text'; });
    });

    // Focus Trap (Rule 10)
    const focusableSelector = 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Tab') return;
        const activeModal = document.querySelector('.modal-overlay.is-active, #story-modal.is-active');
        if (!activeModal) return;
        const focusable = activeModal.querySelectorAll(focusableSelector);
        if (focusable.length === 0) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }
    initDestinationHeroParallax();
    initNarrativeCardsCascade();
    initScrollReveal();
    initPlannerSteps();
    bindDestinationEvents();
});

window.addEventListener('load', () => {
    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
});
})();
