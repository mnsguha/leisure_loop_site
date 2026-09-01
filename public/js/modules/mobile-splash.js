'use strict';

(() => {
    const splashView = document.getElementById('mobile-splash-view');
    const homeView = document.getElementById('mobile-home-view');
    const splash = document.getElementById('splash-container');
    const intro = document.getElementById('splash-state-1');
    const formState = document.getElementById('splash-state-2');
    const letsGoButton = document.getElementById('btn-lets-tour');
    const skipButton = document.getElementById('splash_skip');
    const form = document.getElementById('splash-signup-form');
    const submitButton = document.getElementById('splash_submit_btn');
    const dots = document.querySelectorAll('[data-splash-index]');

    if (!splashView || !homeView || !splash || !intro || !formState || !letsGoButton) return;

    let currentIndex = 1;
    let carouselId;

    const showHome = () => {
        window.clearInterval(carouselId);
        splashView.classList.remove('is-active');
        homeView.removeAttribute('hidden');
    };

    const setBackground = (index) => {
        currentIndex = index;
        splash.classList.remove('splash-background--1', 'splash-background--2', 'splash-background--3');
        splash.classList.add(`splash-background--${index}`);
        dots.forEach((dot) => {
            const active = Number(dot.dataset.splashIndex) === index;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-pressed', String(active));
        });
    };

    const startCarousel = () => {
        window.clearInterval(carouselId);
        carouselId = window.setInterval(() => setBackground(currentIndex === 3 ? 1 : currentIndex + 1), 4000);
    };

    homeView.setAttribute('hidden', '');
    splashView.classList.add('is-active');
    setBackground(currentIndex);
    startCarousel();

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            setBackground(Number(dot.dataset.splashIndex));
            startCarousel();
        });
    });

    letsGoButton.addEventListener('click', () => {
        document.getElementById('splash-step-toggle')?.setAttribute('checked', 'checked');
        intro.classList.add('is-hidden');
        formState.classList.add('is-active');
        formState.setAttribute('aria-hidden', 'false');
        window.clearInterval(carouselId);
        document.getElementById('splash_name')?.focus();
    });

    skipButton?.addEventListener('click', showHome);

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        submitButton.disabled = true;
        submitButton.textContent = 'Saving…';
        const formData = new FormData(form);
        formData.set('source', 'Mobile App Onboarding');
        formData.set('destination', 'App Onboarding Signup');

        try {
            await fetch('api-submit-lead.php', { method: 'POST', body: formData, credentials: 'same-origin' });
        } catch (error) {
            // Onboarding remains optional; network failures must never trap a visitor here.
        }

        showHome();
    });
})();
