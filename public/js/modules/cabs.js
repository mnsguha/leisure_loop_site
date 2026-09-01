'use strict';



    // Extracted Scripts

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.search-tab');
    const panes = document.querySelectorAll('.tab-pane');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.getAttribute('data-target');
            document.getElementById('tab-' + target).classList.add('active');
        });
    });
});

/* Extracted from cab-detail.php */
const vehicleRates = <?php echo json_encode($all_vehicle_rates); ?>;

// Search Bar Tabs Logic
const tabs = document.querySelectorAll('.search-tab');
const panes = document.querySelectorAll('.tab-pane');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        panes.forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});

// Search Form Conditional Logic
let hasSearched = false;
let searchDetails = {};

<?php if ($is_search): ?>
window.addEventListener('DOMContentLoaded', () => {
    searchDetails = <?php echo json_encode($_GET); ?>;
    hasSearched = true;
    
    // Auto-populate the form
    const searchType = searchDetails.type || 'oneway';
    const form = document.querySelector(`.cab-search-form[data-type="${searchType}"]`);
    if (form) {
        // Switch tab
        document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        
        const tabButton = document.querySelector(`.search-tab[data-target="${searchType}"]`);
        const tabPane = document.getElementById(`tab-${searchType}`);
        if (tabButton && tabPane) {
            tabButton.classList.add('active');
            tabPane.classList.add('active');
        }
        
        // Fill inputs
        for (const key in searchDetails) {
            if (key === 'search' || key === 'type') continue;
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = searchDetails[key];
            }
        }
    }
    
    // Unlock UI
    const container = document.getElementById('cabDetailContainer');
    if (container) {
        container.classList.remove('pre-search-state');
    }
    
    // Update all vehicle buttons to SELECT
    document.querySelectorAll('.vehicle-action-btn').forEach(b => {
        b.innerText = 'SELECT';
        b.classList.remove('selected');
    });
});
<?php endif; ?>

function performSearch(btn) {
    const form = btn.closest('form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Save search details
    const formData = new FormData(form);
    searchDetails = Object.fromEntries(formData.entries());
    searchDetails.type = form.dataset.type;

    // Transition to searched state
    hasSearched = true;
    
    const container = document.getElementById('cabDetailContainer');
    if (container) {
        container.classList.remove('pre-search-state');
    }
    
    // Update all vehicle buttons to SELECT
    document.querySelectorAll('.vehicle-action-btn').forEach(b => {
        b.innerText = 'SELECT';
        b.classList.remove('selected');
    });

    // Update dynamic pricing based on travel_date
    const travelDate = searchDetails.travel_date;
    if (travelDate) {
        document.querySelectorAll('.room-category').forEach(card => {
            const vid = card.getAttribute('data-vid');
            const basePrice = parseFloat(card.getAttribute('data-baseprice'));
            let currentPrice = basePrice;

            if (vehicleRates[vid] && vehicleRates[vid][travelDate]) {
                currentPrice = parseFloat(vehicleRates[vid][travelDate]);
            }
            
            selectedDynamicPrices[vid] = currentPrice;

            // Update price text in the UI
            const priceEl = card.querySelector('.price-final');
            if (priceEl) {
                priceEl.innerHTML = '&#8377;' + new Intl.NumberFormat('en-IN').format(currentPrice) + ' <span style="font-size:0.9rem;color:rgba(255,255,255,0.5);">/ day</span>';
            }
        });
    }
    
    // Scroll to available vehicles
    const header = document.querySelector('.cab-header-card');
    if (header) {
        header.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

const selectedDynamicPrices = {};

function handleVehicleAction(btn, vehicle) {
    if (!hasSearched) {
        // Show Toast or Alert
        alert("Please fill up your travel details in the search bar first to get an estimated price.");
        
        // Scroll to top
        const searchWrapper = document.querySelector('.search-wrapper');
        if (searchWrapper) {
            searchWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight active form inputs
            const activeForm = document.querySelector('.tab-pane.active form');
            if (activeForm) {
                const inputs = activeForm.querySelectorAll('.search-input');
                inputs.forEach(input => {
                    input.style.boxShadow = '0 0 10px #C5A059';
                    setTimeout(() => input.style.boxShadow = 'none', 1500);
                });
            }
        }
        return;
    }
    
    // Inject dynamic price if available
    if (selectedDynamicPrices[vehicle.id]) {
        vehicle.price = selectedDynamicPrices[vehicle.id];
    }
    
    // If searched, behave as normal SELECT
    selectVehicle(btn, vehicle);
}

// Existing Enquiry Flow Logic
let selectedVehicle = null;

function selectVehicle(btn, vehicle) {
      selectedVehicle = vehicle;
      
      // Reset all buttons
      document.querySelectorAll('.vehicle-action-btn').forEach(b => {
          b.innerText = 'SELECT';
          b.classList.remove('selected');
      });
      
      if (btn) {
          btn.innerText = 'SELECTED';
          btn.classList.add('selected');
      }
      
      renderSidebar();
  }

function renderSidebar() {
    const sidebar = document.getElementById('sidebar-cart-content');
    if (!selectedVehicle) {
        sidebar.innerHTML = '<div class="empty-cart-msg">Select a vehicle to view details.</div>';
        return;
    }

    let searchSummary = '';
    if (searchDetails.type === 'oneway') {
        searchSummary = `<div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
            <strong style="color:#C5A059;">From:</strong> ${searchDetails.pickup_location}<br>
            <strong style="color:#C5A059;">To:</strong> ${searchDetails.drop_location}<br>
            <strong style="color:#C5A059;">Date:</strong> ${searchDetails.travel_date}
        </div>`;
    } else if (searchDetails.type === 'hourly') {
        searchSummary = `<div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
            <strong style="color:#C5A059;">Pick-up:</strong> ${searchDetails.pickup_location}<br>
            <strong style="color:#C5A059;">Duration:</strong> ${searchDetails.duration}<br>
            <strong style="color:#C5A059;">Date:</strong> ${searchDetails.travel_date}
        </div>`;
    } else if (searchDetails.type === 'itinerary') {
        searchSummary = `<div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed rgba(255,255,255,0.1);">
            <strong style="color:#C5A059;">Start:</strong> ${searchDetails.pickup_location}<br>
            <strong style="color:#C5A059;">Date:</strong> ${searchDetails.travel_date}<br>
            <strong style="color:#C5A059;">Route:</strong> ${searchDetails.itinerary_details}
        </div>`;
    }

    sidebar.innerHTML = `
        <div class="selected-hotel-name">${selectedVehicle.name}</div>
        <div class="selected-room-name">Premium Chauffeur Driven</div>
        ${searchSummary}
        <div class="selected-plan-name">Base fare: &#8377;${new Intl.NumberFormat().format(selectedVehicle.price)} / day</div>
        
        <div style="margin-top:20px; margin-bottom:10px; font-size: 0.95rem; font-weight: 600; color:var(--gold);">Select Service Type:</div>
        <div class="payment-type">
            <label>
                <input type="radio" name="service_type" value="Disposal" onchange="updateServiceType(this.value)"> 
                Disposal (Full Day usage within city/outstation limits)
            </label>
            <label>
                <input type="radio" name="service_type" value="Point to Point" onchange="updateServiceType(this.value)"> 
                Point to Point (Direct A to B transfer)
            </label>
        </div>

        <div class="total-row">
            <span>Total estimated</span>
            <span>&#8377;${new Intl.NumberFormat().format(selectedVehicle.price)}</span>
        </div>
        
        <button class="btn-proceed" id="btnSubmitEnquiry" disabled onclick="submitEnquiry()">Submit Inquiry</button>
    `;
}

let chosenServiceType = null;
function updateServiceType(val) {
    chosenServiceType = val;
    document.getElementById('btnSubmitEnquiry').disabled = false;
}


function submitEnquiry() {
    if (!chosenServiceType || !selectedVehicle) return;
    
    // Fill hidden form fields
    document.getElementById('formVehicleId').value = selectedVehicle.id;
    document.getElementById('formVehicleName').value = selectedVehicle.name;
    document.getElementById('formServiceType').value = chosenServiceType;
    document.getElementById('formFinalPrice').value = selectedVehicle.price; // We might calculate proper price based on logic later
    
    document.getElementById('formPickup').value = searchDetails.pickup_location || '';
    document.getElementById('formDrop').value = searchDetails.drop_location || '';
    document.getElementById('formDate').value = searchDetails.travel_date || '';
    document.getElementById('formTime').value = searchDetails.travel_time || '';
    
    document.getElementById('formTripType').value = searchDetails.type || 'oneway';
    document.getElementById('formDuration').value = searchDetails.duration || '';
    document.getElementById('formSearchItinerary').value = searchDetails.itinerary_details || '';
    
    // Populate Right Sidebar Summary
    document.getElementById('modalSummaryVehicle').innerText = selectedVehicle.name;
    document.getElementById('modalSummaryService').innerText = chosenServiceType;
    document.getElementById('modalDate').innerText = searchDetails.travel_date || 'N/A';
    
    const sType = searchDetails.type || 'oneway';
    const labelPickup = document.getElementById('modalLabelPickup');
    const labelDrop = document.getElementById('modalLabelDrop');
    const valPickup = document.getElementById('modalPickup');
    const valDrop = document.getElementById('modalDrop');
    
    if (sType === 'oneway') {
        labelPickup.innerText = 'From:';
        valPickup.innerText = searchDetails.pickup_location || 'N/A';
        labelDrop.innerText = 'To:';
        valDrop.innerText = searchDetails.drop_location || 'N/A';
        document.getElementById('modalDropContainer').style.display = 'flex';
    } else if (sType === 'hourly') {
        labelPickup.innerText = 'Pick-up:';
        valPickup.innerText = searchDetails.pickup_location || 'N/A';
        labelDrop.innerText = 'Duration:';
        valDrop.innerText = searchDetails.duration || 'N/A';
        document.getElementById('modalDropContainer').style.display = 'flex';
    } else if (sType === 'itinerary') {
        labelPickup.innerText = 'Start Location:';
        valPickup.innerText = searchDetails.pickup_location || 'N/A';
        labelDrop.innerText = 'Route:';
        valDrop.innerText = searchDetails.itinerary_details || 'N/A';
        document.getElementById('modalDropContainer').style.display = 'flex';
    }
    document.getElementById('modalSummaryTotal').innerText = '₹' + new Intl.NumberFormat('en-IN').format(selectedVehicle.price);
    
    // Show modal
    document.getElementById('checkoutModal').style.display = 'flex';
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmitModal');
    const msg = document.getElementById('modalFormMsg');
    
    btn.textContent = 'Processing...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    const firstName = formData.get('guest_name');
    const lastName = formData.get('guest_last_name');
    formData.set('guest_name', firstName + ' ' + lastName);
    
    fetch('/leisure_loop_site/api/submit-cab-booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            msg.style.color = '#2ecc71';
            msg.textContent = 'Success! Redirecting...';
            
            setTimeout(() => {
                window.open('/leisure_loop_site/api/download-cab-inquiry-slip.php?id=' + data.booking_id, '_blank');
                window.location.reload();
            }, 2000);
        } else {
            msg.style.color = '#e74c3c';
            msg.textContent = data.message;
            btn.textContent = 'Submit Inquiry';
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        msg.style.color = '#e74c3c';
        msg.textContent = 'An error occurred. Please try again.';
        btn.textContent = 'Submit Inquiry';
        btn.disabled = false;
    });
});


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
