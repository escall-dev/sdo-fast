<?php
/**
 * Save Role Permissions API for SDO FAST.
 * Restricts updates to Super Admin and audits mutations.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$adminId = $_SESSION['user_id'] ?? null;

if ($userRole !== 'Super Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admins can save role permissions.']);
    exit;
}

$roleId = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
if ($roleId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid role ID.']);
    exit;
}

// Allowed permission keys (now including view_bactrack)
$allowedKeys = [
    'view',
    'encode',
    'edit',
    'approve',
    'delete',
    'manage_users',
    'configure_system',
    'view_bactrack'
];

try {
    // 1. Fetch role to verify it exists and get details for logging
    $roleStmt = $fastPDO->prepare("SELECT role_name FROM roles WHERE id = :id LIMIT 1");
    $roleStmt->execute(['id' => $roleId]);
    $role = $roleStmt->fetch();

    if (!$role) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Role not found.']);
        exit;
    }

    $roleName = $role['role_name'];

    // 2. Fetch old permissions for auditing
    $oldStmt = $fastPDO->prepare("SELECT permission_key, is_enabled FROM role_permissions WHERE role_id = :role_id");
    $oldStmt->execute(['role_id' => $roleId]);
    $oldPermissions = $oldStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Default permissions mapping fallback for old permissions representation if no entries exist yet
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

    $oldFormatted = [];
    foreach ($allowedKeys as $key) {
        $oldFormatted[$key] = isset($oldPermissions[$key]) ? (int)$oldPermissions[$key] : (int)$defaults[$key];
    }

    // 3. Process new permissions from POST
    $newFormatted = [];
    $fastPDO->beginTransaction();

    $stmt = $fastPDO->prepare("
        INSERT INTO role_permissions (role_id, permission_key, is_enabled) 
        VALUES (:role_id, :permission_key, :is_enabled)
        ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)
    ");

    foreach ($allowedKeys as $key) {
        // In checkbox submissions, we send them as permissions[key] = 1 or 0
        $isEnabled = isset($_POST['permissions'][$key]) && $_POST['permissions'][$key] == 1 ? 1 : 0;
        $newFormatted[$key] = $isEnabled;

        $stmt->execute([
            'role_id' => $roleId,
            'permission_key' => $key,
            'is_enabled' => $isEnabled
        ]);
    }

    // 4. Log audit log
    AuditLogService::log(
        $fastPDO,
        $adminId,
        "Updated permissions for role: " . $roleName,
        $oldFormatted,
        $newFormatted
    );

    $fastPDO->commit();
    echo json_encode(['success' => true, 'message' => 'Role permissions updated successfully.']);

} catch (PDOException $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Save role permissions failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred while saving.']);
}
