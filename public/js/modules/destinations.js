'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('destSearchInput');
    const seasonSelect = document.getElementById('destSeasonSelect');
    const discoveryChips = document.querySelectorAll('.discovery-chip');
    const filterBtn = document.querySelector('[data-action="apply-filters"]');
    const resetBtn = document.querySelector('[data-action="reset-filters"]');
    const cards = document.querySelectorAll('.js-dest-card');
    const emptyState = document.getElementById('no-packages-placeholder');

    let currentChipFilter = 'all';

    function applyFilters() {
        const query = (searchInput ? searchInput.value.toLowerCase().trim() : '');
        const season = (seasonSelect ? seasonSelect.value.toLowerCase().trim() : '');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const cat = (card.getAttribute('data-category') || '').toLowerCase();
            const tag = (card.getAttribute('data-tagline') || '').toLowerCase();
            const times = (card.getAttribute('data-times') || '').toLowerCase();

            let matchesSearch = !query || name.includes(query) || cat.includes(query) || tag.includes(query);
            let matchesSeason = !season || times.includes(season);
            let matchesChip = true;

            if (currentChipFilter !== 'all') {
                if (currentChipFilter === 'domestic' || currentChipFilter === 'international') {
                    matchesChip = (cat === currentChipFilter);
                } else if (currentChipFilter === 'summer') {
                    matchesChip = times.includes('summer') || times.includes('monsoon') || times.includes('june') || times.includes('july') || times.includes('august');
                } else if (currentChipFilter === 'winter') {
                    matchesChip = times.includes('winter') || times.includes('autumn') || times.includes('december') || times.includes('january') || times.includes('february') || times.includes('october') || times.includes('november');
                }
            }

            if (matchesSearch && matchesSeason && matchesChip) {
                card.classList.remove('is-hidden');
                visibleCount++;
            } else {
                card.classList.add('is-hidden');
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('is-hidden', visibleCount > 0);
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (seasonSelect) seasonSelect.addEventListener('change', applyFilters);
    if (filterBtn) filterBtn.addEventListener('click', applyFilters);
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (seasonSelect) seasonSelect.value = '';
            currentChipFilter = 'all';
            discoveryChips.forEach(c => c.classList.remove('active'));
            document.querySelector('.discovery-chip[data-val="all"]')?.classList.add('active');
            applyFilters();
        });
    }

    discoveryChips.forEach(chip => {
        chip.addEventListener('click', () => {
            discoveryChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentChipFilter = chip.getAttribute('data-val');
            applyFilters();
        });
    });
});
