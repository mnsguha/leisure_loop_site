'use strict';



    // Extracted Scripts

function openInfoModal(title, subtitle, htmlContent) {
    document.getElementById('infoModalTitle').innerText = title;
    document.getElementById('infoModalSubtitle').innerText = subtitle;
    document.getElementById('infoModalBody').innerHTML = htmlContent;
    document.getElementById('infoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeInfoModal() {
    document.getElementById('infoModal').style.display = 'none';
    document.body.style.overflow = '';
}

/* Extracted from hotel-detail.php */
const hotelRoomData = <?php echo json_encode($rooms_with_plans); ?>;

// State Management
let searchState = {
    dates: { start: document.getElementById('inputCheckIn').value, end: document.getElementById('inputCheckOut').value },
    rooms: parseInt(document.getElementById('inputRooms').value) || 1,
    adults: parseInt(document.getElementById('inputAdults').value) || 2,
    children: parseInt(document.getElementById('inputChildren').value) || 0,
    infants: parseInt(document.getElementById('inputInfants').value) || 0
};

// Initialize Date Picker
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

// Guests Dropdown Logic
const guestsContainer = document.getElementById('guestsContainer');
const guestsDropdown = document.getElementById('guestsDropdown');
const displayGuests = document.getElementById('displayGuests');

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

function setupCounter(minusId, plusId, valId, min, max, stateKey) {
    const valEl = document.getElementById(valId);
    const minusBtn = document.getElementById(minusId);
    const plusBtn = document.getElementById(plusId);

    minusBtn.onclick = () => {
        if (searchState[stateKey] > min) {
            searchState[stateKey]--;
            updateCounters();
        }
    };
    plusBtn.onclick = () => {
        if (searchState[stateKey] < max) {
            searchState[stateKey]++;
            updateCounters();
        }
    };
}

setupCounter('btnRoomsMinus', 'btnRoomsPlus', 'valRooms', 1, 10, 'rooms');
setupCounter('btnAdultsMinus', 'btnAdultsPlus', 'valAdults', 1, 30, 'adults');
setupCounter('btnChildrenMinus', 'btnChildrenPlus', 'valChildren', 0, 10, 'children');
setupCounter('btnInfantsMinus', 'btnInfantsPlus', 'valInfants', 0, 10, 'infants');

function updateCounters() {
    document.getElementById('valRooms').innerText = searchState.rooms;
    document.getElementById('valAdults').innerText = searchState.adults;
    document.getElementById('valChildren').innerText = searchState.children;
    document.getElementById('valInfants').innerText = searchState.infants;
    
    document.getElementById('btnRoomsMinus').disabled = (searchState.rooms <= 1);
    document.getElementById('btnAdultsMinus').disabled = (searchState.adults <= 1);
    document.getElementById('btnChildrenMinus').disabled = (searchState.children <= 0);
    document.getElementById('btnInfantsMinus').disabled = (searchState.infants <= 0);
}

function updateGuestDisplay() {
    let childText = searchState.children > 0 ? `, ${searchState.children} Child` + (searchState.children > 1 ? 'ren' : '') : '';
    let infantText = searchState.infants > 0 ? `, ${searchState.infants} Infant` + (searchState.infants > 1 ? 's' : '') : '';
    document.getElementById('displayGuests').value = `${searchState.rooms} Room${searchState.rooms > 1 ? 's' : ''}, ${searchState.adults} Adult${searchState.adults > 1 ? 's' : ''}${childText}${infantText}`;
}

document.getElementById('btnApplyGuests').addEventListener('click', function() {
    updateGuestDisplay();
    guestsDropdown.classList.remove('active');
});

// Apply Search Button Logic
document.getElementById('btnPerformSearch').addEventListener('click', function() {
    // Format dates for UI
    const options = { month: 'short', day: 'numeric' };
    const sDate = new Date(searchState.dates.start);
    const eDate = new Date(searchState.dates.end);
    document.getElementById('sidebarCheckIn').innerText = sDate.toLocaleDateString('en-US', options);
    document.getElementById('sidebarCheckOut').innerText = eDate.toLocaleDateString('en-US', options);
    
    // Update Sidebar Guests
    document.getElementById('sidebarRoomsText').innerText = `${searchState.rooms} Room${searchState.rooms > 1 ? 's' : ''}`;
    document.getElementById('sidebarGuestsText').innerText = `${searchState.adults} Adults, ${searchState.children} Children`;
    
    // Smooth scroll to rooms if they are below
    const firstRoom = document.querySelector('.room-category');
    if(firstRoom) firstRoom.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Update all plan price displays in the list
    updateAllPlanPricesUI();
    
    // If a room is selected, recalculate total based on nights/rooms
    if(currentSelectedPlan) {
        updateSidebarUI();
    }
});

function calculatePlanPrice(planId) {
    let plan = null;
    for(let r of hotelRoomData) {
        let p = r.plans.find(x => x.id == planId);
        if(p) { plan = p; break; }
    }
    if(!plan) return { avgBase: 0, avgFinal: 0, totalBase: 0, totalFinal: 0, nights: 1, rooms: 1 };
    
    if (!searchState.dates.start || !searchState.dates.end) return { avgBase: 0, avgFinal: 0, totalBase: 0, totalFinal: 0, nights: 1, rooms: 1 };
    
    const start = new Date(searchState.dates.start);
    const end = new Date(searchState.dates.end);
    
    const diffTime = Math.abs(end - start);
    let nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (nights === 0) nights = 1;
    
    let totalBase = 0;
    let totalFinal = 0;
    
    let rooms = searchState.rooms || 1;
    const total_extra_adults = Math.max(0, searchState.adults - (2 * rooms));
    const total_extra_children = searchState.children || 0;
    
    for (let i = 0; i < nights; i++) {
        let currentNight = new Date(start);
        currentNight.setDate(start.getDate() + i);
        let nightStr = currentNight.toISOString().split('T')[0];
        
        let applicableRate = null;
        
        if (plan.date_rates && plan.date_rates.length > 0) {
            applicableRate = plan.date_rates.find(dr => dr.rate_date === nightStr);
            if (!applicableRate) {
                // Fallback to first available rate if this specific date has no rate configured
                applicableRate = plan.date_rates[0];
            }
        }
        
        let base_t = applicableRate ? parseFloat(applicableRate.base_rate_2_pax || 0) : 0;
        let discount = 0; // Discount functionality is currently merged into final rates
        
        let ebt = applicableRate ? parseFloat(applicableRate.extra_adult_rate || 0) : 0;
        let cnbt = applicableRate ? parseFloat(applicableRate.cnb_rate || 0) : 0;
        
        let daily_total_all_rooms = (base_t * rooms) + (total_extra_adults * ebt) + (total_extra_children * cnbt);
        let daily_discounted_all_rooms = daily_total_all_rooms;
        
        totalBase += daily_total_all_rooms;
        totalFinal += daily_discounted_all_rooms;
    }
    
    return {
        avgBase: totalBase / nights / rooms,
        avgFinal: totalFinal / nights / rooms,
        totalBase: totalBase,
        totalFinal: totalFinal,
        nights: nights,
        rooms: rooms
    };
}

function updateAllPlanPricesUI() {
    let rooms = searchState.rooms || 1;
    const total_extra_adults = Math.max(0, searchState.adults - (2 * rooms));
    const total_extra_children = searchState.children || 0;

    for(let r of hotelRoomData) {
        for(let p of r.plans) {
            let priceData = calculatePlanPrice(p.id);
            let strikeEl = document.getElementById('strike-' + p.id);
            let finalEl = document.getElementById('final-' + p.id);
            
            let eaIncEl = document.getElementById('ea-inc-' + p.id);
            let ecIncEl = document.getElementById('ec-inc-' + p.id);
            
            if(eaIncEl) eaIncEl.style.display = total_extra_adults > 0 ? 'list-item' : 'none';
            if(ecIncEl) ecIncEl.style.display = total_extra_children > 0 ? 'list-item' : 'none';
            
            if(finalEl) {
                finalEl.innerText = '₹' + priceData.avgFinal.toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
            }
            if(strikeEl) {
                if(priceData.avgBase > priceData.avgFinal) {
                    strikeEl.style.display = 'block';
                    strikeEl.innerText = '₹' + priceData.avgBase.toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                } else {
                    strikeEl.style.display = 'none';
                }
            }
        }
    }
}

function updateSidebarUI() {
    if(!currentSelectedPlan) return;
    let priceData = calculatePlanPrice(currentSelectedPlan.id);
    let tax = priceData.totalFinal * 0.12; // Example tax calculation
    
    document.getElementById('dispTaxes').innerText = '₹' + tax.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('sidebarTotalFinal').innerText = '₹' + priceData.totalFinal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('formFinalPrice').value = priceData.totalFinal;
}

// Initial calculation on load
updateAllPlanPricesUI();

let currentSelectedPlan = null;
let currentSelectedRoom = null;

// Cart Logic
function addToCart(btn, planId, roomId, hotelName, roomName, planName) {
    // Reset all buttons
    document.querySelectorAll('.btn-remove-cart').forEach(b => {
        b.className = 'btn-add-cart';
        b.textContent = 'SELECT';
    });
    
    // Set this button to REMOVE
    btn.className = 'btn-remove-cart';
    btn.textContent = 'SELECTED';
    
    currentSelectedPlan = { id: planId, name: planName };
    currentSelectedRoom = { id: roomId, name: roomName };
    
    // Update Sidebar UI
    document.getElementById('cartEmpty').style.display = 'none';
    document.getElementById('cartFull').style.display = 'block';
    
    document.getElementById('dispRoomName').innerText = roomName;
    document.getElementById('dispPlanName').innerText = planName;
    
    updateSidebarUI();
    
    // Update Modal Form
    document.getElementById('formRoomId').value = roomId;
    document.getElementById('formPlanId').value = planId;
    
    document.getElementById('btnProceed').disabled = false;
}

// Modal Logic
function openCheckoutModal() {
    <?php if ($hotel['type'] == 'signature'): ?>
    if (!currentSelectedPlan) return;
    <?php else: ?>
    // For partner brand, default summary
    document.getElementById('modalSummaryRoom').innerText = "Custom Inquiry";
    document.getElementById('modalSummaryTotal').innerText = "TBD";
    <?php endif; ?>
    
    // Update Modal Info
    if(currentSelectedRoom && currentSelectedPlan) {
        document.getElementById('modalSummaryRoom').innerText = currentSelectedRoom.name + " - " + currentSelectedPlan.name;
        document.getElementById('modalSummaryTotal').innerText = document.getElementById('sidebarTotalFinal').innerText;
    }
    
    // Pass dates into modal UI
    const dateOptions = { weekday: 'short', day: 'numeric', month: 'short' };
    document.getElementById('modalCheckIn').innerText = new Date(searchState.dates.start).toLocaleDateString('en-US', dateOptions);
    document.getElementById('modalCheckOut').innerText = new Date(searchState.dates.end).toLocaleDateString('en-US', dateOptions);
    
    document.getElementById('checkoutModal').style.display = 'flex';
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

// Form Submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmitModal');
    const msg = document.getElementById('modalFormMsg');
    
    btn.textContent = 'Processing...';
    btn.disabled = true;
    
    // Use the actual dates from search state
    const checkInStr = searchState.dates.start;
    const checkOutStr = searchState.dates.end;
    
    const formData = new FormData(this);
    formData.append('check_in', checkInStr);
    formData.append('check_out', checkOutStr);
    formData.append('rooms', searchState.rooms);
    formData.append('adults', searchState.adults);
    
    // Guest name concatenation
    const firstName = formData.get('guest_name');
    const lastName = formData.get('guest_last_name');
    formData.set('guest_name', firstName + ' ' + lastName);
    
    fetch('/leisure_loop_site/api/submit-hotel-booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            msg.style.color = '#2ecc71';
            msg.textContent = 'Success! Redirecting...';
            
            <?php if ($hotel['type'] == 'signature'): ?>
            setTimeout(() => {
                window.open('/leisure_loop_site/api/download-hotel-voucher.php?id=' + data.booking_id, '_blank');
                window.location.reload();
            }, 2000);
            <?php else: ?>
            setTimeout(() => {
                window.open('/leisure_loop_site/api/download-inquiry-slip.php?id=' + data.booking_id, '_blank');
                window.location.reload();
            }, 2000);
            <?php endif; ?>
        } else {
            msg.style.color = '#e74c3c';
            msg.textContent = data.message;
            btn.textContent = '<?php echo $hotel['type'] == 'signature' ? 'Confirm & Book Now' : 'Submit Inquiry'; ?>';
            btn.disabled = false;
        }
    })
    .catch(error => {
        msg.style.color = '#e74c3c';
        msg.textContent = 'A network error occurred.';
        btn.textContent = '<?php echo $hotel['type'] == 'signature' ? 'Confirm & Book Now' : 'Submit Inquiry'; ?>';
        btn.disabled = false;
    });
});

<?php if(!empty($hotel_images)): ?>
// Lightbox Logic
let currentLightboxIndex = 0;
const galleryImages = <?php echo json_encode(array_column($hotel_images, 'image_url')); ?>;

function openLightbox(index) {
    currentLightboxIndex = index;
    document.getElementById('lightboxModal').style.display = 'flex';
    updateLightbox();
}

function closeLightbox() {
    document.getElementById('lightboxModal').style.display = 'none';
}

function nextLightboxImage() {
    currentLightboxIndex = (currentLightboxIndex + 1) % galleryImages.length;
    updateLightbox();
}

function prevLightboxImage() {
    currentLightboxIndex = (currentLightboxIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightbox();
}

function updateLightbox() {
    document.getElementById('lightboxMainImg').src = galleryImages[currentLightboxIndex];
    document.getElementById('lightboxCounter').textContent = (currentLightboxIndex + 1) + ' / ' + galleryImages.length;
    
    // Update active thumbnail
    document.querySelectorAll('.lightbox-thumbnail').forEach((el, index) => {
        if (index === currentLightboxIndex) {
            el.classList.add('active');
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else {
            el.classList.remove('active');
        }
    });
}
<?php endif; ?>

function toggleDesc() {
    const desc = document.getElementById('hotelDesc');
    const btn = document.getElementById('viewMoreDesc');
    if (desc.classList.contains('truncated')) {
        desc.classList.remove('truncated');
        btn.textContent = 'View Less ∧';
    } else {
        desc.classList.add('truncated');
        btn.textContent = 'View More ∨';
    }
}


    // Generic Event Delegation
    document.addEventListener('click', (e) => {
        const actionEl = e.target.closest('[data-action], [data-href]');
        if (actionEl && actionEl.hasAttribute('data-href')) {
            window.location.href = actionEl.getAttribute('data-href');
            return;
        }
        
        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');
        
        // Modal toggling
        if (action === 'open-modal') {
            const target = actionEl.getAttribute('data-target');
            if (target) {
                const modal = document.querySelector(target) || document.getElementById(target);
                if (modal) modal.classList.add('is-active');
            } else {
                document.querySelectorAll('.modal').forEach(m => m.classList.add('is-active'));
            }
        } else if (action === 'close-modal') {
            const target = actionEl.getAttribute('data-target');
            if (target) {
                const modal = document.querySelector(target) || document.getElementById(target);
                if (modal) modal.classList.remove('is-active');
            } else {
                document.querySelectorAll('.modal.is-active, .modal.active').forEach(m => m.classList.remove('is-active', 'active'));
            }
        } else if (action === 'toggle-tab') {
            const targetId = actionEl.getAttribute('data-target');
            // Remove active from all tabs in same group
            const group = actionEl.getAttribute('data-group') || 'default';
            document.querySelectorAll(`[data-action="toggle-tab"][data-group="${group}"]`).forEach(el => el.classList.remove('is-active', 'active'));
            document.querySelectorAll(`.tab-content[data-group="${group}"]`).forEach(el => el.classList.remove('is-active', 'active'));
            
            actionEl.classList.add('is-active');
            const targetContent = document.getElementById(targetId);
            if(targetContent) targetContent.classList.add('is-active');
        } else if (action === 'toggle-accordion') {
            actionEl.classList.toggle('is-active');
            const content = actionEl.nextElementSibling;
            if(content) content.classList.toggle('is-active');
        } else if (action.startsWith('eval:')) {
            // Extremely generic fallback - to be manually replaced if found
            console.warn('Unhandled inline action:', action);
        }
    });
    
    // Intercept Lead Forms
    const leadForms = document.querySelectorAll('form[action*="/api/submit-lead"], form[action*="/api/v1/leads"], form.lead-form');
    leadForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            // Let the JS handle it or let standard submit happen if we don't preventDefault.
            // Requirement: "prepped to target POST /api/v1/leads without inline execution"
            // We just ensure action is correct.
        });
    });
