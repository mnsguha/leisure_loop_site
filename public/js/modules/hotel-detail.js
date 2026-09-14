'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 0. Component Query Guard (Rule 11 §60) ──────────────────────
    const getEl = (id) => document.getElementById('desktop-' + id) || document.getElementById(id);

    // ── 1. Read Room & Plan Dataset from DOM ─────────────────────────
    const dataStore = document.getElementById('hotel-room-data-store');
    const hotelRoomData = dataStore ? JSON.parse(dataStore.dataset.rooms || '[]') : (window.hotelRoomData || []);

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

    let currentSelectedPlan = null;
    let currentSelectedRoom = null;

    // ── 2. Flatpickr Date Range ──────────────────────────────────────
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

    if (checkInInput) {
        checkInInput.addEventListener('change', (e) => {
            searchState.dates.start = e.target.value;
            let inDt = new Date(searchState.dates.start);
            let outDt = new Date(searchState.dates.end);
            if (outDt <= inDt) {
                outDt = new Date(inDt);
                outDt.setDate(outDt.getDate() + 1);
                searchState.dates.end = outDt.toISOString().split('T')[0];
                if (checkOutInput) checkOutInput.value = searchState.dates.end;
            }
            if (typeof updateAllPlanPricesUI === 'function') updateAllPlanPricesUI();
            if (typeof updateSidebarUI === 'function' && currentSelectedPlan) updateSidebarUI();
        });
    }
    if (checkOutInput) {
        checkOutInput.addEventListener('change', (e) => {
            searchState.dates.end = e.target.value;
            let inDt = new Date(searchState.dates.start);
            let outDt = new Date(searchState.dates.end);
            if (outDt <= inDt) {
                inDt = new Date(outDt);
                inDt.setDate(inDt.getDate() - 1);
                searchState.dates.start = inDt.toISOString().split('T')[0];
                if (checkInInput) checkInInput.value = searchState.dates.start;
            }
            if (typeof updateAllPlanPricesUI === 'function') updateAllPlanPricesUI();
            if (typeof updateSidebarUI === 'function' && currentSelectedPlan) updateSidebarUI();
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
            }
        });

        document.addEventListener('click', (e) => {
            if (!guestsContainer.contains(e.target)) {
                guestsDropdown.classList.remove('is-active');
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

    if (btnApplyGuests) {
        btnApplyGuests.addEventListener('click', (e) => {
            e.stopPropagation();
            if (displayGuests) {
                let childText = searchState.children > 0 ? `, ${searchState.children} Child${searchState.children > 1 ? 'ren' : ''}` : '';
                displayGuests.value = `${searchState.rooms} Room${searchState.rooms > 1 ? 's' : ''}, ${searchState.adults} Adult${searchState.adults > 1 ? 's' : ''}${childText}`;
            }
            if (guestsDropdown) guestsDropdown.classList.remove('is-active');
        });
    }

    // ── 4. Price Calculation Engine ──────────────────────────────────
    function calculatePlanPrice(planId) {
        let plan = null;
        for (let r of hotelRoomData) {
            let p = (r.plans || []).find(x => x.id == planId);
            if (p) { plan = p; break; }
        }
        if (!plan || !searchState.dates.start || !searchState.dates.end) {
            return { avgBase: 0, avgFinal: 0, totalBase: 0, totalFinal: 0, nights: 1, rooms: 1 };
        }

        const start = new Date(searchState.dates.start);
        const end = new Date(searchState.dates.end);
        let nights = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) || 1;

        let totalBase = 0;
        let totalFinal = 0;
        let totalTax = 0;
        let rooms = searchState.rooms || 1;
        const extraAdults = Math.max(0, searchState.adults - (2 * rooms));
        const extraChildren = searchState.children || 0;

        for (let i = 0; i < nights; i++) {
            let cur = new Date(start);
            cur.setDate(start.getDate() + i);
            let nightStr = cur.toISOString().split('T')[0];

            let rate = null;
            if (plan.date_rates && plan.date_rates.length > 0) {
                rate = plan.date_rates.find(dr => dr.rate_date === nightStr) || plan.date_rates[0];
            }

            let baseRate = rate ? parseFloat(rate.base_rate_2_pax || 0) : 0;
            let adultRate = rate ? parseFloat(rate.extra_adult_rate || 0) : 0;
            let childRate = rate ? parseFloat(rate.cnb_rate || 0) : 0;

            let dailyBase = (baseRate * rooms) + (extraAdults * adultRate) + (extraChildren * childRate);
            let dailyBasePerRoom = dailyBase / rooms;
            
            // Universal GST calculation based on slab determined by total daily package per room
            let dailyTax = 0;
            if (dailyBasePerRoom <= 1000) dailyTax = 0;
            else if (dailyBasePerRoom <= 7500) dailyTax = dailyBase * 0.05;
            else dailyTax = dailyBase * 0.18;

            totalBase += dailyBase;
            totalTax += dailyTax;
            totalFinal += dailyBase + dailyTax;
        }

        return {
            avgBase: totalBase / nights / rooms,
            avgFinal: totalFinal / nights / rooms,
            totalBase: totalBase,
            totalTax: totalTax,
            totalFinal: totalFinal,
            nights: nights,
            rooms: rooms
        };
    }

    function updateAllPlanPricesUI() {
        const rooms = searchState.rooms || 1;
        const extraAdults = Math.max(0, searchState.adults - (2 * rooms));
        const extraChildren = searchState.children || 0;

        for (let r of hotelRoomData) {
            for (let p of (r.plans || [])) {
                let priceData = calculatePlanPrice(p.id);
                let finalEl = document.getElementById('desktop-final-' + p.id) || document.getElementById('final-' + p.id);
                let strikeEl = document.getElementById('desktop-strike-' + p.id) || document.getElementById('strike-' + p.id);
                let eaInc = document.getElementById('desktop-ea-inc-' + p.id) || document.getElementById('ea-inc-' + p.id);
                let ecInc = document.getElementById('desktop-ec-inc-' + p.id) || document.getElementById('ec-inc-' + p.id);

                if (eaInc) eaInc.classList.toggle('is-hidden', extraAdults === 0);
                if (ecInc) ecInc.classList.toggle('is-hidden', extraChildren === 0);

                if (finalEl && priceData.avgFinal > 0) {
                    finalEl.innerText = '₹' + Math.round(priceData.avgFinal).toLocaleString('en-IN');
                }
                if (strikeEl) {
                    strikeEl.classList.toggle('is-hidden', priceData.avgBase <= priceData.avgFinal);
                }
            }
        }
    }

    function updateSidebarUI() {
        if (!currentSelectedPlan) return;
        let priceData = calculatePlanPrice(currentSelectedPlan.id);

        const setTxt = (id, v) => { const el = getEl(id); if (el) el.innerText = v; };
        
        const dispTaxes = getEl('dispTaxes');
        if (dispTaxes) {
            dispTaxes.innerText = priceData.totalTax > 0 ? '+ ₹' + priceData.totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2 }) + ' Taxes' : 'Taxes Included';
        }
        
        setTxt('sidebarTotalFinal', '₹' + Math.round(priceData.totalFinal).toLocaleString('en-IN'));

        const priceInput = getEl('formFinalPrice');
        if (priceInput) priceInput.value = priceData.totalFinal;
    }

    updateAllPlanPricesUI();

    // ── 5. Plan Selection & Modal Delegation ─────────────────────────
    document.addEventListener('click', (e) => {
        // Plan selection
        const selectBtn = e.target.closest('[data-action="select-plan"]');
        if (selectBtn) {
            e.preventDefault();
            const planId = selectBtn.getAttribute('data-plan-id');
            const roomId = selectBtn.getAttribute('data-room-id');
            const roomName = selectBtn.getAttribute('data-room-name');
            const planName = selectBtn.getAttribute('data-plan-name');

            document.querySelectorAll('[data-action="select-plan"]').forEach(b => {
                b.className = 'btn-add-cart';
                b.innerText = 'SELECT';
            });

            selectBtn.className = 'btn-remove-cart';
            selectBtn.innerText = 'SELECTED';

            currentSelectedPlan = { id: planId, name: planName };
            currentSelectedRoom = { id: roomId, name: roomName };

            const cartEmpty = getEl('cartEmpty');
            const cartFull = getEl('cartFull');
            const btnProceed = getEl('btnProceed');

            if (cartEmpty) cartEmpty.classList.add('is-hidden', 'hidden');
            if (cartFull) cartFull.classList.remove('is-hidden', 'hidden');
            if (btnProceed) btnProceed.disabled = false;

            const dispRoom = getEl('dispRoomName');
            const dispPlan = getEl('dispPlanName');
            if (dispRoom) dispRoom.innerText = roomName;
            if (dispPlan) dispPlan.innerText = planName;

            const roomInput = getEl('formRoomId');
            const planInput = getEl('formPlanId');
            if (roomInput) roomInput.value = roomId;
            if (planInput) planInput.value = planId;

            updateSidebarUI();
            return;
        }

        // Room details toggle
        const toggleRoomBtn = e.target.closest('[data-action="toggle-room-details"]');
        if (toggleRoomBtn) {
            e.preventDefault();
            const targetId = toggleRoomBtn.getAttribute('data-target');
            const target = document.getElementById(targetId);
            if (target) {
                const isClosed = target.classList.contains('is-hidden') || target.style.display === 'none' || target.style.display === '';
                target.classList.toggle('is-hidden', !isClosed);
                target.style.display = isClosed ? 'block' : 'none';
                toggleRoomBtn.innerText = isClosed ? '- Less Details' : '+ More Details';
            }
            return;
        }

        // Description toggle
        const toggleDescBtn = e.target.closest('[data-action="toggle-desc"]');
        if (toggleDescBtn) {
            e.preventDefault();
            const desc = getEl('hotelDesc');
            if (desc) {
                const isTruncated = desc.classList.toggle('truncated');
                toggleDescBtn.innerText = isTruncated ? 'View More ∨' : 'View Less ∧';
            }
            return;
        }

        // Open Checkout Modal
        const openCheckoutBtn = e.target.closest('[data-action="open-checkout"]');
        if (openCheckoutBtn) {
            e.preventDefault();
            const modal = getEl('checkoutModal');
            if (modal) {
                const summaryRoom = getEl('modalSummaryRoomPlan');
                const summaryTotal = getEl('modalSummaryTotal');
                const sidebarTotal = getEl('sidebarTotalFinal');
                const modalCheckIn = getEl('modalCheckIn');
                const modalCheckOut = getEl('modalCheckOut');
                const modalNightsVal = getEl('modalNightsVal');
                const modalBasePrice = getEl('modalBasePrice');

                if (summaryRoom && currentSelectedRoom && currentSelectedPlan) {
                    summaryRoom.innerText = `Room: ${currentSelectedRoom.name} - ${currentSelectedPlan.name}`;
                }
                if (summaryTotal && sidebarTotal) {
                    summaryTotal.innerText = sidebarTotal.innerText;
                }
                
                if (modalCheckIn && searchState.dates.start) {
                    const startDt = new Date(searchState.dates.start);
                    if (!isNaN(startDt)) modalCheckIn.innerText = startDt.toLocaleDateString('en-US', {weekday: 'short', day: 'numeric', month: 'short'});
                }
                if (modalCheckOut && searchState.dates.end) {
                    const endDt = new Date(searchState.dates.end);
                    if (!isNaN(endDt)) modalCheckOut.innerText = endDt.toLocaleDateString('en-US', {weekday: 'short', day: 'numeric', month: 'short'});
                }
                
                if (currentSelectedPlan && modalNightsVal && modalBasePrice) {
                    let priceData = calculatePlanPrice(currentSelectedPlan.id);
                    modalNightsVal.innerText = `${priceData.nights}N`;
                    modalBasePrice.innerText = '₹' + Math.round(priceData.totalBase).toLocaleString('en-IN');
                    
                    const modalTaxes = getEl('modalTaxes');
                    if (modalTaxes) {
                        modalTaxes.innerText = priceData.totalTax > 0 ? '+ ₹' + priceData.totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2 }) : 'Included';
                    }
                }

                modal.classList.add('is-active', 'active');
                document.body.classList.add('scroll-lock');
            }
            return;
        }

        // Close Modal
        const closeBtn = e.target.closest('[data-action="close-checkout"]');
        if (closeBtn) {
            e.preventDefault();
            const modal = getEl('checkoutModal');
            if (modal) {
                modal.classList.remove('is-active', 'active');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }
    });

    // ── 6. Search Bar Submission ─────────────────────────────────────
    const btnPerformSearch = getEl('btnPerformSearch');
    if (btnPerformSearch) {
        btnPerformSearch.addEventListener('click', () => {
            const url = new URL(window.location.href);
            let startDate = checkInInput ? checkInInput.value : searchState.dates.start;
            let endDate = checkOutInput ? checkOutInput.value : searchState.dates.end;
            url.searchParams.set('check_in', startDate);
            url.searchParams.set('check_out', endDate);
            url.searchParams.set('rooms', searchState.rooms);
            url.searchParams.set('adults', searchState.adults);
            url.searchParams.set('children', searchState.children);
            url.searchParams.set('infants', searchState.infants);
            window.location.href = url.toString();
        });
    }

    // ── 7. Checkout Form Submission ──────────────────────────────────
    const checkoutForm = getEl('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = getEl('btnSubmitModal');
            const msg = getEl('modalFormMsg');

            if (btn) { btn.innerText = 'Processing...'; btn.disabled = true; }

            const formData = new FormData(this);
            formData.set('check_in', searchState.dates.start);
            formData.set('check_out', searchState.dates.end);
            formData.set('rooms', searchState.rooms);
            formData.set('adults', searchState.adults);

            const firstName = formData.get('guest_name') || '';
            const lastName = formData.get('guest_last_name') || '';
            formData.set('guest_name', `${firstName} ${lastName}`.trim());

            fetch('api/submit-hotel-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (msg) { msg.style.color = '#2ecc71'; msg.innerText = 'Success! Downloading slip...'; }
                    
                    if (data.booking_id) {
                        window.open('api-download-hotel-inquiry-slip.php?id=' + data.booking_id, '_blank');
                    }
                    
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    if (msg) { msg.style.color = '#e74c3c'; msg.innerText = data.message || 'Error occurred'; }
                    if (btn) { btn.innerText = 'Submit'; btn.disabled = false; }
                }
            })
            .catch(() => {
                if (msg) { msg.style.color = '#e74c3c'; msg.innerText = 'Network error.'; }
                if (btn) { btn.innerText = 'Submit'; btn.disabled = false; }
            });
        });
    }

    // ── 8. Lightbox Logic ────────────────────────────────────────────
    const lightboxModal = document.getElementById('lightboxModal');
    const mainImg = document.getElementById('lightboxMainImg');
    const counter = document.getElementById('lightboxCounter');
    const thumbs = document.querySelectorAll('.lightbox-thumbnail');
    let curIdx = 0;

    function showLightbox(idx) {
        if (!thumbs.length || !lightboxModal || !mainImg) return;
        curIdx = (idx + thumbs.length) % thumbs.length;
        mainImg.src = thumbs[curIdx].getAttribute('src');
        if (counter) counter.innerText = `${curIdx + 1} / ${thumbs.length}`;
        thumbs.forEach((t, i) => t.classList.toggle('active', i === curIdx));
        lightboxModal.classList.add('is-active');
        document.body.classList.add('scroll-lock');
    }

    document.addEventListener('click', (e) => {
        const lbOpen = e.target.closest('[data-action="open-lightbox"]');
        if (lbOpen) {
            e.preventDefault();
            const idx = parseInt(lbOpen.getAttribute('data-idx')) || 0;
            showLightbox(idx);
            return;
        }

        const lbClose = e.target.closest('[data-action="close-lightbox"]');
        if (lbClose) {
            e.preventDefault();
            if (lightboxModal) {
                lightboxModal.classList.remove('is-active');
                document.body.classList.remove('scroll-lock');
            }
            return;
        }

        const lbNext = e.target.closest('[data-action="next-lightbox"]');
        if (lbNext) {
            e.preventDefault();
            showLightbox(curIdx + 1);
            return;
        }

        const lbPrev = e.target.closest('[data-action="prev-lightbox"]');
        if (lbPrev) {
            e.preventDefault();
            showLightbox(curIdx - 1);
            return;
        }
    });
});
