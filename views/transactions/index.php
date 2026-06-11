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

$userRole = $_SESSION['user_role'] ?? '';
$userPosition = $_SESSION['user_position'] ?? '';

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
<?php if (in_array($userRole, ['Super Admin', 'Approver', 'Accounting Staff', 'Budget Officer']) || in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS'])): ?>
<div class="modal fade" id="workflowModal" tabindex="-1" aria-labelledby="workflowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="workflowModalLabel">Transaction Workflow Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="workflowForm" onsubmit="handleWorkflowSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="workflowTransactionId">
                    
                    <div class="mb-3">
                        <span class="fs-8 text-muted text-uppercase d-block">Transaction Details</span>
                        <div class="p-3 bg-light rounded-3 mt-1">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Tracking No.</small>
                                    <strong id="modalTrackingNo" class="text-primary">-</strong>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Net Amount</small>
                                    <strong id="modalNetAmount" class="text-primary-dark">-</strong>
                                </div>
                                <div class="col-12 mb-2">
                                    <small class="text-muted d-block">Type / Category</small>
                                    <strong id="modalTypeCategory" class="fs-8">-</strong>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Event Name</small>
                                    <strong id="modalEventName" class="fs-8">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="workflowAction" class="form-label fs-8 fw-semibold text-muted">Workflow Action</label>
                        <select id="workflowAction" class="form-select" required onchange="toggleWorkflowFormDetails()">
                            <!-- Generated dynamically based on user role and transaction state -->
                        </select>
                    </div>

                    <!-- DV details input (Shown only if STAFF moving to Pending Final Approval or Approver approving) -->
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
                        <label for="workflowRemarks" class="form-label fs-8 fw-semibold text-muted">Action Remarks</label>
                        <textarea id="workflowRemarks" class="form-control" rows="3" placeholder="Provide reason or audit notes for this action..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process State</button>
                </div>
            </form>
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
        let statusBadgeClass = 'bg-secondary';
        switch (row.current_status) {
            case 'Pending Accountant 1':
                statusBadgeClass = 'bg-warning text-dark';
                break;
            case 'Pending Support':
                statusBadgeClass = 'bg-secondary';
                break;
            case 'Pending Budget Check':
                statusBadgeClass = 'bg-info text-dark';
                break;
            case 'Pending Accountant 2':
                statusBadgeClass = 'bg-primary text-white';
                break;
            case 'Pending Final Approval':
                statusBadgeClass = 'bg-danger text-white';
                break;
            case 'Approved':
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
        let actionBtn = '';
        
        // Show Workflow action button if role or position is authorized
        const showWorkflowAction = 
            (userRole === 'Super Admin' && ['Pending Accountant 1', 'Pending Support', 'Pending Budget Check', 'Pending Accountant 2', 'Pending Final Approval'].includes(row.current_status)) ||
            (userPosition === 'Accountant' && ['Pending Accountant 1', 'Pending Accountant 2'].includes(row.current_status)) ||
            ((userRole === 'Accounting Staff' || userPosition === 'Accounting Support') && row.current_status === 'Pending Support') ||
            ((userRole === 'Budget Officer' || userPosition === 'Budget Officer') && row.current_status === 'Pending Budget Check') ||
            ((userRole === 'Approver' || userPosition === 'ASDS' || userPosition === 'SDS') && row.current_status === 'Pending Final Approval');

        if (showWorkflowAction) {
            actionBtn = `
                <button class="btn btn-sm btn-primary" onclick="openWorkflowModal(${JSON.stringify(row).replace(/"/g, '&quot;')})" title="Workflow Action">
                    <i class="bi bi-lightning-fill"></i>
                </button>
            `;
        }

        const rowHTML = `
            <tr>
                <td>
                    <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=${encodeURIComponent(row.tracking_number)}" class="fw-bold text-decoration-none text-primary" title="${row.tracking_number}">
                        ${row.tracking_number}
                    </a>
                </td>
                <td class="transactions-col-event" title="${row.event_name}">${row.event_name}</td>
                <td title="${row.transaction_type}${row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : ''}"><span class="badge bg-light text-dark border txn-type-badge">${row.transaction_type}${row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : ''}</span></td>
                <td class="transactions-col-requestor" title="${row.requestor_name} — ${row.requestor_email}">
                    <span class="txn-requestor-name fw-semibold">${row.requestor_name}</span>
                    <span class="txn-requestor-email text-muted">${row.requestor_email}</span>
                </td>
                <td class="fw-semibold" title="₱${gross}">₱${gross}</td>
                <td title="${row.current_status}">
                    <span class="badge badge-status ${statusBadgeClass}">${row.current_status}</span>
                </td>
                <td class="text-muted" title="${dateFormatted}">${dateFormatted}</td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1 txn-actions">
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

function openWorkflowModal(row) {
    currentModalTransactionData = row;
    
    document.getElementById('workflowTransactionId').value = row.id;
    document.getElementById('modalTrackingNo').innerText = row.tracking_number;
    document.getElementById('modalNetAmount').innerText = '₱' + parseFloat(row.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('modalEventName').innerText = row.event_name;
    document.getElementById('modalTypeCategory').innerText = row.transaction_type + (row.cash_advance_category ? ' (' + row.cash_advance_category + ')' : '');
    document.getElementById('workflowRemarks').value = '';

    const actionSelect = document.getElementById('workflowAction');
    actionSelect.innerHTML = '';

    const userRole = '<?php echo $userRole; ?>';
    const userPosition = '<?php echo $userPosition; ?>';
    
    if (userRole === 'Super Admin' || userPosition === 'Accountant') {
        if (row.current_status === 'Pending Accountant 1') {
            actionSelect.innerHTML += `
                <option value="Pending Support">Route to Accounting Support (Accountant Initial Check Complete)</option>
                <option value="Returned">Return to Requestor</option>
                <option value="Rejected">Reject Transaction</option>
            `;
        }
        if (row.current_status === 'Pending Accountant 2') {
            actionSelect.innerHTML += `
                <option value="Pending Final Approval">Route to Final Approver (Accountant Final Check Complete)</option>
                <option value="Returned">Return to Requestor</option>
                <option value="Rejected">Reject Transaction</option>
            `;
        }
    }
    
    if (userRole === 'Super Admin' || userRole === 'Accounting Staff' || userPosition === 'Accounting Support') {
        if (row.current_status === 'Pending Support') {
            actionSelect.innerHTML += `
                <option value="Pending Budget Check">Route to Budget Officer (Support Verification Complete)</option>
                <option value="Returned">Return to Requestor</option>
                <option value="Rejected">Reject Transaction</option>
            `;
        }
    }
    
    if (userRole === 'Super Admin' || userRole === 'Budget Officer' || userPosition === 'Budget Officer') {
        if (row.current_status === 'Pending Budget Check') {
            actionSelect.innerHTML += `
                <option value="Pending Accountant 2">Route to Accountant (Budget Check Complete)</option>
                <option value="Returned">Return to Requestor</option>
                <option value="Rejected">Reject Transaction</option>
            `;
        }
    }
    
    if (userRole === 'Super Admin' || userRole === 'Approver' || userPosition === 'ASDS' || userPosition === 'SDS') {
        if (row.current_status === 'Pending Final Approval') {
            actionSelect.innerHTML += `
                <option value="Approved">Approve Disbursement / Release Payment</option>
                <option value="Returned">Return to Requestor</option>
                <option value="Rejected">Reject Transaction</option>
            `;
        }
    }

    // Force default select change trigger
    toggleWorkflowFormDetails();
    
    // Open Bootstrap Modal
    const modalEl = document.getElementById('workflowModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function toggleWorkflowFormDetails() {
    const action = document.getElementById('workflowAction').value;
    const dvSection = document.getElementById('dvDetailsSection');
    const userRole = '<?php echo $userRole; ?>';

    // Show DV / BIR fields if staff routing to Final Approval (Accounting check complete), OR if Approver/Admin is approving
    const isRoutingToApprover = (action === 'Pending Final Approval');
    const isFinalApproval = (action === 'Approved');

    if (isRoutingToApprover || isFinalApproval) {
        dvSection.style.display = 'block';
        
        // Populate inputs if already populated in row data
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
        
        // Close modal
        const modalEl = document.getElementById('workflowModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        
        // Refresh Table
        fetchTransactions(globalPage);
    } else {
        API.showToast(response.message || 'Failed to update transaction status.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
