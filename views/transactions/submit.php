<?php
/**
 * Transaction Submission Form View for SDO FAST.
 */

$currentPage = 'submit_transaction';
$pageTitle = 'Submit Transaction';
$pageHeader = 'Submit Transaction';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userRole = $_SESSION['user_role'] ?? '';

// Fetch tax configuration keys for dropdown select
$taxConfigurations = [];
if ($fastPDO !== null) {
    try {
        $taxConfigurations = $fastPDO->query("SELECT * FROM tax_configurations WHERE is_active = 1")->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch tax configs: " . $e->getMessage());
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary-dark">New Transaction Submission Form</h5>
            </div>
            <div class="card-body">
                <form id="submitTransactionForm" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <!-- Basic details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="transactionType" class="form-label fs-8 fw-semibold text-muted">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transactionType" class="form-select" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Cash Advance">Cash Advance</option>
                                <option value="Reimbursement">Reimbursement</option>
                                <option value="Payroll">Payroll</option>
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="targetDate" class="form-label fs-8 fw-semibold text-muted">Target Completion Date</label>
                            <input type="date" name="target_date" id="targetDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-12">
                            <label for="eventName" class="form-label fs-8 fw-semibold text-muted">Particulars / Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" id="eventName" class="form-control" placeholder="e.g. SDO Seminar Reimbursement for Math Teachers" required>
                        </div>
                    </div>

                    <!-- Financial and Tax details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="amount" class="form-label fs-8 fw-semibold text-muted">Gross Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" min="1" required oninput="calculateTaxPreview()">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="taxType" class="form-label fs-8 fw-semibold text-muted">Tax Classification <span class="text-danger">*</span></label>
                            <select name="tax_type" id="taxType" class="form-select" required onchange="calculateTaxPreview()">
                                <option value="" disabled selected>Select Tax Type</option>
                                <?php foreach ($taxConfigurations as $config): ?>
                                    <option value="<?php echo htmlspecialchars($config['tax_type']); ?>">
                                        <?php echo htmlspecialchars($config['tax_type']) . " (" . number_format($config['tax_percentage']) . "%)"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Interactive Tax Preview Card -->
                    <div class="mb-4 d-none" id="taxPreviewCard">
                        <span class="fs-8 fw-semibold text-muted text-uppercase d-block mb-1">Financial Calculation Preview</span>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row text-center text-sm-start">
                                <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                    <small class="text-muted d-block">Gross Amount</small>
                                    <span class="fw-semibold text-dark fs-6" id="previewGross">₱0.00</span>
                                </div>
                                <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                    <small class="text-muted d-block">Tax Deduction Amount</small>
                                    <span class="fw-semibold text-danger fs-6" id="previewTax">₱0.00</span>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <small class="text-muted d-block">Estimated Net Payout</small>
                                    <strong class="text-primary-dark fs-5" id="previewNet">₱0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload attachment -->
                    <div class="mb-4">
                        <label for="attachment" class="form-label fs-8 fw-semibold text-muted">Supporting Attachment Document (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                        <small class="text-muted fs-9 d-block mt-1">Accepted formats: PDF, JPG, PNG, DOCX. Max file size: 10MB.</small>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="form-label fs-8 fw-semibold text-muted">Submission Remarks / Notes <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Enter supporting statements, supplier specifics, or DV details..." required></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo env('APP_URL'); ?>/views/dashboard/index.php" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 justify-content-center">Submit Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT LOGIC
     ========================================================================= -->
<script>
async function calculateTaxPreview() {
    const amount = parseFloat(document.getElementById('amount').value);
    const taxType = document.getElementById('taxType').value;
    const previewCard = document.getElementById('taxPreviewCard');

    if (!amount || amount <= 0 || !taxType) {
        previewCard.classList.add('d-none');
        return;
    }

    const payload = new FormData();
    payload.append('amount', amount);
    payload.append('tax_type', taxType);
    payload.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/tax/compute-tax.php', {
        method: 'POST',
        body: payload
    });

    if (response && response.success) {
        document.getElementById('previewGross').innerText = '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('previewTax').innerText = '₱' + parseFloat(response.data.tax_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('previewNet').innerText = '₱' + parseFloat(response.data.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        previewCard.classList.remove('d-none');
    } else {
        previewCard.classList.add('d-none');
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = document.getElementById('submitTransactionForm');
    const formData = new FormData(form);

    API.showSpinner();

    // Call Submit Endpoint
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/transactions/submit-transaction.php', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
        },
        body: formData
    });

    const data = await response.json().catch(() => ({ success: false, message: 'Response parsing failure.' }));
    
    API.hideSpinner();

    if (data.success) {
        API.showToast(data.message, 'success');
        
        // Redirect directly to Tracker timeline page on success!
        setTimeout(() => {
            window.location.href = '<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=' + encodeURIComponent(data.data.tracking_number);
        }, 1500);
    } else {
        API.showToast(data.message || 'Submission failed.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
