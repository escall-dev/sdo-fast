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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
$adminId = $_SESSION['user_id'];

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
