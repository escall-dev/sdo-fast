<?php
/**
 * Document Resubmission API for Workflow v3 Stage 3.
 * Requestor uploads Mandatory Documentary Requirements after budget approval.
 *
 * Only accessible when transaction status is 'Pending Requestor'
 * (post-budget-approval, awaiting document submission).
 *
 * On success: advances to 'Pending Accounting Support' (Accounting Support Document Inspection).
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$transactionId = (int)($_POST['transaction_id'] ?? 0);
$remarks = trim($_POST['remarks'] ?? '');

if ($transactionId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction ID is required.']);
    exit;
}

// Verify the transaction exists, is at the right stage, and belongs to this user
$stmt = $fastPDO->prepare("
    SELECT t.*, cad.category as ca_category, rd.category as reimb_category
    FROM transactions t
    LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
    LEFT JOIN reimbursement_details rd ON t.id = rd.transaction_id
    WHERE t.id = :id LIMIT 1
");
$stmt->execute(['id' => $transactionId]);
$transaction = $stmt->fetch();

if (!$transaction) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    exit;
}

// Only allow at Stage 2 (Pending Requestor — awaiting document submission)
if ($transaction['current_status'] !== 'Pending Requestor') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Document submission is only allowed after budget approval. Current status: ' . $transaction['current_status']]);
    exit;
}

// Only the requestor (or Super Admin) can submit documents
$isRequestor = ((int)$transaction['requestor_id'] === (int)$userId);
$isSuperAdmin = ($userRole === 'Super Admin');
if (!$isRequestor && !$isSuperAdmin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only the transaction requestor can submit documents.']);
    exit;
}

// =========================================================================
// Secure file upload helper (same as submit-transaction.php)
// =========================================================================
function resubmitHandleSecureUpload($fileKey, $uploadDir) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File upload error on ' . $fileKey . ': ' . $file['error']]);
        exit;
    }

    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'File ' . $fileKey . ' exceeds the 10MB limit.']);
        exit;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
    if (!in_array($extension, $allowedExtensions)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid format for ' . $fileKey . '. Allowed: PDF, JPG, PNG, DOCX.']);
        exit;
    }

    // Robust MIME type detection with extension-based fallback
    $mimeType = null;
    $extensionMimeMap = [
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
        $mimeType = $extensionMimeMap[$extension] ?? 'application/octet-stream';
    }

    $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid file content type for ' . $fileKey . '.']);
        exit;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    $uploaded = defined('TEST_MODE') ? copy($file['tmp_name'], $targetPath) : move_uploaded_file($file['tmp_name'], $targetPath);
    if (!$uploaded) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save file: ' . $fileKey]);
        exit;
    }

    return 'uploads/transactions/' . $filename;
}

// =========================================================================
// Determine required documents based on transaction type and coverage
// =========================================================================
$uploadDir = dirname(dirname(__DIR__)) . '/uploads/transactions/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$type = $transaction['transaction_type'];
$caCategory = $transaction['ca_category'] ?? '';
$reimbCategory = $transaction['reimb_category'] ?? '';

$caFieldConfig = [];
$reimbFieldConfig = [];
if ($type === 'Cash Advance' && $caCategory !== '') {
    $caCatRow = CoverageCategoryService::getCategoryByName($fastPDO, 'Cash Advance', $caCategory);
    $caFieldConfig = $caCatRow['field_config'] ?? [];
}
if ($type === 'Reimbursement' && $reimbCategory !== '') {
    $reimbCatRow = CoverageCategoryService::getCategoryByName($fastPDO, 'Reimbursement', $reimbCategory);
    $reimbFieldConfig = $reimbCatRow['field_config'] ?? [];
}

$allUploadedFiles = [];

// Validate required uploads and process files
if ($type === 'Cash Advance') {
    if (!empty($caFieldConfig['taItinerary'])) {
        if (!isset($_FILES['approved_ta']) || $_FILES['approved_ta']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Approved TA (Travel Authority) is required for Travel cash advances.']);
            exit;
        }
        if (!isset($_FILES['travel_itinerary']) || $_FILES['travel_itinerary']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Travel Itinerary is required for Travel cash advances.']);
            exit;
        }
        $path = resubmitHandleSecureUpload('approved_ta', $uploadDir);
        if ($path) $allUploadedFiles[] = ['path' => $path, 'label' => 'Approved Travel Authority'];
        $path = resubmitHandleSecureUpload('travel_itinerary', $uploadDir);
        if ($path) $allUploadedFiles[] = ['path' => $path, 'label' => 'Travel Itinerary'];
        // Update cash_advance_details
        if (!empty($allUploadedFiles)) {
            $updCa = $fastPDO->prepare("UPDATE cash_advance_details SET approved_ta_path = COALESCE(NULLIF(:ta, ''), approved_ta_path), travel_itinerary_path = COALESCE(NULLIF(:ti, ''), travel_itinerary_path) WHERE transaction_id = :id");
            $taPath = $allUploadedFiles[0]['path'] ?? null;
            $tiPath = $allUploadedFiles[1]['path'] ?? null;
            $updCa->execute(['ta' => $taPath, 'ti' => $tiPath, 'id' => $transactionId]);
        }
    }
    if (!empty($caFieldConfig['activityProposal'])) {
        if (!isset($_FILES['activity_proposal']) || $_FILES['activity_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity Proposal is required for ' . $caCategory . ' cash advances.']);
            exit;
        }
        $path = resubmitHandleSecureUpload('activity_proposal', $uploadDir);
        if ($path) {
            $allUploadedFiles[] = ['path' => $path, 'label' => 'Activity Proposal'];
            $updCa = $fastPDO->prepare("UPDATE cash_advance_details SET activity_proposal_path = :ap WHERE transaction_id = :id");
            $updCa->execute(['ap' => $path, 'id' => $transactionId]);
        }
    }
} elseif ($type === 'Reimbursement') {
    if (!empty($reimbFieldConfig['taItinerary'])) {
        if (!isset($_FILES['reimb_approved_ta']) || $_FILES['reimb_approved_ta']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Approved TA (Travel Authority) is required for Travel reimbursement.']);
            exit;
        }
        if (!isset($_FILES['reimb_travel_itinerary']) || $_FILES['reimb_travel_itinerary']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Travel Itinerary is required for Travel reimbursement.']);
            exit;
        }
        $path = resubmitHandleSecureUpload('reimb_approved_ta', $uploadDir);
        if ($path) $allUploadedFiles[] = ['path' => $path, 'label' => 'Approved Travel Authority (Reimb)'];
        $path = resubmitHandleSecureUpload('reimb_travel_itinerary', $uploadDir);
        if ($path) $allUploadedFiles[] = ['path' => $path, 'label' => 'Travel Itinerary (Reimb)'];
        if (!empty($allUploadedFiles)) {
            $updReimb = $fastPDO->prepare("UPDATE reimbursement_details SET approved_ta_path = COALESCE(NULLIF(:ta, ''), approved_ta_path), travel_itinerary_path = COALESCE(NULLIF(:ti, ''), travel_itinerary_path) WHERE transaction_id = :id");
            $taPath = $allUploadedFiles[0]['path'] ?? null;
            $tiPath = $allUploadedFiles[1]['path'] ?? null;
            $updReimb->execute(['ta' => $taPath, 'ti' => $tiPath, 'id' => $transactionId]);
        }
    }
    if (!empty($reimbFieldConfig['activityProposal'])) {
        if (!isset($_FILES['reimb_activity_proposal']) || $_FILES['reimb_activity_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity Proposal is required for ' . $reimbCategory . ' reimbursement.']);
            exit;
        }
        $path = resubmitHandleSecureUpload('reimb_activity_proposal', $uploadDir);
        if ($path) {
            $allUploadedFiles[] = ['path' => $path, 'label' => 'Activity Proposal (Reimb)'];
            $updReimb = $fastPDO->prepare("UPDATE reimbursement_details SET activity_proposal_path = :ap WHERE transaction_id = :id");
            $updReimb->execute(['ap' => $path, 'id' => $transactionId]);
        }
    }
    if (!empty($reimbFieldConfig['communications'])) {
        if (!isset($_FILES['reimb_dtr']) || $_FILES['reimb_dtr']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'DTR document is required for Communication Load.']);
            exit;
        }
        if (!isset($_FILES['reimb_certificate']) || $_FILES['reimb_certificate']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Certificate document is required for Communication Load.']);
            exit;
        }
        if (!isset($_FILES['reimb_bill_proof']) || $_FILES['reimb_bill_proof']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Bill / proof of payment is required for Communication Load.']);
            exit;
        }
        $dtrPath = resubmitHandleSecureUpload('reimb_dtr', $uploadDir);
        if ($dtrPath) $allUploadedFiles[] = ['path' => $dtrPath, 'label' => 'DTR Document'];
        $certPath = resubmitHandleSecureUpload('reimb_certificate', $uploadDir);
        if ($certPath) $allUploadedFiles[] = ['path' => $certPath, 'label' => 'Certificate'];
        $billPath = resubmitHandleSecureUpload('reimb_bill_proof', $uploadDir);
        if ($billPath) $allUploadedFiles[] = ['path' => $billPath, 'label' => 'Bill / Proof of Payment'];
        $updReimb = $fastPDO->prepare("UPDATE reimbursement_details SET dtr_path = COALESCE(NULLIF(:dtr, ''), dtr_path), certificate_path = COALESCE(NULLIF(:cert, ''), certificate_path), bill_proof_path = COALESCE(NULLIF(:bill, ''), bill_proof_path) WHERE transaction_id = :id");
        $updReimb->execute(['dtr' => $dtrPath, 'cert' => $certPath, 'bill' => $billPath, 'id' => $transactionId]);
    }
    if (!empty($reimbFieldConfig['utilityBills'])) {
        if (!isset($_FILES['utility_bill_proof']) || $_FILES['utility_bill_proof']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Bill / proof of payment is required for Utility Bills.']);
            exit;
        }
        $path = resubmitHandleSecureUpload('utility_bill_proof', $uploadDir);
        if ($path) {
            $allUploadedFiles[] = ['path' => $path, 'label' => 'Utility Bill / Proof'];
            $updReimb = $fastPDO->prepare("UPDATE reimbursement_details SET bill_proof_path = :bp WHERE transaction_id = :id");
            $updReimb->execute(['bp' => $path, 'id' => $transactionId]);
        }
    }
}

// Process general supporting attachments
if (isset($_FILES['attachment']) && is_array($_FILES['attachment']['name'])) {
    $fileCount = count($_FILES['attachment']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['attachment']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

        $fileError = $_FILES['attachment']['error'][$i];
        if ($fileError !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Attachment ' . ($i + 1) . ' upload error: ' . $fileError]);
            exit;
        }

        $fileSize = $_FILES['attachment']['size'][$i];
        if ($fileSize > 10 * 1024 * 1024) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Attachment ' . ($i + 1) . ' exceeds 10MB limit.']);
            exit;
        }

        $fileName = $_FILES['attachment']['name'][$i];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'docx'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid format for attachment ' . ($i + 1) . '.']);
            exit;
        }

        $tmpName = $_FILES['attachment']['tmp_name'][$i];

        // Robust MIME type detection with extension-based fallback
        $mimeType = null;
        $attachExtMimeMap = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        if (function_exists('finfo_open') && function_exists('finfo_file')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = @finfo_file($finfo, $tmpName);
                if ($detected !== false) {
                    $mimeType = $detected;
                }
                @finfo_close($finfo);
            }
        }
        if ($mimeType === null) {
            $mimeType = $attachExtMimeMap[$extension] ?? 'application/octet-stream';
        }

        $allowedMimeTypes = [
            'application/pdf', 'image/jpeg', 'image/png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid content type for attachment ' . ($i + 1) . '.']);
            exit;
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;
        $uploaded = defined('TEST_MODE') ? copy($tmpName, $targetPath) : move_uploaded_file($tmpName, $targetPath);
        if (!$uploaded) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save attachment ' . ($i + 1)]);
            exit;
        }
        $allUploadedFiles[] = ['path' => 'uploads/transactions/' . $newFilename, 'label' => 'Supporting Attachment: ' . basename($fileName)];
    }
}

// Must have uploaded at least something
// (checklist_files are processed below, inside the DB transaction)
$hasFiles = !empty($allUploadedFiles) 
    || (isset($_FILES['checklist_files']) && is_array($_FILES['checklist_files']['name']) && $_FILES['checklist_files']['error'][0] !== UPLOAD_ERR_NO_FILE);

if (!$hasFiles) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'No documents were uploaded. Please attach the required Mandatory Documentary Requirements.']);
    exit;
}

// =========================================================================
// Database transaction: update status, seed attachment_approvals, update document_details
// =========================================================================
try {
    // Read checklist labels from frontend (JSON array, index → label)
    $labelsJson = trim($_POST['attachment_labels_json'] ?? '');
    $checklistLabels = [];
    if (!empty($labelsJson)) {
        $decoded = json_decode($labelsJson, true);
        if (is_array($decoded)) $checklistLabels = $decoded;
    }

    // Process checklist_files[] (per-document Attach buttons) with proper labels
    $checklistFilesDetected = false;
    $checklistFilesProcessed = 0;
    $checklistFilesSkipped = 0;
    $skipReasons = [];
    if (isset($_FILES['checklist_files']) && is_array($_FILES['checklist_files']['name'])) {
        $fileCount = count($_FILES['checklist_files']['name']);
        $checklistFilesDetected = true;
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['checklist_files']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                // Ignore empty file inputs
                continue;
            }
            $fileError = $_FILES['checklist_files']['error'][$i];
            if ($fileError !== UPLOAD_ERR_OK) {
                $skipReasons[] = "File {$i} upload error code: {$fileError}";
                $checklistFilesSkipped++;
                continue;
            }

            $fileSize = $_FILES['checklist_files']['size'][$i];
            if ($fileSize > 10 * 1024 * 1024) {
                $skipReasons[] = "File {$i} exceeds 10MB limit (size: {$fileSize})";
                $checklistFilesSkipped++;
                continue;
            }

            $fileName = $_FILES['checklist_files']['name'][$i];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'docx'])) {
                $skipReasons[] = "File '{$fileName}' has invalid extension: {$extension}";
                $checklistFilesSkipped++;
                continue;
            }

            $tmpName = $_FILES['checklist_files']['tmp_name'][$i];

            // Robust MIME type detection with fallback (finfo may be unavailable on some servers)
            $mimeType = null;
            if (function_exists('finfo_open') && function_exists('finfo_file')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $detected = @finfo_file($finfo, $tmpName);
                    if ($detected !== false) {
                        $mimeType = $detected;
                    }
                    @finfo_close($finfo);
                }
            }

            // Extension-based MIME fallback map
            $extensionMimeMap = [
                'pdf'  => 'application/pdf',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            if ($mimeType === null) {
                // finfo not available or failed — fall back to extension-based check
                $mimeType = $extensionMimeMap[$extension] ?? 'application/octet-stream';
                error_log("Resubmit: checklist file index {$i} using extension-based MIME fallback: {$mimeType}");
            }

            // Normalize mimeType (sometimes finfo returns extra metadata like "image/jpeg; charset=binary")
            $mimeType = explode(';', $mimeType)[0];
            $mimeType = trim($mimeType);

            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $skipReasons[] = "File '{$fileName}' rejected MIME type: {$mimeType}";
                $checklistFilesSkipped++;
                continue;
            }

            $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;
            
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            
            $uploaded = defined('TEST_MODE') ? copy($tmpName, $targetPath) : move_uploaded_file($tmpName, $targetPath);
            if (!$uploaded) {
                $errDetail = error_get_last();
                $skipReasons[] = "File '{$fileName}' could not be saved to server. Permission issue or disk full. PHP Error: " . ($errDetail ? $errDetail['message'] : 'Unknown');
                $checklistFilesSkipped++;
                continue;
            }

            // Use the label from JSON array at this index, or fallback to filename
            $label = isset($checklistLabels[$i]) ? $checklistLabels[$i] : basename($fileName);
            $allUploadedFiles[] = ['path' => 'uploads/transactions/' . $newFilename, 'label' => $label];
            $checklistFilesProcessed++;
        }
    }

    // If checklist files were detected in the request BUT none were successfully processed,
    // block the submission — the transaction must not advance with zero attachments.
    if ($checklistFilesDetected && $checklistFilesProcessed === 0 && empty($allUploadedFiles)) {
        $reasonStr = empty($skipReasons) ? "Unknown error." : implode("; ", array_unique($skipReasons));
        error_log("Resubmit blocked for transaction {$transactionId}: " . $reasonStr);
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => "Upload failed: {$checklistFilesSkipped} file(s) were detected but could not be processed. Reasons:\n" . $reasonStr
        ]);
        exit;
    }

    // Final safety net: if nothing was uploaded at all, reject
    if (empty($allUploadedFiles)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'No documents were uploaded. Please attach the required Mandatory Documentary Requirements.'
        ]);
        exit;
    }

    $fastPDO->beginTransaction();

    // 1. Advance status to Pending Accounting Support (Document Inspection)
    $newStatus = 'Pending Accounting Support';
    $updateStmt = $fastPDO->prepare("UPDATE transactions SET current_status = :status, remarks = :remarks WHERE id = :id");
    $updateStmt->execute([
        'status' => $newStatus,
        'remarks' => $remarks ?: 'Mandatory Documentary Requirements submitted.',
        'id' => $transactionId
    ]);

    // 2. Record status log
    $logStmt = $fastPDO->prepare("
        INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks)
        VALUES (:tx_id, 'Pending Requestor', :new_status, :user_id, :remarks)
    ");
    $logStmt->execute([
        'tx_id' => $transactionId,
        'new_status' => $newStatus,
        'user_id' => $userId,
        'remarks' => 'Mandatory Documentary Requirements submitted by requestor. ' . count($allUploadedFiles) . ' file(s) uploaded. ' . $remarks
    ]);

    // 3. Seed attachment_approvals for Stage 4 (Document Inspection)
    $insertApproval = $fastPDO->prepare("
        INSERT INTO attachment_approvals (transaction_id, file_path, file_label, status)
        VALUES (:tx_id, :file_path, :file_label, 'pending')
    ");
    foreach ($allUploadedFiles as $f) {
        $insertApproval->execute([
            'tx_id' => $transactionId,
            'file_path' => $f['path'],
            'file_label' => $f['label']
        ]);
    }

    // 4. Update document_details with attachment paths
    $attachmentPaths = array_map(function($f) { return $f['path']; }, $allUploadedFiles);
    $attachmentJson = json_encode($attachmentPaths);

    $docCheckStmt = $fastPDO->prepare("SELECT id FROM document_details WHERE transaction_id = :id LIMIT 1");
    $docCheckStmt->execute(['id' => $transactionId]);
    if ($docCheckStmt->fetchColumn()) {
        $updDoc = $fastPDO->prepare("UPDATE document_details SET attachment_path = :ap WHERE transaction_id = :id");
        $updDoc->execute(['ap' => $attachmentJson, 'id' => $transactionId]);
    } else {
        $insDoc = $fastPDO->prepare("INSERT INTO document_details (transaction_id, attachment_path) VALUES (:id, :ap)");
        $insDoc->execute(['id' => $transactionId, 'ap' => $attachmentJson]);
    }

    $fastPDO->commit();

    AuditLogService::log($fastPDO, $userId,
        "Documents submitted for: {$transaction['tracking_number']}",
        ['status' => 'Pending Requestor'],
        ['status' => $newStatus, 'files' => count($allUploadedFiles)]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Mandatory Documentary Requirements submitted successfully. Transaction routed to Accounting Support for Document Inspection.',
        'tracking_number' => $transaction['tracking_number'],
        'new_status' => $newStatus,
        'files_uploaded' => count($allUploadedFiles)
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Resubmit documents failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during document submission.']);
}
