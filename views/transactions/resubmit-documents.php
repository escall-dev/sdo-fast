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
                   rd.category as reimb_category, rd.reimbursement_month, rd.inclusive_dates as reimb_inclusive_dates,
                   rd.mode_of_travel as reimb_mode_of_travel
            FROM transactions t
            LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
            LEFT JOIN reimbursement_details rd ON t.id = rd.transaction_id
            WHERE t.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $transactionId]);
        $transaction = $stmt->fetch();

        if (!$transaction) {
            $errorMsg = 'Transaction not found.';
        } elseif (!in_array($transaction['current_status'], ['Pending Requestor', 'Pending Liquidation'])) {
            $errorMsg = 'Document submission is only available after budget approval or during liquidation. Current status: ' . htmlspecialchars($transaction['current_status']);
        } elseif ((int)$transaction['requestor_id'] !== (int)$userId && $userRole !== 'Super Admin') {
            $errorMsg = 'Access denied: Only the original requestor can submit documents for this transaction.';
        }
        
        $currentStage = ($transaction['current_status'] === 'Pending Liquidation') ? 'liquidation' : 'submission';
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
                            <?php if ($transaction['transaction_type'] === 'Reimbursement' && ($transaction['reimb_category'] ?? '') === 'Travel' && !empty($transaction['reimb_mode_of_travel'])): ?>
                            <div class="col-12 col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold">Mode of Travel</span>
                                <strong class="text-dark"><?php echo htmlspecialchars($transaction['reimb_mode_of_travel']); ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Info Notice -->
                    <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>
                            <?php if ($currentStage === 'liquidation'): ?>
                                <strong>Transaction is ready for Liquidation!</strong><br>
                                <span class="fs-8">Please submit the required Liquidation Documentary Requirements for this transaction.</span>
                            <?php else: ?>
                                <strong>Budget has been approved!</strong><br>
                                <span class="fs-8">Please now submit the required Mandatory Documentary Requirements for this transaction.</span>
                            <?php endif; ?>
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
                                <span><?php echo $currentStage === 'liquidation' ? 'Liquidation Documentary Requirements' : 'Mandatory Documentary Requirements'; ?></span>
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
                        $reimbModeOfTravel = $transaction['reimb_mode_of_travel'] ?? '';

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

                        <?php if ($currentStage !== 'liquidation' && $type === 'Cash Advance' && !empty($caFieldConfig['taItinerary'])): ?>
                            <!-- Travel Documents -->
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Travel Authority Documents (<?php echo htmlspecialchars($caCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Approved Travel Authority</label>
                                        <input type="file" name="approved_ta" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx">
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB (optional)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Travel Itinerary</label>
                                        <input type="file" name="travel_itinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx">
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB (optional)</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($currentStage !== 'liquidation' && $type === 'Cash Advance' && !empty($caFieldConfig['activityProposal'])): ?>
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

                        <?php if ($currentStage !== 'liquidation' && $type === 'Reimbursement' && !empty($reimbFieldConfig['taItinerary'])): ?>
                            <div class="card border mb-3">
                                <div class="card-header bg-light"><h6 class="mb-0 fw-bold fs-7">Travel Authority Documents (Reimbursement — <?php echo htmlspecialchars($reimbCategory); ?>)</h6></div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Approved Travel Authority <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_approved_ta" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_travel_itinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                                        <small class="text-muted">PDF, JPG, PNG, DOCX — Max 10MB</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($currentStage !== 'liquidation' && $type === 'Reimbursement' && !empty($reimbFieldConfig['activityProposal'])): ?>
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

                        <?php if ($currentStage !== 'liquidation' && $type === 'Reimbursement' && !empty($reimbFieldConfig['communications'])): ?>
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

                        <?php if ($currentStage !== 'liquidation' && $type === 'Reimbursement' && !empty($reimbFieldConfig['utilityBills'])): ?>
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
                            <button type="submit" class="btn btn-primary px-4">Submit <?php echo $currentStage === 'liquidation' ? 'Liquidation' : 'Mandatory Documentary'; ?> Requirements</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// DM 214 checklist data for this transaction type + category
const CURRENT_STAGE = '<?php echo $currentStage; ?>';
const DOCUMENT_CHECKLIST_DATA = <?php echo getDocumentChecklistsForJs($currentStage); ?>;
const CHECKLIST_TX_TYPE = <?php echo json_encode($checklistType); ?>;
const CHECKLIST_CATEGORY = <?php echo json_encode($checklistCategory); ?>;
const CHECKLIST_TX_ID = <?php echo json_encode($transaction['id']); ?>;
const REIMB_MODE_OF_TRAVEL = <?php echo json_encode($reimbModeOfTravel); ?>;

// Track files per checklist document
let checklistFileMap = {};
let selectedChecklistFiles = [];
let totalRequiredDocs = 0;
let uploadedRequiredDocs = 0;
let checklistRequiredDocs = []; // Copy of required docs array, used by updateSubmitButtonState

// =========================================================================
// IndexedDB persistence for page refresh cache
// =========================================================================
const DB_NAME = 'FAST_ResubmitDocsDB';
const DB_VERSION = 1;
const STORE_NAME = 'cached_files';

function getDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME);
            }
        };
        request.onsuccess = (e) => resolve(e.target.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

async function persistChecklistFiles() {
    try {
        const db = await getDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        
        // Clean up old checklist items for this transaction
        const keysRequest = store.getAllKeys();
        keysRequest.onsuccess = () => {
            const keys = keysRequest.result;
            keys.forEach(k => {
                if (k.startsWith(`chk|${CHECKLIST_TX_ID}|`)) {
                    store.delete(k);
                }
            });
            
            // Save current map
            for (const [docKey, files] of Object.entries(checklistFileMap)) {
                files.forEach((file, index) => {
                    const dbKey = `chk|${CHECKLIST_TX_ID}|${docKey}|${index}`;
                    store.put(file, dbKey);
                });
            }
        };
    } catch (err) {
        console.error('Failed to persist checklist files:', err);
    }
}

async function persistStaticFile(inputName, files) {
    try {
        const db = await getDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        const dbKey = `static|${CHECKLIST_TX_ID}|${inputName}`;
        
        if (!files || files.length === 0) {
            store.delete(dbKey);
        } else {
            const fileArray = Array.from(files);
            store.put(fileArray, dbKey);
        }
    } catch (err) {
        console.error('Failed to persist static file:', err);
    }
}

async function clearCacheForTransaction() {
    try {
        const db = await getDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        const keysRequest = store.getAllKeys();
        keysRequest.onsuccess = () => {
            const keys = keysRequest.result;
            keys.forEach(k => {
                if (k.startsWith(`chk|${CHECKLIST_TX_ID}|`) || k.startsWith(`static|${CHECKLIST_TX_ID}|`)) {
                    store.delete(k);
                }
            });
        };
    } catch (err) {
        console.error('Failed to clear cache:', err);
    }
}

async function loadCachedFiles() {
    try {
        const db = await getDB();
        const tx = db.transaction(STORE_NAME, 'readonly');
        const store = tx.objectStore(STORE_NAME);
        const request = store.openCursor();
        
        const checklistItems = [];
        const staticItems = {};
        
        request.onsuccess = (e) => {
            const cursor = e.target.result;
            if (cursor) {
                const key = cursor.key;
                if (key.startsWith(`chk|${CHECKLIST_TX_ID}|`)) {
                    const parts = key.split('|');
                    const index = parseInt(parts[parts.length - 1]);
                    const docKey = parts.slice(2, parts.length - 1).join('|');
                    const file = cursor.value;
                    checklistItems.push({ docKey, index, file });
                } else if (key.startsWith(`static|${CHECKLIST_TX_ID}|`)) {
                    const inputName = key.substring(`static|${CHECKLIST_TX_ID}|`.length);
                    staticItems[inputName] = cursor.value;
                }
                cursor.continue();
            } else {
                // Done traversing. Reconstruct checklistFileMap
                checklistItems.sort((a, b) => a.index - b.index);
                checklistItems.forEach(item => {
                    if (!checklistFileMap[item.docKey]) checklistFileMap[item.docKey] = [];
                    const reconstructedFile = new File([item.file], item.file.name, {
                        type: item.file.type,
                        lastModified: item.file.lastModified
                    });
                    checklistFileMap[item.docKey].push(reconstructedFile);
                    selectedChecklistFiles.push(reconstructedFile);
                });
                
                // Populate static inputs
                for (const [inputName, files] of Object.entries(staticItems)) {
                    const selector = inputName === 'attachment[]' ? '#attachment' : `input[name="${inputName}"]`;
                    const input = document.querySelector(selector);
                    if (input && files && files.length > 0) {
                        const dt = new DataTransfer();
                        files.forEach(f => {
                            const reconstructedFile = new File([f], f.name, {
                                type: f.type,
                                lastModified: f.lastModified
                            });
                            dt.items.add(reconstructedFile);
                        });
                        input.files = dt.files;
                        if (inputName === 'attachment[]') {
                            input.dispatchEvent(new Event('change'));
                        }
                    }
                }
                
                // Render checklist UI
                renderChecklist();
            }
        };
    } catch (err) {
        console.error('Failed to load cached files:', err);
        renderChecklist();
    }
}

function bindStaticInputsPersistence() {
    document.querySelectorAll('#resubmitDocumentsForm input[type="file"]:not(.doc-file-input)').forEach(input => {
        input.addEventListener('change', () => {
            const name = input.getAttribute('name');
            persistStaticFile(name, input.files);
        });
    });
}

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
    const allDocs = baseDocs.concat(sectionDocs).filter(d => {
        // For Travel reimbursements, only show docs matching the selected Mode of Travel
        if (CHECKLIST_TX_TYPE === 'Reimbursement' && CHECKLIST_CATEGORY === 'Travel') {
            const selectedMode = REIMB_MODE_OF_TRAVEL;
            if (!selectedMode) return true;
            // Only show documents explicitly assigned to the selected mode
            if (!d.modesOfTravel || d.modesOfTravel.length === 0) return false;
            return d.modesOfTravel.includes(selectedMode);
        }
        return true;
    });
    const requiredDocs = allDocs.filter(d => d.required);
    const optionalDocs = allDocs.filter(d => !d.required);

    totalRequiredDocs = requiredDocs.length;
    checklistRequiredDocs = requiredDocs;
    uploadedRequiredDocs = requiredDocs.filter(d => (checklistFileMap[makeDocKey(d)] || []).length > 0).length;

    let html = '';
    if (note) html += `<div class="alert alert-info border-0 py-2 px-3 mb-3 fs-9"><i class="bi bi-info-circle me-1"></i>${escapeHtml(note)}</div>`;
    if (sourceLabel) html += `<p class="text-muted fs-9 mb-2"><i class="bi bi-arrow-return-right me-1"></i>${escapeHtml(sourceLabel)}</p>`;

    // 9. Progress summary at the top
    const progressPercent = totalRequiredDocs === 0 ? 100 : Math.round((uploadedRequiredDocs / totalRequiredDocs) * 100);
    html += `
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-end mb-1">
            <span class="fs-8 fw-semibold text-dark">${uploadedRequiredDocs} of ${totalRequiredDocs} Required Uploaded</span>
            <span class="fs-9 text-muted">${progressPercent}%</span>
        </div>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-success transition-all" role="progressbar" style="width: ${progressPercent}%" aria-valuenow="${progressPercent}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>`;

    if (requiredDocs.length > 0) {
        html += '<h6 class="fw-semibold text-secondary fs-8 mb-2">Required Documents</h6>';
        html += '<div class="d-flex flex-column gap-3 mb-4">';
        html += renderDocRows(requiredDocs);
        html += '</div>';
    }
    
    // 10. Collapsible optional section
    if (optionalDocs.length > 0) {
        html += `
        <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
            <h6 class="fw-semibold text-secondary fs-8 mb-0">Optional / Conditional Documents</h6>
            <button class="btn btn-sm btn-link text-decoration-none fs-9 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#optionalDocsCollapse" aria-expanded="false" aria-controls="optionalDocsCollapse">
                Toggle Visibility <i class="bi bi-chevron-down ms-1"></i>
            </button>
        </div>
        <div class="collapse" id="optionalDocsCollapse">
            <div class="d-flex flex-column gap-3 mb-2">
                ${renderDocRows(optionalDocs)}
            </div>
        </div>`;
    }
    container.innerHTML = html;
    wireAttachEvents();
    updateSubmitButtonState();
}

function makeDocKey(doc) {
    return (doc.title + '__' + (doc.sectionTitle || '')).replace(/[^a-zA-Z0-9_-]/g, '_');
}

function renderDocRows(docs) {
    return docs.map(doc => {
        const required = !!doc.required;
        const condition = doc.condition ? ` <small class="text-muted">(${escapeHtml(doc.condition)})</small>` : '';
        const docKey = makeDocKey(doc);
        const files = checklistFileMap[docKey] || [];
        const isCompleted = files.length > 0;
        
        // 3. Status badge change
        const badgeClass = isCompleted ? 'bg-success-subtle text-success' : (required ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary');
        const badgeText = isCompleted ? 'Completed' : (required ? 'Required' : 'Optional');
        
        // 8. Checklist icons
        const iconHtml = isCompleted 
            ? '<i class="bi bi-check-circle-fill text-success fs-5"></i>' 
            : (required ? '<i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>' : '<i class="bi bi-hourglass-split text-secondary fs-5"></i>');

        // 11. Each row as a status card
        // 5. Clickable card + drag and drop (input file covers the card)
        return `
            <div class="card border ${isCompleted ? 'border-success bg-success-subtle' : 'border-primary-subtle bg-light'} shadow-sm doc-card position-relative" style="transition: all 0.3s;" id="card-${docKey}">
                <input type="file" class="doc-file-input position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.jpg,.jpeg,.png,.docx" multiple style="cursor: pointer; z-index: 10;" data-doc-key="${docKey}">
                <div class="card-body p-3">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="pt-1">${iconHtml}</div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <span class="fw-bold text-dark fs-7" style="z-index: 11; position: relative; pointer-events: none;">
                                    ${escapeHtml(doc.title)}${condition}
                                    ${doc.sectionTitle ? `<br><small class="text-muted fw-normal">Section: ${escapeHtml(doc.sectionTitle)}</small>` : ''}
                                </span>
                                <span class="badge ${badgeClass} fs-9 flex-shrink-0 status-badge" style="z-index: 11; position: relative;">${badgeText}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <small class="text-muted fs-9" style="z-index: 11; position: relative;">Accepts: PDF, JPG, PNG, DOCX (Max 10MB per file)</small>
                                <button type="button" class="btn ${isCompleted ? 'btn-success' : 'btn-outline-primary'} btn-sm py-1 px-3 fs-9 attach-btn-visual" style="pointer-events: none; z-index: 11; position: relative;">
                                    <i class="bi ${isCompleted ? 'bi-check-circle' : 'bi-paperclip'} me-1"></i><span class="btn-text">${isCompleted ? 'Attached' : 'Attach'}</span>
                                </button>
                            </div>
                            
                            <!-- 1. Upload progress bar container -->
                            <div class="upload-progress-container d-none mt-2" id="progress-${docKey}" style="z-index: 11; position: relative;">
                                <div class="d-flex justify-content-between fs-9 mb-1">
                                    <span class="text-primary fw-semibold"><i class="spinner-border spinner-border-sm me-1"></i>Uploading...</span>
                                    <span class="progress-percent-text text-primary">0%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <!-- 2. File info after upload -->
                            <div class="files-container mt-2 ${files.length ? '' : 'd-none'}" id="files-${docKey}" style="z-index: 20; position: relative;">
                                ${files.map((f, i) => `
                                    <div class="d-flex align-items-center justify-content-between bg-white border rounded p-2 mb-1 shadow-sm">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <i class="bi bi-file-earmark-check text-success fs-5"></i>
                                            <div class="d-flex flex-column text-truncate">
                                                <span class="text-dark fs-8 fw-semibold text-truncate" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</span>
                                                <span class="text-muted fs-9">${(f.size / (1024 * 1024)).toFixed(1)} MB • ${f.lastModifiedDate ? new Date(f.lastModifiedDate).toLocaleTimeString() : new Date().toLocaleTimeString()}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 ms-2 flex-shrink-0">
                                            <!-- 6. Quick actions after upload -->
                                            <button type="button" class="btn btn-light btn-sm text-primary fs-8 py-0 px-2" onclick="previewFile('${docKey}', ${i}, event)">Preview</button>
                                            <button type="button" class="btn btn-light btn-sm text-secondary fs-8 py-0 px-2" onclick="triggerReplace('${docKey}', ${i}, event)">Replace</button>
                                            <button type="button" class="btn btn-light btn-sm text-danger fs-8 py-0 px-2 fw-bold" onclick="removeChecklistFile('${docKey}', ${i}, event)">&times; Remove</button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

let fileToReplace = null; // { docKey, index }

function processFiles(key, fileList, isReplace = false) {
    let validFiles = [];
    for (let f of Array.from(fileList)) {
        if (f.size > 10 * 1024 * 1024) {
            API.showToast('File "' + f.name + '" exceeds the 10MB limit and was ignored.', 'danger');
            continue;
        }
        validFiles.push(f);
    }
    if (validFiles.length === 0) return;

    if (isReplace && fileToReplace) {
        if (fileToReplace.docKey === key) {
            const oldFile = checklistFileMap[fileToReplace.docKey][fileToReplace.index];
            const newFile = validFiles[0];
            
            // 7. Replace confirmation dialog
            API.confirmAction(
                'Replace File Confirmation',
                `Are you sure you want to replace "${oldFile.name}" with "${newFile.name}"?`,
                'Yes, Replace'
            ).then(isConfirmed => {
                if (isConfirmed) {
                    // Remove old
                    const removed = checklistFileMap[fileToReplace.docKey].splice(fileToReplace.index, 1)[0];
                    const poolIdx = selectedChecklistFiles.indexOf(removed);
                    if (poolIdx > -1) selectedChecklistFiles.splice(poolIdx, 1);
                    
                    // Add new
                    simulateUploadAndAddFiles(fileToReplace.docKey, [newFile]);
                }
                fileToReplace = null;
            });
            return;
        } else {
            fileToReplace = null;
        }
    }

    simulateUploadAndAddFiles(key, validFiles);
}

function simulateUploadAndAddFiles(key, validFiles) {
    // 4. Micro-animations: Show progress bar
    const card = document.getElementById(`card-${key}`);
    const progressContainer = document.getElementById(`progress-${key}`);
    const progressBar = progressContainer.querySelector('.progress-bar');
    const progressText = progressContainer.querySelector('.progress-percent-text');
    const visualBtn = card.querySelector('.attach-btn-visual');
    const btnText = visualBtn.querySelector('.btn-text');
    const btnIcon = visualBtn.querySelector('.bi');
    
    // Hide files temporarily during "upload"
    const filesContainer = document.getElementById(`files-${key}`);
    if (filesContainer) filesContainer.classList.add('d-none');
    
    progressContainer.classList.remove('d-none');
    
    // Button to spinner animation
    btnIcon.className = 'spinner-border spinner-border-sm me-1';
    btnText.textContent = 'Uploading...';
    visualBtn.className = 'btn btn-outline-primary btn-sm py-1 px-3 fs-9 attach-btn-visual position-relative';
    visualBtn.style.zIndex = '11';

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.floor(Math.random() * 20) + 10;
        if (progress > 100) progress = 100;
        progressBar.style.width = progress + '%';
        progressText.textContent = progress + '%';
        
        if (progress === 100) {
            clearInterval(interval);
            setTimeout(() => {
                if (!checklistFileMap[key]) checklistFileMap[key] = [];
                validFiles.forEach(f => {
                    checklistFileMap[key].push(f);
                    selectedChecklistFiles.push(f);
                });
                persistChecklistFiles();
                renderChecklist(); // Re-render to show new files and completed status
            }, 300); // Small delay to let user see 100%
        }
    }, 100);
}

function wireAttachEvents() {
    document.querySelectorAll('.doc-file-input').forEach(input => {
        input.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            input.closest('.doc-card').classList.add('border-primary', 'shadow');
        });
        input.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            input.closest('.doc-card').classList.remove('border-primary', 'shadow');
        });
        input.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            input.closest('.doc-card').classList.remove('border-primary', 'shadow');
            const key = input.getAttribute('data-doc-key');
            if (e.dataTransfer.files.length > 0) {
                processFiles(key, e.dataTransfer.files, !!fileToReplace);
            }
        });
        input.addEventListener('click', (e) => {
            // If this input click is not programmatically triggered by a Replace action,
            // we must clear the replace state to prevent it from leaking to other cards.
            if (input.dataset.isReplacing !== "true") {
                fileToReplace = null;
            }
            // Reset the flag
            delete input.dataset.isReplacing;
        });
        input.addEventListener('cancel', () => {
            fileToReplace = null;
            delete input.dataset.isReplacing;
        });
        input.addEventListener('change', (e) => {
            const key = input.getAttribute('data-doc-key');
            if (input.files.length > 0) {
                processFiles(key, input.files, !!fileToReplace);
                input.value = ''; // Reset input so same file can be selected again
            }
        });
    });
}

window.removeChecklistFile = function(docKey, index, e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const removed = checklistFileMap[docKey].splice(index, 1)[0];
    if (removed) {
        const poolIdx = selectedChecklistFiles.indexOf(removed);
        if (poolIdx > -1) selectedChecklistFiles.splice(poolIdx, 1);
    }
    persistChecklistFiles();
    renderChecklist();
};

window.triggerReplace = function(docKey, index, e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    fileToReplace = { docKey, index };
    const card = document.getElementById(`card-${docKey}`);
    const input = card.querySelector('.doc-file-input');
    input.dataset.isReplacing = "true";
    input.click(); // Trigger file dialog
};

window.previewFile = function(docKey, index, e) {
    if (e) { e.preventDefault(); e.stopPropagation(); }
    const file = checklistFileMap[docKey][index];
    if (file) {
        const objectUrl = URL.createObjectURL(file);
        window.open(objectUrl, '_blank');
        setTimeout(() => URL.revokeObjectURL(objectUrl), 60000); // cleanup
    }
};

function updateSubmitButtonState() {
    const submitBtn = document.getElementById('resubmitDocumentsForm').querySelector('button[type="submit"]');
    // 12. Disable submit until all required docs uploaded.
    // Recalculate from checklistFileMap directly to avoid any stale variable issues.
    const uploaded = checklistRequiredDocs.filter(d => (checklistFileMap[makeDocKey(d)] || []).length > 0).length;
    const total = checklistRequiredDocs.length;
    
    // Keep globals in sync
    totalRequiredDocs = total;
    uploadedRequiredDocs = uploaded;

    if (uploaded < total) {
        const remaining = total - uploaded;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `Missing ${remaining} Required Document${remaining > 1 ? 's' : ''}`;
    } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = CURRENT_STAGE === 'liquidation' 
            ? 'Submit Liquidation Documentary Requirements' 
            : 'Submit Mandatory Documentary Requirements';
    }
}

loadCachedFiles();
bindStaticInputsPersistence();

async function handleResubmit(e) {
    e.preventDefault();
    const form = document.getElementById('resubmitDocumentsForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Build labels array matching the order of checklist files
    const labels = [];
    selectedChecklistFiles.forEach((file) => {
        let label = file.name;
        for (const [docKey, files] of Object.entries(checklistFileMap)) {
            if (files.includes(file)) {
                label = docKey.split('__')[0].replace(/_/g, ' ');
                break;
            }
        }
        labels.push(label);
    });

    // Validate size on other static file inputs
    const staticInputs = form.querySelectorAll('input[type="file"]:not(.doc-file-input)');
    let valid = true;
    staticInputs.forEach(input => {
        if (input.files.length > 0) {
            Array.from(input.files).forEach(f => {
                if (f.size > 10 * 1024 * 1024) {
                    valid = false;
                    API.showToast(`File "${f.name}" exceeds the 10MB limit.`, 'danger');
                }
            });
        }
    });

    if (!valid) return;

    // Build FormData from the form
    const formData = new FormData(form);

    // Append checklist files from JS memory
    if (selectedChecklistFiles.length > 0) {
        for (let i = 0; i < selectedChecklistFiles.length; i++) {
            formData.append('checklist_files[]', selectedChecklistFiles[i]);
        }
    }

    // Append attachment labels JSON
    formData.append('attachment_labels_json', JSON.stringify(labels));

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    try {
        const response = await fetch('<?php echo env('APP_URL'); ?>/api/transactions/resubmit-documents.php', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            clearCacheForTransaction();
            API.showToast('Documents submitted successfully! The transaction has been routed to Accounting Support for Document Inspection.', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=' + encodeURIComponent(data.tracking_number);
            }, 1500);
        } else {
            API.showToast(data.message || 'Submission failed.', 'danger');
            updateSubmitButtonState();
        }
    } catch (err) {
        API.showToast('Network error during submission. Please try again.', 'danger');
        updateSubmitButtonState();
    }
}

// File list display for general attachments with Max Size validation
document.getElementById('attachment')?.addEventListener('change', function() {
    const container = document.getElementById('fileListContainer');
    const list = document.getElementById('selectedFilesList');
    list.innerHTML = '';
    
    // Max size validation
    const dt = new DataTransfer();
    let hasError = false;

    if (this.files.length > 0) {
        for (let i = 0; i < this.files.length; i++) {
            const f = this.files[i];
            if (f.size > 10 * 1024 * 1024) {
                API.showToast(`General attachment "${f.name}" exceeds the 10MB limit and was ignored.`, 'danger');
                hasError = true;
                continue;
            }
            dt.items.add(f);
        }
        
        if (hasError) {
            this.files = dt.files;
        }

        if (this.files.length > 0) {
            container.classList.remove('d-none');
            for (let i = 0; i < this.files.length; i++) {
                const f = this.files[i];
                const sizeMB = (f.size / (1024 * 1024)).toFixed(1);
                list.innerHTML += `<div class="list-group-item p-2 d-flex justify-content-between align-items-center fs-9">
                    <span><i class="bi bi-file-earmark me-2 text-primary"></i>${escapeHtml(f.name)}</span>
                    <span class="text-muted">${sizeMB} MB</span>
                </div>`;
            }
        } else {
            container.classList.add('d-none');
        }
    } else {
        container.classList.add('d-none');
    }
    persistStaticFile('attachment[]', this.files);
});
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
