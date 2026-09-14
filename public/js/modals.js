'use strict';

/**
 * Leisure Loop - Main Interactions Module
 * Rule 10 State Hygiene & Rule 11 Component Query Guarding
 */
document.addEventListener('DOMContentLoaded', () => {

    // ─── UTILITY: MODAL STATE MANAGEMENT (Rule 10 Compliant) ───
    const toggleModal = (modal, forceState) => {
        if (!modal) return;
        const isOpen = forceState !== undefined 
            ? forceState 
            : (modal.classList.contains('is-hidden') || !modal.classList.contains('is-active'));
        
        if (isOpen) {
            modal.classList.remove('is-hidden');
            modal.classList.add('is-active', 'active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('scroll-lock');
        } else {
            modal.classList.add('is-hidden');
            modal.classList.remove('is-active', 'active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('scroll-lock');
        }
    };

    // ─── UNIFIED DOCK (WHATSAPP/CALL) (Rule 11 Guarded) ───
    const dockTrigger = document.querySelector('.unified-trigger-head');
    const dock = document.getElementById('mob-unifiedConciergeDock') || document.getElementById('unifiedConciergeDock');

    if (dockTrigger && dock) {
        dockTrigger.addEventListener('click', () => {
            const isExpanded = dock.classList.toggle('dock-open');
            dockTrigger.setAttribute('aria-expanded', isExpanded);
        });
        
        document.addEventListener('click', (event) => {
            if (!dock.contains(event.target) && dock.classList.contains('dock-open')) {
                dock.classList.remove('dock-open');
                dockTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ─── ENQUIRY MODAL LOGIC (Rule 11 §60 Guarded Lookup) ───
    const getEnquiryModal = () => document.getElementById('mob-enquiryModal') || document.getElementById('enquiryModal');
    
    document.querySelectorAll('.btn-enquiry-nav, [data-target="#enquiryModal"], [data-target="#mob-enquiryModal"], [data-action="open-enquiry-modal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(getEnquiryModal(), true);
        });
    });

    document.querySelectorAll('.enquiry-modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(getEnquiryModal(), false);
        });
    });

    // ─── PLANNER MODAL LOGIC (Rule 11 §60 Guarded Lookup) ───
    const getPlannerModal = () => document.getElementById('mob-plannerModal') || document.getElementById('plannerModal');
    
    document.querySelectorAll('[data-target="#plannerModal"], [data-target="#mob-plannerModal"], [data-action="open-planner-modal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(getPlannerModal(), true);
        });
    });

    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = btn.closest('.modal-overlay, .enquiry-modal-overlay');
            if (modal) {
                e.preventDefault();
                toggleModal(modal, false);
            }
        });
    });

    // Close modals on ESC key (Rule 10)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleModal(getEnquiryModal(), false);
            toggleModal(getPlannerModal(), false);
            const step2 = document.getElementById('desktop-step2Modal') || document.getElementById('step2Modal');
            toggleModal(step2, false);
            const notice = document.getElementById('noticePopup');
            if (notice && (notice.classList.contains('is-active') || notice.classList.contains('active'))) {
                notice.classList.remove('is-active', 'active');
                notice.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
        }
    });

    // ─── FOCUS TRAP (Rule 10) ───
    const focusableSelector = 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Tab') return;
        const activeModal = document.querySelector('.modal-overlay.is-active, .enquiry-modal-overlay.is-active');
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

    // ─── DATE INPUT UX FIX ───
    document.querySelectorAll('.date-input').forEach(input => {
        input.addEventListener('focus', () => input.type = 'date');
        input.addEventListener('blur', () => {
            if (!input.value) input.type = 'text';
        });
    });

    // ─── INITIALIZE GSAP DRAG ───
    const dragTracks = document.querySelectorAll('[data-gsap-drag="true"]');
    if (dragTracks.length > 0 && typeof gsap !== 'undefined') {
        dragTracks.forEach(track => initMomentumDrag(track));
    }
});

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

    track.querySelectorAll('a').forEach(el => {
        el.addEventListener('click', (e) => {
            if (didDrag) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}
