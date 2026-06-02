<?php
/**
 * Password Reset Submission Processor API for SDO FAST.
 * Updated to return JSON for AJAX submission.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request format.']);
    exit;
}

$token = trim($input['token'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if (empty($token) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match. Please verify.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

if ($fastPDO === null) {
    echo json_encode(['success' => false, 'message' => 'Database connection failure.']);
    exit;
}

try {
    // 1. Fetch token and verify
    $stmt = $fastPDO->prepare("
        SELECT * FROM password_reset_tokens 
        WHERE token = :token AND used_at IS NULL AND expires_at > NOW() 
        LIMIT 1
    ");
    $stmt->execute(['token' => $token]);
    $tokenRow = $stmt->fetch();

    if (!$tokenRow) {
        echo json_encode(['success' => false, 'message' => 'This password reset link is invalid or has expired.']);
        exit;
    }

    $userId = $tokenRow['user_id'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 2. Begin Transaction
    $fastPDO->beginTransaction();

    // 3. Update user password
    $userStmt = $fastPDO->prepare("UPDATE users SET password = :password WHERE id = :id");
    $userStmt->execute([
        'password' => $hashedPassword,
        'id' => $userId
    ]);

    // 4. Invalidate Token
    $tokenStmt = $fastPDO->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE id = :id");
    $tokenStmt->execute(['id' => $tokenRow['id']]);

    // 5. Log Activity
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $actStmt = $fastPDO->prepare("
        INSERT INTO activity_logs (user_id, activity, ip_address) 
        VALUES (:user_id, 'Password updated via reset link', :ip)
    ");
    $actStmt->execute([
        'user_id' => $userId,
        'ip' => $ip
    ]);

    $fastPDO->commit();

    echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Password reset processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected database error occurred.']);
}
