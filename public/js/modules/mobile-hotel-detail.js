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

    // ── 1. Hero Image Slider & Gallery Modal ──────────────────────────
    const slider    = document.getElementById('mHdHeroSlider');
    const dotsWrap  = document.getElementById('mHdHeroDots');
    const dots      = dotsWrap ? dotsWrap.querySelectorAll('.mhd-hero-dot') : [];
    let heroAutoSlideInterval = null;

    const startAutoSlide = () => {
        if (!slider) return;
        heroAutoSlideInterval = setInterval(() => {
            const slideWidth = slider.offsetWidth;
            if (!slideWidth) return;
            let nextScroll = slider.scrollLeft + slideWidth;
            
            // If we reach the end, jump back to start
            if (nextScroll >= slider.scrollWidth - 10) {
                nextScroll = 0;
            }
            slider.scrollTo({ left: nextScroll, behavior: 'smooth' });
        }, 4000); // 4 seconds
    };

    const stopAutoSlide = () => {
        if (heroAutoSlideInterval) clearInterval(heroAutoSlideInterval);
    };

    if (slider && dots.length > 0) {
        slider.addEventListener('scroll', () => {
            const slideWidth  = slider.offsetWidth;
            if (!slideWidth) return;
            const activeIndex = Math.round(slider.scrollLeft / slideWidth);
            dots.forEach((dot, idx) => {
                dot.classList.toggle('is-active', idx === activeIndex);
            });
        }, { passive: true });

        // Start sliding and pause on touch
        startAutoSlide();
        slider.addEventListener('touchstart', stopAutoSlide, { passive: true });
        slider.addEventListener('touchend', startAutoSlide, { passive: true });
    }

    // Bento Gallery Modal Logic
    const galleryModal = document.getElementById('mHdGalleryModal');
    const galleryCloseBtn = document.getElementById('mHdGalleryCloseBtn');
    
    if (slider && galleryModal) {
        // Open gallery when tapping the slider
        slider.addEventListener('click', () => {
            galleryModal.classList.remove('is-hidden');
            galleryModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden'; // prevent bg scroll
            stopAutoSlide(); // pause slider when gallery open
        });
        
        // Close gallery
        if (galleryCloseBtn) {
            galleryCloseBtn.addEventListener('click', () => {
                galleryModal.classList.add('is-hidden');
                galleryModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                startAutoSlide();
            });
        }
    }

    // ── Fullscreen Lightbox Slider Logic ──────────────────────────────
    const lightboxModal = document.getElementById('mHdLightbox');
    const lightboxCloseBtn = document.getElementById('mHdLightboxCloseBtn');
    const lightboxSlider = document.getElementById('mHdLightboxSlider');
    const lightboxCounter = document.getElementById('mHdLightboxCounter');
    
    if (galleryModal && lightboxModal && lightboxSlider) {
        let totalLightboxSlides = 0;
        
        // Event delegation for bento items
        galleryModal.addEventListener('click', (e) => {
            const bentoItem = e.target.closest('.mhd-bento-item');
            if (!bentoItem) return;
            
            const indexStr = bentoItem.getAttribute('data-index');
            if (indexStr === null) return;
            
            const idx = parseInt(indexStr, 10);
            
            // Show lightbox
            lightboxModal.classList.remove('is-hidden');
            lightboxModal.setAttribute('aria-hidden', 'false');
            
            // Wait for modal to be visible before scrolling to the exact slide
            requestAnimationFrame(() => {
                const slideWidth = lightboxSlider.offsetWidth;
                if (slideWidth > 0) {
                    lightboxSlider.scrollTo({ left: slideWidth * idx, behavior: 'instant' });
                }
            });
        });
        
        // Count total slides for pagination text
        const slides = lightboxSlider.querySelectorAll('.mhd-lightbox-slide');
        totalLightboxSlides = slides.length;
        
        // Scroll listener to update counter
        lightboxSlider.addEventListener('scroll', () => {
            const slideWidth = lightboxSlider.offsetWidth;
            if (!slideWidth) return;
            const activeIndex = Math.round(lightboxSlider.scrollLeft / slideWidth);
            if (lightboxCounter) {
                lightboxCounter.textContent = `${activeIndex + 1} / ${totalLightboxSlides}`;
            }
        }, { passive: true });
        
        // Close logic
        const closeLightbox = () => {
            lightboxModal.classList.add('is-hidden');
            lightboxModal.setAttribute('aria-hidden', 'true');
            // Keep body locked because we return to the Bento gallery which also locks body
        };
        
        if (lightboxCloseBtn) {
            lightboxCloseBtn.addEventListener('click', closeLightbox);
        }
        
        // Escape key to close Lightbox (if open) or Gallery (if only gallery is open)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const isLightboxOpen = !lightboxModal.classList.contains('is-hidden');
                const isGalleryOpen = !galleryModal.classList.contains('is-hidden');
                
                if (isLightboxOpen) {
                    closeLightbox();
                } else if (isGalleryOpen) {
                    galleryModal.classList.add('is-hidden');
                    galleryModal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    if (typeof startAutoSlide === 'function') startAutoSlide();
                }
            }
        });
    }

    // ── Room Category Image Gallery Logic ─────────────────────────────
    const roomGalleryModal = document.getElementById('mHdRoomGalleryModal');
    const roomGalleryCloseBtn = document.getElementById('mHdRoomGalleryCloseBtn');
    const roomGallerySlider = document.getElementById('mHdRoomGallerySlider');
    const roomGalleryCounter = document.getElementById('mHdRoomGalleryCounter');
    const roomGalleryTitle = document.getElementById('mHdRoomGalleryTitle');
    const roomGalleryOverlay = document.getElementById('mHdRoomGalleryOverlay');

    if (roomGalleryModal && roomGallerySlider) {
        let totalRoomSlides = 0;
        
        // Listen for clicks on any room image wrapper
        document.body.addEventListener('click', (e) => {
            const roomImgWrap = e.target.closest('.mhd-room-img-wrap');
            if (!roomImgWrap) return;
            
            const roomName = roomImgWrap.getAttribute('data-room-name') || 'Room Gallery';
            const imagesJson = roomImgWrap.getAttribute('data-images');
            
            if (!imagesJson) return;
            
            try {
                const images = JSON.parse(imagesJson);
                if (images.length === 0) return;
                
                totalRoomSlides = images.length;
                
                // Populate slider
                roomGallerySlider.innerHTML = images.map(img => 
                    `<div class="mhd-room-gallery-slide"><img src="${img}" alt="Room photo"></div>`
                ).join('');
                
                if (roomGalleryTitle) roomGalleryTitle.textContent = roomName;
                if (roomGalleryCounter) roomGalleryCounter.textContent = `1 / ${totalRoomSlides}`;
                
                // Reset scroll
                roomGallerySlider.scrollLeft = 0;
                
                // Show modal
                roomGalleryModal.classList.remove('is-hidden');
                roomGalleryModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            } catch (err) {
                console.error("Failed to parse room images", err);
            }
        });
        
        // Update counter on scroll
        roomGallerySlider.addEventListener('scroll', () => {
            const slideWidth = roomGallerySlider.offsetWidth;
            if (!slideWidth) return;
            const activeIndex = Math.round(roomGallerySlider.scrollLeft / slideWidth);
            if (roomGalleryCounter) {
                roomGalleryCounter.textContent = `${activeIndex + 1} / ${totalRoomSlides}`;
            }
        }, { passive: true });
        
        // Close modal
        const closeRoomGallery = () => {
            roomGalleryModal.classList.add('is-hidden');
            roomGalleryModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // Clear DOM after transition to avoid ghosting
            setTimeout(() => {
                roomGallerySlider.innerHTML = '';
            }, 300);
        };
        
        if (roomGalleryCloseBtn) roomGalleryCloseBtn.addEventListener('click', closeRoomGallery);
        if (roomGalleryOverlay) roomGalleryOverlay.addEventListener('click', closeRoomGallery);
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !roomGalleryModal.classList.contains('is-hidden')) {
                closeRoomGallery();
            }
        });
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
        // Pre-select the radio with the lowest price if none is selected
        let hasSelection = Array.from(planRadios).some(r => r.checked);
        if (!hasSelection) {
            let lowestRadio = planRadios[0];
            let lowestPrice = parseInt(lowestRadio.dataset.price, 10) || Infinity;
            
            planRadios.forEach(radio => {
                let p = parseInt(radio.dataset.price, 10);
                if (!isNaN(p) && p > 0 && p < lowestPrice) {
                    lowestPrice = p;
                    lowestRadio = radio;
                }
            });
            
            lowestRadio.checked = true;
            updateBottomBarForPlan(lowestRadio);
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
                const pdModal = document.getElementById('mHdPriceDetailsModal');
                if (pdModal) {
                    const pdRoomName = document.getElementById('mHdDispRoomName');
                    const pdPlanName = document.getElementById('mHdDispPlanName');
                    const pdNetRate = document.getElementById('mHdSidebarNetRate');
                    const pdTotalTax = document.getElementById('mHdSidebarTotalTax');
                    const pdTotalFinal = document.getElementById('mHdSidebarTotalFinal');
                    
                    if (pdRoomName) pdRoomName.textContent = roomName;
                    
                    const numRooms = parseInt(document.getElementById('mHdRoomsCounter')?.textContent || '1', 10);
                    const adults = parseInt(document.getElementById('mHdAdultsCounter')?.textContent || '2', 10);
                    const children = parseInt(document.getElementById('mHdChildrenCounter')?.textContent || '0', 10);
                    
                    if (pdPlanName) {
                        let paxStrFull = `${adults} Adult${adults > 1 ? 's' : ''}`;
                        if (children > 0) {
                            paxStrFull += `, ${children} Child${children > 1 ? 'ren' : ''}`;
                        }
                        const roomStr = `${numRooms} Room${numRooms > 1 ? 's' : ''}`;
                        pdPlanName.textContent = `${paxStrFull} | ${roomStr} | ${planName}`;
                    }

                    const checkInInput = document.getElementById('mHdFormCheckIn')?.value;
                    const checkOutInput = document.getElementById('mHdFormCheckOut')?.value;
                    
                    let numNights = 1;
                    if (checkInInput && checkOutInput) {
                        const inDate = new Date(checkInInput);
                        const outDate = new Date(checkOutInput);
                        if (!isNaN(inDate) && !isNaN(outDate) && outDate > inDate) {
                            numNights = Math.round((outDate - inDate) / (1000 * 60 * 60 * 24));
                        }
                        const dispCheckIn = document.getElementById('mHdSidebarCheckIn');
                        const dispCheckOut = document.getElementById('mHdSidebarCheckOut');
                        if (dispCheckIn) dispCheckIn.textContent = inDate.toLocaleDateString('en-GB', { month: 'short', day: 'numeric' });
                        if (dispCheckOut) dispCheckOut.textContent = outDate.toLocaleDateString('en-GB', { month: 'short', day: 'numeric' });
                    }
                    
                    const dispNights = document.getElementById('mHdSidebarNights');
                    if (dispNights) dispNights.textContent = numNights + 'N';
                    
                    const totalNet = price * numRooms * numNights;
                    const totalGst = tax * numRooms * numNights;
                    const finalPayable = totalNet + totalGst;
                    
                    if (pdNetRate) pdNetRate.textContent = '₹' + totalNet.toLocaleString('en-IN');
                    if (pdTotalTax) pdTotalTax.textContent = '+ ₹' + totalGst.toLocaleString('en-IN') + ' Taxes & fees';
                    if (pdTotalFinal) pdTotalFinal.textContent = '₹' + finalPayable.toLocaleString('en-IN');
                    
                    // Update form values for checkout
                    const formRooms = document.getElementById('mHdFormRooms');
                    const formFinalPrice = document.getElementById('mHdFormFinalPrice');
                    const formRoomId = document.getElementById('mHdFormRoomId');
                    const formPlanId = document.getElementById('mHdFormPlanId');
                    
                    if (formRooms) formRooms.value = numRooms;
                    if (formFinalPrice) formFinalPrice.value = finalPayable;
                    if (formRoomId) formRoomId.value = radio.dataset.roomId || '';
                    if (formPlanId) formPlanId.value = radio.value || ''; // radio.value is the plan id
                    
                    pdModal.classList.remove('is-hidden');
                    document.body.style.overflow = 'hidden';
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

        fetch('api-hotel-price.php', {
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
    
    // ── Price Details Modal Logic ──────────────────────────────────────────
    const pdModal = document.getElementById('mHdPriceDetailsModal');
    const pdOverlay = document.getElementById('mHdPriceDetailsOverlay');
    const pdCloseBtn = document.getElementById('mHdPriceDetailsCloseBtn');
    const pdProceedBtn = document.getElementById('mHdBtnProceed');
    
    const coModal = document.getElementById('mHdCheckoutModal');
    const coCloseBtn = document.getElementById('mHdCheckoutCloseBtn');
    
    const closePdModal = () => {
        if (pdModal) {
            pdModal.classList.add('is-hidden');
            document.body.style.overflow = '';
        }
    };
    
    if (pdCloseBtn) pdCloseBtn.addEventListener('click', closePdModal);
    if (pdOverlay) pdOverlay.addEventListener('click', closePdModal);
    
    if (pdProceedBtn) {
        pdProceedBtn.addEventListener('click', () => {
            // Close Price Details, Open Checkout
            if (pdModal) pdModal.classList.add('is-hidden');
            if (coModal) coModal.classList.remove('is-hidden');
            
            // Sync guest counts from guest picker if applicable
            const mainAdults = document.getElementById('mHdAdultsCounter');
            const mainChildren = document.getElementById('mHdChildrenCounter');
            const formAdults = document.getElementById('mHdFormAdults');
            const formChildren = document.getElementById('mHdFormChildren');
            if (mainAdults && formAdults) formAdults.value = mainAdults.textContent;
            if (mainChildren && formChildren) formChildren.value = mainChildren.textContent;
            
            // Also ensure selected_room and selected_plan_name are updated in form
            const formSelectedRoom = document.getElementById('mHdFormSelectedRoom');
            const formSelectedPlan = document.getElementById('mHdFormSelectedPlanName');
            const pdRoomName = document.getElementById('mHdDispRoomName');
            const pdPlanName = document.getElementById('mHdDispPlanName');
            if (formSelectedRoom && pdRoomName) formSelectedRoom.value = pdRoomName.textContent;
            if (formSelectedPlan && pdPlanName) formSelectedPlan.value = pdPlanName.textContent;

            // Update Checkout Enquiry Summary Cards
            const chkCheckIn = document.getElementById('mHdCheckoutSummaryCheckIn');
            const chkCheckOut = document.getElementById('mHdCheckoutSummaryCheckOut');
            const chkNights = document.getElementById('mHdCheckoutSummaryNights');
            const chkGuests = document.getElementById('mHdCheckoutSummaryGuests');
            const chkRoom = document.getElementById('mHdCheckoutSummaryRoomName');
            const chkPlan = document.getElementById('mHdCheckoutSummaryPlanName');
            const chkBase = document.getElementById('mHdCheckoutSummaryBasePrice');
            const chkTax = document.getElementById('mHdCheckoutSummaryTaxes');
            const chkTotal = document.getElementById('mHdCheckoutSummaryTotal');
            
            if (chkCheckIn) chkCheckIn.textContent = document.getElementById('mHdSidebarCheckIn')?.textContent || '--';
            if (chkCheckOut) chkCheckOut.textContent = document.getElementById('mHdSidebarCheckOut')?.textContent || '--';
            
            if (chkNights) {
                const nText = document.getElementById('mHdSidebarNights')?.textContent || '1N';
                const nNum = parseInt(nText, 10) || 1;
                chkNights.textContent = nNum > 1 ? `${nNum} Nights` : `${nNum} Night`;
            }
            
            if (chkGuests) {
                const aCnt = parseInt(document.getElementById('mHdAdultsCounter')?.textContent || '2', 10);
                const rCnt = parseInt(document.getElementById('mHdRoomsCounter')?.textContent || '1', 10);
                const aStr = aCnt > 1 ? `${aCnt} Adults` : `${aCnt} Adult`;
                const rStr = rCnt > 1 ? `${rCnt} Rooms` : `${rCnt} Room`;
                chkGuests.textContent = `${aStr} • ${rStr}`;
            }
            
            if (chkRoom) chkRoom.textContent = document.getElementById('mHdDispRoomName')?.textContent || '--';
            if (chkPlan) chkPlan.textContent = document.getElementById('mHdDispPlanName')?.textContent || '--';
            if (chkBase) chkBase.textContent = document.getElementById('mHdSidebarNetRate')?.textContent || '--';
            if (chkTax) chkTax.textContent = document.getElementById('mHdSidebarTotalTax')?.textContent || 'Included';
            if (chkTotal) chkTotal.textContent = document.getElementById('mHdSidebarTotalFinal')?.textContent || '--';
        });
    }
    
    const closeCoModal = () => {
        if (coModal) {
            coModal.classList.add('is-hidden');
            document.body.style.overflow = '';
        }
    };
    
    if (coCloseBtn) coCloseBtn.addEventListener('click', closeCoModal);

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

            // Combine First Name and Last Name
            const firstName = formData.get('guest_name') || '';
            const lastName = formData.get('guest_last_name') || '';
            formData.set('guest_name', `${firstName} ${lastName}`.trim());

            fetch(detailForm.action, {
                method: 'POST',
                body:   formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (formMsg) {
                        formMsg.textContent = data.message ?? 'Success! Downloading slip...';
                        formMsg.classList.remove('msg--error');
                        formMsg.classList.add('msg--gold');
                    }
                    
                    if (data.booking_id) {
                        window.open('api-download-hotel-inquiry-slip.php?id=' + data.booking_id, '_blank');
                    }
                    
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    if (formMsg) {
                        formMsg.textContent = data.message || 'Something went wrong. Please try again.';
                        formMsg.classList.add('msg--error');
                        formMsg.classList.remove('msg--gold');
                    }
                    if (submitBtn) {
                        submitBtn.disabled    = false;
                        submitBtn.textContent = 'RESERVE NOW';
                    }
                }
            })
            .catch(() => {
                if (formMsg) {
                    formMsg.textContent = 'Network error. Please try again.';
                    formMsg.classList.add('msg--error');
                    formMsg.classList.remove('msg--gold');
                }
                if (submitBtn) {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'RESERVE NOW';
                }
            });
        });
    }

});
