<?php
/**
 * Chatbot Handler API Endpoint for SDO FAST.
 * Conducts session-level rate limiting and routes queries.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth
require_once __DIR__ . '/../../services/ChatbotService.php';

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

$userMessage = trim($inputData['message'] ?? '');

if (empty($userMessage)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Message query cannot be empty.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// 1. Session Rate Limiting Check (Limit to 1 message every 2 seconds to prevent spam)
$currentTime = time();
if (isset($_SESSION['chatbot_last_request_time'])) {
    $timeElapsed = $currentTime - $_SESSION['chatbot_last_request_time'];
    if ($timeElapsed < 2) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'You are sending messages too quickly. Please wait a moment.'
        ]);
        exit;
    }
}
$_SESSION['chatbot_last_request_time'] = $currentTime;

$activeTracking = trim($inputData['active_tracking'] ?? '');

// 2. Delegate query to ChatbotService fallback chain
$result = ChatbotService::processQuery($userMessage, $userId, $fastPDO, $activeTracking);

if ($result['success']) {
    echo json_encode([
        'success' => true,
        'message' => $result['message'],
        'provider' => $result['provider'] ?? 'Unknown'
    ]);
} else {
    // If all AI providers fail or rate-limit
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}
