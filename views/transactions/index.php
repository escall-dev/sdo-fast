<?php
/**
 * Transactions Management View for SDO FAST.
 * Renders lists for All, Cash Advance, Reimbursement, and Payroll.
 */

$userTypeFilter = trim($_GET['type'] ?? '');

$pageTitle = 'All Transactions';
$currentPage = 'all_transactions';

if ($userTypeFilter === 'Cash Advance') {
    $pageTitle = 'Cash Advance Transactions';
    $currentPage = 'cash_advance';
} elseif ($userTypeFilter === 'Reimbursement') {
    $pageTitle = 'Reimbursement Transactions';
    $currentPage = 'reimbursement';
} elseif ($userTypeFilter === 'Payroll') {
    $pageTitle = 'Payroll Transactions';
    $currentPage = 'payroll';
} elseif ($userTypeFilter === 'BACtrack') {
    $pageTitle = 'BACtrack Transactions';
    $currentPage = 'bactrack';
}

$pageHeader = $pageTitle;

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

if ($userTypeFilter === 'BACtrack' && !hasPermission('view_bactrack')) {
    $_SESSION['flash_error'] = 'Access denied: Your role does not permit access to BACtrack Transactions.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
$userPosition = $_SESSION['user_position'] ?? '';

// Fetch active tax configurations for workflow processor dropdown
$taxConfigurations = [];
if ($fastPDO !== null) {
    try {
        $taxConfigurations = $fastPDO->query("SELECT * FROM tax_configurations WHERE is_active = 1")->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch tax configs: " . $e->getMessage());
    }
}

// Fetch active requestor list for admin/staff filter dropdown
$requestors = [];
if (in_array($userRole, ['Super Admin', 'Accounting Staff']) && $fastPDO !== null) {
    try {
        $stmt = $fastPDO->prepare("
            SELECT u.id, u.full_name, u.email 
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

<!-- Filter Form Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form id="filterForm" onsubmit="event.preventDefault(); fetchTransactions(1);">
            <div class="row g-3 align-items-end">
                <!-- Search bar -->
                <div class="col-12 col-md-4">
                    <label for="filterSearch" class="form-label fs-8 fw-semibold text-muted">Search Keywords</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="filterSearch" class="form-control border-start-0" placeholder="Tracking No. or Event Name...">
                    </div>
                </div>

                <!-- Type Filter (Hidden if locked by URL parameters) -->
                <?php if (empty($userTypeFilter)): ?>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="filterType" class="form-label fs-8 fw-semibold text-muted">Transaction Type</label>
                        <select id="filterType" class="form-select">
                            <option value="">All Types</option>
                            <option value="Cash Advance">Cash Advance</option>
                            <option value="Reimbursement">Reimbursement</option>
                            <option value="Payroll">Payroll</option>
                            <option value="BACtrack">BACtrack</option>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" id="filterType" value="<?php echo htmlspecialchars($userTypeFilter); ?>">
                <?php endif; ?>

                <!-- Status Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filterStatus" class="form-label fs-8 fw-semibold text-muted">Current Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Pending Requestor">Pending Requestor</option>
                        <option value="Pending Budget">Pending Budget</option>
                        <option value="Pending Accounting Support">Pending Accounting Support</option>
                        <option value="Pending Signatory Approval">Pending Signatory Approval</option>
                        <option value="Pending Signatory Approval">Pending Signatory Approval</option>
                        <option value="Released">Released</option>
                        <option value="Rejected">Disapproved</option>
                        <option value="Returned">Returned</option>
                    </select>
                </div>

                <!-- Requestor Filter (Only visible to Admin/Staff) -->
                <?php if (in_array($userRole, ['Super Admin', 'Accounting Staff'])): ?>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="filterRequestor" class="form-label fs-8 fw-semibold text-muted">Submitted By</label>
                        <select id="filterRequestor" class="form-select">
                            <option value="">All Users</option>
                            <?php foreach ($requestors as $req): ?>
                                <option value="<?php echo $req['id']; ?>"><?php echo htmlspecialchars($req['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" id="filterRequestor" value="">
                <?php endif; ?>

                <!-- Date Range Start -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filterDateStart" class="form-label fs-8 fw-semibold text-muted">Submitted From</label>
                    <input type="date" id="filterDateStart" class="form-control">
                </div>

                <!-- Date Range End -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filterDateEnd" class="form-label fs-8 fw-semibold text-muted">Submitted To</label>
                    <input type="date" id="filterDateEnd" class="form-control">
                </div>

                <!-- Action buttons -->
                <div class="col-12 col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 justify-content-center">Apply</button>
                        <button type="button" class="btn btn-light border w-100 justify-content-center" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
        <h5 class="mb-0 fw-bold text-primary-dark">Transaction Records</h5>
        
        <div class="d-flex align-items-center gap-2">
            <span class="fs-8 text-muted text-nowrap">Show per page:</span>
            <select id="pageSizeSelect" class="form-select form-select-sm" style="width: 75px; min-height: 38px;" onchange="fetchTransactions(1)">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive border-0">
            <table class="table align-middle table-hover transactions-records-table">
                <colgroup>
                    <col style="width: 11%">
                    <col style="width: 17%">
                    <col style="width: 14%">
                    <col style="width: 16%">
                    <col style="width: 10%">
                    <col style="width: 13%">
                    <col style="width: 12%">
                    <col style="width: 7%">
                </colgroup>
                <thead>
                    <tr class="text-uppercase text-muted">
                        <th class="sortable" onclick="handleSort('tracking_number')">Tracking <span id="sort_icon_tracking_number"></span></th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Submitted By</th>
                        <th class="sortable" onclick="handleSort('amount')">Gross <span id="sort_icon_amount"></span></th>
                        <th class="sortable" onclick="handleSort('status')">Status <span id="sort_icon_status"></span></th>
                        <th class="sortable" onclick="handleSort('created_at')">Submitted <span id="sort_icon_created_at"></span></th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="transactionsTableBody">
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">Loading transactions...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Card Footer / Pagination -->
    <div class="card-footer bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="fs-8 text-muted" id="paginationStats">
                Showing 0 to 0 of 0 records
            </div>
            <nav aria-label="Transaction table navigation">
                <ul class="pagination pagination-sm mb-0" id="paginationList">
                    <!-- Pagination nodes loaded dynamically -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- =========================================================================
     WORKFLOW DECISION MODAL (For Super Admin and Approver roles)
     ========================================================================= -->
<?php if (in_array($userRole, ['Super Admin', 'Approver', 'Accounting Staff', 'Budget Officer', 'Cashier']) || in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS', 'Cashier'])): ?>
<div class="modal fade" id="workflowModal" tabindex="-1" aria-labelledby="workflowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-gradient bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="workflowModalLabel">
                    <i class="bi bi-gear-fill me-2 rotate-hover"></i>FAST Workflow Processor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Transaction Details Card -->
                <div class="mb-4">
                    <span class="fs-8 text-muted text-uppercase fw-bold d-block mb-2">Transaction Details</span>
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <small class="text-muted d-block fs-9">Tracking Number</small>
                                <strong id="modalTrackingNo" class="text-primary fs-7">-</strong>
                            </div>
                            <div class="col-12 col-sm-6">
                                <small class="text-muted d-block fs-9">Net Amount</small>
                                <strong id="modalNetAmount" class="text-primary-dark fs-7">-</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block fs-9">Transaction Type & Category</small>
                                <strong id="modalTypeCategory" class="text-dark fs-8">-</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block fs-9">Event Name</small>
                                <strong id="modalEventName" class="text-dark fs-8">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div id="modalLoadingSpinner" class="text-center my-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted mt-2 fs-8">Fetching details...</div>
                </div>

                <!-- Attachment approvals section -->
                <div id="stage2AttachmentsSection" class="mb-4" style="display:none;">
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                        <span class="fs-8">Review and approve each attachment individually. All attachments must be approved before the transaction automatically advances.</span>
                    </div>

                    <!-- Tax Classification Selection -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label for="modalTaxType" class="form-label fs-8 fw-semibold text-muted">Tax Classification <span class="text-danger">*</span></label>
                        <select id="modalTaxType" class="form-select" onchange="updateTaxClassification(currentModalTransactionData.id, this.value)">
                            <option value="" disabled selected>Select Tax Type</option>
                            <?php foreach ($taxConfigurations as $config): ?>
                                <option value="<?php echo htmlspecialchars($config['tax_type']); ?>">
                                    <?php echo htmlspecialchars($config['tax_type']) . " (" . number_format($config['tax_percentage']) . "%)"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted fs-9 d-block mt-1">Applying a tax classification dynamically computes the tax deduction and net payout for this transaction.</small>
                    </div>

                    <h6 class="fw-bold text-primary-dark fs-8 text-uppercase mb-2"><i class="bi bi-paperclip me-1 text-primary"></i>Attachments Checklist</h6>
                    <div id="attachmentsChecklistContainer" class="list-group list-group-flush border rounded-3 overflow-hidden mb-3">
                        <!-- Loaded dynamically -->
                    </div>
                </div>

                <!-- Budget check section -->
                <div id="stage3BudgetSection" class="mb-4" style="display:none;">
                    <h6 class="fw-bold text-primary-dark fs-8 text-uppercase mb-3"><i class="bi bi-bank2 me-1 text-primary"></i>Budget Allocation</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="modalBudgetFundSource" class="form-label fs-8 fw-semibold text-muted">Fund Source <span class="text-danger">*</span></label>
                            <input type="text" id="modalBudgetFundSource" class="form-control" placeholder="e.g. MOOE-2026, SEF-2026">
                        </div>
                        <div class="col-12">
                            <label for="modalBudgetFundTrackingNumber" class="form-label fs-8 fw-semibold text-muted">Fund Source Tracking Number (SARO No., etc.) (Optional)</label>
                            <input type="text" id="modalBudgetFundTrackingNumber" class="form-control" placeholder="e.g. SARO-2026-00123">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="modalBudgetFundAvailable" checked>
                                <label class="form-check-label fw-semibold text-dark fs-8" for="modalBudgetFundAvailable">Is Fund Available & Allocated?</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatory tasks section -->
                <div id="stage5SignatorySection" class="mb-4" style="display:none;">
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                        <span class="fs-8">Both payroll and DV/ORS preparation tasks run in parallel. Complete each below with optional document uploads.</span>
                    </div>
                    <h6 class="fw-bold text-primary-dark fs-8 text-uppercase mb-2"><i class="bi bi-check2-square me-1 text-primary"></i>Signatory / Processing Tasks</h6>
                    <div id="signatoryTasksContainer" class="d-flex flex-column gap-3 mb-3">
                        <!-- Loaded dynamically -->
                    </div>
                </div>

                <!-- Standard Workflow Action Form Form -->
                <form id="workflowForm" onsubmit="handleWorkflowSubmit(event)">
                    <input type="hidden" id="workflowTransactionId">
                    <div id="standardActionSection">
                        <div class="mb-3">
                            <label for="workflowAction" class="form-label fs-8 fw-semibold text-muted">Workflow Action</label>
                            <select id="workflowAction" class="form-select" required onchange="toggleWorkflowFormDetails()">
                                <!-- Generated dynamically -->
                            </select>
                        </div>

                        <!-- DV details input -->
                        <div id="dvDetailsSection" style="display: none;">
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label for="modalDvNumber" class="form-label fs-8 fw-semibold text-muted">DV Number</label>
                                    <input type="text" id="modalDvNumber" class="form-control" placeholder="e.g. DV-2026-0032">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="modalBirNumber" class="form-label fs-8 fw-semibold text-muted">BIR 2307 Number</label>
                                    <input type="text" id="modalBirNumber" class="form-control" placeholder="e.g. BIR-2307-8891">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="workflowRemarks" class="form-label fs-8 fw-semibold text-muted">Remarks / Comments <span class="text-danger">*</span></label>
                            <textarea id="workflowRemarks" class="form-control" rows="3" placeholder="Provide reason or audit notes for this action..." required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnSubmitWorkflow" class="btn btn-primary bg-gradient">Submit Action</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- =========================================================================
     JAVASCRIPT LOGIC
     ========================================================================= -->
<script>
let currentSortColumn = 'created_at';
let currentSortOrder = 'desc';
let globalPage = 1;
let currentModalTransactionData = null;

document.addEventListener('DOMContentLoaded', function() {
    fetchTransactions(1);
    updateSortHeaders();
});

function updateSortHeaders() {
    const columns = ['tracking_number', 'amount', 'status', 'created_at'];
    columns.forEach(col => {
        const span = document.getElementById('sort_icon_' + col);
        if (!span) return;
        if (currentSortColumn === col) {
            span.innerHTML = currentSortOrder === 'asc' 
                ? '<i class="bi bi-arrow-up-short"></i>' 
                : '<i class="bi bi-arrow-down-short"></i>';
        } else {
            span.innerHTML = '';
        }
    });
}

function handleSort(col) {
    if (currentSortColumn === col) {
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        currentSortColumn = col;
        currentSortOrder = 'desc';
    }
    updateSortHeaders();
    fetchTransactions(globalPage);
}

function resetFilters() {
    document.getElementById('filterSearch').value = '';
    const typeFilter = document.getElementById('filterType');
    // only reset type filter if it is not locked in URL
    if (typeFilter && typeFilter.tagName === 'SELECT') {
        typeFilter.value = '';
    }
    const reqFilter = document.getElementById('filterRequestor');
    if (reqFilter) reqFilter.value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterDateStart').value = '';
    document.getElementById('filterDateEnd').value = '';
    fetchTransactions(1);
}

async function fetchTransactions(page) {
    globalPage = page;
    const perPage = document.getElementById('pageSizeSelect').value;
    const search = document.getElementById('filterSearch').value;
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    const dateStart = document.getElementById('filterDateStart').value;
    const dateEnd = document.getElementById('filterDateEnd').value;
    const requestorEl = document.getElementById('filterRequestor');
    const requestorId = requestorEl ? requestorEl.value : '';

    const params = new URLSearchParams({
        page: page,
        per_page: perPage,
        search: search,
        type: type,
        status: status,
        date_start: dateStart,
        date_end: dateEnd,
        requestor_id: requestorId,
        sort_by: currentSortColumn,
        sort_order: currentSortOrder
    });

    const tbody = document.getElementById('transactionsTableBody');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading transactions...</td></tr>';

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/list-transactions.php?' + params.toString());
    
    if (response && response.success) {
        renderTable(response.data.transactions);
        renderPagination(response.data.total_count, page, perPage);
    } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i> Failed to load transactions.</td></tr>';
    }
}

function renderTable(transactions) {
    const tbody = document.getElementById('transactionsTableBody');
    tbody.innerHTML = '';

    if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No matching transaction records found.</td></tr>';
        return;
    }

    transactions.forEach(row => {
        // Map DB status value to display label (Workflow v3)
        const statusLabels = {
            'Pending Budget': 'Source of Funds Verification',
            'Pending Requestor': 'Source of Funds Verified',
            'Pending Accounting Support': 'Mandatory Documentary Requirements Submitted',
            'Pending Signatory Approval': 'Document for Approval and Signature',
            'For Payment': 'Release of Payment',
            'Awaiting Payment': 'Release of Payment',
            'Released': 'Payment Released',
            'Rejected': 'Disapproved',
            'Returned': 'Returned to Requestor'
        };
        const statusDisplay = statusLabels[row.current_status] || row.current_status;
        
        let statusBadgeClass = 'bg-secondary';
        switch (row.current_status) {
            case 'Pending Requestor':
                statusBadgeClass = 'bg-secondary text-white';
                break;
            case 'Pending Budget':
                statusBadgeClass = 'bg-warning text-dark';
                break;
            case 'Pending Accounting Support':
                statusBadgeClass = 'bg-info text-dark';
                break;
            case 'Pending Signatory Approval':
                statusBadgeClass = 'bg-danger text-white';
                break;
            case 'Released':
                statusBadgeClass = 'bg-success';
                break;
            case 'Rejected':
                statusBadgeClass = 'bg-danger';
                break;
            case 'Returned':
                statusBadgeClass = 'bg-dark';
                break;
        }

        const dateFormatted = new Date(row.created_at).toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });

        const gross = parseFloat(row.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const tax = parseFloat(row.tax_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const net = parseFloat(row.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });

        // Action Buttons Setup
        const userRole = '<?php echo $userRole; ?>';
        const userPosition = '<?php echo $userPosition; ?>';
        const userId = <?php echo (int)$_SESSION['user_id']; ?>;
        let actionBtn = '';
        let docUploadBtn = '';
        
        // Show Workflow action button if role or position is authorized
        const showWorkflowAction = 
            (userRole === 'Super Admin') ||
            ((userRole === 'Accounting Staff' || userPosition === 'Accounting Support') && ['Pending Requestor', 'Pending Accounting Support', 'Pending Signatory Approval'].includes(row.current_status)) ||
            ((userRole === 'Budget Officer' || userPosition === 'Budget Officer') && row.current_status === 'Pending Budget') ||
            (userPosition === 'Accountant' && ['Pending Accounting Support', 'Pending Signatory Approval'].includes(row.current_status)) ||
            ((userPosition === 'ASDS' || userPosition === 'SDS') && row.current_status === 'Pending Signatory Approval') ||
            ((userRole === 'Cashier' || userPosition.toLowerCase().includes('cashier')) && ['Pending Signatory Approval', 'For Payment', 'Awaiting Payment'].includes(row.current_status));

        if (showWorkflowAction) {
            actionBtn = `
                <button class="btn btn-sm btn-primary" onclick="openWorkflowModal(${JSON.stringify(row).replace(/"/g, '&quot;')})" title="Workflow Action">
                    <i class="bi bi-lightning-fill"></i>
                </button>
            `;
        }

        // Show "Submit Mandatory Documentary Requirements" button for Requestor when status is Pending Requestor
        if ((userRole === 'Super Admin' || userRole === 'Requestor') && 
            row.current_status === 'Pending Requestor' && 
            row.requestor_id == userId) {
            docUploadBtn = `
                <a href="<?php echo env('APP_URL'); ?>/views/transactions/resubmit-documents.php?id=${row.id}" class="btn btn-sm btn-success" title="Submit Mandatory Documentary Requirements">
                    <i class="bi bi-upload me-1"></i>Submit Docs
                </a>
            `;
        }

        const rowHTML = `
            <tr>
                <td>
                    <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=${encodeURIComponent(row.tracking_number)}" class="fw-bold text-decoration-none text-primary" title="${row.tracking_number}">
                        ${row.tracking_number}
                    </a>
                    ${row.fund_source_tracking_number ? `<div class="text-muted fs-9 mt-1" title="Fund Source Tracking Number">${row.fund_source_tracking_number}</div>` : ''}
                </td>
                <td class="transactions-col-event" title="${row.event_name}">${row.event_name}</td>
                <td title="${row.transaction_type}${row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : ''}${row.reimbursement_category ? ' (' + row.reimbursement_category + ')' : ''}"><span class="badge bg-light text-dark border txn-type-badge">${row.transaction_type}${row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : ''}${row.reimbursement_category ? ' (' + row.reimbursement_category + ')' : ''}</span></td>
                <td class="transactions-col-requestor" title="${row.requestor_name} — ${row.requestor_email}">
                    <span class="txn-requestor-name fw-semibold">${row.requestor_name}</span>
                    <span class="txn-requestor-email text-muted">${row.requestor_email}</span>
                </td>
                <td class="fw-semibold" title="₱${gross}">₱${gross}</td>
                <td title="${statusDisplay}">
                    <span class="badge badge-status ${statusBadgeClass}">${statusDisplay}</span>
                </td>
                <td class="text-muted" title="${dateFormatted}">${dateFormatted}</td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1 txn-actions">
                        ${docUploadBtn}
                        ${actionBtn}
                        <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=${encodeURIComponent(row.tracking_number)}" class="btn btn-sm btn-light border" title="Track Timeline">
                            <i class="bi bi-geo-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', rowHTML);
    });
}

function renderPagination(totalCount, currentPage, perPage) {
    const list = document.getElementById('paginationList');
    list.innerHTML = '';

    const stats = document.getElementById('paginationStats');
    
    if (totalCount === 0) {
        stats.innerHTML = 'Showing 0 to 0 of 0 records';
        return;
    }

    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, totalCount);
    stats.innerHTML = `Showing ${start} to ${end} of ${totalCount} records`;

    const totalPages = Math.ceil(totalCount / perPage);

    // Prev Page
    const prevClass = currentPage === 1 ? 'disabled' : '';
    list.insertAdjacentHTML('beforeend', `
        <li class="page-item ${prevClass}">
            <a class="page-item" class="page-link" href="javascript:void(0)" onclick="fetchTransactions(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a>
        </li>
    `);

    // Dynamic Pages
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            const activeClass = currentPage === i ? 'active' : '';
            list.insertAdjacentHTML('beforeend', `
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="javascript:void(0)" onclick="fetchTransactions(${i})">${i}</a>
                </li>
            `);
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            list.insertAdjacentHTML('beforeend', `<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
    }

    // Next Page
    const nextClass = currentPage === totalPages ? 'disabled' : '';
    list.insertAdjacentHTML('beforeend', `
        <li class="page-item ${nextClass}">
            <a class="page-item" class="page-link" href="javascript:void(0)" onclick="fetchTransactions(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a>
        </li>
    `);
    
    // Quick Pagination style override compatibility
    const pageItems = list.querySelectorAll('.page-item');
    pageItems.forEach(item => {
        item.style.cursor = 'pointer';
        const link = item.querySelector('.page-link');
        if (link) {
            link.style.display = 'inline-flex';
            link.style.alignItems = 'center';
            link.style.justifyContent = 'center';
            link.style.width = '34px';
            link.style.height = '34px';
            link.style.borderRadius = '6px';
            link.style.margin = '0 2px';
            link.style.color = 'var(--color-primary)';
            link.style.border = '1px solid #e2e8f0';
        }
        if (item.classList.contains('active')) {
            const activeLink = item.querySelector('.page-link');
            if (activeLink) {
                activeLink.style.backgroundColor = 'var(--color-primary)';
                activeLink.style.color = '#ffffff';
                activeLink.style.borderColor = 'var(--color-primary)';
            }
        }
    });
}

async function openWorkflowModal(row) {
    currentModalTransactionData = row;
    
    document.getElementById('workflowTransactionId').value = row.id;
    document.getElementById('modalTrackingNo').innerText = row.tracking_number;
    document.getElementById('modalNetAmount').innerText = '₱' + parseFloat(row.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('modalEventName').innerText = row.event_name;
    document.getElementById('modalTypeCategory').innerText = row.transaction_type + (row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : '') + (row.reimbursement_category ? ' (' + row.reimbursement_category + ')' : '');
    document.getElementById('workflowRemarks').value = '';

    // Hide stage-specific sections initially
    document.getElementById('stage2AttachmentsSection').style.display = 'none';
    document.getElementById('stage3BudgetSection').style.display = 'none';
    document.getElementById('stage5SignatorySection').style.display = 'none';
    document.getElementById('standardActionSection').style.display = 'none';
    document.getElementById('modalLoadingSpinner').style.display = 'block';
    document.getElementById('btnSubmitWorkflow').style.display = 'none';

    // Open Modal first
    const modalEl = document.getElementById('workflowModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    if (!modalEl.classList.contains('show')) {
        modal.show();
    }

    // Fetch live details (attachment approvals, budget, signatory tasks)
    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/get-transaction-details.php?transaction_id=' + row.id);
    document.getElementById('modalLoadingSpinner').style.display = 'none';

    if (!response || !response.success) {
        API.showToast('Failed to load transaction details.', 'danger');
        modal.hide();
        return;
    }

    const details = response.data;
    const tx = details.transaction;
    currentModalTransactionData = tx; // use updated info

    const taxTypeSelect = document.getElementById('modalTaxType');
    if (taxTypeSelect) {
        taxTypeSelect.value = tx.tax_type || '';
        taxTypeSelect.disabled = true; // Disabled by default, enabled only during Document Inspection review
    }

    const userRole = '<?php echo $userRole; ?>';
    const userPosition = '<?php echo $userPosition; ?>';
    const actionSelect = document.getElementById('workflowAction');
    actionSelect.innerHTML = '';
    
    // Workflow v3: Pending Requestor — Requestor must upload docs via resubmit page
    if (tx.current_status === 'Pending Requestor' && (userRole === 'Super Admin' || userRole === 'Accounting Staff' || userPosition === 'Accounting Support')) {
        // Accounting Support can still return/reject at this stage
        actionSelect.innerHTML = `
            <option value="">-- Select Action --</option>
            <option value="Returned">Return to Requestor</option>
            <option value="Rejected">Disapprove Transaction</option>
        `;
        document.getElementById('standardActionSection').style.display = 'block';
        document.getElementById('btnSubmitWorkflow').style.display = 'block';
    }
    // Workflow v3: Pending Budget — Budget Officer verifies source of funds
    else if (tx.current_status === 'Pending Budget' && (userRole === 'Super Admin' || userRole === 'Budget Officer' || userPosition === 'Budget Officer')) {
        document.getElementById('stage3BudgetSection').style.display = 'block';
        document.getElementById('modalBudgetFundSource').value = details.budget_check ? details.budget_check.fund_source : '';
        document.getElementById('modalBudgetFundTrackingNumber').value = details.budget_check ? (details.budget_check.fund_source_tracking_number || '') : '';
        document.getElementById('modalBudgetFundAvailable').checked = details.budget_check ? (details.budget_check.fund_available == 1) : true;
        
        actionSelect.innerHTML = `
            <option value="approve_budget">Verify Source of Funds (Route to Requestor for Docs)</option>
            <option value="Returned">Return to Requestor</option>
            <option value="Rejected">Disapprove Transaction</option>
        `;
        document.getElementById('standardActionSection').style.display = 'block';
        document.getElementById('btnSubmitWorkflow').style.display = 'block';
    }
    // Workflow v3: Pending Accounting Support — Accounting Support inspects documents
    else if (tx.current_status === 'Pending Accounting Support' && (userRole === 'Super Admin' || userRole === 'Accounting Staff' || userPosition === 'Accounting Support' || userPosition === 'Accountant')) {
        // Show attachment approvals section for per-file review
        document.getElementById('stage2AttachmentsSection').style.display = 'block';
        renderAttachmentChecklist(details.attachments, tx.id);
        if (taxTypeSelect) {
            taxTypeSelect.disabled = false;
        }
        
        actionSelect.innerHTML = `
            <option value="">-- Select Action (Forward / Return / Disapprove) --</option>
            <option value="Pending Signatory Approval">Route to Signatories (Document Inspection Complete)</option>
            <option value="Returned">Return to Requestor</option>
            <option value="Rejected">Disapprove Transaction</option>
        `;
        document.getElementById('standardActionSection').style.display = 'block';
        document.getElementById('btnSubmitWorkflow').style.display = 'block';
    }
    // Workflow v3: Pending Signatory Approval — Approve/Disapprove by ASDS/SDS
    else if (tx.current_status === 'Pending Signatory Approval') {
        if (userRole === 'Super Admin' || userPosition === 'ASDS' || userPosition === 'SDS') {
            actionSelect.innerHTML = `
                <option value="">-- Select Action --</option>
                <option value="For Payment">Approve — Route to Cashier</option>
                <option value="Returned">Disapprove — Return to Requestor</option>
            `;
            document.getElementById('standardActionSection').style.display = 'block';
            document.getElementById('btnSubmitWorkflow').style.display = 'block';
        }
    }
    // Workflow v3: For Payment / Awaiting Payment — Cashier releases payment
    else if ((tx.current_status === 'For Payment' || tx.current_status === 'Awaiting Payment') && (userRole === 'Super Admin' || userRole === 'Cashier' || userPosition.toLowerCase().includes('cashier'))) {
        actionSelect.innerHTML = `
            <option value="">-- Select Action --</option>
            <option value="Released">Release Payment</option>
            <option value="Returned">Return to Requestor</option>
            <option value="Rejected">Disapprove Transaction</option>
        `;
        document.getElementById('standardActionSection').style.display = 'block';
        document.getElementById('btnSubmitWorkflow').style.display = 'block';
    }

    toggleWorkflowFormDetails();
}

function renderAttachmentChecklist(attachments, transactionId) {
    const container = document.getElementById('attachmentsChecklistContainer');
    container.innerHTML = '';

    if (attachments.length === 0) {
        container.innerHTML = '<div class="list-group-item text-center text-muted fs-8 py-3">No attachments uploaded.</div>';
        return;
    }

    attachments.forEach(att => {
        let badgeHTML = '';
        if (att.status === 'approved') {
            badgeHTML = '<span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Approved</span>';
        } else if (att.status === 'rejected') {
            badgeHTML = `<span class="badge bg-danger rounded-pill px-2 py-1" title="${att.remarks || ''}"><i class="bi bi-x-circle me-1"></i>Rejected</span>`;
        } else {
            badgeHTML = '<span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-hourglass me-1"></i>Pending</span>';
        }

        const filename = att.file_path.split('/').pop();
        const docUrl = '<?php echo env('APP_URL'); ?>/' + att.file_path;

        let actionButtons = '';
        if (att.status === 'pending') {
            actionButtons = `
                <div class="d-flex gap-1 mt-2 align-items-center">
                    <input type="text" id="remarks_att_${att.id}" class="form-control form-control-sm fs-9" style="max-width: 200px;" placeholder="Optional remarks...">
                    <button class="btn btn-sm btn-success py-1 px-2" onclick="processAttachmentAction(${transactionId}, ${att.id}, 'approve')" title="Approve Attachment">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-sm btn-danger py-1 px-2" onclick="processAttachmentAction(${transactionId}, ${att.id}, 'reject')" title="Reject Attachment">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
        } else {
            if (att.remarks) {
                actionButtons = `<div class="text-muted fs-9 mt-1">Remarks: <em>${att.remarks}</em></div>`;
            }
        }

        const itemHTML = `
            <div class="list-group-item p-3 border-bottom d-flex flex-column gap-1">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <span class="text-muted fs-9 text-uppercase fw-semibold">Mandatory Documentary Requirement</span>
                        <span class="fw-bold text-dark fs-8 d-block">${att.file_label || 'Unlabeled Document'}</span>
                        <span class="text-muted fs-9 d-block mt-1"><i class="bi bi-file-earmark me-1"></i>Uploaded file:</span>
                        <a href="${docUrl}" target="_blank" class="fs-9 text-primary text-decoration-none"><i class="bi bi-download me-1"></i>${filename}</a>
                    </div>
                    <div>${badgeHTML}</div>
                </div>
                ${actionButtons}
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHTML);
    });
}

async function updateTaxClassification(transactionId, taxType) {
    if (!transactionId || !taxType) return;

    const payload = {
        transaction_id: transactionId,
        tax_type: taxType
    };

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/update-tax-classification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        showLoader: true
    });

    if (response && response.success) {
        API.showToast(response.message, 'success');
        
        // Update modal values in real-time
        if (response.data) {
            document.getElementById('modalNetAmount').innerText = '₱' + parseFloat(response.data.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
            currentModalTransactionData.net_amount = response.data.net_amount;
            currentModalTransactionData.tax_amount = response.data.tax_amount;
            currentModalTransactionData.tax_type = taxType;
        }

        if (response.auto_advanced) {
            const modalEl = document.getElementById('workflowModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
            fetchTransactions(globalPage);
        }
    } else {
        API.showToast(response ? response.message : 'Failed to update tax classification.', 'danger');
        // Reset the select element to current data value
        document.getElementById('modalTaxType').value = currentModalTransactionData.tax_type || '';
    }
}

async function processAttachmentAction(transactionId, approvalId, action) {
    const remarks = document.getElementById('remarks_att_' + approvalId).value.trim();
    
    const payload = {
        transaction_id: transactionId,
        approval_id: approvalId,
        action: action,
        remarks: remarks
    };

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/approve-attachment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        showLoader: true
    });

    if (response && response.success) {
        API.showToast(response.message, 'success');
        
        // Refresh modal content
        const modalEl = document.getElementById('workflowModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        
        if (response.auto_advanced) {
            if (modalInstance) modalInstance.hide();
            fetchTransactions(globalPage);
        } else {
            openWorkflowModal(currentModalTransactionData);
        }
    } else {
        API.showToast(response.message || 'Action failed.', 'danger');
    }
}

function renderSignatoryTasks(tasks, transactionId) {
    const container = document.getElementById('signatoryTasksContainer');
    container.innerHTML = '';

    if (tasks.length === 0) {
        container.innerHTML = '<div class="text-center text-muted fs-8 py-3">No signatory tasks seeded.</div>';
        return;
    }

    tasks.forEach(t => {
        const isDone = t.status === 'completed';
        const taskLabel = (t.task_type === 'payroll_prep') ? 'Payroll Prep & Signatures' : 'DV/ORS Prep & Signatures';
        
        let badgeClass = 'bg-warning text-dark';
        let badgeIcon = 'bi-hourglass';
        if (isDone) {
            badgeClass = 'bg-success text-white';
            badgeIcon = 'bi-check-circle';
        }

        let actionForm = '';
        if (!isDone) {
            actionForm = `
                <div class="mt-2 border-top pt-2">
                    <form id="sig_form_${t.id}" onsubmit="submitSignatoryTask(event, ${transactionId}, '${t.task_type}', ${t.id})">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <input type="file" id="sig_doc_${t.id}" class="form-control form-control-sm fs-9" required>
                                <span class="fs-9 text-muted d-block">Upload signed document</span>
                            </div>
                            <div class="col-12 col-md-5">
                                <input type="text" id="sig_remarks_${t.id}" class="form-control form-control-sm fs-9" placeholder="Optional comments...">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100 fs-9 py-1 px-2">Complete</button>
                            </div>
                        </div>
                    </form>
                </div>
            `;
        } else {
            actionForm = `
                <div class="mt-1 fs-9 text-muted">
                    Completed by: <strong>${t.completed_by_name || 'System'}</strong> on ${new Date(t.completed_at).toLocaleString()}<br>
                    ${t.document_path ? `<a href="<?php echo env('APP_URL'); ?>/${t.document_path}" target="_blank" class="text-decoration-none text-primary"><i class="bi bi-file-earmark-check me-1"></i>View Uploaded Document</a>` : ''}
                    ${t.remarks ? `<div class="mt-1">Comments: <em>${t.remarks}</em></div>` : ''}
                </div>
            `;
        }

        const taskHTML = `
            <div class="p-3 border rounded-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="fs-8 text-primary-dark">${taskLabel}</strong>
                    <span class="badge ${badgeClass} fs-9 rounded-pill px-2 py-1"><i class="bi ${badgeIcon} me-1"></i>${t.status}</span>
                </div>
                ${actionForm}
            </div>
        `;
        container.insertAdjacentHTML('beforeend', taskHTML);
    });
}

async function submitSignatoryTask(e, transactionId, taskType, sigId) {
    e.preventDefault();
    const fileInput = document.getElementById('sig_doc_' + sigId);
    const remarksInput = document.getElementById('sig_remarks_' + sigId);
    
    const formData = new FormData();
    formData.append('transaction_id', transactionId);
    formData.append('task_type', taskType);
    formData.append('remarks', remarksInput.value.trim());
    
    if (fileInput.files.length > 0) {
        formData.append('document', fileInput.files[0]);
    }

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/complete-signatory-task.php', {
        method: 'POST',
        body: formData,
        showLoader: true
    });

    if (response && response.success) {
        API.showToast(response.message, 'success');
        if (response.auto_advanced) {
            const modalEl = document.getElementById('workflowModal');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
            fetchTransactions(globalPage);
        } else {
            openWorkflowModal(currentModalTransactionData);
        }
    } else {
        API.showToast(response.message || 'Failed to complete task.', 'danger');
    }
}

function toggleWorkflowFormDetails() {
    const action = document.getElementById('workflowAction').value;
    const dvSection = document.getElementById('dvDetailsSection');
    
    const showDV = (action === 'Pending Signatory Approval' || action === 'Released');
    
    if (showDV && currentModalTransactionData) {
        dvSection.style.display = 'block';
        document.getElementById('modalDvNumber').value = currentModalTransactionData.dv_number || '';
        document.getElementById('modalBirNumber').value = currentModalTransactionData.bir_2307_number || '';
    } else {
        dvSection.style.display = 'none';
    }
}

async function handleWorkflowSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('workflowTransactionId').value;
    const action = document.getElementById('workflowAction').value;
    const remarks = document.getElementById('workflowRemarks').value.trim();
    const dvNumber = document.getElementById('modalDvNumber').value.trim();
    const birNumber = document.getElementById('modalBirNumber').value.trim();

    if (action === '' && currentModalTransactionData.current_status !== 'Pending Requestor' && currentModalTransactionData.current_status !== 'Pending Signatory Approval') {
        API.showToast('Please select a workflow action.', 'warning');
        return;
    }

    // Special Route for Budget check
    if (action === 'approve_budget') {
        const fundSource = document.getElementById('modalBudgetFundSource').value.trim();
        const fundSourceTrackingNumber = document.getElementById('modalBudgetFundTrackingNumber').value.trim();
        const fundAvailable = document.getElementById('modalBudgetFundAvailable').checked ? 1 : 0;
        
        if (fundSource === '') {
            API.showToast('Fund source is required for budget approval.', 'warning');
            return;
        }

        const payload = {
            transaction_id: id,
            fund_source: fundSource,
            fund_source_tracking_number: fundSourceTrackingNumber,
            fund_available: fundAvailable,
            remarks: remarks,
            action: 'approve'
        };

        const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/budget-check.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            showLoader: true
        });

        if (response && response.success) {
            API.showToast(response.message, 'success');
            const modalEl = document.getElementById('workflowModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            fetchTransactions(globalPage);
        } else {
            API.showToast(response.message || 'Failed to complete budget check.', 'danger');
        }
        return;
    }

    // Handle return/rejection at Budget Check
    if (currentModalTransactionData.current_status === 'Pending Budget' && ['Returned', 'Rejected'].includes(action)) {
        const fundSource = document.getElementById('modalBudgetFundSource').value.trim() || 'N/A';
        const fundSourceTrackingNumber = document.getElementById('modalBudgetFundTrackingNumber').value.trim();
        const fundAvailable = document.getElementById('modalBudgetFundAvailable').checked ? 1 : 0;

        const payload = {
            transaction_id: id,
            fund_source: fundSource,
            fund_source_tracking_number: fundSourceTrackingNumber,
            fund_available: fundAvailable,
            remarks: remarks,
            action: action === 'Rejected' ? 'reject' : 'return'
        };

        const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/budget-check.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            showLoader: true
        });

        if (response && response.success) {
            API.showToast(response.message, 'success');
            const modalEl = document.getElementById('workflowModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            fetchTransactions(globalPage);
        } else {
            API.showToast(response.message || 'Action failed.', 'danger');
        }
        return;
    }

    // Pre-flight validation: Forward to Signatories from Accounting Support
    if (action === 'Pending Signatory Approval' && currentModalTransactionData.current_status === 'Pending Accounting Support') {
        // Check attachments exist
        const container = document.getElementById('attachmentsChecklistContainer');
        const attachmentItems = container ? container.querySelectorAll('.list-group-item:not(.text-center)') : [];
        if (attachmentItems.length === 0) {
            API.showToast('Cannot forward: No attachments have been uploaded for this transaction. All Mandatory Documentary Requirements must be submitted and approved before routing to Signatories.', 'warning');
            return;
        }
        // Check all attachments are approved (look for non-approved badges)
        const pendingOrRejected = container.querySelectorAll('.badge.bg-warning, .badge.bg-danger');
        if (pendingOrRejected.length > 0) {
            API.showToast('Cannot forward: All attachments must be approved before routing to Signatories.', 'warning');
            return;
        }
        // Check tax classification is set
        const taxSelect = document.getElementById('modalTaxType');
        if (!taxSelect || taxSelect.value === '') {
            API.showToast('Cannot forward: Tax classification must be set before routing to Signatories.', 'warning');
            return;
        }
    }

    // Standard transitions (Return/Reject, Forward to Signatories, Cashier Release)
    if (action === '') {
        // If they left it blank (meaning no Return/Reject selected for checklist stages), do nothing
        return;
    }

    const payload = {
        transaction_id: id,
        new_status: action,
        remarks: remarks,
        dv_number: dvNumber,
        bir_2307_number: birNumber
    };

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/transactions/update-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        showLoader: true
    });

    if (response && response.success) {
        API.showToast(response.message, 'success');
        const modalEl = document.getElementById('workflowModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        fetchTransactions(globalPage);
    } else {
        API.showToast(response.message || 'Failed to process action.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
