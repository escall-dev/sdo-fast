<?php
/**
 * Reports Selector and Generator View for SDO FAST.
 */

$currentPage = 'reports';
$pageTitle = 'Reports Module';
$pageHeader = 'Reports Module';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userRole = $_SESSION['user_role'] ?? '';

// Double check permission (Requestors have no access)
if ($userRole === 'Requestor') {
    $_SESSION['flash_error'] = 'Access denied: Requestors do not have access to reports.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Fetch active requestor list for filters
$requestors = [];
if (in_array($userRole, ['Super Admin', 'Accounting Staff']) && $fastPDO !== null) {
    try {
        $stmt = $fastPDO->prepare("
            SELECT u.id, u.full_name 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            WHERE r.role_name IN ('Requestor', 'Super Admin')
            ORDER BY u.full_name ASC
        ");
        $stmt->execute();
        $requestors = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch requestors: " . $e->getMessage());
    }
}
?>

<div class="row g-4 mb-4">
    <!-- Filters Sidebar -->
    <div class="col-12 col-lg-4 col-xxl-3">
        <div class="card shadow-sm border-0 h-100 mb-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary-dark">Report Settings</h5>
            </div>
            <div class="card-body">
                <form id="reportSettingsForm" onsubmit="handleGenerateReport(event)">
                    <!-- Report Type Selection -->
                    <div class="mb-3">
                        <label for="reportType" class="form-label fs-8 fw-semibold text-muted">Report Type <span class="text-danger">*</span></label>
                        <select id="reportType" class="form-select" required onchange="handleReportTypeChange()">
                            <?php if (in_array($userRole, ['Super Admin', 'Accounting Staff', 'Budget Officer'])): ?>
                                <option value="transaction_summary" selected>Transaction Summary Report</option>
                                <option value="financial_summary">Financial Summary Report</option>
                            <?php endif; ?>
                            
                            <?php if (in_array($userRole, ['Super Admin', 'Accounting Staff', 'Approver'])): ?>
                                <option value="pending_approvals" <?php echo ($userRole === 'Approver') ? 'selected' : ''; ?>>Pending Approvals Report</option>
                            <?php endif; ?>
                            
                            <?php if (in_array($userRole, ['Super Admin', 'Accounting Staff'])): ?>
                                <option value="audit_trail">System Audit Trail Report</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Date Range Selection -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="dateStart" class="form-label fs-8 fw-semibold text-muted">Start Date</label>
                            <input type="date" id="dateStart" class="form-control" value="<?php echo date('Y-m-01'); ?>" required>
                        </div>
                        <div class="col-6">
                            <label for="dateEnd" class="form-label fs-8 fw-semibold text-muted">End Date</label>
                            <input type="date" id="dateEnd" class="form-control" value="<?php echo date('Y-m-t'); ?>" required>
                        </div>
                    </div>

                    <!-- Dynamic Report Filters Container -->
                    <div id="dynamicFiltersSection">
                        <!-- Transaction Type filter -->
                        <div class="mb-3 filter-group" id="filterTxTypeContainer">
                            <label for="filterTxType" class="form-label fs-8 fw-semibold text-muted">Transaction Type</label>
                            <select id="filterTxType" class="form-select">
                                <option value="">All Types</option>
                                <option value="Cash Advance">Cash Advance</option>
                                <option value="Reimbursement">Reimbursement</option>
                                <?php if (hasPermission('view_bactrack')): ?>
                                    <option value="BACtrack">BACtrack</option>
                                <?php endif; ?>
                                <option value="Payroll">Payroll</option>
                            </select>
                        </div>

                        <div class="mb-3 filter-group" id="filterStatusContainer">
                            <label for="filterStatus" class="form-label fs-8 fw-semibold text-muted">Workflow Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Pending Accountant 1">Pending Accountant 1</option>
                                <option value="Pending Support">Pending Support</option>
                                <option value="Pending Budget Check">Pending Budget Check</option>
                                <option value="Pending Accountant 2">Pending Accountant 2</option>
                                <option value="Pending Final Approval">Pending Final Approval</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Returned">Returned</option>
                            </select>
                        </div>

                        <!-- Requestor filter (Admin/Staff only) -->
                        <?php if (in_array($userRole, ['Super Admin', 'Accounting Staff'])): ?>
                            <div class="mb-3 filter-group" id="filterRequestorContainer">
                                <label for="filterRequestor" class="form-label fs-8 fw-semibold text-muted">Requestor / User</label>
                                <select id="filterRequestor" class="form-select">
                                    <option value="">All Users</option>
                                    <?php foreach ($requestors as $req): ?>
                                        <option value="<?php echo $req['id']; ?>"><?php echo htmlspecialchars($req['full_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Output generation triggers -->
                    <button type="submit" class="btn btn-primary w-100 py-3 justify-content-center mb-3">
                        <i class="bi bi-play-fill fs-5 me-1"></i>
                        <span>Generate on Screen</span>
                    </button>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-danger w-100 justify-content-center gap-1" onclick="exportReport('pdf')">
                                <i class="bi bi-file-pdf"></i>
                                <span>PDF Export</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-success w-100 justify-content-center gap-1" onclick="exportReport('csv')">
                                <i class="bi bi-file-spreadsheet"></i>
                                <span>CSV Export</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Output Preview Panel -->
    <div class="col-12 col-lg-8 col-xxl-9">
        <div class="card shadow-sm border-0 h-100 mb-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary-dark" id="reportPreviewTitle">Report Output Preview</h5>
                <span class="badge bg-light text-dark border" id="reportPreviewRange">-</span>
            </div>
            
            <div class="card-body p-0 d-flex flex-column h-100" style="min-height: 400px;">
                <div class="table-responsive border-0 flex-grow-1" id="reportTableContainer">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-bar-graph text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                        <h6 class="fw-bold">No Report Generated</h6>
                        <p class="fs-8 mx-auto" style="max-width: 320px;">Adjust the filter configurations on the left sidebar and click "Generate on Screen" to preview records.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT CONTROLS
     ========================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    handleReportTypeChange(); // Run once to toggle correct filter fields
});

function handleReportTypeChange() {
    const type = document.getElementById('reportType').value;
    const txTypeContainer = document.getElementById('filterTxTypeContainer');
    const statusContainer = document.getElementById('filterStatusContainer');
    const reqContainer = document.getElementById('filterRequestorContainer');

    // Reset filters state
    if (txTypeContainer) txTypeContainer.style.display = 'block';
    if (statusContainer) statusContainer.style.display = 'block';
    if (reqContainer) reqContainer.style.display = 'block';

    if (type === 'pending_approvals') {
        // Pending approvals has no status filter (always pending)
        if (statusContainer) statusContainer.style.display = 'none';
    } else if (type === 'audit_trail') {
        // Audit trail has no status or transaction type filter (activity_logs mapping only)
        if (txTypeContainer) txTypeContainer.style.display = 'none';
        if (statusContainer) statusContainer.style.display = 'none';
    } else if (type === 'financial_summary') {
        // Financial summary focuses on monthly aggregates, hide status/type filters for simplicity
        if (txTypeContainer) txTypeContainer.style.display = 'none';
        if (statusContainer) statusContainer.style.display = 'none';
    }
}

function getFilterParams(format = 'screen') {
    const type = document.getElementById('reportType').value;
    const start = document.getElementById('dateStart').value;
    const end = document.getElementById('dateEnd').value;
    
    const txTypeEl = document.getElementById('filterTxType');
    const statusEl = document.getElementById('filterStatus');
    const reqEl = document.getElementById('filterRequestor');

    const txType = (txTypeEl && txTypeEl.style.display !== 'none') ? txTypeEl.value : '';
    const status = (statusEl && statusEl.style.display !== 'none') ? statusEl.value : '';
    const reqId = (reqEl && reqEl.style.display !== 'none') ? reqEl.value : '';

    return new URLSearchParams({
        report_type: type,
        format: format,
        date_start: start,
        date_end: end,
        transaction_type: txType,
        status: status,
        requestor_id: reqId
    });
}

async function handleGenerateReport(e) {
    e.preventDefault();
    const params = getFilterParams('screen');
    const container = document.getElementById('reportTableContainer');
    
    container.innerHTML = '<div class="text-center py-5 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Compiling report records...</div>';

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/reports/generate-report.php?' + params.toString());
    
    if (response && response.success) {
        document.getElementById('reportPreviewTitle').innerText = response.report_title;
        document.getElementById('reportPreviewRange').innerText = response.date_range;
        renderReportTable(response.data, document.getElementById('reportType').value);
    } else {
        container.innerHTML = `<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle"></i> Failed to generate report preview: ${response.message || 'System error'}</div>`;
    }
}

function exportReport(format) {
    const params = getFilterParams(format);
    const url = '<?php echo env('APP_URL'); ?>/api/reports/generate-report.php?' + params.toString();
    window.open(url, '_blank');
}

function renderReportTable(data, type) {
    const container = document.getElementById('reportTableContainer');
    container.innerHTML = '';

    if (data.length === 0) {
        container.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-folder-x fs-2 d-block"></i> No records match filters within selected range.</div>';
        return;
    }

    let tableHTML = '';
    
    if (type === 'transaction_summary') {
        tableHTML = `
            <table class="table align-middle table-striped">
                <thead>
                    <tr>
                        <th>Transaction Type</th>
                        <th>Status</th>
                        <th class="text-center">Count</th>
                        <th class="text-end">Gross Amount</th>
                        <th class="text-end">Tax Deductions</th>
                        <th class="text-end">Net Payout</th>
                    </tr>
                </thead>
                <tbody>
        `;
        let totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
        data.forEach(row => {
            const count = parseInt(row.count);
            const gross = parseFloat(row.total_amount);
            const tax = parseFloat(row.total_tax);
            const net = parseFloat(row.total_net);
            
            totCount += count;
            $totGross += gross;
            $totTax += tax;
            $totNet += net;

            tableHTML += `
                <tr>
                    <td><strong>${row.transaction_type}</strong></td>
                    <td><span class="badge bg-light text-dark border">${row.current_status}</span></td>
                    <td class="text-center">${count}</td>
                    <td class="text-end">₱${gross.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end text-danger">₱${tax.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end text-primary-dark fw-bold">₱${net.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                </tr>
            `;
        });
        tableHTML += `
                <tr class="table-dark fw-bold">
                    <td colspan="2">GRAND TOTALS</td>
                    <td class="text-center">${totCount}</td>
                    <td class="text-end">₱${$totGross.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end">₱${$totTax.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end">₱${$totNet.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                </tr>
            </tbody>
        </table>`;

    } else if (type === 'financial_summary') {
        tableHTML = `
            <table class="table align-middle table-striped">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th class="text-center">Transaction Count</th>
                        <th class="text-end">Total Gross</th>
                        <th class="text-end">Total Tax</th>
                        <th class="text-end">Total Net</th>
                    </tr>
                </thead>
                <tbody>
        `;
        let totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
        data.forEach(row => {
            const count = parseInt(row.transaction_count);
            const gross = parseFloat(row.total_amount);
            const tax = parseFloat(row.total_tax);
            const net = parseFloat(row.total_net);
            
            totCount += count;
            $totGross += gross;
            $totTax += tax;
            $totNet += net;
            
            const monthName = new Date(row.year_num, row.month_num - 1, 1).toLocaleString('default', { month: 'long' });

            tableHTML += `
                <tr>
                    <td><strong>${monthName} ${row.year_num}</strong></td>
                    <td class="text-center">${count}</td>
                    <td class="text-end">₱${gross.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end text-danger">₱${tax.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end text-primary-dark fw-bold">₱${net.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                </tr>
            `;
        });
        tableHTML += `
                <tr class="table-dark fw-bold">
                    <td>GRAND TOTALS</td>
                    <td class="text-center">${totCount}</td>
                    <td class="text-end">₱${$totGross.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end">₱${$totTax.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td class="text-end">₱${$totNet.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                </tr>
            </tbody>
        </table>`;

    } else if (type === 'pending_approvals') {
        tableHTML = `
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Particulars</th>
                        <th>Type</th>
                        <th>Requestor</th>
                        <th class="text-end">Net Payout</th>
                        <th>Status</th>
                        <th class="text-center">Aging (Days)</th>
                    </tr>
                </thead>
                <tbody>
        `;
        let totNet = 0;
        data.forEach(row => {
            const net = parseFloat(row.net_amount);
            totNet += net;
            const age = parseInt(row.aging_days);
            const ageBadge = age > 14 ? 'bg-danger text-white' : (age > 7 ? 'bg-warning text-dark' : 'bg-light text-dark border');

            tableHTML += `
                <tr>
                    <td><a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=${encodeURIComponent(row.tracking_number)}" class="fw-bold text-decoration-none text-primary" target="_blank">${row.tracking_number}</a></td>
                    <td class="text-truncate" style="max-width: 150px;">${row.event_name}</td>
                    <td><small class="badge bg-light text-dark border">${row.transaction_type}</small></td>
                    <td>${row.requestor_name}</td>
                    <td class="text-end fw-bold text-primary-dark">₱${net.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td><span class="badge bg-warning text-dark border">${row.current_status}</span></td>
                    <td class="text-center"><span class="badge ${ageBadge} py-1 px-2">${age} days</span></td>
                </tr>
            `;
        });
        tableHTML += `
                <tr class="table-dark fw-bold">
                    <td colspan="4">TOTAL OUTSTANDING VALUE</td>
                    <td class="text-end">₱${totNet.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>`;

    } else if (type === 'audit_trail') {
        tableHTML = `
            <table class="table align-middle table-striped">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Activity Description</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Old Details</th>
                        <th>New Details</th>
                    </tr>
                </thead>
                <tbody>
        `;
        data.forEach(row => {
            const userStr = row.full_name ? `<strong>${row.full_name}</strong><br><small class="text-muted">${row.email}</small>` : '<span class="text-muted">System Event</span>';
            const oldVal = row.old_value ? `<code class="fs-9">${row.old_value}</code>` : '-';
            const newVal = row.new_value ? `<code class="fs-9">${row.new_value}</code>` : '-';
            
            tableHTML += `
                <tr>
                    <td class="text-muted" style="font-size: 0.8rem;">${row.created_at}</td>
                    <td><strong>${row.activity}</strong></td>
                    <td>${userStr}</td>
                    <td><code class="text-dark">${row.ip_address}</code></td>
                    <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${oldVal}</td>
                    <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${newVal}</td>
                </tr>
            `;
        });
        tableHTML += `</tbody></table>`;
    }

    container.innerHTML = tableHTML;
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
