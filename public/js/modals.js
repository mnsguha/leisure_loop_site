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
        const activeModal = document.querySelector('.modal-overlay.is-active, .enquiry-modal-overlay.is-active, .cabs-modal-overlay.is-active, .info-modal.is-active');
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

    // ─── DRAGGABLE FLOATING DOCK HUB (both pills as one unit) ───
    const DRAG_THRESHOLD_PX = 6;
    const DOCK_EDGE_PAD = 8;

    const dockHub = document.querySelector('[data-drag-hub]');
    if (dockHub) {
        // Always start from CSS default on load/refresh (no sticky positions)
        try {
            localStorage.removeItem('llt_dock_pills');
        } catch (err) {
            /* storage unavailable */
        }
        dockHub.classList.remove('is-dock-float', 'is-dragging');
        dockHub.style.removeProperty('--dock-x');
        dockHub.style.removeProperty('--dock-y');

        const clampHubPos = (x, y) => {
            const w = dockHub.offsetWidth || 256;
            const h = dockHub.offsetHeight || 120;
            const maxX = Math.max(DOCK_EDGE_PAD, window.innerWidth - w - DOCK_EDGE_PAD);
            const maxY = Math.max(DOCK_EDGE_PAD, window.innerHeight - h - DOCK_EDGE_PAD);
            return {
                x: Math.min(Math.max(DOCK_EDGE_PAD, x), maxX),
                y: Math.min(Math.max(DOCK_EDGE_PAD, y), maxY)
            };
        };

        const applyHubPos = (x, y) => {
            const pos = clampHubPos(x, y);
            dockHub.classList.add('is-dock-float');
            dockHub.style.setProperty('--dock-x', pos.x + 'px');
            dockHub.style.setProperty('--dock-y', pos.y + 'px');
            return pos;
        };

        let pointerId = null;
        let startX = 0;
        let startY = 0;
        let origX = 0;
        let origY = 0;
        let dragging = false;
        let moved = false;

        dockHub.addEventListener('dragstart', (e) => {
            e.preventDefault();
        });

        dockHub.addEventListener('pointerdown', (e) => {
            if (e.button !== undefined && e.button !== 0) return;
            // Drag from either pill (or any descendant of a pill)
            if (!e.target.closest || !e.target.closest('.emt-dock-pill')) return;

            pointerId = e.pointerId;
            startX = e.clientX;
            startY = e.clientY;
            dragging = false;
            moved = false;

            const rect = dockHub.getBoundingClientRect();
            origX = rect.left;
            origY = rect.top;

            try {
                dockHub.setPointerCapture(pointerId);
            } catch (err) {
                /* capture optional */
            }
        });

        dockHub.addEventListener('pointermove', (e) => {
            if (pointerId === null || e.pointerId !== pointerId) return;

            const dx = e.clientX - startX;
            const dy = e.clientY - startY;

            if (!dragging) {
                if (Math.abs(dx) < DRAG_THRESHOLD_PX && Math.abs(dy) < DRAG_THRESHOLD_PX) {
                    return;
                }
                dragging = true;
                moved = true;
                dockHub.classList.add('is-dragging');
            }

            e.preventDefault();
            applyHubPos(origX + dx, origY + dy);
        });

        const endPointer = (e) => {
            if (pointerId === null || (e && e.pointerId !== undefined && e.pointerId !== pointerId)) return;

            if (dragging) {
                dockHub.classList.remove('is-dragging');
                const styles = getComputedStyle(dockHub);
                const x = parseFloat(styles.getPropertyValue('--dock-x')) || 0;
                const y = parseFloat(styles.getPropertyValue('--dock-y')) || 0;
                applyHubPos(x, y);
            }

            try {
                if (pointerId !== null) dockHub.releasePointerCapture(pointerId);
            } catch (err) {
                /* already released */
            }
            pointerId = null;
            dragging = false;
        };

        dockHub.addEventListener('pointerup', endPointer);
        dockHub.addEventListener('pointercancel', endPointer);

        dockHub.addEventListener('click', (e) => {
            if (moved) {
                e.preventDefault();
                e.stopPropagation();
                moved = false;
            }
        }, true);

        window.addEventListener('resize', () => {
            if (!dockHub.classList.contains('is-dock-float')) return;
            const styles = getComputedStyle(dockHub);
            const x = parseFloat(styles.getPropertyValue('--dock-x'));
            const y = parseFloat(styles.getPropertyValue('--dock-y'));
            if (Number.isNaN(x) || Number.isNaN(y)) return;
            applyHubPos(x, y);
        });
    }

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
