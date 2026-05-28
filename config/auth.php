<?php
/**
 * Role-Based Access Control Middleware & CSRF Verification for SDO FAST.
 */

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/env.php';

// Helper: Check if current script is in public lists
function isPublicRoute() {
    $currentScript = $_SERVER['SCRIPT_NAME'];
    $publicPatterns = [
        '/login.php',
        '/views/reset-password.php',
        '/api/auth/login.php',
        '/api/auth/reset-request.php',
        '/api/auth/reset-password.php',
        '/api/integrations/receive-bac.php',
        '/api/integrations/send-to-bac.php',
    ];
    foreach ($publicPatterns as $pattern) {
        if (strpos($currentScript, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

// 1. Enforce Authentication Check
if (!isPublicRoute()) {
    if (!isLoggedIn()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthenticated access.',
                'redirect' => env('APP_URL') . '/login.php'
            ]);
            exit;
        } else {
            $_SESSION['flash_error'] = 'Please log in to access this system.';
            header('Location: ' . env('APP_URL') . '/login.php');
            exit;
        }
    }

    // 2. Enforce Access Matrix
    $userRole = $_SESSION['user_role'] ?? '';
    $userPosition = $_SESSION['user_position'] ?? '';
    $currentUri = $_SERVER['REQUEST_URI'];
    $allowed = true;

    // Define restricted path checks
    if (strpos($currentUri, '/views/users/') !== false || strpos($currentUri, '/api/users/') !== false) {
        if ($userRole !== 'Super Admin') {
            $allowed = false;
        }
    }
    
    if (strpos($currentUri, '/views/settings/') !== false) {
        if ($userRole !== 'Super Admin') {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/views/integrations/') !== false || strpos($currentUri, '/api/integrations/') !== false) {
        if (!in_array($userRole, ['Super Admin', 'Admin', 'Accounting Staff']) &&
            !in_array($userPosition, ['Accounting Support', 'Accountant'])) {
            // Note: API receive-bac and send-to-bac are public routes because they authorize via bearer token, which is skipped above.
            if (strpos($currentUri, '/receive-bac.php') === false && strpos($currentUri, '/send-to-bac.php') === false) {
                $allowed = false;
            }
        }
    }

    if (strpos($currentUri, '/views/reports/') !== false || strpos($currentUri, '/api/reports/') !== false) {
        if (!in_array($userRole, ['Super Admin', 'Admin', 'Accounting Staff', 'Budget Officer', 'Approver']) &&
            !in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS'])) {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/views/transactions/submit.php') !== false || strpos($currentUri, '/api/transactions/submit-transaction.php') !== false) {
        if (!in_array($userRole, ['Super Admin', 'Admin', 'User', 'Requestor']) &&
            !in_array($userPosition, ['Personnel'])) {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/api/transactions/update-status.php') !== false) {
        if (!in_array($userRole, ['Super Admin', 'Admin', 'Accounting Staff', 'Budget Officer', 'Approver']) &&
            !in_array($userPosition, ['Accounting Support', 'Accountant', 'Budget Officer', 'ASDS', 'SDS'])) {
            $allowed = false;
        }
    }

    if (!$allowed) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Forbidden: You do not have permissions to perform this action.'
            ]);
            exit;
        } else {
            $_SESSION['flash_error'] = 'Access denied: Your role does not permit access to that page.';
            header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
            exit;
        }
    }
}

// 3. Centralized CSRF Protection for state-changing POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isPublicRoute()) {
    // Exclude SDO-BAC integration endpoints which use Bearer tokens
    $isIntegrationEndpoint = (strpos($_SERVER['SCRIPT_NAME'], '/api/integrations/') !== false);
    
    if (!$isIntegrationEndpoint) {
        $token = null;
        
        // Check X-CSRF-Token header first (API/Ajax requests)
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        } 
        // Check post parameter (Traditional form submissions)
        elseif (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        }

        if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
            header('Content-Type: application/json');
            http_response_code(419);
            echo json_encode([
                'success' => false,
                'message' => 'CSRF verification failed. Request expired.'
            ]);
            exit;
        }
    }
}
