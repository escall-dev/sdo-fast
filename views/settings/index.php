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
$goodsPercentage = 5.00;
$foodsPercentage = 2.00;
$servicesPercentage = 10.00;

// Fetch users with positions and roles
$users = [];

if ($fastPDO !== null) {
    try {
        $configs = $fastPDO->query("SELECT tax_type, tax_percentage FROM tax_configurations")->fetchAll(PDO::FETCH_KEY_PAIR);
        $goodsPercentage = $configs['Goods'] ?? 5.00;
        $foodsPercentage = $configs['Foods'] ?? 2.00;
        $servicesPercentage = $configs['Services'] ?? 10.00;

        $stmt = $fastPDO->query("
            SELECT u.id, u.full_name, u.position_id, p.position_name, r.role_name 
            FROM users u 
            LEFT JOIN positions p ON u.position_id = p.id 
            LEFT JOIN user_roles ur ON u.id = ur.user_id 
            LEFT JOIN roles r ON ur.role_id = r.id 
            ORDER BY u.full_name ASC
        ");
        $users = $stmt->fetchAll();
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
                    <i class="bi bi-shield-lock me-2"></i>User Access Control
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
                            
                            <!-- Goods Tax Percentage -->
                            <div class="mb-4">
                                <label for="taxGoods" class="form-label fs-8 fw-semibold text-muted">Goods Tax Rate (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-box-seam text-muted"></i></span>
                                    <input type="number" name="tax_goods" id="taxGoods" class="form-control" value="<?php echo htmlspecialchars($goodsPercentage); ?>" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                            </div>

                            <!-- Foods Tax Percentage -->
                            <div class="mb-4">
                                <label for="taxFoods" class="form-label fs-8 fw-semibold text-muted">Foods Tax Rate (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-egg-fried text-muted"></i></span>
                                    <input type="number" name="tax_foods" id="taxFoods" class="form-control" value="<?php echo htmlspecialchars($foodsPercentage); ?>" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                            </div>

                            <!-- Services Tax Percentage -->
                            <div class="mb-4">
                                <label for="taxServices" class="form-label fs-8 fw-semibold text-muted">Services Tax Rate (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person-gear text-muted"></i></span>
                                    <input type="number" name="tax_services" id="taxServices" class="form-control" value="<?php echo htmlspecialchars($servicesPercentage); ?>" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text bg-light">%</span>
                                </div>
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
    
    <!-- Tab 2: User Access Control -->
    <div class="tab-pane fade" id="accessContent" role="tabpanel" aria-labelledby="access-tab">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary-dark">User Access Control</h5>
                <span class="badge bg-light text-muted border">Manage permissions</span>
            </div>
            <div class="card-body">
                <p class="text-muted fs-8 mb-4">Override default role-based permissions on a per-user basis. Saved permissions will apply across all system modules immediately.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>System Role</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No registered users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="fw-semibold text-primary-dark"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['position_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                            $roleBadgeClass = 'bg-light text-dark border';
                                            if ($user['role_name'] === 'Super Admin') {
                                                $roleBadgeClass = 'bg-warning text-dark';
                                            } else if ($user['role_name'] === 'Admin') {
                                                $roleBadgeClass = 'bg-primary text-white';
                                            } else if ($user['role_name'] === 'Accounting Staff') {
                                                $roleBadgeClass = 'bg-info text-white';
                                            }
                                            ?>
                                            <span class="badge <?php echo $roleBadgeClass; ?>">
                                                <?php echo htmlspecialchars($user['role_name'] ?? 'User'); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary px-3" 
                                                    onclick="openEditPermissionsModal(
                                                        <?php echo $user['id']; ?>, 
                                                        '<?php echo addslashes(htmlspecialchars($user['full_name'])); ?>', 
                                                        '<?php echo addslashes(htmlspecialchars($user['position_name'] ?? 'N/A')); ?>', 
                                                        '<?php echo addslashes(htmlspecialchars($user['role_name'] ?? 'User')); ?>'
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
</div>

<!-- =========================================================================
     MODAL: EDIT PERMISSIONS
     ========================================================================= -->
<div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-labelledby="editPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="editPermissionsModalLabel">Edit User Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionsForm" onsubmit="handlePermissionsSubmit(event)">
                <input type="hidden" name="user_id" id="permUserId">
                <div class="modal-body py-3">
                    <!-- User details block -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="row">
                            <div class="col-6">
                                <span class="fs-8 text-muted d-block">User Name</span>
                                <strong id="permUserName" class="text-primary-dark">Name</strong>
                            </div>
                            <div class="col-6">
                                <span class="fs-8 text-muted d-block">Position & Role</span>
                                <strong id="permUserRole" class="text-primary-dark">Position / Role</strong>
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
                            'configure_system' => 'configure_system (Manage taxes, settings, integrations)'
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
    const formData = new FormData(form);

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

// Get default checklist status based on user role matrix
function getDefaultPermissions(role) {
    const defaults = {
        view: false,
        encode: false,
        edit: false,
        approve: false,
        delete: false,
        manage_users: false,
        configure_system: false
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
async function openEditPermissionsModal(userId, fullName, position, role) {
    // Fill user info
    document.getElementById('permUserId').value = userId;
    document.getElementById('permUserName').innerText = fullName;
    document.getElementById('permUserRole').innerText = position + ' (' + role + ')';
    
    // Pre-check base role default matrix
    const defaults = getDefaultPermissions(role);
    Object.keys(defaults).forEach(key => {
        const checkbox = document.getElementById('switch-' + key);
        if (checkbox) {
            checkbox.checked = defaults[key];
        }
    });
    
    API.showSpinner();
    try {
        // Fetch any overrides from database
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/permissions/get-permissions.php?user_id=' + userId);
        const data = await response.json();
        
        if (data.success && data.permissions) {
            // Apply override settings if they exist
            Object.keys(data.permissions).forEach(key => {
                const checkbox = document.getElementById('switch-' + key);
                if (checkbox) {
                    checkbox.checked = data.permissions[key] === 1;
                }
            });
        }
    } catch (err) {
        console.error("Failed to load permissions override:", err);
    } finally {
        API.hideSpinner();
    }
    
    // Show Modal
    const modalEl = document.getElementById('editPermissionsModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Handle save permission overrides
async function handlePermissionsSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('editPermissionsForm');
    const formData = new FormData(form);
    
    // Checkbox values not checked are not sent in FormData by default,
    const permissionKeys = ['view', 'encode', 'edit', 'approve', 'delete', 'manage_users', 'configure_system'];
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
        API.showToast(data.message || 'Failed to save user permissions.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
