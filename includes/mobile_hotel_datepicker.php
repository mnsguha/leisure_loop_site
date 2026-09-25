<!-- ── Mobile Full-Screen Date Picker (Hotel Detail) ──────────────────── -->
<div id="mDatePickerSheet" class="mhd-fs-modal is-hidden" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="mhd-fs-modal-content">
        <!-- Header -->
        <div class="mhd-fs-header">
            <button type="button" class="mhd-fs-back" id="mDpCloseBtn" aria-label="Close">
                <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <h2 class="mhd-fs-title" id="mDpTitle">Select Check-In Date</h2>
            <button type="button" class="mhd-fs-reset" id="mDpResetBtn">Reset</button>
        </div>

        <!-- Weekdays -->
        <div class="mhd-dp-weekdays">
            <span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span><span>SUN</span>
        </div>

        <!-- Scrollable Calendar Body -->
        <div class="mhd-dp-body" id="mDpCalendarBody">
            <?php
            // Generate 12 months for the calendar body
            $monthsToGenerate = 12;
            $currentDate = new DateTime();
            $calendarHtml = '';

            for ($m = 0; $m < $monthsToGenerate; $m++) {
                $monthDate = clone $currentDate;
                $monthDate->modify("+$m month");
                $monthName = $monthDate->format('F Y');
                $daysInMonth = $monthDate->format('t');
                $firstDay = clone $monthDate;
                $firstDay->modify('first day of this month');
                $dayOfWeek = $firstDay->format('N'); // 1 (Mon) - 7 (Sun)
                
                $calendarHtml .= "<div class='mhd-dp-month-title'>{$monthName}</div>";
                $calendarHtml .= "<div class='mhd-dp-days-grid'>";
                
                // Blank days
                for ($i = 1; $i < $dayOfWeek; $i++) {
                    $calendarHtml .= "<div class='mhd-dp-day mhd-dp-empty'></div>";
                }
                
                // Days
                $todayStr = date('Y-m-d');
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateStr = $monthDate->format('Y-m-') . str_pad((string)$d, 2, '0', STR_PAD_LEFT);
                    $disabledCls = ($dateStr < $todayStr) ? ' mhd-dp-disabled' : '';
                    $calendarHtml .= "<div class='mhd-dp-day{$disabledCls}' data-date='{$dateStr}'>{$d}</div>";
                }
                
                $calendarHtml .= "</div>";
            }
            echo $calendarHtml;
            ?>
        </div>

        <!-- Sticky Bottom Footer -->
        <div class="mhd-dp-footer">
            <div class="mhd-dp-selection-row">
                <!-- Check-in Box -->
                <div class="mhd-dp-box is-active" id="mDpCheckInBox">
                    <span class="mhd-dp-box-label">CHECK-IN DATE</span>
                    <span class="mhd-dp-box-val" id="mDpCheckInVal">Select Date</span>
                </div>

                <!-- Night Pill -->
                <div class="mhd-dp-night-pill" id="mDpNightPill">0 NIGHTS</div>

                <!-- Check-out Box -->
                <div class="mhd-dp-box" id="mDpCheckOutBox">
                    <span class="mhd-dp-box-label">CHECK-OUT DATE</span>
                    <span class="mhd-dp-box-val" id="mDpCheckOutVal">Select Date</span>
                </div>
            </div>
            
            <button type="button" class="mhd-dp-done-btn" id="mDpDoneBtn">DONE</button>
        </div>
    </div>
</div>
