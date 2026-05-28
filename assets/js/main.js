/**
 * Main UI Controller for SDO FAST.
 * Handles sidebar toggling, live clock, chatbot interactions, and global responsive layouts.
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Chatbot Widget Toggle Logic (Moved to footer.php inline script for persistent states)


    // 2. Initialize Bootstrap Tooltips/Popovers if present
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined') {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // 3. Sidebar Toggle Trigger (Desktop & Mobile support)
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                // Desktop: toggle collapsed class on body
                document.body.classList.toggle('sidebar-collapsed');
                var isCollapsed = document.body.classList.contains('sidebar-collapsed');
                try { localStorage.setItem('sidebar-collapsed', isCollapsed); } catch(e) {}
            } else {
                // Mobile: open/close offcanvas
                var mobileSidebar = document.getElementById('mobileSidebar');
                if (mobileSidebar && typeof bootstrap !== 'undefined') {
                    var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(mobileSidebar);
                    bsOffcanvas.toggle();
                }
            }
        });
    }

    // 4. Live Date & Clock
    function updateDatetime() {
        var now = new Date();

        // Day & Date format: "Thursday, May 28, 2026"
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var months = ['January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];

        var dayName = days[now.getDay()];
        var monthName = months[now.getMonth()];
        var dateNum = now.getDate();
        var year = now.getFullYear();

        var dateStr = dayName + ', ' + monthName + ' ' + dateNum + ', ' + year;

        // Clock format: "08:39:58 PM"
        var hours = now.getHours();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        if (hours === 0) hours = 12;
        var mins = now.getMinutes().toString().padStart(2, '0');
        var secs = now.getSeconds().toString().padStart(2, '0');
        var timeStr = hours.toString().padStart(2, '0') + ':' + mins + ':' + secs + ' ' + ampm;

        var dateEl = document.getElementById('liveDateDisplay');
        var clockEl = document.getElementById('liveClockDisplay');

        if (dateEl) dateEl.textContent = dateStr;
        if (clockEl) clockEl.textContent = timeStr;
    }

    // Run immediately then every second
    updateDatetime();
    setInterval(updateDatetime, 1000);
});
