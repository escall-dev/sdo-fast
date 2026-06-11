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
                    
                    <!-- Cash Advance Sub-options (Hidden by default, shown when Cash Advance is selected) -->
                    <div id="cashAdvanceCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cashAdvanceCategory" class="form-label fs-8 fw-semibold text-muted">Cash Advance Category <span class="text-danger">*</span></label>
                                <select name="cash_advance_category" id="cashAdvanceCategory" class="form-select">
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="MOOE">MOOE (Travel Cash Advance)</option>
                                    <option value="Activity">Activity (Seminar/Training Proposal)</option>
                                </select>
                            </div>
                        </div>

                        <!-- MOOE Form Fields -->
                        <div id="mooeFieldsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-airplane-engines me-1"></i>MOOE Travel Cash Advance Details</h6>
                                
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
                                    <div><strong>Note:</strong> For Cash Advance (MOOE), the attached documents you must upload below are your <strong>Approved TA (Travel Authority)</strong> and <strong>Travel Itinerary</strong>.</div>
                                </div>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="mooeStartDate" class="form-label fs-8 fw-semibold text-muted">Inclusive Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_start_date" id="mooeStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="mooeEndDate" class="form-label fs-8 fw-semibold text-muted">Inclusive End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_end_date" id="mooeEndDate" class="form-control">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="fundSource" class="form-label fs-8 fw-semibold text-muted">Fund Source <span class="text-danger">*</span></label>
                                        <input type="text" name="fund_source" id="fundSource" class="form-control" placeholder="e.g. School MOOE, Division MOOE, SEF">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="mooeVenue" class="form-label fs-8 fw-semibold text-muted">Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="venue" id="mooeVenue" class="form-control" placeholder="e.g. Regional Office, Hotel Venue Name">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="approvedTa" class="form-label fs-8 fw-semibold text-muted">Upload Approved TA (Travel Authority) <span class="text-danger">*</span></label>
                                        <input type="file" name="approved_ta" id="approvedTa" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="travelItinerary" class="form-label fs-8 fw-semibold text-muted">Upload Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="travel_itinerary" id="travelItinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Form Fields -->
                        <div id="activityFieldsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-journal-check me-1"></i>Activity Seminar/Training Details</h6>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="activityStartDate" class="form-label fs-8 fw-semibold text-muted">Activity Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="activity_start_date" id="activityStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="activityEndDate" class="form-label fs-8 fw-semibold text-muted">Activity End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="activity_end_date" id="activityEndDate" class="form-control">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="activityVenue" class="form-label fs-8 fw-semibold text-muted">Activity Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="activity_venue" id="activityVenue" class="form-control" placeholder="e.g. SDO Conference Hall, School Gym">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="activityProposal" class="form-label fs-8 fw-semibold text-muted">Upload Activity Proposal <span class="text-danger">*</span></label>
                                        <input type="file" name="activity_proposal" id="activityProposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reimbursement Sub-options (Hidden by default, shown when Reimbursement is selected) -->
                    <div id="reimbursementCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reimbursementCategory" class="form-label fs-8 fw-semibold text-muted">Reimbursement Category <span class="text-danger">*</span></label>
                                <select name="reimbursement_category" id="reimbursementCategory" class="form-select">
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Communications Allowance">Communications Allowance</option>
                                    <option value="Procured Goods">Procured Goods</option>
                                </select>
                            </div>
                        </div>

                        <!-- Communications Allowance Fields -->
                        <div id="communicationsAllowanceFieldsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-telephone-inbound me-1"></i>Communications Allowance Details</h6>
                                
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
                                    <div><strong>Note:</strong> All three documents (DTR, Certificate, and Bill / Proof of Payment) are strictly required.</div>
                                </div>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbursementMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="reimbursement_month" id="reimbursementMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
                                            $currentYear = (int)date('Y');
                                            $prevYear = $currentYear - 1;
                                            $monthsList = [
                                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                            ];
                                            for ($y = $currentYear; $y >= $prevYear; $y--) {
                                                for ($m = 12; $m >= 1; $m--) {
                                                    $mLabel = $monthsList[$m] . ' ' . $y;
                                                    echo '<option value="' . htmlspecialchars($mLabel) . '">' . htmlspecialchars($mLabel) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbDtr" class="form-label fs-8 fw-semibold text-muted">Upload DTR <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_dtr" id="reimbDtr" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbCertificate" class="form-label fs-8 fw-semibold text-muted">Upload Certificate <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_certificate" id="reimbCertificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbBillProof" class="form-label fs-8 fw-semibold text-muted">Upload Bill / Proof of Payment <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_bill_proof" id="reimbBillProof" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
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
                        <label class="form-label fs-8 fw-semibold text-muted">Supporting Attachment Document(s) <span class="text-muted fw-normal">(Optional)</span></label>
                        
                        <!-- Drag and Drop Area -->
                        <div id="dropzone" class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="cursor: pointer; transition: background-color 0.2s, border-color 0.2s;">
                            <input type="file" name="attachment[]" id="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.jpg,.jpeg,.png,.docx" multiple style="cursor: pointer; z-index: 10;">
                            <div class="dz-message">
                                <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary mb-2 d-block"></i>
                                <span class="fw-bold text-dark d-block">Drag & Drop files here or click to upload</span>
                                <span class="text-muted fs-9">Accepts PDF, JPG, PNG, DOCX (Max 10MB per file)</span>
                            </div>
                        </div>

                        <!-- Selected Files List -->
                        <div id="fileListContainer" class="mt-3 d-none">
                            <span class="fs-9 fw-semibold text-muted text-uppercase d-block mb-2">Selected Attachment(s):</span>
                            <div class="list-group list-group-flush border rounded-3 overflow-hidden bg-white shadow-sm" id="selectedFilesList">
                                <!-- Dynamically loaded files -->
                            </div>
                        </div>
                        
                        <small class="text-muted fs-9 d-block mt-1">Upload any general supporting document(s) if applicable.</small>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="form-label fs-8 fw-semibold text-muted">Submission Remarks / Notes (Optional)</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Enter supporting statements, supplier specifics, or DV details..."></textarea>
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
document.addEventListener('DOMContentLoaded', function() {
    const txTypeSelect = document.getElementById('transactionType');
    const caCategorySelect = document.getElementById('cashAdvanceCategory');
    const caSection = document.getElementById('cashAdvanceCategorySection');
    const mooeContainer = document.getElementById('mooeFieldsContainer');
    const activityContainer = document.getElementById('activityFieldsContainer');
    const reimbCategorySelect = document.getElementById('reimbursementCategory');
    const reimbSection = document.getElementById('reimbursementCategorySection');
    const commAllowanceContainer = document.getElementById('communicationsAllowanceFieldsContainer');

    function toggleFormFields() {
        const txType = txTypeSelect.value;
        const caCat = caCategorySelect.value;
        const reimbCat = reimbCategorySelect.value;

        // Cash Advance toggle
        if (txType === 'Cash Advance') {
            caSection.classList.remove('d-none');
            caCategorySelect.disabled = false;
            caCategorySelect.required = true;

            if (caCat === 'MOOE') {
                mooeContainer.classList.remove('d-none');
                activityContainer.classList.add('d-none');

                setFieldsState(mooeContainer, true, true);
                setFieldsState(activityContainer, false, false);
            } else if (caCat === 'Activity') {
                activityContainer.classList.remove('d-none');
                mooeContainer.classList.add('d-none');

                setFieldsState(activityContainer, true, true);
                setFieldsState(mooeContainer, false, false);
            } else {
                mooeContainer.classList.add('d-none');
                activityContainer.classList.add('d-none');
                setFieldsState(mooeContainer, false, false);
                setFieldsState(activityContainer, false, false);
            }
        } else {
            caSection.classList.add('d-none');
            caCategorySelect.disabled = true;
            caCategorySelect.required = false;
            mooeContainer.classList.add('d-none');
            activityContainer.classList.add('d-none');
            setFieldsState(mooeContainer, false, false);
            setFieldsState(activityContainer, false, false);
        }

        // Reimbursement toggle
        if (txType === 'Reimbursement') {
            reimbSection.classList.remove('d-none');
            reimbCategorySelect.disabled = false;
            reimbCategorySelect.required = true;

            if (reimbCat === 'Communications Allowance') {
                commAllowanceContainer.classList.remove('d-none');
                setFieldsState(commAllowanceContainer, true, true);
            } else {
                commAllowanceContainer.classList.add('d-none');
                setFieldsState(commAllowanceContainer, false, false);
            }
        } else {
            reimbSection.classList.add('d-none');
            reimbCategorySelect.disabled = true;
            reimbCategorySelect.required = false;
            commAllowanceContainer.classList.add('d-none');
            setFieldsState(commAllowanceContainer, false, false);
        }
    }

    function setFieldsState(container, enabled, required) {
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !enabled;
            input.required = required;
        });
    }

    txTypeSelect.addEventListener('change', toggleFormFields);
    caCategorySelect.addEventListener('change', toggleFormFields);
    reimbCategorySelect.addEventListener('change', toggleFormFields);
    
    // Initial call
    toggleFormFields();

    // Drag and drop dropzone handlers
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('attachment');
    const fileListContainer = document.getElementById('fileListContainer');
    const selectedFilesList = document.getElementById('selectedFilesList');
    let selectedFiles = [];

    if (dropzone && fileInput) {
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-primary-subtle', 'border-primary');
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                dropzone.classList.remove('bg-primary-subtle', 'border-primary');
            });
        });

        fileInput.addEventListener('change', updateFileList);
    }

    function updateFileList() {
        const files = fileInput.files;
        if (files.length > 0) {
            Array.from(files).forEach(file => {
                // Prevent duplicate files in the list
                const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    selectedFiles.push(file);
                }
            });
            // Clear input value so selecting the same file again triggers 'change'
            fileInput.value = '';
            syncFileInput();
        }
        renderSelectedFiles();
    }

    function syncFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        syncFileInput();
        renderSelectedFiles();
    };

    function renderSelectedFiles() {
        selectedFilesList.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            fileListContainer.classList.remove('d-none');
            
            selectedFiles.forEach((file, index) => {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                let iconClass = 'bi-file-earmark-code';
                if (file.type.includes('image')) iconClass = 'bi-file-earmark-image text-success';
                else if (file.type.includes('pdf')) iconClass = 'bi-file-earmark-pdf text-danger';
                else if (file.name.endsWith('.docx')) iconClass = 'bi-file-earmark-word text-primary';
                
                const fileItem = `
                    <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 border-light">
                        <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 70%;">
                            <i class="bi ${iconClass} fs-5"></i>
                            <span class="text-dark fw-medium text-truncate" title="${file.name}">${file.name}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-muted border py-1 px-2">${sizeInMB} MB</span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeFile(${index})" style="line-height: 1;">
                                <i class="bi bi-x-circle-fill fs-6"></i>
                            </button>
                        </div>
                    </div>
                `;
                selectedFilesList.insertAdjacentHTML('beforeend', fileItem);
            });
        } else {
            fileListContainer.classList.add('d-none');
        }
    }
});

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
