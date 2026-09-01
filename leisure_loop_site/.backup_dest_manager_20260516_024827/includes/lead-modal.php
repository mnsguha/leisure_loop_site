<!-- Bespoke Travel Planner Modal -->
<div id="plannerModal" class="modal-overlay" style="display: none;">
    <div class="modal-glass">
        <button class="modal-close" onclick="closePlanner()">&times;</button>
        
        <form id="travelPlannerForm">
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
                    <div style="margin-top: 2rem;">
                        <input type="text" name="name" placeholder="YOUR NAME" required class="planner-input">
                        <input type="tel" name="phone" placeholder="PHONE NUMBER" required class="planner-input">
                        <input type="email" name="email" placeholder="EMAIL ADDRESS" class="planner-input">
                    </div>
                </div>

            </div>

            <div class="planner-footer">
                <button type="button" id="prevBtn" class="btn-outline" style="display: none;">Back</button>
                <button type="button" id="nextBtn" class="btn-gold">Next Step</button>
                <button type="submit" id="submitBtn" class="btn-gold" style="display: none;">Request Consultation</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(7, 12, 24, 0.95);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(20px);
}
.modal-glass {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    padding: 5rem;
    width: min(800px, 92%);
    position: relative;
    text-align: center;
}
.modal-close {
    position: absolute;
    top: 2rem;
    right: 2rem;
    background: transparent;
    border: none;
    color: var(--text-muted);
    font-size: 2rem;
    cursor: pointer;
}
.planner-steps .step {
    display: none;
}
.planner-steps .step.active {
    display: block;
    animation: fadeIn 0.8s var(--ease-out);
}
.option-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-top: 3rem;
}
.option-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--glass-border);
    padding: 2rem;
    cursor: pointer;
    transition: all 0.3s;
}
.option-card:hover {
    border-color: var(--gold);
    background: rgba(255,255,255,0.05);
}
.option-card input {
    display: none;
}
.option-card input:checked + span {
    color: var(--gold);
}
.option-card:has(input:checked) {
    border-color: var(--gold);
    box-shadow: 0 0 20px var(--amber-glow);
}
.planner-input {
    width: 100%;
    background: transparent;
    border: none;
    border-bottom: 1px solid var(--glass-border);
    padding: 1rem 0;
    margin-bottom: 2rem;
    color: #fff;
    outline: none;
    font-size: 1.1rem;
    letter-spacing: 0.1em;
}
.planner-footer {
    margin-top: 4rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
}
</style>
