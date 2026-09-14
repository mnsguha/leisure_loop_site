'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Read Rates Dataset from DOM ───────────────────────────────
    const ratesDataStore = document.getElementById('desktop-cab-rates-data-store');
    const vehicleRates = ratesDataStore ? JSON.parse(ratesDataStore.dataset.rates || '{}') : {};

    const container = document.getElementById('desktop-cabDetailContainer');
    const isSearchActive = container ? !container.classList.contains('pre-search-state') : false;

    let hasSearched = isSearchActive;
    let searchDetails = {};
    let selectedVehicle = null;
    let chosenServiceType = null;
    const selectedDynamicPrices = {};

    // Parse URL parameters on initial load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('search') === '1' || urlParams.get('pickup_location')) {
        hasSearched = true;
        searchDetails = {
            type: urlParams.get('type') || 'oneway',
            pickup_location: urlParams.get('pickup_location') || '',
            drop_location: urlParams.get('drop_location') || '',
            travel_date: urlParams.get('travel_date') || '',
            travel_time: urlParams.get('travel_time') || '',
            duration: urlParams.get('duration') || '',
            itinerary_details: urlParams.get('itinerary_details') || '',
            cab_type: urlParams.get('cab_type') || ''
        };
        
        // Apply dynamic date pricing if travel_date was provided
        if (searchDetails.travel_date) {
            document.querySelectorAll('.room-category').forEach(card => {
                const vid = card.getAttribute('data-vid');
                const basePrice = parseFloat(card.getAttribute('data-baseprice')) || 0;
                let currentPrice = basePrice;

                if (vehicleRates[vid] && vehicleRates[vid][searchDetails.travel_date]) {
                    currentPrice = parseFloat(vehicleRates[vid][searchDetails.travel_date]);
                }

                selectedDynamicPrices[vid] = currentPrice;

                const priceEl = card.querySelector('.price-final');
                if (priceEl) {
                    priceEl.innerHTML = '₹' + Math.round(currentPrice).toLocaleString('en-IN') + ' <span style="font-size:0.9rem;color:rgba(255,255,255,0.5);">/ day</span>';
                }
            });
        }
    }

    // ── 2. Search Tabs (Oneway / Hourly / Itinerary) ──────────────────
    const tabs = document.querySelectorAll('.search-tab');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetId = tab.getAttribute('data-target');
            const targetPane = document.getElementById('desktop-tab-' + targetId);

            if (!targetPane) return;

            tabs.forEach(t => t.classList.remove('is-active'));
            panes.forEach(p => p.classList.remove('is-active'));

            tab.classList.add('is-active');
            targetPane.classList.add('is-active');
        });
    });

    // ── 3. Cart Sidebar & Service Type Selection ─────────────────────
    function renderSidebar() {
        const sidebar = document.getElementById('desktop-sidebar-cart-content');
        if (!sidebar) return;

        if (!selectedVehicle) {
            sidebar.innerHTML = '<div class="empty-cart-msg">Select a vehicle to view details.</div>';
            return;
        }

        let searchSummary = '';
        if (searchDetails.type === 'hourly') {
            searchSummary = `
                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.15);">
                    <strong style="color: var(--gold);">Pick-up:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong style="color: var(--gold);">Duration:</strong> ${searchDetails.duration || 'N/A'}<br>
                    <strong style="color: var(--gold);">Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        } else if (searchDetails.type === 'itinerary') {
            searchSummary = `
                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.15);">
                    <strong style="color: var(--gold);">Start:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong style="color: var(--gold);">Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        } else {
            searchSummary = `
                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed rgba(255,255,255,0.15);">
                    <strong style="color: var(--gold);">From:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong style="color: var(--gold);">To:</strong> ${searchDetails.drop_location || 'N/A'}<br>
                    <strong style="color: var(--gold);">Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        }

        sidebar.innerHTML = `
            <div class="selected-hotel-name">${selectedVehicle.name}</div>
            <div class="selected-room-name">Premium Chauffeur Driven</div>
            ${searchSummary}
            <div class="selected-plan-name">Base fare: ₹${Math.round(selectedVehicle.price).toLocaleString('en-IN')} / day</div>

            <div style="margin-top:16px; margin-bottom:8px; font-size: 0.9rem; font-weight: 600; color:var(--gold);">Select Service Type:</div>
            <div class="payment-type">
                <label>
                    <input type="radio" name="service_type_radio" value="Disposal" data-action="set-service-type">
                    Disposal (Full Day usage within city/outstation limits)
                </label>
                <label>
                    <input type="radio" name="service_type_radio" value="Point to Point" data-action="set-service-type">
                    Point to Point (Direct A to B transfer)
                </label>
            </div>

            <div class="total-row">
                <span>Total Estimated</span>
                <span>₹${Math.round(selectedVehicle.price).toLocaleString('en-IN')}</span>
            </div>

            <button type="button" class="btn-proceed" id="btnSubmitEnquiry" disabled data-action="open-cab-checkout">Submit Inquiry</button>
        `;
    }

    // ── 5. Global Event Delegation ───────────────────────────────────
    document.addEventListener('click', (e) => {
        // Location swap
        const swapBtn = e.target.closest('[data-action="swap-locations"]');
        if (swapBtn) {
            e.preventDefault();
            const form = swapBtn.closest('form');
            if (form) {
                const pick = form.querySelector('input[name="pickup_location"]');
                const drop = form.querySelector('input[name="drop_location"]');
                if (pick && drop) {
                    const temp = pick.value;
                    pick.value = drop.value;
                    drop.value = temp;
                }
            }
            return;
        }

        // Select vehicle
        const selectBtn = e.target.closest('[data-action="select-vehicle"]');
        if (selectBtn) {
            e.preventDefault();

            if (!hasSearched) {
                const searchWrapper = document.querySelector('.search-wrapper');
                if (searchWrapper) {
                    searchWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const activeForm = document.querySelector('.tab-pane.is-active form');
                    if (activeForm) {
                        activeForm.querySelectorAll('.search-input').forEach(input => {
                            input.style.boxShadow = '0 0 10px #c5a059';
                            setTimeout(() => { input.style.boxShadow = ''; }, 1500);
                        });
                    }
                }
                return;
            }

            const vid = selectBtn.getAttribute('data-id');
            const vname = selectBtn.getAttribute('data-name');
            const vprice = parseFloat(selectBtn.getAttribute('data-price')) || 0;
            const dynamicPrice = selectedDynamicPrices[vid] || vprice;

            selectedVehicle = { id: vid, name: vname, price: dynamicPrice };

            document.querySelectorAll('.vehicle-action-btn').forEach(b => {
                b.innerText = 'SELECT';
                b.classList.remove('is-selected');
            });

            selectBtn.innerText = 'SELECTED';
            selectBtn.classList.add('is-selected');

            renderSidebar();
            return;
        }

        // Open checkout modal
        const openCheckoutBtn = e.target.closest('[data-action="open-cab-checkout"]');
        if (openCheckoutBtn) {
            e.preventDefault();
            if (!chosenServiceType || !selectedVehicle) return;

            const setField = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
            setField('desktop-formVehicleId', selectedVehicle.id);
            setField('desktop-formVehicleName', selectedVehicle.name);
            setField('desktop-formServiceType', chosenServiceType);
            setField('desktop-formFinalPrice', selectedVehicle.price);
            setField('desktop-formPickup', searchDetails.pickup_location || '');
            setField('desktop-formDrop', searchDetails.drop_location || '');
            setField('desktop-formDate', searchDetails.travel_date || '');
            setField('desktop-formTime', searchDetails.travel_time || '');
            setField('desktop-formTripType', searchDetails.type || 'oneway');
            setField('desktop-formDuration', searchDetails.duration || '');
            setField('desktop-formSearchItinerary', searchDetails.itinerary_details || '');

            const setTxt = (id, v) => { const el = document.getElementById(id); if (el) el.innerText = v; };
            setTxt('desktop-modalSummaryVehicle', selectedVehicle.name);
            setTxt('desktop-modalSummaryService', chosenServiceType);
            setTxt('desktop-modalDate', searchDetails.travel_date || 'N/A');
            setTxt('desktop-modalPickup', searchDetails.pickup_location || 'N/A');
            setTxt('desktop-modalDrop', searchDetails.drop_location || (searchDetails.duration || searchDetails.itinerary_details || 'N/A'));
            setTxt('desktop-modalSummaryTotal', '₹' + Math.round(selectedVehicle.price).toLocaleString('en-IN'));

            const modal = document.getElementById('desktop-checkoutModal');
            if (modal) modal.classList.add('is-active');
            return;
        }

        // Close checkout modal
        const closeCheckoutBtn = e.target.closest('[data-action="close-checkout"]');
        if (closeCheckoutBtn) {
            e.preventDefault();
            const modal = document.getElementById('desktop-checkoutModal');
            if (modal) modal.classList.remove('is-active');
            return;
        }

        if (e.target.id === 'desktop-checkoutModal') {
            e.target.classList.remove('is-active');
        }
    });

    // ── 6. Radio Change Delegation for Service Type ──────────────────
    document.addEventListener('change', (e) => {
        if (e.target.matches('[data-action="set-service-type"]')) {
            chosenServiceType = e.target.value;
            const btn = document.getElementById('desktop-btnSubmitEnquiry');
            if (btn) btn.disabled = false;
        }
    });

    // ── 7. Checkout Form Submission ──────────────────────────────────
    const checkoutForm = document.getElementById('desktop-checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('desktop-btnSubmitModal');
            const msg = document.getElementById('desktop-modalFormMsg');

            if (btn) { btn.innerText = 'Processing...'; btn.disabled = true; }

            const formData = new FormData(this);
            const firstName = formData.get('guest_name') || '';
            const lastName = formData.get('guest_last_name') || '';
            formData.set('guest_name', `${firstName} ${lastName}`.trim());

            // Determine the base URL path (works whether running at root or under /leisure_loop_site/)
            const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            const submitEndpoint = basePath ? `${basePath}/api/submit-cab-booking.php` : 'api/submit-cab-booking.php';
            const voucherEndpoint = basePath ? `${basePath}/api/download-cab-inquiry-slip.php` : 'api/download-cab-inquiry-slip.php';

            fetch(submitEndpoint, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (msg) {
                        msg.className = 'form-feedback-msg is-success is-active';
                        msg.innerText = 'Success! Generating inquiry voucher...';
                    }
                    setTimeout(() => {
                        if (data.booking_id) {
                            window.open(`${voucherEndpoint}?id=${data.booking_id}`, '_blank');
                        }
                        window.location.reload();
                    }, 1800);
                } else {
                    if (msg) {
                        msg.className = 'form-feedback-msg is-error is-active';
                        msg.innerText = data.message || 'Submission failed. Please try again.';
                    }
                    if (btn) { btn.innerText = 'Submit Inquiry'; btn.disabled = false; }
                }
            })
            .catch(() => {
                if (msg) {
                    msg.className = 'form-feedback-msg is-error is-active';
                    msg.innerText = 'Unable to reach the server. Please check the network path.';
                }
                if (btn) { btn.innerText = 'Submit Inquiry'; btn.disabled = false; }
            });
        });
    }

    const detailStart = document.getElementById('desktop-detail-itin-start');
    const detailEnd = document.getElementById('desktop-detail-itin-end');

    if (detailStart && detailEnd) {
        detailStart.addEventListener('change', () => {
            detailEnd.min = detailStart.value;
            if (detailEnd.value && detailEnd.value < detailStart.value) {
                detailEnd.value = detailStart.value;
            }
        });
    }
});
