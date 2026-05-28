<?php
/**
 * Inbound Sync Receiver API for SDO-BAC Ingestion.
 * Receives procurement approval events and creates FAST draft transactions.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Included to expose global configurations
require_once __DIR__ . '/../../services/IntegrationAuthService.php';
require_once __DIR__ . '/../../services/FastIntegrationService.php';
require_once __DIR__ . '/../../services/SyncLogService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// 1. Authenticate Request using IntegrationAuthService
$authSystem = IntegrationAuthService::authenticate($fastPDO);
if (!$authSystem) {
    http_response_code(401);
    
    // Log auth failure for auditing
    SyncLogService::log(
        $fastPDO,
        'SDO-BAC',
        'SDO-FAST',
        'INBOUND_CONNECT',
        null,
        'FAILED_AUTH',
        'Unauthorized Bearer Token supplied by integration request.'
    );

    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized: Invalid or missing integration Bearer token.'
    ]);
    exit;
}

// 2. Parse Input Payload
$inputData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($inputData)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or empty JSON integration payload.'
    ]);
    exit;
}

$refNumber = trim($inputData['reference_number'] ?? '');
$eventType = trim($inputData['event_type'] ?? '');
$payload = $inputData['payload'] ?? [];

if (empty($refNumber) || empty($eventType) || empty($payload)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Unprocessable Entity: Missing root parameters (reference_number, event_type, payload).'
    ]);
    exit;
}

// 3. Process Inbound procurement approved event
if ($eventType === 'PROCUREMENT_APPROVED') {
    // Reconstruct input fields for FastIntegrationService
    $syncData = [
        'reference_number' => $refNumber,
        'reference_id' => $payload['reference_id'] ?? 0,
        'project_number' => $payload['project_number'] ?? '',
        'procurement_type' => $payload['procurement_type'] ?? 'Goods',
        'particulars' => $payload['particulars'] ?? '',
        'amount' => $payload['amount'] ?? 0.00
    ];

    $result = FastIntegrationService::processBacProcurement($syncData, $fastPDO);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'tracking_number' => $result['tracking_number']
            ]
        ]);
        
        // Log sync success
        SyncLogService::log(
            $fastPDO,
            'SDO-BAC',
            'SDO-FAST',
            $eventType,
            $refNumber,
            'SUCCESS',
            "Draft generated successfully: {$result['tracking_number']}"
        );
    } else {
        // Return 422 if duplicate or payload is invalid
        $httpCode = $result['status'] === 'DUPLICATE' ? 409 : 422;
        http_response_code($httpCode);
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
        
        // Log failure in integration_logs
        SyncLogService::log(
            $fastPDO,
            'SDO-BAC',
            'SDO-FAST',
            $eventType,
            $refNumber,
            $result['status'],
            $result['message']
        );
    }
} else {
    // Event type not supported
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "Unsupported integration event type: '{$eventType}'."
    ]);
}
