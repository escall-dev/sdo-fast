<?php
/**
 * Get Transaction Details API for SDO FAST.
 * Returns attachment approvals, budget check details, and signatory tasks.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$transactionId = (int)($_GET['transaction_id'] ?? 0);

if ($transactionId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid transaction ID.']);
    exit;
}

try {
    // 1. Fetch transaction basic info
    $stmt = $fastPDO->prepare("
        SELECT t.*, u.full_name as requestor_name, u.email as requestor_email,
               d.dv_number, d.bir_2307_number, d.tax_type
        FROM transactions t
        LEFT JOIN users u ON t.requestor_id = u.id
        LEFT JOIN document_details d ON t.id = d.transaction_id
        WHERE t.id = :id LIMIT 1
    ");
    $stmt->execute(['id' => $transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    // Check data visibility permission
    // Admin/Staff can see it, Requestor can only see their own
    $userRole = $_SESSION['user_role'] ?? '';
    $userId = $_SESSION['user_id'] ?? 0;
    
    if ($userRole !== 'Super Admin' && $userRole !== 'Accounting Staff' && $userRole !== 'Budget Officer' && $userRole !== 'Cashier' && $userRole !== 'Approver') {
        if ($transaction['requestor_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: You do not have permission to view this transaction.']);
            exit;
        }
    }

    // 2. Fetch attachment approvals
    $stmt = $fastPDO->prepare("
        SELECT aa.*, u.full_name as reviewer_name 
        FROM attachment_approvals aa
        LEFT JOIN users u ON aa.reviewed_by = u.id
        WHERE aa.transaction_id = :tx_id
        ORDER BY aa.id ASC
    ");
    $stmt->execute(['tx_id' => $transactionId]);
    $attachments = $stmt->fetchAll();

    // 3. Fetch budget check
    $stmt = $fastPDO->prepare("
        SELECT bc.*, u.full_name as checker_name 
        FROM budget_checks bc
        LEFT JOIN users u ON bc.checked_by = u.id
        WHERE bc.transaction_id = :tx_id
        LIMIT 1
    ");
    $stmt->execute(['tx_id' => $transactionId]);
    $budgetCheck = $stmt->fetch() ?: null;

    // 4. Fetch signatory tasks
    $stmt = $fastPDO->prepare("
        SELECT st.*, u.full_name as completed_by_name 
        FROM signatory_tasks st
        LEFT JOIN users u ON st.completed_by = u.id
        WHERE st.transaction_id = :tx_id
        ORDER BY st.id ASC
    ");
    $stmt->execute(['tx_id' => $transactionId]);
    $signatoryTasks = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'transaction' => $transaction,
            'attachments' => $attachments,
            'budget_check' => $budgetCheck,
            'signatory_tasks' => $signatoryTasks
        ]
    ]);

} catch (Exception $e) {
    error_log("Get transaction details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
