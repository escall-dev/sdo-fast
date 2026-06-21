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
require_once __DIR__ . '/../../includes/document_checklist.php';
require_once __DIR__ . '/../../services/CoverageCategoryService.php';

$userRole = $_SESSION['user_role'] ?? '';

// Fetch tax configuration keys for dropdown select
$taxConfigurations = [];
$caCategories = [];
$reimbCategories = [];
$categoryFieldMaps = ['caFieldMap' => [], 'reimbFieldMap' => []];

if ($fastPDO !== null) {
    try {
        $taxConfigurations = $fastPDO->query("SELECT * FROM tax_configurations WHERE is_active = 1")->fetchAll();
        $caCategories = CoverageCategoryService::getActiveCategories($fastPDO, 'Cash Advance');
        $reimbCategories = CoverageCategoryService::getActiveCategories($fastPDO, 'Reimbursement');
        $categoryFieldMaps = CoverageCategoryService::getFieldMapsForJs($fastPDO);
    } catch (PDOException $e) {
        error_log("Failed to fetch submit form data: " . $e->getMessage());
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
                <form id="submitTransactionForm" onsubmit="handleFormSubmit(event)">
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
                            <label for="targetDate" class="form-label fs-8 fw-semibold text-muted">Target Implementation Date</label>
                            <input type="date" name="target_date" id="targetDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-12">
                            <label for="eventName" class="form-label fs-8 fw-semibold text-muted">Particulars / Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" id="eventName" class="form-control" placeholder="e.g. SDO Seminar Reimbursement for Math Teachers" required>
                        </div>
                    </div>
                    
                    <!-- ====================================================================
                         CASH ADVANCE COVERAGE SECTION
                         ==================================================================== -->
                    <div id="cashAdvanceCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cashAdvanceCategory" class="form-label fs-8 fw-semibold text-muted">Cash Advance Coverage Type <span class="text-danger">*</span></label>
                                <select name="cash_advance_category" id="cashAdvanceCategory" class="form-select">
                                    <option value="" disabled selected>Select Coverage Type</option>
                                    <?php foreach ($caCategories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                            <?php echo htmlspecialchars($cat['display_label'] ?: $cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- CA: Date & Venue Fields (Travel, Training, Meals, Accommodation, M&A, SLAC/GAWAD) -->
                        <div id="caDateVenueContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-calendar-event me-1"></i>Schedule & Venue Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="caStartDate" class="form-label fs-8 fw-semibold text-muted">Inclusive Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_start_date" id="caStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="caEndDate" class="form-label fs-8 fw-semibold text-muted">Inclusive End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_end_date" id="caEndDate" class="form-control">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caVenue" class="form-label fs-8 fw-semibold text-muted">Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="venue" id="caVenue" class="form-control" placeholder="e.g. Regional Office, Hotel Venue Name">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Fund Source Field — MOVED to Budget Officer in Workflow v3 -->
                        

                        <!-- CA: TA + Itinerary Uploads (Travel only) — DEFERRED in Workflow v3 -->
                        <div id="caTaItineraryContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Travel documents (Approved TA, Travel Itinerary)</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Activity Proposal Upload (Training, SLAC/GAWAD) — DEFERRED in Workflow v3 -->
                        <div id="caActivityProposalContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Activity Proposal</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Month Selector (Communication Expenses) -->
                        <div id="caMonthContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-telephone-inbound me-1"></i>Communication Period</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="ca_month" id="caMonth" class="form-select">
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
                            </div>
                        </div>
                    </div>

                    <!-- ====================================================================
                         REIMBURSEMENT COVERAGE SECTION
                         ==================================================================== -->
                    <div id="reimbursementCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reimbursementCategory" class="form-label fs-8 fw-semibold text-muted">Reimbursement Coverage Type <span class="text-danger">*</span></label>
                                <select name="reimbursement_category" id="reimbursementCategory" class="form-select">
                                    <option value="" disabled selected>Select Coverage Type</option>
                                    <?php foreach ($reimbCategories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                            <?php echo htmlspecialchars($cat['display_label'] ?: $cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Reimb: Date & Venue Fields (Travel, Meals, Accommodation, M&A, Seminars/Trainings, GAD/SLAC) -->
                        <div id="reimbDateVenueContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-calendar-event me-1"></i>Schedule & Venue Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbStartDate" class="form-label fs-8 fw-semibold text-muted">Inclusive Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="reimb_start_date" id="reimbStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbEndDate" class="form-label fs-8 fw-semibold text-muted">Inclusive End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="reimb_end_date" id="reimbEndDate" class="form-control">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbVenue" class="form-label fs-8 fw-semibold text-muted">Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="reimb_venue" id="reimbVenue" class="form-control" placeholder="e.g. SDO Conference Hall, School Gym">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: TA + Itinerary Uploads (Travel only) — DEFERRED in Workflow v3 -->
                        <div id="reimbTaItineraryContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Travel documents (Approved TA, Travel Itinerary)</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Activity Proposal Upload (Seminars/Trainings) — DEFERRED in Workflow v3 -->
                        <div id="reimbActivityProposalContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Activity Proposal</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Communications Load Fields — DEFERRED in Workflow v3 -->
                        <div id="reimbCommunicationsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-telephone-inbound me-1"></i>Communications Load Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbursementMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="reimbursement_month" id="reimbursementMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
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
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Supporting documents (DTR, Certificate, Bill/Proof of Payment)</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Utility Bills Fields — DEFERRED in Workflow v3 -->
                        <div id="reimbUtilityBillsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-lightning-charge me-1"></i>Utility Bill Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="utilityMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="utility_month" id="utilityMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
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
                                <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0 py-2 px-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span><strong>Bill / Proof of Payment</strong> will be uploaded after the Budget Officer approves the source of funds for this transaction.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DM 214 Documents Checklist — Reference list (Workflow v3: uploads happen after budget approval) -->
                    <div id="documentChecklistSection" class="mb-4 d-none">
                        <div class="card border border-primary-subtle bg-white shadow-sm">
                            <div class="card-header bg-primary-subtle py-2 px-3 border-bottom border-primary-subtle">
                                <h6 class="mb-0 fw-bold text-primary-dark d-flex align-items-center gap-2 fs-7">
                                    <i class="bi bi-clipboard2-check"></i>
                                    <span>Mandatory Documentary Requirements</span>
                                    <span class="badge bg-primary fs-9" id="documentChecklistCategoryLabel"></span>
                                </h6>
                                <small class="text-muted fs-9 d-block mt-1">Per DM No. 214, S. 2026 — prepare these documents. You will upload them after the Budget Officer approves the source of funds.</small>
                            </div>
                            <div class="card-body p-3">
                                <div class="alert alert-info border-0 py-2 px-3 mb-3 fs-8">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <span>You will submit the <em>Mandatory Documentary Requirements</em> after the Budget Officer has verified and approved the source of funds for this transaction.</span>
                                </div>
                                <div id="documentChecklistNote" class="d-none alert alert-info border-0 py-2 px-3 mb-3 fs-9"></div>
                                <div id="documentChecklistSource" class="d-none text-muted fs-9 mb-2"></div>
                                <div id="documentChecklistContent"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial and Tax details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="amount" class="form-label fs-8 fw-semibold text-muted"> Amount Requested (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" min="1" required>
                        </div>
                    </div>

                    <!-- Upload attachment — REMOVED in Workflow v3 -->
                    <!-- Documents are submitted AFTER budget approval via the resubmit page -->
                    <div class="mb-4">
                        <div class="alert alert-info border-0 d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div>
                                <strong>Document uploads are not required at this stage.</strong><br>
                                <span class="fs-8">You will submit the <em>Mandatory Documentary Requirements</em> after the Budget Officer has verified and approved the source of funds for this transaction.</span>
                            </div>
                        </div>
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
const DOCUMENT_CHECKLIST_DATA = <?php echo getDocumentChecklistsForJs(); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const txTypeSelect = document.getElementById('transactionType');
    const caCategorySelect = document.getElementById('cashAdvanceCategory');
    const caSection = document.getElementById('cashAdvanceCategorySection');
    const reimbCategorySelect = document.getElementById('reimbursementCategory');
    const reimbSection = document.getElementById('reimbursementCategorySection');

    // CA sub-field containers
    const caDateVenue = document.getElementById('caDateVenueContainer');
    const caFundSource = document.getElementById('caFundSourceContainer');
    const caTaItinerary = document.getElementById('caTaItineraryContainer');
    const caActivityProposal = document.getElementById('caActivityProposalContainer');
    const caMonth = document.getElementById('caMonthContainer');

    // Reimb sub-field containers
    const reimbDateVenue = document.getElementById('reimbDateVenueContainer');
    const reimbTaItinerary = document.getElementById('reimbTaItineraryContainer');
    const reimbActivityProposal = document.getElementById('reimbActivityProposalContainer');
    const reimbCommunications = document.getElementById('reimbCommunicationsContainer');
    const reimbUtilityBills = document.getElementById('reimbUtilityBillsContainer');
    const documentChecklistSection = document.getElementById('documentChecklistSection');
    const documentChecklistContent = document.getElementById('documentChecklistContent');
    const documentChecklistNote = document.getElementById('documentChecklistNote');
    const documentChecklistSource = document.getElementById('documentChecklistSource');
    const documentChecklistCategoryLabel = document.getElementById('documentChecklistCategoryLabel');

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function resolveChecklistClient(txType, category) {
        if (!txType || !category) return null;

        const alias = DOCUMENT_CHECKLIST_DATA.aliases?.[txType]?.[category];
        let lookupType = txType;
        let lookupCategory = category;
        let note = null;
        let sourceLabel = null;

        if (alias) {
            if (alias.ref) {
                lookupType = alias.ref;
                lookupCategory = alias.category;
            }
            note = alias.note || null;
            sourceLabel = alias.source_label || null;
        }

        const entry = DOCUMENT_CHECKLIST_DATA.checklists?.[lookupType]?.[lookupCategory];
        if (!entry) return null;

        return { entry, note, sourceLabel, category };
    }

    function renderDocumentRows(documents) {
        if (!documents || !documents.length) return '';

        return documents.map(doc => {
            const required = !!doc.required;
            const badgeClass = required ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary';
            const badgeText = required ? 'Required' : 'Optional';
            const condition = doc.condition ? ` <small class="text-muted">(${escapeHtml(doc.condition)})</small>` : '';
            return `
                <li class="list-group-item d-flex justify-content-between align-items-start gap-2 py-2 px-3 fs-8">
                    <span class="text-dark">
                        ${escapeHtml(doc.title)}${condition}
                        ${doc.sectionTitle ? `<br><small class="text-muted">Section: ${escapeHtml(doc.sectionTitle)}</small>` : ''}
                    </span>
                    <span class="badge ${badgeClass} fs-9 flex-shrink-0">${badgeText}</span>
                </li>
            `;
        }).join('');
    }

    function updateDocumentChecklist() {
        const txType = txTypeSelect.value;
        let category = '';
        if (txType === 'Cash Advance') {
            category = caCategorySelect.value;
        } else if (txType === 'Reimbursement') {
            category = reimbCategorySelect.value;
        }

        if (!category || (txType !== 'Cash Advance' && txType !== 'Reimbursement')) {
            documentChecklistSection.classList.add('d-none');
            documentChecklistContent.innerHTML = '';
            return;
        }

        const resolved = resolveChecklistClient(txType, category);
        if (!resolved) {
            documentChecklistSection.classList.add('d-none');
            documentChecklistContent.innerHTML = '';
            return;
        }

        documentChecklistSection.classList.remove('d-none');
        documentChecklistCategoryLabel.textContent = category;

        const baseDocs = (resolved.entry.documents || []).map(d => ({ ...d, sectionTitle: null }));
        const sectionDocs = (resolved.entry.sections || []).flatMap(section =>
            (section.documents || []).map(d => ({
                ...d,
                sectionTitle: section.title || 'Additional Documents'
            }))
        );

        const allDocs = baseDocs.concat(sectionDocs);
        const requiredDocs = allDocs.filter(d => d.required);
        const optionalDocs = allDocs.filter(d => !d.required);
        const reqCount = allDocs.filter(d => d.required).length;
        const optCount = allDocs.length - reqCount;

        if (resolved.note) {
            documentChecklistNote.classList.remove('d-none');
            documentChecklistNote.innerHTML = '<i class="bi bi-info-circle me-1"></i>' + escapeHtml(resolved.note);
        } else {
            documentChecklistNote.classList.add('d-none');
            documentChecklistNote.innerHTML = '';
        }

        if (resolved.sourceLabel) {
            documentChecklistSource.classList.remove('d-none');
            documentChecklistSource.innerHTML = '<i class="bi bi-arrow-return-right me-1"></i>' + escapeHtml(resolved.sourceLabel);
        } else {
            documentChecklistSource.classList.add('d-none');
            documentChecklistSource.innerHTML = '';
        }

        let html = `<div class="d-flex gap-2 mb-3 flex-wrap">
            <span class="badge bg-danger-subtle text-danger fs-9">${reqCount} Required</span>
            <span class="badge bg-secondary-subtle text-secondary fs-9">${optCount} Optional</span>
        </div>`;
        if (requiredDocs.length > 0) {
            html += '<h6 class="fw-semibold text-secondary fs-8 mb-2">Required Documents</h6>';
            html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
            html += renderDocumentRows(requiredDocs);
            html += '</ul>';
        }

        if (optionalDocs.length > 0) {
            html += '<h6 class="fw-semibold text-secondary fs-8 mt-3 mb-2">Optional / Conditional Documents</h6>';
            html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
            html += renderDocumentRows(optionalDocs);
            html += '</ul>';
        }

        documentChecklistContent.innerHTML = html;
    }

    // Coverage type → sub-field mapping (from database)
    const caFieldMap = <?php echo json_encode($categoryFieldMaps['caFieldMap'], JSON_UNESCAPED_UNICODE); ?>;
    const reimbFieldMap = <?php echo json_encode($categoryFieldMaps['reimbFieldMap'], JSON_UNESCAPED_UNICODE); ?>;

    function setFieldsState(container, enabled, required) {
        if (!container) return;
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !enabled;
            // Only set required on non-file inputs or file inputs when explicitly needed
            if (input.type !== 'file') {
                input.required = required;
            } else {
                input.required = required;
            }
        });
    }

    function hideAndDisable(container) {
        if (!container) return;
        container.classList.add('d-none');
        setFieldsState(container, false, false);
    }

    function showAndEnable(container) {
        if (!container) return;
        container.classList.remove('d-none');
        setFieldsState(container, true, true);
    }

    function toggleFormFields() {
        const txType = txTypeSelect.value;

        // ── CASH ADVANCE ──
        if (txType === 'Cash Advance') {
            caSection.classList.remove('d-none');
            caCategorySelect.disabled = false;
            caCategorySelect.required = true;

            const caCat = caCategorySelect.value;
            const cfg = caFieldMap[caCat] || {};

            // Toggle each sub-field group
            cfg.dateVenue ? showAndEnable(caDateVenue) : hideAndDisable(caDateVenue);
            cfg.fundSource ? showAndEnable(caFundSource) : hideAndDisable(caFundSource);
            cfg.taItinerary ? showAndEnable(caTaItinerary) : hideAndDisable(caTaItinerary);
            cfg.activityProposal ? showAndEnable(caActivityProposal) : hideAndDisable(caActivityProposal);
            cfg.month ? showAndEnable(caMonth) : hideAndDisable(caMonth);
        } else {
            caSection.classList.add('d-none');
            caCategorySelect.disabled = true;
            caCategorySelect.required = false;
            hideAndDisable(caDateVenue);
            hideAndDisable(caFundSource);
            hideAndDisable(caTaItinerary);
            hideAndDisable(caActivityProposal);
            hideAndDisable(caMonth);
        }

        // ── REIMBURSEMENT ──
        if (txType === 'Reimbursement') {
            reimbSection.classList.remove('d-none');
            reimbCategorySelect.disabled = false;
            reimbCategorySelect.required = true;

            const reimbCat = reimbCategorySelect.value;
            const cfg = reimbFieldMap[reimbCat] || {};

            cfg.dateVenue ? showAndEnable(reimbDateVenue) : hideAndDisable(reimbDateVenue);
            cfg.taItinerary ? showAndEnable(reimbTaItinerary) : hideAndDisable(reimbTaItinerary);
            cfg.activityProposal ? showAndEnable(reimbActivityProposal) : hideAndDisable(reimbActivityProposal);
            cfg.communications ? showAndEnable(reimbCommunications) : hideAndDisable(reimbCommunications);
            cfg.utilityBills ? showAndEnable(reimbUtilityBills) : hideAndDisable(reimbUtilityBills);
        } else {
            reimbSection.classList.add('d-none');
            reimbCategorySelect.disabled = true;
            reimbCategorySelect.required = false;
            hideAndDisable(reimbDateVenue);
            hideAndDisable(reimbTaItinerary);
            hideAndDisable(reimbActivityProposal);
            hideAndDisable(reimbCommunications);
            hideAndDisable(reimbUtilityBills);
        }

        updateDocumentChecklist();
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
                    if (currentAttachDocKey) {
                        const existing = docFileMap[currentAttachDocKey] || [];
                        if (!existing.includes(file.name)) {
                            existing.push(file.name);
                        }
                        docFileMap[currentAttachDocKey] = existing;
                    }
                }
            });
            // Clear input value so selecting the same file again triggers 'change'
            fileInput.value = '';
            syncFileInput();
        }
        if (!currentAttachDocKey) {
            autoAssignUnmappedFiles();
        }
        currentAttachDocKey = null;
        renderSelectedFiles();
        refreshDocFileSummaries();
    }

    function syncFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    window.removeFile = function(index) {
        const removed = selectedFiles.splice(index, 1)[0];

        if (removed) {
            Object.keys(docFileMap).forEach(key => {
                docFileMap[key] = (docFileMap[key] || []).filter(name => name !== removed.name);
            });
        }

        syncFileInput();
        renderSelectedFiles();
        refreshDocFileSummaries();
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
