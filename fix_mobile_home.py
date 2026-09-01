import sys
content = open('g:/Antigravity/leisure_loop_site/includes/mobile_home.php', 'r', encoding='utf-8').read()

parts = content.split("<?php include __DIR__ . '/mobile_bottom_nav.php'; ?>")
if len(parts) >= 2:
    good_part = parts[0]
    rest = """<?php include __DIR__ . '/mobile_bottom_nav.php'; ?>

<!-- Search Bottom Sheet Modal -->
<style>
    .search-modal-overlay { position: fixed; inset: 0; background: rgba(5,10,20,0.8); backdrop-filter: blur(10px); z-index: 200; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
    .search-modal-overlay.active { opacity: 1; pointer-events: auto; }
    .search-modal-content { position: fixed; bottom: 0; left: 0; width: 100%; max-height: 90vh; background: #0a0a0c; border-top-left-radius: 24px; border-top-right-radius: 24px; z-index: 201; transform: translateY(100%); transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1); display: flex; flex-direction: column; border-top: 1px solid rgba(197, 160, 89, 0.2); }
    .search-modal-content.active { transform: translateY(0); }
</style>

<div class="search-modal-overlay" id="searchModalOverlay" onclick="closeSearchModal()"></div>
<div class="search-modal-content pb-6" id="searchModal">
    <div class="flex justify-between items-center p-6 border-b border-white/5">
        <div>
            <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold block mb-1">BESPOKE TRAVEL</span>
            <h2 class="font-serif italic text-2xl text-white m-0">Plan Your Journey</h2>
        </div>
        <button onclick="closeSearchModal()" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white border-none">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto">
        <form action="api-submit-lead.php" method="POST" class="space-y-4">
            <input type="hidden" name="source" value="mobile_home_search">
            
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <input class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Where do you want to go?" type="text" name="destination" required/>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
                <input class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Travel Date" type="text" onfocus="(this.type='date')" name="date" required/>
            </div>
            
            <button type="submit" class="w-full text-black py-4 rounded-xl font-bold text-[16px] shadow-[0_4px_15px_rgba(197,160,89,0.3)] active:scale-[0.98] transition-transform flex justify-center items-center gap-2 mt-4" style="background: linear-gradient(135deg, #e6c888, #b88a44);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Search Trips
            </button>
        </form>
    </div>
</div>

<!-- Location Bottom Sheet Modal -->
<div class="search-modal-overlay" id="locationModalOverlay" onclick="closeLocationModal()"></div>
<div class="search-modal-content pb-6" id="locationModal">
    <div class="flex justify-between items-center p-6 border-b border-white/5">
        <div>
            <span class="text-gold text-[10px] uppercase tracking-[0.2em] font-bold block mb-1">CURRENT CITY</span>
            <h2 class="font-serif italic text-2xl text-white m-0">Select Location</h2>
        </div>
        <button onclick="closeLocationModal()" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white border-none">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="p-6 overflow-y-auto">
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-[#c5a059]" style="font-size: 18px;">search</span>
            </div>
            <input class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[16px] placeholder-white/40 outline-none transition-all font-sans" placeholder="Search for your city..." type="text" id="citySearchInput" onkeyup="filterCities()"/>
        </div>
        
        <!-- Use my current location -->
        <button onclick="useCurrentLocation()" id="currentLocationBtn" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 flex items-center gap-3 mb-6 hover:bg-white/10 active:scale-[0.98] transition-all text-left">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-400" style="font-size: 22px;">my_location</span>
            </div>
            <div class="flex-1">
                <div class="text-blue-400 font-medium text-[15px] mb-0.5" id="currentLocationBtnText">Use my current location</div>
                <div class="text-white/40 text-[12px]">Allow access to location</div>
            </div>
        </button>

        <div class="space-y-3" id="cityList">
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" onclick="selectCity('New Delhi')">
                <span class="material-symbols-outlined text-white/50" style="font-size: 20px;">location_city</span>
                <span class="text-white text-[15px]">New Delhi, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" onclick="selectCity('Mumbai')">
                <span class="material-symbols-outlined text-white/50" style="font-size: 20px;">location_city</span>
                <span class="text-white text-[15px]">Mumbai, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" onclick="selectCity('Bangalore')">
                <span class="material-symbols-outlined text-white/50" style="font-size: 20px;">location_city</span>
                <span class="text-white text-[15px]">Bangalore, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" onclick="selectCity('Kolkata')">
                <span class="material-symbols-outlined text-white/50" style="font-size: 20px;">location_city</span>
                <span class="text-white text-[15px]">Kolkata, India</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 active:bg-white/10 transition-colors cursor-pointer" onclick="selectCity('Chennai')">
                <span class="material-symbols-outlined text-white/50" style="font-size: 20px;">location_city</span>
                <span class="text-white text-[15px]">Chennai, India</span>
            </div>
        </div>
    </div>
</div>

<script>
    function openSearchModal() {
        document.getElementById('searchModalOverlay').classList.add('active');
        document.getElementById('searchModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSearchModal() {
        document.getElementById('searchModalOverlay').classList.remove('active');
        document.getElementById('searchModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    function openLocationModal() {
        document.getElementById('locationModalOverlay').classList.add('active');
        document.getElementById('locationModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeLocationModal() {
        document.getElementById('locationModalOverlay').classList.remove('active');
        document.getElementById('locationModal').classList.remove('active');
        document.body.style.overflow = '';
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
                items[i].style.display = 'flex';
            } else {
                items[i].style.display = 'none';
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
</script>

</main>
</body>
</html>
"""
    with open('g:/Antigravity/leisure_loop_site/includes/mobile_home.php', 'w', encoding='utf-8') as f:
        f.write(good_part + rest)
    print("Fixed!")
