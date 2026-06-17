<?php
/**
 * Update Tax Classification API for Stage 2 (ACCTG Support).
 * Sets/updates the tax classification on the fly, computes tax & net amounts,
 * and auto-advances the status to Stage 3 if all attachments are already approved.
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
$taxType = trim($inputData['tax_type'] ?? '');

if ($transactionId <= 0 || empty($taxType)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction ID and tax classification type are required.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Only ACCTG Support / Accounting Staff / Super Admin can set tax classification
$authorized = ($userRole === 'Super Admin' || $userRole === 'Accounting Staff' || $userPosition === 'Accounting Support');
if (!$authorized) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only ACCTG Support staff can set tax classification.']);
    exit;
}

try {
    // 1. Verify transaction is at Stage 2 and fetch its gross amount
    $stmt = $fastPDO->prepare("SELECT id, current_status, amount, tracking_number FROM transactions WHERE id = :id LIMIT 1");
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

    // 2. Fetch the percentage from tax_configurations
    $taxStmt = $fastPDO->prepare("SELECT tax_percentage FROM tax_configurations WHERE tax_type = :tax_type AND is_active = 1 LIMIT 1");
    $taxStmt->execute(['tax_type' => $taxType]);
    $taxPercentage = $taxStmt->fetchColumn();

    if ($taxPercentage === false) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid or inactive tax type selected.']);
        exit;
    }

    $fastPDO->beginTransaction();

    // 3. Update tax_type in document_details (or insert if not exists)
    $checkDoc = $fastPDO->prepare("SELECT COUNT(*) FROM document_details WHERE transaction_id = :tx_id");
    $checkDoc->execute(['tx_id' => $transactionId]);
    if ($checkDoc->fetchColumn() > 0) {
        $updateDoc = $fastPDO->prepare("UPDATE document_details SET tax_type = :tax_type WHERE transaction_id = :tx_id");
        $updateDoc->execute(['tax_type' => $taxType, 'tx_id' => $transactionId]);
    } else {
        $insertDoc = $fastPDO->prepare("INSERT INTO document_details (transaction_id, tax_type) VALUES (:tx_id, :tax_type)");
        $insertDoc->execute(['tx_id' => $transactionId, 'tax_type' => $taxType]);
    }

    // 4. Compute amounts and update transactions table
    $grossAmount = (float)$transaction['amount'];
    $taxAmount = $grossAmount * ((float)$taxPercentage / 100);
    $netAmount = $grossAmount - $taxAmount;

    $updateTx = $fastPDO->prepare("
        UPDATE transactions 
        SET tax_amount = :tax_amount, net_amount = :net_amount 
        WHERE id = :id
    ");
    $updateTx->execute([
        'tax_amount' => $taxAmount,
        'net_amount' => $netAmount,
        'id' => $transactionId
    ]);

    // 5. Check if ALL attachments for this transaction are already approved
    $checkStmt = $fastPDO->prepare("
        SELECT COUNT(*) as total, 
               SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count
        FROM attachment_approvals 
        WHERE transaction_id = :tx_id
    ");
    $checkStmt->execute(['tx_id' => $transactionId]);
    $counts = $checkStmt->fetch();

    $allApproved = ($counts['total'] > 0 && $counts['approved_count'] == $counts['total']);
    $autoAdvanced = false;

    if ($allApproved) {
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
            'remarks' => "Tax classification set to '{$taxType}'. All attachments approved. Auto-advanced to Stage 3 (Budget)."
        ]);

        AuditLogService::log(
            $fastPDO, $userId,
            "Tax classification set and transaction auto-advanced: {$transaction['tracking_number']}",
            ['status' => 'Pending ACCTG Support', 'tax_type' => null],
            ['status' => 'Pending Budget', 'tax_type' => $taxType]
        );

        $autoAdvanced = true;
    } else {
        // Just log the tax classification update
        AuditLogService::log(
            $fastPDO, $userId,
            "Updated tax classification to '{$taxType}' for transaction: {$transaction['tracking_number']}",
            ['tax_type' => null],
            ['tax_type' => $taxType]
        );
    }

    $fastPDO->commit();

    $message = "Tax classification updated to '{$taxType}' successfully.";
    if ($autoAdvanced) {
        $message .= " All attachments approved — transaction advanced to Pending Budget.";
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'auto_advanced' => $autoAdvanced,
        'data' => [
            'tax_amount' => round($taxAmount, 2),
            'net_amount' => round($netAmount, 2),
            'tax_percentage' => (float)$taxPercentage
        ]
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Update tax classification error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during tax classification update.']);
}
