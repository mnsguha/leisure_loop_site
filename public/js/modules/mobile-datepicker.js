'use strict';

(function() {
    document.addEventListener('DOMContentLoaded', () => {
        const sheet = document.getElementById('mDatePickerSheet');
        const trigger = document.getElementById('mDateGridTrigger');
        const closeBtn = document.getElementById('mDpCloseBtn');
        const doneBtn = document.getElementById('mDpDoneBtn');
        const calendarBody = document.getElementById('mDpCalendarBody');
        
        // Display elements in the form
        const dispCheckInDay = document.getElementById('mDisplayCheckInDay');
        const dispCheckInYear = document.getElementById('mDisplayCheckInYear');
        const dispCheckOutDay = document.getElementById('mDisplayCheckOutDay');
        const dispCheckOutYear = document.getElementById('mDisplayCheckOutYear');
        const dispNights = document.getElementById('mDisplayNightsPill');
        
        // Hidden inputs
        const inputCheckIn = document.getElementById('mCheckInDate');
        const inputCheckOut = document.getElementById('mCheckOutDate');
        
        // Header display in modal
        const headerCheckIn = document.getElementById('mDpCheckInVal');
        const headerCheckOut = document.getElementById('mDpCheckOutVal');

        if (!sheet || !trigger || !calendarBody) return;

        let checkInDate = inputCheckIn && inputCheckIn.value ? new Date(inputCheckIn.value) : new Date();
        let checkOutDate = inputCheckOut && inputCheckOut.value ? new Date(inputCheckOut.value) : new Date(Date.now() + 86400000);
        let currentSelectionStep = 'checkIn'; // 'checkIn' or 'checkOut'
        let isOpen = false;

        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        function formatDateDisplay(d) {
            if (!d || isNaN(d.getTime())) return "Select Date";
            return `${d.getDate()} ${months[d.getMonth()]} '${d.getFullYear().toString().substr(-2)}`;
        }

        function formatInputDate(d) {
            if (!d || isNaN(d.getTime())) return "";
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function renderCalendar() {
            calendarBody.innerHTML = '';
            const today = new Date();
            today.setHours(0,0,0,0);
            
            // Render next 12 months
            for (let i = 0; i < 12; i++) {
                const renderMonth = new Date(today.getFullYear(), today.getMonth() + i, 1);
                
                const monthDiv = document.createElement('div');
                monthDiv.className = 'm-datepicker-month';
                
                const title = document.createElement('div');
                title.className = 'm-datepicker-month-title';
                title.textContent = `${months[renderMonth.getMonth()]} ${renderMonth.getFullYear()}`;
                monthDiv.appendChild(title);
                
                const grid = document.createElement('div');
                grid.className = 'm-datepicker-grid';
                
                const firstDayIndex = renderMonth.getDay();
                const daysInMonth = new Date(renderMonth.getFullYear(), renderMonth.getMonth() + 1, 0).getDate();
                
                // Empty cells for first week
                for (let j = 0; j < firstDayIndex; j++) {
                    const empty = document.createElement('div');
                    empty.className = 'm-datepicker-cell is-empty';
                    grid.appendChild(empty);
                }
                
                for (let d = 1; d <= daysInMonth; d++) {
                    const cellDate = new Date(renderMonth.getFullYear(), renderMonth.getMonth(), d);
                    const cell = document.createElement('div');
                    cell.className = 'm-datepicker-cell';
                    
                    const numWrap = document.createElement('div');
                    numWrap.className = 'm-datepicker-date-num';
                    numWrap.textContent = d;
                    cell.appendChild(numWrap);
                    
                    if (cellDate < today) {
                        cell.classList.add('is-disabled');
                    } else {
                        cell.dataset.date = cellDate.getTime();
                        cell.addEventListener('click', () => handleDateClick(cellDate));
                    }
                    
                    grid.appendChild(cell);
                }
                
                monthDiv.appendChild(grid);
                calendarBody.appendChild(monthDiv);
            }
            updateHighlighting();
        }

        function handleDateClick(clickedDate) {
            clickedDate.setHours(0,0,0,0);
            
            if (currentSelectionStep === 'checkIn') {
                checkInDate = clickedDate;
                checkOutDate = null;
                currentSelectionStep = 'checkOut';
            } else {
                if (clickedDate <= checkInDate) {
                    checkInDate = clickedDate;
                    checkOutDate = null;
                } else {
                    checkOutDate = clickedDate;
                    currentSelectionStep = 'checkIn'; // Reset back
                }
            }
            updateHighlighting();
        }

        function updateHighlighting() {
            headerCheckIn.textContent = formatDateDisplay(checkInDate);
            headerCheckOut.textContent = formatDateDisplay(checkOutDate);
            
            doneBtn.disabled = !(checkInDate && checkOutDate);
            if (doneBtn.disabled) {
                doneBtn.textContent = 'Select Dates';
            } else {
                const diffTime = Math.abs(checkOutDate - checkInDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                doneBtn.textContent = `Done (${diffDays} Night${diffDays > 1 ? 's' : ''})`;
            }

            const cells = calendarBody.querySelectorAll('.m-datepicker-cell[data-date]');
            cells.forEach(cell => {
                const cellTime = parseInt(cell.dataset.date, 10);
                cell.classList.remove('is-start', 'is-end', 'is-in-range');
                
                if (checkInDate && cellTime === checkInDate.getTime()) {
                    cell.classList.add('is-start');
                }
                if (checkOutDate && cellTime === checkOutDate.getTime()) {
                    cell.classList.add('is-end');
                }
                if (checkInDate && checkOutDate && cellTime > checkInDate.getTime() && cellTime < checkOutDate.getTime()) {
                    cell.classList.add('is-in-range');
                }
            });
        }

        function openSheet() {
            isOpen = true;
            sheet.classList.remove('is-hidden');
            sheet.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            history.pushState({ modal: 'datepicker' }, '');
            
            // Refresh from inputs in case they cancelled previously
            if (inputCheckIn && inputCheckIn.value) checkInDate = new Date(inputCheckIn.value);
            if (inputCheckOut && inputCheckOut.value) checkOutDate = new Date(inputCheckOut.value);
            currentSelectionStep = 'checkIn';
            
            renderCalendar();
            
            // Scroll to check in month
            if (checkInDate) {
                setTimeout(() => {
                    const startCell = calendarBody.querySelector('.is-start');
                    if (startCell) {
                        startCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            }
        }

        function closeSheet() {
            isOpen = false;
            sheet.classList.add('is-hidden');
            sheet.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (history.state && history.state.modal === 'datepicker') {
                history.back();
            }
        }

        // Apply selections to the form fields
        function applyDates() {
            if (checkInDate && checkOutDate) {
                if (inputCheckIn) inputCheckIn.value = formatInputDate(checkInDate);
                if (inputCheckOut) inputCheckOut.value = formatInputDate(checkOutDate);
                
                if (dispCheckInDay) {
                    const padD = String(checkInDate.getDate()).padStart(2, '0');
                    dispCheckInDay.textContent = `${padD} ${months[checkInDate.getMonth()]}`;
                    dispCheckInYear.textContent = `'${checkInDate.getFullYear().toString().substr(-2)}, ${checkInDate.toLocaleDateString('en-US', {weekday: 'short'})}`;
                }
                
                if (dispCheckOutDay) {
                    const padD = String(checkOutDate.getDate()).padStart(2, '0');
                    dispCheckOutDay.textContent = `${padD} ${months[checkOutDate.getMonth()]}`;
                    dispCheckOutYear.textContent = `'${checkOutDate.getFullYear().toString().substr(-2)}, ${checkOutDate.toLocaleDateString('en-US', {weekday: 'short'})}`;
                }
                
                if (dispNights) {
                    const diffTime = Math.abs(checkOutDate - checkInDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    dispNights.textContent = `${diffDays}N`;
                }
            }
            closeSheet();
        }

        trigger.addEventListener('click', openSheet);
        closeBtn.addEventListener('click', closeSheet);
        doneBtn.addEventListener('click', applyDates);
        
        // Handle android back button
        window.addEventListener('popstate', (e) => {
            if (isOpen) {
                isOpen = false;
                sheet.classList.add('is-hidden');
                sheet.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        });
        
        // Close on overlay click
        sheet.addEventListener('click', (e) => {
            if (e.target === sheet) closeSheet();
        });
    });
})();
