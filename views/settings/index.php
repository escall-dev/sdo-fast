<?php
/**
 * System Settings View for SDO FAST.
 * Access restricted to Super Admin.
 */

$currentPage = 'settings';
$pageTitle = 'System Settings';
$pageHeader = 'System Settings';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

// Double check permission
if (!hasPermission('configure_system')) {
    $_SESSION['flash_error'] = 'Access denied: System Settings is restricted to Super Admin.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Fetch current configurations
$taxConfigurations = [
    ['tax_type' => 'Goods', 'tax_percentage' => 5.00, 'is_active' => 1],
    ['tax_type' => 'Foods', 'tax_percentage' => 2.00, 'is_active' => 1],
    ['tax_type' => 'Services', 'tax_percentage' => 10.00, 'is_active' => 1]
];

// Fetch roles with user count
$roles = [];
$roleUsers = [];

if ($fastPDO !== null) {
    try {
        $configs = $fastPDO->query("SELECT tax_type, tax_percentage, is_active FROM tax_configurations ORDER BY tax_type ASC")->fetchAll();
        if (!empty($configs)) {
            $taxConfigurations = $configs;
        }

        $stmt = $fastPDO->query("
            SELECT r.id, r.role_name, COUNT(ur.user_id) as user_count 
            FROM roles r
            LEFT JOIN user_roles ur ON r.id = ur.role_id
            GROUP BY r.id, r.role_name
            ORDER BY r.role_name ASC
        ");
        $roles = $stmt->fetchAll();

        // Fetch users assigned to each role with their positions
        $usersStmt = $fastPDO->query("
            SELECT ur.role_id, u.full_name, p.position_name 
            FROM user_roles ur
            JOIN users u ON ur.user_id = u.id
            LEFT JOIN positions p ON u.position_id = p.id
            ORDER BY u.full_name ASC
        ");
        foreach ($usersStmt->fetchAll() as $row) {
            $roleUsers[$row['role_id']][] = $row;
        }
    } catch (PDOException $e) {
        error_log("Failed to load settings data: " . $e->getMessage());
    }
}
?>

<!-- Tab Layout Header -->
<div class="row mb-4">
    <div class="col-12">
        <ul class="nav nav-tabs border-bottom" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="tax-tab" data-bs-toggle="tab" data-bs-target="#taxContent" type="button" role="tab" aria-controls="taxContent" aria-selected="true">
                    <i class="bi bi-percent me-2"></i>Global Tax Configuration
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="access-tab" data-bs-toggle="tab" data-bs-target="#accessContent" type="button" role="tab" aria-controls="accessContent" aria-selected="false">
                    <i class="bi bi-shield-lock me-2"></i>Role Access Control
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categoriesContent" type="button" role="tab" aria-controls="categoriesContent" aria-selected="false">
                    <i class="bi bi-tags me-2"></i>Coverage Categories
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="settingsTabsContent">
    <!-- Tab 1: Global Tax Configuration -->
    <div class="tab-pane fade show active" id="taxContent" role="tabpanel" aria-labelledby="tax-tab">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold text-primary-dark">Global Tax Configurations</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-8 mb-4">Define the dynamic tax rates applied during transaction submission. All changes are logged instantly in the system audit logs.</p>
                        
                        <form id="taxSettingsForm" onsubmit="handleSettingsSubmit(event)">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fs-8 fw-semibold text-muted mb-0">Tax Items</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTaxRow()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Tax
                                </button>
                            </div>
                            <div id="taxRowsContainer" class="d-flex flex-column gap-3 mb-4">
                                <?php foreach ($taxConfigurations as $config): ?>
                                    <div class="p-3 border rounded-3 bg-light-subtle tax-row">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-12 col-md-5">
                                                <label class="form-label fs-9 fw-semibold text-muted">Tax Label</label>
                                                <input type="text" name="tax_type[]" class="form-control tax-type-input" maxlength="50" required value="<?php echo htmlspecialchars($config['tax_type']); ?>" placeholder="e.g. Goods">
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label fs-9 fw-semibold text-muted">Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" name="tax_percentage[]" class="form-control" value="<?php echo htmlspecialchars($config['tax_percentage']); ?>" step="0.01" min="0" max="100" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-8 col-md-2">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input tax-active-input" type="checkbox" name="is_active[]" value="1" <?php echo ((int)$config['is_active'] === 1) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label fs-9 text-muted">Active</label>
                                                </div>
                                            </div>
                                            <div class="col-4 col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTaxRow(this)" title="Remove Tax Row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo env('APP_URL'); ?>/views/dashboard/index.php" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 justify-content-center">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab 2: Role Access Control -->
    <div class="tab-pane fade" id="accessContent" role="tabpanel" aria-labelledby="access-tab">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary-dark">Role Access Control</h5>
                <span class="badge bg-light text-muted border">Manage permissions</span>
            </div>
            <div class="card-body">
                <p class="text-muted fs-8 mb-4">Define and manage permissions per system role. Saved permissions will apply across all system modules immediately for all users assigned to that role.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0">
                        <thead class="table-light">
                            <tr>
                                <th>Role Name</th>
                                <th>Assigned Users Count</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($roles)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No system roles found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $roleBadgeClass = 'bg-light text-dark border';
                                            if ($role['role_name'] === 'Super Admin') {
                                                $roleBadgeClass = 'bg-warning text-dark';
                                            } else if ($role['role_name'] === 'Admin') {
                                                $roleBadgeClass = 'bg-primary text-white';
                                            } else if ($role['role_name'] === 'Accounting Staff') {
                                                $roleBadgeClass = 'bg-info text-white';
                                            } else if ($role['role_name'] === 'Budget Officer') {
                                                $roleBadgeClass = 'bg-success text-white';
                                            } else if ($role['role_name'] === 'Approver') {
                                                $roleBadgeClass = 'bg-dark text-white';
                                            } else if ($role['role_name'] === 'Cashier') {
                                                $roleBadgeClass = 'bg-secondary text-white';
                                            }
                                            ?>
                                            <span class="badge <?php echo $roleBadgeClass; ?> fs-7 py-2 px-3">
                                                <?php echo htmlspecialchars($role['role_name']); ?>
                                            </span>
                                        </td>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark mb-1"><?php echo htmlspecialchars($role['user_count']); ?> user(s)</div>
                                            <?php if (!empty($roleUsers[$role['id']])): ?>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <?php foreach ($roleUsers[$role['id']] as $u): ?>
                                                        <span class="badge bg-light text-secondary border fs-9" style="font-size: 0.75rem;" title="Position: <?php echo htmlspecialchars($u['position_name'] ?? 'None'); ?>">
                                                            <?php echo htmlspecialchars($u['full_name']); ?> 
                                                            <span class="text-muted" style="font-size: 0.65rem;">(<?php echo htmlspecialchars($u['position_name'] ?? 'No Position'); ?>)</span>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted fst-italic">No users assigned</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary px-3" 
                                                    onclick="openEditPermissionsModal(
                                                        <?php echo $role['id']; ?>, 
                                                        '<?php echo addslashes(htmlspecialchars($role['role_name'])); ?>'
                                                    )">
                                                <i class="bi bi-shield-check me-1"></i> Edit Permissions
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Coverage Categories -->
    <div class="tab-pane fade" id="categoriesContent" role="tabpanel" aria-labelledby="categories-tab">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-primary-dark">Coverage Categories</h5>
                <button type="button" class="btn btn-sm btn-primary" onclick="openCategoryModal()">
                    <i class="bi bi-plus-lg me-1"></i>Add Category
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted fs-8 mb-4">Manage Cash Advance and Reimbursement coverage types, sub-field rules, and DM 214 document checklists. Deactivated categories are hidden from new submissions but remain on existing transactions.</p>

                <ul class="nav nav-pills mb-3" id="categoryTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ca-cat-tab" data-bs-toggle="pill" data-bs-target="#caCatList" type="button" role="tab">Cash Advance</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reimb-cat-tab" data-bs-toggle="pill" data-bs-target="#reimbCatList" type="button" role="tab">Reimbursement</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="caCatList" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Display Label</th>
                                        <th>Sub-fields</th>
                                        <th>Docs</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="caCategoriesTableBody">
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading categories...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reimbCatList" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Display Label</th>
                                        <th>Sub-fields</th>
                                        <th>Docs</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reimbCategoriesTableBody">
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading categories...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: EDIT COVERAGE CATEGORY
     ========================================================================= -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="editCategoryModalLabel">Coverage Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" onsubmit="handleCategorySave(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="catId">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fs-8 fw-semibold">Transaction Type</label>
                            <select name="transaction_type" id="catTransactionType" class="form-select" required onchange="toggleCategoryFieldConfig()">
                                <option value="Cash Advance">Cash Advance</option>
                                <option value="Reimbursement">Reimbursement</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-8 fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="catSortOrder" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="catIsActive" value="1" checked>
                                <label class="form-check-label" for="catIsActive">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-8 fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="catName" class="form-control" maxlength="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-8 fw-semibold">Display Label</label>
                            <input type="text" name="display_label" id="catDisplayLabel" class="form-control" maxlength="255" placeholder="Optional dropdown label">
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary mb-2">Sub-fields</h6>
                    <div class="row g-2 mb-3" id="caFieldConfigGroup">
                        <?php
                        $caFields = [
                            'dateVenue' => 'Date & Venue',
                            'fundSource' => 'Fund Source',
                            'taItinerary' => 'Travel Authority / Itinerary',
                            'activityProposal' => 'Activity Proposal',
                            'month' => 'Month Selector',
                        ];
                        foreach ($caFields as $key => $label): ?>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input ca-field-cb" type="checkbox" name="field_config[<?php echo $key; ?>]" value="1" id="caField_<?php echo $key; ?>">
                                    <label class="form-check-label fs-8" for="caField_<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="row g-2 mb-3 d-none" id="reimbFieldConfigGroup">
                        <?php
                        $reimbFields = [
                            'dateVenue' => 'Date & Venue',
                            'taItinerary' => 'Travel Authority / Itinerary',
                            'activityProposal' => 'Activity Proposal',
                            'communications' => 'Communication Load uploads',
                            'utilityBills' => 'Utility Bills uploads',
                        ];
                        foreach ($reimbFields as $key => $label): ?>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input reimb-field-cb" type="checkbox" name="field_config[<?php echo $key; ?>]" value="1" id="reimbField_<?php echo $key; ?>">
                                    <label class="form-check-label fs-8" for="reimbField_<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="accordion mb-3" id="aliasAccordion">
                        <div class="accordion-item border rounded-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fs-8" type="button" data-bs-toggle="collapse" data-bs-target="#aliasCollapse">
                                    Checklist Alias (optional)
                                </button>
                            </h2>
                            <div id="aliasCollapse" class="accordion-collapse collapse" data-bs-parent="#aliasAccordion">
                                <div class="accordion-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fs-8">Use checklist from another category</label>
                                            <select name="alias_category_id" id="catAliasCategoryId" class="form-select">
                                                <option value="">None — use own documents</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-8">Alias Note</label>
                                            <input type="text" name="alias_note" id="catAliasNote" class="form-control" placeholder="Shown above checklist when aliased">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fs-8">Source Label</label>
                                            <input type="text" name="alias_source_label" id="catAliasSourceLabel" class="form-control" placeholder="e.g. Same as Cash Advance: Honorarium">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-secondary mb-0">Required Documents</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocumentRow()">
                            <i class="bi bi-plus-lg me-1"></i>Add Document
                        </button>
                    </div>
                    <div id="documentRowsContainer" class="d-flex flex-column gap-2 mb-2"></div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: EDIT PERMISSIONS
     ========================================================================= -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-labelledby="editPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="editPermissionsModalLabel">Edit Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionsForm" onsubmit="handlePermissionsSubmit(event)">
                <input type="hidden" name="role_id" id="permRoleId">
                <div class="modal-body py-3">
                    <!-- Role details block -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <span class="fs-8 text-muted d-block">System Role Name</span>
                                <strong id="permRoleName" class="text-primary-dark fs-7">Role Name</strong>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3 text-secondary">Permission Checklist</h6>
                    
                    <div class="row g-2">
                        <?php
                        $permissionsList = [
                            'view' => 'view (Read-only access to transactions and reports)',
                            'encode' => 'encode (Submit/register new transactions)',
                            'edit' => 'edit (Update existing transactions/details)',
                            'approve' => 'approve (Approve or route transaction status)',
                            'delete' => 'delete (Permanently remove records)',
                            'manage_users' => 'manage_users (Create, edit, suspend users)',
                            'configure_system' => 'configure_system (Manage taxes, settings, integrations)',
                            'view_bactrack' => 'view_bactrack (Access and view BACtrack Transactions)'
                        ];
                        foreach ($permissionsList as $key => $desc):
                        ?>
                            <div class="col-12">
                                <div class="form-check form-switch p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <label class="form-check-label fs-8 fw-semibold cursor-pointer mb-0 w-75" for="switch-<?php echo $key; ?>">
                                        <?php echo htmlspecialchars($desc); ?>
                                    </label>
                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[<?php echo $key; ?>]" value="1" id="switch-<?php echo $key; ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT SETTINGS SUBMIT & PERMISSIONS HANDLERS
     ========================================================================= -->
<script>
async function handleSettingsSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('taxSettingsForm');
    const rows = [...document.querySelectorAll('#taxRowsContainer .tax-row')];
    if (rows.length === 0) {
        API.showToast('Please add at least one tax item.', 'danger');
        return;
    }

    const seen = new Set();
    for (const row of rows) {
        const typeInput = row.querySelector('.tax-type-input');
        const taxType = (typeInput?.value || '').trim().toLowerCase();
        if (!taxType) {
            API.showToast('Tax label is required for all rows.', 'danger');
            typeInput?.focus();
            return;
        }
        if (seen.has(taxType)) {
            API.showToast('Tax labels must be unique.', 'danger');
            typeInput?.focus();
            return;
        }
        seen.add(taxType);
    }

    const formData = new FormData();
    formData.append('csrf_token', form.querySelector('input[name="csrf_token"]').value);
    rows.forEach(row => {
        formData.append('tax_type[]', row.querySelector('.tax-type-input').value.trim());
        formData.append('tax_percentage[]', row.querySelector('input[name="tax_percentage[]"]').value);
        formData.append('is_active[]', row.querySelector('.tax-active-input').checked ? '1' : '0');
    });

    API.showSpinner();

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/tax/manage-tax.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });

    const data = await response.json();
    API.hideSpinner();

    if (data.success) {
        API.showToast(data.message, 'success');
    } else {
        API.showToast(data.message || 'Failed to save settings.', 'danger');
    }
}

function addTaxRow() {
    const container = document.getElementById('taxRowsContainer');
    const row = document.createElement('div');
    row.className = 'p-3 border rounded-3 bg-light-subtle tax-row';
    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label fs-9 fw-semibold text-muted">Tax Label</label>
                <input type="text" name="tax_type[]" class="form-control tax-type-input" maxlength="50" required placeholder="e.g. Allowances">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fs-9 fw-semibold text-muted">Rate (%)</label>
                <div class="input-group">
                    <input type="number" name="tax_percentage[]" class="form-control" step="0.01" min="0" max="100" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>
            <div class="col-8 col-md-2">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input tax-active-input" type="checkbox" name="is_active[]" value="1" checked>
                    <label class="form-check-label fs-9 text-muted">Active</label>
                </div>
            </div>
            <div class="col-4 col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTaxRow(this)" title="Remove Tax Row">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

function removeTaxRow(button) {
    const row = button.closest('.tax-row');
    if (!row) return;
    const container = document.getElementById('taxRowsContainer');
    if (container.querySelectorAll('.tax-row').length === 1) {
        API.showToast('At least one tax item must remain.', 'danger');
        return;
    }
    row.remove();
}

// Get default checklist status based on user role matrix
function getDefaultPermissions(role) {
    const defaults = {
        view: false,
        encode: false,
        edit: false,
        approve: false,
        delete: false,
        manage_users: false,
        configure_system: false,
        view_bactrack: false
    };
    
    if (role === 'Super Admin') {
        Object.keys(defaults).forEach(k => defaults[k] = true);
    } else if (role === 'Admin') {
        defaults.view = true;
        defaults.encode = true;
        defaults.edit = true;
        defaults.approve = true;
    } else if (role === 'Accounting Staff') {
        defaults.view = true;
        defaults.encode = true;
        defaults.approve = true;
    } else {
        defaults.view = true; // User (and others) default to view only
    }
    
    return defaults;
}

// Function to open permissions modal
async function openEditPermissionsModal(roleId, roleName) {
    // Fill role info
    document.getElementById('permRoleId').value = roleId;
    document.getElementById('permRoleName').innerText = roleName;
    
    // Pre-check base role default matrix
    const defaults = getDefaultPermissions(roleName);
    Object.keys(defaults).forEach(key => {
        const checkbox = document.getElementById('switch-' + key);
        if (checkbox) {
            checkbox.checked = defaults[key];
        }
    });
    
    API.showSpinner();
    try {
        // Fetch any permissions from database
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/permissions/get-permissions.php?role_id=' + roleId);
        const data = await response.json();
        
        if (data.success && data.permissions) {
            // Apply role settings if they exist in DB
            Object.keys(data.permissions).forEach(key => {
                const checkbox = document.getElementById('switch-' + key);
                if (checkbox) {
                    checkbox.checked = data.permissions[key] === 1;
                }
            });
        }
    } catch (err) {
        console.error("Failed to load role permissions:", err);
    } finally {
        API.hideSpinner();
    }
    
    // Show Modal
    const modalEl = document.getElementById('editPermissionsModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    if (!modalEl.classList.contains('show')) {
        modal.show();
    }
}

// Handle save permissions
async function handlePermissionsSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('editPermissionsForm');
    const formData = new FormData(form);
    
    // Checkbox values not checked are not sent in FormData by default,
    const permissionKeys = ['view', 'encode', 'edit', 'approve', 'delete', 'manage_users', 'configure_system', 'view_bactrack'];
    permissionKeys.forEach(key => {
        const checkbox = document.getElementById('switch-' + key);
        if (checkbox) {
            formData.set(`permissions[${key}]`, checkbox.checked ? '1' : '0');
        }
    });
    
    API.showSpinner();
    
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/permissions/save-permissions.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    
    const data = await response.json();
    API.hideSpinner();
    
    if (data.success) {
        API.showToast(data.message, 'success');
        const modalEl = document.getElementById('editPermissionsModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    } else {
        API.showToast(data.message || 'Failed to save role permissions.', 'danger');
    }
}

// =========================================================================
// COVERAGE CATEGORIES
// =========================================================================
let coverageCategories = [];
let aliasOptions = [];
const CA_FIELD_LABELS = { dateVenue: 'Dates', fundSource: 'Fund', taItinerary: 'TA', activityProposal: 'Proposal', month: 'Month' };
const REIMB_FIELD_LABELS = { dateVenue: 'Dates', taItinerary: 'TA', activityProposal: 'Proposal', communications: 'Comm', utilityBills: 'Utility' };

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function fieldSummaryChips(cat) {
    const labels = cat.transaction_type === 'Cash Advance' ? CA_FIELD_LABELS : REIMB_FIELD_LABELS;
    const chips = [];
    Object.entries(cat.field_config || {}).forEach(([key, enabled]) => {
        if (enabled && labels[key]) {
            chips.push(`<span class="badge bg-light text-secondary border fs-9 me-1">${labels[key]}</span>`);
        }
    });
    return chips.length ? chips.join('') : '<span class="text-muted fs-9">—</span>';
}

function renderCategoryTable(type, tbodyId) {
    const tbody = document.getElementById(tbodyId);
    const rows = coverageCategories.filter(c => c.transaction_type === type);
    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>';
        return;
    }
    tbody.innerHTML = rows.map(cat => `
        <tr>
            <td class="fw-semibold">${escapeHtml(cat.name)}</td>
            <td class="fs-8 text-muted">${escapeHtml(cat.display_label || '—')}</td>
            <td>${fieldSummaryChips(cat)}</td>
            <td><span class="badge bg-secondary-subtle text-secondary">${(cat.documents || []).length}</span></td>
            <td>${cat.is_active ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>'}</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openCategoryModal(${cat.id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                ${cat.is_active ? `<button type="button" class="btn btn-sm btn-outline-warning" onclick="deactivateCategory(${cat.id})">Deactivate</button>` : ''}
            </td>
        </tr>
    `).join('');
}

async function loadCoverageCategories() {
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/categories/list-categories.php');
        const data = await response.json();
        if (!data.success) {
            API.showToast(data.message || 'Failed to load categories.', 'danger');
            return;
        }
        coverageCategories = data.categories || [];
        aliasOptions = data.alias_options || [];
        renderCategoryTable('Cash Advance', 'caCategoriesTableBody');
        renderCategoryTable('Reimbursement', 'reimbCategoriesTableBody');
    } catch (err) {
        console.error(err);
        API.showToast('Failed to load coverage categories.', 'danger');
    }
}

function populateAliasSelect(selectedId = '') {
    const select = document.getElementById('catAliasCategoryId');
    select.innerHTML = '<option value="">None — use own documents</option>';
    aliasOptions.forEach(opt => {
        const label = `${opt.transaction_type}: ${opt.label}`;
        const selected = String(opt.id) === String(selectedId) ? 'selected' : '';
        select.innerHTML += `<option value="${opt.id}" data-tx-type="${escapeHtml(opt.transaction_type)}" ${selected}>${escapeHtml(label)}</option>`;
    });
}

function toggleCategoryFieldConfig() {
    const txType = document.getElementById('catTransactionType').value;
    const caGroup = document.getElementById('caFieldConfigGroup');
    const reimbGroup = document.getElementById('reimbFieldConfigGroup');
    if (txType === 'Cash Advance') {
        caGroup.classList.remove('d-none');
        reimbGroup.classList.add('d-none');
        reimbGroup.querySelectorAll('input').forEach(cb => { cb.checked = false; cb.disabled = true; });
        caGroup.querySelectorAll('input').forEach(cb => { cb.disabled = false; });
    } else {
        reimbGroup.classList.remove('d-none');
        caGroup.classList.add('d-none');
        caGroup.querySelectorAll('input').forEach(cb => { cb.checked = false; cb.disabled = true; });
        reimbGroup.querySelectorAll('input').forEach(cb => { cb.disabled = false; });
    }
}

function addDocumentRow(doc = {}) {
    const container = document.getElementById('documentRowsContainer');
    const row = document.createElement('div');
    row.className = 'border rounded-3 p-2 doc-row';
    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fs-9 text-muted">Document Title</label>
                <input type="text" name="doc_title[]" class="form-control form-control-sm" value="${escapeHtml(doc.title || '')}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-9 text-muted">Section (optional)</label>
                <input type="text" name="doc_section[]" class="form-control form-control-sm" value="${escapeHtml(doc.section_title || '')}">
            </div>
            <div class="col-md-3">
                <label class="form-label fs-9 text-muted">Condition</label>
                <input type="text" name="doc_condition[]" class="form-control form-control-sm" value="${escapeHtml(doc.condition_text || '')}">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="doc_required[]" value="1" ${doc.is_required !== 0 ? 'checked' : ''}>
                    <label class="form-check-label fs-9">Req</label>
                </div>
            </div>
            <div class="col-12 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.doc-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

function resetDocumentRows(docs = []) {
    const container = document.getElementById('documentRowsContainer');
    container.innerHTML = '';
    if (docs.length) {
        docs.forEach(doc => addDocumentRow(doc));
    } else {
        addDocumentRow();
    }
}

function setFieldConfigCheckboxes(txType, fieldConfig = {}) {
    const prefix = txType === 'Cash Advance' ? 'caField_' : 'reimbField_';
    Object.keys(fieldConfig).forEach(key => {
        const cb = document.getElementById(prefix + key);
        if (cb) cb.checked = !!fieldConfig[key];
    });
}

function openCategoryModal(id = null) {
    const form = document.getElementById('editCategoryForm');
    form.reset();
    document.getElementById('catId').value = '';
    document.getElementById('catIsActive').checked = true;
    resetDocumentRows();
    populateAliasSelect();

    let cat = null;
    if (id) {
        cat = coverageCategories.find(c => c.id === id);
    }

    if (cat) {
        document.getElementById('editCategoryModalLabel').textContent = 'Edit Coverage Category';
        document.getElementById('catId').value = cat.id;
        document.getElementById('catTransactionType').value = cat.transaction_type;
        document.getElementById('catSortOrder').value = cat.sort_order;
        document.getElementById('catIsActive').checked = cat.is_active === 1;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catDisplayLabel').value = cat.display_label || '';
        populateAliasSelect(cat.alias_category_id || '');
        document.getElementById('catAliasNote').value = cat.alias_note || '';
        document.getElementById('catAliasSourceLabel').value = cat.alias_source_label || '';
        toggleCategoryFieldConfig();
        setFieldConfigCheckboxes(cat.transaction_type, cat.field_config);
        resetDocumentRows(cat.documents || []);
    } else {
        document.getElementById('editCategoryModalLabel').textContent = 'Add Coverage Category';
        const activePill = document.querySelector('#categoryTypeTabs .nav-link.active');
        if (activePill && activePill.id === 'reimb-cat-tab') {
            document.getElementById('catTransactionType').value = 'Reimbursement';
        }
        toggleCategoryFieldConfig();
    }

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCategoryModal'));
    modal.show();
}

async function handleCategorySave(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('action', 'save');
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
    formData.append('id', document.getElementById('catId').value || '');
    formData.append('transaction_type', document.getElementById('catTransactionType').value);
    formData.append('name', document.getElementById('catName').value.trim());
    formData.append('display_label', document.getElementById('catDisplayLabel').value.trim());
    formData.append('sort_order', document.getElementById('catSortOrder').value || '0');
    formData.append('is_active', document.getElementById('catIsActive').checked ? '1' : '0');

    const aliasSelect = document.getElementById('catAliasCategoryId');
    const selectedOpt = aliasSelect.options[aliasSelect.selectedIndex];
    if (selectedOpt && selectedOpt.value) {
        formData.append('alias_category_id', selectedOpt.value);
        formData.append('alias_transaction_type', selectedOpt.dataset.txType || '');
    }
    formData.append('alias_note', document.getElementById('catAliasNote').value.trim());
    formData.append('alias_source_label', document.getElementById('catAliasSourceLabel').value.trim());

    const txType = document.getElementById('catTransactionType').value;
    const prefix = txType === 'Cash Advance' ? 'caField_' : 'reimbField_';
    const fieldKeys = txType === 'Cash Advance'
        ? Object.keys(CA_FIELD_LABELS)
        : Object.keys(REIMB_FIELD_LABELS);
    fieldKeys.forEach(key => {
        const cb = document.getElementById(prefix + key);
        formData.append(`field_config[${key}]`, cb && cb.checked ? '1' : '0');
    });

    const docRows = [...document.querySelectorAll('#documentRowsContainer .doc-row')];
    docRows.forEach(row => {
        const title = row.querySelector('input[name="doc_title[]"]')?.value?.trim();
        if (!title) return;
        formData.append('doc_title[]', title);
        formData.append('doc_section[]', row.querySelector('input[name="doc_section[]"]')?.value || '');
        formData.append('doc_condition[]', row.querySelector('input[name="doc_condition[]"]')?.value || '');
        formData.append('doc_required[]', row.querySelector('input[name="doc_required[]"]')?.checked ? '1' : '0');
    });

    API.showSpinner();
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/categories/manage-category.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    const data = await response.json();
    API.hideSpinner();

    if (data.success) {
        API.showToast(data.message, 'success');
        bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'))?.hide();
        await loadCoverageCategories();
    } else {
        API.showToast(data.message || 'Failed to save category.', 'danger');
    }
}

async function deactivateCategory(id) {
    const cat = coverageCategories.find(c => c.id === id);
    const name = cat ? cat.name : 'this category';
    if (!confirm(`Deactivate "${name}"? It will be hidden from new submissions but existing transactions are unaffected.`)) {
        return;
    }
    const formData = new FormData();
    formData.append('action', 'deactivate');
    formData.append('id', id);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    API.showSpinner();
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/categories/manage-category.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: formData
    });
    const data = await response.json();
    API.hideSpinner();

    if (data.success) {
        API.showToast(data.message, 'success');
        await loadCoverageCategories();
    } else {
        API.showToast(data.message || 'Failed to deactivate category.', 'danger');
    }
}

document.getElementById('categories-tab')?.addEventListener('shown.bs.tab', loadCoverageCategories);
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('categoriesContent')?.classList.contains('active')) {
        loadCoverageCategories();
    }
});
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
