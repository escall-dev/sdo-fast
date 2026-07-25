<?php
/**
 * Main Dashboard View for SDO FAST.
 * Features a dedicated Landing Page Hub and an Overview Financial Metrics View.
 */

$view = isset($_GET['view']) && $_GET['view'] === 'overview' ? 'overview' : 'landing';

$currentPage = 'dashboard';
$pageTitle = $view === 'overview' ? 'Dashboard Overview' : 'Welcome Dashboard';
$pageHeader = $view === 'overview' ? 'Dashboard Overview' : 'Welcome';
$loadChartJS = ($view === 'overview');

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';
$userName = $_SESSION['user_name'] ?? $_SESSION['user_username'] ?? 'User';

if ($view === 'landing'):
?>

<!-- =========================================================================
     LANDING PAGE VIEW — SDO FAST Welcome Hub
     ========================================================================= -->
<div class="container-fluid p-0">
    <!-- Hero Welcome Banner -->
    <div class="card dashboard-hero-card mb-4 p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-2 text-white">
                    Welcome, <?php echo htmlspecialchars($userName); ?>!
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 640px; font-size: 1.05rem;">
                    Welcome to the SDO FAST Financial Accounting Services and Transactions portal. Access real-time metric statistics, track submission timelines, and manage division transactions.
                </p>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="index.php?view=overview" class="btn btn-lg px-4 py-2-5 fw-bold rounded-pill shadow-sm d-inline-flex align-items-center gap-2" style="background-color: #f97316; border-color: #f97316; color: #ffffff !important;">
                        <i class="fas fa-chart-pie fs-5"></i> Overview
                    </a>
                    <?php if (hasPermission('encode')): ?>
                        <a href="<?php echo env('APP_URL'); ?>/views/transactions/submit.php" class="btn btn-outline-light btn-lg px-4 py-2-5 fw-semibold rounded-pill d-inline-flex align-items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Submit Transaction
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Feature Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Overview Card -->
        <div class="col-12 col-md-6 col-lg-4">
            <a href="index.php?view=overview" class="text-decoration-none">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-start gap-3 p-4">
                        <div class="stat-icon stat-icon-blue flex-shrink-0">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Financial Overview</h5>
                            <p class="text-muted fs-8 mb-2">Access full metrics, transaction volumes, disbursement totals, and interactive financial summary charts.</p>
                            <span class="text-primary fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                Go to Overview <i class="fas fa-arrow-right fs-9 ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Submit Transaction Card -->
        <?php if (hasPermission('encode')): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?php echo env('APP_URL'); ?>/views/transactions/submit.php" class="text-decoration-none">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-start gap-3 p-4">
                            <div class="stat-icon stat-icon-gold flex-shrink-0">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Submit Transaction</h5>
                                <p class="text-muted fs-8 mb-2">File new Cash Advance, Reimbursement, Payroll, or BACtrack requests quickly.</p>
                                <span class="text-warning-emphasis fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                    File Request <i class="fas fa-arrow-right fs-9 ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- Progress Tracker Card -->
        <div class="col-12 col-md-6 col-lg-4">
            <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php" class="text-decoration-none">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-start gap-3 p-4">
                        <div class="stat-icon stat-icon-indigo flex-shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Progress Tracker</h5>
                            <p class="text-muted fs-8 mb-2">Monitor the real-time status, timeline, and document trail of pending accounting files.</p>
                            <span class="text-info fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                Track Status <i class="fas fa-arrow-right fs-9 ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Reports Card -->
        <?php if (hasPermission('view') && $userRole !== 'Requestor'): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?php echo env('APP_URL'); ?>/views/reports/index.php" class="text-decoration-none">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-start gap-3 p-4">
                            <div class="stat-icon stat-icon-green flex-shrink-0">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Reports & Analytics</h5>
                                <p class="text-muted fs-8 mb-2">Generate summary reports, tax withholding ledgers, and financial breakdown statistics.</p>
                                <span class="text-success fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                    View Reports <i class="fas fa-arrow-right fs-9 ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- System Settings Card -->
        <?php if (hasPermission('configure_system')): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a href="<?php echo env('APP_URL'); ?>/views/settings/index.php" class="text-decoration-none">
                    <div class="card stat-card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-start gap-3 p-4">
                            <div class="stat-icon stat-icon-orange flex-shrink-0">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">System Settings</h5>
                                <p class="text-muted fs-8 mb-2">Configure categories, travel modes, tax rates, and documentary requirements.</p>
                                <span class="text-secondary-emphasis fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                    Open Settings <i class="fas fa-arrow-right fs-9 ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- Contact Support Card -->
        <div class="col-12 col-md-6 col-lg-4">
            <a href="<?php echo env('APP_URL'); ?>/views/contact.php" class="text-decoration-none">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-start gap-3 p-4">
                        <div class="stat-icon stat-icon-red flex-shrink-0">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Contact Support</h5>
                            <p class="text-muted fs-8 mb-2">Need assistance? Reach out to the Division Accounting & Finance office support staff.</p>
                            <span class="text-danger fw-bold fs-8 d-inline-flex align-items-center gap-1">
                                Contact Us <i class="fas fa-arrow-right fs-9 ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
else:
    // Initialize default statistics for Overview view
    $totalTransactions = 0;
    $pendingApprovals = 0;
    $approvedTransactions = 0;
    $rejectedTransactions = 0;
    $returnedTransactions = 0;
    $totalDisbursed = 0.00;
    $totalTaxDeducted = 0.00;
    $recentTransactions = [];
    $monthlyData = array_fill(1, 12, ['amount' => 0.0, 'tax' => 0.0, 'net' => 0.0]);

    if ($fastPDO !== null) {
        try {
            // Role-based query helper utilizing new data visibility scope
            $scopeFilter = get_data_scope_filter($userRole, $userId, null);
            $roleFilter = " AND " . $scopeFilter;
            if (!hasPermission('view_bactrack')) {
                $roleFilter .= " AND transaction_type != 'BACtrack'";
            }
            $roleParams = [];

            // 1. Total Transactions count
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE 1=1" . $roleFilter);
            $stmt->execute($roleParams);
            $totalTransactions = (int)$stmt->fetchColumn();

            // 2. Pending Approvals count (all pending workflow stages)
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status IN ('Pending Requestor', 'Pending Budget', 'Pending Accounting Support', 'Pending Signatory Approval', 'For Payment')" . $roleFilter);
            $stmt->execute($roleParams);
            $pendingApprovals = (int)$stmt->fetchColumn();

            // 3. Approved Transactions count
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = 'Released'" . $roleFilter);
            $stmt->execute($roleParams);
            $approvedTransactions = (int)$stmt->fetchColumn();

            // 4. Rejected Transactions count
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = 'Rejected'" . $roleFilter);
            $stmt->execute($roleParams);
            $rejectedTransactions = (int)$stmt->fetchColumn();

            // 5. Returned Transactions count
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = 'Returned'" . $roleFilter);
            $stmt->execute($roleParams);
            $returnedTransactions = (int)$stmt->fetchColumn();

            // 6. Total Disbursed (net amount of approved transactions)
            $stmt = $fastPDO->prepare("SELECT COALESCE(SUM(net_amount), 0) FROM transactions WHERE current_status = 'Released'" . $roleFilter);
            $stmt->execute($roleParams);
            $totalDisbursed = (float)$stmt->fetchColumn();

            // 7. Total Tax Deducted (tax from approved transactions)
            $stmt = $fastPDO->prepare("SELECT COALESCE(SUM(tax_amount), 0) FROM transactions WHERE current_status = 'Released'" . $roleFilter);
            $stmt->execute($roleParams);
            $totalTaxDeducted = (float)$stmt->fetchColumn();

            // 8. Recent Transactions table (Latest 10)
            $bactrackFilter = !hasPermission('view_bactrack') ? " AND t.transaction_type != 'BACtrack'" : "";
            $recentQuery = "
                SELECT t.*, u.full_name as requestor_name 
                FROM transactions t 
                LEFT JOIN users u ON t.requestor_id = u.id
                WHERE 1=1 AND " . get_data_scope_filter($userRole, $userId, 't') . $bactrackFilter . "
                ORDER BY t.created_at DESC LIMIT 10
            ";
            $stmt = $fastPDO->prepare($recentQuery);
            $stmt->execute($roleParams);
            $recentTransactions = $stmt->fetchAll();

            // 9. Monthly Financial Summary aggregation (Current Year)
            $monthlyQuery = "
                SELECT MONTH(created_at) as month_num, 
                       SUM(amount) as total_amount, 
                       SUM(tax_amount) as total_tax, 
                       SUM(net_amount) as total_net 
                FROM transactions 
                WHERE YEAR(created_at) = YEAR(CURDATE())" . $roleFilter . "
                GROUP BY MONTH(created_at) ORDER BY month_num
            ";
            $monthlyStmt = $fastPDO->prepare($monthlyQuery);
            $monthlyStmt->execute($roleParams);
            $monthlyRaw = $monthlyStmt->fetchAll();

            foreach ($monthlyRaw as $row) {
                $m = (int)$row['month_num'];
                if ($m >= 1 && $m <= 12) {
                    $monthlyData[$m] = [
                        'amount' => (float)$row['total_amount'],
                        'tax' => (float)$row['total_tax'],
                        'net' => (float)$row['total_net']
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Dashboard query failure: " . $e->getMessage());
        }
    }

    // Convert monthly metrics to JSON arrays for Chart.js
    $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $chartAmounts = [];
    $chartTaxes = [];
    $chartNets = [];
    for ($i = 1; $i <= 12; $i++) {
        $chartAmounts[] = $monthlyData[$i]['amount'];
        $chartTaxes[] = $monthlyData[$i]['tax'];
        $chartNets[] = $monthlyData[$i]['net'];
    }
?>

<!-- =========================================================================
     DASHBOARD STAT CARDS — FAST Financial Accounting Metrics
     ========================================================================= -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Transactions -->
    <div class="col-6 col-md-4 col-xl">
        <div class="card stat-card mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-blue">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0" id="statTotalTransactions"><?php echo number_format($totalTransactions); ?></h3>
                    <span class="stat-label">Total Transactions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Pending Review -->
    <div class="col-6 col-md-4 col-xl">
        <div class="card stat-card mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-orange">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0" id="statPendingApprovals"><?php echo number_format($pendingApprovals); ?></h3>
                    <span class="stat-label">Pending Review</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Approved -->
    <div class="col-6 col-md-4 col-xl">
        <div class="card stat-card mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-green">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0" id="statApprovedTransactions"><?php echo number_format($approvedTransactions); ?></h3>
                    <span class="stat-label">Approved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Disbursed -->
    <div class="col-6 col-md-6 col-xl">
        <div class="card stat-card stat-card-accent mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-gold">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0" id="statTotalDisbursed"><?php echo '₱' . number_format($totalDisbursed, 2); ?></h3>
                    <span class="stat-label">Total Disbursed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 5: Disapproved -->
    <div class="col-6 col-md-6 col-xl">
        <div class="card stat-card mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-red">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0" id="statRejectedTransactions"><?php echo number_format($rejectedTransactions); ?></h3>
                    <span class="stat-label">Disapproved</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     DASHBOARD FILTER CONTROLS
     ========================================================================= -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form id="dashboardFilterForm" onsubmit="event.preventDefault(); fetchDashboardData();">
            <div class="row g-3 align-items-end">
                <!-- Annual Filter -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterAnnual" class="form-label fs-8 fw-semibold text-muted">Annual</label>
                    <select id="filterAnnual" class="form-select">
                        <option value="">All Years</option>
                        <?php 
                        $currentYear = (int)date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 5; $y--): 
                        ?>
                            <option value="<?php echo $y; ?>" <?php echo $y === $currentYear ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Monthly Filter -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterMonthly" class="form-label fs-8 fw-semibold text-muted">Monthly</label>
                    <select id="filterMonthly" class="form-select">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <!-- Transaction Type Filter -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterType" class="form-label fs-8 fw-semibold text-muted">Transaction Type</label>
                    <select id="filterType" class="form-select">
                        <option value="">All Types</option>
                        <option value="Cash Advance">Cash Advance</option>
                        <option value="Reimbursement">Reimbursement</option>
                        <option value="Payroll">Payroll</option>
                        <?php if (hasPermission('view_bactrack')): ?>
                            <option value="BACtrack">BACtrack</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterStatus" class="form-label fs-8 fw-semibold text-muted">Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Pending Requestor">Pending Requestor</option>
                        <option value="Pending Budget">Pending Budget</option>
                        <option value="Pending Accounting Support">Pending Accounting Support</option>
                        <option value="Pending Signatory Approval">Pending Signatory Approval</option>
                        <option value="For Payment">For Payment</option>
                        <option value="Released">Released</option>
                        <option value="Rejected">Disapproved</option>
                        <option value="Returned">Returned</option>
                    </select>
                </div>

                <!-- Date Range Start -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterDateStart" class="form-label fs-8 fw-semibold text-muted">Date From</label>
                    <input type="date" id="filterDateStart" class="form-control">
                </div>

                <!-- Date Range End -->
                <div class="col-6 col-sm-4 col-md-2">
                    <label for="filterDateEnd" class="form-label fs-8 fw-semibold text-muted">Date To</label>
                    <input type="date" id="filterDateEnd" class="form-control">
                </div>

                <!-- Action Buttons -->
                <div class="col-12 col-sm-6 col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 justify-content-center">
                            <i class="bi bi-filter me-1"></i> Apply
                        </button>
                        <button type="button" class="btn btn-light border w-100 justify-content-center" onclick="resetDashboardFilters()">
                            <i class="bi bi-x-lg me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <!-- Chart Container -->
    <div class="col-12 col-xl-8">
        <div class="card h-100 mb-xl-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">Monthly Financial Summary</h5>
                <span class="badge bg-light text-dark border py-2 px-3 fs-8" id="chartYearBadge">Current Year (<?php echo date('Y'); ?>)</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 320px;">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Card -->
    <div class="col-12 col-xl-4">
        <div class="card h-100 mb-0">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Quick Shortcuts</h5>
            </div>
            <div class="card-body d-flex flex-column gap-3 justify-content-center">
                <?php if (in_array($userRole, ['Super Admin', 'Requestor'])): ?>
                    <a href="<?php echo env('APP_URL'); ?>/views/transactions/submit.php" class="btn btn-primary w-100 py-3 justify-content-center align-items-center gap-2">
                        <i class="bi bi-plus-circle fs-5"></i>
                        <span>Submit New Transaction</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php" class="btn btn-outline-primary w-100 py-3 justify-content-center align-items-center gap-2">
                    <i class="bi bi-search fs-5"></i>
                    <span>Track Status Timeline</span>
                </a>
                <?php if (in_array($userRole, ['Super Admin', 'Admin', 'Accounting Staff', 'Budget Officer', 'Approver', 'Cashier']) || 
                          in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS', 'Cashier'])): ?>
                    <a href="<?php echo env('APP_URL'); ?>/views/reports/index.php" class="btn btn-light border w-100 py-3 justify-content-center align-items-center gap-2">
                        <i class="bi bi-file-earmark-bar-graph fs-5"></i>
                        <span>Generate Financial Report</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card mb-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Recent Transactions</h5>
        <a href="<?php echo env('APP_URL'); ?>/views/transactions/index.php" class="btn btn-sm btn-outline-primary py-1 px-3">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive border-0">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Event Name</th>
                        <th>Type</th>
                        <th>Requestor</th>
                        <th>Amount</th>
                        <th>Tax Amount</th>
                        <th>Net Amount</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody id="recentTransactionsBody">
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No transactions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $row): 
                            // Workflow v3 display labels
                            $statusLabels = [
                                'Pending Budget' => 'Source of Funds Verification',
                                'Pending Requestor' => 'Source of Funds Verified',
                                'Pending Accounting Support' => 'Mandatory Documentary Requirements Submitted',
                                'Pending Signatory Approval' => 'Document for Approval and Signature',
                                'For Payment' => 'Release of Payment',
                                'Released' => 'Payment Released',
                                'Rejected' => 'Disapproved',
                                'Returned' => 'Returned to Requestor'
                            ];
                            $statusDisplay = $statusLabels[$row['current_status']] ?? $row['current_status'];
                            $statusBadgeClass = 'bg-secondary';
                            switch ($row['current_status']) {
                                case 'Pending Requestor':
                                case 'Pending Budget':
                                case 'Pending Accounting Support':
                                case 'Pending Signatory Approval':
                                    $statusBadgeClass = 'bg-warning text-dark';
                                    break;
                                case 'Released':
                                    $statusBadgeClass = 'bg-success';
                                    break;
                                case 'Rejected':
                                    $statusBadgeClass = 'bg-danger';
                                    break;
                                case 'Returned':
                                    $statusBadgeClass = 'bg-info text-dark';
                                    break;
                            }
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=<?php echo urlencode($row['tracking_number']); ?>" class="fw-bold text-decoration-none text-primary">
                                        <?php echo htmlspecialchars($row['tracking_number']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['requestor_name'] ?? 'System Sync'); ?></td>
                                <td class="fw-semibold">₱<?php echo number_format($row['amount'], 2); ?></td>
                                <td class="text-muted">₱<?php echo number_format($row['tax_amount'], 2); ?></td>
                                <td class="fw-bold text-primary">₱<?php echo number_format($row['net_amount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-status <?php echo $statusBadgeClass; ?>">
                                        <?php echo htmlspecialchars($statusDisplay); ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     DASHBOARD FILTER & CHART SCRIPTING
     ========================================================================= -->
<script>
let financialChart = null;

document.addEventListener('DOMContentLoaded', function() {
    initChart(<?php echo json_encode($chartLabels); ?>, <?php echo json_encode($chartAmounts); ?>, <?php echo json_encode($chartTaxes); ?>, <?php echo json_encode($chartNets); ?>);
});

function initChart(labels, amounts, taxes, nets) {
    if (financialChart) {
        financialChart.destroy();
    }
    const ctx = document.getElementById('financialChart').getContext('2d');
    financialChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Gross Amount',
                    data: amounts,
                    backgroundColor: '#1b4a9a',
                    borderRadius: 4,
                    maxBarThickness: 15
                },
                {
                    label: 'Tax Ded.',
                    data: taxes,
                    backgroundColor: '#d4af37',
                    borderRadius: 4,
                    maxBarThickness: 15
                },
                {
                    label: 'Net Amount',
                    data: nets,
                    backgroundColor: '#2563eb',
                    borderRadius: 4,
                    maxBarThickness: 15
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans' } }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value.toLocaleString(); },
                        font: { family: 'Plus Jakarta Sans' }
                    }
                }
            }
        }
    });
}

// ── Filter Handlers ─────────────────────────────────────────────────────────

function resetDashboardFilters() {
    document.getElementById('filterAnnual').value = '';
    document.getElementById('filterMonthly').value = '';
    document.getElementById('filterType').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDateStart').value = '';
    document.getElementById('filterDateEnd').value = '';
    fetchDashboardData();
}

async function fetchDashboardData() {
    const annual     = document.getElementById('filterAnnual').value;
    const monthly    = document.getElementById('filterMonthly').value;
    const type       = document.getElementById('filterType').value;
    const status     = document.getElementById('filterStatus').value;
    const dateStart  = document.getElementById('filterDateStart').value;
    const dateEnd    = document.getElementById('filterDateEnd').value;

    const params = new URLSearchParams();
    if (annual) params.set('annual', annual);
    if (monthly) params.set('monthly', monthly);
    if (type) params.set('type', type);
    if (status) params.set('status', status);
    if (dateStart) params.set('date_start', dateStart);
    if (dateEnd) params.set('date_end', dateEnd);

    // Show loading state on stat cards
    ['statTotalTransactions','statPendingApprovals','statApprovedTransactions','statTotalDisbursed','statRejectedTransactions'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = '<span class="spinner-border spinner-border-sm text-muted"></span>';
    });
    document.getElementById('recentTransactionsBody').innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading...</td></tr>';

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/dashboard/dashboard-data.php?' + params.toString());

    if (response && response.success) {
        updateStatCards(response.data);
        updateChartFromData(response.data);
        updateRecentTransactions(response.data.recent_transactions);
    } else {
        document.getElementById('recentTransactionsBody').innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i> Failed to load data.</td></tr>';
    }
}

function updateStatCards(data) {
    document.getElementById('statTotalTransactions').textContent = data.total_transactions.toLocaleString();
    document.getElementById('statPendingApprovals').textContent = data.pending_approvals.toLocaleString();
    document.getElementById('statApprovedTransactions').textContent = data.approved_transactions.toLocaleString();
    document.getElementById('statTotalDisbursed').textContent = '₱' + data.total_disbursed.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('statRejectedTransactions').textContent = data.rejected_transactions.toLocaleString();
    document.getElementById('chartYearBadge').textContent = 'Year ' + data.chart_year;
}

function updateChartFromData(data) {
    const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const amounts = [];
    const taxes = [];
    const nets = [];
    for (let i = 1; i <= 12; i++) {
        const m = data.monthly_data[i] || { amount: 0, tax: 0, net: 0 };
        amounts.push(m.amount);
        taxes.push(m.tax);
        nets.push(m.net);
    }
    initChart(labels, amounts, taxes, nets);
}

function updateRecentTransactions(transactions) {
    const tbody = document.getElementById('recentTransactionsBody');
    tbody.innerHTML = '';

    if (!transactions || transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">No transactions found.</td></tr>';
        return;
    }

    const statusLabels = {
        'Pending Budget': 'Source of Funds Verification',
        'Pending Requestor': 'Source of Funds Verified',
        'Pending Accounting Support': 'Mandatory Documentary Requirements Submitted',
        'Pending Signatory Approval': 'Document for Approval and Signature',
        'For Payment': 'Release of Payment',
        'Released': 'Payment Released',
        'Rejected': 'Disapproved',
        'Returned': 'Returned to Requestor'
    };

    transactions.forEach(row => {
        const statusDisplay = statusLabels[row.current_status] || row.current_status;
        let statusBadgeClass = 'bg-secondary';
        switch (row.current_status) {
            case 'Pending Requestor':
            case 'Pending Budget':
            case 'Pending Accounting Support':
            case 'Pending Signatory Approval':
                statusBadgeClass = 'bg-warning text-dark';
                break;
            case 'Released':
                statusBadgeClass = 'bg-success';
                break;
            case 'Rejected':
                statusBadgeClass = 'bg-danger';
                break;
            case 'Returned':
                statusBadgeClass = 'bg-info text-dark';
                break;
        }

        tbody.innerHTML += `
            <tr>
                <td>
                    <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=${encodeURIComponent(row.tracking_number)}" class="fw-bold text-decoration-none text-primary">
                        ${escapeHtml(row.tracking_number)}
                    </a>
                </td>
                <td>${escapeHtml(row.event_name)}</td>
                <td>${escapeHtml(row.transaction_type)}</td>
                <td>${escapeHtml(row.requestor_name || 'System Sync')}</td>
                <td class="fw-semibold">₱${parseFloat(row.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td class="text-muted">₱${parseFloat(row.tax_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td class="fw-bold text-primary">₱${parseFloat(row.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                <td><span class="badge badge-status ${statusBadgeClass}">${escapeHtml(statusDisplay)}</span></td>
                <td class="text-muted">${formatDate(row.created_at)}</td>
            </tr>
        `;
    });
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}
</script>

<?php 
endif; // End overview check
require_once __DIR__ . '/../../includes/footer.php'; 
?>

