'use strict';

/**
 * Leisure Loop - Main Interactions Module
 * Encapsulated to prevent global scope pollution.
 */
document.addEventListener('DOMContentLoaded', () => {

    // ─── UTILITY: MODAL STATE MANAGEMENT ───
    const toggleModal = (modal, forceState) => {
        if (!modal) return;
        const isOpen = forceState !== undefined ? forceState : !modal.classList.contains('is-active');
        
        if (isOpen) {
            modal.classList.add('is-active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        } else {
            modal.classList.remove('is-active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }
    };

    // ─── UNIFIED DOCK (WHATSAPP/CALL) ───
    const dockTrigger = document.querySelector('.unified-trigger-head');
    const dock = document.getElementById('unifiedConciergeDock');

    if (dockTrigger && dock) {
        dockTrigger.addEventListener('click', () => {
            const isExpanded = dock.classList.toggle('dock-open');
            dockTrigger.setAttribute('aria-expanded', isExpanded);
        });
        
        // Close when clicking outside
        document.addEventListener('click', (event) => {
            if (!dock.contains(event.target) && dock.classList.contains('dock-open')) {
                dock.classList.remove('dock-open');
                dockTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ─── ENQUIRY MODAL LOGIC ───
    const enquiryModal = document.getElementById('enquiryModal');
    
    document.querySelectorAll('.btn-enquiry-nav, [data-target="#enquiryModal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(enquiryModal, true);
        });
    });

    document.querySelectorAll('.enquiry-modal-close').forEach(btn => {
        btn.addEventListener('click', () => toggleModal(enquiryModal, false));
    });

    // ─── PLANNER MODAL LOGIC ───
    const plannerModal = document.getElementById('plannerModal');
    
    document.querySelectorAll('[data-target="#plannerModal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(plannerModal, true);
        });
    });

    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => toggleModal(plannerModal, false));
    });

    // Close modals on ESC key (Accessibility Best Practice)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleModal(enquiryModal, false);
            toggleModal(plannerModal, false);
        }
    });

    // ─── DATE INPUT UX FIX ───
    document.querySelectorAll('.date-input').forEach(input => {
        input.addEventListener('focus', () => input.type = 'date');
        input.addEventListener('blur', () => {
            if (!input.value) input.type = 'text';
        });
    });

    // ─── INITIALIZE GSAP DRAG ───
    // Instead of assigning to window, initialize it locally based on data attributes
    const dragTracks = document.querySelectorAll('[data-gsap-drag="true"]');
    if (dragTracks.length > 0 && typeof gsap !== 'undefined') {
        dragTracks.forEach(track => initMomentumDrag(track));
    }
});

// ─── LOCAL GSAP MOMENTUM DRAG FUNCTION ───
// Kept outside DOMContentLoaded for readability, but NOT attached to window.
function initMomentumDrag(track, options = {}) {
    if (!track || track.dataset.dragInitialized) return;
    track.dataset.dragInitialized = 'true';
    
    let isDown = false, didDrag = false;
    let startX, scrollLeft, snapTimeout, lastX = 0, lastTime = 0, velocity = 0;
    
    const activeClass = options.activeClass || 'active-drag';
    const multiplier = options.multiplier || 2;

    track.addEventListener('mousedown', (e) => {
        isDown = true;
        didDrag = false;
        clearTimeout(snapTimeout);
        track.classList.add(activeClass);
        
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
        lastX = e.pageX;
        lastTime = performance.now();
        velocity = 0;
        
        if (typeof gsap !== 'undefined') gsap.killTweensOf(track);
        e.preventDefault();
    });

    const endDrag = () => {
        if (!isDown) return;
        isDown = false;
        
        if (didDrag && Math.abs(velocity) > 0.1 && typeof gsap !== 'undefined') {
            const coastDistance = -velocity * 300;
            gsap.to(track, {
                scrollTo: { x: track.scrollLeft + coastDistance },
                duration: 0.8,
                ease: "power2.out",
                onComplete: () => track.classList.remove(activeClass)
            });
        } else {
            track.classList.remove(activeClass);
        }
    };

    track.addEventListener('mouseleave', endDrag);
    track.addEventListener('mouseup', endDrag);
    track.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        
        const x = e.pageX - track.offsetLeft;
        const dt = performance.now() - lastTime;
        
        if (dt > 0) {
            velocity = (e.pageX - lastX) / dt;
            lastX = e.pageX;
            lastTime = performance.now();
        }

        const walk = (x - startX) * multiplier;
        if (Math.abs(walk) > 5) didDrag = true;
        track.scrollLeft = scrollLeft - walk;
    });

    // Prevent link clicking if the user was dragging
    track.querySelectorAll('a').forEach(el => {
        el.addEventListener('click', (e) => {
            if (didDrag) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}
