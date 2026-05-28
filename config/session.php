<?php
/**
 * Session configuration and security helper for SDO FAST.
 */

require_once __DIR__ . '/env.php';

// Inactivity timeout limit in seconds (30 minutes)
define('SESSION_TIMEOUT_LIMIT', 1800); 

// Configure session cookie parameters
$cookieParams = [
    'lifetime' => 0, // Until browser closes
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

// Session timeout handling
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive = time() - $_SESSION['LAST_ACTIVITY'];
    if ($inactive > SESSION_TIMEOUT_LIMIT) {
        // Session expired, clear and destroy
        session_unset();
        session_destroy();
        
        // Redirect to login if this is a view request, or send unauthorized response if API
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Please log in again.'
            ]);
            exit;
        } else {
            // Re-start a fresh session to hold flash message
            session_start();
            $_SESSION['flash_error'] = 'Your session has expired due to inactivity. Please log in again.';
            header('Location: ' . env('APP_URL') . '/login.php');
            exit;
        }
    }
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

/**
 * Regenerates the session ID to prevent session fixation attacks.
 */
function secureSessionRegenerate() {
    // Regenerate session ID and delete old session file
    session_regenerate_id(true);
}

/**
 * Helper to check if a user is logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}
