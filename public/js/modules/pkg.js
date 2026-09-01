/* Extracted from pkg.php */
          },
        },
      }

/* Extracted from pkg.php */
// Micro-interactions for smooth scrolling and hover effects
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Atmospheric parallax on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroImg = document.querySelector('section img');
            if (heroImg) {
                heroImg.style.transform = `translateY(${scrolled * 0.4}px)`;
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
