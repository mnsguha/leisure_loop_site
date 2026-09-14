<?php $pfx = $modal_prefix ?? ''; ?>
<!-- Bespoke Travel Planner Modal -->
<div id="<?= $pfx ?>plannerModal" class="modal-overlay is-hidden" aria-hidden="true">
    <div class="modal-glass">
        <button class="modal-close" data-action="close-modal" aria-label="Close modal">&times;</button>
        
        <form id="<?= $pfx ?>travelPlannerForm" action="/api/v1/leads" method="POST" class="js-lead-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
            <input type="hidden" name="source" value="Bespoke Planner Modal">
            <div class="planner-steps">
                
                <!-- Step 1: Destination -->
                <div class="step active" data-step="1">
                    <span class="section-label">Step 01 / 04</span>
                    <h2 class="serif">Where does your <br>heart take you?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="destination" value="Sikkim" required>
                            <span>Sikkim</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Kashmir">
                            <span>Kashmir</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Ladakh">
                            <span>Ladakh</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="destination" value="Other">
                            <span>Other Destination</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Timeline -->
                <div class="step" data-step="2">
                    <span class="section-label">Step 02 / 04</span>
                    <h2 class="serif">When is the <br>escape?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="timeline" value="Next 30 Days" required>
                            <span>Next 30 Days</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="1-3 Months">
                            <span>1-3 Months</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="3-6 Months">
                            <span>3-6 Months</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="timeline" value="Just Planning">
                            <span>Just Planning</span>
                        </label>
                    </div>
                </div>

                <!-- Step 3: Travelers -->
                <div class="step" data-step="3">
                    <span class="section-label">Step 03 / 04</span>
                    <h2 class="serif">Who is joining <br>the journey?</h2>
                    <div class="option-grid">
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Solo" required>
                            <span>Solo Traveler</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Couple">
                            <span>Bespoke Couple</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Family">
                            <span>Elite Family</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="travelers" value="Group">
                            <span>Private Group</span>
                        </label>
                    </div>
                </div>

                <!-- Step 4: Contact -->
                <div class="step" data-step="4">
                    <span class="section-label">Final Step</span>
                    <h2 class="serif">How can our <br>curators reach you?</h2>
                    <div class="planner-contact-fields">
                        <div>
                            <label for="<?= $pfx ?>planner_name" class="planner-field-label">Your Name</label>
                            <input type="text" id="<?= $pfx ?>planner_name" name="name" placeholder=" " required class="planner-input" aria-required="true">
                        </div>
                        <div>
                            <label for="<?= $pfx ?>planner_phone" class="planner-field-label">Phone Number</label>
                            <input type="tel" id="<?= $pfx ?>planner_phone" name="phone" placeholder=" " required class="planner-input" aria-required="true">
                        </div>
                        <div>
                            <label for="<?= $pfx ?>planner_email" class="planner-field-label">Email Address</label>
                            <input type="email" id="<?= $pfx ?>planner_email" name="email" placeholder=" " class="planner-input">
                        </div>
                    </div>
                </div>

            </div>

            <div class="planner-footer">
                <button type="button" id="<?= $pfx ?>prevBtn" class="btn-outline is-hidden">Back</button>
                <button type="button" id="<?= $pfx ?>nextBtn" class="btn-gold">Next Step</button>
                <button type="submit" id="<?= $pfx ?>submitBtn" class="btn-gold is-hidden">Request Consultation</button>
            </div>
        </form>
    </div>
</div>
