<?php
/**
 * Outbound Manual Retry Synchronization API for SDO FAST.
 * Invokes BacIntegrationService to re-attempt failed sync logs.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth & CSRF
require_once __DIR__ . '/../../services/BacIntegrationService.php';

// Handle GET request for integration fetch
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/../../services/IntegrationAuthService.php';
    
    // Authenticate BACtrack request using IntegrationAuthService
    $authSystem = IntegrationAuthService::authenticate($fastPDO);
    if (!$authSystem) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized: Invalid or missing integration Bearer token.'
        ]);
        exit;
    }

    try {
        // Fetch transactions linked to BAC track (i.e. bac_reference_number is not null)
        // We can fetch those that are not fully Approved/Rejected in SDO-BACtrack yet or simply all of them
        $stmt = $fastPDO->query("
            SELECT t.*, u.full_name as requestor_name, d.dv_number, d.bir_2307_number, d.tax_type
            FROM transactions t
            LEFT JOIN users u ON t.requestor_id = u.id
            LEFT JOIN document_details d ON t.id = d.transaction_id
            WHERE t.bac_reference_number IS NOT NULL
            ORDER BY t.id DESC
        ");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => 'Pending transactions retrieved successfully.',
            'transactions' => $transactions
        ]);
        exit;

    } catch (PDOException $e) {
        error_log("Failed to fetch pending transactions: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'A database error occurred.'
        ]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
$adminId = $_SESSION['user_id'] ?? null;

// Restrict to Super Admin and Accounting Staff
if (!in_array($userRole, ['Super Admin', 'Accounting Staff'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Unauthorized action.']);
    exit;
}

$logId = isset($_POST['log_id']) ? (int)$_POST['log_id'] : 0;

if ($logId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid Log ID parameter.']);
    exit;
}

try {
    $success = BacIntegrationService::retryLogSync($logId, $fastPDO);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Manual synchronization completed successfully. Linked system has been updated.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Manual synchronization attempt failed. Please check cURL connectivity or target system availability.'
        ]);
    }
} catch (Exception $e) {
    error_log("Manual sync endpoint error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected system error occurred during sync retry.']);
}
