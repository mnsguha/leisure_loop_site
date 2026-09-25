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
        
        // Auto-set service type from search tab (hourly/oneway fixed; itinerary = user choice)
        if (searchDetails.type === 'hourly') {
            chosenServiceType = 'Hourly';
        } else if (searchDetails.type === 'itinerary') {
            chosenServiceType = null;
        } else {
            chosenServiceType = 'Oneway';
        }

        // Apply dynamic date pricing if travel_date was provided
                    if (searchDetails.travel_date) {
                        document.querySelectorAll('.room-category').forEach(card => {
                            const vid = card.getAttribute('data-vid');
                            const basePrice = parseFloat(card.getAttribute('data-baseprice')) || 0;
                            let currentPrice = basePrice;

                            if (vehicleRates[vid] && vehicleRates[vid][searchDetails.travel_date]) {
                                let ratesForDate = vehicleRates[vid][searchDetails.travel_date];
                                
                                if (searchDetails.type === 'hourly' && searchDetails.duration) {
                                    const hours = parseInt(searchDetails.duration, 10);
                                    const perHour = parseFloat(ratesForDate['price_per_hour']) || 0;
                                    currentPrice = (perHour > 0 && hours > 0) ? perHour * hours : parseFloat(ratesForDate['price']) || 0;
                                } else {
                                    currentPrice = parseFloat(ratesForDate['price']) || 0;
                                }
                            }

                            if (currentPrice <= 0) {
                                currentPrice = 0;
                            }

                            selectedDynamicPrices[vid] = currentPrice;

                const priceEl = card.querySelector('.price-final');
                if (priceEl) {
                    if (currentPrice > 0) {
                        priceEl.textContent = '₹' + Math.round(currentPrice).toLocaleString('en-IN');
                    } else {
                        priceEl.innerHTML = '<span class="price-unit">N/A</span>';
                    }
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

        const isHourly = searchDetails.type === 'hourly';
        const isItinerary = searchDetails.type === 'itinerary';

        let searchSummary = '';
        if (isHourly) {
            searchSummary = `
                <div class="sidebar-summary">
                    <strong>Pick-up:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong>Duration:</strong> ${searchDetails.duration || 'N/A'}<br>
                    <strong>Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        } else if (isItinerary) {
            searchSummary = `
                <div class="sidebar-summary">
                    <strong>Start:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong>Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        } else {
            searchSummary = `
                <div class="sidebar-summary">
                    <strong>From:</strong> ${searchDetails.pickup_location || 'N/A'}<br>
                    <strong>To:</strong> ${searchDetails.drop_location || 'N/A'}<br>
                    <strong>Date:</strong> ${searchDetails.travel_date || 'N/A'}
                </div>`;
        }

        let serviceTypeHtml = '';
        if (isItinerary) {
            const checkedDisposal = chosenServiceType === 'Disposal' ? 'checked' : '';
            const checkedP2P = chosenServiceType === 'Point to Point' ? 'checked' : '';
            serviceTypeHtml = `
                <div class="sidebar-section-label">Select Service Type:</div>
                <div class="payment-type">
                    <label>
                        <input type="radio" name="service_type_radio" value="Disposal" data-action="set-service-type" ${checkedDisposal}>
                        Disposal (Full Day usage within city/outstation limits)
                    </label>
                    <label>
                        <input type="radio" name="service_type_radio" value="Point to Point" data-action="set-service-type" ${checkedP2P}>
                        Point to Point (Direct A to B transfer)
                    </label>
                </div>`;
        } else {
            serviceTypeHtml = `<div class="sidebar-section-label">Service Type: ${chosenServiceType || 'N/A'}</div>`;
        }

        const canSubmit = Boolean(chosenServiceType);

        sidebar.innerHTML = `
            <div class="selected-hotel-name">${selectedVehicle.name}</div>
            <div class="selected-room-name">Premium Chauffeur Driven</div>
            ${searchSummary}
            ${serviceTypeHtml}

            <div class="total-row">
                <span>Total Estimated</span>
                <span>₹${Math.round(selectedVehicle.price).toLocaleString('en-IN')}</span>
            </div>

            <button type="button" class="btn-proceed" id="btnSubmitEnquiry" ${canSubmit ? '' : 'disabled'} data-action="open-cab-checkout">Submit Inquiry</button>
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
            const vimage = selectBtn.getAttribute('data-image') || '';
            const vkm = selectBtn.getAttribute('data-km') || '';
            const vseats = selectBtn.getAttribute('data-seats') || '';
            const vbags = selectBtn.getAttribute('data-bags') || '';
            const vac = selectBtn.getAttribute('data-ac') || '';
            const vfuel = selectBtn.getAttribute('data-fuel') || '';
            const dynamicPrice = selectedDynamicPrices[vid] || vprice;

            if (dynamicPrice <= 0) {
                alert('No rate available for this vehicle on the selected date.');
                return;
            }

            selectedVehicle = { id: vid, name: vname, price: dynamicPrice, image: vimage, km: vkm, seats: vseats, bags: vbags, ac: vac, fuel: vfuel };

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
            const setHtml = (id, v) => { const el = document.getElementById(id); if (el) el.innerHTML = v; };
            const setHidden = (id, show) => { const el = document.getElementById(id); if (el) el.hidden = !show; };

            const tripType = searchDetails.type || 'oneway';
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            const formatDateNice = (raw) => {
                if (!raw) return 'N/A';
                const parts = String(raw).split('-');
                if (parts.length !== 3) return raw;
                const y = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10);
                const d = parseInt(parts[2], 10);
                if (!y || !m || !d) return raw;
                const dt = new Date(y, m - 1, d);
                return dayNames[dt.getDay()] + ', ' + monthNames[m - 1] + ' ' + d;
            };

            const formatTimeNice = (raw) => {
                if (!raw) return 'TBD';
                const t = String(raw).split(':');
                if (t.length < 2) return raw;
                let hh = parseInt(t[0], 10);
                const mm = t[1];
                if (Number.isNaN(hh)) return raw;
                const ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12;
                if (hh === 0) hh = 12;
                return hh + ':' + mm + ' ' + ampm;
            };

            setTxt('desktop-modalSummaryVehicle', selectedVehicle.name);
            setTxt('desktop-modalSummaryService', chosenServiceType);

            const vimg = document.getElementById('desktop-modalVehicleImg');
            if (vimg) {
                if (selectedVehicle.image) {
                    vimg.src = selectedVehicle.image;
                    vimg.alt = selectedVehicle.name;
                    vimg.hidden = false;
                } else {
                    vimg.removeAttribute('src');
                    vimg.alt = '';
                    vimg.hidden = true;
                }
            }

            let labelPickup = 'Pick-up';
            let labelDrop = 'Drop-off';
            let pillHtml = '<span class="material-symbols-outlined">arrow_forward</span>';

            if (tripType === 'hourly') {
                labelPickup = 'Pick-up';
                labelDrop = 'Duration';
                const hrs = searchDetails.duration ? String(searchDetails.duration) + 'H' : '—';
                pillHtml = hrs;
            } else if (tripType === 'itinerary') {
                labelPickup = 'Start';
                labelDrop = 'Route';
                pillHtml = '<span class="material-symbols-outlined">route</span>';
            }

            const updateRouteLabel = (id, text) => {
                const el = document.getElementById(id);
                if (!el) return;
                const icon = el.querySelector('.material-symbols-outlined');
                el.textContent = text + ' ';
                if (icon) el.appendChild(icon);
            };
            updateRouteLabel('desktop-modalLabelPickup', labelPickup);
            updateRouteLabel('desktop-modalLabelDrop', labelDrop);

            setHtml('desktop-modalRoutePill', pillHtml);

            setTxt('desktop-modalPickup', searchDetails.pickup_location || 'N/A');
            if (tripType === 'hourly') {
                setTxt('desktop-modalDrop', searchDetails.duration ? searchDetails.duration + ' hr' : 'N/A');
            } else if (tripType === 'itinerary') {
                const itin = (searchDetails.itinerary_details || '').trim();
                setTxt('desktop-modalDrop', itin ? (itin.length > 42 ? itin.slice(0, 42) + '…' : itin) : 'N/A');
            } else {
                setTxt('desktop-modalDrop', searchDetails.drop_location || 'N/A');
            }

            setTxt('desktop-modalDate', formatDateNice(searchDetails.travel_date));
            setTxt('desktop-modalTime', formatTimeNice(searchDetails.travel_time));
            setTxt('desktop-modalSummaryTotal', '₹' + Math.round(selectedVehicle.price).toLocaleString('en-IN'));

            const setMeta = (wrapId, valId, val) => {
                const text = String(val || '').trim();
                setHidden(wrapId, Boolean(text));
                if (text) setTxt(valId, text);
            };
            setMeta('desktop-modalMetaSeats', 'desktop-modalSeats', selectedVehicle.seats ? selectedVehicle.seats + ' Seats' : '');
            setMeta('desktop-modalMetaBags', 'desktop-modalBags', selectedVehicle.bags ? selectedVehicle.bags + ' Bags' : '');
            setMeta('desktop-modalMetaAc', 'desktop-modalAc', selectedVehicle.ac);
            setMeta('desktop-modalMetaFuel', 'desktop-modalFuel', selectedVehicle.fuel);
            setMeta('desktop-modalMetaKm', 'desktop-modalKmCharges', selectedVehicle.km);

            const modal = document.getElementById('desktop-checkoutModal');
            if (modal) {
                modal.classList.add('is-active');
                document.body.classList.add('scroll-lock');
            }
            return;
        }

        // Close checkout modal
        const closeCheckoutBtn = e.target.closest('[data-action="close-checkout"]');
        if (closeCheckoutBtn) {
            e.preventDefault();
            const modal = document.getElementById('desktop-checkoutModal');
            if (modal) modal.classList.remove('is-active');
            document.body.classList.remove('scroll-lock');
            return;
        }

        if (e.target.id === 'desktop-checkoutModal') {
            e.target.classList.remove('is-active');
            document.body.classList.remove('scroll-lock');
        }
    });

    // ── 6. Radio Change Delegation for Service Type ──────────────────
    document.addEventListener('change', (e) => {
        if (e.target.matches('[data-action="set-service-type"]')) {
            chosenServiceType = e.target.value;
            const btn = document.getElementById('btnSubmitEnquiry');
            if (btn) btn.disabled = !chosenServiceType;
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
            const submitEndpoint = basePath ? `${basePath}/api-submit-cab-booking.php` : 'api-submit-cab-booking.php';
            const voucherEndpoint = basePath ? `${basePath}/api-download-cab-inquiry-slip.php` : 'api-download-cab-inquiry-slip.php';

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
