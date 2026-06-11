<?php
/**
 * Transaction Submission Processor API for SDO FAST.
 * Conducts tax computations, secure file uploads, and tracking code generation.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth & CSRF
require_once __DIR__ . '/../../services/TrackingNumberService.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

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

// 1. Parse and sanitize POST parameters
$type = trim($_POST['transaction_type'] ?? '');
$eventName = trim($_POST['event_name'] ?? '');
$amount = (float)($_POST['amount'] ?? 0.00);
$taxType = trim($_POST['tax_type'] ?? '');
$targetDate = trim($_POST['target_date'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

// Cash Advance specific fields
$cashAdvanceCategory = trim($_POST['cash_advance_category'] ?? '');
$mooeStartDate = trim($_POST['mooe_start_date'] ?? '');
$mooeEndDate = trim($_POST['mooe_end_date'] ?? '');
$fundSource = trim($_POST['fund_source'] ?? '');
$venue = trim($_POST['venue'] ?? '');

// 2. Validate Inputs
if (empty($type) || empty($eventName) || $amount <= 0 || empty($taxType)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction type, event name, tax type, and amount are required.']);
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
    if (empty($cashAdvanceCategory) || !in_array($cashAdvanceCategory, ['MOOE', 'Activity'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Cash Advance Category (MOOE or Activity) is required.']);
        exit;
    }
    if ($cashAdvanceCategory === 'MOOE') {
        if (empty($mooeStartDate) || empty($mooeEndDate) || empty($fundSource) || empty($venue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Inclusive dates, fund source, and venue are required for MOOE.']);
            exit;
        }
    } elseif ($cashAdvanceCategory === 'Activity') {
        if (empty($mooeStartDate) || empty($mooeEndDate) || empty($venue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity dates and venue are required for Activity.']);
            exit;
        }
    }
    $inclusiveDates = $mooeStartDate . ' to ' . $mooeEndDate;
}

// Fetch active tax configurations to validate and calculate tax
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

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

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

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file: ' . $fileKey]);
        exit;
    }
    
    return 'uploads/transactions/' . $filename;
}

// 4. Secure File Upload Handler
$uploadDir = dirname(dirname(__DIR__)) . '/uploads/transactions/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$attachmentPath = null;
$attachmentPaths = [];
$approvedTaPath = null;
$travelItineraryPath = null;
$activityProposalPath = null;

// Enforce required uploads for Cash Advance MOOE & Activity
if ($type === 'Cash Advance') {
    if ($cashAdvanceCategory === 'MOOE') {
        if (!isset($_FILES['approved_ta']) || $_FILES['approved_ta']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Approved TA (Travel Authority) is required for MOOE cash advances.']);
            exit;
        }
        if (!isset($_FILES['travel_itinerary']) || $_FILES['travel_itinerary']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Travel Itinerary is required for MOOE cash advances.']);
            exit;
        }
        $approvedTaPath = handleSecureUpload('approved_ta', $uploadDir);
        $travelItineraryPath = handleSecureUpload('travel_itinerary', $uploadDir);
    } elseif ($cashAdvanceCategory === 'Activity') {
        if (!isset($_FILES['activity_proposal']) || $_FILES['activity_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity Proposal is required for Activity cash advances.']);
            exit;
        }
        $activityProposalPath = handleSecureUpload('activity_proposal', $uploadDir);
    }
}

// Process supporting attachments if provided
if (isset($_FILES['attachment']) && is_array($_FILES['attachment']['name']) && $_FILES['attachment']['error'][0] !== UPLOAD_ERR_NO_FILE) {
    $fileCount = count($_FILES['attachment']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['attachment']['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        
        $fileError = $_FILES['attachment']['error'][$i];
        if ($fileError !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File upload error code on attachment ' . ($i + 1) . ': ' . $fileError]);
            exit;
        }

        $fileSize = $_FILES['attachment']['size'][$i];
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($fileSize > $maxSize) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Attachment file ' . ($i + 1) . ' exceeds the maximum limit of 10MB.']);
            exit;
        }

        $fileName = $_FILES['attachment']['name'][$i];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
        if (!in_array($extension, $allowedExtensions)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Allowed file formats for attachment ' . ($i + 1) . ': PDF, JPG, PNG, DOCX.']);
            exit;
        }

        $tmpName = $_FILES['attachment']['tmp_name'][$i];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Security check: Invalid file content type detected for attachment ' . ($i + 1) . '.']);
            exit;
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded attachment: ' . ($i + 1)]);
            exit;
        }

        $attachmentPaths[] = 'uploads/transactions/' . $newFilename;
    }
}

$attachmentPath = !empty($attachmentPaths) ? json_encode($attachmentPaths) : null;

// 5. Database Insertion Workflow
try {
    $fastPDO->beginTransaction();

    // Generate tracking code sequentially with a concurrency lock
    $trackingNumber = TrackingNumberService::generate($fastPDO);
    $uuid = bin2hex(random_bytes(16)); // simple UUID format
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
    
    $status = 'Pending Accountant 1';

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
        $insertCaSql = "
            INSERT INTO cash_advance_details (transaction_id, category, inclusive_dates, fund_source, venue, approved_ta_path, travel_itinerary_path, activity_proposal_path) 
            VALUES (:transaction_id, :category, :inclusive_dates, :fund_source, :venue, :approved_ta_path, :travel_itinerary_path, :activity_proposal_path)
        ";
        $caStmt = $fastPDO->prepare($insertCaSql);
        $caStmt->execute([
            'transaction_id' => $transactionDbId,
            'category' => $cashAdvanceCategory,
            'inclusive_dates' => $inclusiveDates,
            'fund_source' => $cashAdvanceCategory === 'MOOE' ? $fundSource : null,
            'venue' => $venue,
            'approved_ta_path' => $approvedTaPath,
            'travel_itinerary_path' => $travelItineraryPath,
            'activity_proposal_path' => $activityProposalPath
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
        'remarks' => 'Initial submission by requestor.'
    ]);

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

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    // Clean up uploaded files if database transaction fails
    if (!empty($attachmentPaths)) {
        foreach ($attachmentPaths as $path) {
            if (file_exists(dirname(dirname(__DIR__)) . '/' . $path)) {
                unlink(dirname(dirname(__DIR__)) . '/' . $path);
            }
        }
    }
    if ($approvedTaPath && file_exists(dirname(dirname(__DIR__)) . '/' . $approvedTaPath)) {
        unlink(dirname(dirname(__DIR__)) . '/' . $approvedTaPath);
    }
    if ($travelItineraryPath && file_exists(dirname(dirname(__DIR__)) . '/' . $travelItineraryPath)) {
        unlink(dirname(dirname(__DIR__)) . '/' . $travelItineraryPath);
    }
    if ($activityProposalPath && file_exists(dirname(dirname(__DIR__)) . '/' . $activityProposalPath)) {
        unlink(dirname(dirname(__DIR__)) . '/' . $activityProposalPath);
    }
    
    error_log("Transaction submission database failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred during transaction submission.']);
}
?>
