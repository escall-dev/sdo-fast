<?php
/**
 * Get User Permissions API for SDO FAST.
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
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admins can access user permissions.']);
    exit;
}

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($userId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

try {
    // Fetch custom permissions for this user
    $stmt = $fastPDO->prepare("SELECT permission_key, is_enabled FROM user_permissions WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $permissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Convert values to booleans/integers
    $formatted = [];
    foreach ($permissions as $key => $val) {
        $formatted[$key] = (int)$val;
    }

    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'permissions' => $formatted
    ]);

} catch (PDOException $e) {
    error_log("Get permissions failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
