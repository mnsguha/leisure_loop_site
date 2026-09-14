'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Generic Drag-to-Scroll Helper ─────────────────────────────
    function enableDragToScroll(track) {
        if (!track) return;

        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;
        let isDragging = false;

        track.addEventListener('mousedown', (e) => {
            isDown = true;
            isDragging = false;
            track.classList.add('active-drag');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        });

        track.addEventListener('mouseleave', () => {
            if (!isDown) return;
            isDown = false;
            track.classList.remove('active-drag');
        });

        track.addEventListener('mouseup', () => {
            if (!isDown) return;
            isDown = false;
            track.classList.remove('active-drag');
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            if (Math.abs(walk) > 5) isDragging = true;
            track.scrollLeft = scrollLeft - walk;
        });

        track.addEventListener('click', (e) => {
            if (isDragging) {
                e.preventDefault();
                e.stopPropagation();
                isDragging = false;
            }
        }, true);
    }

    // Initialize all scroll tracks on the page
    document.querySelectorAll('.events-cards-track').forEach(track => {
        enableDragToScroll(track);
    });

    // ── 2. Arrow Controls Delegation ─────────────────────────────────
    document.addEventListener('click', (e) => {
        const navBtn = e.target.closest('[data-action="scroll-track"]');
        if (navBtn) {
            e.preventDefault();
            const targetId = navBtn.getAttribute('data-target');
            const dir = parseInt(navBtn.getAttribute('data-dir')) || 1;
            const track = document.getElementById(targetId);
            if (track) {
                const firstChild = track.firstElementChild;
                const step = (firstChild ? firstChild.offsetWidth : 280) + 24;
                track.scrollBy({ left: dir * step * 2, behavior: 'smooth' });
            }
            return;
        }

        // Open Event Quote Modal
        const quoteBtn = e.target.closest('[data-action="open-event-quote"]');
        if (quoteBtn) {
            e.preventDefault();
            const eventType = quoteBtn.getAttribute('data-event-type') || 'Signature Occasion';
            const venueName = quoteBtn.getAttribute('data-venue') || '';
            const modal = document.getElementById('eventsQuoteModal');

            if (modal) {
                const titleEl = document.getElementById('eventModalTitle');
                const subEl = document.getElementById('eventModalSubtitle');
                const typeInput = document.getElementById('eventInputType');
                const venueInput = document.getElementById('eventInputVenue');

                if (titleEl) titleEl.innerText = `Plan Your ${eventType}`;
                if (subEl) subEl.innerText = venueName ? `Curated experience at ${venueName}` : 'Bespoke event planning & luxury accommodations';
                if (typeInput) typeInput.value = eventType;
                if (venueInput) venueInput.value = venueName;

                modal.classList.add('is-active');
            }
            return;
        }

        // Close Modal
        const closeBtn = e.target.closest('[data-action="close-modal"]');
        if (closeBtn) {
            e.preventDefault();
            const target = closeBtn.getAttribute('data-target');
            const modal = target ? document.querySelector(target) : closeBtn.closest('.events-modal-overlay');
            if (modal) modal.classList.remove('is-active');
            return;
        }

        if (e.target.classList.contains('events-modal-overlay')) {
            e.target.classList.remove('is-active');
        }
    });

    // ── 3. Event Lead Submission ─────────────────────────────────────
    const eventForm = document.getElementById('eventsQuoteForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('eventSubmitBtn');
            const msg = document.getElementById('eventModalMsg');

            if (btn) {
                btn.innerText = 'Submitting Request...';
                btn.disabled = true;
            }

            const formData = new FormData(this);
            const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            const leadEndpoint = basePath ? `${basePath}/api/v1/leads` : '/api/v1/leads';

            fetch(leadEndpoint, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (msg) {
                        msg.className = 'events-feedback-msg is-success is-active';
                        msg.innerText = 'Thank you! Our celebration curator will reach out within 24 hours.';
                    }
                    setTimeout(() => {
                        const modal = document.getElementById('eventsQuoteModal');
                        if (modal) modal.classList.remove('is-active');
                        eventForm.reset();
                        if (btn) {
                            btn.innerText = 'Submit Celebration Request';
                            btn.disabled = false;
                        }
                        if (msg) msg.className = 'events-feedback-msg';
                    }, 2500);
                } else {
                    if (msg) {
                        msg.className = 'events-feedback-msg is-error is-active';
                        msg.innerText = data.message || 'Unable to submit request. Please try again.';
                    }
                    if (btn) {
                        btn.innerText = 'Submit Celebration Request';
                        btn.disabled = false;
                    }
                }
            })
            .catch(() => {
                if (msg) {
                    msg.className = 'events-feedback-msg is-error is-active';
                    msg.innerText = 'Network error. Please try again shortly.';
                }
                if (btn) {
                    btn.innerText = 'Submit Celebration Request';
                    btn.disabled = false;
                }
            });
        });
    }
});
