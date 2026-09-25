(function() {
    if (document.body.dataset.mobileViewsInit) return;
    document.body.dataset.mobileViewsInit = 'true';

    // --- 0. data-bg Hydration (Rule 1: no inline style="background-image") ---
    document.querySelectorAll('[data-bg]').forEach(function(el) {
        el.style.backgroundImage = 'url(' + el.dataset.bg + ')';
    });
    document.querySelectorAll('[data-sz]').forEach(function(el) {
        el.style.setProperty('--sz', el.dataset.sz + 'px');
        el.style.setProperty('--top', el.dataset.top + '%');
        el.style.setProperty('--left', el.dataset.left + '%');
    });
    document.querySelectorAll('[data-badge-bg]').forEach(function(el) {
        el.style.setProperty('--badge-bg', el.dataset.badgeBg);
        el.style.setProperty('--badge-color', el.dataset.badgeColor);
    });
    document.querySelectorAll('[data-rotation]').forEach(function(el) {
        el.style.setProperty('--rotation', el.dataset.rotation + 'deg');
    });

    // --- 1. Centralized Event Delegation & Hardware Back Sync ---
    const activeModals = [];

    function pushModalState(modalId) {
        history.pushState({ modal: modalId }, '', `#${modalId}`);
        activeModals.push(modalId);
    }

    function popModalState() {
        if (activeModals.length > 0) {
            history.back();
        }
    }

    window.addEventListener('popstate', () => {
        if (activeModals.length > 0) {
            const modalId = activeModals.pop();
            const modal = document.getElementById(modalId) || document.querySelector(`.${modalId}`);
            if (modal) {
                modal.classList.remove('active', 'is-active');
                if (modalId === 'mobileMenuOverlay' || modalId === 'mobileMenuPanel') {
                    const panel = document.getElementById('mobileMenuPanel');
                    const overlay = document.getElementById('mobileMenuOverlay');
                    if (panel) panel.classList.remove('active', 'is-active');
                    if (overlay) overlay.classList.remove('active', 'is-active');
                }
            } else {
                document.querySelectorAll('.active, .is-active').forEach(el => {
                    el.classList.remove('active', 'is-active');
                });
            }
            document.body.classList.remove('scroll-lock');
        }
    });

    // MakeMyTrip Guest Counter Utilities (Single Canonical Definition)
    const valRooms = document.getElementById('mValRooms');
    const inputRooms = document.getElementById('mInputRooms');
    const valAdults = document.getElementById('mValAdults');
    const inputAdults = document.getElementById('mInputAdults');
    const valChildren = document.getElementById('mValChildren');
    const inputChildren = document.getElementById('mInputChildren');
    const guestsSummary = document.getElementById('mDisplayGuestsSummary');

    function updateCounters(delta, min, max, valEl, inputEl) {
        if (!valEl) return;
        const current = parseInt(valEl.innerText) || 0;
        const next = current + delta;
        if (next >= min && next <= max) {
            valEl.innerText = next;
            if (inputEl) inputEl.value = next;
        }
    }

    function updateGuestsSummary() {
        if (!guestsSummary) return;
        const r = parseInt(valRooms?.innerText || '1');
        const a = parseInt(valAdults?.innerText || '2');
        const c = parseInt(valChildren?.innerText || '0');
        guestsSummary.innerText = `${r} Room${r > 1 ? 's' : ''}, ${a} Adult${a > 1 ? 's' : ''}${c > 0 ? ', ' + c + ' Child' : ''}`;
    }

    document.addEventListener('click', (e) => {
        const actionEl = e.target.closest('[data-action], [data-href]');
        
        if (actionEl && actionEl.hasAttribute('data-href')) {
            window.location.href = actionEl.getAttribute('data-href');
            return;
        }

        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');
        
        const openModal = (selector, modalId) => {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.add('active', 'is-active');
                document.body.classList.add('scroll-lock');
                pushModalState(modalId);
            }
        };

        const closeModal = (selector) => {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.remove('active', 'is-active');
                document.body.classList.remove('scroll-lock');
                if (activeModals.length > 0) popModalState();
            }
        };

        const closeAll = () => {
            document.querySelectorAll('.active, .is-active').forEach(el => el.classList.remove('active', 'is-active'));
            document.body.classList.remove('scroll-lock');
            if (activeModals.length > 0) popModalState();
        };

        // Navigation Drawer
        if (action === 'open-mobile-menu') {
            const overlay = document.getElementById('mobileMenuOverlay');
            const panel = document.getElementById('mobileMenuPanel');
            if (overlay) overlay.classList.add('is-active', 'active');
            if (panel) panel.classList.add('is-active', 'active');
            document.body.classList.add('scroll-lock');
            pushModalState('mobileMenuPanel');
        } else if (action === 'close-mobile-menu') {
            const overlay = document.getElementById('mobileMenuOverlay');
            const panel = document.getElementById('mobileMenuPanel');
            if (overlay) overlay.classList.remove('is-active', 'active');
            if (panel) panel.classList.remove('is-active', 'active');
            document.body.classList.remove('scroll-lock');
            if (activeModals.length > 0) popModalState();
        } 
        // Modals
        else if (action === 'open-modal' || action === 'open-enquiry-modal') {
            document.getElementById('enquiryModalOverlay')?.classList.add('active', 'is-active');
            openModal('#enquiryModal', 'enquiryModal');
        } else if (action === 'close-modal') {
            document.getElementById('enquiryModalOverlay')?.classList.remove('active', 'is-active');
            closeModal('#enquiryModal');
        } else if (action === 'close-all') {
            closeAll();
        } 
        // Hotel Guest Bottom Sheet (Strict Single Listener Execution)
        else if (action === 'open-guest-modal') {
            document.getElementById('mHotelGuestModalOverlay')?.classList.add('active', 'is-active');
            const sheet = document.getElementById('mHotelGuestModal');
            if (sheet) sheet.classList.add('active', 'is-active');
            document.body.classList.add('scroll-lock');
            pushModalState('mHotelGuestModal');
        } else if (action === 'close-guest-modal' || action === 'apply-guests-modal') {
            if (action === 'apply-guests-modal') updateGuestsSummary();
            document.getElementById('mHotelGuestModalOverlay')?.classList.remove('active', 'is-active');
            const sheet = document.getElementById('mHotelGuestModal');
            if (sheet) sheet.classList.remove('active', 'is-active');
            document.body.classList.remove('scroll-lock');
            if (activeModals.length > 0) popModalState();
        } 
        // Guest Counters (Exactly 1 Increment per Click)
        else if (action === 'inc-rooms') updateCounters(1, 1, 10, valRooms, inputRooms);
        else if (action === 'dec-rooms') updateCounters(-1, 1, 10, valRooms, inputRooms);
        else if (action === 'inc-adults') updateCounters(1, 1, 30, valAdults, inputAdults);
        else if (action === 'dec-adults') updateCounters(-1, 1, 30, valAdults, inputAdults);
        else if (action === 'inc-children') updateCounters(1, 0, 10, valChildren, inputChildren);
        else if (action === 'dec-children') updateCounters(-1, 0, 10, valChildren, inputChildren);
        // Hotel Listing Category Filter
        else if (action === 'filter-hotels') {
            const cat = actionEl.getAttribute('data-category');
            document.querySelectorAll('.m-hotel-filter-btn').forEach(b => b.classList.remove('active'));
            actionEl.classList.add('active');
            document.querySelectorAll('.m-hotel-card').forEach(card => {
                if (cat === 'all' || card.getAttribute('data-category') === cat) {
                    card.classList.remove('is-hidden');
                } else {
                    card.classList.add('is-hidden');
                }
            });
        }
        // Accordion Toggles
        else if (action === 'toggle-accordion') {
            actionEl.closest('.accordion-item')?.classList.toggle('active');
        }
        // Room Selection
        else if (action === 'select-room') {
            const idx = actionEl.getAttribute('data-idx');
            const title = actionEl.getAttribute('data-title');
            const display = document.getElementById('mSelectedRoomDisplay');
            const nameEl = document.getElementById('mSelectedRoomName');
            const roomInput = document.getElementById('mFormRoomId');
            if (display && nameEl && roomInput) {
                nameEl.textContent = title;
                roomInput.value = idx;
                display.classList.remove('is-hidden', 'hidden');
                document.getElementById('bookingFormSection')?.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

    // --- 2. Centralized Destinations Search & Badge Filter (Rule 1 Compliance) ---
    const destInput = document.getElementById('destSearchInput');
    const destClear = document.getElementById('destClearSearch');
    const destCards = Array.from(document.querySelectorAll('#destGrid .js-card'));
    const destNoResults = document.getElementById('noDestResults');
    let activeDestFilter = 'all';

    function filterDestinations() {
        const q = (destInput?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        destCards.forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const cat = (card.dataset.category || '').toLowerCase();
            const matchesSearch = !q || name.includes(q);
            const matchesCategory = (activeDestFilter === 'all') || (cat === activeDestFilter);

            if (matchesSearch && matchesCategory) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (destNoResults) {
            destNoResults.classList.toggle('hidden', visibleCount > 0);
        }
    }

    if (destInput) {
        destInput.addEventListener('input', () => {
            if (destClear) destClear.classList.toggle('hidden', !destInput.value);
            filterDestinations();
        });
    }

    if (destClear) {
        destClear.addEventListener('click', () => {
            destInput.value = '';
            destClear.classList.add('hidden');
            filterDestinations();
        });
    }

    document.addEventListener('click', (e) => {
        const badge = e.target.closest('.m-filter-badge');
        if (!badge) return;

        document.querySelectorAll('.m-filter-badge').forEach(b => b.classList.remove('active'));
        badge.classList.add('active');

        if (badge.dataset.filter) {
            activeDestFilter = badge.dataset.filter;
        } else if (badge.dataset.sort === 'popular') {
            const grid = document.getElementById('destGrid');
            destCards.sort((a, b) => parseInt(a.dataset.order || '0') - parseInt(b.dataset.order || '0'));
            destCards.forEach(c => grid?.appendChild(c));
        }
        filterDestinations();
    });

    // --- 3. Hotel Dates & Nights Reactive Sync ---
    function updateMobileHotelDates() {
        const checkIn = document.getElementById('mCheckInDate');
        const checkOut = document.getElementById('mCheckOutDate');
        const inDay = document.getElementById('mDisplayCheckInDay');
        const inYr = document.getElementById('mDisplayCheckInYear');
        const outDay = document.getElementById('mDisplayCheckOutDay');
        const outYr = document.getElementById('mDisplayCheckOutYear');
        const nights = document.getElementById('mDisplayNightsPill');

        if (!checkIn || !checkOut || !inDay || !outDay || !nights) return;

        const pIn = checkIn.value.split('-');
        const pOut = checkOut.value.split('-');
        if (pIn.length !== 3 || pOut.length !== 3) return;

        const dIn = new Date(parseInt(pIn[0]), parseInt(pIn[1]) - 1, parseInt(pIn[2]));
        const dOut = new Date(parseInt(pOut[0]), parseInt(pOut[1]) - 1, parseInt(pOut[2]));

        if (isNaN(dIn.getTime()) || isNaN(dOut.getTime())) return;

        if (dOut <= dIn) {
            dOut.setTime(dIn.getTime() + 86400000);
            const m = String(dOut.getMonth() + 1).padStart(2, '0');
            const d = String(dOut.getDate()).padStart(2, '0');
            checkOut.value = `${dOut.getFullYear()}-${m}-${d}`;
        }

        const mNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        const getOrd = (n) => { const s=["th","st","nd","rd"], v=n%100; return n+(s[(v-20)%10]||s[v]||s[0]); };

        inDay.innerText = `${getOrd(dIn.getDate())} ${mNames[dIn.getMonth()]}`;
        if (inYr) inYr.innerText = `'${String(dIn.getFullYear()).slice(2)}, ${dayNames[dIn.getDay()]}`;

        outDay.innerText = `${getOrd(dOut.getDate())} ${mNames[dOut.getMonth()]}`;
        if (outYr) outYr.innerText = `'${String(dOut.getFullYear()).slice(2)}, ${dayNames[dOut.getDay()]}`;

        const diff = Math.round((dOut - dIn) / 86400000);
        nights.innerHTML = `<span class="material-symbols-outlined">nightlight</span><span>${Math.max(1, diff)}N</span>`;
    }

    document.addEventListener('change', (e) => {
        if (e.target.id === 'mCheckInDate' || e.target.id === 'mCheckOutDate') {
            updateMobileHotelDates();
        }
    });

    document.addEventListener('focusin', (e) => {
        if (e.target.hasAttribute('data-focus')) {
            e.target.type = e.target.getAttribute('data-focus');
        }
    });

    // --- Hero Slider Logic ---
    const heroSlider = document.querySelector('.m-hotel-hero-slider');
    const heroDots = document.querySelectorAll('.m-hotel-hero-dot');
    if (heroSlider && heroDots.length > 1) {
        let currentSlide = 0;
        const totalSlides = heroDots.length;
        
        let autoPlayInterval = setInterval(nextSlide, 4000);
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            heroSlider.scrollTo({
                left: currentSlide * heroSlider.clientWidth,
                behavior: 'smooth'
            });
            updateDots(currentSlide);
        }
        
        function updateDots(index) {
            heroDots.forEach((dot, idx) => {
                dot.classList.toggle('is-active', idx === index);
            });
        }
        
        heroSlider.addEventListener('scroll', () => {
            const index = Math.round(heroSlider.scrollLeft / heroSlider.clientWidth);
            if (index !== currentSlide) {
                currentSlide = index;
                updateDots(index);
                clearInterval(autoPlayInterval);
                autoPlayInterval = setInterval(nextSlide, 4000);
            }
        });
    }

    // --- Lead Form AJAX Submit (mobile: main.js is not loaded in mobile views) ---
    const showMobileToast = (message, isError) => {
        const container = document.getElementById('mob-toastContainer');
        if (!container) return;
        const msg = document.createElement('div');
        msg.className = isError ? 'toast-msg toast-msg--error' : 'toast-msg';
        msg.textContent = message;
        container.appendChild(msg);
        setTimeout(() => msg.remove(), 4000);
    };

    document.querySelectorAll('.js-lead-form').forEach((form) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (form.dataset.leadBusy === '1') return;
            form.dataset.leadBusy = '1';
            const btn = form.querySelector('button[type="submit"]');
            const btnText = btn ? btn.textContent : '';
            if (btn) { btn.disabled = true; btn.textContent = 'Sending...'; }
            try {
                const res = await fetch(form.action, { method: 'POST', body: new FormData(form) });
                const data = await res.json();
                if (data.success) {
                    form.reset();
                    const modal = form.closest('.enquiry-modal-overlay, .modal-overlay');
                    if (modal) {
                        modal.classList.remove('is-active', 'active');
                        modal.classList.add('is-hidden');
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('scroll-lock');
                    }
                    showMobileToast(data.message || 'Thank you! Our curators will reach out to you soon.', false);
                } else {
                    showMobileToast(data.message || 'Something went wrong. Please try again.', true);
                    if (window.grecaptcha && typeof window.grecaptcha.reset === 'function') {
                        try { window.grecaptcha.reset(); } catch (err) { /* widget optional */ }
                    }
                }
            } catch (err) {
                showMobileToast('Connection issue. Please try again.', true);
            } finally {
                form.dataset.leadBusy = '0';
                if (btn) { btn.disabled = false; btn.textContent = btnText; }
            }
        });
    });
    // --- Destination Hero Auto-Slider (mobile; hotel-detail parity) ---
    const destHeroSlider = document.getElementById('mobDestHeroSlider');
    if (destHeroSlider) {
        const destHeroDots = destHeroSlider.parentElement
            ? destHeroSlider.parentElement.querySelectorAll('.mob-dest__hero-dot')
            : [];
        let destHeroTimer = null;

        const destHeroStop = () => {
            if (destHeroTimer) {
                clearInterval(destHeroTimer);
                destHeroTimer = null;
            }
        };

        const destHeroStart = () => {
            destHeroStop();
            destHeroTimer = setInterval(() => {
                const slideWidth = destHeroSlider.offsetWidth;
                if (!slideWidth) return;
                let nextScroll = destHeroSlider.scrollLeft + slideWidth;
                if (nextScroll >= destHeroSlider.scrollWidth - 10) {
                    nextScroll = 0;
                }
                destHeroSlider.scrollTo({ left: nextScroll, behavior: 'smooth' });
            }, 4000);
        };

        if (destHeroDots.length > 1) {
            destHeroSlider.addEventListener('scroll', () => {
                const slideWidth = destHeroSlider.offsetWidth;
                if (!slideWidth) return;
                const activeIndex = Math.round(destHeroSlider.scrollLeft / slideWidth);
                destHeroDots.forEach((dot, idx) => {
                    dot.classList.toggle('is-active', idx === activeIndex);
                });
            }, { passive: true });
        }

        destHeroStart();
        destHeroSlider.addEventListener('touchstart', destHeroStop, { passive: true });
        destHeroSlider.addEventListener('touchend', destHeroStart, { passive: true });
    }
})();
