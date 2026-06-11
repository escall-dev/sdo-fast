<?php
/**
 * Get Role Permissions API for SDO FAST.
 * Restricts updates to Super Admin.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
if ($userRole !== 'Super Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admins can access permissions.']);
    exit;
}

$roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
if ($roleId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid role ID.']);
    exit;
}

try {
    // 1. Fetch role name to get defaults if needed
    $roleStmt = $fastPDO->prepare("SELECT role_name FROM roles WHERE id = :id LIMIT 1");
    $roleStmt->execute(['id' => $roleId]);
    $role = $roleStmt->fetch();
    
    if (!$role) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Role not found.']);
        exit;
    }
    
    $roleName = $role['role_name'];

    // 2. Fetch custom permissions for this role
    $stmt = $fastPDO->prepare("SELECT permission_key, is_enabled FROM role_permissions WHERE role_id = :role_id");
    $stmt->execute(['role_id' => $roleId]);
    $permissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Default permissions mapping fallback if no entries saved in DB yet
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
    
    if ($roleName === 'Super Admin') {
        foreach ($defaults as $k => $v) {
            $defaults[$k] = 1;
        }
    } elseif ($roleName === 'Admin') {
        $defaults['view'] = 1;
        $defaults['encode'] = 1;
        $defaults['edit'] = 1;
        $defaults['approve'] = 1;
    } elseif ($roleName === 'Accounting Staff') {
        $defaults['view'] = 1;
        $defaults['encode'] = 1;
        $defaults['approve'] = 1;
    } else {
        $defaults['view'] = 1;
    }

    $formatted = [];
    $allowedKeys = array_keys($defaults);
    foreach ($allowedKeys as $key) {
        $formatted[$key] = isset($permissions[$key]) ? (int)$permissions[$key] : (int)$defaults[$key];
    }

    echo json_encode([
        'success' => true,
        'role_id' => $roleId,
        'role_name' => $roleName,
        'permissions' => $formatted
    ]);

} catch (PDOException $e) {
    error_log("Get role permissions failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
