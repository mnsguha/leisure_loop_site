import re
import os

JS_FILE = r'g:\Antigravity\leisure_loop_site\public\js\modules\mobile-views.js'

with open(JS_FILE, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove tailwind.config blocks (there are many duplicates)
content = re.sub(r'tailwind\.config\s*=\s*\{.*?\}(?=\s*(?:\/\*|var|const|let|function|document|' + "')" + ')', '', content, flags=re.DOTALL)
# Actually, tailwind config might be better stripped completely since it's redundant and we're moving to CSS. But let's just use a simpler regex
content = re.sub(r'tailwind\.config\s*=\s*\{.*?\n\s*\}\s*\n', '', content, flags=re.DOTALL)

# Remove old function definitions that are now handled by event delegation
functions_to_remove = [
    'openModal', 'closeModal', 'openEnquiryModal', 'openMobileMenu', 'closeMobileMenu',
    'toggleSort', 'toggleFilter', 'closeAll', 'clearFilters', 'applyFiltersAndClose',
    'openLocationModal', 'closeLocationModal', 'openSearchModal', 'closeSearchModal',
    'selectCity', 'filterCities', 'useCurrentLocation', 'switchFilterPane'
]

for func in functions_to_remove:
    # Match function name() { ... }
    # This is a bit tricky with regex, we can match until the balanced closing brace, or just match typical formats.
    # Since they are usually short, we can match `function funcName() { [^{}]* }`
    # Some have nested blocks, so regex is hard. Let's just remove them manually if possible, or use a robust pattern.
    pass

# We will just wrap the whole thing and prepend the event delegation logic
# It's safer to just inject the delegation logic at the top. The dead functions won't be called.

delegation_logic = """
    // --- Centralized Event Delegation & Hardware Back Sync ---
    const activeModals = [];

    function pushModalState(modalId) {
        history.pushState({ modal: modalId }, '', `#${modalId}`);
        activeModals.push(modalId);
    }

    function popModalState() {
        if (activeModals.length > 0) {
            history.back();
        }
    }

    window.addEventListener('popstate', (e) => {
        if (activeModals.length > 0) {
            const modalId = activeModals.pop();
            const modal = document.getElementById(modalId) || document.querySelector(`.${modalId}`);
            if (modal) {
                modal.classList.remove('active', 'is-active');
                if (modalId === 'mobileMenuOverlay' || modalId === 'mobileMenuPanel') {
                    const panel = document.getElementById('mobileMenuPanel');
                    const overlay = document.getElementById('mobileMenuOverlay');
                    if (panel) panel.classList.add('translate-x-full');
                    if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
                }
            } else {
                document.querySelectorAll('.active, .is-active').forEach(el => {
                    el.classList.remove('active', 'is-active');
                });
            }
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('click', (e) => {
        const actionEl = e.target.closest('[data-action], [data-href]');
        
        if (actionEl && actionEl.hasAttribute('data-href')) {
            window.location.href = actionEl.getAttribute('data-href');
            return;
        }

        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');
        
        const openModal = (selector, modalId) => {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.add('active', 'is-active');
                document.body.style.overflow = 'hidden';
                pushModalState(modalId);
            }
        };

        const closeModal = (selector) => {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.remove('active', 'is-active');
                document.body.style.overflow = '';
                if(activeModals.length > 0) popModalState();
            }
        };

        const closeAll = () => {
            document.querySelectorAll('.active, .is-active').forEach(el => el.classList.remove('active', 'is-active'));
            document.body.style.overflow = '';
            if(activeModals.length > 0) popModalState();
        }
        
        if (action === 'open-modal' || action === 'open-enquiry-modal') {
            document.getElementById('enquiryModalOverlay')?.classList.add('active', 'is-active');
            openModal('#enquiryModal', 'enquiryModal');
        } else if (action === 'close-modal') {
            document.getElementById('enquiryModalOverlay')?.classList.remove('active', 'is-active');
            closeModal('#enquiryModal');
        } else if (action === 'open-mobile-menu') {
            const overlay = document.getElementById('mobileMenuOverlay');
            const panel = document.getElementById('mobileMenuPanel');
            if (overlay) overlay.classList.remove('opacity-0', 'pointer-events-none');
            if (panel) panel.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
            pushModalState('mobileMenuPanel');
        } else if (action === 'close-mobile-menu') {
            const overlay = document.getElementById('mobileMenuOverlay');
            const panel = document.getElementById('mobileMenuPanel');
            if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
            if (panel) panel.classList.add('translate-x-full');
            document.body.style.overflow = '';
            if(activeModals.length > 0) popModalState();
        } else if (action === 'close-all') {
            closeAll();
        } else if (action === 'clear-filters') {
            document.querySelectorAll('.filter-theme, .filter-dest, .filter-dur, .filter-type, .filter-time').forEach(el => el.checked = false);
            const pMin = document.getElementById('price_min');
            const pMax = document.getElementById('price_max');
            if (pMin) pMin.value = '';
            if (pMax) pMax.value = '';
            // Trigger apply filters if function exists
            if (typeof applyFilters === 'function') applyFilters();
        } else if (action === 'apply-filters-close') {
            if (typeof applyFilters === 'function') applyFilters();
            closeAll();
        } else if (action === 'toggle-sort') {
            document.getElementById('modalOverlay')?.classList.add('active');
            document.getElementById('sortSheet')?.classList.add('active');
            document.getElementById('filterSheet')?.classList.remove('active');
            pushModalState('sortSheet');
        } else if (action === 'toggle-filter') {
            document.getElementById('modalOverlay')?.classList.add('active');
            document.getElementById('filterSheet')?.classList.add('active');
            document.getElementById('sortSheet')?.classList.remove('active');
            pushModalState('filterSheet');
        } else if (action === 'open-location') {
            document.getElementById('locationModalOverlay')?.classList.add('active');
            openModal('#locationModal', 'locationModal');
        } else if (action === 'open-search') {
            document.getElementById('searchModalOverlay')?.classList.add('active');
            openModal('#searchModal', 'searchModal');
        } else if (action === 'close-search') {
            document.getElementById('searchModalOverlay')?.classList.remove('active');
            closeModal('#searchModal');
        } else if (action === 'close-location') {
            document.getElementById('locationModalOverlay')?.classList.remove('active');
            closeModal('#locationModal');
        } else if (action === 'use-location') {
            if (typeof useCurrentLocation === 'function') useCurrentLocation();
        } else if (action === 'select-city') {
            const city = actionEl.getAttribute('data-city');
            const curText = document.getElementById('currentLocationText');
            if(curText) curText.textContent = city;
            document.getElementById('locationModalOverlay')?.classList.remove('active');
            closeModal('#locationModal');
        } else if (action === 'scroll-carousel') {
            const carousel = document.getElementById('fd-mobile-carousel');
            if (carousel) carousel.scrollBy({left: 140, behavior: 'smooth'});
        } else if (action === 'switch-filter') {
            const pane = actionEl.getAttribute('data-pane');
            document.querySelectorAll('.filter-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            actionEl.classList.add('active');
            const targetPane = document.getElementById(pane);
            if(targetPane) targetPane.classList.add('active');
        } else if (action === 'share-package') {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            }
        } else if (action === 'toggle-accordion') {
            actionEl.classList.toggle('active');
            const body = actionEl.nextElementSibling;
            if (body) body.classList.toggle('active');
        } else if (action === 'select-stay') {
            const idx = actionEl.getAttribute('data-idx');
            const title = actionEl.getAttribute('data-title');
            const span = document.getElementById('stay-title-' + idx);
            if (span) span.innerText = title;
            // close drawer
            document.getElementById('drawer-stay-' + idx)?.classList.remove('translate-y-0');
            document.getElementById('drawer-stay-' + idx)?.classList.add('translate-y-full');
            document.getElementById('overlay-stay-' + idx)?.classList.remove('opacity-100', 'pointer-events-auto');
            document.getElementById('overlay-stay-' + idx)?.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
            if(activeModals.length > 0) popModalState();
        } else if (action === 'select-cab') {
            const idx = actionEl.getAttribute('data-idx');
            const name = actionEl.getAttribute('data-name');
            const span = document.getElementById('cab-title-' + idx);
            if (span) span.innerText = name;
            // close drawer
            document.getElementById('drawer-cab-' + idx)?.classList.remove('translate-y-0');
            document.getElementById('drawer-cab-' + idx)?.classList.add('translate-y-full');
            document.getElementById('overlay-cab-' + idx)?.classList.remove('opacity-100', 'pointer-events-auto');
            document.getElementById('overlay-cab-' + idx)?.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
            if(activeModals.length > 0) popModalState();
        } else if (action === 'enable-map') {
            const overlay = document.getElementById('map-overlay');
            if (overlay) overlay.style.pointerEvents = 'none';
        } else if (action === 'select-room') {
            const idx = actionEl.getAttribute('data-idx');
            const title = actionEl.getAttribute('data-title');
            
            const display = document.getElementById('mSelectedRoomDisplay');
            const nameEl = document.getElementById('mSelectedRoomName');
            const inputEl = document.getElementById('mFormRoomId');
            
            if(display && nameEl && inputEl) {
                display.classList.remove('hidden');
                nameEl.textContent = title;
                inputEl.value = idx;
                document.getElementById('bookingFormSection')?.scrollIntoView({behavior: 'smooth'});
            }
        }
    });

    document.addEventListener('focusin', (e) => {
        if (e.target.hasAttribute('data-focus')) {
            e.target.type = e.target.getAttribute('data-focus');
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.hasAttribute('data-change')) {
            const action = e.target.getAttribute('data-change');
            if (action === 'apply-filters') {
                if (typeof applyFilters === 'function') applyFilters();
            }
        }
    });
"""

final_content = f"""'use strict';

document.addEventListener('DOMContentLoaded', () => {{
{delegation_logic}

    // --- Original Extracted Logic ---
{content}
}});
"""

# Strip out the first occurrences of 'use strict' and DOMContentLoaded from the original content since we wrapped it
final_content = final_content.replace("'use strict';\n\ndocument.addEventListener('DOMContentLoaded', () => {\n", "", 1)
final_content = final_content.replace("});\n});", "});", 1)

with open(JS_FILE, 'w', encoding='utf-8') as f:
    f.write(final_content)

print("Updated mobile-views.js")
