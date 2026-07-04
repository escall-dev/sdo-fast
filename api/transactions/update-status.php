<?php
/**
 * Transaction Workflow Status Update API for SDO FAST.
 * Updated for 6-Stage Workflow v3.
 *
 * Stage Flow (v3):
 *   Stage 1: Requestor submits (no docs)     → Pending Budget
 *   Stage 2: Budget verifies funds           → Pending Requestor  (back to Requestor)
 *   Stage 3: Requestor uploads docs          → Pending Accounting Support   (via resubmit-documents.php)
 *   Stage 4: ACCTG Support inspects docs     → Pending Signatories
 *   Stage 5: Signatories approve             → Pending Signatory Approval
 *   Stage 6a: Cashier accepts                → Awaiting Payment
 *   Stage 6b: Cashier releases               → Released
 *
 * This endpoint handles:
 *   - Stage 4: ACCTG Support → Pending Signatories (forward), Returned, Rejected
 *   - Stage 6: Cashier → Released, Returned, Rejected
 *   - Any stage: Return or Reject (by authorized role)
 *   - Super Admin: any transition
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

// Support JSON input payloads
$inputData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($inputData)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request payload.'
    ]);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

$transactionId = (int)($inputData['transaction_id'] ?? 0);
$newStatus = trim($inputData['new_status'] ?? '');
$remarks = trim($inputData['remarks'] ?? '');
$dvNumber = trim($inputData['dv_number'] ?? '');
$birNumber = trim($inputData['bir_2307_number'] ?? '');

if ($transactionId <= 0 || empty($newStatus) || empty($remarks)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Transaction ID, new status, and action remarks are required.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Valid Status List (5-stage workflow v3)
$allowedStatuses = [
    'Pending Budget',              // Stage 1
    'Pending Requestor',           // Stage 2
    'Pending Accounting Support',  // Stage 3
    'Pending Signatory Approval',  // Stage 4 — Document for Approval and Signature
    'For Payment',                 // Stage 5 — awaiting Cashier release
    'Released',                    // Stage 6 — Payment Released
    'Pending Liquidation',         // Stage 7 — Liquidation tracking (Cash Advance only)
    'Liquidated',                  // Stage 8 — Liquidation complete
    'Rejected',
    'Returned'
];

if (!in_array($newStatus, $allowedStatuses)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid target workflow state.'
    ]);
    exit;
}

try {
    // 1. Fetch current transaction details
    $stmt = $fastPDO->prepare("SELECT * FROM transactions WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(444);
        echo json_encode([
            'success' => false,
            'message' => 'Transaction record not found.'
        ]);
        exit;
    }

    $oldStatus = $transaction['current_status'];
    
    // 2. Validate Role-Based Transition Permissions (6-Stage Workflow v3)
    $authorized = false;

    if ($userRole === 'Super Admin') {
        // Super Admin can do anything
        $authorized = true;
    } elseif ($userRole === 'Accounting Staff' || $userPosition === 'Accounting Support') {
        // Stage 3 (Pending Accounting Support): Document Inspection → forward to Signatory Approval, return, reject
        if ($oldStatus === 'Pending Accounting Support' && in_array($newStatus, ['Pending Signatory Approval', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
        // Liquidation Stage: Accounting Support marks Cash Advance as Pending Liquidation from Released, and then Liquidated
        if ($oldStatus === 'Released' && $newStatus === 'Pending Liquidation') {
            $authorized = true;
        }
        if ($oldStatus === 'Pending Liquidation' && in_array($newStatus, ['Liquidated', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
        // Can return/reject at any stage they have visibility of
        if (in_array($oldStatus, ['Pending Requestor', 'Pending Budget']) && in_array($newStatus, ['Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userPosition === 'Accountant') {
        // Accountant handles Stage 3 (Pending Accounting Support) — Document Inspection
        if ($oldStatus === 'Pending Accounting Support' && in_array($newStatus, ['Pending Signatory Approval', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Budget Officer' || $userPosition === 'Budget Officer') {
        // Stage 1 (Pending Budget): Budget Officer uses budget-check.php for forward,
        // but can also return/reject via this endpoint
        if ($oldStatus === 'Pending Budget' && in_array($newStatus, ['Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userPosition === 'ASDS' || $userPosition === 'SDS') {
        // Stage 4 (Pending Signatory Approval): Signatories approve → For Payment
        if ($oldStatus === 'Pending Signatory Approval' && in_array($newStatus, ['For Payment', 'Returned'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Cashier' || stripos($userPosition, 'Cashier') !== false) {
        // Stage 5 (For Payment / Awaiting Payment): Cashier releases → Released
        if (in_array($oldStatus, ['For Payment', 'Awaiting Payment']) && in_array($newStatus, ['Released', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    }

    if (!$authorized) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => "Forbidden: You are not authorized to transition this transaction from '{$oldStatus}' to '{$newStatus}'."
        ]);
        exit;
    }

    // 2b. Pre-flight validation: When forwarding from Pending Accounting Support → Pending Signatory Approval,
    // ensure ALL attachments are approved and tax classification is set (matching auto-advance rules
    // in approve-attachment.php and update-tax-classification.php).
    if ($oldStatus === 'Pending Accounting Support' && $newStatus === 'Pending Signatory Approval') {
        // Check attachments exist and are all approved
        $attCheckStmt = $fastPDO->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                   SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                   SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
            FROM attachment_approvals
            WHERE transaction_id = :tx_id
        ");
        $attCheckStmt->execute(['tx_id' => $transactionId]);
        $attCounts = $attCheckStmt->fetch();

        if ((int)$attCounts['total'] === 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Cannot forward: No attachments have been uploaded for this transaction. All Mandatory Documentary Requirements must be submitted and approved before routing to Signatories.'
            ]);
            exit;
        }

        if ((int)$attCounts['pending_count'] > 0 || (int)$attCounts['rejected_count'] > 0) {
            $pendingMsg = (int)$attCounts['pending_count'] > 0 ? $attCounts['pending_count'] . ' pending' : '';
            $rejectedMsg = (int)$attCounts['rejected_count'] > 0 ? $attCounts['rejected_count'] . ' rejected' : '';
            $detailMsg = implode(' and ', array_filter([$pendingMsg, $rejectedMsg]));
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => "Cannot forward: {$detailMsg} attachment(s) require review. All attachments must be approved before routing to Signatories."
            ]);
            exit;
        }

        // Check tax classification is set
        $taxCheckStmt = $fastPDO->prepare("SELECT tax_type FROM document_details WHERE transaction_id = :tx_id LIMIT 1");
        $taxCheckStmt->execute(['tx_id' => $transactionId]);
        $existingTaxType = $taxCheckStmt->fetchColumn();

        if (empty($existingTaxType)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Cannot forward: Tax classification must be set before routing to Signatories.'
            ]);
            exit;
        }
    }

    // 3. Begin Database Transaction
    $fastPDO->beginTransaction();

    // 4. Update Transaction Status
    $updateStmt = $fastPDO->prepare("
        UPDATE transactions 
        SET current_status = :new_status, remarks = :remarks 
        WHERE id = :id
    ");
    $updateStmt->execute([
        'new_status' => $newStatus,
        'remarks' => $remarks,
        'id' => $transactionId
    ]);

    // 5. Update Document Details (DV / BIR Numbers) if provided — Stage 4 (ACCT Support)
    if (!empty($dvNumber) || !empty($birNumber)) {
        // Check if details exist
        $docStmt = $fastPDO->prepare("SELECT id FROM document_details WHERE transaction_id = :id LIMIT 1");
        $docStmt->execute(['id' => $transactionId]);
        $docExists = $docStmt->fetchColumn();

        if ($docExists) {
            $updateDocSql = "
                UPDATE document_details 
                SET dv_number = COALESCE(NULLIF(:dv_num, ''), dv_number),
                    bir_2307_number = COALESCE(NULLIF(:bir_num, ''), bir_2307_number)
                WHERE transaction_id = :id
            ";
            $updateDocStmt = $fastPDO->prepare($updateDocSql);
            $updateDocStmt->execute([
                'dv_num' => $dvNumber,
                'bir_num' => $birNumber,
                'id' => $transactionId
            ]);
        } else {
            $insertDocSql = "
                INSERT INTO document_details (transaction_id, dv_number, bir_2307_number) 
                VALUES (:id, :dv_num, :bir_num)
            ";
            $insertDocStmt = $fastPDO->prepare($insertDocSql);
            $insertDocStmt->execute([
                'id' => $transactionId,
                'dv_num' => $dvNumber,
                'bir_num' => $birNumber
            ]);
        }
    }

    // 6. Insert Status Log
    // Signatory approval stays under Document for Approval and Signature (same-status log).
    // Cashier release additionally creates a For Payment entry to attribute Release of Payment to Cashier.
    if ($oldStatus === 'Pending Signatory Approval' && $newStatus === 'For Payment') {
        // Signatory approval: log as same-status so it appears under Document for Approval and Signature
        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:transaction_id, :prev_status, :prev_status2, :changed_by, :remarks)
        ");
        $logStmt->execute([
            'transaction_id' => $transactionId,
            'prev_status' => $oldStatus,
            'prev_status2' => $oldStatus,
            'changed_by' => $userId,
            'remarks' => $remarks
        ]);
    } elseif (in_array($oldStatus, ['For Payment', 'Awaiting Payment']) && $newStatus === 'Released') {
        // Cashier release: add a For Payment entry attributed to Cashier, then the Released entry
        $cashierLogStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:transaction_id, 'Pending Signatory Approval', 'For Payment', :changed_by, :remarks)
        ");
        $cashierLogStmt->execute([
            'transaction_id' => $transactionId,
            'changed_by' => $userId,
            'remarks' => 'Payment processed and released by Cashier.'
        ]);

        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:transaction_id, :prev_status, :new_status, :changed_by, :remarks)
        ");
        $logStmt->execute([
            'transaction_id' => $transactionId,
            'prev_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'remarks' => $remarks
        ]);
    } else {
        $logStmt = $fastPDO->prepare("
            INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
            VALUES (:transaction_id, :prev_status, :new_status, :changed_by, :remarks)
        ");
        $logStmt->execute([
            'transaction_id' => $transactionId,
            'prev_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'remarks' => $remarks
        ]);
    }

    // 7. Audit System Change
    AuditLogService::log(
        $fastPDO, 
        $userId, 
        "Transaction Status changed: {$transaction['tracking_number']}", 
        ['status' => $oldStatus], 
        ['status' => $newStatus, 'remarks' => $remarks]
    );

    // Commit changes
    $fastPDO->commit();

    // 8. BAC Integration Sync — triggers on Released or Rejected
    if (in_array($newStatus, ['Released', 'Rejected'])) {
        try {
            require_once __DIR__ . '/../../services/BacIntegrationService.php';
            BacIntegrationService::syncStatusToBac($transactionId, $newStatus, $remarks, $dvNumber, $fastPDO);
        } catch (Exception $e) {
            error_log("Failed to sync status update to SDO-BAC: " . $e->getMessage());
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Transaction workflow status updated successfully to '{$newStatus}'."
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Workflow update processing error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected database error occurred during the status update.'
    ]);
}
