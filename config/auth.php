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
        '/views/tracker/index.php',
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

// Load database connection
require_once __DIR__ . '/database.php';

if (!function_exists('hasPermission')) {
    function hasPermission($permissionKey) {
        if (!isLoggedIn()) {
            return false;
        }

        static $permissions = null;
        
        if ($permissions === null) {
            $permissions = [];
            $userId = $_SESSION['user_id'] ?? null;
            $userRole = $_SESSION['user_role'] ?? 'User';
            
            // 1. Get default permissions based on role
            $defaults = [
                'view' => 0,
                'encode' => 0,
                'edit' => 0,
                'approve' => 0,
                'delete' => 0,
                'manage_users' => 0,
                'configure_system' => 0,
                'view_bactrack' => 0
            ];
            
            if ($userRole === 'Super Admin') {
                foreach ($defaults as $k => $v) {
                    $defaults[$k] = 1;
                }
            } elseif ($userRole === 'Admin') {
                $defaults['view'] = 1;
                $defaults['encode'] = 1;
                $defaults['edit'] = 1;
                $defaults['approve'] = 1;
            } elseif ($userRole === 'Accounting Staff') {
                $defaults['view'] = 1;
                $defaults['encode'] = 1;
                $defaults['approve'] = 1;
            } else {
                $defaults['view'] = 1; // User or other roles default to view checked only
            }
            
            $permissions = $defaults;
            
            // 2. Query role permissions if user is logged in
            if ($userId) {
                global $fastPDO;
                if ($fastPDO !== null) {
                    try {
                        $stmt = $fastPDO->prepare("
                            SELECT rp.permission_key, rp.is_enabled 
                            FROM role_permissions rp
                            JOIN roles r ON rp.role_id = r.id
                            JOIN user_roles ur ON r.id = ur.role_id
                            WHERE ur.user_id = :user_id
                        ");
                        $stmt->execute(['user_id' => $userId]);
                        $rolePermissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                        if (!empty($rolePermissions)) {
                            // If role_permissions contains records, override the defaults
                            foreach ($rolePermissions as $k => $v) {
                                $permissions[$k] = (int)$v;
                            }
                        }
                    } catch (PDOException $e) {
                        error_log("Failed to query role_permissions: " . $e->getMessage());
                    }
                }
            }
        }
        
        return isset($permissions[$permissionKey]) && $permissions[$permissionKey] == 1;
    }
}

if (!function_exists('get_data_scope_filter')) {
    function get_data_scope_filter($userRole, $userId, $tableAlias = 't') {
        global $fastPDO;
        
        $scope = 'own'; // default fallback
        
        if ($fastPDO !== null) {
            try {
                $stmt = $fastPDO->prepare("SELECT scope FROM role_data_scope WHERE role = :role LIMIT 1");
                $stmt->execute(['role' => $userRole]);
                $res = $stmt->fetchColumn();
                if ($res) {
                    $scope = $res;
                }
            } catch (PDOException $e) {
                error_log("Failed to fetch role data scope: " . $e->getMessage());
            }
        }
        
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        $userIdInt = (int)$userId;
        
        if ($scope === 'all') {
            return "1=1";
        } elseif ($scope === 'assigned') {
            // Determine the pending status(es) assigned to this user role or position dynamically
            $userPosition = $_SESSION['user_position'] ?? '';
            
            // If user position is empty/not set in session (e.g. running in CLI or background sync), look it up in DB
            if (empty($userPosition) && $fastPDO !== null) {
                try {
                    $posStmt = $fastPDO->prepare("
                        SELECT p.position_name 
                        FROM users u 
                        LEFT JOIN positions p ON u.position_id = p.id 
                        WHERE u.id = :id 
                        LIMIT 1
                    ");
                    $posStmt->execute(['id' => $userIdInt]);
                    $userPosition = $posStmt->fetchColumn() ?: '';
                } catch (PDOException $e) {
                    error_log("Failed to query user position in get_data_scope_filter: " . $e->getMessage());
                }
            }

            $statuses = [];
            if ($userRole === 'Budget Officer' || $userPosition === 'Budget Officer') {
                $statuses[] = "'Pending Budget Check'";
            } elseif ($userRole === 'Approver' || $userPosition === 'ASDS' || $userPosition === 'SDS') {
                $statuses[] = "'Pending Final Approval'";
            } elseif ($userPosition === 'Accountant') {
                $statuses[] = "'Pending Accountant 1'";
                $statuses[] = "'Pending Accountant 2'";
            } else {
                // Default to Accounting Support / Accounting Staff / other assigned roles
                $statuses[] = "'Pending Support'";
            }

            $statusCondition = "{$prefix}current_status IN (" . implode(',', $statuses) . ")";
            return "({$prefix}created_by = {$userIdInt} OR {$statusCondition} OR {$prefix}id IN (SELECT DISTINCT transaction_id FROM transaction_status_logs WHERE changed_by = {$userIdInt}))";
        } else {
            // scope = own
            return "{$prefix}created_by = {$userIdInt}";
        }
    }
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
        if (!hasPermission('manage_users')) {
            $allowed = false;
        }
    }
    
    if (strpos($currentUri, '/views/settings/') !== false) {
        if (!hasPermission('configure_system')) {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/views/integrations/') !== false || strpos($currentUri, '/api/integrations/') !== false) {
        // Exclude public integration routes
        if (strpos($currentUri, '/receive-bac.php') === false && strpos($currentUri, '/send-to-bac.php') === false) {
            if (!hasPermission('configure_system')) {
                $allowed = false;
            }
        }
    }

    if (strpos($currentUri, '/views/reports/') !== false || strpos($currentUri, '/api/reports/') !== false) {
        if (!hasPermission('view')) {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/views/transactions/submit.php') !== false || strpos($currentUri, '/api/transactions/submit-transaction.php') !== false) {
        if (!hasPermission('encode')) {
            $allowed = false;
        }
    }

    if (strpos($currentUri, '/api/transactions/update-status.php') !== false) {
        if (!hasPermission('approve')) {
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
