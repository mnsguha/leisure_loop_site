import sys

file_path = "public/package-detail.php"
with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

# Truncate at line 1957 (index 1956)
good_lines = lines[:1957]

rest_of_html_and_js = """            </form>
        </div>
        
        <!-- Right: Summary -->
        <div class="p-modal-right">
            <h3 style="color: var(--gold); margin: 0 0 20px; font-family: 'Playfair Display', serif; font-size: 1.2rem;">Booking Summary</h3>
            
            <h4 style="color: #fff; font-size: 1.1rem; margin: 0 0 5px;"><?php echo htmlspecialchars($pkg['title']); ?></h4>
            <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin: 0 0 20px;">TOUR CODE: <?php echo htmlspecialchars($pkg['tour_code'] ?? 'TBA'); ?></p>
            
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Hotel Category</span>
                    <span style="color: #fff; font-weight: 600; font-size: 0.9rem;" id="modal_summary_hotel">4 Star Luxury</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Private Cab</span>
                    <span style="color: #fff; font-weight: 600; font-size: 0.9rem;" id="modal_summary_cab">Innova / Xylo / Scorpio</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span style="color: rgba(255,255,255,0.7); font-size: 1rem;">Total Amount:</span>
                <span style="color: var(--gold); font-size: 1.5rem; font-weight: bold; font-family: 'Playfair Display', serif;" id="modal_summary_total">₹0</span>
            </div>
            <div style="text-align: right; margin-top: 5px;">
                <span style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">(Calculated based on Base Price × Guests)</span>
            </div>
        </div>
    </div>
</div>

<script>
function openPackageCheckoutModal() {
    const modal = document.getElementById('packageCheckoutModal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    
    // Update summary values from hidden inputs on the main page
    const stayInput = document.getElementById('preferred_stay_input');
    const cabInput = document.getElementById('preferred_cab_input');
    
    if (stayInput) {
        document.getElementById('modal_hotel_input').value = stayInput.value;
        document.getElementById('modal_summary_hotel').innerText = stayInput.value;
    }
    if (cabInput) {
        document.getElementById('modal_cab_input').value = cabInput.value;
        document.getElementById('modal_summary_cab').innerText = cabInput.value;
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
    const basePrice = <?php echo isset($pkg['price']) ? (float)$pkg['price'] : 0; ?>;
    const total = basePrice * guests;
    const totalEl = document.getElementById('modal_summary_total');
    if (totalEl) {
        totalEl.innerText = '₹' + total.toLocaleString('en-IN');
    }
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
        
        fetch('../api/submit-package-booking.php', {
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
                    window.open('../api/download-package-inquiry-slip.php?id=' + data.booking_id, '_blank');
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
</script>
"""

with open(file_path, "w", encoding="utf-8") as f:
    f.writelines(good_lines)
    f.write(rest_of_html_and_js)
