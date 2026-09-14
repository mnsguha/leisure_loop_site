'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 0. Component Query Guard (Rule 11) ───────────────────────────
    const getEl = (id) => document.getElementById('desktop-' + id) || document.getElementById(id);

    // ── 1. Tab Switching (Oneway / Hourly / Itinerary) ────────────────
    const tabs = document.querySelectorAll('.search-tab');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetId = tab.getAttribute('data-target');
            const targetPane = getEl('tab-' + targetId) || document.getElementById('tab-' + targetId);

            if (!targetPane) return;

            tabs.forEach(t => t.classList.remove('is-active'));
            panes.forEach(p => p.classList.remove('is-active'));

            tab.classList.add('is-active');
            targetPane.classList.add('is-active');
        });
    });

    // ── 2. Location Swap & Modal Controls Delegation ─────────────────
    document.addEventListener('click', (e) => {
        const swapBtn = e.target.closest('[data-action="swap-locations"]');
        if (swapBtn) {
            e.preventDefault();
            const form = swapBtn.closest('form');
            if (form) {
                const pickupInput = form.querySelector('input[name="pickup_location"]');
                const dropInput = form.querySelector('input[name="drop_location"]');
                if (pickupInput && dropInput) {
                    const temp = pickupInput.value;
                    pickupInput.value = dropInput.value;
                    dropInput.value = temp;
                }
            }
            return;
        }

        const closeModalBtn = e.target.closest('[data-action="close-modal"]');
        if (closeModalBtn) {
            e.preventDefault();
            const targetSelector = closeModalBtn.getAttribute('data-target');
            const modal = targetSelector ? document.querySelector(targetSelector) : closeModalBtn.closest('.cabs-modal-overlay');
            if (modal) {
                modal.classList.remove('is-active');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }

        if (e.target.classList.contains('cabs-modal-overlay')) {
            e.target.classList.remove('is-active');
            document.body.classList.remove('scroll-lock');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = getEl('contactModal');
            if (modal) {
                modal.classList.remove('is-active');
                document.body.classList.remove('scroll-lock');
            }
        }
    });

    // ── 3. Contact Lead Form Submission ──────────────────────────────
    const finalForm = getEl('finalCabSubmitForm');
    if (finalForm) {
        finalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = getEl('modalSubmitBtn');
            const msg = getEl('modalMsg');

            if (btn) {
                btn.innerText = 'Submitting...';
                btn.disabled = true;
            }

            const formData = new FormData(this);
            fetch('/api/v1/leads', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (msg) {
                        msg.className = 'form-feedback-msg is-success is-active';
                        msg.innerText = 'Thank you! A travel curator will contact you shortly.';
                    }
                    setTimeout(() => {
                        const modal = getEl('contactModal');
                        if (modal) {
                            modal.classList.remove('is-active');
                            document.body.classList.remove('scroll-lock');
                        }
                        finalForm.reset();
                        finalForm.querySelectorAll('.injected-search-field').forEach(el => el.remove());
                        if (btn) {
                            btn.innerText = 'CONFIRM & SUBMIT';
                            btn.disabled = false;
                        }
                        if (msg) msg.className = 'form-feedback-msg';
                    }, 2500);
                } else {
                    if (msg) {
                        msg.className = 'form-feedback-msg is-error is-active';
                        msg.innerText = data.message || 'Submission failed. Please try again.';
                    }
                    if (btn) {
                        btn.innerText = 'CONFIRM & SUBMIT';
                        btn.disabled = false;
                    }
                }
            })
            .catch(() => {
                if (msg) {
                    msg.className = 'form-feedback-msg is-error is-active';
                    msg.innerText = 'Network error. Please try again.';
                }
                if (btn) {
                    btn.innerText = 'CONFIRM & SUBMIT';
                    btn.disabled = false;
                }
            })
            .finally(() => {
                finalForm.querySelectorAll('.injected-search-field').forEach(el => el.remove());
            });
        });
    }

    // ── 4. Itinerary Date Synchronization ────────────────────────────
    const itinStart = document.getElementById('desktop-itinerary-start');
    const itinEnd = document.getElementById('desktop-itinerary-end');

    if (itinStart && itinEnd) {
        itinStart.addEventListener('change', () => {
            itinEnd.min = itinStart.value;
            if (itinEnd.value && itinEnd.value < itinStart.value) {
                itinEnd.value = itinStart.value;
            }
        });
    }
});
