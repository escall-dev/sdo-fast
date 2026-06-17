<?php
/**
 * Per-Attachment Approval API for Stage 2 (ACCTG Support).
 * Approves or rejects individual attachments. Auto-advances to Stage 3
 * when all attachments are approved.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

$inputData = defined('TEST_MODE') ? ($GLOBALS['TEST_INPUT'] ?? []) : json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($inputData)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$transactionId = (int)($inputData['transaction_id'] ?? 0);
$approvalId = (int)($inputData['approval_id'] ?? 0);
$action = trim($inputData['action'] ?? ''); // 'approve' or 'reject'
$remarks = trim($inputData['remarks'] ?? '');

if ($transactionId <= 0 || $approvalId <= 0 || !in_array($action, ['approve', 'reject'])) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction ID, approval ID, and action (approve/reject) are required.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Only ACCTG Support / Accounting Staff / Super Admin can approve attachments
$authorized = ($userRole === 'Super Admin' || $userRole === 'Accounting Staff' || $userPosition === 'Accounting Support');
if (!$authorized) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only ACCTG Support staff can approve attachments.']);
    exit;
}

try {
    // 1. Verify transaction is at Stage 2
    $stmt = $fastPDO->prepare("SELECT id, current_status, tracking_number FROM transactions WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    if ($transaction['current_status'] !== 'Pending ACCTG Support') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Transaction is not at Stage 2 (Pending ACCTG Support). Current status: ' . $transaction['current_status']]);
        exit;
    }

    // 2. Verify the attachment approval record exists and belongs to this transaction
    $stmt = $fastPDO->prepare("SELECT id, status FROM attachment_approvals WHERE id = :id AND transaction_id = :tx_id LIMIT 1");
    $stmt->execute(['id' => $approvalId, 'tx_id' => $transactionId]);
    $approval = $stmt->fetch();

    if (!$approval) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Attachment approval record not found.']);
        exit;
    }

    $fastPDO->beginTransaction();

    // 3. Update the attachment approval
    $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
    $updateStmt = $fastPDO->prepare("
        UPDATE attachment_approvals 
        SET status = :status, reviewed_by = :user_id, reviewed_at = NOW(), remarks = :remarks
        WHERE id = :id
    ");
    $updateStmt->execute([
        'status' => $newStatus,
        'user_id' => $userId,
        'remarks' => $remarks,
        'id' => $approvalId
    ]);

    // 4. Check if ALL attachments for this transaction are now approved
    $checkStmt = $fastPDO->prepare("
        SELECT COUNT(*) as total, 
               SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
               SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
        FROM attachment_approvals 
        WHERE transaction_id = :tx_id
    ");
    $checkStmt->execute(['tx_id' => $transactionId]);
    $counts = $checkStmt->fetch();

    // Verify if tax classification is already set
    $taxCheckStmt = $fastPDO->prepare("SELECT tax_type FROM document_details WHERE transaction_id = :tx_id LIMIT 1");
    $taxCheckStmt->execute(['tx_id' => $transactionId]);
    $taxType = $taxCheckStmt->fetchColumn();

    $allApproved = ($counts['total'] > 0 && $counts['approved_count'] == $counts['total']);
    $autoAdvanced = false;

    if ($allApproved && !empty($taxType)) {
        // Auto-advance to Stage 3: Pending Budget
        $advanceStmt = $fastPDO->prepare("UPDATE transactions SET current_status = 'Pending Budget' WHERE id = :id");
        $advanceStmt->execute(['id' => $transactionId]);

        // Log the status change
        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:tx_id, 'Pending ACCTG Support', 'Pending Budget', :user_id, :remarks)
        ");
        $logStmt->execute([
            'tx_id' => $transactionId,
            'user_id' => $userId,
            'remarks' => 'All attachments approved and tax classification set. Auto-advanced to Stage 3 (Budget).'
        ]);

        AuditLogService::log(
            $fastPDO, $userId,
            "All attachments approved and tax classification set, transaction auto-advanced: {$transaction['tracking_number']}",
            ['status' => 'Pending ACCTG Support'],
            ['status' => 'Pending Budget']
        );

        $autoAdvanced = true;
    }

    $fastPDO->commit();

    $message = "Attachment " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully.";
    if ($autoAdvanced) {
        $message .= " All attachments approved and tax classification set — transaction advanced to Pending Budget.";
    } elseif ($allApproved && empty($taxType)) {
        $message .= " All attachments approved, but you must select a Tax Classification before the transaction can advance to the Budget stage.";
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'all_approved' => $allApproved,
        'auto_advanced' => $autoAdvanced,
        'counts' => [
            'total' => (int)$counts['total'],
            'approved' => (int)$counts['approved_count'],
            'rejected' => (int)$counts['rejected_count'],
            'pending' => (int)$counts['total'] - (int)$counts['approved_count'] - (int)$counts['rejected_count']
        ]
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Attachment approval error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during attachment approval.']);
}
