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
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="sysconfigs-tab" data-bs-toggle="tab" data-bs-target="#sysconfigsContent" type="button" role="tab" aria-controls="sysconfigsContent" aria-selected="false">
                    <i class="bi bi-gear me-2"></i>System Configurations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="checklists-tab" data-bs-toggle="tab" data-bs-target="#checklistsContent" type="button" role="tab" aria-controls="checklistsContent" aria-selected="false">
                    <i class="bi bi-card-checklist me-2"></i>Document Checklists
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
    
    <!-- Tab 4: System Configurations -->
    <div class="tab-pane fade" id="sysconfigsContent" role="tabpanel" aria-labelledby="sysconfigs-tab">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold text-primary-dark">System Configurations</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-8 mb-4">Manage global system behaviors and features.</p>
                        
                        <form id="sysconfigsForm" onsubmit="handleSysConfigsSubmit(event)">
                            <div class="list-group mb-4">
                                <!-- BIR 2307 Toggle -->
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Enable BIR 2307 Number Field</h6>
                                        <p class="mb-0 fs-8 text-muted">When active, Accounting Support and Cashiers can input a BIR 2307 Number during document inspection and cash release.</p>
                                    </div>
                                    <div class="form-check form-switch fs-4 mb-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" id="configEnableBirNumber" name="enable_bir_number" value="1">
                                    </div>
                                </div>
                                <!-- Signatory Tracker Toggle -->
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Enable Approval & Signatures in Progress Tracker</h6>
                                        <p class="mb-0 fs-8 text-muted">When active, the "Document for Approval and Signature" section will be displayed in the transaction Progress Tracker.</p>
                                    </div>
                                    <div class="form-check form-switch fs-4 mb-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" id="configEnableSignatoryTracker" name="enable_signatory_tracker" value="1">
                                    </div>
                                </div>
                                <!-- Add other system settings here in the future -->
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">Save Configurations</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold text-primary-dark">Mode of Travel Configurations</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-8 mb-4">Manage the modes of travel available for Travel Reimbursements.</p>
                        
                        <form id="travelModesForm" onsubmit="handleTravelModesSubmit(event)">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fs-8 fw-semibold text-muted mb-0">Travel Modes</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTravelModeRow()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Mode
                                </button>
                            </div>
                            <div id="travelModesContainer" class="d-flex flex-column gap-3 mb-4">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading travel modes...</div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">Save Travel Modes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 5: Document Checklists -->
    <div class="tab-pane fade" id="checklistsContent" role="tabpanel" aria-labelledby="checklists-tab">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 fw-bold text-primary-dark">Document Checklists</h5>
                            <span class="text-muted fs-8">Manage required documents for Submission and Liquidation stages.</span>
                        </div>
                        <button class="btn btn-primary btn-sm px-3" onclick="openChecklistModal()">
                            <i class="bi bi-plus-lg me-1"></i> Add Checklist Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="checklistsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Transaction Type</th>
                                        <th>Category</th>
                                        <th>Stage</th>
                                        <th>Document Title</th>
                                        <th>Required</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Loading checklists...</td></tr>
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
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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
                            <input type="text" name="name" id="catName" class="form-control" maxlength="100" required onkeyup="toggleCategoryFieldConfig()">
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

                    <div id="adminModeFilterContainer" class="mb-3 d-none p-3 bg-light rounded border">
                        <label class="form-label fw-bold text-primary"><i class="bi bi-funnel-fill me-1"></i> Filter by Mode of Travel</label>
                        <p class="fs-9 text-muted mb-2">Select a mode to view and edit its specific documents. Documents must have a mode checked to appear on the submit form.</p>
                        <select id="adminModeFilter" class="form-select form-select-sm" onchange="filterAdminDocuments()">
                            <option value="">-- Select Mode to View/Edit Documents --</option>
                            <option value="UNASSIGNED">-- View Unassigned/Standalone Documents --</option>
                            <option value="GLOBAL">All Modes (documents checked for every mode)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-secondary mb-0">Required Documents</h6>
                        <button type="button" id="btnAddDocument" class="btn btn-sm btn-outline-primary" onclick="addDocumentRow()">
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
     MODAL: EDIT CHECKLIST DOCUMENT ITEM
     ========================================================================= -->
<div class="modal fade" id="editChecklistDocModal" tabindex="-1" aria-labelledby="editChecklistDocModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="editChecklistDocModalLabel">Checklist Document Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editChecklistDocForm" onsubmit="handleChecklistDocSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="chkDocId">
                    <input type="hidden" id="chkOriginalCatId">
                    
                    <div class="mb-3">
                        <label class="form-label fs-8 fw-semibold">Coverage Category <span class="text-danger">*</span></label>
                        <select id="chkCategoryId" class="form-select" required onchange="toggleChecklistModesOfTravel()">
                            <option value="">-- Select Category --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fs-8 fw-semibold">Document Title <span class="text-danger">*</span></label>
                        <input type="text" id="chkTitle" class="form-control" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 fw-semibold">Section (optional)</label>
                        <input type="text" id="chkSectionTitle" class="form-control" maxlength="255" placeholder="e.g. Mandatory Documents">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-8 fw-semibold">Stage</label>
                            <select id="chkStage" class="form-select">
                                <option value="submission">Submission</option>
                                <option value="liquidation">Liquidation</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="chkIsRequired" checked>
                                <label class="form-check-label fs-8 fw-semibold" for="chkIsRequired">Required</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-8 fw-semibold">Condition (optional)</label>
                        <input type="text" id="chkConditionText" class="form-control" maxlength="255" placeholder="e.g. If amount exceeds 10,000">
                    </div>

                    <div class="mb-3 d-none" id="chkModesOfTravelContainer">
                        <label class="form-label fs-8 fw-semibold">Modes of Travel</label>
                        <div class="d-flex flex-wrap gap-2" id="chkModesOfTravelCheckboxGroup">
                            <!-- Dynamically populated checkboxes -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Checklist Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT SETTINGS SUBMIT & PERMISSIONS HANDLERS
     ========================================================================= -->
<script>
// System Configurations
async function loadSystemConfigurations() {
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/settings/manage-system-settings.php');
        const data = await response.json();
        if (data.success && data.settings) {
            const birSetting = data.settings['enable_bir_number'];
            if (birSetting) {
                document.getElementById('configEnableBirNumber').checked = (birSetting.setting_value === '1');
            }
            const signatorySetting = data.settings['enable_signatory_tracker'];
            if (signatorySetting) {
                document.getElementById('configEnableSignatoryTracker').checked = (signatorySetting.setting_value === '1');
            }
        }
    } catch (err) {
        console.error('Failed to load system configs:', err);
    }
}

async function handleSysConfigsSubmit(e) {
    e.preventDefault();
    API.showSpinner();
    
    const enableBir = document.getElementById('configEnableBirNumber').checked ? '1' : '0';
    const enableSignatory = document.getElementById('configEnableSignatoryTracker').checked ? '1' : '0';
    
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/settings/manage-system-settings.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: JSON.stringify({
                settings: {
                    'enable_bir_number': enableBir,
                    'enable_signatory_tracker': enableSignatory
                }
            })
        });
        
        const data = await response.json();
        API.hideSpinner();
        
        if (data.success) {
            API.showToast(data.message, 'success');
        } else {
            API.showToast(data.message || 'Failed to save configurations.', 'danger');
        }
    } catch (err) {
        API.hideSpinner();
        API.showToast('Network error while saving configurations.', 'danger');
    }
}

// Load configurations on initialization
let globalTravelModes = [];

document.addEventListener('DOMContentLoaded', () => {
    loadSystemConfigurations();
    loadTravelModes();
});

async function loadTravelModes() {
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/settings/manage-travel-modes.php');
        const data = await response.json();
        
        const container = document.getElementById('travelModesContainer');
        container.innerHTML = '';
        
        if (data.success && data.modes && data.modes.length > 0) {
            globalTravelModes = data.modes;
            data.modes.forEach(mode => addTravelModeRow(mode));
            updateAdminModeFilterDropdown();
        } else {
            globalTravelModes = [];
            addTravelModeRow();
            updateAdminModeFilterDropdown();
        }
    } catch (err) {
        console.error('Failed to load travel modes:', err);
        const container = document.getElementById('travelModesContainer');
        container.innerHTML = '<div class="alert alert-danger p-2 fs-8 mb-0">Failed to load travel modes.</div>';
    }
}

function addTravelModeRow(mode = {}) {
    const container = document.getElementById('travelModesContainer');
    const row = document.createElement('div');
    row.className = 'p-3 border rounded-3 bg-light-subtle travel-mode-row';
    const isChecked = mode.is_active !== undefined ? (mode.is_active == 1 ? 'checked' : '') : 'checked';
    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-8">
                <label class="form-label fs-9 fw-semibold text-muted">Mode Name</label>
                <input type="text" name="mode_name[]" class="form-control mode-name-input" maxlength="100" required value="${escapeHtml(mode.name || '')}" placeholder="e.g. Plane (airfare)">
            </div>
            <div class="col-8 col-md-3">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input mode-active-input" type="checkbox" name="is_active[]" value="1" ${isChecked}>
                    <label class="form-check-label fs-9 text-muted">Active</label>
                </div>
            </div>
            <div class="col-4 col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTravelModeRow(this)" title="Remove Mode">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

function removeTravelModeRow(button) {
    const row = button.closest('.travel-mode-row');
    if (!row) return;
    const container = document.getElementById('travelModesContainer');
    if (container.querySelectorAll('.travel-mode-row').length === 1) {
        API.showToast('At least one travel mode must remain.', 'danger');
        return;
    }
    row.remove();
}

async function handleTravelModesSubmit(e) {
    e.preventDefault();
    const rows = [...document.querySelectorAll('#travelModesContainer .travel-mode-row')];
    if (rows.length === 0) {
        API.showToast('Please add at least one travel mode.', 'danger');
        return;
    }

    const seen = new Set();
    for (const row of rows) {
        const typeInput = row.querySelector('.mode-name-input');
        const modeName = (typeInput?.value || '').trim().toLowerCase();
        if (!modeName) {
            API.showToast('Mode name is required for all rows.', 'danger');
            typeInput?.focus();
            return;
        }
        if (seen.has(modeName)) {
            API.showToast('Mode names must be unique.', 'danger');
            typeInput?.focus();
            return;
        }
        seen.add(modeName);
    }

    const formData = new FormData();
    const token = document.querySelector('#taxSettingsForm input[name="csrf_token"]');
    if(token) {
        formData.append('csrf_token', token.value);
    } else {
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
    }
    
    rows.forEach(row => {
        formData.append('mode_name[]', row.querySelector('.mode-name-input').value.trim());
        formData.append('is_active[]', row.querySelector('.mode-active-input').checked ? '1' : '0');
    });

    API.showSpinner();

    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/settings/manage-travel-modes.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        API.hideSpinner();

        if (data.success) {
            API.showToast(data.message, 'success');
            loadTravelModes(); // Reload to refresh global array
        } else {
            API.showToast(data.message || 'Failed to save travel modes.', 'danger');
        }
    } catch (err) {
        API.hideSpinner();
        API.showToast('Network error while saving travel modes.', 'danger');
    }
}


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
                ${cat.is_active ? `<button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="deactivateCategory(${cat.id})">Deactivate</button>` : ''}
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCoverageCategory(${cat.id})">
                    <i class="bi bi-trash"></i> Delete
                </button>
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
        if (typeof renderChecklistsTable === 'function') {
            renderChecklistsTable();
        }
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
    
    // Toggle Admin Mode Filter visibility for Reimbursement -> Travel
    const catName = document.getElementById('catName').value.trim();
    if (txType === 'Reimbursement' && catName === 'Travel') {
        document.getElementById('adminModeFilterContainer').classList.remove('d-none');
    } else {
        document.getElementById('adminModeFilterContainer').classList.add('d-none');
    }
    filterAdminDocuments();
}

function filterAdminDocuments() {
    const filterContainer = document.getElementById('adminModeFilterContainer');
    const filterSelect = document.getElementById('adminModeFilter');
    const btnAddDocument = document.getElementById('btnAddDocument');
    const docRowsContainer = document.getElementById('documentRowsContainer');
    const isFilterVisible = !filterContainer.classList.contains('d-none');
    
    if (isFilterVisible) {
        const mode = filterSelect.value;
        if (!mode) {
            docRowsContainer.style.display = 'none';
            btnAddDocument.disabled = true;
            return;
        } else {
            docRowsContainer.style.display = 'flex';
            btnAddDocument.disabled = false;
        }
        
        const docRows = docRowsContainer.querySelectorAll('.doc-row');
        const totalModes = globalTravelModes.filter(m => m.is_active == 1).length; // Total number of active mode checkboxes
        docRows.forEach(row => {
            const checkboxes = row.querySelectorAll('.doc-mode-cb:checked');
            const checkedModes = Array.from(checkboxes).map(cb => cb.value);
            
            if (mode === 'GLOBAL') {
                // Show documents that have ALL modes checked (universal docs)
                if (checkedModes.length === totalModes) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            } else if (mode === 'UNASSIGNED') {
                // Show documents that have NO modes checked
                if (checkedModes.length === 0) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            } else {
                if (checkedModes.includes(mode)) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            }
        });
    } else {
        docRowsContainer.style.display = 'flex';
        btnAddDocument.disabled = false;
        const docRows = docRowsContainer.querySelectorAll('.doc-row');
        docRows.forEach(row => row.classList.remove('d-none'));
    }
}

function addDocumentRow(doc = {}, isNew = false) {
    const container = document.getElementById('documentRowsContainer');
    const row = document.createElement('div');
    row.className = 'border rounded-3 p-2 doc-row';
    const allModes = globalTravelModes.filter(m => m.is_active == 1).map(m => m.name);
    let selectedModes = doc.modes_of_travel || doc.modesOfTravel || [];
    
    const filterContainer = document.getElementById('adminModeFilterContainer');
    if (isNew && filterContainer && !filterContainer.classList.contains('d-none')) {
        const mode = document.getElementById('adminModeFilter').value;
        if (mode && mode !== 'GLOBAL') {
            selectedModes = [mode];
        }
    }
    const modeCheckboxes = allModes.map(m => {
        const sel = selectedModes.includes(m) ? 'checked' : '';
        return `
            <div class="form-check form-check-inline mb-1">
                <input class="form-check-input doc-mode-cb" type="checkbox" value="${escapeHtml(m)}" ${sel}>
                <label class="form-check-label fs-9">${escapeHtml(m)}</label>
            </div>
        `;
    }).join('');

    const txType = document.getElementById('catTransactionType').value;
    const catName = document.getElementById('catName').value.trim();
    const isTravelCategory = (txType === 'Reimbursement' && catName === 'Travel');

    const modeHTML = isTravelCategory ? `
            <div class="col-md-11 mt-2">
                <label class="form-label fs-9 text-muted mb-1 d-block">Modes of Travel</label>
                <div class="d-flex flex-wrap gap-2">
                    ${modeCheckboxes}
                </div>
            </div>
    ` : '';

    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fs-9 text-muted">Document Title</label>
                <input type="text" name="doc_title[]" class="form-control form-control-sm" value="${escapeHtml(doc.title || '')}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9 text-muted">Section (optional)</label>
                <input type="text" name="doc_section[]" class="form-control form-control-sm" value="${escapeHtml(doc.section_title || '')}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9 text-muted">Stage</label>
                <select name="doc_stage[]" class="form-select form-select-sm">
                    <option value="submission" ${doc.stage === 'submission' || !doc.stage ? 'selected' : ''}>Submission</option>
                    <option value="liquidation" ${doc.stage === 'liquidation' ? 'selected' : ''}>Liquidation</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fs-9 text-muted">Condition</label>
                <input type="text" name="doc_condition[]" class="form-control form-control-sm" value="${escapeHtml(doc.condition_text || '')}">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="doc_required[]" value="1" ${doc.is_required !== 0 ? 'checked' : ''}>
                    <label class="form-check-label fs-9">Req</label>
                </div>
            </div>
            ${modeHTML}
            <div class="col-md-1 text-end mt-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocumentRow(this)"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    `;
    container.appendChild(row);
    // Scroll the modal body to show the newly added row
    const modalBody = container.closest('.modal-body');
    if (modalBody) {
        setTimeout(() => {
            row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 50);
    }
}

function resetDocumentRows(docs = []) {
    const container = document.getElementById('documentRowsContainer');
    container.innerHTML = '';
    if (docs.length) {
        docs.forEach(doc => addDocumentRow(doc));
    } else {
        addDocumentRow({}, true);
    }
}

async function removeDocumentRow(btn) {
    const row = btn.closest('.doc-row');
    const titleInput = row.querySelector('input[name="doc_title[]"]');
    const title = titleInput && titleInput.value.trim() ? titleInput.value.trim() : 'this document';
    
    const confirmed = await API.confirmAction(
        'Delete Document?',
        `Are you sure you want to remove "${title}" from the required documents?`,
        'Yes, remove it',
        'danger'
    );
    
    if (confirmed) {
        row.remove();
        API.showToast(`Removed "${title}". Make sure to click Save to apply changes.`, 'info');
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
    
    // Reset admin mode filter dropdown logic
    document.getElementById('adminModeFilter').value = '';
    filterAdminDocuments();

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCategoryModal'));
    modal.show();
}

function updateAdminModeFilterDropdown() {
    const select = document.getElementById('adminModeFilter');
    if (!select) return;
    const existingVal = select.value;
    select.innerHTML = `
        <option value="">-- Select Mode to View/Edit Documents --</option>
        <option value="UNASSIGNED">-- View Unassigned/Standalone Documents --</option>
        <option value="GLOBAL">All Modes (documents checked for every mode)</option>
    `;
    globalTravelModes.forEach(mode => {
        if (mode.is_active == 1) {
            const opt = document.createElement('option');
            opt.value = mode.name;
            opt.textContent = mode.name;
            select.appendChild(opt);
        }
    });
    // Try to restore previous selection
    select.value = existingVal;
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
        formData.append('doc_stage[]', row.querySelector('select[name="doc_stage[]"]')?.value || 'submission');
        formData.append('doc_condition[]', row.querySelector('input[name="doc_condition[]"]')?.value || '');
        formData.append('doc_required[]', row.querySelector('input[name="doc_required[]"]')?.checked ? '1' : '0');

        const modeCheckboxes = row.querySelectorAll('.doc-mode-cb:checked');
        let selectedModes = [];
        if (modeCheckboxes.length > 0) {
            selectedModes = Array.from(modeCheckboxes).map(cb => cb.value);
        }
        formData.append('doc_modes_of_travel[]', selectedModes.length ? JSON.stringify(selectedModes) : '');
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
    
    const confirmed = await API.confirmAction(
        'Deactivate Category?',
        `Deactivate "${name}"? It will be hidden from new submissions but existing transactions are unaffected.`,
        'Yes, deactivate',
        'warning'
    );
    
    if (!confirmed) {
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

async function deleteCoverageCategory(id) {
    const cat = coverageCategories.find(c => c.id === id);
    const name = cat ? cat.name : 'this category';
    
    const confirmed = await API.confirmAction(
        'Delete Category?',
        `Are you sure you want to completely delete "${name}"? This will permanently remove the category and its documents.`,
        'Yes, delete it',
        'danger'
    );
    
    if (!confirmed) {
        return;
    }
    const formData = new FormData();
    formData.append('action', 'delete');
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
        API.showToast(data.message || 'Failed to delete category.', 'danger');
    }
}

// =========================================================================
// DOCUMENT CHECKLISTS (TAB 5)
// =========================================================================
function renderChecklistsTable() {
    const tbody = document.querySelector('#checklistsTable tbody');
    if (!tbody) return;
    
    let rowsHtml = '';
    let totalDocsCount = 0;
    
    coverageCategories.forEach(cat => {
        const docs = cat.documents || [];
        docs.forEach(doc => {
            totalDocsCount++;
            rowsHtml += `
                <tr>
                    <td class="ps-4 fw-semibold">${escapeHtml(cat.transaction_type)}</td>
                    <td>${escapeHtml(cat.name)}</td>
                    <td><span class="badge ${doc.stage === 'liquidation' ? 'bg-warning text-dark' : 'bg-info text-white'}">${escapeHtml(doc.stage || 'submission')}</span></td>
                    <td>${escapeHtml(doc.title)}</td>
                    <td>
                        ${doc.is_required ? '<span class="badge bg-danger-subtle text-danger">Required</span>' : '<span class="badge bg-light text-secondary border">Optional</span>'}
                    </td>
                    <td>
                        ${cat.is_active ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>'}
                    </td>
                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editChecklistDoc(${cat.id}, ${doc.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteChecklistDoc(${cat.id}, ${doc.id})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
        });
    });
    
    if (totalDocsCount === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No checklist items found.</td></tr>';
    } else {
        tbody.innerHTML = rowsHtml;
    }
}

function openChecklistModal(catId = null, docId = null) {
    const form = document.getElementById('editChecklistDocForm');
    form.reset();
    
    // Populate categories select
    const select = document.getElementById('chkCategoryId');
    select.innerHTML = '<option value="">-- Select Category --</option>';
    coverageCategories.forEach(cat => {
        if (cat.is_active) {
            select.innerHTML += `<option value="${cat.id}">${escapeHtml(cat.transaction_type)}: ${escapeHtml(cat.name)}</option>`;
        }
    });

    // Populate travel modes checkboxes
    const modesContainer = document.getElementById('chkModesOfTravelCheckboxGroup');
    modesContainer.innerHTML = '';
    const activeModes = globalTravelModes.filter(m => m.is_active == 1);
    activeModes.forEach(mode => {
        modesContainer.innerHTML += `
            <div class="form-check form-check-inline mb-1">
                <input class="form-check-input chk-mode-cb" type="checkbox" value="${escapeHtml(mode.name)}" id="chkMode_${escapeHtml(mode.name)}">
                <label class="form-check-label fs-9" for="chkMode_${escapeHtml(mode.name)}">${escapeHtml(mode.name)}</label>
            </div>
        `;
    });

    document.getElementById('chkDocId').value = docId || '';
    document.getElementById('chkOriginalCatId').value = catId || '';
    document.getElementById('chkIsRequired').checked = true;
    document.getElementById('chkModesOfTravelContainer').classList.add('d-none');

    if (catId && docId) {
        // Edit mode
        document.getElementById('editChecklistDocModalLabel').innerText = 'Edit Checklist Document Item';
        document.getElementById('chkCategoryId').value = catId;
        document.getElementById('chkCategoryId').disabled = true;
        
        const cat = coverageCategories.find(c => c.id == catId);
        if (cat) {
            const doc = (cat.documents || []).find(d => d.id == docId);
            if (doc) {
                document.getElementById('chkTitle').value = doc.title || '';
                document.getElementById('chkSectionTitle').value = doc.section_title || '';
                document.getElementById('chkStage').value = doc.stage || 'submission';
                document.getElementById('chkIsRequired').checked = doc.is_required !== 0;
                document.getElementById('chkConditionText').value = doc.condition_text || '';
                
                // Show modes of travel if applicable
                if (cat.transaction_type === 'Reimbursement' && cat.name === 'Travel') {
                    document.getElementById('chkModesOfTravelContainer').classList.remove('d-none');
                    const selectedModes = doc.modes_of_travel || [];
                    selectedModes.forEach(m => {
                        const cb = document.getElementById(`chkMode_${m}`);
                        if (cb) cb.checked = true;
                    });
                }
            }
        }
    } else {
        // Add mode
        document.getElementById('editChecklistDocModalLabel').innerText = 'Add Checklist Document Item';
        document.getElementById('chkCategoryId').disabled = false;
    }

    const modalEl = document.getElementById('editChecklistDocModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function toggleChecklistModesOfTravel() {
    const catId = document.getElementById('chkCategoryId').value;
    const container = document.getElementById('chkModesOfTravelContainer');
    if (!catId) {
        container.classList.add('d-none');
        return;
    }
    const cat = coverageCategories.find(c => c.id == catId);
    if (cat && cat.transaction_type === 'Reimbursement' && cat.name === 'Travel') {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

async function handleChecklistDocSubmit(e) {
    e.preventDefault();
    const docId = document.getElementById('chkDocId').value;
    const catId = document.getElementById('chkCategoryId').value;
    
    if (!catId) {
        API.showToast('Please select a category.', 'danger');
        return;
    }
    
    const cat = coverageCategories.find(c => c.id == catId);
    if (!cat) {
        API.showToast('Category not found.', 'danger');
        return;
    }
    
    // Prepare the document object
    const title = document.getElementById('chkTitle').value.trim();
    const sectionTitle = document.getElementById('chkSectionTitle').value.trim();
    const stage = document.getElementById('chkStage').value;
    const isRequired = document.getElementById('chkIsRequired').checked ? 1 : 0;
    const conditionText = document.getElementById('chkConditionText').value.trim();
    
    let modesOfTravel = null;
    if (cat.transaction_type === 'Reimbursement' && cat.name === 'Travel') {
        const checkedCbs = document.querySelectorAll('#chkModesOfTravelCheckboxGroup .chk-mode-cb:checked');
        modesOfTravel = Array.from(checkedCbs).map(cb => cb.value);
    }
    
    const docObj = {
        id: docId ? parseInt(docId) : null,
        title,
        section_title: sectionTitle,
        stage,
        is_required: isRequired,
        condition_text: conditionText,
        modes_of_travel: modesOfTravel
    };

    let updatedDocs = [...(cat.documents || [])];
    if (docId) {
        // Edit mode
        const idx = updatedDocs.findIndex(d => d.id == docId);
        if (idx !== -1) {
            updatedDocs[idx] = docObj;
        }
    } else {
        // Add mode
        updatedDocs.push(docObj);
    }

    // Hide modal
    bootstrap.Modal.getInstance(document.getElementById('editChecklistDocModal'))?.hide();
    
    // Save
    await saveChecklistCategoryDocs(cat, updatedDocs);
}

async function saveChecklistCategoryDocs(cat, updatedDocs) {
    const formData = new FormData();
    formData.append('action', 'save');
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
    formData.append('id', cat.id);
    formData.append('transaction_type', cat.transaction_type);
    formData.append('name', cat.name);
    formData.append('display_label', cat.display_label || '');
    formData.append('sort_order', cat.sort_order || '0');
    formData.append('is_active', cat.is_active ? '1' : '0');
    
    if (cat.alias_category_id) {
        formData.append('alias_category_id', cat.alias_category_id);
        formData.append('alias_transaction_type', cat.alias_transaction_type || '');
    }
    formData.append('alias_note', cat.alias_note || '');
    formData.append('alias_source_label', cat.alias_source_label || '');

    // Field Config
    const fieldKeys = cat.transaction_type === 'Cash Advance'
        ? Object.keys(CA_FIELD_LABELS)
        : Object.keys(REIMB_FIELD_LABELS);
    fieldKeys.forEach(key => {
        const val = cat.field_config && cat.field_config[key] ? '1' : '0';
        formData.append(`field_config[${key}]`, val);
    });

    // Documents
    updatedDocs.forEach((doc, idx) => {
        formData.append('doc_title[]', doc.title.trim());
        formData.append('doc_section[]', doc.section_title || '');
        formData.append('doc_stage[]', doc.stage || 'submission');
        formData.append('doc_condition[]', doc.condition_text || '');
        formData.append('doc_required[]', doc.is_required ? '1' : '0');
        
        let modes = '';
        if (doc.modes_of_travel && doc.modes_of_travel.length > 0) {
            modes = JSON.stringify(doc.modes_of_travel);
        }
        formData.append('doc_modes_of_travel[]', modes);
    });

    API.showSpinner();
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/categories/manage-category.php', {
            method: 'POST',
            headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            API.showToast(data.message, 'success');
            await loadCoverageCategories();
        } else {
            API.showToast(data.message || 'Failed to save checklist item.', 'danger');
        }
    } catch (err) {
        console.error(err);
        API.showToast('Network error while saving checklist item.', 'danger');
    } finally {
        API.hideSpinner();
    }
}

async function deleteChecklistDoc(catId, docId) {
    const cat = coverageCategories.find(c => c.id == catId);
    if (!cat) return;
    
    const doc = (cat.documents || []).find(d => d.id == docId);
    if (!doc) return;
    
    const confirmed = await API.confirmAction(
        'Delete Checklist Item?',
        `Are you sure you want to delete "${doc.title}"?`,
        'Yes, delete',
        'danger'
    );
    
    if (!confirmed) return;
    
    const updatedDocs = (cat.documents || []).filter(d => d.id != docId);
    await saveChecklistCategoryDocs(cat, updatedDocs);
}

// Bind to window for HTML inline event handlers
window.openChecklistModal = openChecklistModal;
window.editChecklistDoc = openChecklistModal;
window.deleteChecklistDoc = deleteChecklistDoc;
window.toggleChecklistModesOfTravel = toggleChecklistModesOfTravel;
window.handleChecklistDocSubmit = handleChecklistDocSubmit;

document.getElementById('categories-tab')?.addEventListener('shown.bs.tab', loadCoverageCategories);
document.getElementById('checklists-tab')?.addEventListener('shown.bs.tab', loadCoverageCategories);
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('categoriesContent')?.classList.contains('active') ||
        document.getElementById('checklistsContent')?.classList.contains('active')) {
        loadCoverageCategories();
    }
});
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
