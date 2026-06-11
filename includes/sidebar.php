<?php
/**
 * Responsive Sidebar Navigation Include for SDO FAST.
 * Renders fixed sidebar for desktop and offcanvas for mobile.
 * Uses Font Awesome 6.5.1 for nav icons, Boxicons 2.1.4 for logout.
 */

// Helper to determine active class
function isPageActive($pages) {
    global $currentPage;
    if (is_array($pages)) {
        return in_array($currentPage, $pages) ? 'active' : '';
    }
    return $currentPage === $pages ? 'active' : '';
}

$userRole = $_SESSION['user_role'] ?? '';
$userPosition = $_SESSION['user_position'] ?? '';

// Build Navigation Links visibility conditions
$showDashboard = true;
$showTransactions = true;
$showTracker = true;
$showReports = hasPermission('view');
$showAuditLogs = hasPermission('configure_system');
$showUserManagement = hasPermission('manage_users');
$showIntegrationMonitor = hasPermission('configure_system');
$showSettings = hasPermission('configure_system');

// User Profile Section HTML content generator
$renderUserProfileSection = function() {
    if (!isLoggedIn()) {
        return;
    }
    $baseUrl = env('APP_URL');
    $fullName = $_SESSION['user_name'] ?? 'User';
    $role = $_SESSION['user_role'] ?? 'User';
    $position = $_SESSION['user_position'] ?? '';
    
    // Generate initials for avatar fallback
    $nameParts = explode(' ', $fullName);
    $initials = '';
    foreach (array_slice($nameParts, 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    $initials = $initials ?: 'U';
    ?>
    <div class="sidebar-user-profile d-flex align-items-center justify-content-between p-3">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
            <!-- User Avatar -->
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary-dark" 
                 style="width: 40px; height: 40px; min-width: 40px; background-color: #ffffff; font-size: 0.95rem; border: 2px solid rgba(255, 255, 255, 0.2);">
                <?php echo htmlspecialchars($initials); ?>
            </div>
            <!-- User Details -->
            <div class="d-flex flex-column text-start overflow-hidden">
                <span class="fw-bold text-white text-truncate fs-7" title="<?php echo htmlspecialchars($fullName); ?>" style="max-width: 120px; display: inline-block;">
                    <?php echo htmlspecialchars($fullName); ?>
                </span>
                <span class="text-white-50" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?php echo htmlspecialchars($position ?: $role); ?>
                </span>
            </div>
        </div>
        <!-- Logout Button (Boxicons) -->
        <a href="<?php echo $baseUrl; ?>/api/auth/logout.php" class="btn btn-logout-sidebar" title="Log Out">
            <i class="bx bx-log-out fs-5"></i>
        </a>
    </div>
    <?php
};

// Navigation HTML content generator
$renderSidebarMenu = function($isMobile = false) use (
    $showDashboard, $showTransactions, $showTracker, $showReports, 
    $showAuditLogs, $showUserManagement, $showIntegrationMonitor, $showSettings, $userRole, $userPosition
) {
    $baseUrl = env('APP_URL');
    ?>
    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <?php if ($showDashboard): ?>
            <li class="<?php echo isPageActive('dashboard'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/dashboard/index.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Transactions Submenu -->
        <?php if ($showTransactions): ?>
            <li class="nav-item">
                <a class="d-flex align-items-center justify-content-between <?php echo isPageActive(['all_transactions', 'cash_advance', 'reimbursement', 'payroll', 'bactrack', 'submit_transaction']) ? 'collapsed' : ''; ?>" 
                   data-bs-toggle="collapse" 
                   href="#transactionsCollapse<?php echo $isMobile ? 'Mobile' : ''; ?>" 
                   role="button" 
                   aria-expanded="<?php echo isPageActive(['all_transactions', 'cash_advance', 'reimbursement', 'payroll', 'bactrack', 'submit_transaction']) ? 'true' : 'false'; ?>" 
                   aria-controls="transactionsCollapse<?php echo $isMobile ? 'Mobile' : ''; ?>">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-folder-open"></i>
                        <span>Transactions</span>
                    </div>
                    <i class="bi bi-chevron-down fs-8"></i>
                </a>
                <div class="collapse <?php echo isPageActive(['all_transactions', 'cash_advance', 'reimbursement', 'payroll', 'bactrack', 'submit_transaction']) ? 'show' : ''; ?>" id="transactionsCollapse<?php echo $isMobile ? 'Mobile' : ''; ?>">
                    <ul class="list-unstyled ps-4 py-1" style="background-color: rgba(0, 0, 0, 0.1); border-radius: 6px; margin: 2px 8px;">
                        <?php if (hasPermission('view')): ?>
                            <li class="<?php echo isPageActive('all_transactions'); ?>"><a class="py-2" href="<?php echo $baseUrl; ?>/views/transactions/index.php"><i class="bi bi-list-ul me-2 fs-9"></i>All Transactions</a></li>
                        <?php endif; ?>
                        
                        <li class="<?php echo isPageActive('cash_advance'); ?>"><a class="py-2" href="<?php echo $baseUrl; ?>/views/transactions/index.php?type=Cash Advance"><i class="bi bi-cash me-2 fs-9"></i>Cash Advance</a></li>
                        <li class="<?php echo isPageActive('reimbursement'); ?>"><a class="py-2" href="<?php echo $baseUrl; ?>/views/transactions/index.php?type=Reimbursement"><i class="bi bi-arrow-repeat me-2 fs-9"></i>Reimbursement</a></li>
                        <?php if (hasPermission('view_bactrack')): ?>
                            <li class="<?php echo isPageActive('bactrack'); ?>"><a class="py-2" href="<?php echo $baseUrl; ?>/views/transactions/index.php?type=BACtrack"><i class="bi bi-cloud-arrow-down me-2 fs-9"></i>BACtrack</a></li>
                        <?php endif; ?>
                        <li class="<?php echo isPageActive('payroll'); ?>"><a class="py-2" href="<?php echo $baseUrl; ?>/views/transactions/index.php?type=Payroll"><i class="bi bi-people-fill me-2 fs-9"></i>Payroll</a></li>
                        
                        <?php if (hasPermission('encode')): ?>
                            <li class="<?php echo isPageActive('submit_transaction'); ?>"><a class="py-2 text-warning fw-semibold" href="<?php echo $baseUrl; ?>/views/transactions/submit.php"><i class="bi bi-plus-circle-fill me-2 fs-9"></i>Submit Transaction</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <!-- Progress Tracker -->
        <?php if ($showTracker): ?>
            <li class="<?php echo isPageActive('tracker'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/tracker/index.php">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Progress Tracker</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Reports -->
        <?php if ($showReports): ?>
            <li class="<?php echo isPageActive('reports'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/reports/index.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Integration Monitor -->
        <?php if ($showIntegrationMonitor): ?>
            <li class="<?php echo isPageActive('integrations_page'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/integrations/integrations.php">
                    <i class="fas fa-sync-alt"></i>
                    <span>Integrations</span>
                </a>
            </li>
            <li class="<?php echo isPageActive('integrations'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/integrations/index.php">
                    <i class="fas fa-network-wired"></i>
                    <span>Integration Monitor</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- User Management -->
        <?php if ($showUserManagement): ?>
            <li class="<?php echo isPageActive('users'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/users/index.php">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- System Settings -->
        <?php if ($showSettings): ?>
            <li class="<?php echo isPageActive('settings'); ?>">
                <a href="<?php echo $baseUrl; ?>/views/settings/index.php">
                    <i class="fas fa-sliders-h"></i>
                    <span>System Settings</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Contact Us (all users) -->
        <li class="<?php echo isPageActive('contact'); ?>">
            <a href="<?php echo $baseUrl; ?>/views/contact.php">
                <i class="fas fa-envelope-open-text"></i>
                <span>Contact Us</span>
            </a>
        </li>

        <!-- My Profile (all users) -->
        <li class="<?php echo isPageActive('profile'); ?>">
            <a href="<?php echo $baseUrl; ?>/views/profile.php">
                <i class="fas fa-user-cog"></i>
                <span>My Profile</span>
            </a>
        </li>
    </ul>
    <?php
};
?>

<!-- 1. DESKTOP SIDEBAR -->
<aside class="sidebar d-none d-lg-flex">
    <div class="sidebar-header d-flex align-items-center gap-2">
        <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.15);" onerror="this.style.display='none'; document.getElementById('sidebar-logo-fallback').style.display='flex';">
        <div id="sidebar-logo-fallback" class="bg-white text-primary rounded-circle align-items-center justify-content-center" style="width: 40px; height: 40px; display: none; font-size: 1.2rem; font-weight: bold;">
            F
        </div>
        <div class="d-flex flex-column">
            <span class="logo-title text-white" style="font-size: 1.15rem; line-height: 1.2;">SDO FAST</span>
            <span class="logo-subtitle text-white-50" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Financial Accounting Services and Transactions</span>
        </div>
    </div>
    <div class="sidebar-menu-wrapper flex-grow-1 overflow-auto">
        <?php $renderSidebarMenu(false); ?>
    </div>
    <?php $renderUserProfileSection(); ?>
</aside>

<!-- 2. MOBILE SIDEBAR (OFFCANVAS) -->
<div class="offcanvas offcanvas-start bg-primary text-white d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: var(--sidebar-width);">
    <div class="offcanvas-header border-bottom border-white-10">
        <div class="d-flex align-items-center gap-2">
            <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.15);" onerror="this.style.display='none'; document.getElementById('mobile-logo-fallback').style.display='flex';">
            <div id="mobile-logo-fallback" class="bg-white text-primary rounded-circle align-items-center justify-content-center" style="width: 40px; height: 40px; display: none; font-size: 1.2rem; font-weight: bold;">
                F
            </div>
            <div class="d-flex flex-column">
                <span class="logo-title text-white" id="mobileSidebarLabel" style="font-size: 1.15rem; line-height: 1.2;">SDO FAST</span>
                <span class="logo-subtitle text-white-50" style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Financial Accounting Services and Transactions</span>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php $renderSidebarMenu(true); ?>
    </div>
    <?php $renderUserProfileSection(); ?>
</div>
