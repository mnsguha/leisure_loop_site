'use strict';

/**
 * mobile-cab-detail.js
 * Mobile-only interactions for cab search results (Rule 11: separate from cab-detail.js).
 * Flow: SELECT → price bar → Review Details → Enquiry Summary → POST.
 */
document.addEventListener('DOMContentLoaded', () => {
    const ratesStore = document.getElementById('m-cab-rates-data-store');
    const vehicleRates = ratesStore ? (JSON.parse(ratesStore.dataset.rates || '{}') || {}) : {};

    const urlParams = new URLSearchParams(window.location.search);
    const searchDetails = {
        type: urlParams.get('type') || 'oneway',
        pickup_location: urlParams.get('pickup_location') || '',
        drop_location: urlParams.get('drop_location') || '',
        travel_date: urlParams.get('travel_date') || '',
        travel_time: urlParams.get('travel_time') || '',
        duration: urlParams.get('duration') || '',
        itinerary_details: urlParams.get('itinerary_details') || ''
    };

    let chosenServiceType = null;
    if (searchDetails.type === 'hourly') {
        chosenServiceType = 'Hourly';
    } else if (searchDetails.type === 'oneway') {
        chosenServiceType = 'Oneway';
    }

    const selectedDynamicPrices = {};
    let selectedVehicle = null;
    let lastFocused = null;

    const modal = document.getElementById('m-cab-checkout-modal');
    const reviewModal = document.getElementById('mCabReviewModal');
    const priceBar = document.getElementById('mCabPriceBar');
    const checkoutForm = document.getElementById('m-cab-checkoutForm');
    const reviewServiceGroup = document.getElementById('mCabReviewServiceGroup');
    const reviewSubmitBtn = document.getElementById('mCabReviewSubmit');
    const reviewMsg = document.getElementById('mCabReviewMsg');
    const submitBtn = document.getElementById('m-btnSubmitModal');
    const formMsg = document.getElementById('m-modalFormMsg');

    const setField = (id, v) => {
        const el = document.getElementById(id);
        if (el) el.value = v == null ? '' : String(v);
    };
    const setTxt = (id, v) => {
        const el = document.getElementById(id);
        if (el) el.textContent = v == null ? '' : String(v);
    };
    const setHidden = (id, show) => {
        const el = document.getElementById(id);
        if (el) el.hidden = !show;
    };

    const formatDateNice = (raw) => {
        if (!raw) return 'N/A';
        const parts = String(raw).split('-');
        if (parts.length !== 3) return raw;
        const y = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);
        const d = parseInt(parts[2], 10);
        if (!y || !m || !d) return raw;
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
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

    const getFocusable = (root) => {
        if (!root) return [];
        return Array.from(root.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )).filter((el) => !el.hidden && el.offsetParent !== null);
    };

    const isModalOpen = (el) => Boolean(el) && !el.hidden;
    const anyDialogOpen = () => isModalOpen(modal) || isModalOpen(reviewModal);

    const openEnquiry = () => {
        if (!modal) return;
        if (!anyDialogOpen()) lastFocused = document.activeElement;
        if (isModalOpen(reviewModal)) {
            reviewModal.classList.remove('is-active');
            reviewModal.classList.add('is-hidden');
            reviewModal.setAttribute('aria-hidden', 'true');
            reviewModal.hidden = true;
        }
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-active');
        document.body.classList.add('scroll-lock');
        const focusables = getFocusable(modal);
        if (focusables.length) focusables[0].focus();
    };

    const closeEnquiryToReview = () => {
        if (!modal) return;
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        openReview();
    };

    const closeEnquiryFully = () => {
        if (!modal) return;
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('scroll-lock');
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
        lastFocused = null;
    };

    const showPriceBar = () => {
        if (!priceBar) return;
        if (!selectedVehicle) return;
        setTxt('mCabBarVehicle', selectedVehicle.name);
        setTxt('mCabBarTotal', '₹' + Math.round(selectedVehicle.price).toLocaleString('en-IN'));
        priceBar.hidden = false;
        priceBar.classList.remove('is-hidden');
        priceBar.classList.add('is-visible');
    };

    const hidePriceBar = () => {
        if (!priceBar) return;
        priceBar.hidden = true;
        priceBar.classList.add('is-hidden');
        priceBar.classList.remove('is-visible');
    };

    const openReview = () => {
        if (!reviewModal || !selectedVehicle) return;
        if (!anyDialogOpen()) lastFocused = document.activeElement;
        if (isModalOpen(modal)) {
            modal.classList.remove('is-active');
            modal.setAttribute('aria-hidden', 'true');
            modal.hidden = true;
        }
        populateReview();
        reviewModal.hidden = false;
        reviewModal.setAttribute('aria-hidden', 'false');
        reviewModal.classList.remove('is-hidden');
        reviewModal.classList.add('is-active');
        document.body.classList.add('scroll-lock');
        const focusables = getFocusable(reviewModal);
        if (focusables.length) focusables[0].focus();
    };

    const closeReview = () => {
        if (!reviewModal) return;
        reviewModal.classList.remove('is-active');
        reviewModal.classList.add('is-hidden');
        reviewModal.setAttribute('aria-hidden', 'true');
        reviewModal.hidden = true;
        document.body.classList.remove('scroll-lock');
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
        lastFocused = null;
    };

    const openModal = openEnquiry;
    const closeModal = closeEnquiryToReview;

    const populateReview = () => {
        if (!selectedVehicle) return;
        const isItinerary = searchDetails.type === 'itinerary';
        if (reviewServiceGroup) reviewServiceGroup.hidden = !isItinerary;
        if (reviewSubmitBtn) {
            reviewSubmitBtn.disabled = Boolean(isItinerary && !chosenServiceType);
        }
        if (reviewMsg) {
            reviewMsg.textContent = '';
            reviewMsg.className = 'm-cab-form-msg';
        }

        setTxt('mCabReviewVehicle', selectedVehicle.name);
        setTxt('mCabReviewService', chosenServiceType || (isItinerary ? 'Select service type' : ''));

        const vimg = document.getElementById('mCabReviewImg');
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
        if (searchDetails.type === 'hourly') {
            labelPickup = 'Pick-up';
            labelDrop = 'Duration';
        } else if (searchDetails.type === 'itinerary') {
            labelPickup = 'Start';
            labelDrop = 'Route';
        }
        setTxt('mCabReviewLabelPickup', labelPickup);
        setTxt('mCabReviewLabelDrop', labelDrop);
        setTxt('mCabReviewPickup', searchDetails.pickup_location || 'N/A');
        if (searchDetails.type === 'hourly') {
            setTxt('mCabReviewDrop', searchDetails.duration ? searchDetails.duration + ' hr' : 'N/A');
        } else if (searchDetails.type === 'itinerary') {
            const itin = (searchDetails.itinerary_details || '').trim();
            setTxt('mCabReviewDrop', itin ? (itin.length > 48 ? itin.slice(0, 48) + '…' : itin) : 'N/A');
        } else {
            setTxt('mCabReviewDrop', searchDetails.drop_location || 'N/A');
        }
        setTxt('mCabReviewDate', formatDateNice(searchDetails.travel_date));
        setTxt('mCabReviewTime', formatTimeNice(searchDetails.travel_time));
        setTxt('mCabReviewTotal', '₹' + Math.round(selectedVehicle.price).toLocaleString('en-IN'));

        const setMeta = (id, text) => {
            const el = document.getElementById(id);
            if (!el) return;
            const val = String(text || '').trim();
            el.hidden = !val;
            const valEl = el.querySelector('[data-meta-val]');
            if (valEl) valEl.textContent = val;
        };
        setMeta('mCabReviewMetaSeats', selectedVehicle.seats ? selectedVehicle.seats + ' Seats' : '');
        setMeta('mCabReviewMetaBags', selectedVehicle.bags ? selectedVehicle.bags + ' Bags' : '');
        setMeta('mCabReviewMetaAc', selectedVehicle.ac);
        setMeta('mCabReviewMetaFuel', selectedVehicle.fuel);
        setMeta('mCabReviewKm', selectedVehicle.km);
    };

    const populateEnquirySummary = () => {
        if (!selectedVehicle) return;
        const isItinerary = searchDetails.type === 'itinerary';
        setTxt('m-modalSummaryVehicle', selectedVehicle.name);
        setTxt('m-modalSummaryService', chosenServiceType || (isItinerary ? 'Select service type' : ''));

        const vimg = document.getElementById('m-modalVehicleImg');
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
        if (searchDetails.type === 'hourly') {
            labelPickup = 'Pick-up';
            labelDrop = 'Duration';
        } else if (searchDetails.type === 'itinerary') {
            labelPickup = 'Start';
            labelDrop = 'Route';
        }
        setTxt('m-modalLabelPickup', labelPickup);
        setTxt('m-modalLabelDrop', labelDrop);
        setTxt('m-modalPickup', searchDetails.pickup_location || 'N/A');
        if (searchDetails.type === 'hourly') {
            setTxt('m-modalDrop', searchDetails.duration ? searchDetails.duration + ' hr' : 'N/A');
        } else if (searchDetails.type === 'itinerary') {
            const itin = (searchDetails.itinerary_details || '').trim();
            setTxt('m-modalDrop', itin ? (itin.length > 48 ? itin.slice(0, 48) + '…' : itin) : 'N/A');
        } else {
            setTxt('m-modalDrop', searchDetails.drop_location || 'N/A');
        }
        setTxt('m-modalDate', formatDateNice(searchDetails.travel_date));
        setTxt('m-modalTime', formatTimeNice(searchDetails.travel_time));
        setTxt('m-modalSummaryTotal', '₹' + Math.round(selectedVehicle.price).toLocaleString('en-IN'));

        const setMeta = (id, text) => {
            const el = document.getElementById(id);
            if (!el) return;
            const val = String(text || '').trim();
            el.hidden = !val;
            const valEl = el.querySelector('[data-meta-val]');
            if (valEl) valEl.textContent = val;
        };
        setMeta('m-modalMetaSeats', selectedVehicle.seats ? selectedVehicle.seats + ' Seats' : '');
        setMeta('m-modalMetaBags', selectedVehicle.bags ? selectedVehicle.bags + ' Bags' : '');
        setMeta('m-modalMetaAc', selectedVehicle.ac);
        setMeta('m-modalMetaFuel', selectedVehicle.fuel);
        setMeta('m-modalMetaKm', selectedVehicle.km);

        if (formMsg) {
            formMsg.textContent = '';
            formMsg.className = 'm-cab-form-msg';
        }
        if (submitBtn) {
            submitBtn.textContent = 'Submit Inquiry';
            submitBtn.disabled = false;
        }
    };

    // Dynamic price refresh from rates store (UI preview only)
    if (searchDetails.travel_date) {
        document.querySelectorAll('.m-cab-result-card').forEach((card) => {
            const vid = card.getAttribute('data-vid');
            const basePrice = parseFloat(card.getAttribute('data-baseprice')) || 0;
            let currentPrice = basePrice;

            if (vehicleRates[vid] && vehicleRates[vid][searchDetails.travel_date]) {
                const ratesForDate = vehicleRates[vid][searchDetails.travel_date];
                if (searchDetails.type === 'hourly' && searchDetails.duration) {
                    const hours = parseInt(searchDetails.duration, 10);
                    const perHour = parseFloat(ratesForDate.price_per_hour) || 0;
                    currentPrice = (perHour > 0 && hours > 0)
                        ? perHour * hours
                        : (parseFloat(ratesForDate.price) || 0);
                } else {
                    currentPrice = parseFloat(ratesForDate.price) || 0;
                }
            }
            if (currentPrice <= 0) currentPrice = 0;
            selectedDynamicPrices[vid] = currentPrice;

            const priceEl = card.querySelector('.price-final');
            if (priceEl) {
                if (currentPrice > 0) {
                    priceEl.textContent = '₹' + Math.round(currentPrice).toLocaleString('en-IN');
                } else {
                    const unit = document.createElement('span');
                    unit.className = 'price-unit';
                    unit.textContent = 'N/A';
                    priceEl.textContent = '';
                    priceEl.appendChild(unit);
                }
            }
        });
    }

    document.addEventListener('click', (e) => {
        const selectBtn = e.target.closest('[data-action="select-vehicle"]');
        if (selectBtn) {
            e.preventDefault();

            const vid = selectBtn.getAttribute('data-id');
            const vname = selectBtn.getAttribute('data-name') || '';
            const vprice = parseFloat(selectBtn.getAttribute('data-price')) || 0;
            const vimage = selectBtn.getAttribute('data-image') || '';
            const vkm = selectBtn.getAttribute('data-km') || '';
            const vseats = selectBtn.getAttribute('data-seats') || '';
            const vbags = selectBtn.getAttribute('data-bags') || '';
            const vac = selectBtn.getAttribute('data-ac') || '';
            const vfuel = selectBtn.getAttribute('data-fuel') || '';
            const dynamicPrice = selectedDynamicPrices[vid] || vprice;

            if (dynamicPrice <= 0) {
                if (formMsg) {
                    formMsg.textContent = 'No rate available for this vehicle on the selected date.';
                    formMsg.className = 'm-cab-form-msg is-error is-active';
                }
                if (reviewMsg) {
                    reviewMsg.textContent = 'No rate available for this vehicle on the selected date.';
                    reviewMsg.className = 'm-cab-form-msg is-error is-active';
                }
                hidePriceBar();
                return;
            }

            selectedVehicle = {
                id: vid,
                name: vname,
                price: dynamicPrice,
                image: vimage,
                km: vkm,
                seats: vseats,
                bags: vbags,
                ac: vac,
                fuel: vfuel
            };

            document.querySelectorAll('.vehicle-action-btn').forEach((b) => {
                b.textContent = b.getAttribute('data-default-label') || 'SELECT';
                b.classList.remove('is-selected');
            });
            const defaultLabel = selectBtn.textContent.trim();
            if (!selectBtn.getAttribute('data-default-label')) {
                selectBtn.setAttribute('data-default-label', defaultLabel);
            }
            selectBtn.textContent = 'SELECTED';
            selectBtn.classList.add('is-selected');

            const isItinerary = searchDetails.type === 'itinerary';
            if (isItinerary) {
                chosenServiceType = null;
                if (reviewServiceGroup) {
                    reviewServiceGroup.querySelectorAll('input[name="review_service_type"]').forEach((r) => {
                        r.checked = false;
                    });
                }
            } else {
                chosenServiceType = searchDetails.type === 'hourly' ? 'Hourly' : 'Oneway';
            }

            setField('m-formVehicleId', selectedVehicle.id);
            setField('m-formVehicleName', selectedVehicle.name);
            setField('m-formServiceType', chosenServiceType || '');
            setField('m-formFinalPrice', selectedVehicle.price);
            setField('m-formPickup', searchDetails.pickup_location || '');
            setField('m-formDrop', searchDetails.drop_location || '');
            setField('m-formDate', searchDetails.travel_date || '');
            setField('m-formTime', searchDetails.travel_time || '');
            setField('m-formTripType', searchDetails.type || 'oneway');
            setField('m-formDuration', searchDetails.duration || '');
            setField('m-formSearchItinerary', searchDetails.itinerary_details || '');

            if (formMsg) {
                formMsg.textContent = '';
                formMsg.className = 'm-cab-form-msg';
            }
            if (reviewMsg) {
                reviewMsg.textContent = '';
                reviewMsg.className = 'm-cab-form-msg';
            }

            showPriceBar();
            return;
        }

        if (e.target.closest('[data-action="open-review"]')) {
            e.preventDefault();
            openReview();
            return;
        }

        if (e.target.closest('[data-action="close-review"]')) {
            e.preventDefault();
            closeReview();
            return;
        }

        if (e.target.closest('[data-action="open-enquiry"]')) {
            e.preventDefault();
            if (!selectedVehicle) return;
            if (searchDetails.type === 'itinerary' && !chosenServiceType) {
                if (reviewMsg) {
                    reviewMsg.textContent = 'Please select a service type.';
                    reviewMsg.className = 'm-cab-form-msg is-error is-active';
                }
                if (reviewServiceGroup) {
                    const firstRadio = reviewServiceGroup.querySelector('input');
                    if (firstRadio) firstRadio.focus();
                }
                return;
            }
            populateEnquirySummary();
            openEnquiry();
            return;
        }

        const closeBtn = e.target.closest('[data-action="close-checkout"]');
        if (closeBtn) {
            e.preventDefault();
            closeEnquiryToReview();
            return;
        }

        if (reviewModal && !reviewModal.hidden && e.target === reviewModal) {
            closeReview();
            return;
        }

        if (modal && !modal.hidden && e.target === modal) {
            closeEnquiryToReview();
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.matches('[data-action="set-service-type-review"]')) {
            chosenServiceType = e.target.value;
            setField('m-formServiceType', chosenServiceType || '');
            setTxt('mCabReviewService', chosenServiceType || '');
            if (reviewSubmitBtn) reviewSubmitBtn.disabled = !chosenServiceType;
            if (reviewMsg) {
                reviewMsg.textContent = '';
                reviewMsg.className = 'm-cab-form-msg';
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (!anyDialogOpen()) return;
        if (e.key === 'Escape') {
            e.preventDefault();
            if (isModalOpen(modal)) {
                closeEnquiryToReview();
            } else if (isModalOpen(reviewModal)) {
                closeReview();
            }
            return;
        }
        if (e.key === 'Tab') {
            const activeRoot = isModalOpen(modal) ? modal : reviewModal;
            const focusables = getFocusable(activeRoot);
            if (!focusables.length) return;
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }
    });

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!selectedVehicle) return;
            if (searchDetails.type === 'itinerary' && !chosenServiceType) {
                if (formMsg) {
                    formMsg.textContent = 'Please select a service type.';
                    formMsg.className = 'm-cab-form-msg is-error is-active';
                }
                return;
            }

            if (submitBtn) {
                submitBtn.textContent = 'Processing...';
                submitBtn.disabled = true;
            }

            const formData = new FormData(this);
            const firstName = formData.get('guest_name') || '';
            const lastName = formData.get('guest_last_name') || '';
            formData.set('guest_name', (firstName + ' ' + lastName).trim());

            const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
            const submitEndpoint = basePath
                ? basePath + '/api-submit-cab-booking.php'
                : 'api-submit-cab-booking.php';
            const voucherEndpoint = basePath
                ? basePath + '/api-download-cab-inquiry-slip.php'
                : 'api-download-cab-inquiry-slip.php';

            fetch(submitEndpoint, {
                method: 'POST',
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        if (formMsg) {
                            formMsg.textContent = 'Success! Generating inquiry voucher...';
                            formMsg.className = 'm-cab-form-msg is-success is-active';
                        }
                        hidePriceBar();
                        setTimeout(() => {
                            if (data.booking_id) {
                                window.open(voucherEndpoint + '?id=' + encodeURIComponent(data.booking_id), '_blank');
                            }
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (formMsg) {
                            formMsg.textContent = data.message || 'Submission failed. Please try again.';
                            formMsg.className = 'm-cab-form-msg is-error is-active';
                        }
                        if (submitBtn) {
                            submitBtn.textContent = 'Submit Inquiry';
                            submitBtn.disabled = false;
                        }
                    }
                })
                .catch(() => {
                    if (formMsg) {
                        formMsg.textContent = 'Unable to reach the server. Please check the network path.';
                        formMsg.className = 'm-cab-form-msg is-error is-active';
                    }
                    if (submitBtn) {
                        submitBtn.textContent = 'Submit Inquiry';
                        submitBtn.disabled = false;
                    }
                });
        });
    }

    // Toggle search form (pencil / edit)
    const editSearchBtn = document.getElementById('mCabEditSearchBtn');
    const searchSectionWrapper = document.getElementById('mCabSearchSectionWrapper');
    if (editSearchBtn && searchSectionWrapper) {
        editSearchBtn.addEventListener('click', () => {
            const nowHidden = searchSectionWrapper.classList.toggle('is-hidden');
            editSearchBtn.setAttribute('aria-expanded', nowHidden ? 'false' : 'true');
            if (!nowHidden) {
                const firstField = searchSectionWrapper.querySelector('input:not([disabled]), select:not([disabled])');
                if (firstField) firstField.focus();
            }
        });
    }
});
