<?php
/**
 * Login Logs View for SDO FAST.
 * Shows paginated login history with search and date filters.
 * Restricted to users with configure_system permission (Super Admin).
 */

$currentPage = 'login_logs';
$pageTitle = 'Login Logs';
$pageHeader = 'Login Logs';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

// Double check permission
if (!hasPermission('configure_system')) {
    $_SESSION['flash_error'] = 'Access denied: Login Logs is restricted to Super Admin.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2"></i>Login Logs</h3>
        <p class="text-muted fs-8 mb-0" id="totalLogsSubtitle">Loading login history...</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" onclick="refreshLogs()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form id="logFilterForm" class="row g-2 align-items-end" onsubmit="event.preventDefault(); fetchLogs(1);">
            <div class="col-12 col-md-3">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Search</label>
                <input type="text" id="logSearch" class="form-control form-control-sm" placeholder="Name, email, IP, device...">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">User</label>
                <select id="filterUser" class="form-select form-select-sm">
                    <option value="">All Users</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Date From</label>
                <input type="date" id="filterDateFrom" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Date To</label>
                <input type="date" id="filterDateTo" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3 d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <button type="button" class="btn btn-sm btn-secondary d-flex align-items-center gap-1" onclick="clearFilters()">
                    <i class="bi bi-x-lg"></i> Clear
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Login Logs Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="fs-8 text-uppercase text-muted">
                        <th>User</th>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>Device / Browser</th>
                        <th>Login Time</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Loading login logs...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    <div class="card-footer bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Page 1 of 1</small>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="paginationControls">
                    <li class="page-item disabled" id="prevPageLi">
                        <button class="page-link" onclick="goToPage(currentPage - 1)" id="prevPageBtn">&laquo; Prev</button>
                    </li>
                    <li class="page-item disabled" id="nextPageLi">
                        <button class="page-link" onclick="goToPage(currentPage + 1)" id="nextPageBtn">Next &raquo;</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT
     ========================================================================= -->
<script>
let currentPage = 1;
let totalPages = 1;
const apiBase = '<?php echo env('APP_URL'); ?>/api/users/login-logs.php';

async function fetchLogs(page = 1) {
    currentPage = page;
    const search = document.getElementById('logSearch').value.trim();
    const userId = document.getElementById('filterUser').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    const params = new URLSearchParams({ action: 'list', page: currentPage, per_page: 20 });
    if (search) params.append('search', search);
    if (userId) params.append('user_id', userId);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    const tbody = document.getElementById('logsTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Loading login logs...</td></tr>';

    try {
        API.showSpinner();
        const response = await fetch(`${apiBase}?${params.toString()}`, {
            headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' }
        });
        const result = await response.json();
        API.hideSpinner();

        if (!result.success) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">${result.message || 'Failed to load logs.'}</td></tr>`;
            return;
        }

        // Populate user filter dropdown on first load
        if (result.users && result.users.length > 0) {
            const userSelect = document.getElementById('filterUser');
            if (userSelect.options.length <= 1) {
                result.users.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = `${u.full_name} (${u.email})`;
                    userSelect.appendChild(opt);
                });
            }
        }

        // Update subtitle
        document.getElementById('totalLogsSubtitle').textContent = `${result.total} login record(s) found`;

        // Render table rows
        if (!result.data || result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No login logs found.</td></tr>';
        } else {
            tbody.innerHTML = result.data.map(log => {
                const device = log.device_info ? log.device_info.substring(0, 80) + (log.device_info.length > 80 ? '...' : '') : '<em class="text-muted">Unknown</em>';
                const loginDate = log.login_at ? escapeHtml(log.login_at) : '<em class="text-muted">N/A</em>';
                const name = log.full_name ? `<strong>${escapeHtml(log.full_name)}</strong>` : '<em class="text-muted">Deleted User</em>';
                const email = log.email ? escapeHtml(log.email) : '<em class="text-muted">N/A</em>';
                return `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" 
                                     style="width: 36px; height: 36px; font-size: 0.85rem;">
                                    ${log.full_name ? getInitials(log.full_name) : '?'}
                                </div>
                                <div>
                                    ${name}
                                    <div class="fs-9 text-muted">${log.office ? escapeHtml(log.office) : ''}</div>
                                </div>
                            </div>
                        </td>
                        <td class="fs-8">${email}</td>
                        <td><code class="fs-8">${escapeHtml(log.ip_address || '—')}</code></td>
                        <td class="fs-8" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(log.device_info || '')}">
                            ${device}
                        </td>
                        <td class="fs-8 text-nowrap">${loginDate}</td>
                    </tr>
                `;
            }).join('');
        }

        // Update pagination
        totalPages = result.total_pages || 1;
        updatePagination();

    } catch (err) {
        API.hideSpinner();
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Network error: ${err.message}</td></tr>`;
    }
}

function updatePagination() {
    document.getElementById('paginationInfo').textContent = `Page ${currentPage} of ${totalPages}`;
    document.getElementById('prevPageLi').classList.toggle('disabled', currentPage <= 1);
    document.getElementById('nextPageLi').classList.toggle('disabled', currentPage >= totalPages);
}

function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    fetchLogs(page);
}

function clearFilters() {
    document.getElementById('logSearch').value = '';
    document.getElementById('filterUser').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    fetchLogs(1);
}

function refreshLogs() {
    fetchLogs(currentPage);
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
}

// Auto-load on page ready
document.addEventListener('DOMContentLoaded', () => fetchLogs(1));
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
