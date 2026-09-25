'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 0. Component Query Guard (Rule 11 §60) ──────────────────────
    const getEl = (id) => document.getElementById('desktop-' + id) || document.getElementById(id);

    // ── 1. State Management ──────────────────────────────────────────
    const checkInInput = getEl('inputCheckIn');
    const checkOutInput = getEl('inputCheckOut');
    const roomsInput = getEl('inputRooms');
    const adultsInput = getEl('inputAdults');
    const childrenInput = getEl('inputChildren');
    const infantsInput = getEl('inputInfants');

    const searchState = {
        dates: {
            start: checkInInput ? checkInInput.value : '',
            end: checkOutInput ? checkOutInput.value : ''
        },
        rooms: parseInt(roomsInput ? roomsInput.value : 1) || 1,
        adults: parseInt(adultsInput ? adultsInput.value : 2) || 2,
        children: parseInt(childrenInput ? childrenInput.value : 0) || 0,
        infants: parseInt(infantsInput ? infantsInput.value : 0) || 0
    };

    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', () => {
            let startD = new Date(checkInInput.value);
            if (!isNaN(startD.getTime())) {
                startD.setDate(startD.getDate() + 1);
                let nextDay = startD.toISOString().split('T')[0];
                checkOutInput.min = nextDay;
                if (checkOutInput.value < nextDay) {
                    checkOutInput.value = nextDay;
                    searchState.dates.end = nextDay;
                }
                searchState.dates.start = checkInInput.value;
            }
        });
        checkOutInput.addEventListener('change', () => {
            searchState.dates.end = checkOutInput.value;
        });
    }

    // ── 2. Flatpickr Date Picker ─────────────────────────────────────
    const displayDates = getEl('displayDates');
    if (displayDates && typeof flatpickr !== 'undefined') {
        flatpickr(displayDates, {
            mode: 'range',
            minDate: 'today',
            dateFormat: 'M d, Y',
            defaultDate: [searchState.dates.start, searchState.dates.end],
            onClose: function(selectedDates) {
                if (selectedDates.length === 2) {
                    const startStr = selectedDates[0].toISOString().split('T')[0];
                    const endStr = selectedDates[1].toISOString().split('T')[0];
                    searchState.dates.start = startStr;
                    searchState.dates.end = endStr;
                    if (checkInInput) checkInInput.value = startStr;
                    if (checkOutInput) checkOutInput.value = endStr;
                }
            }
        });
    }

    // ── 3. Guests Dropdown & Counters ────────────────────────────────
    const guestsContainer = getEl('guestsContainer');
    const guestsDropdown = getEl('guestsDropdown');
    const displayGuests = getEl('displayGuests');
    const btnApplyGuests = getEl('btnApplyGuests');

    if (guestsContainer && guestsDropdown) {
        guestsContainer.addEventListener('click', (e) => {
            if (!guestsDropdown.contains(e.target)) {
                guestsDropdown.classList.toggle('is-active');
                guestsDropdown.classList.toggle('active');
            }
        });

        document.addEventListener('click', (e) => {
            if (!guestsContainer.contains(e.target)) {
                guestsDropdown.classList.remove('is-active');
                guestsDropdown.classList.remove('active');
            }
        });
    }

    function setupCounter(minusId, plusId, valId, min, max, stateKey, inputEl) {
        const minusBtn = getEl(minusId);
        const plusBtn = getEl(plusId);

        if (minusBtn) {
            minusBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (searchState[stateKey] > min) {
                    searchState[stateKey]--;
                    if (inputEl) inputEl.value = searchState[stateKey];
                    updateCounters();
                }
            });
        }
        if (plusBtn) {
            plusBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (searchState[stateKey] < max) {
                    searchState[stateKey]++;
                    if (inputEl) inputEl.value = searchState[stateKey];
                    updateCounters();
                }
            });
        }
    }

    setupCounter('btnRoomsMinus', 'btnRoomsPlus', 'valRooms', 1, 10, 'rooms', roomsInput);
    setupCounter('btnAdultsMinus', 'btnAdultsPlus', 'valAdults', 1, 30, 'adults', adultsInput);
    setupCounter('btnChildrenMinus', 'btnChildrenPlus', 'valChildren', 0, 10, 'children', childrenInput);
    setupCounter('btnInfantsMinus', 'btnInfantsPlus', 'valInfants', 0, 10, 'infants', infantsInput);

    function updateCounters() {
        const setVal = (id, v) => { const el = getEl(id); if (el) el.innerText = v; };
        setVal('valRooms', searchState.rooms);
        setVal('valAdults', searchState.adults);
        setVal('valChildren', searchState.children);
        setVal('valInfants', searchState.infants);

        const setDis = (id, cond) => { const el = getEl(id); if (el) el.disabled = cond; };
        setDis('btnRoomsMinus', searchState.rooms <= 1);
        setDis('btnAdultsMinus', searchState.adults <= 1);
        setDis('btnChildrenMinus', searchState.children <= 0);
        setDis('btnInfantsMinus', searchState.infants <= 0);
    }

    function updateGuestDisplay() {
        if (!displayGuests) return;
        let childText = searchState.children > 0 ? `, ${searchState.children} Child${searchState.children > 1 ? 'ren' : ''}` : '';
        let infantText = searchState.infants > 0 ? `, ${searchState.infants} Infant${searchState.infants > 1 ? 's' : ''}` : '';
        displayGuests.value = `${searchState.rooms} Room${searchState.rooms > 1 ? 's' : ''}, ${searchState.adults} Adult${searchState.adults > 1 ? 's' : ''}${childText}${infantText}`;
    }

    if (btnApplyGuests) {
        btnApplyGuests.addEventListener('click', (e) => {
            e.stopPropagation();
            updateGuestDisplay();
            if (guestsDropdown) {
                guestsDropdown.classList.remove('is-active');
                guestsDropdown.classList.remove('active');
            }
        });
    }

    // ── 4. Category Filter Buttons ──────────────────────────────────────
    const filterButtons = document.querySelectorAll('.hotel-filters .filter-btn');
    const hotelCards = document.querySelectorAll('.hotel-list .hotel-card');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active', 'is-active'));
            this.classList.add('active', 'is-active');

            const filterValue = this.getAttribute('data-filter');
            hotelCards.forEach(card => {
                const category = card.getAttribute('data-category');
                if (filterValue === 'all' || category === filterValue) {
                    card.classList.remove('is-hidden');
                } else {
                    card.classList.add('is-hidden');
                }
            });
        });
    });

    // ── 5. Info Modals Delegation (Rule 10 Compliant) ───────────────────
    document.addEventListener('click', (e) => {
        const openBtn = e.target.closest('[data-action="open-info-modal"]');
        if (openBtn) {
            e.preventDefault();
            e.stopPropagation();
            const hotelName = openBtn.getAttribute('data-hotel-name');
            const modalTitle = openBtn.getAttribute('data-modal-title');
            const contentId = openBtn.getAttribute('data-content-id');
            const contentEl = document.getElementById(contentId);
            const modal = getEl('infoModal');

            if (contentEl && modal) {
                const titleEl = getEl('infoModalTitle');
                const subEl = getEl('infoModalSubtitle');
                const bodyEl = getEl('infoModalBody');

                if (titleEl) titleEl.innerText = hotelName || '';
                if (subEl) subEl.innerText = modalTitle || '';
                if (bodyEl) bodyEl.innerHTML = contentEl.innerHTML || '';

                modal.classList.add('is-active', 'active');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('scroll-lock');
            }
            return;
        }

        const closeBtn = e.target.closest('[data-action="close-info-modal"]');
        if (closeBtn) {
            e.preventDefault();
            const modal = getEl('infoModal');
            if (modal) {
                modal.classList.remove('is-active', 'active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }

        const modal = getEl('infoModal');
        if (e.target === modal) {
            modal.classList.remove('is-active', 'active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('scroll-lock');
        }
    });

    // Close info modal on ESC (Rule 10)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = getEl('infoModal');
            if (modal && (modal.classList.contains('is-active') || modal.classList.contains('active'))) {
                modal.classList.remove('is-active', 'active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('scroll-lock');
            }
        }
    });
});
