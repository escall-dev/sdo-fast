<?php
/**
 * Chatbot History Retrieval API Endpoint for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Retrieve the last 50 chat logs for this user to restore context
    $stmt = $fastPDO->prepare("
        SELECT user_message, bot_response, created_at 
        FROM chatbot_logs 
        WHERE user_id = :user_id 
        ORDER BY created_at ASC, id ASC
        LIMIT 50
    ");
    $stmt->execute(['user_id' => $userId]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'history' => $logs
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve chat history: ' . $e->getMessage()
    ]);
}
