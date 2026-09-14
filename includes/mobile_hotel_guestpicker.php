<!-- ── Mobile Guest Picker Bottom Sheet ─────────────────────────────── -->
<div id="mGuestPickerSheet" class="mhd-bottom-sheet is-hidden" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="mhd-sheet-overlay" data-action="close-guest-sheet"></div>
    <div class="mhd-sheet-content">
        <!-- Header -->
        <div class="mhd-sheet-header">
            <button type="button" class="mhd-sheet-close" data-action="close-guest-sheet" aria-label="Close">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h2 class="mhd-sheet-title">Select Rooms and Guests</h2>
        </div>

        <!-- Body -->
        <div class="mhd-sheet-body">
            
            <div class="mhd-gp-row">
                <div class="mhd-gp-label-col">
                    <span class="mhd-gp-label">Rooms</span>
                </div>
                <div class="mhd-gp-input-col">
                    <select id="mGpRoomsSelect" class="mhd-gp-select" aria-label="Select number of rooms">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == ($rooms ?? 1) ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="mhd-gp-row">
                <div class="mhd-gp-label-col">
                    <span class="mhd-gp-label">Adults</span>
                </div>
                <div class="mhd-gp-input-col">
                    <select id="mGpAdultsSelect" class="mhd-gp-select" aria-label="Select number of adults">
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == ($adults ?? 2) ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="mhd-gp-row mhd-gp-row--children">
                <div class="mhd-gp-label-col">
                    <span class="mhd-gp-label">Children</span>
                    <span class="mhd-gp-sublabel">0 - 17 Years old</span>
                </div>
                <div class="mhd-gp-input-col">
                    <select id="mGpChildrenSelect" class="mhd-gp-select" aria-label="Select number of children">
                        <?php for ($i = 0; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == ($children ?? 0) ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <p class="mhd-gp-help-text">Please provide right number of children along with their right age for best options and prices.</p>

        </div>

        <!-- Footer -->
        <div class="mhd-sheet-footer">
            <button type="button" class="mhd-dp-done-btn" id="mGpDoneBtn">DONE</button>
        </div>
    </div>
</div>
