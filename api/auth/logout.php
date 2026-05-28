<?php
/**
 * User Logout API for SDO FAST.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

if (isLoggedIn() && $fastPDO !== null) {
    try {
        $userId = $_SESSION['user_id'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Log logout action in activity_logs
        $actStmt = $fastPDO->prepare("
            INSERT INTO activity_logs (user_id, activity, ip_address) 
            VALUES (:user_id, 'User logout successful', :ip_address)
        ");
        $actStmt->execute([
            'user_id' => $userId,
            'ip_address' => $ip
        ]);
    } catch (PDOException $e) {
        error_log("Logout logging failure: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: ' . env('APP_URL') . '/login.php');
exit;
