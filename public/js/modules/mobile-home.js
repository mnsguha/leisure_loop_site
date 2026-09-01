'use strict';

(() => {
    const searchOverlay = document.getElementById('searchModalOverlay');
    const searchModal = document.getElementById('searchModal');
    const locationOverlay = document.getElementById('locationModalOverlay');
    const locationModal = document.getElementById('locationModal');
    const locationText = document.getElementById('currentLocationText');
    const citySearch = document.getElementById('citySearchInput');
    const cityList = document.getElementById('cityList');

    const setSheetState = (overlay, sheet, isOpen) => {
        if (!overlay || !sheet) return;
        overlay.classList.toggle('is-active', isOpen);
        sheet.classList.toggle('is-active', isOpen);
        sheet.setAttribute('aria-hidden', String(!isOpen));
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-action]');
        if (!trigger) return;

        const action = trigger.dataset.action;
        if (action === 'open-search') setSheetState(searchOverlay, searchModal, true);
        if (action === 'close-search') setSheetState(searchOverlay, searchModal, false);
        if (action === 'open-location') setSheetState(locationOverlay, locationModal, true);
        if (action === 'close-location') setSheetState(locationOverlay, locationModal, false);
        if (action === 'select-city') {
            if (locationText) locationText.textContent = trigger.dataset.city || 'Location selected';
            setSheetState(locationOverlay, locationModal, false);
        }
    });

    citySearch?.addEventListener('input', () => {
        const query = citySearch.value.trim().toLocaleLowerCase();
        cityList?.querySelectorAll('[data-city]').forEach((city) => {
            city.hidden = !city.textContent.toLocaleLowerCase().includes(query);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        setSheetState(searchOverlay, searchModal, false);
        setSheetState(locationOverlay, locationModal, false);
    });
})();
