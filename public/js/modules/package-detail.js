/* Extracted from package-detail.php */
// --- Day-by-Day Itinerary Accordion Handler ---
document.addEventListener('click', function(e) {
    const trigger = e.target.closest('.glass-acc-trigger');
    if (!trigger) return;

    e.preventDefault();
    const item = trigger.closest('.glass-acc-item');
    if (!item) return;

    const body = item.querySelector('.glass-acc-body');
    const isAlreadyOpen = trigger.classList.contains('is-active');

    // Close all other items
    document.querySelectorAll('.glass-acc-item').forEach(otherItem => {
        const otherTrigger = otherItem.querySelector('.glass-acc-trigger');
        const otherBody = otherItem.querySelector('.glass-acc-body');
        if (otherTrigger) otherTrigger.classList.remove('is-active');
        if (otherBody) otherBody.classList.remove('is-active');
    });

    // Toggle clicked item
    if (!isAlreadyOpen) {
        trigger.classList.add('is-active');
        if (body) body.classList.add('is-active');

        // Trigger route map focus if map function exists
        const coords = trigger.dataset.coords;
        if (coords && coords.trim() && typeof animateCarTo === 'function') {
            const p = coords.split(',');
            if (p.length === 2) animateCarTo(parseFloat(p[0]), parseFloat(p[1]));
        }
    }
});


document.addEventListener('DOMContentLoaded', function() {
                const wrapper = document.getElementById('tc-content-wrapper');
                const btn = document.getElementById('tc-toggle-btn');
                const btnText = document.getElementById('tc-btn-text');
                const btnIcon = document.getElementById('tc-btn-icon');
                const fade = document.getElementById('tc-fade-overlay');

                if (!wrapper || !btn) return;

                // Adjust dynamically in case content is actually shorter than 180px
                if (wrapper.scrollHeight <= 180) {
                    wrapper.style.maxHeight = 'none';
                    btn.style.display = 'none';
                    fade.style.opacity = '0';
                    return;
                }

                btn.addEventListener('click', function() {
                    const isCollapsed = wrapper.style.maxHeight === '180px' || wrapper.style.maxHeight === '';
                    if (isCollapsed) {
                        wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
                        btnText.textContent = 'Read Less';
                        btnIcon.style.transform = 'rotate(180deg)';
                        fade.style.opacity = '0';
                    } else {
                        wrapper.style.maxHeight = '180px';
                        btnText.textContent = 'Read More';
                        btnIcon.style.transform = 'rotate(0deg)';
                        fade.style.opacity = '1';
                    }
                });
            });

/* Extracted from package-detail.php */
let currentTranslate = 0;
        function scrollRelated(direction) {
            const grid = document.getElementById('relatedGrid');
            if (!grid) return;
            const card = grid.querySelector('.related-card');
            if (!card) return;
            
            const cardWidth = card.getBoundingClientRect().width;
            const gap = parseFloat(window.getComputedStyle(grid).gap) || 0;
            const scrollAmount = cardWidth + gap;
            
            const maxScroll = -(grid.scrollWidth - grid.clientWidth);
            currentTranslate += direction * -scrollAmount;
            
            if (currentTranslate > 0) currentTranslate = 0;
            if (currentTranslate < maxScroll) currentTranslate = maxScroll;
            
            grid.style.transform = `translateX(${currentTranslate}px)`;
        }

/* Extracted from package-detail.php */
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("luxuryForm");
    const container = document.getElementById("formContainer");

    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            
            // Render glowing loader state on the button
            const btn = form.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "COMMUNICATING CONCIERGE...";
            btn.style.boxShadow = "0 0 30px var(--gold)";

            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Success state in Obsidian/Gold Card
                    container.innerHTML = `
                        <div class="form-success-card">
                            <div class="form-success-icon">✓</div>
                            <h3 class="serif" style="font-size: 1.6rem; color: white; margin-bottom: 0.8rem; font-weight: 500;">Connection Secure</h3>
                            <p style="color: var(--gold); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 1.5rem;">Lead Synchronized</p>
                            <p style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.7; margin-bottom: 2rem;">
                                Thank you. Your luxury curation request has been received. Our dedicated travel host will call you shortly to outline the next steps.
                            </p>
                            <button id="btnReloadEscape" class="btn-outline" style="padding: 0.8rem 2rem; font-size: 0.8rem; letter-spacing: 0.08em; border-radius: 8px;">
                                SUBMIT NEW ESCAPE
                            </button>
                        </div>
                    `;
                    setTimeout(() => {
                        const btnReload = document.getElementById('btnReloadEscape');
                        if (btnReload) btnReload.addEventListener('click', () => window.location.reload());
                    }, 50);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.boxShadow = "none";
                    alert(data.message || "An unexpected issue occurred. Please try again.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.style.boxShadow = "none";
                alert("Network communication error. Please try again.");
            });
        });
    }
});

/* Extracted from package-detail.php */
// ── Itinerary data from PHP ──────────────────────────────────────
            const pkgDataStore = document.getElementById('pkg-data-store');
            const itineraryData = pkgDataStore ? JSON.parse(pkgDataStore.dataset.itinerary || '[]') : [];
            let fallbackCoords = [27.3314, 88.6138];
            if (pkgDataStore && pkgDataStore.dataset.coords) {
                const parts = pkgDataStore.dataset.coords.split(',');
                if (parts.length === 2) fallbackCoords = [parseFloat(parts[0]), parseFloat(parts[1])];
            }

            // ── Build waypoints list ─────────────────────────────────────────
            const waypoints = itineraryData.map((d, i) => {
                if (d.coords && d.coords.trim()) {
                    const p = d.coords.split(',');
                    if (p.length === 2) return { lat: parseFloat(p[0]), lng: parseFloat(p[1]), title: d.title || ('Day '+(i+1)) };
                }
                return null;
            }).filter(Boolean);

            // ── Map init ─────────────────────────────────────────────────────
            let map, carMarker, routeLine, destMarkers = [];

            document.addEventListener('DOMContentLoaded', () => {
                if (!document.getElementById('journey-map')) return;

                const center = waypoints.length ? [waypoints[0].lat, waypoints[0].lng] : fallbackCoords;
                map = L.map('journey-map', { center, zoom: 9, scrollWheelZoom: false });

                // Dark style tile layer
                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap, &copy; CartoDB'
                }).addTo(map);

                if (waypoints.length > 0) {
                    // Draw dashed gold route line
                    const latlngs = waypoints.map(w => [w.lat, w.lng]);
                    routeLine = L.polyline(latlngs, {
                        color: '#C5A059',
                        weight: 2,
                        opacity: 0.6,
                        dashArray: '8, 10'
                    }).addTo(map);

                    // Destination label markers
                    waypoints.forEach((wp, i) => {
                        const pin = L.divIcon({
                            className: '',
                            html: `<div style="background:rgba(7,12,24,0.9);border:1px solid rgba(197,160,89,0.5);color:#C5A059;padding:4px 10px;border-radius:20px;font-size:11px;font-family:Inter,sans-serif;white-space:nowrap;">${wp.title}</div>`,
                            iconAnchor: [0, 0]
                        });
                        destMarkers.push(L.marker([wp.lat, wp.lng], { icon: pin }).addTo(map));
                    });

                    // Car marker at start
                    const carIcon = L.divIcon({
                        className: '',
                        html: `<div id="car-icon" style="font-size:26px;filter:drop-shadow(0 0 6px rgba(197,160,89,0.8));transform:rotate(90deg);">🚗</div>`,
                        iconAnchor: [13, 13]
                    });
                    carMarker = L.marker([waypoints[0].lat, waypoints[0].lng], { icon: carIcon, zIndexOffset: 1000 }).addTo(map);

                    map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
                }
            });

            // ── Animate car to coords ─────────────────────────────────────────
            function animateCarTo(lat, lng) {
                if (!carMarker) return;
                const startPos = carMarker.getLatLng();
                const steps = 40;
                let step = 0;
                const dLat = (lat - startPos.lat) / steps;
                const dLng = (lng - startPos.lng) / steps;

                // Rotate car direction
                const angle = Math.atan2(lng - startPos.lng, lat - startPos.lat) * (180 / Math.PI);
                const carEl = document.getElementById('car-icon');
                if (carEl) carEl.style.transform = `rotate(${angle}deg)`;

                const timer = setInterval(() => {
                    step++;
                    const cur = carMarker.getLatLng();
                    carMarker.setLatLng([cur.lat + dLat, cur.lng + dLng]);
                    if (step >= steps) {
                        clearInterval(timer);
                        carMarker.setLatLng([lat, lng]);
                        map.panTo([lat, lng], { animate: true, duration: 0.6 });
                    }
                }, 18);
            }

            // Accordion logic is now handled globally at the top of the file

            // ── Bespoke Stay & Cab Interactive Selection ──────────────────────
            document.addEventListener('DOMContentLoaded', () => {
                // Hotel Category Selection
                const stayRadios = document.querySelectorAll('input[name="hotel_selection_radio"]');
                stayRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const card = this.closest('.compact-stay-card');
                        if (!card) return;
                        
                        const idx = card.getAttribute('data-stay-idx');
                        const val = card.getAttribute('data-stay-label');
                        
                        // Reset all
                        document.querySelectorAll('.compact-stay-card').forEach(c => {
                            c.classList.remove('selected-stay', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                            c.classList.add('border-white/10', 'bg-white/5');
                        });
                        
                        document.querySelectorAll('.stay-detail-card').forEach(d => d.classList.remove('is-active'));
                        
                        // Set active
                        card.classList.remove('border-white/10', 'bg-white/5');
                        card.classList.add('selected-stay', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                        
                        const selDetail = document.getElementById('stay-detail-' + idx);
                        if (selDetail) selDetail.classList.add('is-active');
                        
                        const sumVal = document.getElementById('summary_stay_val');
                        const inputVal = document.getElementById('preferred_stay_input');
                        if (sumVal) sumVal.innerText = val;
                        if (inputVal) inputVal.value = val;
                    });
                });

                // Cab Selection
                const cabRadios = document.querySelectorAll('input[name="cab_selection_radio"]');
                cabRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const card = this.closest('.compact-cab-card');
                        if (!card) return;
                        
                        const idx = card.getAttribute('data-cab-idx');
                        const val = card.getAttribute('data-cab-label');
                        
                        // Reset all
                        document.querySelectorAll('.compact-cab-card').forEach(c => {
                            c.classList.remove('selected-cab', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                            c.classList.add('border-white/10', 'bg-white/5');
                        });
                        
                        document.querySelectorAll('.cab-detail-card').forEach(d => d.classList.remove('is-active'));
                        
                        // Set active
                        card.classList.remove('border-white/10', 'bg-white/5');
                        card.classList.add('selected-cab', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                        
                        const selDetail = document.getElementById('cab-detail-' + idx);
                        if (selDetail) selDetail.classList.add('is-active');
                        
                        const sumVal = document.getElementById('summary_cab_val');
                        const inputVal = document.getElementById('preferred_cab_input');
                        if (sumVal) sumVal.innerText = val;
                        if (inputVal) inputVal.value = val;
                    });
                });
            });

/* Extracted from package-detail.php */
function openPackageCheckoutModal() {
    const modal = document.getElementById('packageCheckoutModal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    
    // Update summary values from the sidebar on the main page
    const stayVal = document.getElementById('summary_stay_val');
    const cabVal = document.getElementById('summary_cab_val');
    
    if (stayVal) {
        document.getElementById('modal_hotel_input').value = stayVal.innerText;
        const modalSummaryHotel = document.getElementById('modal_summary_hotel');
        if (modalSummaryHotel) modalSummaryHotel.innerText = stayVal.innerText;
    }
    if (cabVal) {
        document.getElementById('modal_cab_input').value = cabVal.innerText;
        const modalSummaryCab = document.getElementById('modal_summary_cab');
        if (modalSummaryCab) modalSummaryCab.innerText = cabVal.innerText;
    }
    
    updateModalSummaryPrice();
}

function closePackageCheckoutModal() {
    const modal = document.getElementById('packageCheckoutModal');
    if (modal) modal.style.display = 'none';
}

function updateModalSummaryPrice() {
    const guestsInput = document.getElementById('modal_adults_input');
    const guests = guestsInput ? (parseInt(guestsInput.value) || 1) : 1;
    const priceEl = document.querySelector('.font-display-lg');
    const rawPriceText = priceEl ? priceEl.innerText.replace(/[^0-9]/g, '') : '0';
    const basePrice = parseFloat(rawPriceText) || 0;
    const total = basePrice * guests;
    const totalEl = document.getElementById('modal_summary_total');
    if (totalEl) {
        totalEl.innerText = '₹' + total.toLocaleString('en-IN');
    }
}

// --- Top Search Bar Logic ---

// State Management
let searchState = {
    dates: { start: document.getElementById('inputCheckIn') ? document.getElementById('inputCheckIn').value : '', end: document.getElementById('inputCheckOut') ? document.getElementById('inputCheckOut').value : '' },
    adults: parseInt(document.getElementById('inputAdults') ? document.getElementById('inputAdults').value : 2) || 2,
    children: parseInt(document.getElementById('inputChildren') ? document.getElementById('inputChildren').value : 0) || 0
};

// Initialize Date Picker
if(document.getElementById("displayDates")) {
    flatpickr("#displayDates", {
        mode: "range",
        minDate: "today",
        dateFormat: "M j, Y",
        defaultDate: [searchState.dates.start, searchState.dates.end],
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                searchState.dates.start = selectedDates[0].toISOString().split('T')[0];
                searchState.dates.end = selectedDates[1].toISOString().split('T')[0];
            }
        }
    });
}

// Guests Dropdown Logic
const guestsContainer = document.getElementById('guestsContainer');
const guestsDropdown = document.getElementById('guestsDropdown');
const displayGuests = document.getElementById('displayGuests');

if(guestsContainer && guestsDropdown) {
    guestsContainer.addEventListener('click', function(e) {
        if(!guestsDropdown.contains(e.target)) {
            guestsDropdown.classList.toggle('active');
        }
    });

    document.addEventListener('click', function(e) {
        if(!guestsContainer.contains(e.target)) {
            guestsDropdown.classList.remove('active');
        }
    });
}

function setupSearchCounter(minusId, plusId, valId, min, max, stateKey) {
    const valEl = document.getElementById(valId);
    const minusBtn = document.getElementById(minusId);
    const plusBtn = document.getElementById(plusId);

    if(!minusBtn || !plusBtn) return;

    minusBtn.addEventListener('click', () => {
        if (searchState[stateKey] > min) {
            searchState[stateKey]--;
            updateSearchCounters();
        }
    });
    plusBtn.addEventListener('click', () => {
        if (searchState[stateKey] < max) {
            searchState[stateKey]++;
            updateSearchCounters();
        }
    });
}

setupSearchCounter('btnAdultsMinus', 'btnAdultsPlus', 'valAdults', 1, 30, 'adults');
setupSearchCounter('btnChildrenMinus', 'btnChildrenPlus', 'valChildren', 0, 10, 'children');

function updateSearchCounters() {
    if(document.getElementById('valAdults')) document.getElementById('valAdults').innerText = searchState.adults;
    if(document.getElementById('valChildren')) document.getElementById('valChildren').innerText = searchState.children;
    
    if(document.getElementById('btnAdultsMinus')) document.getElementById('btnAdultsMinus').disabled = (searchState.adults <= 1);
    if(document.getElementById('btnChildrenMinus')) document.getElementById('btnChildrenMinus').disabled = (searchState.children <= 0);
}

function updateSearchGuestDisplay() {
    let childText = searchState.children > 0 ? `, ${searchState.children} Child` + (searchState.children > 1 ? 'ren' : '') : '';
    if(displayGuests) displayGuests.value = `${searchState.adults} Adult${searchState.adults > 1 ? 's' : ''}${childText}`;
}

const btnApplyGuests = document.getElementById('btnApplyGuests');
if(btnApplyGuests) {
    btnApplyGuests.addEventListener('click', function() {
        updateSearchGuestDisplay();
        if(guestsDropdown) guestsDropdown.classList.remove('active');
    });
}

function submitSearch() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('check_in', searchState.dates.start);
    urlParams.set('check_out', searchState.dates.end);
    urlParams.set('adults', searchState.adults);
    urlParams.set('children', searchState.children);
    window.location.search = urlParams.toString();
}

// Form Submission
const packageForm = document.getElementById('packageCheckoutForm');
if (packageForm) {
    packageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnPackageSubmitModal');
        const msg = document.getElementById('packageModalFormMsg');
        
        if(btn) {
            btn.innerHTML = 'Submitting...';
            btn.disabled = true;
        }
        
        const formData = new FormData(this);
        
        fetch('api-submit-package-booking.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(msg) {
                    msg.style.color = '#4ade80';
                    msg.innerText = 'Inquiry submitted successfully! Downloading slip...';
                }
                
                // Open the PDF in a new tab
                if (data.booking_id) {
                    window.open('api-download-package-inquiry-slip.php?id=' + data.booking_id, '_blank');
                }
                
                setTimeout(() => {
                    closePackageCheckoutModal();
                    if(btn) {
                        btn.innerHTML = 'Submit Inquiry';
                        btn.disabled = false;
                    }
                    if(msg) msg.innerText = '';
                    packageForm.reset();
                }, 3000);
            } else {
                if(msg) {
                    msg.style.color = '#ef4444';
                    msg.innerText = data.error || 'Something went wrong. Please try again.';
                }
                if(btn) {
                    btn.innerHTML = 'Submit Inquiry';
                    btn.disabled = false;
                }
            }
        })
        .catch(err => {
            if(msg) {
                msg.style.color = '#ef4444';
                msg.innerText = 'Network error. Please try again.';
            }
            if(btn) {
                btn.innerHTML = 'Submit Inquiry';
                btn.disabled = false;
            }
        });
    });
}


// --- Lightbox Gallery Logic ---
document.addEventListener('DOMContentLoaded', () => {
    let currentLightboxIdx = 0;
    const lightboxModal = document.getElementById('lightboxModal');
    const lightboxMainImg = document.getElementById('lightboxMainImg');
    const lightboxCounter = document.getElementById('lightboxCounter');
    
    // We can collect all images dynamically from the thumbnails
    const thumbnails = Array.from(document.querySelectorAll('.lightbox-thumbnail'));
    const totalImages = thumbnails.length;

    function openLightbox(idx) {
        if (!lightboxModal || totalImages === 0) return;
        idx = parseInt(idx);
        if (isNaN(idx) || idx < 0) idx = 0;
        if (idx >= totalImages) idx = totalImages - 1;
        
        currentLightboxIdx = idx;
        
        // Set main image source from the thumbnail's src
        const activeThumb = document.querySelector(`.lightbox-thumbnail[data-idx="${idx}"]`);
        if (activeThumb) {
            lightboxMainImg.src = activeThumb.src;
            
            // Highlight thumbnail using classes
            thumbnails.forEach(t => t.classList.remove('active'));
            activeThumb.classList.add('active');
            
            // Scroll thumbnail into view
            activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
        
        if (lightboxCounter) {
            lightboxCounter.textContent = `${idx + 1} / ${totalImages}`;
        }
        
        lightboxModal.classList.add('is-active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeLightbox() {
        if (lightboxModal) {
            lightboxModal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    function prevLightbox() {
        if (currentLightboxIdx > 0) {
            openLightbox(currentLightboxIdx - 1);
        } else {
            openLightbox(totalImages - 1); // loop
        }
    }

    function nextLightbox() {
        if (currentLightboxIdx < totalImages - 1) {
            openLightbox(currentLightboxIdx + 1);
        } else {
            openLightbox(0); // loop
        }
    }

    // Global click listener for event delegation
    document.addEventListener('click', (e) => {
        // Open lightbox
        const openBtn = e.target.closest('[data-action="open-lightbox"]');
        if (openBtn) {
            e.preventDefault();
            const idx = openBtn.dataset.idx || 0;
            openLightbox(idx);
            return;
        }

        // Close lightbox
        const closeBtn = e.target.closest('[data-action="close-lightbox"]');
        if (closeBtn) {
            e.preventDefault();
            closeLightbox();
            return;
        }

        // Prev lightbox
        const prevBtn = e.target.closest('[data-action="prev-lightbox"]');
        if (prevBtn) {
            e.preventDefault();
            prevLightbox();
            return;
        }

        // Next lightbox
        const nextBtn = e.target.closest('[data-action="next-lightbox"]');
        if (nextBtn) {
            e.preventDefault();
            nextLightbox();
            return;
        }
        
        // Open checkout modal
        const openCheckoutBtn = e.target.closest('[data-action="open-checkout"]');
        if (openCheckoutBtn) {
            e.preventDefault();
            if (typeof openPackageCheckoutModal === 'function') {
                openPackageCheckoutModal();
            }
            return;
        }

        // Close checkout modal
        const closeCheckoutBtn = e.target.closest('[data-action="close-checkout"]');
        if (closeCheckoutBtn) {
            e.preventDefault();
            if (typeof closePackageCheckoutModal === 'function') {
                closePackageCheckoutModal();
            }
            return;
        }

        // Close checkout modal on outside click
        if (e.target.id === 'packageCheckoutModal') {
            if (typeof closePackageCheckoutModal === 'function') {
                closePackageCheckoutModal();
            }
        }

        // Click outside image to close

        if (e.target === lightboxModal || e.target.classList.contains('lightbox-main')) {
            closeLightbox();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (lightboxModal && lightboxModal.classList.contains('is-active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
        }
    });
});

// --- Hotel & Cab Interactivity ---
document.addEventListener('DOMContentLoaded', () => {
    
    // Hotel Selection Logic
    const hotelRadios = document.querySelectorAll('input[name="hotel_selection_radio"]');
    const hotelCards = document.querySelectorAll('.compact-stay-card');
    const hotelDetailCards = document.querySelectorAll('.stay-detail-card');
    const summaryStayVal = document.getElementById('summary_stay_val');

    hotelRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.checked) {
                const selectedIdx = e.target.value;
                const parentLabel = e.target.closest('.compact-stay-card');

                // Update classes for all compact hotel cards
                hotelCards.forEach(card => {
                    card.classList.remove('selected-stay', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                    card.classList.add('border-white/10', 'bg-white/5');
                });
                
                // Add active classes to selected
                parentLabel.classList.remove('border-white/10', 'bg-white/5');
                parentLabel.classList.add('selected-stay', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');

                // Toggle Detail Cards
                hotelDetailCards.forEach(detail => detail.classList.remove('is-active'));
                const targetDetail = document.getElementById(`stay-detail-${selectedIdx}`);
                if (targetDetail) targetDetail.classList.add('is-active');

                // Update sidebar summary
                if (summaryStayVal && parentLabel.dataset.stayLabel) {
                    summaryStayVal.innerText = parentLabel.dataset.stayLabel;
                }
            }
        });
    });

    // Cab Selection Logic
    const cabRadios = document.querySelectorAll('input[name="cab_selection_radio"]');
    const cabCards = document.querySelectorAll('.compact-cab-card');
    const cabDetailCards = document.querySelectorAll('.cab-detail-card');
    const summaryCabVal = document.getElementById('summary_cab_val');

    cabRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.checked) {
                const selectedIdx = e.target.value;
                const parentLabel = e.target.closest('.compact-cab-card');

                // Update classes for all compact cab cards
                cabCards.forEach(card => {
                    card.classList.remove('selected-cab', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');
                    card.classList.add('border-white/10', 'bg-white/5');
                });
                
                // Add active classes to selected
                parentLabel.classList.remove('border-white/10', 'bg-white/5');
                parentLabel.classList.add('selected-cab', 'border-secondary/60', 'bg-secondary/5', 'selected-stay-bg');

                // Toggle Detail Cards
                cabDetailCards.forEach(detail => detail.classList.remove('is-active'));
                const targetDetail = document.getElementById(`cab-detail-${selectedIdx}`);
                if (targetDetail) targetDetail.classList.add('is-active');

                // Update sidebar summary
                if (summaryCabVal && parentLabel.dataset.cabLabel) {
                    summaryCabVal.innerText = parentLabel.dataset.cabLabel;
                }
            }
        });
    });
});
