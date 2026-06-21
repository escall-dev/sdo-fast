<?php
/**
 * Complete Signatory Task API for Stage 5 (Signatories / ACCTG Support).
 * Marks a parallel sub-task (payroll_prep or dv_ors_prep) as completed.
 * Auto-advances to Stage 6 when both tasks are done.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Handle multipart/form-data (for file upload) or JSON
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $inputData = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $inputData = $_POST;
}

$transactionId = (int)($inputData['transaction_id'] ?? 0);
$taskType = trim($inputData['task_type'] ?? '');
$remarks = trim($inputData['remarks'] ?? '');

if ($transactionId <= 0 || !in_array($taskType, ['payroll_prep', 'dv_ors_prep'])) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction ID and valid task_type (payroll_prep or dv_ors_prep) are required.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Authorized: ACCTG Support, Accounting Staff, Approver, or Super Admin
$authorized = ($userRole === 'Super Admin' || $userRole === 'Accounting Staff' || $userRole === 'Approver'
    || in_array($userPosition, ['Accounting Support', 'Accountant', 'ASDS', 'SDS']));
if (!$authorized) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: You do not have permission to complete signatory tasks.']);
    exit;
}

try {
    // 1. Verify transaction is at Stage 5
    $stmt = $fastPDO->prepare("SELECT id, current_status, tracking_number FROM transactions WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    if ($transaction['current_status'] !== 'Pending Signatory Approval') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Transaction is not at Stage 5 (Pending Signatory Approval). Current status: ' . $transaction['current_status']]);
        exit;
    }

    // 2. Handle optional file upload
    $documentPath = null;
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['document'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit.']);
            exit;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'xlsx'];
        if (!in_array($extension, $allowed)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: PDF, JPG, PNG, DOCX, XLSX.']);
            exit;
        }

        $uploadDir = __DIR__ . '/../../uploads/signatory-docs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = "{$transactionId}_{$taskType}_" . time() . ".{$extension}";
        $documentPath = 'uploads/signatory-docs/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
            exit;
        }
    }

    $fastPDO->beginTransaction();

    // 3. Mark the task as completed
    $updateTask = $fastPDO->prepare("
        UPDATE signatory_tasks 
        SET status = 'completed', completed_by = :user_id, completed_at = NOW(), 
            document_path = COALESCE(:doc_path, document_path), remarks = :remarks
        WHERE transaction_id = :tx_id AND task_type = :task_type
    ");
    $updateTask->execute([
        'user_id' => $userId,
        'doc_path' => $documentPath,
        'remarks' => $remarks,
        'tx_id' => $transactionId,
        'task_type' => $taskType
    ]);

    if ($updateTask->rowCount() === 0) {
        // Task row might not exist — create it
        $insertTask = $fastPDO->prepare("
            INSERT INTO signatory_tasks (transaction_id, task_type, status, completed_by, completed_at, document_path, remarks)
            VALUES (:tx_id, :task_type, 'completed', :user_id, NOW(), :doc_path, :remarks)
            ON DUPLICATE KEY UPDATE status = 'completed', completed_by = VALUES(completed_by), 
                completed_at = NOW(), document_path = COALESCE(VALUES(document_path), document_path), 
                remarks = VALUES(remarks)
        ");
        $insertTask->execute([
            'tx_id' => $transactionId,
            'task_type' => $taskType,
            'user_id' => $userId,
            'doc_path' => $documentPath,
            'remarks' => $remarks
        ]);
    }

    // 4. Check if BOTH tasks are now complete
    $checkStmt = $fastPDO->prepare("
        SELECT COUNT(*) as total, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as done
        FROM signatory_tasks WHERE transaction_id = :tx_id
    ");
    $checkStmt->execute(['tx_id' => $transactionId]);
    $taskCounts = $checkStmt->fetch();

    $bothComplete = ($taskCounts['total'] >= 2 && $taskCounts['done'] >= 2);
    $autoAdvanced = false;

    if ($bothComplete) {
        // Auto-advance to Stage 6: Pending Signatory Approval
        $advStmt = $fastPDO->prepare("UPDATE transactions SET current_status = 'Pending Signatory Approval' WHERE id = :id");
        $advStmt->execute(['id' => $transactionId]);

        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:tx_id, 'Pending Signatory Approval', 'Pending Signatory Approval', :user_id, :remarks)
        ");
        $logStmt->execute([
            'tx_id' => $transactionId,
            'user_id' => $userId,
            'remarks' => 'Both signatory tasks completed. Routed to Cashier for release.'
        ]);

        AuditLogService::log($fastPDO, $userId,
            "Both signatory tasks completed, auto-advanced: {$transaction['tracking_number']}",
            ['status' => 'Pending Signatory Approval'],
            ['status' => 'Pending Signatory Approval']
        );

        $autoAdvanced = true;
    }

    $fastPDO->commit();

    $taskLabel = ($taskType === 'payroll_prep') ? 'Payroll Preparation' : 'DV/ORS Preparation';
    $message = "$taskLabel task marked as completed.";
    if ($autoAdvanced) {
        $message .= " Both tasks done — transaction advanced to Pending Signatory Approval.";
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'both_complete' => $bothComplete,
        'auto_advanced' => $autoAdvanced,
        'document_path' => $documentPath
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Signatory task error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during signatory task completion.']);
}
