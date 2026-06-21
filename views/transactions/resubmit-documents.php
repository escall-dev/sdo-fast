<?php
/**
 * Resubmit Mandatory Documentary Requirements — Workflow v3 Stage 3.
 * Requestor uploads documents AFTER budget approval.
 */

$currentPage = 'resubmit_documents';
$pageTitle = 'Submit Mandatory Documentary Requirements';
$pageHeader = 'Submit Mandatory Documentary Requirements';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/document_checklist.php';
require_once __DIR__ . '/../../services/CoverageCategoryService.php';

$userRole = $_SESSION['user_role'] ?? '';
$userId = $_SESSION['user_id'];

$transactionId = (int)($_GET['id'] ?? 0);
$transaction = null;
$errorMsg = null;

if ($transactionId > 0 && $fastPDO !== null) {
    try {
        $stmt = $fastPDO->prepare("
            SELECT t.*, cad.category as ca_category, cad.inclusive_dates as ca_inclusive_dates,
                   rd.category as reimb_category, rd.reimbursement_month, rd.inclusive_dates as reimb_inclusive_dates
            FROM transactions t
            LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
            LEFT JOIN reimbursement_details rd ON t.id = rd.transaction_id
            WHERE t.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $transactionId]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            $errorMsg = 'Transaction not found.';
        } elseif ($transaction['current_status'] !== 'Pending Requestor') {
            $errorMsg = 'Document submission is only available after budget approval. Current status: ' . htmlspecialchars($transaction['current_status']);
        } elseif ((int)$transaction['requestor_id'] !== (int)$userId && $userRole !== 'Super Admin') {
            $errorMsg = 'Access denied: Only the original requestor can submit documents for this transaction.';
        }
    } catch (Exception $e) {
        $errorMsg = 'Database error: ' . $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary-dark">Submit Mandatory Documentary Requirements</h5>
            </div>
            <div class="card-body">
                <?php if ($errorMsg): ?>
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div><?php echo $errorMsg; ?></div>
                    </div>
                    <a href="<?php echo env('APP_URL'); ?>/views/dashboard/index.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                <?php elseif ($transaction): ?>
                    <!-- Transaction Summary -->
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <div class="row g-3 fs-8">
                            <div class="col-12 col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold">Tracking Number</span>
                                <strong class="text-dark"><?php echo htmlspecialchars($transaction['tracking_number']); ?></strong>
                            </div>
                            <div class="col-12 col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold">Transaction Type</span>
                                <strong class="text-dark"><?php echo htmlspecialchars($transaction['transaction_type']); ?></strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block text-uppercase fw-semibold">Particulars / Event Name</span>
                                <strong class="text-dark"><?php echo htmlspecialchars($transaction['event_name']); ?></strong>
                            </div>
                            <div class="col-12 col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold">Gross Amount</span>
                                <strong class="text-dark">₱<?php echo number_format($transaction['amount'], 2); ?></strong>
                            </div>
                            <div class="col-12 col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold">Budget Status</span>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Source of Funds Verified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Notice -->
                    <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>
                            <strong>Budget has been approved!</strong><br>
                            <span class="fs-8">Please now submit the required Mandatory Documentary Requirements for this transaction.</span>
                        </div>
                    </div>

                    <?php
                    $checklistType = $transaction['transaction_type'];
                    $checklistCategory = '';
                    if ($checklistType === 'Cash Advance') {
                        $checklistCategory = $transaction['ca_category'] ?? '';
                    } elseif ($checklistType === 'Reimbursement') {
                        $checklistCategory = $transaction['reimb_category'] ?? '';
                    }
                    ?>
                    <!-- Mandatory Documentary Requirements — Interactive Checklist -->
                    <div id="checklistSection" class="card border border-primary-subtle bg-white shadow-sm mb-4">
                        <div class="card-header bg-primary-subtle py-2 px-3 border-bottom border-primary-subtle">
                            <h6 class="mb-0 fw-bold text-primary-dark d-flex align-items-center gap-2 fs-7">
                                <i class="bi bi-clipboard2-check"></i>
                                <span>Mandatory Documentary Requirements</span>
                                <?php if ($checklistCategory): ?>
                                    <span class="badge bg-primary fs-9"><?php echo htmlspecialchars($checklistCategory); ?></span>
                                <?php endif; ?>
                            </h6>
                            <small class="text-muted fs-9 d-block mt-1">Per DM No. 214, S. 2026 — click <strong>Attach</strong> next to each required document below.</small>
                        </div>
                        <div class="card-body p-3">
                            <div id="checklistContent"></div>
                        </div>
                    </div>

                    <form id="resubmitDocumentsForm" onsubmit="handleResubmit(event)" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">

                        <?php 
                        $type = $transaction['transaction_type'];
                        $caCategory = $transaction['ca_category'] ?? '';
                        $reimbCategory = $transaction['reimb_category'] ?? '';

                        $caFieldConfig = [];
                        $reimbFieldConfig = [];
                        if ($fastPDO !== null && $type === 'Cash Advance' && $caCategory !== '') {
                            $caCatRow = CoverageCategoryService::getCategoryByName($fastPDO, 'Cash Advance', $caCategory);
                            $caFieldConfig = $caCatRow['field_config'] ?? [];
                        }
                        if ($fastPDO !== null && $type === 'Reimbursement' && $reimbCategory !== '') {
                            $reimbCatRow = CoverageCategoryService::getCategoryByName($fastPDO, 'Reimbursement', $reimbCategory);
                            $reimbFieldConfig = $reimbCatRow['field_config'] ?? [];
                        }
                        ?>

                        <?php if ($type === 'Cash Advance' && !empty($caFieldConfig['taItinerary'])): ?>
                            <!-- Travel Documents -->
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Travel Authority Documents (<?php echo htmlspecialchars($caCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Approved Travel Authority <span class="text-danger">*</span></label>
                                        <input type="file" name="approved_ta" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="travel_itinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type === 'Cash Advance' && !empty($caFieldConfig['activityProposal'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Activity Proposal (<?php echo htmlspecialchars($caCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Activity Proposal <span class="text-danger">*</span></label>
                                        <input type="file" name="activity_proposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type === 'Reimbursement' && !empty($reimbFieldConfig['taItinerary'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Travel Authority Documents (Reimbursement — <?php echo htmlspecialchars($reimbCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Approved Travel Authority <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_approved_ta" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_travel_itinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type === 'Reimbursement' && !empty($reimbFieldConfig['activityProposal'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Activity Proposal (<?php echo htmlspecialchars($reimbCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Activity Proposal <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_activity_proposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type === 'Reimbursement' && !empty($reimbFieldConfig['communications'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Communication Load Documents</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">DTR Document <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_dtr" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Certificate <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Bill / Proof of Payment <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_bill_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type === 'Reimbursement' && !empty($reimbFieldConfig['utilityBills'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Utility Bill Documents</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Bill / Proof of Payment <span class="text-danger">*</span></label>
                                        <input type="file" name="utility_bill_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- General Supporting Attachments -->
                        <div class="card border mb-3">
                            <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Additional Supporting Attachments</h6></div>
                            <div class="card-body">
                                <div id="dropzone" class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="cursor: pointer;">
                                    <input type="file" name="attachment[]" id="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.jpg,.jpeg,.png,.docx" multiple style="cursor: pointer; z-index: 10;">
                                    <div class="dz-message">
                                        <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary mb-2 d-block"></i>
                                        <span class="fw-bold text-dark d-block">Drag & Drop files here or click to upload</span>
                                        <span class="text-muted fs-9">Accepts PDF, JPG, PNG, DOCX (Max 10MB per file)</span>
                                    </div>
                                </div>
                                <div id="fileListContainer" class="mt-3 d-none">
                                    <span class="fs-9 fw-semibold text-muted text-uppercase d-block mb-2">Selected Files:</span>
                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden bg-white shadow-sm" id="selectedFilesList"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-semibold">Submission Remarks (Optional)</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Any notes about the submitted documents..."></textarea>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=<?php echo urlencode($transaction['tracking_number']); ?>" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Submit Mandatory Documentary Requirements</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// DM 214 checklist data for this transaction type + category
const DOCUMENT_CHECKLIST_DATA = <?php echo getDocumentChecklistsForJs(); ?>;
const CHECKLIST_TX_TYPE = <?php echo json_encode($checklistType); ?>;
const CHECKLIST_CATEGORY = <?php echo json_encode($checklistCategory); ?>;

// Track files per checklist document
let checklistFileMap = {};
let selectedChecklistFiles = [];

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function resolveChecklist() {
    if (!CHECKLIST_TX_TYPE || !CHECKLIST_CATEGORY) return null;
    const aliases = DOCUMENT_CHECKLIST_DATA.aliases || {};
    const alias = (aliases[CHECKLIST_TX_TYPE] || {})[CHECKLIST_CATEGORY];
    let lookupType = CHECKLIST_TX_TYPE;
    let lookupCategory = CHECKLIST_CATEGORY;
    let note = null;
    let sourceLabel = null;
    if (alias) {
        if (alias.ref) { lookupType = alias.ref; lookupCategory = alias.category; }
        note = alias.note || null;
        sourceLabel = alias.source_label || null;
    }
    const entry = (DOCUMENT_CHECKLIST_DATA.checklists || {})[lookupType]?.[lookupCategory];
    if (!entry) return null;
    return { entry, note, sourceLabel };
}

function renderChecklist() {
    const container = document.getElementById('checklistContent');
    const section = document.getElementById('checklistSection');
    const resolved = resolveChecklist();
    if (!resolved) {
        section.style.display = 'none';
        return;
    }
    section.style.display = '';

    const { entry, note, sourceLabel } = resolved;
    const baseDocs = (entry.documents || []).map(d => ({ ...d, sectionTitle: null }));
    const sectionDocs = (entry.sections || []).flatMap(s =>
        (s.documents || []).map(d => ({ ...d, sectionTitle: s.title || 'Additional Documents' }))
    );
    const allDocs = baseDocs.concat(sectionDocs);
    const requiredDocs = allDocs.filter(d => d.required);
    const optionalDocs = allDocs.filter(d => !d.required);

    let html = '';
    if (note) html += `<div class="alert alert-info border-0 py-2 px-3 mb-3 fs-9"><i class="bi bi-info-circle me-1"></i>${escapeHtml(note)}</div>`;
    if (sourceLabel) html += `<p class="text-muted fs-9 mb-2"><i class="bi bi-arrow-return-right me-1"></i>${escapeHtml(sourceLabel)}</p>`;

    html += `<div class="d-flex gap-2 mb-3 flex-wrap">
        <span class="badge bg-danger-subtle text-danger fs-9">${requiredDocs.length} Required</span>
        <span class="badge bg-secondary-subtle text-secondary fs-9">${optionalDocs.length} Optional</span>
    </div>`;

    if (requiredDocs.length > 0) {
        html += '<h6 class="fw-semibold text-secondary fs-8 mb-2">Required Documents</h6>';
        html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
        html += renderDocRows(requiredDocs);
        html += '</ul>';
    }
    if (optionalDocs.length > 0) {
        html += '<h6 class="fw-semibold text-secondary fs-8 mt-3 mb-2">Optional / Conditional Documents</h6>';
        html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
        html += renderDocRows(optionalDocs);
        html += '</ul>';
    }
    container.innerHTML = html;
    wireAttachButtons();
}

function makeDocKey(doc) {
    return (doc.title + '__' + (doc.sectionTitle || '')).replace(/[^a-zA-Z0-9_-]/g, '_');
}

function renderDocRows(docs) {
    return docs.map(doc => {
        const required = !!doc.required;
        const badgeClass = required ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary';
        const badgeText = required ? 'Required' : 'Optional';
        const condition = doc.condition ? ` <small class="text-muted">(${escapeHtml(doc.condition)})</small>` : '';
        const docKey = makeDocKey(doc);
        const files = checklistFileMap[docKey] || [];
        return `
            <li class="list-group-item py-2 px-3 fs-8">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="text-dark">
                        ${escapeHtml(doc.title)}${condition}
                        ${doc.sectionTitle ? `<br><small class="text-muted">Section: ${escapeHtml(doc.sectionTitle)}</small>` : ''}
                    </span>
                    <span class="badge ${badgeClass} fs-9 flex-shrink-0">${badgeText}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                    <small class="text-muted fs-9" id="sum-${docKey}">${files.length ? files.length + ' file(s) attached' : 'No file attached yet'}</small>
                    <button type="button" class="btn ${files.length ? 'btn-success' : 'btn-outline-primary'} btn-xs py-0 px-2 fs-9 attach-btn" data-doc-key="${docKey}">
                        <i class="bi ${files.length ? 'bi-check-circle' : 'bi-paperclip'} me-1"></i>${files.length ? 'Attached' : 'Attach'}
                    </button>
                </div>
                <div class="mt-1" id="files-${docKey}">
                    ${files.map((f, i) => `
                        <span class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center gap-1">
                            <span class="text-truncate" style="max-width:200px;" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</span>
                            <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-bold" onclick="removeChecklistFile('${docKey}', ${i})" title="Remove">&times;</button>
                        </span>
                    `).join('')}
                </div>
            </li>
        `;
    }).join('');
}

function wireAttachButtons() {
    document.querySelectorAll('.attach-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const key = btn.getAttribute('data-doc-key');
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf,.jpg,.jpeg,.png,.docx';
            input.multiple = true;
            input.onchange = () => {
                if (!checklistFileMap[key]) checklistFileMap[key] = [];
                Array.from(input.files).forEach(f => {
                    checklistFileMap[key].push(f);
                    selectedChecklistFiles.push(f);
                });
                renderChecklist();
                syncHiddenFileInputs();
            };
            input.click();
        });
    });
}

window.removeChecklistFile = function(docKey, index) {
    const removed = checklistFileMap[docKey].splice(index, 1)[0];
    if (removed) {
        const poolIdx = selectedChecklistFiles.indexOf(removed);
        if (poolIdx > -1) selectedChecklistFiles.splice(poolIdx, 1);
    }
    renderChecklist();
    syncHiddenFileInputs();
};

function syncHiddenFileInputs() {
    // Remove old hidden inputs
    document.querySelectorAll('.checklist-file-input').forEach(el => el.remove());
    const labelInput = document.getElementById('attachmentLabelsJson');
    if (labelInput) labelInput.remove();
    
    const labels = [];
    
    selectedChecklistFiles.forEach((file, i) => {
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'checklist_files[]';
        input.className = 'checklist-file-input';
        input.style.display = 'none';
        input.files = dt.files;
        document.getElementById('resubmitDocumentsForm').appendChild(input);
        
        let label = file.name;
        for (const [docKey, files] of Object.entries(checklistFileMap)) {
            if (files.includes(file)) {
                label = docKey.split('__')[0].replace(/_/g, ' ');
                break;
            }
        }
        labels.push(label);
    });
    
    const jsonInput = document.createElement('input');
    jsonInput.type = 'hidden';
    jsonInput.name = 'attachment_labels_json';
    jsonInput.id = 'attachmentLabelsJson';
    jsonInput.value = JSON.stringify(labels);
    document.getElementById('resubmitDocumentsForm').appendChild(jsonInput);
}

renderChecklist();

async function handleResubmit(e) {
    e.preventDefault();
    const form = document.getElementById('resubmitDocumentsForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    
    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/transactions/resubmit-documents.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            API.showToast('Documents submitted successfully! The transaction has been routed to Accounting Support for Document Inspection.', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=' + encodeURIComponent(data.tracking_number);
            }, 1500);
        } else {
            API.showToast(data.message || 'Submission failed.', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Mandatory Documentary Requirements';
        }
    } catch (err) {
        API.showToast('Network error during submission. Please try again.', 'danger');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Mandatory Documentary Requirements';
    }
}

// File list display for general attachments
document.getElementById('attachment')?.addEventListener('change', function() {
    const container = document.getElementById('fileListContainer');
    const list = document.getElementById('selectedFilesList');
    list.innerHTML = '';
    
    if (this.files.length > 0) {
        container.classList.remove('d-none');
        for (let i = 0; i < this.files.length; i++) {
            const f = this.files[i];
            const sizeMB = (f.size / (1024 * 1024)).toFixed(1);
            list.innerHTML += `<div class="list-group-item p-2 d-flex justify-content-between align-items-center fs-9">
                <span><i class="bi bi-file-earmark me-2 text-primary"></i>${f.name}</span>
                <span class="text-muted">${sizeMB} MB</span>
            </div>`;
        }
    } else {
        container.classList.add('d-none');
    }
});
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
