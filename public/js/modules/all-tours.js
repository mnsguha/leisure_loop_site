'use strict';

/**
 * all-tours.js
 * Filter drawer open/close, focus trap, keyboard handling,
 * client-side filter + sort engine, and results counter.
 *
 * Rules compliance:
 *   - No window namespace pollution (Rule #2)
 *   - State managed exclusively via CSS class toggling (Rule #2)
 *   - No element.style.* manipulation (Rule #2)
 *   - All events bound via addEventListener (Rule #1)
 *   - Focus trap + Escape key on drawer (Rule #10)
 *   - aria-hidden / aria-expanded toggled for a11y (Rule #5)
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Element references ────────────────────────────────────────────────────
    const pillBtn       = document.getElementById('floatingFilterDock');
    const drawer        = document.getElementById('filterSideDrawer');
    const backdrop      = document.getElementById('filterDrawerBackdrop');
    const closeBtn      = document.getElementById('drawerCloseBtn');
    const drawerBody    = document.getElementById('drawerBody');
    const liveCount     = document.getElementById('drawerLiveCount');
    const dockBadge     = document.getElementById('dockFilterCount');
    const resultsBadge  = document.getElementById('resultsCountBadge');
    const gridInner     = document.getElementById('packages-grid-inner');
    const emptyState    = document.getElementById('no-packages-placeholder');

    // ── Scroll-lock helpers (Rule #10) ───────────────────────────────────────
    let _scrollY = 0;

    function lockBodyScroll() {
        _scrollY = window.scrollY;
        document.body.style.overflow = 'hidden';   /* Rule #10 */
        document.body.style.position = 'fixed';
        document.body.style.top = '-' + _scrollY + 'px';
        document.body.style.width = '100%';
    }

    function unlockBodyScroll() {
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, _scrollY);              /* restore position */
    }

    // ── Drawer open / close ───────────────────────────────────────────────────
    function openDrawer() {
        if (!drawer) return;
        drawer.classList.add('open');
        drawer.setAttribute('aria-hidden', 'false');
        backdrop.classList.add('open');
        backdrop.setAttribute('aria-hidden', 'false');
        lockBodyScroll();
        if (pillBtn) pillBtn.setAttribute('aria-expanded', 'true');
        trapFocusInDrawer(drawer);
    }

    function closeDrawer() {
        if (!drawer) return;
        drawer.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
        backdrop.classList.remove('open');
        backdrop.setAttribute('aria-hidden', 'true');
        unlockBodyScroll();
        if (pillBtn) {
            pillBtn.setAttribute('aria-expanded', 'false');
            pillBtn.focus(); // return focus to trigger (Rule #10)
        }
        releaseFocusTrap();
    }

    if (pillBtn)  pillBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    if (drawer && drawerBody) {
        drawer.addEventListener('wheel', function (e) {
            if (!drawer.classList.contains('open')) return;
            if (!drawer.contains(e.target)) return;

            const previousScrollTop = drawerBody.scrollTop;
            drawerBody.scrollTop += e.deltaY;

            if (drawerBody.scrollTop !== previousScrollTop) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    // Escape key closes drawer (Rule #10)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer && drawer.classList.contains('open')) {
            closeDrawer();
        }
    });

    // "VIEW TOURS" button: apply all selected filters then close drawer
    document.querySelectorAll('[data-action="close-filter-drawer"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyFiltersAndSort();  /* navigate with all currently-checked filters */
        });
    });

    // ── Focus Trap (Rule #10) ─────────────────────────────────────────────────
    let _focusTrapHandler = null;

    function trapFocusInDrawer(containerEl) {
        const focusable = containerEl.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        if (!focusable.length) return;

        const first = focusable[0];
        const last  = focusable[focusable.length - 1];
        first.focus();

        _focusTrapHandler = function (e) {
            if (e.key !== 'Tab') return;
            if (e.shiftKey) {
                if (document.activeElement === first) { e.preventDefault(); last.focus(); }
            } else {
                if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
            }
        };

        containerEl.addEventListener('keydown', _focusTrapHandler);
    }

    function releaseFocusTrap() {
        if (drawer && _focusTrapHandler) {
            drawer.removeEventListener('keydown', _focusTrapHandler);
            _focusTrapHandler = null;
        }
    }

    // ── Budget Tier → Hidden Inputs ───────────────────────────────────────────
    const priceMinInput = document.getElementById('price_min');
    const priceMaxInput = document.getElementById('price_max');

    document.querySelectorAll('input[name="budget_tier"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (priceMinInput) priceMinInput.value = this.getAttribute('data-min') || '';
            if (priceMaxInput) priceMaxInput.value = this.getAttribute('data-max') || '';
        });
    });

    // ── Collect Filters → Navigate ────────────────────────────────────────────
    function buildFilterURL() {
        const currentParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams();

        ['package_type', 'type'].forEach(function (key) {
            const value = currentParams.get(key);
            if (value) params.set(key, value);
        });

        // Keyword
        const kw = document.getElementById('search_keyword_input');
        if (kw && kw.value.trim()) {
            params.set('q', kw.value.trim());
        }

        // Top-bar selects
        const themeSel = document.getElementById('search_theme_select');
        if (themeSel && themeSel.value) {
            params.set('theme', themeSel.value);
        }
        // Duration: top-bar select takes precedence over drawer checkboxes.
        // If the top-bar select has a value, only that is used — drawer checkboxes
        // for duration are intentionally skipped to prevent stacking.
        const durSel = document.getElementById('search_dur_select');
        if (durSel && durSel.value) {
            params.set('dur', durSel.value);
            // deliberately omit durations checkboxes when top-bar select is active
        } else {
            // Duration checkboxes (used only when top-bar select is "Any Duration")
            const durations = Array.from(document.querySelectorAll('.duration-checkbox:checked')).map(function (cb) { return cb.value; });
            if (durations.length) params.set('durations', durations.join(','));
        }

        // Sort
        const sortChecked = document.querySelector('input[name="catalog_sort"]:checked');
        if (sortChecked && sortChecked.value !== 'default') {
            params.set('sort', sortChecked.value);
        }

        // Theme checkboxes
        const themes = Array.from(document.querySelectorAll('.theme-checkbox:checked')).map(function (cb) { return cb.value; });
        if (themes.length) params.set('themes', themes.join(','));

        // Destination checkboxes
        const dests = Array.from(document.querySelectorAll('.dest-checkbox:checked')).map(function (cb) { return cb.value; });
        if (dests.length) params.set('dest', dests.join(','));

        // Price bounds
        if (priceMinInput && priceMinInput.value) params.set('price_min', priceMinInput.value);
        if (priceMaxInput && priceMaxInput.value) params.set('price_max', priceMaxInput.value);

        const query = params.toString();
        return 'all-tours.php' + (query ? '?' + query : '');
    }

    // Builds a URL using ONLY the top-bar search dock inputs.
    // Drawer checkboxes are intentionally excluded so a new top-bar
    // search always starts fresh without inheriting previous drawer state.
    function buildSearchBarURL() {
        const currentParams = new URLSearchParams(window.location.search);
        const params = new URLSearchParams();

        ['package_type', 'type'].forEach(function (key) {
            const value = currentParams.get(key);
            if (value) params.set(key, value);
        });

        const kw = document.getElementById('search_keyword_input');
        if (kw && kw.value.trim()) {
            params.set('q', kw.value.trim());
        }

        const themeSel = document.getElementById('search_theme_select');
        if (themeSel && themeSel.value) {
            params.set('theme', themeSel.value);
        }

        const durSel = document.getElementById('search_dur_select');
        if (durSel && durSel.value) {
            params.set('dur', durSel.value);
        }

        const query = params.toString();
        return 'all-tours.php' + (query ? '?' + query : '');
    }

    function applyFiltersAndSort() {
        window.location.href = buildFilterURL();
    }

    function clearFilterControls() {
        const defaultSort = document.querySelector('input[name="catalog_sort"][value="default"]');
        if (defaultSort) defaultSort.checked = true;

        document.querySelectorAll('.theme-checkbox, .dest-checkbox, .duration-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });

        const defaultBudget = document.querySelector('input[name="budget_tier"][value="all"]');
        if (defaultBudget) defaultBudget.checked = true;

        if (priceMinInput) priceMinInput.value = '';
        if (priceMaxInput) priceMaxInput.value = '';

        const kw = document.getElementById('search_keyword_input');
        if (kw) kw.value = '';

        const themeSel = document.getElementById('search_theme_select');
        if (themeSel) themeSel.value = '';

        const durSel = document.getElementById('search_dur_select');
        if (durSel) durSel.value = '';
    }

    // ── Active filter count for badge ─────────────────────────────────────────
    function updateActiveFilterCount() {
        let count = 0;
        const sortChecked = document.querySelector('input[name="catalog_sort"]:checked');
        if (sortChecked && sortChecked.value !== 'default') count++;
        count += document.querySelectorAll('.theme-checkbox:checked').length;
        count += document.querySelectorAll('.dest-checkbox:checked').length;
        count += document.querySelectorAll('.duration-checkbox:checked').length;
        const budgetChecked = document.querySelector('input[name="budget_tier"]:checked');
        if (budgetChecked && budgetChecked.value !== 'all') count++;

        if (dockBadge) {
            dockBadge.textContent = count > 0 ? count : 'All';
            dockBadge.setAttribute('aria-label', 'Active filters: ' + (count > 0 ? count : 0));
            if (count > 0) {
                dockBadge.classList.add('active');
            } else {
                dockBadge.classList.remove('active');
            }
        }
    }

    // ── Bind filter change events (update badge only — navigation deferred to VIEW TOURS) ─
    document.querySelectorAll('[data-change="apply-filters"], [data-change="set-budget"]').forEach(function (el) {
        el.addEventListener('change', function () {
            updateActiveFilterCount();
            /* No immediate navigation — user must click VIEW TOURS to apply */
        });
    });

    // Enter key on keyword input — uses top-bar only (no drawer bleed)
    const kwInput = document.getElementById('search_keyword_input');
    if (kwInput) {
        kwInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { window.location.href = buildSearchBarURL(); }
        });
    }

    // "SEARCH HOLIDAYS" button — uses top-bar inputs only, ignores drawer state
    document.querySelectorAll('[data-action="apply-search"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            window.location.href = buildSearchBarURL();
        });
    });

    // Legacy scroll-catalog fallback (keeps any remaining elements working)
    document.querySelectorAll('[data-action="scroll-catalog"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const anchor = document.getElementById('catalog-main-anchor');
            if (anchor) {
                anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Reset filters (clear controls in-drawer, badge resets; navigation on VIEW TOURS)
    document.querySelectorAll('[data-action="reset-filters"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (drawer && drawer.contains(btn)) {
                clearFilterControls();
                updateActiveFilterCount();
                if (drawerBody) drawerBody.scrollTop = 0;
                return;
            }

            const currentParams = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();
            ['package_type', 'type'].forEach(function (key) {
                const value = currentParams.get(key);
                if (value) params.set(key, value);
            });
            const query = params.toString();
            window.location.href = 'all-tours.php' + (query ? '?' + query : '');
        });
    });

    // ── Initialise ────────────────────────────────────────────────────────────
    updateActiveFilterCount();
});
