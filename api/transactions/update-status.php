<?php
/**
 * Transaction Workflow Status Update API for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces authorization
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

// Valid Status List
$allowedStatuses = ['Pending Accountant 1', 'Pending Support', 'Pending Budget Check', 'Pending Accountant 2', 'Pending Final Approval', 'Approved', 'Rejected', 'Returned'];
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
    
    // 2. Validate Role-Based Transition Permissions
    $authorized = false;
    if ($userRole === 'Super Admin') {
        $authorized = true;
    } elseif ($userPosition === 'Accountant') {
        // Accountant checks first (Pending Accountant 1 -> Pending Support)
        if ($oldStatus === 'Pending Accountant 1' && in_array($newStatus, ['Pending Support', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
        // Accountant checks second (Pending Accountant 2 -> Pending Final Approval)
        elseif ($oldStatus === 'Pending Accountant 2' && in_array($newStatus, ['Pending Final Approval', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Accounting Staff' || $userPosition === 'Accounting Support') {
        // Accounting Support checks (Pending Support -> Pending Budget Check)
        if ($oldStatus === 'Pending Support' && in_array($newStatus, ['Pending Budget Check', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Budget Officer' || $userPosition === 'Budget Officer') {
        // Budget Officer checks (Pending Budget Check -> Pending Accountant 2)
        if ($oldStatus === 'Pending Budget Check' && in_array($newStatus, ['Pending Accountant 2', 'Returned', 'Rejected'])) {
            $authorized = true;
        }
    } elseif ($userRole === 'Approver' || $userPosition === 'ASDS' || $userPosition === 'SDS') {
        // Final Approver (ASDS/SDS) signs off (Pending Final Approval -> Approved)
        if ($oldStatus === 'Pending Final Approval' && in_array($newStatus, ['Approved', 'Returned', 'Rejected'])) {
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

    // 5. Update Document Details (DV / BIR Numbers) if provided
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

    // 8. Dynamic Integration Sync Trigger (Phase 13 hook)
    // We will initiate a service sync to BAC asynchronously if payment released or approved
    if (in_array($newStatus, ['Approved', 'Rejected'])) {
        try {
            // Include service loader
            require_once __DIR__ . '/../../services/BacIntegrationService.php';
            // Call integration handler to update BAC. We catch exceptions so it doesn't rollback FAST db
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
