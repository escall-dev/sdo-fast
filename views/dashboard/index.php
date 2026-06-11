<?php
/**
 * Main Dashboard View for SDO FAST.
 */

$currentPage = 'dashboard';
$pageTitle = 'Dashboard';
$pageHeader = 'Dashboard';
$loadChartJS = true;

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Initialize default statistics
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
        $roleParams = [];

        // 1. Total Transactions count
        $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE 1=1" . $roleFilter);
        $stmt->execute($roleParams);
        $totalTransactions = (int)$stmt->fetchColumn();

        // 2. Pending Approvals count (all pending workflow stages)
        $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status IN ('Pending Accountant 1', 'Pending Support', 'Pending Budget Check', 'Pending Accountant 2', 'Pending Final Approval')" . $roleFilter);
        $stmt->execute($roleParams);
        $pendingApprovals = (int)$stmt->fetchColumn();

        // 3. Approved Transactions count
        $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = 'Approved'" . $roleFilter);
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
        $stmt = $fastPDO->prepare("SELECT COALESCE(SUM(net_amount), 0) FROM transactions WHERE current_status = 'Approved'" . $roleFilter);
        $stmt->execute($roleParams);
        $totalDisbursed = (float)$stmt->fetchColumn();

        // 7. Total Tax Deducted (tax from approved transactions)
        $stmt = $fastPDO->prepare("SELECT COALESCE(SUM(tax_amount), 0) FROM transactions WHERE current_status = 'Approved'" . $roleFilter);
        $stmt->execute($roleParams);
        $totalTaxDeducted = (float)$stmt->fetchColumn();

        // 8. Recent Transactions table (Latest 10)
        $recentQuery = "
            SELECT t.*, u.full_name as requestor_name 
            FROM transactions t 
            LEFT JOIN users u ON t.requestor_id = u.id
            WHERE 1=1 AND " . get_data_scope_filter($userRole, $userId, 't') . "
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
                    <h3 class="stat-number mb-0"><?php echo number_format($totalTransactions); ?></h3>
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
                    <h3 class="stat-number mb-0"><?php echo number_format($pendingApprovals); ?></h3>
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
                    <h3 class="stat-number mb-0"><?php echo number_format($approvedTransactions); ?></h3>
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
                    <h3 class="stat-number mb-0"><?php echo '₱' . number_format($totalDisbursed, 2); ?></h3>
                    <span class="stat-label">Total Disbursed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 5: Rejected -->
    <div class="col-6 col-md-6 col-xl">
        <div class="card stat-card mb-0">
            <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                <div class="stat-icon stat-icon-red">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h3 class="stat-number mb-0"><?php echo number_format($rejectedTransactions); ?></h3>
                    <span class="stat-label">Rejected</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Chart Container -->
    <div class="col-12 col-xl-8">
        <div class="card h-100 mb-xl-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">Monthly Financial Summary</h5>
                <span class="badge bg-light text-dark border py-2 px-3 fs-8">Current Year (<?php echo date('Y'); ?>)</span>
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
                        <span>New Disbursement Voucher</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php" class="btn btn-outline-primary w-100 py-3 justify-content-center align-items-center gap-2">
                    <i class="bi bi-search fs-5"></i>
                    <span>Track Status Timeline</span>
                </a>
                <?php if (in_array($userRole, ['Super Admin', 'Admin', 'Accounting Staff', 'Budget Officer', 'Approver']) || 
                          in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS'])): ?>
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
                <tbody>
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No transactions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $row): 
                            $statusBadgeClass = 'bg-secondary';
                            switch ($row['current_status']) {
                                case 'Pending Accountant 1':
                                case 'Pending Support':
                                case 'Pending Budget Check':
                                case 'Pending Accountant 2':
                                case 'Pending Final Approval':
                                    $statusBadgeClass = 'bg-warning text-dark';
                                    break;
                                case 'Approved':
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
                                        <?php echo htmlspecialchars($row['current_status']); ?>
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
     CHART SCRIPTING
     ========================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financialChart').getContext('2d');
    
    const labels = <?php echo json_encode($chartLabels); ?>;
    const amounts = <?php echo json_encode($chartAmounts); ?>;
    const taxes = <?php echo json_encode($chartTaxes); ?>;
    const nets = <?php echo json_encode($chartNets); ?>;

    new Chart(ctx, {
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
                            if (label) {
                                label += ': ';
                            }
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
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        },
                        font: {
                            family: 'Plus Jakarta Sans'
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
