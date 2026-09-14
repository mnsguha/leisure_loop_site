'use strict';

/**
 * mobile-hotel-detail.js
 * Dedicated module for the Mobile Hotel Detail page.
 * SRP: Only this page's interactions live here, keeping mobile-views.js clean.
 *
 * Frontend Rules compliance:
 * - Zero inline event handlers (all bound via addEventListener)
 * - All DOM queries guarded with null checks
 * - State toggled via classList, never element.style.display
 * - 'use strict' mode enforced
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Hero Image Slider (Scroll-snap + Dot Sync) ──────────────────
    const slider    = document.getElementById('mHdHeroSlider');
    const dotsWrap  = document.getElementById('mHdHeroDots');
    const dots      = dotsWrap ? dotsWrap.querySelectorAll('.mhd-hero-dot') : [];

    if (slider && dots.length > 0) {
        slider.addEventListener('scroll', () => {
            const slideWidth  = slider.offsetWidth;
            if (!slideWidth) return;
            const activeIndex = Math.round(slider.scrollLeft / slideWidth);
            dots.forEach((dot, idx) => {
                dot.classList.toggle('is-active', idx === activeIndex);
            });
        }, { passive: true });
    }

    // ── 2. Sticky Tab Navigation → Smooth Scroll ───────────────────────
    const tabNav = document.getElementById('mHdTabNav');
    if (tabNav) {
        tabNav.addEventListener('click', (e) => {
            const btn = e.target.closest('.mhd-tab-btn');
            if (!btn) return;

            // Update active state
            tabNav.querySelectorAll('.mhd-tab-btn').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            // Scroll to target section
            const targetId = btn.getAttribute('data-target');
            if (!targetId) return;
            const target = document.getElementById(targetId);
            if (!target) return;

            // Offset for sticky tab height (~48px) + bottom bar
            const tabHeight = tabNav.offsetHeight || 48;
            const topPos    = target.getBoundingClientRect().top + window.scrollY - tabHeight - 8;
        window.scrollTo({ top: topPos, behavior: 'smooth' });
        });
    }

    // ── 2b. Review badge button → scroll to reviews (delegated) ───────────
    document.addEventListener('click', (e) => {
        const reviewBtn = e.target.closest('[data-action="scroll-to-reviews"]');
        if (!reviewBtn) return;
        const reviewsSection = document.getElementById('mHdReviewsSection');
        if (!reviewsSection) return;
        const tabH   = tabNav ? tabNav.offsetHeight || 48 : 48;
        const topPos = reviewsSection.getBoundingClientRect().top + window.scrollY - tabH - 8;
        window.scrollTo({ top: topPos, behavior: 'smooth' });
        // Sync active tab indicator
        if (tabNav) {
            tabNav.querySelectorAll('.mhd-tab-btn').forEach(b => {
                b.classList.toggle('is-active', b.getAttribute('data-target') === 'mHdReviewsSection');
            });
        }
    });

    // ── 3. Auto-update active tab on scroll ────────────────────────────
    const sectionIds = ['mHdOverviewSection', 'mHdAmenitiesSection', 'mHdReviewsSection', 'mHdBookingSection'];
    const sections   = sectionIds.map(id => document.getElementById(id)).filter(Boolean);
    const tabBtns    = tabNav ? tabNav.querySelectorAll('.mhd-tab-btn') : [];

    if (sections.length > 0 && tabBtns.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    tabBtns.forEach(btn => {
                        btn.classList.toggle('is-active', btn.getAttribute('data-target') === id);
                    });
                }
            });
        }, { threshold: 0.4 });

        sections.forEach(sec => observer.observe(sec));
    }

    // ── 4. "SELECT ROOM" → scroll to booking form ──────────────────────
    const selectRoomBtn = document.getElementById('mHdSelectRoomBtn');
    if (selectRoomBtn) {
        selectRoomBtn.addEventListener('click', () => {
            const bookingSection = document.getElementById('mHdBookingSection');
            if (!bookingSection) return;
            bookingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // ── 5. Share Button ────────────────────────────────────────────────
    const shareBtn = document.getElementById('mHdShareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            const shareData = {
                title: document.title,
                url:   window.location.href,
            };
            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    await navigator.clipboard.writeText(window.location.href);
                    // Provide feedback using existing toast container
                    const toast = document.getElementById('mob-toastContainer');
                    if (toast) {
                        const msg = document.createElement('div');
                        msg.className = 'toast-msg';
                        msg.textContent = 'Link copied to clipboard!';
                        toast.appendChild(msg);
                        setTimeout(() => msg.remove(), 3000);
                    }
                }
            } catch (_) {
                // User cancelled share — no action needed
            }
        });
    }

    // ── 6. Read More / Property Overview Toggle ────────────────────────
    const readMoreBtn = document.getElementById('mHdReadMoreBtn');
    const descExcerpt = document.getElementById('mHdDescExcerpt');
    const descFull    = document.getElementById('mHdDescFull');

    if (readMoreBtn && descExcerpt && descFull) {
        readMoreBtn.addEventListener('click', () => {
            const isExpanded = readMoreBtn.getAttribute('aria-expanded') === 'true';
            if (isExpanded) {
                descFull.classList.add('is-hidden');
                descExcerpt.classList.remove('is-hidden');
                readMoreBtn.setAttribute('aria-expanded', 'false');
                readMoreBtn.querySelector('.mhd-bounce-arrow').textContent = 'keyboard_arrow_down';
            } else {
                descExcerpt.classList.add('is-hidden');
                descFull.classList.remove('is-hidden');
                readMoreBtn.setAttribute('aria-expanded', 'true');
                readMoreBtn.querySelector('.mhd-bounce-arrow').textContent = 'keyboard_arrow_up';
            }
        });
    }

    // ── 7. See All Amenities Expand ────────────────────────────────────
    const seeAllBtn = document.getElementById('mHdSeeAllAmenities');
    if (seeAllBtn) {
        seeAllBtn.addEventListener('click', () => {
            const grid      = document.querySelector('#mHdAmenitiesSection .mhd-amenity-grid');
            const isExpanded = seeAllBtn.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                seeAllBtn.setAttribute('aria-expanded', 'false');
                const allAmenities = seeAllBtn.dataset.allAmenities.split('||').filter(Boolean);
                const preview = allAmenities.slice(0, 4);
                if (grid) {
                    grid.innerHTML = preview.map(am => `
                        <div class="mhd-amenity-item">
                            <span class="material-symbols-outlined mhd-amenity-icon">check_circle</span>
                            <span class="mhd-amenity-label">${am.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</span>
                        </div>
                    `).join('');
                    grid.classList.remove('mhd-amenity-grid--expanded');
                }
                seeAllBtn.innerHTML = '<span class="material-symbols-outlined mhd-bounce-arrow">keyboard_arrow_down</span>';
            } else {
                const allAmenities = seeAllBtn.dataset.allAmenities.split('||').filter(Boolean);
                if (grid) {
                    grid.innerHTML = allAmenities.map(am => `
                        <div class="mhd-amenity-item">
                            <span class="material-symbols-outlined mhd-amenity-icon">check_circle</span>
                            <span class="mhd-amenity-label">${am.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</span>
                        </div>
                    `).join('');
                    grid.classList.add('mhd-amenity-grid--expanded');
                }
                seeAllBtn.setAttribute('aria-expanded', 'true');
                seeAllBtn.innerHTML = '<span class="material-symbols-outlined mhd-bounce-arrow">keyboard_arrow_up</span>';
            }
        });
    }

    // ── 7.5. Room Plan Selection ───────────────────────────────────────
    const planRadios = document.querySelectorAll('.mhd-plan-radio-input');
    const bottomPriceAmount = document.querySelector('.mhd-bottom-price .mhd-price-amount');
    const bottomPriceTaxes  = document.querySelector('.mhd-bottom-price .mhd-price-taxes');
    const bottomSelectBtn   = document.getElementById('mHdSelectRoomBtn');
    const reservationForm   = document.getElementById('mobileHotelDetailForm');

    if (planRadios.length > 0) {
        // Pre-select the first radio if none is selected
        let hasSelection = Array.from(planRadios).some(r => r.checked);
        if (!hasSelection) {
            planRadios[0].checked = true;
            updateBottomBarForPlan(planRadios[0]);
        }

        planRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    updateBottomBarForPlan(e.target);
                }
            });
        });
    }

    function updateBottomBarForPlan(radio) {
        if (!bottomPriceAmount || !bottomPriceTaxes) return;
        
        const price = parseInt(radio.dataset.price, 10);
        const tax   = parseInt(radio.dataset.tax, 10);
        const roomName = radio.dataset.roomName;
        const planName = radio.dataset.planName;
        
        if (!isNaN(price) && price > 0) {
            bottomPriceAmount.textContent = '₹' + price.toLocaleString('en-IN');
            bottomPriceTaxes.textContent = '+ ₹' + tax.toLocaleString('en-IN') + ' taxes & fees per night';
        } else {
            bottomPriceAmount.textContent = 'Price on Request';
            bottomPriceTaxes.textContent = '';
        }
        
        if (bottomSelectBtn) {
            bottomSelectBtn.textContent = 'CONTINUE';
            bottomSelectBtn.onclick = () => {
                const bookingSection = document.getElementById('mHdBookingSection');
                if (bookingSection) {
                    bookingSection.scrollIntoView({ behavior: 'smooth' });
                }
            };
        }

        // Add hidden fields to form if they don't exist, or update them
        if (reservationForm) {
            let roomInput = reservationForm.querySelector('input[name="selected_room"]');
            if (!roomInput) {
                roomInput = document.createElement('input');
                roomInput.type = 'hidden';
                roomInput.name = 'selected_room';
                reservationForm.appendChild(roomInput);
            }
            roomInput.value = roomName;

            let planInput = reservationForm.querySelector('input[name="selected_plan"]');
            if (!planInput) {
                planInput = document.createElement('input');
                planInput.type = 'hidden';
                planInput.name = 'selected_plan_name'; // Distinct from radio group name
                reservationForm.appendChild(planInput);
            }
            planInput.value = planName;
        }
    }

    // ── 8. Mobile Pickers (Date & Guest) & Dynamic Pricing ─────────────
    const datePill  = document.getElementById('mHdDatePill');
    const guestPill = document.getElementById('mHdGuestPill');
    const dpSheet   = document.getElementById('mDatePickerSheet');
    const gpSheet   = document.getElementById('mGuestPickerSheet');

    // Open Date Picker
    if (datePill && dpSheet) {
        datePill.addEventListener('click', () => {
            dpSheet.classList.remove('is-hidden');
            dpSheet.setAttribute('aria-hidden', 'false');
        });
    }

    // Open Guest Picker
    if (guestPill && gpSheet) {
        guestPill.addEventListener('click', () => {
            gpSheet.classList.remove('is-hidden');
            gpSheet.setAttribute('aria-hidden', 'false');
        });
    }

    // Close Modals
    document.addEventListener('click', (e) => {
        // Date Picker closers
        if (e.target.closest('#mDpCloseBtn')) {
            if (dpSheet) {
                if (document.activeElement) document.activeElement.blur();
                dpSheet.classList.add('is-hidden');
                dpSheet.setAttribute('aria-hidden', 'true');
            }
        }
        // Guest Picker closers
        if (e.target.closest('[data-action="close-guest-sheet"]')) {
            if (gpSheet) {
                if (document.activeElement) document.activeElement.blur();
                gpSheet.classList.add('is-hidden');
                gpSheet.setAttribute('aria-hidden', 'true');
            }
        }
    });

    // Dynamic Pricing Fetch
    function updateHotelPrice() {
        const hotelId = document.querySelector('input[name="hotel_id"]')?.value;
        const rooms   = document.getElementById('mGpRoomsSelect')?.value || 1;
        const adults  = document.getElementById('mGpAdultsSelect')?.value || 2;
        const children = document.getElementById('mGpChildrenSelect')?.value || 0;
        
        // Update guest pill visually
        if (guestPill) {
            const guestVal = guestPill.querySelector('.mhd-pill-val');
            if (guestVal) {
                const rText = rooms == 1 ? 'Room' : 'Rooms';
                const aText = adults == 1 ? 'Guest' : 'Guests';
                guestVal.textContent = `${rooms} ${rText}, ${adults} ${aText}...`;
            }
        }

        // Sync hidden form inputs
        const formRooms = document.getElementById('mHdFormRooms');
        const formAdults = document.getElementById('mHdFormAdults');
        if (formRooms) formRooms.value = rooms;
        if (formAdults) formAdults.value = adults;

        if (!hotelId) return;

        const formCheckIn = document.getElementById('mHdFormCheckIn');
        const formCheckOut = document.getElementById('mHdFormCheckOut');
        
        const formData = new FormData();
        formData.append('hotel_id', hotelId);
        formData.append('rooms', rooms);
        formData.append('adults', adults);
        formData.append('children', children);
        if (formCheckIn && formCheckIn.value) formData.append('check_in', formCheckIn.value);
        if (formCheckOut && formCheckOut.value) formData.append('check_out', formCheckOut.value);

        fetch('api/hotel-price.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                const amountEl = document.querySelector('.mhd-price-amount');
                const taxesEl  = document.querySelector('.mhd-price-taxes');
                if (amountEl) amountEl.textContent = '₹' + res.data.formatted_tariff;
                if (taxesEl)  taxesEl.textContent = '+ ₹' + res.data.formatted_tax + ' taxes & fees per night';
            }
        })
        .catch(err => console.error('Pricing update failed:', err));
    }

    // Date Selection Logic
    const calendarBody = document.getElementById('mDpCalendarBody');
    const initCheckIn = document.getElementById('mHdFormCheckIn')?.value;
    const initCheckOut = document.getElementById('mHdFormCheckOut')?.value;
    let checkInDate = initCheckIn || null;
    let checkOutDate = initCheckOut || null;

    function formatDateForDisplay(dateStr) {
        if (!dateStr) return 'Select Date';
        const d = new Date(dateStr);
        const day = d.getDate();
        const month = d.toLocaleString('en-US', { month: 'short' });
        const weekday = d.toLocaleString('en-US', { weekday: 'short' });
        const year = d.getFullYear();
        return `<span style="font-size:1.1rem;font-weight:800;display:block;">${day} ${month}</span><span style="font-size:0.75rem;font-weight:500;color:rgba(255,255,255,0.6);display:block;margin-top:2px;">${weekday}, ${year}</span>`;
    }

    function calculateNights(start, end) {
        if (!start || !end) return 0;
        const s = new Date(start);
        const e = new Date(end);
        return Math.max(0, Math.round((e - s) / (1000 * 60 * 60 * 24)));
    }

    if (calendarBody) {
        const checkInVal = document.getElementById('mDpCheckInVal');
        const checkOutVal = document.getElementById('mDpCheckOutVal');
        const checkInBox = document.getElementById('mDpCheckInBox');
        const checkOutBox = document.getElementById('mDpCheckOutBox');
        const nightPill = document.getElementById('mDpNightPill');
        const resetBtn = document.getElementById('mDpResetBtn');

        function updateCalendarUI() {
            const allDays = Array.from(calendarBody.querySelectorAll('.mhd-dp-day:not(.mhd-dp-empty)'));
            allDays.forEach(d => {
                d.classList.remove('is-selected', 'is-range', 'is-checkout');
                const dDateStr = d.getAttribute('data-date');
                const dDate = new Date(dDateStr);

                if (checkInDate && dDateStr === checkInDate) {
                    d.classList.add('is-selected');
                }
                
                if (checkOutDate) {
                    if (dDateStr === checkOutDate) {
                        d.classList.add('is-checkout');
                    }
                    const checkInD = new Date(checkInDate);
                    const checkOutD = new Date(checkOutDate);
                    if (dDate > checkInD && dDate < checkOutD) {
                        d.classList.add('is-range');
                    }
                } else if (checkInDate) {
                    // Disable dates before check-in if checkOut is not yet selected
                    const checkInD = new Date(checkInDate);
                    if (dDate < checkInD) {
                        d.style.opacity = '0.3';
                        d.style.pointerEvents = 'none';
                    } else {
                        d.style.opacity = '1';
                        d.style.pointerEvents = 'auto';
                    }
                }
                
                if (!checkInDate && !checkOutDate) {
                    d.style.opacity = '1';
                    d.style.pointerEvents = 'auto';
                }
            });

            if (checkInVal) checkInVal.innerHTML = checkInDate ? formatDateForDisplay(checkInDate) : 'Select Date';
            if (checkOutVal) checkOutVal.innerHTML = checkOutDate ? formatDateForDisplay(checkOutDate) : 'Select Date';
            
            if (checkInBox) checkInBox.classList.toggle('is-active', !checkInDate || (checkInDate && checkOutDate));
            if (checkOutBox) checkOutBox.classList.toggle('is-active', checkInDate && !checkOutDate);
            
            if (nightPill) {
                const nights = calculateNights(checkInDate, checkOutDate);
                nightPill.textContent = nights === 1 ? '1 NIGHT' : `${nights} NIGHTS`;
            }

            if (datePill && checkInDate && checkOutDate) {
                const datePillVal = datePill.querySelector('.mhd-pill-val');
                if (datePillVal) {
                    const inD = new Date(checkInDate);
                    const outD = new Date(checkOutDate);
                    const inStr = inD.getDate() + ' ' + inD.toLocaleString('en-US', {month: 'short'});
                    const outStr = outD.getDate() + ' ' + outD.toLocaleString('en-US', {month: 'short'});
                    datePillVal.textContent = `${inStr} - ${outStr}`;
                }
                
                // Sync hidden forms
                const formCheckIn = document.getElementById('mHdFormCheckIn');
                const formCheckOut = document.getElementById('mHdFormCheckOut');
                if (formCheckIn) formCheckIn.value = checkInDate;
                if (formCheckOut) formCheckOut.value = checkOutDate;
            }
        }

        calendarBody.addEventListener('click', (e) => {
            const dayBtn = e.target.closest('.mhd-dp-day:not(.mhd-dp-empty)');
            if (dayBtn) {
                const dateStr = dayBtn.getAttribute('data-date');
                
                if (!checkInDate || (checkInDate && checkOutDate)) {
                    // Start new range
                    checkInDate = dateStr;
                    checkOutDate = null;
                } else if (checkInDate && !checkOutDate) {
                    // Pick checkout
                    const d1 = new Date(checkInDate);
                    const d2 = new Date(dateStr);
                    if (d2 > d1) {
                        checkOutDate = dateStr;
                    } else {
                        checkInDate = dateStr; // Reset if clicked before checkin
                    }
                }
                updateCalendarUI();
            }
        });

        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                checkInDate = null;
                checkOutDate = null;
                updateCalendarUI();
            });
        }
        
        // Init UI on load
        updateCalendarUI();
    }

    // Done Buttons
    const dpDoneBtn = document.getElementById('mDpDoneBtn');
    const gpDoneBtn = document.getElementById('mGpDoneBtn');

    if (dpDoneBtn) {
        dpDoneBtn.addEventListener('click', () => {
            if (dpSheet) {
                if (document.activeElement) document.activeElement.blur();
                dpSheet.classList.add('is-hidden');
                dpSheet.setAttribute('aria-hidden', 'true');
            }
            // Add date selection logic here when actual calendar is built
            updateHotelPrice();
        });
    }

    if (gpDoneBtn) {
        gpDoneBtn.addEventListener('click', () => {
            if (gpSheet) {
                if (document.activeElement) document.activeElement.blur();
                gpSheet.classList.add('is-hidden');
                gpSheet.setAttribute('aria-hidden', 'true');
            }
            updateHotelPrice();
        });
    }

    // ── 9. Wishlist / Favourite Button toggle ──────────────────────────
    const wishlistBtn = document.getElementById('mHdWishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', () => {
            const icon = wishlistBtn.querySelector('.material-symbols-outlined');
            if (!icon) return;
            const isSaved = icon.textContent === 'favorite';
            icon.textContent        = isSaved ? 'favorite_border' : 'favorite';
            icon.classList.toggle('mhd-wishlist-saved', !isSaved);
            wishlistBtn.setAttribute('aria-label', isSaved ? 'Save to wishlist' : 'Saved to wishlist');
        });
    }

    // ── 10. Reservation Form Submission ───────────────────────────────
    const detailForm = document.getElementById('mobileHotelDetailForm');
    const formMsg    = document.getElementById('mHdFormMsg');

    if (detailForm) {
        detailForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = detailForm.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Sending...';
            }

            const formData = new FormData(detailForm);

            fetch(detailForm.action, {
                method: 'POST',
                body:   formData,
            })
            .then(res => res.json())
            .then(data => {
                if (formMsg) {
                    formMsg.textContent = data.message ?? 'Thank you! We will contact you shortly.';
                    formMsg.classList.remove('msg--error');
                    formMsg.classList.add('msg--gold');
                }
                detailForm.reset();
            })
            .catch(() => {
                if (formMsg) {
                    formMsg.textContent = 'Something went wrong. Please try again.';
                    formMsg.classList.add('msg--error');
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'RESERVE NOW';
                }
            });
        });
    }

});
