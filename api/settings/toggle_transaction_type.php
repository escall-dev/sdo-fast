<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

// Verify permission
if (!hasPermission('configure_system')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permission denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['is_active'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    $id = (int)$data['id'];
    $isActive = (int)$data['is_active'];

    $stmt = $fastPDO->prepare("UPDATE transaction_types SET is_active = ? WHERE id = ?");
    $stmt->execute([$isActive, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Transaction type status updated']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Transaction type not found or no change made']);
    }
} catch (PDOException $e) {
    error_log("Transaction type toggle error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
