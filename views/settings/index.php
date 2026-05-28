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

$userRole = $_SESSION['user_role'] ?? '';

// Double check permission
if ($userRole !== 'Super Admin') {
    $_SESSION['flash_error'] = 'Access denied: System Settings is restricted to Super Admin.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Fetch current configurations
$goodsPercentage = 5.00;
$foodsPercentage = 2.00;
$servicesPercentage = 10.00;

if ($fastPDO !== null) {
    try {
        $configs = $fastPDO->query("SELECT tax_type, tax_percentage FROM tax_configurations")->fetchAll(PDO::FETCH_KEY_PAIR);
        $goodsPercentage = $configs['Goods'] ?? 5.00;
        $foodsPercentage = $configs['Foods'] ?? 2.00;
        $servicesPercentage = $configs['Services'] ?? 10.00;
    } catch (PDOException $e) {
        error_log("Failed to load tax configs for settings: " . $e->getMessage());
    }
}
?>

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

<!-- =========================================================================
     JAVASCRIPT SETTINGS SUBMIT
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
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
