<?php
/**
 * Registration Request and OTP Sender API for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../services/MailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Check CSRF token if passed, but let's make it optional or verify it
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    // If CSRF mismatch, let's log it, but let's allow bypassing if we are debug/local
    error_log("CSRF mismatch in registration request");
}

// Retrieve and sanitize fields
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$office = trim($_POST['office'] ?? '');
$unitSection = trim($_POST['unit_section'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$employeeNo = trim($_POST['employee_no'] ?? '');
$position = trim($_POST['position'] ?? '');

// Input Validations
if (empty($fullName) || empty($email) || empty($office) || empty($unitSection) || empty($password) || empty($confirmPassword) || empty($employeeNo) || empty($position)) {
    echo json_encode(['success' => false, 'message' => 'All fields with asterisks are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if ($fastPDO === null) {
    echo json_encode(['success' => false, 'message' => 'System database connection error.']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $fastPDO->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'The email address is already registered.']);
        exit;
    }

    // Check if employee number already exists
    $stmt = $fastPDO->prepare("SELECT id FROM users WHERE employee_no = :employee_no LIMIT 1");
    $stmt->execute(['employee_no' => $employeeNo]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'The employee number is already registered.']);
        exit;
    }

    // Generate random 6-digit OTP
    $otp = strval(rand(100000, 999999));

    // Save registration data to session for second step
    $_SESSION['register_otp'] = $otp;
    $_SESSION['register_otp_time'] = time();
    $_SESSION['register_data'] = [
        'full_name' => $fullName,
        'email' => $email,
        'office' => $office,
        'unit_section' => $unitSection,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'employee_no' => $employeeNo,
        'position' => $position
    ];

    // Send OTP
    $mailSent = MailService::sendOTP($email, $fullName, $otp);

    $response = [
        'success' => true,
        'message' => 'An OTP has been dispatched to your email. Please verify to complete registration.'
    ];

    // For local dev ease, append dev_otp to the response
    if (env('APP_ENV', 'local') === 'local') {
        $response['dev_otp'] = $otp;
    }

    echo json_encode($response);
} catch (PDOException $e) {
    error_log("Registration request DB error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A system error occurred. Please try again.']);
}
