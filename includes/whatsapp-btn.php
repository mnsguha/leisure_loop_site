<!-- EaseMyTrip-Inspired Floating Concierge Dock Hub -->
<div class="emt-floating-dock-hub" data-drag-hub>
    <!-- Top Pill: Plan Your Trip (Enquiry Modal Trigger) -->
    <a href="#inquiry-form" class="emt-dock-pill emt-pill-orange btn-enquiry-nav" aria-label="Plan Your Trip" draggable="false">
        <div class="emt-pill-border-glow"></div>
        <div class="emt-pill-content">
            <span class="emt-pill-icon">
                <!-- Travel Planner / Custom Tour Icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </span>
            <div class="emt-pill-text-stack">
                <span class="emt-pill-title">Plan Your Trip</span>
                <span class="emt-pill-sub-hint">Custom Tour Quotes</span>
            </div>
            <span class="expand-chevron style-orange-arrow">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.6" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </span>
        </div>
    </a>

    <!-- Option 3 Unified Pill: 24/7 Concierge Hotline (Interactive Phone + WhatsApp Expandable Dock) -->
    <div class="emt-dock-pill emt-pill-unified" id="unifiedConciergeDock">
        <div class="emt-pill-border-glow-unified"></div>
        <div class="emt-pill-content unified-content-container">
            <!-- Collapsed Header / Interactive Trigger -->
            <div class="unified-trigger-head">
                <span class="emt-pill-icon unified-dual-icon">
                    <!-- Integrated Headset & WhatsApp Communication Badge -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        <!-- Vivid green status beacon -->
                        <circle cx="18" cy="6" r="3.5" fill="#25d366" stroke="#ffffff" stroke-width="1.5"></circle>
                    </svg>
                </span>
                <div class="emt-pill-text-stack">
                    <span class="emt-pill-title">24/7 Concierge Hotline</span>
                    <span class="emt-pill-sub-hint">Voice Call &middot; WhatsApp Chat</span>
                </div>
                <span class="expand-chevron">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </span>
            </div>

            <!-- Expandable Dual Communication Panel -->
            <div class="unified-channels-panel">
                <div class="channels-divider-line"></div>
                <div class="unified-actions-grid">
                    <!-- Action 1: Direct Voice Hotline -->
                    <a href="tel:+918918921629" class="channel-btn call-channel" title="Direct Telephone Hotline">
                        <div class="channel-icon badge-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="channel-info">
                            <span class="channel-label">Call Curator</span>
                            <span class="channel-num">+91 89189 21629</span>
                        </div>
                    </a>

                    <!-- Action 2: Live WhatsApp Chat -->
                    <a href="https://wa.me/918918921629?text=Hello%20Leisure%20Loop%20Trip!%20I%20am%20interested%20in%20planning%20an%20elite%20escape." target="_blank" class="channel-btn whatsapp-channel" title="Chat on Official WhatsApp">
                        <div class="channel-icon badge-green">
                            <svg viewBox="0 0 448 512" fill="currentColor" width="18" height="18"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.6-2.8-23.6-8.7-45-27.7-16.6-14.8-27.8-33.1-31.1-38.6-3.2-5.6-.3-8.6 2.5-11.4 2.5-2.5 5.5-6.5 8.3-9.7 2.8-3.2 3.7-5.6 5.6-9.3 1.9-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.6 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                        </div>
                        <div class="channel-info">
                            <span class="channel-label">WhatsApp Live</span>
                            <span class="channel-num">Chat with Curator</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
