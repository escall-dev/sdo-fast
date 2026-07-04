<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/database.php';

date_default_timezone_set('Asia/Manila');

define('SESSION_TIMEOUT_LIMIT', 300);

$cookieParams = [
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
];

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params($cookieParams);
    session_start();
}

// Auto-login via remember-me cookie if no active session
if (!isLoggedIn() && isset($_COOKIE['remember_me'])) {
    handleRememberMe();
}

// Session timeout handling — try remember-me before expiring
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive = time() - $_SESSION['LAST_ACTIVITY'];
    if ($inactive > SESSION_TIMEOUT_LIMIT) {
        if (handleRememberMe()) {
            // Session restored via remember-me, continue
        } else {
            session_unset();
            session_destroy();

            if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Session expired. Please log in again.'
                ]);
                exit;
            } else {
                session_start();
                $_SESSION['flash_error'] = 'Your session has expired due to inactivity. Please log in again.';
                header('Location: ' . env('APP_URL') . '/login.php');
                exit;
            }
        }
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

function secureSessionRegenerate() {
    session_regenerate_id(true);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

function handleRememberMe() {
    if (!isset($_COOKIE['remember_me'])) {
        return false;
    }

    global $fastPDO;
    if ($fastPDO === null) {
        return false;
    }

    $token = $_COOKIE['remember_me'];
    $hash = hash('sha256', $token);

    try {
        $stmt = $fastPDO->prepare("
            SELECT u.*, r.role_name, p.position_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN positions p ON u.position_id = p.id
            WHERE u.remember_token = :token AND u.status = 'active'
            LIMIT 1
        ");
        $stmt->execute(['token' => $hash]);
        $user = $stmt->fetch();

        if ($user) {
            secureSessionRegenerate();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_uuid'] = $user['uuid'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role_name'] ?? 'User';
            $_SESSION['user_position'] = $user['position_name'] ?? '';
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $newToken = bin2hex(random_bytes(64));
            $newHash = hash('sha256', $newToken);
            $upd = $fastPDO->prepare("UPDATE users SET remember_token = :hash WHERE id = :id");
            $upd->execute(['hash' => $newHash, 'id' => $user['id']]);
            setcookie('remember_me', $newToken, time() + 86400 * 30, '/', '', isset($_SERVER['HTTPS']), true);

            return true;
        }
    } catch (PDOException $e) {
        error_log("Remember-me error: " . $e->getMessage());
    }

    setcookie('remember_me', '', time() - 3600, '/');
    return false;
}
