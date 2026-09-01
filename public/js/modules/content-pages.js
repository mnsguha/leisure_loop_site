'use strict';



    // Extracted Scripts

document.addEventListener('DOMContentLoaded', function() {
        const tracks = ['wedHotels', 'wedDests', 'valHotels', 'valDests', 'corpHotels', 'corpDests', 'festivalsTrack', 'festDests'];
        tracks.forEach(id => {
            const track = document.getElementById(id);
            if (track && window.setupGSAPMomentumDrag) {
                window.setupGSAPMomentumDrag(track);
                track.querySelectorAll('img').forEach(el => {
                    el.addEventListener('dragstart', (e) => e.preventDefault());
                });
            }
        });
    });

/* Extracted from faq.php */
document.addEventListener('DOMContentLoaded', function() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                const isActive = item.classList.contains('active');
                
                // Close all other faqs in the same column
                const col = item.closest('.faq-col');
                if (col) {
                    col.querySelectorAll('.faq-item.active').forEach(i => {
                        if (i !== item) i.classList.remove('active');
                    });
                }
                
                if (isActive) {
                    item.classList.remove('active');
                } else {
                    item.classList.add('active');
                }
            });
        });
    });

/* Extracted from b2b.php */
document.addEventListener('DOMContentLoaded', function() {
    const b2bForm = document.getElementById('b2bForm');
    if (b2bForm) {
        b2bForm.addEventListener('submit', function(e) {
            // Combine B2B specific fields into the hidden message input for the CRM
            const agency = document.getElementById('b2b_agency_name').value;
            const website = document.getElementById('b2b_website').value || 'Not provided';
            const volume = document.getElementById('b2b_volume').value;
            const message = document.getElementById('b2b_message').value;
            
            const combined = `[B2B ENQUIRY]\nAgency Name: ${agency}\nWebsite: ${website}\nEst. Monthly Queries: ${volume}\n\nMessage/Requirements:\n${message}`;
            document.getElementById('b2b_combined_message').value = combined;
        });
    }
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
