<?php
/**
 * Registration OTP Verification and Account Creation API for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$otp = trim($_POST['otp'] ?? '');

if (empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
    exit;
}

// Check session
$sessionOtp = $_SESSION['register_otp'] ?? null;
$sessionTime = $_SESSION['register_otp_time'] ?? 0;
$regData = $_SESSION['register_data'] ?? null;

if (!$sessionOtp || !$regData || (time() - $sessionTime) > 900) {
    echo json_encode(['success' => false, 'message' => 'Verification code expired or session lost. Please register again.']);
    exit;
}

if ($otp !== $sessionOtp) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
    exit;
}

if ($fastPDO === null) {
    echo json_encode(['success' => false, 'message' => 'System database connection error.']);
    exit;
}

try {
    $fastPDO->beginTransaction();

    // Check once more if email exists (concurrency safety)
    $stmt = $fastPDO->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $regData['email']]);
    if ($stmt->fetch()) {
        $fastPDO->rollBack();
        echo json_encode(['success' => false, 'message' => 'This email address is already registered.']);
        exit;
    }

    // Check once more if employee number exists
    $stmt = $fastPDO->prepare("SELECT id FROM users WHERE employee_no = :employee_no LIMIT 1");
    $stmt->execute(['employee_no' => $regData['employee_no']]);
    if ($stmt->fetch()) {
        $fastPDO->rollBack();
        echo json_encode(['success' => false, 'message' => 'This employee number is already registered.']);
        exit;
    }

    // Generate User UUID
    $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

    // Generate unique username from email prefix
    $baseUsername = explode('@', $regData['email'])[0];
    $username = $baseUsername;
    $counter = 1;
    while (true) {
        $stmt = $fastPDO->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        if (!$stmt->fetch()) {
            break;
        }
        $username = $baseUsername . $counter;
        $counter++;
    }

    // Insert user
    $insertStmt = $fastPDO->prepare("
        INSERT INTO users (uuid, full_name, email, username, password, office, unit_section, employee_no, position, status) 
        VALUES (:uuid, :full_name, :email, :username, :password, :office, :unit_section, :employee_no, :position, 'active')
    ");
    $insertStmt->execute([
        'uuid' => $uuid,
        'full_name' => $regData['full_name'],
        'email' => $regData['email'],
        'username' => $username,
        'password' => $regData['password_hash'],
        'office' => $regData['office'],
        'unit_section' => $regData['unit_section'],
        'employee_no' => $regData['employee_no'],
        'position' => $regData['position']
    ]);

    $userId = $fastPDO->lastInsertId();

    // Assign "Requestor" role (role_id 5)
    // Verify first if role exists, or just use 5
    $roleStmt = $fastPDO->prepare("SELECT id FROM roles WHERE role_name = 'Requestor' LIMIT 1");
    $roleStmt->execute();
    $role = $roleStmt->fetch();
    $roleId = $role ? $role['id'] : 5;

    $roleInsertStmt = $fastPDO->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
    $roleInsertStmt->execute([
        'user_id' => $userId,
        'role_id' => $roleId
    ]);

    // Log Activity
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $actStmt = $fastPDO->prepare("
        INSERT INTO activity_logs (user_id, activity, ip_address) 
        VALUES (:user_id, 'User account registered & verified successfully', :ip_address)
    ");
    $actStmt->execute([
        'user_id' => $userId,
        'ip_address' => $ip
    ]);

    $fastPDO->commit();

    // Clear session registration variables
    unset($_SESSION['register_otp'], $_SESSION['register_otp_time'], $_SESSION['register_data']);

    // Set flash message
    $_SESSION['flash_success'] = "Verification successful! Your username is '{$username}'. You can now sign in.";

    echo json_encode(['success' => true, 'message' => 'Verification successful! Redirecting to login...']);
} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("OTP Verification DB error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Verification error occurred. Please try again.']);
}
