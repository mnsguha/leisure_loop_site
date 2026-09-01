(function() {
    if (document.body.dataset.mobileViewsInit) return;
    document.body.dataset.mobileViewsInit = 'true';


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
                    if (panel) panel.classList.add('-translate-x-full');
                    if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
                }
            } else {
                document.querySelectorAll('.active, .is-active').forEach(el => {
                    el.classList.remove('active', 'is-active');
                });
            }
            document.body.classList.remove('scroll-lock');
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
                document.body.classList.add('scroll-lock');
                pushModalState(modalId);
            }
        };

        const closeModal = (selector) => {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.remove('active', 'is-active');
                document.body.classList.remove('scroll-lock');
                if(activeModals.length > 0) popModalState();
            }
        };

        const closeAll = () => {
            document.querySelectorAll('.active, .is-active').forEach(el => el.classList.remove('active', 'is-active'));
            document.body.classList.remove('scroll-lock');
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
            if (panel) panel.classList.remove('-translate-x-full');
            document.body.classList.add('scroll-lock');
            pushModalState('mobileMenuPanel');
        } else if (action === 'close-mobile-menu') {
            const overlay = document.getElementById('mobileMenuOverlay');
            const panel = document.getElementById('mobileMenuPanel');
            if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
            if (panel) panel.classList.add('-translate-x-full');
            document.body.classList.remove('scroll-lock');
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
            document.body.classList.remove('scroll-lock');
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
            document.body.classList.remove('scroll-lock');
            if(activeModals.length > 0) popModalState();
        } else if (action === 'enable-map') {
            const overlay = document.getElementById('map-overlay');
            if (overlay) overlay.classList.add('map-overlay--disabled');
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


    // --- Page-Specific Extracted Logic ---

// Removed outer DOMContentLoaded




/* Extracted from mobile_about.php */


/* Extracted from mobile_about.php */
// openModal / closeModal: handled by centralized data-action delegation above.

/* Extracted from mobile_about.php */
let lastScroll = 0;
    const topBar = document.getElementById('topAppBar');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 50) {
            topBar.classList.remove('-translate-y-full');
            topBar.classList.add('bg-obsidian/80', 'backdrop-blur-lg');
            topBar.classList.remove('bg-obsidian', 'shadow-lg');
            return;
        }
        
        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scrolling down
            topBar.classList.add('-translate-y-full');
        } else {
            // Scrolling up
            topBar.classList.remove('-translate-y-full');
            topBar.classList.add('bg-obsidian', 'shadow-lg');
            topBar.classList.remove('bg-obsidian/80');
        }
        
        lastScroll = currentScroll;
    });

/* Extracted from mobile_b2b.php */


/* Extracted from mobile_b2b.php */
// openModal / closeModal: handled by centralized data-action delegation above.

/* Extracted from mobile_b2b.php */
document.addEventListener('DOMContentLoaded', function() {
        const b2bForm = document.getElementById('b2bFormMobile');
        if (b2bForm) {
            b2bForm.addEventListener('submit', function(e) {
                const agency = document.getElementById('b2b_agency_name').value;
                const volume = document.getElementById('b2b_volume').value;
                const message = document.getElementById('b2b_message').value;
                
                const combined = `[B2B ENQUIRY]\nAgency Name: ${agency}\nEst. Monthly Queries: ${volume}\n\nMessage/Requirements:\n${message}`;
                document.getElementById('b2b_combined_message').value = combined;
            });
        }
    });

/* Extracted from mobile_bottom_nav.php */
// openMobileMenu / closeMobileMenu: handled by centralized data-action delegation above.

        if (typeof Swiper !== 'undefined') {
            const swiper = new Swiper('.fleet-swiper', {
                slidesPerView: 1.2,
                spaceBetween: 20,
                centeredSlides: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            });
        }

        // Form submission handling
        document.getElementById('cabForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const msgEl = document.getElementById('cabFormMsg');
            msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-[var(--gold)]/20 text-[var(--gold)] block';
            msgEl.classList.add('msg--gold'); msgEl.classList.remove('msg--success', 'msg--error');
            msgEl.textContent = 'Sending request...';
            
            fetch('api/submit-lead.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    msgEl.textContent = 'Booking requested successfully! We will contact you soon.';
                    msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-green-500/20 text-green-400 block';
                    msgEl.classList.add('msg--success'); msgEl.classList.remove('msg--gold', 'msg--error');
                    this.reset();
                } else {
                    msgEl.textContent = 'Error: ' + data.message;
                    msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-red-500/20 text-red-400 block';
                    msgEl.classList.add('msg--error'); msgEl.classList.remove('msg--gold', 'msg--success');
                }
            })
            .catch(err => {
                msgEl.textContent = 'Network error. Please try again.';
                msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-red-500/20 text-red-400 block';
                msgEl.classList.add('msg--error'); msgEl.classList.remove('msg--gold', 'msg--success');
            });
        });

/* Extracted from mobile_contact.php */


/* Extracted from mobile_contact.php */
// openModal / closeModal: handled by centralized data-action delegation above.

/* Extracted from mobile_corporate.php */


/* Extracted from mobile_corporate.php */
// openModal / closeModal: handled by centralized data-action delegation above.

/* Extracted from mobile_destinations.php */


/* Extracted from mobile_destinations.php */
function toggleSort() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('sortSheet').classList.add('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function toggleFilter() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('filterSheet').classList.add('active');
            document.getElementById('sortSheet').classList.remove('active');
        }
        function closeAll() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('sortSheet').classList.remove('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function clearFilters() {
            document.querySelectorAll('.filter-time').forEach(el => el.checked = false);
            applyFilters();
        }
        function applyFiltersAndClose() {
            applyFilters();
            closeAll();
        }
        function applyFilters() {
            const times = Array.from(document.querySelectorAll('.filter-time:checked')).map(el => el.value);
            const sortVal = document.querySelector('input[name="sort"]:checked').value;
            
            const cards = Array.from(document.querySelectorAll('.js-card'));
            let visibleCount = 0;
            
            cards.forEach(card => {
                const cardTimes = card.dataset.times.split('|');
                let timeMatch = times.length === 0 || times.some(t => cardTimes.includes(t));
                
                if (timeMatch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            const container = document.getElementById('destGrid');
            const noResults = document.getElementById('noResults');
            const visibleCards = cards.filter(c => !c.classList.contains('hidden'));
            
            visibleCards.sort((a, b) => {
                if (sortVal === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
                if (sortVal === 'name_desc') return b.dataset.name.localeCompare(a.dataset.name);
                return parseInt(a.dataset.order) - parseInt(b.dataset.order);
            });
            
            visibleCards.forEach(card => container.appendChild(card));
            container.appendChild(noResults);
            
            if (visibleCount === 0) {
                noResults.classList.add('no-results--visible');
            } else {
                noResults.classList.remove('no-results--visible');
            }
        }

/* Extracted from mobile_destination_details.php */
// openModal / closeModal: handled by centralized data-action delegation above.

/* Extracted from mobile_faq.php */


/* Extracted from mobile_faq.php */
// openModal / closeModal: handled by centralized data-action delegation above.
        
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(q => {
                q.addEventListener('click', function() {
                    const item = this.closest('.faq-item');
                    const isActive = item.classList.contains('active');
                    
                    // Close all other faqs
                    document.querySelectorAll('.faq-item').forEach(i => {
                        i.classList.remove('active');
                    });
                    
                    // If the clicked one wasn't active, open it
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });

/* Extracted from mobile_home.php */


/* Extracted from mobile_home.php */
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.hero-coverflow', {
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            loop: true,
            coverflowEffect: {
                rotate: 0,
                stretch: -15,
                depth: 120,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
});

/* Extracted from mobile_home.php */
// Enable mouse drag-to-scroll for the new carousels on desktop testing
            document.querySelectorAll('.dest-mini-carousel').forEach(carousel => {
                let isDown = false;
                let startX;
                let scrollLeft;
                let isDragging = false;

                carousel.addEventListener('mousedown', (e) => {
                    isDown = true;
                    isDragging = false;
                    startX = e.pageX - carousel.offsetLeft;
                    scrollLeft = carousel.scrollLeft;
                    e.preventDefault(); // Prevent text selection
                });
                carousel.addEventListener('mouseleave', () => { isDown = false; });
                carousel.addEventListener('mouseup', () => { isDown = false; });
                carousel.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - carousel.offsetLeft;
                    const walk = (x - startX) * 2;
                    if (Math.abs(walk) > 5) isDragging = true;
                    carousel.scrollLeft = scrollLeft - walk;
                });
                
                // Prevent click on drag
                carousel.querySelectorAll('.dest-mini-card').forEach(card => {
                    card.addEventListener('click', (e) => {
                        if (isDragging) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });
                });
            });

/* Extracted from mobile_home.php */
document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('promoCarousel');
            const dots = document.querySelectorAll('.promo-dot');
            if (!carousel || dots.length <= 1) return;
            
            let currentIndex = 0;
            const totalSlides = dots.length;
            
            // Update dots on scroll
            carousel.addEventListener('scroll', function() {
                const scrollLeft = carousel.scrollLeft;
                const slideWidth = carousel.clientWidth;
                currentIndex = Math.round(scrollLeft / slideWidth);
                
                dots.forEach((dot, index) => {
                    dot.classList.toggle('promo-dot--active', index === currentIndex);
                });
            });
            
            // Auto scroll every 4 seconds
            setInterval(() => {
                let nextIndex = currentIndex + 1;
                if (nextIndex >= totalSlides) nextIndex = 0;
                
                const slideWidth = carousel.clientWidth;
                carousel.scrollTo({
                    left: nextIndex * slideWidth,
                    behavior: 'smooth'
                });
            }, 4000);
        });

/* Extracted from mobile_home.php */
function openSearchModal() {
        document.getElementById('searchModalOverlay').classList.add('active');
        document.getElementById('searchModal').classList.add('active');
        document.body.classList.add('scroll-lock');
    }
    function closeSearchModal() {
        document.getElementById('searchModalOverlay').classList.remove('active');
        document.getElementById('searchModal').classList.remove('active');
        document.body.classList.remove('scroll-lock');
    }
    function openLocationModal() {
        document.getElementById('locationModalOverlay').classList.add('active');
        document.getElementById('locationModal').classList.add('active');
        document.body.classList.add('scroll-lock');
    }
    function closeLocationModal() {
        document.getElementById('locationModalOverlay').classList.remove('active');
        document.getElementById('locationModal').classList.remove('active');
        document.body.classList.remove('scroll-lock');
    }
    function selectCity(city) {
        document.getElementById('currentLocationText').textContent = city;
        closeLocationModal();
    }
    function filterCities() {
        let input = document.getElementById('citySearchInput').value.toLowerCase();
        let items = document.getElementById('cityList').children;
        for (let i = 0; i < items.length; i++) {
            let text = items[i].innerText.toLowerCase();
            if (text.includes(input)) {
                items[i].classList.remove('city-item--hidden');
            } else {
                items[i].classList.add('city-item--hidden');
            }
        }
    }

    async function useCurrentLocation() {
        const btnText = document.getElementById('currentLocationBtnText');
        
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        btnText.textContent = 'Locating...';
        
        navigator.geolocation.getCurrentPosition(async (position) => {
            try {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                // Reverse geocode using Nominatim (free, no API key required)
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                const data = await response.json();
                
                let city = 'Current Location';
                if (data && data.address) {
                    const a = data.address;
                    // Find the most specific populated place
                    const locality = a.city || a.town || a.village || a.suburb || a.neighbourhood || a.municipality || a.city_district || a.county || a.state_district;
                    
                    if (locality) {
                        city = locality;
                    } else if (a.state) {
                        city = a.state;
                    }
                }
                
                selectCity(city);
                btnText.textContent = 'Use my current location';
            } catch (error) {
                console.error("Error fetching location details:", error);
                selectCity('Current Location');
                btnText.textContent = 'Use my current location';
            }
        }, (error) => {
            console.error("Geolocation error:", error);
            if (error.code === error.PERMISSION_DENIED) {
                alert('Location access was denied. Please allow location access in your browser settings.');
            } else {
                alert('Unable to retrieve your location.');
            }
            btnText.textContent = 'Use my current location';
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    }

/* Extracted from mobile_hotel-detail.php */
function selectMobileRoom(roomId, roomName) {
            const display = document.getElementById('mSelectedRoomDisplay');
            const nameEl = document.getElementById('mSelectedRoomName');
            const inputEl = document.getElementById('mFormRoomId');
            
            if(display && nameEl && inputEl) {
                display.classList.remove('hidden');
                nameEl.textContent = roomName;
                inputEl.value = roomId;
                
                // Scroll to form smoothly
                document.getElementById('bookingFormSection').scrollIntoView({behavior: 'smooth'});
            }
        }

        document.getElementById('mobileHotelBookingForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const hotelType = this.dataset.hotelType || '';
            if (hotelType === 'signature') {
            if(!document.getElementById('mFormRoomId').value) {
                alert("Please select a room first.");
                document.getElementById('roomsSection').scrollIntoView({behavior: 'smooth'});
                return;
            }
            }

            const msgEl = document.getElementById('mHotelFormMsg');
            msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-[#C5A059]/20 text-[#C5A059] block';
            msgEl.textContent = 'Processing request...';
            
            // Re-using the same endpoint logic as desktop, assuming submit-lead handles it or a dedicated handler.
            // Let's assume there's an API or we just use submit-lead
            // Actually, desktop hotel-detail.php doesn't have the JS code shown in lines 1-170 for submit.
            // I'll simulate success for now, or post to api/submit-lead.php.
            // The user form uses ID hotelBookingForm, let's post to api/submit-lead.php with form_type = hotel_booking
            
            const formData = new FormData(this);
            formData.append('form_type', 'hotel_booking');
            
            fetch('api/submit-lead.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-green-500/20 text-green-400 block';
                    msgEl.textContent = 'Booking requested successfully!';
                    this.reset();
                    const hotelType = this.dataset.hotelType || '';
                    if (hotelType === 'signature') {
                        document.getElementById('mSelectedRoomDisplay').classList.add('hidden');
                        document.getElementById('mFormRoomId').value = '';
                    }
                } else {
                    msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-red-500/20 text-red-400 block';
                    msgEl.textContent = data.message || 'Error processing request.';
                }
            })
            .catch(err => {
                msgEl.className = 'text-center text-sm font-medium p-3 rounded-xl mt-2 bg-red-500/20 text-red-400 block';
                msgEl.textContent = 'Network error. Please try again.';
            });
        });

/* Extracted from mobile_packages.php */


/* Extracted from mobile_packages.php */
function toggleSort() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('sortSheet').classList.add('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function toggleFilter() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('filterSheet').classList.add('active');
            document.getElementById('sortSheet').classList.remove('active');
        }
        function closeAll() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('sortSheet').classList.remove('active');
            document.getElementById('filterSheet').classList.remove('active');
        }
        function switchFilterPane(paneId, tabEl) {
            document.querySelectorAll('.filter-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            document.getElementById(paneId).classList.add('active');
            tabEl.classList.add('active');
        }
        
        function clearFilters() {
            document.querySelectorAll('.filter-theme, .filter-dest, .filter-dur, .filter-type').forEach(el => el.checked = false);
            document.getElementById('price_min').value = '';
            document.getElementById('price_max').value = '';
            applyFilters();
        }
        
        function applyFiltersAndClose() {
            applyFilters();
            closeAll();
        }
        
        function applyFilters() {
            const themes = Array.from(document.querySelectorAll('.filter-theme:checked')).map(el => el.value);
            const dests = Array.from(document.querySelectorAll('.filter-dest:checked')).map(el => el.value);
            const durs = Array.from(document.querySelectorAll('.filter-dur:checked')).map(el => el.value);
            const types = Array.from(document.querySelectorAll('.filter-type:checked')).map(el => el.value);
            
            const minPrice = parseFloat(document.getElementById('price_min').value) || 0;
            const maxPrice = parseFloat(document.getElementById('price_max').value) || Infinity;
            
            const sortVal = document.querySelector('input[name="sort"]:checked').value;
            
            const cards = Array.from(document.querySelectorAll('.js-card'));
            let visibleCount = 0;
            
            cards.forEach(card => {
                const cardThemes = card.dataset.themes.split('|');
                const cardDest = card.dataset.destination;
                const cardDur = card.dataset.duration;
                const cardType = card.dataset.packageType;
                const cardPrice = parseFloat(card.dataset.price);
                
                let themeMatch = themes.length === 0 || themes.some(t => cardThemes.includes(t));
                let destMatch = dests.length === 0 || dests.includes(cardDest);
                let durMatch = durs.length === 0 || durs.includes(cardDur);
                let typeMatch = types.length === 0 || types.includes(cardType);
                let priceMatch = cardPrice >= minPrice && cardPrice <= maxPrice;
                
                if (themeMatch && destMatch && durMatch && typeMatch && priceMatch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Sort
            const container = document.getElementById('packagesGrid');
            const noResults = document.getElementById('noResults');
            const visibleCards = cards.filter(c => !c.classList.contains('hidden'));
            
            visibleCards.sort((a, b) => {
                if (sortVal === 'price_asc') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                if (sortVal === 'price_desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                if (sortVal === 'duration_asc') return parseInt(a.dataset.days) - parseInt(b.dataset.days);
                if (sortVal === 'duration_desc') return parseInt(b.dataset.days) - parseInt(a.dataset.days);
                return 0; // Default
            });
            
            visibleCards.forEach(card => container.appendChild(card));
            container.appendChild(noResults); // keep no-results at the end
            
            if (visibleCount === 0) {
                const titleEl = document.getElementById('noResultsTitle');
                if (titleEl) {
                    if (dests.length === 1) {
                        const capDest = dests[0].split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                        titleEl.innerHTML = `No signature packages currently found for <span style="color: #C5A059;">"${capDest}"</span>`;
                    } else if (themes.length === 1) {
                        const capTheme = themes[0].charAt(0).toUpperCase() + themes[0].slice(1);
                        titleEl.innerHTML = `No signature tours currently found under <span style="color: #C5A059;">"${capTheme} Escapes"</span>`;
                    } else {
                        titleEl.textContent = 'No packages found matching your criteria';
                    }
                }
                noResults.classList.add('no-results--visible');
            } else {
                noResults.classList.remove('no-results--visible');
            }
        }
        
        window.addEventListener('DOMContentLoaded', () => {
            applyFilters();
        });

/* Extracted from mobile_package_details.php */
// Header Scroll Effect
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                document.getElementById('appHeader')?.classList.add('scrolled');
            } else {
                document.getElementById('appHeader')?.classList.remove('scrolled');
            }
        });

        // Carousel Scroll Sync
        const carousel = document.getElementById('heroCarousel');
        const dots = document.querySelectorAll('.dot');
        carousel?.addEventListener('scroll', () => {
            let index = Math.round(carousel.scrollLeft / carousel.offsetWidth);
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        });

        // Accordion
        function toggleAccordion(header) {
            const item = header.parentElement;
            const content = header.nextElementSibling;
            
            if (item.classList.contains('active')) {
                item.classList.remove('active');
                content.style.maxHeight = null;
            } else {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + "px";
            }
        }

        // Modal
        const overlay = document.getElementById('enquiryModalOverlay');
        const modal = document.getElementById('enquiryModal');
        
        // openModal / closeModal: handled by centralized data-action delegation above.

        function sharePackage() {
            if (navigator.share) {
                const btn = document.querySelector('[data-action="share-package"]');
                const title = btn ? btn.dataset.title : document.title;
                navigator.share({
                    title: title,
                    text: 'Check out this amazing tour package!',
                    url: window.location.href,
                });
            }
        }

        // Map Initialization
        const mapEl = document.getElementById('tourMap');
        if (mapEl) {
            let itineraryData = [];
            let fallbackCoords = [27.3314, 88.6138];
            try {
                if (mapEl.dataset.itinerary) itineraryData = JSON.parse(mapEl.dataset.itinerary);
                if (mapEl.dataset.fallbackCoords) {
                    const parts = mapEl.dataset.fallbackCoords.split(',');
                    if (parts.length === 2) {
                        fallbackCoords = [parseFloat(parts[0]), parseFloat(parts[1])];
                    }
                }
            } catch(e) {}


        const waypoints = itineraryData.map((d, i) => {
            if (d.coords && d.coords.trim()) {
                const p = d.coords.split(',');
                if (p.length === 2) return { lat: parseFloat(p[0]), lng: parseFloat(p[1]), title: d.title || ('Day '+(i+1)) };
            }
            return null;
        }).filter(Boolean);

        const center = waypoints.length ? [waypoints[0].lat, waypoints[0].lng] : fallbackCoords;
        var map = L.map('tourMap', { center: center, zoom: 9, scrollWheelZoom: false, dragging: false, tap: false });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
        }).addTo(map);

        if (waypoints.length > 0) {
            const latlngs = waypoints.map(w => [w.lat, w.lng]);
            var routeLine = L.polyline(latlngs, {
                color: '#C5A059',
                weight: 2,
                opacity: 0.6,
                dashArray: '8, 10'
            }).addTo(map);

            waypoints.forEach((wp) => {
                const pin = L.divIcon({
                    className: '',
                    html: `<div style="background:rgba(7,12,24,0.9);border:1px solid rgba(197,160,89,0.5);color:#C5A059;padding:4px 10px;border-radius:20px;font-size:11px;font-family:Inter,sans-serif;white-space:nowrap;">${wp.title}</div>`,
                    iconAnchor: [0, 0]
                });
                L.marker([wp.lat, wp.lng], { icon: pin }).addTo(map);
            });

            map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
        }

        function enableMap() {
            document.getElementById('mapOverlay').style.opacity = '0';
            setTimeout(() => {
                document.getElementById('mapOverlay').style.display = 'none';
                map.dragging.enable();
                map.scrollWheelZoom.enable();
                if (map.tap) map.tap.enable();
            }, 300);
        }
        } // End of mapEl block

        // ── Bespoke Mobile Stay & Cab Interactive Selection ──────────────────────
        function mSelectStay(idx, val) {
            const totalStays = document.querySelectorAll('[id^="m-stay-card-"]').length || 10;
            for (let i = 0; i < totalStays; i++) {
                const card = document.getElementById('m-stay-card-' + i);
                const btn = document.getElementById('m-stay-btn-' + i);
                if (card && btn) {
                    card.classList.remove('selected');
                    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;">radio_button_unchecked</span><span>Select Category</span>';
                }
            }
            const selCard = document.getElementById('m-stay-card-' + idx);
            const selBtn = document.getElementById('m-stay-btn-' + idx);
            if (selCard && selBtn) {
                selCard.classList.add('selected');
                selBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;">check_circle</span><span>Selected Tier</span>';
            }
            const sumVal = document.getElementById('m_summary_stay_val');
            const inputVal = document.getElementById('m_preferred_stay_input');
            if (sumVal) sumVal.innerText = val;
            if (inputVal) inputVal.value = val;
        }

        function mSelectCab(idx, val) {
            const totalCabs = document.querySelectorAll('[id^="m-cab-card-"]').length || 10;
            for (let i = 0; i < totalCabs; i++) {
                const card = document.getElementById('m-cab-card-' + i);
                const btn = document.getElementById('m-cab-btn-' + i);
                if (card && btn) {
                    card.classList.remove('selected');
                    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;">radio_button_unchecked</span><span>Select Cab</span>';
                }
            }
            const selCard = document.getElementById('m-cab-card-' + idx);
            const selBtn = document.getElementById('m-cab-btn-' + idx);
            if (selCard && selBtn) {
                selCard.classList.add('selected');
                selBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;">check_circle</span><span>Selected Cab</span>';
            }
            const sumVal = document.getElementById('m_summary_cab_val');
            const inputVal = document.getElementById('m_preferred_cab_input');
            if (sumVal) sumVal.innerText = val;
            if (inputVal) inputVal.value = val;
        }

/* Extracted from mobile_splash.php */
(function() {
    const btnLetsTour = document.getElementById('btn-lets-tour');
    if (!btnLetsTour) return;

    const state1 = document.getElementById('splash-state-1');
    const state2 = document.getElementById('splash-state-2');
    const skipBtn = document.getElementById('splash_skip');
    const signupForm = document.getElementById('splash-signup-form');
    const submitBtn = document.getElementById('splash_submit_btn');
    
    // Background Carousel Logic
    const splashContainer = document.getElementById('splash-container');
    const dots = document.querySelectorAll('.splash-dot');
    let currentBg = 1;
    let bgInterval;

    function setBg(idx) {
        currentBg = idx;
        if (splashContainer) {
            splashContainer.style.backgroundImage = `url('assets/img/mobile_splash_bg_${idx}.png')`;
        }
        dots.forEach(dot => {
            if (parseInt(dot.getAttribute('data-idx')) === idx) {
                dot.style.width = '16px';
                dot.style.borderRadius = '4px';
                dot.style.background = '#fff';
            } else {
                dot.style.width = '6px';
                dot.style.borderRadius = '50%';
                dot.style.background = 'rgba(255,255,255,0.4)';
            }
        });
    }

    function nextBg() {
        let next = currentBg + 1;
        if (next > 3) next = 1;
        setBg(next);
    }

    bgInterval = setInterval(nextBg, 4000);

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            clearInterval(bgInterval);
            setBg(parseInt(dot.getAttribute('data-idx')));
            bgInterval = setInterval(nextBg, 4000);
        });
    });

    // Transition to State 2
    btnLetsTour.addEventListener('click', () => {
        if (state1 && state2) {
            state1.style.opacity = '0';
            state1.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                state1.style.display = 'none';
                state2.style.display = 'block';
                // Trigger reflow
                void state2.offsetWidth;
                state2.style.opacity = '1';
                state2.style.transform = 'translateY(0)';
            }, 400);
        }
    });

    // Helper to dismiss splash
    function dismissSplash() {
        sessionStorage.setItem('has_seen_splash', 'true');
        const splashCont = document.getElementById('splash-container');
        const homeView = document.getElementById('mobile-home-view');
        
        if (splashCont) {
            splashCont.style.transition = 'opacity 0.6s ease';
            splashCont.style.opacity = '0';
        }
        
        if (homeView) {
            homeView.style.display = 'block';
            homeView.style.opacity = '0';
            homeView.style.transition = 'opacity 0.6s ease';
            
            // Trigger reflow
            void homeView.offsetWidth;
            homeView.style.opacity = '1';
        }
        
        setTimeout(() => {
            if (splashCont) splashCont.style.display = 'none';
        }, 600);
    }

    if (skipBtn) {
        skipBtn.addEventListener('click', (e) => {
            e.preventDefault();
            dismissSplash();
        });
    }

    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (submitBtn) {
                submitBtn.innerText = 'Saving...';
                submitBtn.disabled = true;
            }

            const formData = new FormData();
            const nameEl = document.getElementById('splash_name');
            const phoneEl = document.getElementById('splash_phone');
            const emailEl = document.getElementById('splash_email');
            
            if (nameEl) formData.append('name', nameEl.value);
            if (phoneEl) formData.append('phone', phoneEl.value);
            if (emailEl) formData.append('email', emailEl.value);
            formData.append('source', 'Mobile App Onboarding');
            formData.append('destination', 'App Onboarding Signup');

            fetch('api-submit-lead.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                dismissSplash();
            })
            .catch(err => {
                console.error('Error submitting splash form:', err);
                // Dismiss anyway so user isn't stuck
                dismissSplash();
            });
        });
    }
})();

})();