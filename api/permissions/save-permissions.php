<?php
/**
 * Save User Permissions API for SDO FAST.
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
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admins can save user permissions.']);
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($userId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

// Allowed permission keys
$allowedKeys = [
    'view',
    'encode',
    'edit',
    'approve',
    'delete',
    'manage_users',
    'configure_system'
];

try {
    // 1. Fetch user to verify they exist and get details for logging
    $userStmt = $fastPDO->prepare("SELECT full_name FROM users WHERE id = :id LIMIT 1");
    $userStmt->execute(['id' => $userId]);
    $user = $userStmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // 2. Fetch old permissions for auditing
    $oldStmt = $fastPDO->prepare("SELECT permission_key, is_enabled FROM user_permissions WHERE user_id = :user_id");
    $oldStmt->execute(['user_id' => $userId]);
    $oldPermissions = $oldStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Fill missing defaults for old permissions representation
    $oldFormatted = [];
    foreach ($allowedKeys as $key) {
        $oldFormatted[$key] = isset($oldPermissions[$key]) ? (int)$oldPermissions[$key] : 0;
    }

    // 3. Process new permissions from POST
    $newFormatted = [];
    $fastPDO->beginTransaction();

    $stmt = $fastPDO->prepare("
        INSERT INTO user_permissions (user_id, permission_key, is_enabled) 
        VALUES (:user_id, :permission_key, :is_enabled)
        ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)
    ");

    foreach ($allowedKeys as $key) {
        // In checkbox submissions, we can send them as permissions[key] = 1 or 0
        $isEnabled = isset($_POST['permissions'][$key]) && $_POST['permissions'][$key] == 1 ? 1 : 0;
        $newFormatted[$key] = $isEnabled;

        $stmt->execute([
            'user_id' => $userId,
            'permission_key' => $key,
            'is_enabled' => $isEnabled
        ]);
    }

    // 4. Log audit log
    AuditLogService::log(
        $fastPDO,
        $adminId,
        "Updated permissions override for user: " . $user['full_name'],
        $oldFormatted,
        $newFormatted
    );

    $fastPDO->commit();
    echo json_encode(['success' => true, 'message' => 'Permissions updated successfully.']);

} catch (PDOException $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Save permissions failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred while saving.']);
}
