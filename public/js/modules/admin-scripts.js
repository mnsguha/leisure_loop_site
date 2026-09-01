'use strict';



    // --- Extracted from blog-form.php ---
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#content',
            plugins: 'lists link image table code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
            skin: 'oxide-dark',
            content_css: 'dark',
            height: 500,
            menubar: false
        });
    }

    // --- Extracted from destination-form.php ---
    const addSpotBtn = document.getElementById('add-spot');
    if (addSpotBtn) {
        addSpotBtn.addEventListener('click', () => {
            const container = document.getElementById('spots-container');
            const div = document.createElement('div');
            div.className = 'spot-item';
            div.innerHTML = `
                <span class="btn-remove js-remove-parent" data-action="remove-parent">Remove</span>
                <div class="form-group">
                    <label>Spot Title</label>
                    <input type="text" name="spot_title[]" required>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="spot_image[]" placeholder="https://..." style="margin-bottom: 0.5rem;">
                    <input type="file" name="upload_spot_image[]" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="spot_desc[]" rows="2"></textarea>
                </div>
            `;
            container.appendChild(div);
        });
    }

    const addExpBtn = document.getElementById('add-exp');
    if (addExpBtn) {
        addExpBtn.addEventListener('click', () => {
            const container = document.getElementById('experiences-container');
            const div = document.createElement('div');
            div.className = 'spot-item';
            div.innerHTML = `
                <span class="btn-remove js-remove-parent" data-action="remove-parent">Remove</span>
                <div class="form-row">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="exp_title[]" required>
                    </div>
                    <div class="form-group">
                        <label>Material Icon Name (e.g. local_cafe)</label>
                        <input type="text" name="exp_icon[]">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="exp_desc[]" rows="2"></textarea>
                </div>
            `;
            container.appendChild(div);
        });
    }

    // --- Extracted from package-form.php ---
    const addDayBtn = document.getElementById('add-day');
    if (addDayBtn) {
        addDayBtn.addEventListener('click', () => {
            const container = document.getElementById('itinerary-container');
            const dayNum = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'itinerary-item';
            div.innerHTML = `
                <span class="btn-remove js-remove-parent" data-action="remove-parent">Remove</span>
                <div class="form-group">
                    <label>Day ${dayNum} Title</label>
                    <input type="text" name="day_title[]" required>
                </div>

                <div class="form-group">
                    <label>Activities / Description</label>
                    <textarea name="day_desc[]" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>📍 GPS Coordinates (Lat, Lng) — for animated map</label>
                    <input type="text" name="day_coords[]" placeholder="e.g. 27.3314, 88.6138">
                </div>
            `;
            container.appendChild(div);
        });
    }

    const radioCurated = document.querySelector('input[name="package_type"][value="curated"]');
    const radioFixed = document.querySelector('input[name="package_type"][value="fixed"]');
    const fixedFields = document.getElementById('fixed-departure-fields');

    function toggleFixedFields() {
        if (radioFixed && radioFixed.checked) {
            fixedFields.classList.add("is-active");
        } else if (fixedFields) {
            fixedFields.classList.remove("is-active");
        }
    }

    if (radioCurated && radioFixed) {
        radioCurated.addEventListener('change', toggleFixedFields);
        radioFixed.addEventListener('change', toggleFixedFields);
        toggleFixedFields();
    }

    // --- Extracted from leads.php ---
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.lead-checkbox');
    const btnDelete = document.getElementById('btnBulkDelete');
    const btnCancel = document.getElementById('btnCancel');

    function updateButtons() {
        if (!btnDelete) return;
        const checkedCount = document.querySelectorAll('.lead-checkbox:checked').length;
        if (checkedCount > 0) {
            btnDelete.classList.add('is-active');
            if (btnCancel) btnCancel.classList.add('is-active');
            btnDelete.innerText = `Delete (${checkedCount})`;
        } else {
            btnDelete.classList.remove('is-active');
            if (btnCancel) btnCancel.classList.remove('is-active');
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateButtons();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.lead-checkbox:checked').length === checkboxes.length;
            if (selectAll) selectAll.checked = allChecked && checkboxes.length > 0;
            updateButtons();
        });
    });

    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            updateButtons();
        });
    }

    // --- hotels.php search ---
    const hotelSearchInput = document.getElementById('hotelSearchInput');
    if (hotelSearchInput) {
        hotelSearchInput.addEventListener('keyup', () => {
            const filter = hotelSearchInput.value.toUpperCase();
            const div = document.getElementById("hotelList");
            if (div) {
                const options = div.getElementsByClassName("hotel-option");
                for (let i = 0; i < options.length; i++) {
                    const txtValue = options[i].textContent || options[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        options[i].classList.remove("is-hidden");
                        options[i].classList.add("is-active");
                    } else {
                        options[i].classList.remove("is-active");
                        options[i].classList.add("is-hidden");
                    }
                }
            }
        });
    }

    // --- Event Delegation for Dynamic / Modified Elements ---
    document.addEventListener('click', function(event) {

        const target = event.target;
        const actionEl = target.closest('[data-action]');
        
        // Hide bulk dropdown if clicked outside
        const bulkUpdateContainer = document.getElementById('bulkUpdateContainer');
        const bulkDropdown = document.getElementById('bulkDropdown');
        if (bulkUpdateContainer && !bulkUpdateContainer.contains(event.target)) {
            if (bulkDropdown) bulkDropdown.classList.remove("is-active");
        }

        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');

        if (action === 'remove-parent') {
            actionEl.parentElement.remove();
        } 
        else if (action === 'confirm') {
            const msg = actionEl.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(msg)) {
                event.preventDefault();
            }
        }
        else if (action === 'toggle-nav') {
            actionEl.parentElement.classList.toggle('expanded');
        }
        else if (action === 'toggle-bulk-dropdown') {
            if (bulkDropdown) {
                bulkDropdown.classList.toggle('is-active');
            }
        }
        else if (action === 'open-bulk-modal') {
            event.preventDefault();
            if (bulkDropdown) bulkDropdown.classList.remove("is-active");
            const modalType = actionEl.getAttribute('data-modal');
            if (modalType === 'inventory') {
                const modal = document.getElementById('bulkInventoryModal');
                if (modal) modal.classList.add('is-active');
            } else if (modalType === 'rates') {
                const modal = document.getElementById('bulkRatesModal');
                if (modal) modal.classList.add('is-active');
            }
        }
        else if (action === 'close-modal') {
            const targetId = actionEl.getAttribute('data-target');
            const modal = document.getElementById(targetId);
            if (modal) modal.classList.remove("is-active");
        }
        else if (action === 'open-modal') {
            const targetId = actionEl.getAttribute('data-target');
            const modal = document.getElementById(targetId);
            if (modal) modal.classList.add("is-active");
        }
        else if (action === 'toggle-plan') {
            const planId = actionEl.getAttribute('data-plan-id');
            const icon = document.getElementById('icon_plan_' + planId);
            if (icon) {
                if (icon.classList.contains('fa-chevron-down')) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                } else {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            }
            
            const details = document.querySelectorAll('[id^="details_plan_' + planId + '_"]');
            details.forEach(function(el) {
                el.classList.toggle("is-active");
            });
        }
        else if (action === 'toggle-meal-plans') {
            const roomId = actionEl.getAttribute('data-room-id');
            const el = document.getElementById('mealPlans_' + roomId);
            if (el) {
                el.classList.toggle("is-active");
            }
        }
        else if (action === 'open-hotel-modal') {
            const modal = document.getElementById('hotelSelectModal');
            if (modal) modal.classList.add('is-active');
            
            if (hotelSearchInput) {
                hotelSearchInput.value = '';
                hotelSearchInput.focus();
                // trigger keyup
                hotelSearchInput.dispatchEvent(new Event('keyup'));
            }
            
            const selectedHotelInput = document.getElementById('selectedHotelId');
            if (selectedHotelInput) selectedHotelInput.value = '';
            
            const goBtn = document.getElementById('goBtn');
            if (goBtn) {
                goBtn.disabled = true;
                goBtn.style.opacity = '0.5';
                goBtn.style.cursor = 'not-allowed';
            }
            
            const options = document.getElementsByClassName('hotel-option');
            for (let i = 0; i < options.length; i++) {
                options[i].classList.remove('selected-hotel');
            }
        }
        else if (action === 'close-hotel-modal') {
            const modal = document.getElementById('hotelSelectModal');
            if (modal) modal.classList.remove("is-active");
        }
        else if (action === 'select-hotel') {
            const hotelId = actionEl.getAttribute('data-hotel-id');
            const selectedHotelInput = document.getElementById('selectedHotelId');
            if (selectedHotelInput) selectedHotelInput.value = hotelId;
            
            const goBtn = document.getElementById('goBtn');
            if (goBtn) {
                goBtn.disabled = false;
                goBtn.style.opacity = '1';
                goBtn.style.cursor = 'pointer';
            }
            
            const options = document.getElementsByClassName('hotel-option');
            for (let i = 0; i < options.length; i++) {
                options[i].classList.remove('selected-hotel');
                options[i].style.background = 'transparent';
                options[i].style.color = '#fff';
            }
            actionEl.classList.add('selected-hotel');
            actionEl.style.background = 'rgba(197,160,89,0.3)';
            actionEl.style.color = 'var(--gold)';
        }
        else if (action === 'copy-emails') {
            const emailsData = actionEl.getAttribute('data-emails');
            if (emailsData) {
                try {
                    const emails = JSON.parse(emailsData);
                    if (emails.length === 0) {
                        alert('No emails to copy.');
                        return;
                    }
                    const emailString = emails.join(', ');
                    navigator.clipboard.writeText(emailString).then(function() {
                        alert(emails.length + ' emails copied to clipboard!');
                    }, function(err) {
                        alert('Could not copy text: ' + err);
                    });
                } catch(e) {}
            }
        }
    });

