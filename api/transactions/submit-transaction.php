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

// Only allow Requestors and Super Admins to submit transactions
if (!in_array($userRole, ['Super Admin', 'Requestor'])) {
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

// 4. Secure File Upload Handler
$attachmentPath = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['attachment'];
    
    // File upload diagnostics
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File upload error code: ' . $file['error']]);
        exit;
    }

    // Size limit check (10 MB maximum)
    $maxSize = 10 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Attached file exceeds the maximum limit of 10MB.']);
        exit;
    }

    // Type/Extension validation
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
    if (!in_array($extension, $allowedExtensions)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Allowed file formats: PDF, JPG, PNG, DOCX.']);
        exit;
    }

    // Mime type validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // docx
    ];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Security check: Invalid file content type detected.']);
        exit;
    }

    // Define target directory and randomized filename
    $uploadDir = dirname(dirname(__DIR__)) . '/uploads/transactions/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate randomized unique filename
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded attachment.']);
        exit;
    }
    
    // Path saved relative to app root
    $attachmentPath = 'uploads/transactions/' . $filename;
}

// 5. Database Insertion Workflow
try {
    $fastPDO->beginTransaction();

    // Generate tracking code sequentially with a concurrency lock
    $trackingNumber = TrackingNumberService::generate($fastPDO);
    $uuid = bin2hex(random_bytes(16)); // simple UUID format
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
    
    $status = 'Pending Support';

    // Insert Transaction
    $insertTxSql = "
        INSERT INTO transactions (uuid, tracking_number, requestor_id, transaction_type, event_name, amount, tax_amount, net_amount, target_date, current_status, remarks) 
        VALUES (:uuid, :tracking_number, :requestor_id, :transaction_type, :event_name, :amount, :tax_amount, :net_amount, :target_date, :current_status, :remarks)
    ";
    
    $txStmt = $fastPDO->prepare($insertTxSql);
    $txStmt->execute([
        'uuid' => $uuid,
        'tracking_number' => $trackingNumber,
        'requestor_id' => $userId,
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
    // Clean up uploaded file if database transaction fails
    if ($attachmentPath && file_exists(dirname(dirname(__DIR__)) . '/' . $attachmentPath)) {
        unlink(dirname(dirname(__DIR__)) . '/' . $attachmentPath);
    }
    
    error_log("Transaction submission database failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred during transaction submission.']);
}
