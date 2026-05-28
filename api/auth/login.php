<?php
/**
 * Login Authenticator API for SDO FAST.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

$identity = trim($_POST['identity'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (empty($identity) || empty($password)) {
    $_SESSION['flash_error'] = 'Please enter both username/email and password.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

if ($fastPDO === null) {
    $_SESSION['flash_error'] = 'Database connection error. Please contact system admin.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

try {
    // Look up user and join role
    $stmt = $fastPDO->prepare("
        SELECT u.*, r.role_name, p.position_name 
        FROM users u 
        LEFT JOIN user_roles ur ON u.id = ur.user_id 
        LEFT JOIN roles r ON ur.role_id = r.id 
        LEFT JOIN positions p ON u.position_id = p.id 
        WHERE u.email = :email_identity OR u.username = :username_identity 
        LIMIT 1
    ");
    $stmt->execute([
        'email_identity' => $identity,
        'username_identity' => $identity
    ]);
    $user = $stmt->fetch();

    if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
        // Authenticated successfully!
        secureSessionRegenerate();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_uuid'] = $user['uuid'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_role'] = $user['role_name'] ?? 'User';
        $_SESSION['user_position'] = $user['position_name'] ?? '';
        
        // Log Login activity
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $logStmt = $fastPDO->prepare("
            INSERT INTO login_logs (user_id, ip_address, device_info) 
            VALUES (:user_id, :ip_address, :device_info)
        ");
        $logStmt->execute([
            'user_id' => $user['id'],
            'ip_address' => $ip,
            'device_info' => substr($userAgent, 0, 255)
        ]);

        // Insert into activity logs
        $actStmt = $fastPDO->prepare("
            INSERT INTO activity_logs (user_id, activity, ip_address) 
            VALUES (:user_id, 'User login successful', :ip_address)
        ");
        $actStmt->execute([
            'user_id' => $user['id'],
            'ip_address' => $ip
        ]);

        // Redirect to dashboard
        header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
        exit;
    } else {
        // Log authentication failure in activity logs
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $actStmt = $fastPDO->prepare("
            INSERT INTO activity_logs (user_id, activity, old_value, ip_address) 
            VALUES (NULL, 'Failed login attempt', :identity, :ip_address)
        ");
        $actStmt->execute([
            'identity' => json_encode(['identity' => $identity]),
            'ip_address' => $ip
        ]);

        $_SESSION['flash_error'] = 'Invalid email/username or password.';
        header('Location: ' . env('APP_URL') . '/login.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'An unexpected system error occurred. Please try again.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}
