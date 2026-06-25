<?php
/**
 * Transaction Submission Processor API for SDO FAST.
 * Conducts tax computations, secure file uploads, and tracking code generation.
 *
 * Workflow v3: Initial submission goes directly to Budget Officer (Stage 1).
 * No documents/attachments are submitted at this stage.
 * Documents are uploaded separately after budget approval via resubmit-documents.php.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth & CSRF
require_once __DIR__ . '/../../services/TrackingNumberService.php';
require_once __DIR__ . '/../../services/AuditLogService.php';
require_once __DIR__ . '/../../services/CoverageCategoryService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Only allow Requestors, Personnel, and Super Admins to submit transactions
if (!in_array($userRole, ['Super Admin', 'Requestor']) && $userPosition !== 'Personnel') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
    exit;
}

if (empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    http_response_code(413);
    $maxSize = ini_get('post_max_size');
    echo json_encode(['success' => false, 'message' => "Total upload size exceeds the server's post_max_size ($maxSize). Please upload smaller or fewer files."]);
    exit;
}

// 1. Parse and sanitize POST parameters
$type = trim($_POST['transaction_type'] ?? '');
$eventName = trim($_POST['event_name'] ?? '');
$amount = (float)($_POST['amount'] ?? 0.00);
$taxType = isset($_POST['tax_type']) ? trim($_POST['tax_type']) : null;
if ($taxType === '') {
    $taxType = null;
}
$targetDate = trim($_POST['target_date'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

// Cash Advance specific fields
$cashAdvanceCategory = trim($_POST['cash_advance_category'] ?? '');
$mooeStartDate = trim($_POST['mooe_start_date'] ?? '');
$mooeEndDate = trim($_POST['mooe_end_date'] ?? '');
$fundSource = trim($_POST['fund_source'] ?? '');
$venue = trim($_POST['venue'] ?? '');
$caMonthValue = trim($_POST['ca_month'] ?? '');

// Reimbursement specific fields
$reimbursementCategory = trim($_POST['reimbursement_category'] ?? '');
$reimbursementMonth = trim($_POST['reimbursement_month'] ?? '');
$reimbStartDate = trim($_POST['reimb_start_date'] ?? '');
$reimbEndDate = trim($_POST['reimb_end_date'] ?? '');
$reimbVenue = trim($_POST['reimb_venue'] ?? '');
$utilityMonth = trim($_POST['utility_month'] ?? '');

// 2. Validate Inputs
if (empty($type) || empty($eventName) || $amount <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction type, event name, and amount are required.']);
    exit;
}

$allowedTypes = ['Cash Advance', 'Reimbursement', 'Payroll'];
if (!in_array($type, $allowedTypes)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid transaction type selected.']);
    exit;
}

// Cash Advance validations
$inclusiveDates = null;
if ($type === 'Cash Advance') {
    $caCategoryConfig = CoverageCategoryService::getCategoryByName($fastPDO, 'Cash Advance', $cashAdvanceCategory, true);
    if ($caCategoryConfig === null) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid Cash Advance Coverage Type is required.']);
        exit;
    }

    $caFields = $caCategoryConfig['field_config'];

    if (!empty($caFields['dateVenue'])) {
        if (empty($mooeStartDate) || empty($mooeEndDate) || empty($venue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Inclusive dates and venue are required for ' . $cashAdvanceCategory . '.']);
            exit;
        }
        $inclusiveDates = $mooeStartDate . ' to ' . $mooeEndDate;
    }

    if (!empty($caFields['month'])) {
        if (empty($caMonthValue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for ' . $cashAdvanceCategory . '.']);
            exit;
        }
    }
}

// Reimbursement validations
$reimbInclusiveDates = null;
if ($type === 'Reimbursement') {
    $reimbCategoryConfig = CoverageCategoryService::getCategoryByName($fastPDO, 'Reimbursement', $reimbursementCategory, true);
    if ($reimbCategoryConfig === null) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid Reimbursement Coverage Type is required.']);
        exit;
    }

    $reimbFields = $reimbCategoryConfig['field_config'];

    if (!empty($reimbFields['dateVenue'])) {
        if (empty($reimbStartDate) || empty($reimbEndDate) || empty($reimbVenue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Inclusive dates and venue are required for ' . $reimbursementCategory . '.']);
            exit;
        }
        $reimbInclusiveDates = $reimbStartDate . ' to ' . $reimbEndDate;
    }

    if (!empty($reimbFields['communications'])) {
        if (empty($reimbursementMonth)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for Communication Load.']);
            exit;
        }
    }

    if (!empty($reimbFields['utilityBills'])) {
        if (empty($utilityMonth)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for Utility Bills.']);
            exit;
        }
        $reimbursementMonth = $utilityMonth;
    }
}

// Fetch active tax configurations to validate and calculate tax
$taxPercentage = 0.00;
if ($taxType !== null) {
    try {
        $taxStmt = $fastPDO->prepare("SELECT tax_percentage FROM tax_configurations WHERE tax_type = :tax_type AND is_active = 1 LIMIT 1");
        $taxStmt->execute(['tax_type' => $taxType]);
        $taxPercentage = $taxStmt->fetchColumn();

        if ($taxPercentage === false) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid or inactive tax type selected.']);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Tax retrieval failure: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'System error during tax validation.']);
        exit;
    }
}

// 3. Compute Tax and Net Amount
$taxAmount = $amount * ($taxPercentage / 100);
$netAmount = $amount - $taxAmount;

// Helper function for secure file uploads
function handleSecureUpload($fileKey, $uploadDir) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fileKey];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File upload error code on ' . $fileKey . ': ' . $file['error']]);
        exit;
    }

    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Attached file for ' . $fileKey . ' exceeds the maximum limit of 10MB.']);
        exit;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
    if (!in_array($extension, $allowedExtensions)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Allowed file formats for ' . $fileKey . ': PDF, JPG, PNG, DOCX.']);
        exit;
    }

    // Robust MIME type detection with extension-based fallback
    $mimeType = null;
    $mimeMap = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    if (function_exists('finfo_open') && function_exists('finfo_file')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected = @finfo_file($finfo, $file['tmp_name']);
            if ($detected !== false) {
                $mimeType = $detected;
            }
            @finfo_close($finfo);
        }
    }
    if ($mimeType === null) {
        $mimeType = $mimeMap[$extension] ?? 'application/octet-stream';
    }

    $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Security check: Invalid file content type detected for ' . $fileKey . '.']);
        exit;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    $uploaded = defined('TEST_MODE') ? copy($file['tmp_name'], $targetPath) : move_uploaded_file($file['tmp_name'], $targetPath);
    if (!$uploaded) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file: ' . $fileKey]);
        exit;
    }
    
    return 'uploads/transactions/' . $filename;
}

// Workflow v3: No documents are uploaded at initial submission (Stage 1).
// All document uploads are deferred to Stage 3 via resubmit-documents.php.
// Skip all file processing for initial submission.
$attachmentPath = null;
$approvedTaPath = null;
$travelItineraryPath = null;
$activityProposalPath = null;
$dtrPath = null;
$certificatePath = null;
$billProofPath = null;
$reimbApprovedTaPath = null;
$reimbTravelItineraryPath = null;
$reimbActivityProposalPath = null;
$utilityBillProofPath = null;
$attachmentPaths = [];

// Block any file upload attempt at this stage
$hasFiles = false;
foreach ($_FILES as $fileKey => $fileInfo) {
    if (isset($fileInfo['error']) && is_array($fileInfo['error'])) {
        foreach ($fileInfo['error'] as $err) {
            if ($err !== UPLOAD_ERR_NO_FILE) $hasFiles = true;
        }
    } elseif (isset($fileInfo['error']) && $fileInfo['error'] !== UPLOAD_ERR_NO_FILE) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Document uploads are not allowed at this stage. Documents must be submitted after budget approval via the resubmit page.']);
    exit;
}

// 4. Secure File Upload Handler (kept for reference, not used at Stage 1)
$uploadDir = dirname(dirname(__DIR__)) . '/uploads/transactions/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 5. Database Insertion Workflow
try {
    $fastPDO->beginTransaction();

    // Generate tracking code sequentially with a concurrency lock
    $trackingNumber = TrackingNumberService::generate($fastPDO);
    $uuid = bin2hex(random_bytes(16)); // simple UUID format
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
    
    // Workflow v3: Initial submission routes directly to Budget Officer
    $status = 'Pending Budget';

    // Insert Transaction
    $insertTxSql = "
        INSERT INTO transactions (uuid, tracking_number, requestor_id, created_by, division_id, transaction_type, event_name, amount, tax_amount, net_amount, target_date, current_status, remarks) 
        VALUES (:uuid, :tracking_number, :requestor_id, :created_by, :division_id, :transaction_type, :event_name, :amount, :tax_amount, :net_amount, :target_date, :current_status, :remarks)
    ";
    
    $txStmt = $fastPDO->prepare($insertTxSql);
    $txStmt->execute([
        'uuid' => $uuid,
        'tracking_number' => $trackingNumber,
        'requestor_id' => $userId,
        'created_by' => $userId,
        'division_id' => null, // Nullable, default references user's office/division
        'transaction_type' => $type,
        'event_name' => $eventName,
        'amount' => $amount,
        'tax_amount' => $taxAmount,
        'net_amount' => $netAmount,
        'target_date' => empty($targetDate) ? null : $targetDate,
        'current_status' => $status,
        'remarks' => $remarks
    ]);

    $transactionDbId = $fastPDO->lastInsertId();

    // Insert Document details
    $insertDocSql = "
        INSERT INTO document_details (transaction_id, tax_type, attachment_path) 
        VALUES (:transaction_id, :tax_type, :attachment_path)
    ";
    $docStmt = $fastPDO->prepare($insertDocSql);
    $docStmt->execute([
        'transaction_id' => $transactionDbId,
        'tax_type' => $taxType,
        'attachment_path' => $attachmentPath
    ]);

    // Insert Cash Advance Details
    if ($type === 'Cash Advance') {
        // For Communication Expenses, store month in inclusive_dates field as a workaround
        $caInclusiveDates = $inclusiveDates;
        if ($cashAdvanceCategory === 'Communication Expenses' && !empty($caMonthValue)) {
            $caInclusiveDates = $caMonthValue; // Store month reference
        }

        $insertCaSql = "
            INSERT INTO cash_advance_details (transaction_id, category, inclusive_dates, fund_source, venue, approved_ta_path, travel_itinerary_path, activity_proposal_path) 
            VALUES (:transaction_id, :category, :inclusive_dates, :fund_source, :venue, :approved_ta_path, :travel_itinerary_path, :activity_proposal_path)
        ";
        $caStmt = $fastPDO->prepare($insertCaSql);
        $caStmt->execute([
            'transaction_id' => $transactionDbId,
            'category' => $cashAdvanceCategory,
            'inclusive_dates' => $caInclusiveDates,
            'fund_source' => null,  // Workflow v3: Budget Officer fills this during Source of Funds Verification
            'venue' => !empty($caFields['dateVenue']) ? $venue : null,
            'approved_ta_path' => $approvedTaPath,
            'travel_itinerary_path' => $travelItineraryPath,
            'activity_proposal_path' => $activityProposalPath
        ]);
    }

    // Insert Reimbursement Details
    if ($type === 'Reimbursement') {
        $insertReimbSql = "
            INSERT INTO reimbursement_details (transaction_id, category, reimbursement_month, inclusive_dates, venue, approved_ta_path, travel_itinerary_path, activity_proposal_path, dtr_path, certificate_path, bill_proof_path) 
            VALUES (:transaction_id, :category, :reimbursement_month, :inclusive_dates, :venue, :approved_ta_path, :travel_itinerary_path, :activity_proposal_path, :dtr_path, :certificate_path, :bill_proof_path)
        ";
        $reimbStmt = $fastPDO->prepare($insertReimbSql);
        $reimbStmt->execute([
            'transaction_id' => $transactionDbId,
            'category' => $reimbursementCategory,
            'reimbursement_month' => $reimbursementMonth ?: null,
            'inclusive_dates' => $reimbInclusiveDates,
            'venue' => !empty($reimbFields['dateVenue']) ? $reimbVenue : null,
            'approved_ta_path' => $reimbApprovedTaPath,
            'travel_itinerary_path' => $reimbTravelItineraryPath,
            'activity_proposal_path' => $reimbActivityProposalPath,
            'dtr_path' => $dtrPath,
            'certificate_path' => $certificatePath,
            'bill_proof_path' => $utilityBillProofPath ?: $billProofPath
        ]);
    }

    // Insert Workflow Log
    $logSql = "
        INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
        VALUES (:transaction_id, NULL, :new_status, :changed_by, :remarks)
    ";
    $logStmt = $fastPDO->prepare($logSql);
    $logStmt->execute([
        'transaction_id' => $transactionDbId,
        'new_status' => $status,
        'changed_by' => $userId,
        'remarks' => 'Transaction submitted. Pending Budget Officer verification.'
    ]);

    // ============================================================
    // Workflow v3: No attachment approvals at Stage 1.
    // Attachments are seeded in resubmit-documents.php at Stage 3.
    // ============================================================

    // ============================================================
    // Workflow v3: Create signatory_tasks (Stage 5 parallel tasks)
    // ============================================================
    $insertTask = $fastPDO->prepare("
        INSERT INTO signatory_tasks (transaction_id, task_type, status)
        VALUES (:tx_id, :task_type, 'pending')
    ");
    $insertTask->execute(['tx_id' => $transactionDbId, 'task_type' => 'payroll_prep']);
    $insertTask->execute(['tx_id' => $transactionDbId, 'task_type' => 'dv_ors_prep']);

    // Audit Log entry
    AuditLogService::log(
        $fastPDO, 
        $userId, 
        "Submitted new transaction: {$trackingNumber}", 
        null, 
        [
            'tracking_number' => $trackingNumber,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
            'tax_type' => $taxType
        ]
    );

    $fastPDO->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction submitted successfully.',
        'data' => [
            'tracking_number' => $trackingNumber
        ]
    ]);
    exit;

} catch (Throwable $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    // Clean up uploaded files if database transaction fails
    $allUploadedPaths = array_merge(
        $attachmentPaths,
        array_filter([$approvedTaPath, $travelItineraryPath, $activityProposalPath, $dtrPath, $certificatePath, $billProofPath, $reimbApprovedTaPath, $reimbTravelItineraryPath, $reimbActivityProposalPath, $utilityBillProofPath])
    );
    foreach ($allUploadedPaths as $path) {
        $fullPath = dirname(dirname(__DIR__)) . '/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    
    error_log("Transaction submission database failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred during transaction submission.']);
    exit;
}
