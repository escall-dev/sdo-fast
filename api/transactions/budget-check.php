<?php
/**
 * Budget Check API — Workflow v3 Stage 1→2 transition.
 * Budget Officer verifies fund availability, then routes back to Requestor
 * for Mandatory Documentary Requirements submission.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

$inputData = json_decode(file_get_contents('php://input'), true);

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

try {
    $colStmt = $fastPDO->query("SHOW COLUMNS FROM budget_checks LIKE 'fund_source_tracking_number'");
    if (!$colStmt->fetch()) {
        $fastPDO->exec("ALTER TABLE budget_checks ADD COLUMN fund_source_tracking_number VARCHAR(255) NULL AFTER fund_source");
    }
} catch (Exception $e) {
    error_log("Failed ensuring budget_checks.fund_source_tracking_number exists: " . $e->getMessage());
}

$transactionId = (int)($inputData['transaction_id'] ?? 0);
$fundSource = trim($inputData['fund_source'] ?? '');
$fundSourceTrackingNumber = trim($inputData['fund_source_tracking_number'] ?? '');
$fundAvailable = isset($inputData['fund_available']) ? (int)$inputData['fund_available'] : 1;
$remarks = trim($inputData['remarks'] ?? '');
$action = trim($inputData['action'] ?? 'approve'); // 'approve' or 'reject'

if ($transactionId <= 0 || empty($remarks)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction ID and remarks are required.']);
    exit;
}

if ($action === 'approve' && empty($fundSource)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Fund source is required when approving.']);
    exit;
}

if (strlen($fundSourceTrackingNumber) > 255) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Fund source tracking number must be at most 255 characters.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Only Budget Officer / Super Admin
$authorized = ($userRole === 'Super Admin' || $userRole === 'Budget Officer' || $userPosition === 'Budget Officer');
if (!$authorized) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Budget Officers can perform budget checks.']);
    exit;
}

try {
    // 1. Verify transaction is at Stage 1 (Pending Budget — awaiting funds verification)
    $stmt = $fastPDO->prepare("SELECT id, current_status, tracking_number FROM transactions WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    if ($transaction['current_status'] !== 'Pending Budget') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Transaction is not at Stage 1 (Pending Budget — Source of Funds Verification). Current status: ' . $transaction['current_status']]);
        exit;
    }

    $fastPDO->beginTransaction();

    // 2. Record budget check
    $budgetStmt = $fastPDO->prepare("
        INSERT INTO budget_checks (transaction_id, fund_source, fund_source_tracking_number, fund_available, checked_by, remarks)
        VALUES (:tx_id, :fund_source, :fund_source_tracking_number, :fund_available, :user_id, :remarks)
        ON DUPLICATE KEY UPDATE 
            fund_source = VALUES(fund_source),
            fund_source_tracking_number = VALUES(fund_source_tracking_number),
            fund_available = VALUES(fund_available),
            checked_by = VALUES(checked_by),
            checked_at = NOW(),
            remarks = VALUES(remarks)
    ");
    $budgetStmt->execute([
        'tx_id' => $transactionId,
        'fund_source' => $fundSource ?: 'N/A',
        'fund_source_tracking_number' => $fundSourceTrackingNumber !== '' ? $fundSourceTrackingNumber : null,
        'fund_available' => $fundAvailable,
        'user_id' => $userId,
        'remarks' => $remarks
    ]);

    // 3. Advance or reject
    if ($action === 'approve' && $fundAvailable) {
        // Workflow v3: Route back to Requestor for document submission
        $newStatus = 'Pending Requestor';
        $updateStmt = $fastPDO->prepare("UPDATE transactions SET current_status = :status, remarks = :remarks WHERE id = :id");
        $updateStmt->execute(['status' => $newStatus, 'remarks' => $remarks, 'id' => $transactionId]);

        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:tx_id, 'Pending Budget', :new_status, :user_id, :remarks)
        ");
        $logStmt->execute([
            'tx_id' => $transactionId,
            'new_status' => $newStatus,
            'user_id' => $userId,
            'remarks' => "Source of funds verified. Fund source: $fundSource." . ($fundSourceTrackingNumber !== '' ? " Tracking No: $fundSourceTrackingNumber." : "") . " Routed to Requestor for Mandatory Documentary Requirements. " . $remarks
        ]);

        AuditLogService::log($fastPDO, $userId,
            "Budget check approved: {$transaction['tracking_number']}",
            ['status' => 'Pending Budget'],
            ['status' => $newStatus, 'fund_source' => $fundSource, 'fund_source_tracking_number' => $fundSourceTrackingNumber]
        );
    } else {
        // Reject
        $newStatus = ($action === 'reject') ? 'Rejected' : 'Returned';
        $updateStmt = $fastPDO->prepare("UPDATE transactions SET current_status = :status, remarks = :remarks WHERE id = :id");
        $updateStmt->execute(['status' => $newStatus, 'remarks' => $remarks, 'id' => $transactionId]);

        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:tx_id, 'Pending Budget', :new_status, :user_id, :remarks)
        ");
        $logStmt->execute([
            'tx_id' => $transactionId,
            'new_status' => $newStatus,
            'user_id' => $userId,
            'remarks' => "Budget check: funds " . ($fundAvailable ? 'available' : 'NOT available') . ". " . $remarks
        ]);

        AuditLogService::log($fastPDO, $userId,
            "Budget check rejected: {$transaction['tracking_number']}",
            ['status' => 'Pending Budget'],
            ['status' => $newStatus]
        );
    }

    $fastPDO->commit();

    echo json_encode([
        'success' => true,
        'message' => "Budget check completed. Transaction moved to '$newStatus'.",
        'new_status' => $newStatus
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Budget check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during budget check.']);
}
