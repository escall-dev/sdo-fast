<?php
/**
 * Profile Update API for SDO FAST.
 * Allows any authenticated user to update their own profile info and password.
 * Actions: ?action=info (update name/email/username), ?action=password (change password)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = trim($_GET['action'] ?? '');

try {
    if ($action === 'info') {
        // ── Update profile information ──
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');

        if (empty($name) || empty($email) || empty($username)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Full name, email, and username are all required.']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        // Fetch current data for audit log comparison
        $currStmt = $fastPDO->prepare("SELECT full_name, email, username FROM users WHERE id = :id LIMIT 1");
        $currStmt->execute(['id' => $userId]);
        $oldData = $currStmt->fetch();

        if (!$oldData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User account not found.']);
            exit;
        }

        // Check for duplicate email/username (excluding self)
        $checkStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE (email = :email OR username = :username) AND id != :id");
        $checkStmt->execute(['email' => $email, 'username' => $username, 'id' => $userId]);
        if ($checkStmt->fetchColumn() > 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Email or username is already used by another account.']);
            exit;
        }

        // Update
        $updateStmt = $fastPDO->prepare("
            UPDATE users 
            SET full_name = :full_name, email = :email, username = :username 
            WHERE id = :id
        ");
        $updateStmt->execute([
            'full_name' => $name,
            'email' => $email,
            'username' => $username,
            'id' => $userId
        ]);

        // Update session so sidebar/navbar reflect immediately on next reload
        $_SESSION['user_name'] = $name;

        // Audit log
        AuditLogService::log(
            $fastPDO,
            $userId,
            "Updated own profile information",
            ['full_name' => $oldData['full_name'], 'email' => $oldData['email'], 'username' => $oldData['username']],
            ['full_name' => $name, 'email' => $email, 'username' => $username]
        );

        echo json_encode(['success' => true, 'message' => 'Profile information updated successfully.']);
        exit;

    } elseif ($action === 'password') {
        // ── Change password ──
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match.']);
            exit;
        }

        if (strlen($newPassword) < 6) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long.']);
            exit;
        }

        // Verify current password
        $stmt = $fastPDO->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $fastPDO->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute(['password' => $hashedPassword, 'id' => $userId]);

        // Audit log
        AuditLogService::log($fastPDO, $userId, "Changed own password", null, null);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        exit;

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action. Use ?action=info or ?action=password']);
        exit;
    }

} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A system error occurred. Please try again.']);
}
