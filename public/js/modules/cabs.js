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
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }

        if (e.target.classList.contains('cabs-modal-overlay')) {
            e.target.classList.remove('is-active');
            e.target.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('scroll-lock');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = getEl('contactModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.setAttribute('aria-hidden', 'true');
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
            fetch('api-submit-lead.php', {
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
                            modal.setAttribute('aria-hidden', 'true');
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

    // ── 5. Mobile Search Tab Logic ───────────────────────────────────
    const mTabs = document.querySelectorAll('.m-cab-tab');
    if (mTabs.length > 0) {
        const mTypeInput = document.getElementById('mCabSearchType');
        const mSearchCard = document.getElementById('mCabSearchCard');
        const mRowDrop = document.getElementById('mCabRowDrop');
        const mRowDuration = document.getElementById('mCabRowDuration');
        const mRowReturnDate = document.getElementById('mCabRowReturnDate');
        const mRowItinerary = document.getElementById('mCabRowItinerary');
        const mInputDrop = document.getElementById('mCabDrop');
        const mInputDuration = document.getElementById('mCabDuration');
        const mInputReturnDate = document.getElementById('mCabReturnDate');
        const mInputItinerary = document.getElementById('mCabItinerary');

        const applyMType = (type, activeTab) => {
            if (!type) return;

            mTabs.forEach((t) => {
                const on = t === activeTab;
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                t.tabIndex = on ? 0 : -1;
            });

            if (mTypeInput) mTypeInput.value = type;
            if (mSearchCard && activeTab) {
                mSearchCard.setAttribute('aria-labelledby', activeTab.id);
            }

            if (mRowDrop) mRowDrop.classList.add('is-hidden');
            if (mRowDuration) mRowDuration.classList.add('is-hidden');
            if (mRowReturnDate) mRowReturnDate.classList.add('is-hidden');
            if (mRowItinerary) mRowItinerary.classList.add('is-hidden');

            if (mInputDrop) { mInputDrop.disabled = true; mInputDrop.required = false; }
            if (mInputDuration) { mInputDuration.disabled = true; mInputDuration.required = false; }
            if (mInputReturnDate) { mInputReturnDate.disabled = true; mInputReturnDate.required = false; }
            if (mInputItinerary) { mInputItinerary.disabled = true; mInputItinerary.required = false; }

            if (type === 'oneway') {
                if (mRowDrop) mRowDrop.classList.remove('is-hidden');
                if (mInputDrop) { mInputDrop.disabled = false; mInputDrop.required = true; }
            } else if (type === 'hourly') {
                if (mRowDuration) mRowDuration.classList.remove('is-hidden');
                if (mInputDuration) { mInputDuration.disabled = false; mInputDuration.required = true; }
            } else if (type === 'itinerary') {
                if (mRowReturnDate) mRowReturnDate.classList.remove('is-hidden');
                if (mRowItinerary) mRowItinerary.classList.remove('is-hidden');
                if (mInputReturnDate) { mInputReturnDate.disabled = false; mInputReturnDate.required = true; }
                if (mInputItinerary) { mInputItinerary.disabled = false; mInputItinerary.required = true; }
            }
        };

        mTabs.forEach((tab, idx) => {
            tab.addEventListener('click', () => {
                applyMType(tab.getAttribute('data-type'), tab);
            });

            tab.addEventListener('keydown', (e) => {
                let next = null;
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    next = mTabs[(idx + 1) % mTabs.length];
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    next = mTabs[(idx - 1 + mTabs.length) % mTabs.length];
                } else if (e.key === 'Home') {
                    next = mTabs[0];
                } else if (e.key === 'End') {
                    next = mTabs[mTabs.length - 1];
                }
                if (next) {
                    e.preventDefault();
                    next.focus();
                    applyMType(next.getAttribute('data-type'), next);
                }
            });
        });

        const initial = document.querySelector('.m-cab-tab[aria-selected="true"]') || mTabs[0];
        applyMType(initial.getAttribute('data-type'), initial);
    }

    // ── 6. Mobile Hero Auto-Scroll (cab class images) ────────────────
    const cabHeroSlider = document.getElementById('mCabsHeroSlider');
    const cabHeroDots = document.querySelectorAll('.m-cabs-hero-dot');
    if (cabHeroSlider && cabHeroDots.length > 1) {
        let cabSlide = 0;
        const cabTotal = cabHeroDots.length;
        let cabTimer = setInterval(cabNextSlide, 4000);

        function cabNextSlide() {
            cabSlide = (cabSlide + 1) % cabTotal;
            cabHeroSlider.scrollTo({
                left: cabSlide * cabHeroSlider.clientWidth,
                behavior: 'smooth'
            });
            cabUpdateDots(cabSlide);
        }

        function cabUpdateDots(index) {
            cabHeroDots.forEach((dot, idx) => {
                dot.classList.toggle('is-active', idx === index);
            });
        }

        cabHeroSlider.addEventListener('scroll', () => {
            if (!cabHeroSlider.clientWidth) return;
            const index = Math.round(cabHeroSlider.scrollLeft / cabHeroSlider.clientWidth);
            if (index !== cabSlide) {
                cabSlide = index;
                cabUpdateDots(cabSlide);
                clearInterval(cabTimer);
                cabTimer = setInterval(cabNextSlide, 4000);
            }
        });
    }
});
