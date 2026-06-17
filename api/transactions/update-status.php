<?php
/**
 * Transaction Workflow Status Update API for SDO FAST.
 * Updated for 6-Stage Workflow v2.
 *
 * Stage Flow:
 *   Stage 1: Requestor submits         → Pending ACCTG Support
 *   Stage 2: ACCTG Support reviews     → Pending Budget          (via approve-attachment.php auto-advance)
 *   Stage 3: Budget checks funds       → Pending ACCT Support    (via budget-check.php)
 *   Stage 4: ACCT Support processes    → Pending Signatories
 *   Stage 5: Signatories complete      → Pending Cashier Release (via complete-signatory-task.php auto-advance)
 *   Stage 6: Cashier releases          → Released
 *
 * This endpoint handles:
 *   - Stage 4: ACCT Support → Pending Signatories (forward), Returned, Rejected
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

// Valid Status List (new 6-stage workflow)
$allowedStatuses = [
    'Pending ACCTG Support',    // Stage 2
    'Pending Budget',           // Stage 3
    'Pending ACCT Support',     // Stage 4
    'Pending Signatories',      // Stage 5
    'Pending Cashier Release',  // Stage 6
    'Released',                 // Final
    'Rejected',                 // Terminal
    'Returned'                  // Terminal
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
    
    // 2. Validate Role-Based Transition Permissions (6-Stage Workflow v2)
    $authorized = false;

    if ($userRole === 'Super Admin') {
        // Super Admin can do anything
        $authorized = true;
    } elseif ($userRole === 'Accounting Staff' || $userPosition === 'Accounting Support') {
        // Stage 2: ACCTG Support reviews attachments (auto-advanced via approve-attachment.php)
        // They can also return/reject at Stage 2
        if ($oldStatus === 'Pending ACCTG Support' && in_array($newStatus, ['Returned', 'Rejected'])) {
            $authorized = true;
        }
        // Stage 4: ACCT Support processes → Pending Signatories
        if ($oldStatus === 'Pending ACCT Support' && in_array($newStatus, ['Pending Signatories', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userPosition === 'Accountant') {
        // Accountant can also handle Stage 4 (ACCT Support)
        if ($oldStatus === 'Pending ACCT Support' && in_array($newStatus, ['Pending Signatories', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Budget Officer' || $userPosition === 'Budget Officer') {
        // Stage 3: Budget check (handled primarily via budget-check.php)
        // Can also return/reject at Stage 3
        if ($oldStatus === 'Pending Budget' && in_array($newStatus, ['Pending ACCT Support', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Approver' || $userPosition === 'ASDS' || $userPosition === 'SDS') {
        // Stage 5: Signatories can return/reject
        if ($oldStatus === 'Pending Signatories' && in_array($newStatus, ['Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Cashier' || $userPosition === 'Cashier') {
        // Stage 6: Cashier releases payment
        if ($oldStatus === 'Pending Cashier Release' && in_array($newStatus, ['Released', 'Returned', 'Rejected'])) {
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
