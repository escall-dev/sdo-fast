<?php
/**
 * Top Navbar Include for SDO FAST.
 * Features sidebar toggle, page title, and live date/time clock.
 */
?>
<div class="main-content">
    <header class="top-navbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <!-- Sidebar Toggle Button (Desktop & Mobile) -->
            <button class="btn-sidebar-toggle me-3" type="button" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-layout-sidebar-inset fs-5"></i>
            </button>
            <h4 class="mb-0 text-primary-dark fw-bold"><?php echo isset($pageHeader) ? htmlspecialchars($pageHeader) : 'Dashboard'; ?></h4>
        </div>
        
        <!-- Live Date & Clock -->
        <div class="d-flex align-items-center gap-2 text-muted" id="liveDatetimeContainer" style="font-size: 0.88rem; letter-spacing: 0.2px;">
            <span id="liveDateDisplay"></span>
            <span id="liveClockDisplay" class="fw-bold" style="color: var(--color-primary);"></span>
        </div>
    </header>
    <div class="content-container">
