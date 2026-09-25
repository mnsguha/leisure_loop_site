'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const splashWrapper = document.getElementById('mobile-splash-view');
    const splashContainer = document.getElementById('splash-container');
    if (!splashWrapper || !splashContainer) return;

    // Ensure it is always visible on every mobile load
    splashWrapper.classList.remove('is-dismissed');
    
    // Hide mobile home view completely from interaction/visibility to prevent any Swiper dots from bleeding through
    const homeView = document.getElementById('mobile-home-view');
    if (homeView) {
        homeView.classList.add('is-hidden');
    }

    // Force cleanup of any duplicate dots that might be injected by rogue scripts/caches
    const dotGroups = splashContainer.querySelectorAll('.splash-dots');
    if (dotGroups.length > 1) {
        for (let i = 1; i < dotGroups.length; i++) {
            dotGroups[i].remove();
        }
    }

    const dots = splashContainer.querySelectorAll('.splash-dot');
    const introState = document.getElementById('splash-state-1');
    const formState = document.getElementById('splash-state-2');
    const btnLetsGo = document.getElementById('btn-lets-tour');
    const btnSkip = document.getElementById('splash_skip');
    const signupForm = document.getElementById('splash-signup-form');

    const splashBackgrounds = {
        '1': 'url("assets/img/mobile_splash_bg_1.webp")',
        '2': 'url("assets/img/mobile_splash_bg_2.webp")',
        '3': 'url("assets/img/mobile_splash_bg_3.webp")'
    };

    dots.forEach(dot => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            const idx = dot.getAttribute('data-splash-index');
            dots.forEach(d => d.classList.remove('is-active'));
            dot.classList.add('is-active');

            if (splashBackgrounds[idx]) {
                splashContainer.style.backgroundImage = splashBackgrounds[idx];
            }
        });
    });

    if (btnLetsGo && introState && formState) {
        btnLetsGo.addEventListener('click', (e) => {
            e.preventDefault();
            introState.classList.add('is-hidden');
            formState.classList.remove('is-hidden');
        });
    }

    function dismissSplash() {
        splashWrapper.classList.add('is-dismissed');
        if (homeView) {
            homeView.classList.remove('is-hidden');
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
            fetch('api-submit-lead.php', {
                method: 'POST',
                body: formData
            }).finally(() => {
                dismissSplash();
            });
        });
    }
});
