<?php
/**
 * Password Reset Submission Processor API for SDO FAST.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate CSRF token (normally executed by auth.php but since public routes skip auth.php checks, we enforce it here manually)
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    $_SESSION['flash_error'] = 'Session expired. Please try again.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

if (empty($token) || empty($password) || empty($confirmPassword)) {
    $_SESSION['flash_error'] = 'All password fields are required.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['flash_error'] = 'Passwords do not match. Please verify.';
    header('Location: ' . env('APP_URL') . '/views/reset-password.php?token=' . urlencode($token));
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['flash_error'] = 'Password must be at least 8 characters long.';
    header('Location: ' . env('APP_URL') . '/views/reset-password.php?token=' . urlencode($token));
    exit;
}

if ($fastPDO === null) {
    $_SESSION['flash_error'] = 'Database connection failure. Please contact administrator.';
    header('Location: ' . env('APP_URL') . '/login.php');
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
        $_SESSION['flash_error'] = 'This password reset link is invalid or has expired.';
        header('Location: ' . env('APP_URL') . '/login.php');
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

    $_SESSION['flash_success'] = 'Password updated successfully! You can now log in with your new credentials.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Password reset processing error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'An unexpected database error occurred. Please try again.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}
