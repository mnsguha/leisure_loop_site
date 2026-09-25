'use strict';

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. Onboarding Splash Controller ──────────────────────────────
    const splashContainer = document.getElementById('splash-container');
    if (splashContainer) {
        const dots = splashContainer.querySelectorAll('.splash-dot');
        const introState = document.getElementById('splash-state-1');
        const formState = document.getElementById('splash-state-2');
        const btnAdvance = document.getElementById('btn-lets-tour');
        const btnSkip = document.getElementById('splash_skip');
        const signupForm = document.getElementById('splash-signup-form');

        // Check Session Dismissal & URL Params
        const urlParams = new URLSearchParams(window.location.search);
        let isDismissed = false;
        
        if (urlParams.has('splash')) {
            isDismissed = urlParams.get('splash') === '0';
        } else {
            try {
                isDismissed = sessionStorage.getItem('splash_dismissed') === '1';
            } catch (e) {
                console.warn('Storage access blocked by browser:', e);
            }
        }

        if (isDismissed) {
            splashContainer.style.display = 'none';
        }

        // Pagination Dots Background Switcher
        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                const idx = dot.getAttribute('data-splash-index');
                dots.forEach(d => d.classList.remove('is-active'));
                dot.classList.add('is-active');
                splashContainer.className = `mobile-splash splash-background--${idx}`;
            });
        });

        // Advance to State 2 (Form)
        if (btnAdvance) {
            btnAdvance.addEventListener('click', (e) => {
                e.preventDefault();
                if (introState) introState.classList.add('is-hidden');
                if (formState) {
                    formState.classList.add('is-active');
                    formState.setAttribute('aria-hidden', 'false');
                }
            });
        }

        // Dismiss Function
        function dismissSplash() {
            splashContainer.style.opacity = '0';
            setTimeout(() => {
                splashContainer.style.display = 'none';
            }, 350);
            try {
                sessionStorage.setItem('splash_dismissed', '1');
            } catch (e) {
                console.warn('Storage access blocked by browser:', e);
            }
        }

        if (btnSkip) {
            btnSkip.addEventListener('click', (e) => {
                e.preventDefault();
                dismissSplash();
            });
        }

        if (signupForm) {
            signupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(signupForm);
                const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const leadEndpoint = basePath ? `${basePath}/api-submit-lead.php` : 'api-submit-lead.php';

                fetch(leadEndpoint, {
                    method: 'POST',
                    body: formData
                }).finally(() => {
                    dismissSplash();
                });
            });
        }
    }

    // ── 2. Mobile Bottom-Sheet Triggers (Search & Location) ──────────
    document.addEventListener('click', (e) => {
        // Open Search Bottom Sheet
        if (e.target.closest('[data-action="open-search"]')) {
            e.preventDefault();
            const sheet = document.getElementById('mobileSearchSheet');
            if (sheet) sheet.classList.add('is-active');
            return;
        }

        // Close Search Bottom Sheet
        if (e.target.closest('[data-action="close-search"]')) {
            e.preventDefault();
            const sheet = document.getElementById('mobileSearchSheet');
            if (sheet) sheet.classList.remove('is-active');
            return;
        }

        // Close Sheet on Backdrop Click
        if (e.target.classList.contains('mobile-search-sheet')) {
            e.target.classList.remove('is-active');
        }
    });
});
