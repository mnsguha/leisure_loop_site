<?php $pfx = $modal_prefix ?? ''; ?>
<!-- Enquiry Popup Modal -->
<div id="<?= $pfx ?>enquiryModal" class="enquiry-modal-overlay is-hidden" aria-hidden="true">
    <div class="enquiry-modal-glass">
        <button class="enquiry-modal-close" data-action="close-modal" aria-label="Close modal">&times;</button>
        
        <div class="enquiry-modal-grid">
            
            <!-- Left Pane: Form -->
            <div class="enquiry-modal-form-pane">
                <div class="enquiry-modal-header">
                    <span class="section-label-gold">Start Your Journey</span>
                    <h2 class="serif">Where do you want to go?</h2>
                </div>
                
                <form id="<?= $pfx ?>enquiryPopupForm" action="api-submit-lead.php" method="POST" class="js-lead-form">
                    <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                    
                    <div class="grid-2-cols enq-grid-row">
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></div>
                            <input type="text" name="date" id="<?= $pfx ?>enq_date" class="form-control-float date-input" placeholder=" " required aria-required="true">
                            <label for="<?= $pfx ?>enq_date" class="form-label-float">Travel Date</label>
                        </div>
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                            <input type="text" name="name" id="<?= $pfx ?>enq_name" class="form-control-float" placeholder=" " required aria-required="true">
                            <label for="<?= $pfx ?>enq_name" class="form-label-float">Name</label>
                        </div>
                    </div>
                    
                    <div class="grid-2-cols enq-grid-row">
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></div>
                            <input type="tel" name="phone" id="<?= $pfx ?>enq_phone" class="form-control-float" placeholder=" " required aria-required="true">
                            <label for="<?= $pfx ?>enq_phone" class="form-label-float">Phone No</label>
                        </div>
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></div>
                            <input type="email" name="email" id="<?= $pfx ?>enq_email" class="form-control-float" placeholder=" " required aria-required="true">
                            <label for="<?= $pfx ?>enq_email" class="form-label-float">Email ID</label>
                        </div>
                    </div>
                    
                    <div class="grid-2-cols enq-grid-row">
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                            <select name="adults" id="<?= $pfx ?>enq_adults" class="form-control-float enq-glass-select" required>
                                <option value="" disabled selected hidden>No. of Adults</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="10+">10+</option>
                            </select>
                        </div>
                        <div class="form-group-float enq-icon-field">
                            <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>
                            <select name="destination" id="<?= $pfx ?>enq_destination" class="form-control-float enq-glass-select" required>
                                <option value="" disabled selected hidden>Select Destination</option>
                                <option value="Darjeeling">Darjeeling</option>
                                <option value="Kalimpong">Kalimpong</option>
                                <option value="Sikkim">Sikkim</option>
                                <option value="Dooars">Dooars</option>
                                <option value="Assam">Assam</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Andaman & Nicobar">Andaman & Nicobar</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Kashmir">Kashmir</option>
                                <option value="Others">Others</option>
                                <option value="International">International</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group-float enq-icon-field enq-field-row">
                        <div class="field-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>
                        <textarea name="message" id="<?= $pfx ?>enq_message" class="form-control-float" rows="2" placeholder=" "></textarea>
                        <label for="<?= $pfx ?>enq_message" class="form-label-float">Message Here</label>
                    </div>
                    
                    <input type="hidden" name="source" value="Header Enquiry Popup">
                    
                    <button type="submit" class="btn-gold enq-submit-btn">
                        GET A QUOTE
                    </button>
                    
                </form>
            </div>
            
            <!-- Right Pane: Value Proposition & Trust -->
            <div class="enquiry-modal-info-pane">
                <div class="enq-info-map-overlay"></div>
                
                <div class="enq-info-body">
                    <h3 class="serif enq-info-title">The Art of <br><span class="enq-info-title-sub">Discovering</span></h3>
                    
                    <p class="enq-info-desc">
                        We don't just book holidays; we craft unforgettable journeys tailored exclusively for you. 
                    </p>

                    <div class="enq-feature-list">
                        <div class="enq-feature-item">
                            <div class="enq-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                            <div>
                                <h4 class="enq-feature-heading">100% Custom Itineraries</h4>
                                <p class="enq-feature-text">Designed around your pace, preferences, and passions.</p>
                            </div>
                        </div>

                        <div class="enq-feature-item">
                            <div class="enq-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div>
                                <h4 class="enq-feature-heading">24/7 Concierge Support</h4>
                                <p class="enq-feature-text">Real-time assistance anywhere, anytime during your trip.</p>
                            </div>
                        </div>

                        <div class="enq-feature-item">
                            <div class="enq-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div>
                                <h4 class="enq-feature-heading">Local Hidden Gems</h4>
                                <p class="enq-feature-text">Access to exclusive spots beyond the tourist traps.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="enq-expert-card">
                        <div class="enq-expert-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div>
                            <p class="enq-expert-label">Speak to an Expert</p>
                            <a href="tel:+918918921629" class="enq-expert-phone">+91 89189 21629</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
