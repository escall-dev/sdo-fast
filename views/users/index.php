<?php
/**
 * User Management Dashboard View for SDO FAST.
 * Access restricted to Super Admin.
 * Includes position management with auto-role mapping.
 */

$currentPage = 'users';
$pageTitle = 'User Management';
$pageHeader = 'User Management';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userRole = $_SESSION['user_role'] ?? '';
$adminId = $_SESSION['user_id'] ?? 0;

// Double check permission (Super Admin or allowed users only)
if (!hasPermission('manage_users')) {
    $_SESSION['flash_error'] = 'Access denied: User Management is restricted to authorized personnel.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-2"></i>User Management</h3>
        <p class="text-muted fs-8 mb-0" id="totalUsersSubtitle">0 Total Users</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#managePositionsModal">
            <i class="bi bi-briefcase-fill"></i>
            <span>Manage Positions</span>
        </button>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add User</span>
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form id="userFilterForm" class="row g-2 align-items-end" onsubmit="event.preventDefault(); fetchUsers(1);">
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Search</label>
                <input type="text" id="userSearch" class="form-control form-control-sm" placeholder="Name, email...">
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Role</label>
                <select id="filterRole" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <!-- Populated dynamically -->
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Office</label>
                <select id="filterOffice" class="form-select form-select-sm" onchange="handleFilterOfficeChange()">
                    <option value="">All Offices</option>
                    <option value="OSDS">OSDS</option>
                    <option value="SGOD">SGOD</option>
                    <option value="CID">CID</option>
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <label class="form-label fs-8 text-muted mb-1 fw-semibold">Unit/Section</label>
                <select id="filterUnit" class="form-select form-select-sm" disabled>
                    <option value="">All Units</option>
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2 d-flex gap-1 justify-content-end">
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

<!-- Users Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive border-0">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="fs-8 text-uppercase text-muted">
                        <th>User</th>
                        <th>Employee Info</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Loading users list...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer / Pagination -->
    <div class="card-footer bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="fs-8 text-muted" id="userPaginationStats">
                Showing 0 to 0 of 0 users
            </div>
            <nav aria-label="User table navigation">
                <ul class="pagination pagination-sm mb-0" id="userPaginationList">
                    <!-- Paginated buttons -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: REGISTER NEW USER
     ========================================================================= -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="createUserModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm" onsubmit="handleCreateUserSubmit(event)">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="full_name" class="form-control" id="regName" placeholder="Full Name" required>
                                <label for="regName">Full Name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control" id="regEmail" placeholder="Email Address" required>
                                <label for="regEmail">Email Address <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="role_id" class="form-select" id="regRole" required>
                                    <option value="" disabled selected>Choose Role</option>
                                    <!-- Populated via JS -->
                                </select>
                                <label for="regRole">System Role <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="office" class="form-select" id="regOffice" onchange="handleModalOfficeChange('regOffice', 'regUnitSection')" required>
                                    <option value="" disabled selected>Choose Office</option>
                                    <option value="OSDS">OSDS (Office of the SDS Staff)</option>
                                    <option value="SGOD">SGOD (School Governance and Operations Division)</option>
                                    <option value="CID">CID (Curriculum Implementation Division)</option>
                                </select>
                                <label for="regOffice">Office <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="unit_section" class="form-select" id="regUnitSection" required disabled>
                                    <option value="" disabled selected>Choose Unit/Section</option>
                                </select>
                                <label for="regUnitSection">Unit/Section <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="employee_no" class="form-control" id="regEmployeeNo" placeholder="Employee No.">
                                <label for="regEmployeeNo">Employee No. (optional)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="position" class="form-control" id="regPositionText" placeholder="Position">
                                <label for="regPositionText">Position (optional)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="regPassword" placeholder="Password" required autocomplete="new-password" minlength="8">
                                <label for="regPassword">Password <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="password" name="confirm_password" class="form-control" id="regConfirmPassword" placeholder="Confirm Password" required autocomplete="new-password" minlength="8">
                                <label for="regConfirmPassword">Confirm Password <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="regIsActive" value="1" checked>
                                <label class="form-check-label fw-semibold text-muted" for="regIsActive">User is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1"><i class="bi bi-save"></i> Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: EDIT USER PROFILE
     ========================================================================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="editUserModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" onsubmit="handleEditUserSubmit(event)">
                <div class="modal-body py-3">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="full_name" class="form-control" id="editName" placeholder="Full Name" required>
                                <label for="editName">Full Name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control" id="editEmail" placeholder="Email Address" required>
                                <label for="editEmail">Email Address <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="role_id" class="form-select" id="editRole" required>
                                    <option value="" disabled>Choose Role</option>
                                    <!-- Populated via JS -->
                                </select>
                                <label for="editRole">System Role <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="office" class="form-select" id="editOffice" onchange="handleModalOfficeChange('editOffice', 'editUnitSection')" required>
                                    <option value="" disabled>Choose Office</option>
                                    <option value="OSDS">OSDS (Office of the SDS Staff)</option>
                                    <option value="SGOD">SGOD (School Governance and Operations Division)</option>
                                    <option value="CID">CID (Curriculum Implementation Division)</option>
                                </select>
                                <label for="editOffice">Office <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select name="unit_section" class="form-select" id="editUnitSection" required>
                                    <option value="" disabled>Choose Unit/Section</option>
                                </select>
                                <label for="editUnitSection">Unit/Section <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="employee_no" class="form-control" id="editEmployeeNo" placeholder="Employee No.">
                                <label for="editEmployeeNo">Employee No. (optional)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="position" class="form-control" id="editPositionText" placeholder="Position">
                                <label for="editPositionText">Position (optional)</label>
                            </div>
                        </div>
                        
                        <?php if ($userRole === 'Super Admin'): ?>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="editPassword" placeholder="New Password" autocomplete="new-password" minlength="8">
                                <label for="editPassword">New Password (optional)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="password" name="confirm_password" class="form-control" id="editConfirmPassword" placeholder="Confirm Password" autocomplete="new-password" minlength="8">
                                <label for="editConfirmPassword">Confirm New Password</label>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-12">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                                <label class="form-check-label fw-semibold text-muted" for="editIsActive">User is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: MANAGE POSITIONS
     ========================================================================= -->
<div class="modal fade" id="managePositionsModal" tabindex="-1" aria-labelledby="managePositionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="managePositionsModalLabel"><i class="bi bi-briefcase-fill me-2"></i>Manage Positions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted fs-8 mb-3">Positions determine which system role is auto-assigned to users. <strong>Personnel</strong> positions map to <span class="badge bg-secondary">User</span> and all others map to <span class="badge bg-primary">Admin</span>.</p>
                
                <!-- Add New Position Form -->
                <div class="card border mb-3">
                    <div class="card-body py-2 px-3">
                        <form id="addPositionForm" onsubmit="handleAddPosition(event)" class="d-flex gap-2 align-items-end flex-wrap">
                            <div class="flex-grow-1">
                                <label class="form-label fs-8 mb-1">Position Name</label>
                                <input type="text" name="position_name" class="form-control form-control-sm" id="newPositionName" placeholder="e.g. Records Officer" required>
                            </div>
                            <div style="min-width: 140px;">
                                <label class="form-label fs-8 mb-1">Maps to Role</label>
                                <select name="mapped_role" class="form-select form-select-sm" id="newPositionRole" required>
                                    <!-- Populated via JS -->
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1" style="height: 31px;">
                                <i class="bi bi-plus-lg"></i> Add
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Positions Table -->
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table align-middle table-striped fs-8 mb-0">
                        <thead>
                            <tr>
                                <th>Position Name</th>
                                <th>Mapped Role</th>
                                <th>Users Assigned</th>
                                <th>Type</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="positionsTableBody">
                            <tr><td colspan="5" class="text-center text-muted py-3">Loading positions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: SHOW PASSWORD RESET LINK
     ========================================================================= -->
<div class="modal fade" id="adminResetModal" tabindex="-1" aria-labelledby="adminResetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-success" id="adminResetModalLabel"><i class="bi bi-shield-check me-2"></i>Reset Link Generated</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted fs-8">Provide this single-use reset URL to the user to bypass their current password. The link expires in <strong>60 minutes</strong>.</p>
                <div class="input-group">
                    <input type="text" id="adminResetLinkInput" class="form-control fw-semibold text-primary fs-8" readonly>
                    <button class="btn btn-primary" type="button" onclick="copyResetLink()"><i class="bi bi-copy"></i> Copy</button>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: LOGIN HISTORY LOGS
     ========================================================================= -->
<div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-labelledby="loginHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-primary-dark" id="loginHistoryModalLabel">User Session History Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0">
                <div class="table-responsive border-0 mb-3" style="max-height: 350px;">
                    <table class="table align-middle table-striped fs-8">
                        <thead>
                            <tr>
                                <th>Login Timestamp</th>
                                <th>IP Address</th>
                                <th>User Agent Device Info</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close Logs</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT USERS LOGIC
     ========================================================================= -->
<script>
let globalUserPage = 1;
let cachedPositions = [];
let cachedRoles = [];

const officeUnits = {
    "OSDS": [
        "OSDS (Office of the SDS Staff)",
        "OASDS (Office of the ASDS Staff)",
        "Personnel",
        "Property and Supply",
        "Records",
        "Cash",
        "Procurement",
        "General Services",
        "Legal",
        "ICT",
        "Accounting (Finance - Accounting)",
        "Budget (Finance - Budget)",
        "Administrative"
    ],
    "SGOD": [
        "SGOD (School Governance and Operations Division)",
        "SMME (School Management Monitoring and Evaluation)",
        "HRD (Human Resource Development)",
        "SMN (Social Mobilization and Networking)",
        "PR (Planning and Research)",
        "DRRM (Disaster Risk Reduction and Management)",
        "EF (Education Facilities)",
        "SHN_DENTAL (School Health and Nutrition - Dental)",
        "SHN_MEDICAL (School Health and Nutrition - Medical)",
        "SHN (School Health and Nutrition)"
    ],
    "CID": [
        "CID (Curriculum Implementation Division)",
        "IM (Instructional Management)",
        "LRM (Learning Resource Management)",
        "ALS (Alternative Learning System)",
        "DIS (District Instructional Supervision)"
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    loadPositions().then(() => {
        fetchUsers(1);
    });

    // Clear and reset modals on show
    document.getElementById('createUserModal').addEventListener('show.bs.modal', function() {
        document.getElementById('createUserForm').reset();
        fillUnitSelectDropdown('regUnitSection', '');
    });

    // Load positions when manage positions modal opens
    document.getElementById('managePositionsModal').addEventListener('show.bs.modal', function() {
        fetchPositionsList();
    });
});

function fillUnitSelectDropdown(selectId, officeCode, selectedValue = '') {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="" disabled selected>Choose Unit/Section</option>';
    select.disabled = true;
    
    if (officeCode && officeUnits[officeCode]) {
        select.disabled = false;
        officeUnits[officeCode].forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            option.textContent = unit;
            if (unit === selectedValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }
}

function handleModalOfficeChange(officeSelectId, unitSelectId) {
    const officeCode = document.getElementById(officeSelectId).value;
    fillUnitSelectDropdown(unitSelectId, officeCode);
}

function handleFilterOfficeChange() {
    const officeCode = document.getElementById('filterOffice').value;
    const select = document.getElementById('filterUnit');
    select.innerHTML = '<option value="">All Units</option>';
    select.disabled = true;
    
    if (officeCode && officeUnits[officeCode]) {
        select.disabled = false;
        officeUnits[officeCode].forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            option.textContent = unit;
            select.appendChild(option);
        });
    }
}

function clearFilters() {
    document.getElementById('userSearch').value = '';
    document.getElementById('filterRole').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterOffice').value = '';
    const filterUnit = document.getElementById('filterUnit');
    filterUnit.innerHTML = '<option value="">All Units</option>';
    filterUnit.disabled = true;
    fetchUsers(1);
}

async function loadPositions() {
    const response = await API.request('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=list_positions');
    
    if (response && response.success) {
        cachedPositions = response.data.positions;
        cachedRoles = response.data.roles;
        populatePositionDropdowns();
    }
}

function populatePositionDropdowns() {
    const regRoleSelect = document.getElementById('regRole');
    const editRoleSelect = document.getElementById('editRole');
    const filterRoleSelect = document.getElementById('filterRole');
    
    // Clear existing options
    regRoleSelect.innerHTML = '<option value="" disabled selected>Choose Role</option>';
    editRoleSelect.innerHTML = '<option value="" disabled>Choose Role</option>';
    filterRoleSelect.innerHTML = '<option value="">All Roles</option>';
    
    cachedRoles.forEach(role => {
        const optionHTML = `<option value="${role.id}">${role.role_name}</option>`;
        regRoleSelect.insertAdjacentHTML('beforeend', optionHTML);
        editRoleSelect.insertAdjacentHTML('beforeend', optionHTML);
        filterRoleSelect.insertAdjacentHTML('beforeend', optionHTML);
    });

    // Also populate new position mapped role select dynamically in position modal
    const newPositionRoleSelect = document.getElementById('newPositionRole');
    if (newPositionRoleSelect) {
        newPositionRoleSelect.innerHTML = '<option value="" disabled selected>Choose Role</option>';
        cachedRoles.forEach(role => {
            const optionHTML = `<option value="${role.role_name}">${role.role_name}</option>`;
            newPositionRoleSelect.insertAdjacentHTML('beforeend', optionHTML);
        });
    }
}

async function fetchUsers(page) {
    globalUserPage = page;
    const search = document.getElementById('userSearch').value;
    const roleId = document.getElementById('filterRole').value;
    const status = document.getElementById('filterStatus').value;
    const office = document.getElementById('filterOffice').value;
    const unitSection = document.getElementById('filterUnit').value;

    const params = new URLSearchParams({
        action: 'list',
        page: page,
        per_page: 10,
        search: search,
        role_id: roleId,
        status: status,
        office: office,
        unit_section: unitSection
    });

    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading users...</td></tr>';

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?' + params.toString());
    
    if (response && response.success) {
        const totalUsersSubtitle = document.getElementById('totalUsersSubtitle');
        if (totalUsersSubtitle) {
            totalUsersSubtitle.textContent = `${response.data.total_count} Total Users`;
        }
        renderUsersTable(response.data.users);
        renderUserPagination(response.data.total_count, page, 10);
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle"></i> Failed to retrieve user accounts.</td></tr>';
    }
}

function renderUsersTable(users) {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No user accounts found.</td></tr>';
        return;
    }

    users.forEach(user => {
        const isSelf = user.id == <?php echo $adminId; ?>;
        
        let statusToggleBtn = '';
        let deleteUserBtn = '';
        if (!isSelf) {
            const isInactive = user.status === 'inactive';
            const actionStatus = isInactive ? 'active' : 'inactive';
            const btnClass = isInactive ? 'btn-outline-success' : 'btn-outline-danger';
            const btnText = isInactive ? 'Activate' : 'Suspend';
            statusToggleBtn = `
                <button class="btn btn-sm ${btnClass} py-1 px-2" onclick="toggleUserStatus(${user.id}, '${actionStatus}')">
                    ${btnText}
                </button>
            `;
            deleteUserBtn = `
                <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="deleteUser(${user.id}, '${user.full_name.replace(/'/g, "\\'")}')" title="Delete User">
                    <i class="bi bi-trash-fill"></i>
                </button>
            `;
        }

        const dateFormatted = new Date(user.created_at).toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric'
        });

        const lastLoginFormatted = user.last_login 
            ? new Date(user.last_login).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true })
            : 'Never';

        const statusClass = user.status === 'active' ? 'bg-success' : 'bg-secondary';
        
        // Role badge color based on role
        let roleBadgeClass = 'bg-light text-dark border';
        if (user.role_name === 'Super Admin') {
            roleBadgeClass = 'bg-warning text-dark';
        } else if (user.role_name === 'Admin') {
            roleBadgeClass = 'bg-primary text-white';
        } else if (user.role_name === 'Accounting Staff') {
            roleBadgeClass = 'bg-info text-white';
        } else if (user.role_name === 'Budget Officer') {
            roleBadgeClass = 'bg-success text-white';
        } else if (user.role_name === 'Approver') {
            roleBadgeClass = 'bg-dark text-white';
        } else if (user.role_name === 'Cashier') {
            roleBadgeClass = 'bg-secondary text-white';
        }

        const initial = user.full_name.charAt(0).toUpperCase();

        const rowHTML = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px; font-size: 1.1rem;">
                            ${initial}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-8">${user.full_name} ${isSelf ? '<span class="badge bg-light text-primary border ms-1 fs-9">You</span>' : ''}</div>
                            <div class="text-muted fs-9">${user.email}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold text-dark fs-8">${user.position || '—'}</div>
                    <div class="text-muted fs-9">${user.office || ''} ${user.unit_section ? ' - ' + user.unit_section : ''}</div>
                </td>
                <td><span class="badge ${roleBadgeClass} text-uppercase fs-9">${user.role_name || 'USER'}</span></td>
                <td><span class="badge ${statusClass} badge-status">${user.status}</span></td>
                <td>
                    <div class="fw-semibold text-dark fs-8">${dateFormatted}</div>
                    <div class="text-muted fs-9">${lastLoginFormatted}</div>
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-sm btn-light border py-1 px-2" onclick="openEditUserModal(${JSON.stringify(user).replace(/"/g, '&quot;')})">Edit</button>
                        ${statusToggleBtn}
                        ${deleteUserBtn}
                        <button class="btn btn-sm btn-light border py-1 px-2" onclick="triggerPasswordReset(${user.id})" title="Reset Password link"><i class="bi bi-key-fill text-muted"></i></button>
                        <button class="btn btn-sm btn-light border py-1 px-2" onclick="viewLoginHistory(${user.id})" title="View Logins"><i class="bi bi-activity text-muted"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', rowHTML);
    });
}

function renderUserPagination(totalCount, currentPage, perPage) {
    const list = document.getElementById('userPaginationList');
    list.innerHTML = '';

    const stats = document.getElementById('userPaginationStats');
    if (totalCount === 0) {
        stats.innerHTML = 'Showing 0 to 0 of 0 users';
        return;
    }

    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, totalCount);
    stats.innerHTML = `Showing ${start} to ${end} of ${totalCount} users`;

    const totalPages = Math.ceil(totalCount / perPage);

    // Prev Page
    const prevClass = currentPage === 1 ? 'disabled' : '';
    list.insertAdjacentHTML('beforeend', `
        <li class="page-item ${prevClass}"><a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>
    `);

    // Pages
    for (let i = 1; i <= totalPages; i++) {
        const activeClass = currentPage === i ? 'active' : '';
        list.insertAdjacentHTML('beforeend', `
            <li class="page-item ${activeClass}"><a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${i})">${i}</a></li>
        `);
    }

    // Next Page
    const nextClass = currentPage === totalPages ? 'disabled' : '';
    list.insertAdjacentHTML('beforeend', `
        <li class="page-item ${nextClass}"><a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>
    `);

    // Override custom style loops for nav links
    list.querySelectorAll('.page-link').forEach(link => {
        link.style.display = 'inline-flex';
        link.style.alignItems = 'center';
        link.style.justifyContent = 'center';
        link.style.width = '34px';
        link.style.height = '34px';
        link.style.borderRadius = '6px';
        link.style.margin = '0 2px';
        link.style.color = 'var(--color-primary)';
        link.style.border = '1px solid #e2e8f0';
    });
    const activeLink = list.querySelector('.page-item.active .page-link');
    if (activeLink) {
        activeLink.style.backgroundColor = 'var(--color-primary)';
        activeLink.style.color = '#ffffff';
        activeLink.style.borderColor = 'var(--color-primary)';
    }
}

async function handleCreateUserSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('createUserForm');
    const formData = new FormData(form);

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=create', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });

    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        
        // Hide Modal
        const modalEl = document.getElementById('createUserModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        form.reset();
        
        fetchUsers(1);
    } else {
        API.showToast(data.message || 'Failed to register account.', 'danger');
    }
}

function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editName').value = user.full_name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editRole').value = user.role_id || '';
    document.getElementById('editOffice').value = user.office || '';
    
    fillUnitSelectDropdown('editUnitSection', user.office || '', user.unit_section || '');
    
    document.getElementById('editEmployeeNo').value = user.employee_no || '';
    document.getElementById('editPositionText').value = user.position || '';
    
    const editPassword = document.getElementById('editPassword');
    if (editPassword) editPassword.value = '';
    const editConfirmPassword = document.getElementById('editConfirmPassword');
    if (editConfirmPassword) editConfirmPassword.value = '';
    
    document.getElementById('editIsActive').checked = (user.status === 'active');

    const modalEl = document.getElementById('editUserModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    if (!modalEl.classList.contains('show')) {
        modal.show();
    }
}

async function handleEditUserSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=update', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });

    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        
        // Hide Modal
        const modalEl = document.getElementById('editUserModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        
        fetchUsers(globalUserPage);
    } else {
        API.showToast(data.message || 'Failed to update account.', 'danger');
    }
}

async function toggleUserStatus(userId, status) {
    if (!await API.confirmAction("Confirmation", `Are you sure you want to suspend or activate this user account?`, "Yes, Proceed", "warning")) return;

    const payload = new FormData();
    payload.append('user_id', userId);
    payload.append('status', status);

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=status', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: payload
    });

    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        fetchUsers(globalUserPage);
    } else {
        API.showToast(data.message || 'Status change failed.', 'danger');
    }
}

async function triggerPasswordReset(userId) {
    if (!await API.confirmAction("Warning", "Are you sure you want to reset this user's password? It will invalidate their current password immediately.", "Yes, Reset", "warning")) return;
    
    const payload = new FormData();
    payload.append('user_id', userId);

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=reset', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: payload
    });

    const data = await response.json();
    if (data.success) {
        document.getElementById('adminResetLinkInput').value = data.data.reset_link;
        
        // Open Modal
        const modalEl = document.getElementById('adminResetModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        if (!modalEl.classList.contains('show')) {
            modal.show();
        }
    } else {
        API.showToast(data.message || 'Failed to generate reset link.', 'danger');
    }
}

function copyResetLink() {
    const input = document.getElementById('adminResetLinkInput');
    input.select();
    input.setSelectionRange(0, 99999); // Mobile
    navigator.clipboard.writeText(input.value);
    API.showToast('Reset URL copied to clipboard!', 'success');
}

async function viewLoginHistory(userId) {
    const response = await API.request('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=history&user_id=' + userId);
    
    if (response && response.success) {
        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = '';
        
        if (response.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No session logs found for this user account.</td></tr>';
        } else {
            response.data.forEach(log => {
                tbody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td><strong>${log.login_at}</strong></td>
                        <td><code>${log.ip_address}</code></td>
                        <td class="text-truncate" style="max-width: 320px;" title="${log.device_info}">${log.device_info}</td>
                    </tr>
                `);
            });
        }
        
        const modalEl = document.getElementById('loginHistoryModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        if (!modalEl.classList.contains('show')) {
            modal.show();
        }
    } else {
        API.showToast('Failed to load login logs.', 'danger');
    }
}

// =========================================================================
// POSITIONS MANAGEMENT (Admin only)
// =========================================================================

async function fetchPositionsList() {
    const tbody = document.getElementById('positionsTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';
    
    const response = await API.request('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=list_positions');
    
    if (response && response.success) {
        cachedPositions = response.data.positions;
        cachedRoles = response.data.roles;
        populatePositionDropdowns();
        renderPositionsTable(cachedPositions);
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Failed to load positions.</td></tr>';
    }
}

function renderPositionsTable(positions) {
    const tbody = document.getElementById('positionsTableBody');
    tbody.innerHTML = '';
    
    if (positions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No positions defined.</td></tr>';
        return;
    }
    
    positions.forEach(pos => {
        const isDefault = parseInt(pos.is_default) === 1;
        let roleBadgeClass = 'bg-light text-dark border';
        if (pos.mapped_role === 'Super Admin') {
            roleBadgeClass = 'bg-warning text-dark';
        } else if (pos.mapped_role === 'Admin') {
            roleBadgeClass = 'bg-primary text-white';
        } else if (pos.mapped_role === 'Accounting Staff') {
            roleBadgeClass = 'bg-info text-white';
        } else if (pos.mapped_role === 'Budget Officer') {
            roleBadgeClass = 'bg-success text-white';
        } else if (pos.mapped_role === 'Approver') {
            roleBadgeClass = 'bg-dark text-white';
        } else if (pos.mapped_role === 'Cashier') {
            roleBadgeClass = 'bg-secondary text-white';
        }
        const typeBadge = isDefault 
            ? '<span class="badge bg-light text-dark border">Default</span>' 
            : '<span class="badge bg-info text-white">Custom</span>';
        
        const deleteBtn = isDefault 
            ? '<span class="text-muted fst-italic fs-9">Protected</span>'
            : `<button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deletePosition(${pos.id}, '${pos.position_name.replace(/'/g, "\\'")}')" ${parseInt(pos.user_count) > 0 ? 'disabled title="Users assigned"' : ''}>
                <i class="bi bi-trash"></i> Delete
              </button>`;
        
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td><strong>${pos.position_name}</strong></td>
                <td><span class="badge ${roleBadgeClass}">${pos.mapped_role}</span></td>
                <td>${pos.user_count} user(s)</td>
                <td>${typeBadge}</td>
                <td class="text-end">${deleteBtn}</td>
            </tr>
        `);
    });
}

async function handleAddPosition(e) {
    e.preventDefault();
    
    const name = document.getElementById('newPositionName').value.trim();
    const role = document.getElementById('newPositionRole').value;
    
    if (!name) return;
    
    const formData = new FormData();
    formData.append('position_name', name);
    formData.append('mapped_role', role);
    
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=add_position', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    
    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        document.getElementById('addPositionForm').reset();
        fetchPositionsList();
    } else {
        API.showToast(data.message || 'Failed to add position.', 'danger');
    }
}

async function deletePosition(positionId, positionName) {
    if (!await API.confirmAction("Delete Position", `Are you sure you want to delete the position "${positionName}"?`, "Yes, Delete", "error")) return;
    
    const formData = new FormData();
    formData.append('position_id', positionId);
    
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=delete_position', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    
    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        fetchPositionsList();
    } else {
        API.showToast(data.message || 'Failed to delete position.', 'danger');
    }
}

async function deleteUser(userId, userName) {
    if (!await API.confirmAction("Delete User", `Are you sure you want to permanently delete the user account "${userName}"? This action cannot be undone.`, "Yes, Delete", "error")) return;
    
    const formData = new FormData();
    formData.append('user_id', userId);
    
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/users/manage-users.php?action=delete', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    
    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        fetchUsers(globalUserPage);
    } else {
        API.showToast(data.message || 'Failed to delete user account.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
