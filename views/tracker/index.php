<?php
/**
 * Transaction Progress Tracker Timeline View for SDO FAST.
 */

$currentPage = 'tracker';
$pageTitle = 'Progress Tracker';
$pageHeader = 'Progress Tracker';

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/document_checklist.php';

// Ensure transaction_documents table exists (Self-healing)
if ($fastPDO !== null) {
    try {
        $fastPDO->exec("
            CREATE TABLE IF NOT EXISTS transaction_documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_id INT NOT NULL,
                category VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                file_size INT NOT NULL DEFAULT 0,
                uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
                INDEX idx_tx_id (transaction_id),
                INDEX idx_cat (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (\Exception $e) {
        error_log("Failed to create transaction_documents table: " . $e->getMessage());
    }
}

// Handle checklist document upload in SDO-FAST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_checklist_file' && $fastPDO !== null) {
    $transactionId = (int)($_POST['transaction_id'] ?? 0);
    $categorySlug = trim($_POST['category_slug'] ?? '');
    $validCategories = ['purchase_request', 'memorandum', 'activity_proposal', 'saro'];
    
    // Redirect back URL helper
    $trackingParam = trim($_POST['tracking_number'] ?? '');
    $redirectUrl = env('APP_URL') . '/views/tracker/index.php?tracking=' . urlencode($trackingParam);
    
    if ($transactionId > 0 && in_array($categorySlug, $validCategories, true)) {
        if (isset($_FILES['checklist_file']) && $_FILES['checklist_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['checklist_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash_error'] = 'File upload failed with error code: ' . $file['error'];
            } else {
                $maxSize = 5 * 1024 * 1024; // 5MB
                if ($file['size'] > $maxSize) {
                    $_SESSION['flash_error'] = 'File size exceeds maximum allowed (5MB).';
                } else {
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                    if (!in_array($extension, $allowedExtensions, true)) {
                        $_SESSION['flash_error'] = 'Only PDF, JPG, and PNG files are allowed.';
                    } else {
                        // Create upload directory
                        $uploadDir = __DIR__ . '/../../uploads/procurement-docs/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        // Generate filename: {$transactionId}_{$category_slug}_{$timestamp}.{$ext}
                        $timestamp = time();
                        $filename = "{$transactionId}_{$categorySlug}_{$timestamp}.{$extension}";
                        $filePath = 'uploads/procurement-docs/' . $filename;
                        $fullPath = $uploadDir . $filename;
                        
                        try {
                            // Find existing document for transaction & category
                            $stmt = $fastPDO->prepare("SELECT id, file_path FROM transaction_documents WHERE transaction_id = ? AND category = ?");
                            $stmt->execute([$transactionId, $categorySlug]);
                            $existing = $stmt->fetch();
                            
                            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                                if ($existing) {
                                    $oldFullPath = __DIR__ . '/../../' . $existing['file_path'];
                                    if (file_exists($oldFullPath)) {
                                        @unlink($oldFullPath);
                                    }
                                    $updateStmt = $fastPDO->prepare("UPDATE transaction_documents SET file_path = ?, original_name = ?, file_size = ?, uploaded_at = NOW() WHERE id = ?");
                                    $updateStmt->execute([$filePath, $file['name'], $file['size'], $existing['id']]);
                                } else {
                                    $insertStmt = $fastPDO->prepare("INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size) VALUES (?, ?, ?, ?, ?)");
                                    $insertStmt->execute([$transactionId, $categorySlug, $filePath, $file['name'], $file['size']]);
                                }
                                $_SESSION['flash_success'] = 'Document uploaded successfully.';
                            } else {
                                $_SESSION['flash_error'] = 'Failed to save uploaded file.';
                            }
                        } catch (Exception $e) {
                            $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
                        }
                    }
                }
            }
        } else {
            $_SESSION['flash_error'] = 'Please select a file.';
        }
    } else {
        $_SESSION['flash_error'] = 'Invalid parameters.';
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// Handle checklist document delete in SDO-FAST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_checklist_file' && $fastPDO !== null) {
    $transactionId = (int)($_POST['transaction_id'] ?? 0);
    $categorySlug = trim($_POST['category_slug'] ?? '');
    $validCategories = ['purchase_request', 'memorandum', 'activity_proposal', 'saro'];
    
    $trackingParam = trim($_POST['tracking_number'] ?? '');
    $redirectUrl = env('APP_URL') . '/views/tracker/index.php?tracking=' . urlencode($trackingParam);
    
    if ($transactionId > 0 && in_array($categorySlug, $validCategories, true)) {
        try {
            $stmt = $fastPDO->prepare("SELECT id, file_path FROM transaction_documents WHERE transaction_id = ? AND category = ?");
            $stmt->execute([$transactionId, $categorySlug]);
            $existing = $stmt->fetch();
            if ($existing) {
                $oldFullPath = __DIR__ . '/../../' . $existing['file_path'];
                if (file_exists($oldFullPath)) {
                    @unlink($oldFullPath);
                }
                $delStmt = $fastPDO->prepare("DELETE FROM transaction_documents WHERE id = ?");
                $delStmt->execute([$existing['id']]);
                $_SESSION['flash_success'] = 'Checklist document removed.';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
        }
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// Handle status sync to BACtrack from detail page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'sync_to_bac' && $fastPDO !== null) {
    $transactionId = (int)($_POST['transaction_id'] ?? 0);
    
    $trackingParam = trim($_POST['tracking_number'] ?? '');
    $redirectUrl = env('APP_URL') . '/views/tracker/index.php?tracking=' . urlencode($trackingParam);
    
    if ($transactionId > 0) {
        try {
            // Check if Purchase Request is uploaded
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transaction_documents WHERE transaction_id = ? AND category = 'purchase_request'");
            $stmt->execute([$transactionId]);
            $hasPr = ($stmt->fetchColumn() > 0);
            
            if (!$hasPr) {
                $_SESSION['flash_error'] = 'Purchase Request document is required before sending.';
            } else {
                $stmt = $fastPDO->prepare("SELECT * FROM transactions WHERE id = ? LIMIT 1");
                $stmt->execute([$transactionId]);
                $tx = $stmt->fetch();
                
                if ($tx) {
                    require_once __DIR__ . '/../../services/BacIntegrationService.php';
                    $success = BacIntegrationService::syncStatusToBac(
                        $transactionId, 
                        $tx['current_status'], 
                        $tx['remarks'] ?? 'Sent status update from FAST Tracker detail page.', 
                        '', 
                        $fastPDO
                    );
                    
                    if ($success) {
                        $_SESSION['flash_success'] = 'Workflow status synced to SDO-BACtrack successfully.';
                    } else {
                        $_SESSION['flash_error'] = 'Status synchronization failed. Check integration monitor logs.';
                    }
                } else {
                    $_SESSION['flash_error'] = 'Transaction not found.';
                }
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
        }
    }
    header('Location: ' . $redirectUrl);
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$searchQuery = trim($_GET['tracking'] ?? '');
$transaction = null;
$logs = [];
$errorMsg = null;

if (!empty($searchQuery) && $fastPDO !== null) {
    try {
        // Fetch transaction details
        $stmt = $fastPDO->prepare("
            SELECT t.*, u.full_name as requestor_name, u.email as requestor_email, 
                   d.dv_number, d.bir_2307_number, d.tax_type, d.attachment_path,
                   cad.category as cash_advance_category, cad.inclusive_dates as ca_inclusive_dates, cad.fund_source, cad.venue as ca_venue,
                   cad.approved_ta_path as ca_approved_ta_path, cad.travel_itinerary_path as ca_travel_itinerary_path, cad.activity_proposal_path as ca_activity_proposal_path,
                   rd.category as reimbursement_category, rd.reimbursement_month, rd.inclusive_dates as reimb_inclusive_dates, rd.venue as reimb_venue,
                   rd.approved_ta_path as reimb_approved_ta_path, rd.travel_itinerary_path as reimb_travel_itinerary_path, rd.activity_proposal_path as reimb_activity_proposal_path,
                   rd.dtr_path, rd.certificate_path, rd.bill_proof_path
            FROM transactions t
            LEFT JOIN users u ON t.requestor_id = u.id
            LEFT JOIN document_details d ON t.id = d.transaction_id
            LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
            LEFT JOIN reimbursement_details rd ON t.id = rd.transaction_id
            WHERE t.tracking_number = :tracking
            LIMIT 1
        ");
        $stmt->execute(['tracking' => $searchQuery]);
        $transaction = $stmt->fetch();

        if ($transaction) {
            if ($transaction['transaction_type'] === 'BACtrack' && !hasPermission('view_bactrack')) {
                $transaction = null;
                $errorMsg = "Access denied: Your role does not permit viewing BACtrack Transactions.";
            } else {
                // Fetch checklist documents for this transaction
                $stmtDocs = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ?");
                $stmtDocs->execute([$transaction['id']]);
                $uploadedDocs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
                $uploadedByCat = [];
                foreach ($uploadedDocs as $doc) {
                    $uploadedByCat[$doc['category']] = $doc;
                }

                // Fetch status logs
                $logStmt = $fastPDO->prepare("
                    SELECT l.*, u.full_name as changer_name, u.email as changer_email, r.role_name as changer_role
                    FROM transaction_status_logs l
                    LEFT JOIN users u ON l.changed_by = u.id
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    WHERE l.transaction_id = :id
                    ORDER BY l.created_at ASC
                ");
                $logStmt->execute(['id' => $transaction['id']]);
                $logs = $logStmt->fetchAll();

                // Fetch attachment approvals
                $stmtAtt = $fastPDO->prepare("SELECT * FROM attachment_approvals WHERE transaction_id = ?");
                $stmtAtt->execute([$transaction['id']]);
                $attachmentsList = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);

                // Fetch budget check
                $stmtBc = $fastPDO->prepare("SELECT bc.*, u.full_name as checker_name FROM budget_checks bc LEFT JOIN users u ON bc.checked_by = u.id WHERE bc.transaction_id = ? LIMIT 1");
                $stmtBc->execute([$transaction['id']]);
                $budgetCheckDetails = $stmtBc->fetch(PDO::FETCH_ASSOC) ?: null;

                // Fetch signatory tasks
                $stmtSt = $fastPDO->prepare("SELECT st.*, u.full_name as completed_by_name FROM signatory_tasks st LEFT JOIN users u ON st.completed_by = u.id WHERE st.transaction_id = ?");
                $stmtSt->execute([$transaction['id']]);
                $signatoryTasksDetails = $stmtSt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $errorMsg = "No transaction record matches tracking number: '" . htmlspecialchars($searchQuery) . "'.";
        }
    } catch (PDOException $e) {
        error_log("Tracker database failure: " . $e->getMessage());
        $errorMsg = "A database query error occurred while searching.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <!-- Tracker Search Panel -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-2">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="tracking" class="form-control border-start-0" placeholder="Enter SDO FAST Tracking Number (e.g. FAST-2026-000001)..." value="<?php echo htmlspecialchars($searchQuery); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100 justify-content-center">Track Transaction</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div><?php echo $errorMsg; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($transaction): 
            // Determine active step index to highlight timeline
            $statusList = ['Pending ACCTG Support', 'Pending Budget', 'Pending ACCT Support', 'Pending Signatories', 'Pending Cashier Release', 'Released'];
            if ($transaction['current_status'] === 'Rejected') {
                $statusList[5] = 'Rejected';
            } elseif ($transaction['current_status'] === 'Returned') {
                $statusList[5] = 'Returned';
            }
            
            $currentStatus = $transaction['current_status'];
            $activeStepIdx = array_search($currentStatus, $statusList);
            if ($activeStepIdx === false) {
                $activeStepIdx = 0; // Fallback
            }
        ?>
            <!-- Transaction Details Summary Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary-dark">Tracking Details - <?php echo htmlspecialchars($transaction['tracking_number']); ?></h5>
                    <span class="badge badge-status <?php 
                        switch($currentStatus) {
                            case 'Released': echo 'bg-success'; break;
                            case 'Rejected': echo 'bg-danger'; break;
                            case 'Returned': echo 'bg-dark'; break;
                            default: echo 'bg-warning text-dark'; break;
                        }
                    ?> py-2 px-3"><?php echo htmlspecialchars($currentStatus); ?></span>
                </div>
                <div class="card-body">
                    <div class="row g-3 fs-8">
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Particulars / Event Name</span>
                            <strong class="fs-7 text-dark"><?php echo htmlspecialchars($transaction['event_name']); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Disbursement Type</span>
                            <strong class="text-dark">
                                <?php 
                                    echo htmlspecialchars($transaction['transaction_type']); 
                                    if ($transaction['transaction_type'] === 'Cash Advance' && !empty($transaction['cash_advance_category'])) {
                                        echo ' (' . htmlspecialchars($transaction['cash_advance_category']) . ')';
                                    } elseif ($transaction['transaction_type'] === 'Reimbursement' && !empty($transaction['reimbursement_category'])) {
                                        echo ' (' . htmlspecialchars($transaction['reimbursement_category']) . ')';
                                    }
                                ?>
                            </strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Submitted By</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['requestor_name']); ?></strong>
                            <small class="text-muted d-block fs-9"><?php echo htmlspecialchars($transaction['requestor_email']); ?></small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Gross Amount</span>
                            <strong class="fs-7 text-dark">₱<?php echo number_format($transaction['amount'], 2); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Tax Deduction (<?php echo htmlspecialchars($transaction['tax_type'] ?: 'Pending Classification'); ?>)</span>
                            <strong class="text-danger">₱<?php echo number_format($transaction['tax_amount'], 2); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Net Payout Amount</span>
                            <strong class="fs-6 text-primary-dark">₱<?php echo number_format($transaction['net_amount'], 2); ?></strong>
                        </div>
                        
                        <hr class="my-3">
                        
                        <!-- Document Specifics -->
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Disbursement Voucher (DV) No.</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['dv_number'] ?: 'Not Assigned'); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">BIR 2307 Ref No.</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['bir_2307_number'] ?: 'Not Assigned'); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Supporting Attachment(s)</span>
                            <?php 
                            if ($transaction['attachment_path']): 
                                $attachments = json_decode($transaction['attachment_path'], true);
                                if (!is_array($attachments)) {
                                    $attachments = [$transaction['attachment_path']];
                                }
                                foreach ($attachments as $att):
                                    $fileName = basename($att);
                            ?>
                                <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($att); ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 mt-1 d-inline-flex align-items-center gap-2 me-1 mb-1">
                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                    <span>Download <?php echo htmlspecialchars($fileName); ?></span>
                                </a>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <span class="text-muted d-block fs-8 mt-1"><i class="bi bi-file-earmark-excel"></i> No files uploaded</span>
                            <?php endif; ?>
                        </div>

                        <!-- Cash Advance Subcategory Details -->
                        <?php if ($transaction['transaction_type'] === 'Cash Advance' && !empty($transaction['cash_advance_category'])): 
                            $caCat = $transaction['cash_advance_category'];
                            $caDateVenueTypes = ['Travel', 'Training', 'Meals', 'Accommodation', 'Meals and Accommodation', 'SLAC / Moving-Up / Graduation / GAWAD'];
                            $caFundSourceTypes = ['Travel', 'School MOOE', 'SBFP'];
                            $caTaTypes = ['Travel'];
                            $caActivityTypes = ['Training', 'SLAC / Moving-Up / Graduation / GAWAD'];
                        ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3 bg-light border">
                                    <h6 class="fw-bold text-primary-dark mb-3 fs-8 text-uppercase"><i class="bi bi-info-circle-fill me-1 text-primary"></i>Cash Advance Specifics (<?php echo htmlspecialchars($caCat); ?>)</h6>
                                    <div class="row g-3">
                                        <?php if (in_array($caCat, $caDateVenueTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Inclusive Dates</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['ca_inclusive_dates'] ?: 'N/A'); ?></strong>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Venue</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['ca_venue'] ?: 'N/A'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($caCat, $caFundSourceTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Fund Source</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['fund_source'] ?: 'N/A'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($caCat, $caTaTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Approved TA (Travel Authority)</small>
                                                <?php if ($transaction['ca_approved_ta_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['ca_approved_ta_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Approved TA</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Travel Itinerary</small>
                                                <?php if ($transaction['ca_travel_itinerary_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['ca_travel_itinerary_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Travel Itinerary</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($caCat, $caActivityTypes, true)): ?>
                                            <div class="col-12">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Activity Proposal</small>
                                                <?php if ($transaction['ca_activity_proposal_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['ca_activity_proposal_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Activity Proposal</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php $caChecklistHtml = renderDocumentChecklistHtml('Cash Advance', $caCat); ?>
                                    <?php if ($caChecklistHtml !== ''): ?>
                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="fw-bold text-primary-dark mb-2 fs-8 d-flex align-items-center gap-2">
                                                <i class="bi bi-clipboard2-check text-primary"></i>
                                                <span>Documents Checklist (DM 214)</span>
                                            </h6>
                                            <?php echo $caChecklistHtml; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Reimbursement Subcategory Details -->
                        <?php if ($transaction['transaction_type'] === 'Reimbursement' && !empty($transaction['reimbursement_category'])): 
                            $reimbCat = $transaction['reimbursement_category'];
                            $reimbDateVenueTypes = ['Travel', 'Meals', 'Accommodation', 'Meals and Accommodation', 'Seminars / Trainings', 'GAD Documents / SLAC Session'];
                            $reimbTaTypes = ['Travel'];
                            $reimbActivityTypes = ['Seminars / Trainings'];
                            $reimbCommTypes = ['Communication Load'];
                            $reimbUtilityTypes = ['Utility Bills'];
                        ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3 bg-light border">
                                    <h6 class="fw-bold text-primary-dark mb-3 fs-8 text-uppercase"><i class="bi bi-info-circle-fill me-1 text-primary"></i>Reimbursement Specifics (<?php echo htmlspecialchars($reimbCat); ?>)</h6>
                                    <div class="row g-3">
                                        <?php if (in_array($reimbCat, $reimbDateVenueTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Inclusive Dates</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['reimb_inclusive_dates'] ?: 'N/A'); ?></strong>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Venue</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['reimb_venue'] ?: 'N/A'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($reimbCat, $reimbTaTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Approved TA (Travel Authority)</small>
                                                <?php if ($transaction['reimb_approved_ta_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['reimb_approved_ta_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Approved TA</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Travel Itinerary</small>
                                                <?php if ($transaction['reimb_travel_itinerary_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['reimb_travel_itinerary_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Travel Itinerary</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($reimbCat, $reimbActivityTypes, true)): ?>
                                            <div class="col-12">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Activity Proposal</small>
                                                <?php if ($transaction['reimb_activity_proposal_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['reimb_activity_proposal_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Activity Proposal</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($reimbCat, $reimbCommTypes, true) || $reimbCat === 'Communications Allowance'): ?>
                                            <div class="col-12 mb-2">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Allowance Month</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['reimbursement_month'] ?: 'N/A'); ?></strong>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">DTR Document</small>
                                                <?php if ($transaction['dtr_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['dtr_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View DTR</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Certificate Document</small>
                                                <?php if ($transaction['certificate_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['certificate_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Certificate</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Bill / Proof of Payment</small>
                                                <?php if ($transaction['bill_proof_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['bill_proof_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Bill / Proof</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (in_array($reimbCat, $reimbUtilityTypes, true)): ?>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Utility Month</small>
                                                <strong class="text-dark fs-8 d-block"><?php echo htmlspecialchars($transaction['reimbursement_month'] ?: 'N/A'); ?></strong>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <small class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 0.7rem;">Bill / Proof of Payment</small>
                                                <?php if ($transaction['bill_proof_path']): ?>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['bill_proof_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center gap-2">
                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                        <span>View Bill / Proof</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fs-8">No document uploaded</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php 
                                    $reimbLookupCat = ($reimbCat === 'Communications Allowance') ? 'Communication Load' : $reimbCat;
                                    $reimbChecklistHtml = renderDocumentChecklistHtml('Reimbursement', $reimbLookupCat); 
                                    ?>
                                    <?php if ($reimbChecklistHtml !== ''): ?>
                                        <div class="mt-4 pt-3 border-top">
                                            <h6 class="fw-bold text-primary-dark mb-2 fs-8 d-flex align-items-center gap-2">
                                                <i class="bi bi-clipboard2-check text-primary"></i>
                                                <span>Documents Checklist (DM 214)</span>
                                            </h6>
                                            <?php echo $reimbChecklistHtml; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Stage 2 Attachment Approvals Detail -->
                        <?php if (!empty($attachmentsList)): ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3 bg-white border">
                                    <h6 class="fw-bold text-primary-dark mb-3 fs-8 text-uppercase"><i class="bi bi-paperclip me-1 text-primary"></i>Attachment Approval Reviews (Stage 2)</h6>
                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                        <?php foreach ($attachmentsList as $att): ?>
                                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 fs-8">
                                                <div>
                                                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($att['file_label']); ?></span>
                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($att['file_path']); ?>" target="_blank" class="fs-9 text-primary text-decoration-none"><i class="bi bi-file-earmark-arrow-down me-1"></i><?php echo basename($att['file_path']); ?></a>
                                                    <?php if (!empty($att['remarks'])): ?>
                                                        <div class="text-muted fs-9 mt-1">Remarks: <em><?php echo htmlspecialchars($att['remarks']); ?></em></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <?php if ($att['status'] === 'approved'): ?>
                                                        <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                    <?php elseif ($att['status'] === 'rejected'): ?>
                                                        <span class="badge bg-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Stage 3 Budget Allocation Details -->
                        <?php if ($budgetCheckDetails): ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3 bg-white border">
                                    <h6 class="fw-bold text-primary-dark mb-3 fs-8 text-uppercase"><i class="bi bi-bank2 me-1 text-primary"></i>Budget Check Allocation (Stage 3)</h6>
                                    <div class="row g-3 fs-8">
                                        <div class="col-12 col-sm-6">
                                            <span class="text-muted d-block">Fund Source Allocated</span>
                                            <strong class="text-dark"><?php echo htmlspecialchars($budgetCheckDetails['fund_source']); ?></strong>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <span class="text-muted d-block">Fund Source Tracking Number</span>
                                            <strong class="text-dark"><?php echo htmlspecialchars($budgetCheckDetails['fund_source_tracking_number'] ?: 'N/A'); ?></strong>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <span class="text-muted d-block">Fund Availability Verified</span>
                                            <?php if ($budgetCheckDetails['fund_available']): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Not Available</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12">
                                            <span class="text-muted d-block">Budget Verification Remarks</span>
                                            <p class="text-dark mb-0 bg-light p-2 rounded border-start border-3 border-info">
                                                <?php echo htmlspecialchars($budgetCheckDetails['remarks'] ?: 'No remarks recorded.'); ?>
                                            </p>
                                            <small class="text-muted">Checked by: <strong><?php echo htmlspecialchars($budgetCheckDetails['checker_name'] ?: 'System'); ?></strong> on <?php echo date('M d, Y h:i A', strtotime($budgetCheckDetails['checked_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Stage 5 Signatory Tasks Details -->
                        <?php if (!empty($signatoryTasksDetails)): ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 rounded-3 bg-white border">
                                    <h6 class="fw-bold text-primary-dark mb-3 fs-8 text-uppercase"><i class="bi bi-check2-square me-1 text-primary"></i>Signatory Document Verification (Stage 5)</h6>
                                    <div class="row g-3">
                                        <?php foreach ($signatoryTasksDetails as $st): 
                                            $taskLabel = ($st['task_type'] === 'payroll_prep') ? 'Payroll Prep & Signatures' : 'DV/ORS Prep & Signatures';
                                        ?>
                                            <div class="col-12 col-md-6">
                                                <div class="p-3 bg-light rounded-3 border h-100 fs-8 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong class="text-primary-dark"><?php echo $taskLabel; ?></strong>
                                                            <?php if ($st['status'] === 'completed'): ?>
                                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Completed</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-hourglass me-1"></i>Pending</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($st['status'] === 'completed'): ?>
                                                            <div class="text-muted fs-9">
                                                                Completed by: <strong><?php echo htmlspecialchars($st['completed_by_name'] ?: 'System'); ?></strong> on <?php echo date('M d, Y h:i A', strtotime($st['completed_at'])); ?>
                                                            </div>
                                                            <?php if ($st['document_path']): ?>
                                                                <div class="mt-2">
                                                                    <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($st['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2 d-inline-flex align-items-center gap-1 fs-9">
                                                                        <i class="bi bi-file-earmark-check-fill"></i>
                                                                        <span>View Signed Document</span>
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($st['remarks']): ?>
                                                                <div class="mt-2 text-dark bg-white p-2 rounded border">
                                                                    Remarks: <em><?php echo htmlspecialchars($st['remarks']); ?></em>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- BAC Reference info if synched -->
                        <?php if ($transaction['bac_reference_number']): ?>
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 bg-light d-flex align-items-center gap-3">
                                    <i class="bi bi-hdd-network-fill text-accent fs-3"></i>
                                    <div>
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">SDO Enterprise Sync</small>
                                        <span class="fs-8">Linked to SDO-BAC Procurement: <strong><?php echo htmlspecialchars($transaction['bac_reference_number']); ?></strong> (Procurement Type: <?php echo htmlspecialchars($transaction['bac_procurement_type'] ?: 'N/A'); ?>)</span>
                                        <?php if (!empty($transaction['approval_file_path'])): ?>
                                            <div class="mt-2">
                                                <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['approval_file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-file-earmark-check-fill"></i>
                                                    <span>View Proof of BAC Approval</span>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Procurement Checklist for Linked BAC Transactions -->
                            <?php 
                            try {
                                if ($fastPDO !== null) {
                                    $checkDocsStmt = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ?");
                                    $checkDocsStmt->execute([$transaction['id']]);
                                    $fastCheckDocs = $checkDocsStmt->fetchAll();
                                } else {
                                    $fastCheckDocs = [];
                                }
                            } catch (\Exception $ex) {
                                $fastCheckDocs = [];
                            }
                            $fastCheckDocsByCat = [];
                            foreach ($fastCheckDocs as $doc) {
                                $fastCheckDocsByCat[$doc['category']] = $doc;
                            }

                            $fastChecklistItems = [
                                'purchase_request' => [
                                    'title' => 'Purchase Request',
                                    'desc' => '3 original copies required',
                                    'required' => true
                                ],
                                'memorandum' => [
                                    'title' => 'Memorandum',
                                    'desc' => 'photocopy only, if applicable',
                                    'required' => false
                                ],
                                'activity_proposal' => [
                                    'title' => 'Activity or Project Proposal',
                                    'desc' => 'photocopy only, if applicable',
                                    'required' => false
                                ],
                                'saro' => [
                                    'title' => 'SARO',
                                    'desc' => 'photocopy only, if applicable',
                                    'required' => false
                                ]
                            ];
                            ?>
                            <div class="col-12 mt-4">
                                <div class="card shadow-sm border border-light rounded-3">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0 fw-bold text-primary-dark d-flex align-items-center gap-2">
                                            <i class="bi bi-clipboard-check fs-5 text-primary"></i>
                                            <span>Procurement Checklist Documents</span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted fs-8 mb-3">Please upload the required procurement documents. Purchase Request is mandatory before syncing this transaction status to SDO-BACtrack.</p>
                                        
                                        <div class="row g-3">
                                            <?php foreach ($fastChecklistItems as $slug => $item): 
                                                $uploadedDoc = $fastCheckDocsByCat[$slug] ?? null;
                                            ?>
                                                <div class="col-12 col-md-6">
                                                    <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                                        <div>
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <span class="fw-bold text-dark fs-8 d-flex align-items-center gap-1">
                                                                    <?php if ($uploadedDoc): ?>
                                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                                    <?php else: ?>
                                                                        <i class="bi bi-exclamation-circle-fill text-<?php echo $item['required'] ? 'danger' : 'secondary'; ?>"></i>
                                                                    <?php endif; ?>
                                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                                </span>
                                                                <span class="badge bg-<?php echo $item['required'] ? 'danger-subtle text-danger' : 'secondary-subtle text-secondary'; ?> fs-9">
                                                                    <?php echo $item['required'] ? 'Required' : 'Optional'; ?>
                                                                </span>
                                                            </div>
                                                            <small class="text-muted d-block mt-1 mb-2 fs-9"><?php echo htmlspecialchars($item['desc']); ?></small>
                                                            
                                                            <?php if ($uploadedDoc): ?>
                                                                <div class="mt-2 p-2 rounded bg-white border border-light d-flex align-items-center justify-content-between">
                                                                    <div class="text-truncate me-2" style="max-width: 70%;">
                                                                        <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($uploadedDoc['file_path']); ?>" target="_blank" class="fw-semibold text-primary fs-8 text-decoration-none text-truncate d-block">
                                                                            <i class="bi bi-file-earmark-arrow-down"></i> <?php echo htmlspecialchars($uploadedDoc['original_name']); ?>
                                                                        </a>
                                                                        <small class="text-muted fs-9"><?php echo number_format($uploadedDoc['file_size'] / 1024, 1); ?> KB</small>
                                                                    </div>
                                                                    <form method="POST" class="m-0">
                                                                        <input type="hidden" name="action" value="delete_checklist_file">
                                                                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                                                        <input type="hidden" name="category_slug" value="<?php echo $slug; ?>">
                                                                        <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($transaction['tracking_number']); ?>">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 py-1 px-2" onclick="API.confirmAction('Confirm Deletion', 'Delete this checklist file?', 'Delete').then(res => { if(res) this.closest('form').submit(); });" title="Delete Document">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <button type="button" class="btn btn-sm btn-outline-primary w-100 py-1" onclick="document.getElementById('upload-form-<?php echo $slug; ?>').classList.toggle('d-none');">
                                                                <i class="bi bi-upload me-1"></i> <?php echo $uploadedDoc ? 'Replace Document' : 'Upload Document'; ?>
                                                            </button>
                                                            
                                                            <div id="upload-form-<?php echo $slug; ?>" class="d-none mt-2 pt-2 border-top border-light">
                                                                <form method="POST" enctype="multipart/form-data" class="m-0">
                                                                    <input type="hidden" name="action" value="upload_checklist_file">
                                                                    <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                                                    <input type="hidden" name="category_slug" value="<?php echo $slug; ?>">
                                                                    <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($transaction['tracking_number']); ?>">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="file" name="checklist_file" accept=".pdf,.jpg,.jpeg,.png" required class="form-control form-control-sm">
                                                                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                                                    </div>
                                                                    <small class="text-muted fs-9 d-block mt-1">Accepts PDF, JPG, PNG only, max 5MB.</small>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Send to BACtrack Sync Option -->
                                        <div class="mt-4 p-3 rounded-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark fs-8">SDO-BACtrack Sync Portal</h6>
                                                <small class="text-muted fs-9">Send this transaction's current workflow status and remarks back to the SDO-BACtrack system.</small>
                                            </div>
                                            <form method="POST" class="m-0">
                                                <input type="hidden" name="action" value="sync_to_bac">
                                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                                <input type="hidden" name="tracking_number" value="<?php echo htmlspecialchars($transaction['tracking_number']); ?>">
                                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                                    <i class="bi bi-send me-1"></i> Send to BACtrack
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workflow Timeline Tracking -->
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold text-primary-dark">Workflow Auditing Timeline</h5>
                </div>
                <div class="card-body">
                    <!-- Timeline wrapper -->
                    <div class="position-relative py-3" style="padding-left: 45px;">
                        
                        <!-- Vertical line background -->
                        <div class="position-absolute" style="left: 17px; top: 0; bottom: 0; width: 4px; background-color: #e2e8f0; z-index: 1;"></div>
                        
                        <!-- Timeline items loop -->
                        <?php 
                        $knownSteps = [
                            'Pending ACCTG Support' => [
                                'title' => 'Disbursement Request Submitted',
                                'subtitle' => 'Disbursement request submitted, pending ACCTG Support check',
                                'role' => 'Requestor'
                            ],
                            'Pending Budget' => [
                                'title' => 'Endorsed to Budget Officer',
                                'subtitle' => 'ACCTG Support review complete, pending Budget Officer check',
                                'role' => 'ACCTG Support'
                            ],
                            'Pending ACCT Support' => [
                                'title' => 'Budget Check Completed',
                                'subtitle' => 'Fund availability verified, endorsed to ACCT Support',
                                'role' => 'Budget Officer'
                            ],
                            'Pending Signatories' => [
                                'title' => 'Endorsed to Signatories',
                                'subtitle' => 'Accounting documents processed, pending payroll/DV signatures',
                                'role' => 'ACCT Support'
                            ],
                            'Pending Cashier Release' => [
                                'title' => 'Signatory Endorsement Completed',
                                'subtitle' => 'Parallel signatory tasks complete, pending Cashier payment release',
                                'role' => 'Signatories'
                            ],
                            'Released' => [
                                'title' => 'Payment Released',
                                'subtitle' => 'Disbursement check or payment released by Cashier',
                                'role' => 'Cashier'
                            ],
                            'Rejected' => [
                                'title' => 'Disbursement Request Rejected',
                                'subtitle' => 'Request denied by audit staff',
                                'role' => 'System Reviewer'
                            ],
                            'Returned' => [
                                'title' => 'Returned to Requestor',
                                'subtitle' => 'Returned for corrections or missing attachments',
                                'role' => 'System Reviewer'
                            ]
                        ];
                        
                        // Map existing database logs to steps to render them
                        $renderedStepsCount = 0;
                        foreach ($logs as $logIdx => $log):
                            $logStatus = $log['new_status'];
                            $stepMeta = $knownSteps[$logStatus] ?? [
                                'title' => 'Workflow Status Changed',
                                'subtitle' => 'Status changed',
                                'role' => 'Staff'
                            ];
                            
                            $dateStr = date('M d, Y h:i A', strtotime($log['created_at']));
                            
                            // Determine status indicator color
                            $nodeBg = 'var(--color-primary)';
                            $nodeIcon = '<i class="bi bi-check text-white"></i>';
                            
                            if ($logStatus === 'Released') {
                                $nodeBg = '#28a745';
                                $nodeIcon = '<i class="bi bi-check2-all text-white fs-5"></i>';
                            } elseif ($logStatus === 'Rejected') {
                                $nodeBg = '#dc3545';
                                $nodeIcon = '<i class="bi bi-x text-white fs-5"></i>';
                            } elseif ($logStatus === 'Returned') {
                                $nodeBg = '#6c757d';
                                $nodeIcon = '<i class="bi bi-arrow-left text-white fs-5"></i>';
                            }
                            
                            $isLast = ($logIdx === count($logs) - 1);
                        ?>
                            <div class="mb-4 position-relative" style="z-index: 2;">
                                <!-- Circle Marker -->
                                <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" 
                                     style="left: -40px; top: 0; width: 28px; height: 28px; background-color: <?php echo $nodeBg; ?>; box-shadow: 0 0 0 6px #ffffff; z-index: 3;">
                                    <?php echo $nodeIcon; ?>
                                </div>
                                
                                <!-- Card content bubble -->
                                <div class="card p-3 mb-0 bg-white border border-light shadow-sm" style="margin-left: 5px;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-primary-dark"><?php echo htmlspecialchars($stepMeta['title']); ?></h6>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo $dateStr; ?></small>
                                    </div>
                                    <small class="text-primary-light fw-medium d-block mb-2">Processed By: <?php echo htmlspecialchars($log['changer_name'] ?: 'System Event'); ?> (<?php echo htmlspecialchars($log['changer_role'] ?: $stepMeta['role']); ?>)</small>
                                    
                                    <p class="text-muted mb-0 fs-8 p-2 rounded-2 bg-light border-start border-3" style="border-left-color: var(--color-primary) !important;">
                                        <strong>Remarks:</strong> <?php echo htmlspecialchars($log['remarks'] ?: 'No workflow audit remarks recorded.'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php 
                        $renderedStepsCount++;
                        endforeach; 
                        ?>
                        
                        <!-- RENDER REMAINING PENDING STEPS (If transaction is not yet final) -->
                        <?php 
                        if (!in_array($currentStatus, ['Released', 'Rejected', 'Returned'])) {
                            // Find which steps are still pending
                            $allExpectedSteps = ['Pending ACCTG Support', 'Pending Budget', 'Pending ACCT Support', 'Pending Signatories', 'Pending Cashier Release', 'Released'];
                            
                            // Find index of current state
                            $currIdx = array_search($currentStatus, $allExpectedSteps);
                            
                            if ($currIdx !== false) {
                                for ($i = $currIdx + 1; $i < count($allExpectedSteps); $i++) {
                                    $pendingStatus = $allExpectedSteps[$i];
                                    $stepMeta = $knownSteps[$pendingStatus];
                                    ?>
                                    <div class="mb-4 position-relative" style="z-index: 2; opacity: 0.5;">
                                        <!-- Circle Marker -->
                                        <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center bg-secondary" 
                                             style="left: -40px; top: 0; width: 28px; height: 28px; box-shadow: 0 0 0 6px #ffffff; z-index: 3;">
                                            <i class="bi bi-dash text-white"></i>
                                        </div>
                                        
                                        <!-- Card content bubble -->
                                        <div class="card p-3 mb-0 bg-white border border-light" style="margin-left: 5px;">
                                            <h6 class="mb-1 fw-semibold text-muted"><?php echo htmlspecialchars($stepMeta['title']); ?> (Pending)</h6>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($stepMeta['subtitle']); ?></small>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Initial Empty State -->
            <?php if (empty($searchQuery)): ?>
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <div class="text-primary-light mb-3">
                            <i class="bi bi-geo-alt-fill" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="fw-bold text-primary-dark">Disbursement Progress Tracker</h4>
                        <p class="text-muted mx-auto" style="max-width: 460px;">Provide a sequential FAST tracking code (e.g. <code>FAST-YYYY-000001</code>) in the search query above to review the real-time workflow audits and transaction approvals.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
