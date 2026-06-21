<?php
/**
 * Forgot Password API Endpoint for SDO FAST
 * Aligned with the SDO ALPAS multi-step OTP standard.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../services/MailService.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Parse JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$action = $input['action'];

if ($fastPDO === null) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Initialize session trackers for OTP attempts
if (!isset($_SESSION['otp_attempts'])) {
    $_SESSION['otp_attempts'] = 0;
}
if (!isset($_SESSION['otp_request_count'])) {
    $_SESSION['otp_request_count'] = 0;
}
if (!isset($_SESSION['otp_request_time'])) {
    $_SESSION['otp_request_time'] = time();
}

// Reset hourly limits if 1 hour passed
if (time() - $_SESSION['otp_request_time'] > 3600) {
    $_SESSION['otp_request_count'] = 0;
    $_SESSION['otp_request_time'] = time();
}

switch ($action) {
    case 'track_access':
        // Simple access tracker matching ALPAS
        echo json_encode(['success' => true]);
        break;

    case 'request':
    case 'resend':
        handleRequestOTP($input, $fastPDO, $action);
        break;

    case 'verify':
        handleVerifyOTP($input, $fastPDO);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

function handleRequestOTP($input, $db, $action) {
    $email = trim($input['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        return;
    }

    // Rate limiting: Max 3 requests per hour
    if ($_SESSION['otp_request_count'] >= 3) {
        $timeLeft = 3600 - (time() - $_SESSION['otp_request_time']);
        $minsLeft = ceil($timeLeft / 60);
        echo json_encode([
            'success' => false,
            'resend_blocked' => true,
            'message' => "You have reached the maximum OTP requests (3 per hour). Try again after {$minsLeft} minutes."
        ]);
        return;
    }

    // Check if user exists and is active
    $stmt = $db->prepare("SELECT id, full_name, status FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'The email address you provided is not registered.']);
        return;
    }

    if ($user['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'This account is not active. Please contact support.']);
        return;
    }

    // Generate 6-digit OTP
    $otp = '';
    for ($i = 0; $i < 6; $i++) {
        $otp .= random_int(0, 9);
    }
    $otpHash = password_hash($otp, PASSWORD_DEFAULT);

    // Invalidate previous reset tokens/OTPs
    $stmt = $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL");
    $stmt->execute(['user_id' => $user['id']]);

    // Store new OTP hash in password_reset_tokens table
    $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
    $stmt->execute([
        'user_id' => $user['id'],
        'token' => $otpHash
    ]);

    // Send OTP via MailService
    $emailSent = MailService::sendPasswordResetOTP($email, $user['full_name'], $otp);
    $isLocal = env('APP_ENV', 'local') === 'local';

    if ($emailSent || $isLocal) {
        $_SESSION['otp_request_count']++;
        $_SESSION['otp_attempts'] = 0; // Reset verification failure count

        $response = [
            'success' => true,
            'message' => $emailSent
                ? 'OTP has been sent to your email address. Please check your inbox.'
                : 'Email delivery failed. Use the verification code shown below (local dev mode).',
            'attempts_remaining' => 3,
            'resend_remaining' => max(0, 3 - $_SESSION['otp_request_count']),
        ];

        if ($isLocal) {
            $response['dev_otp'] = $otp;
        }

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again later or contact support.']);
    }
}

function handleVerifyOTP($input, $db) {
    $email = trim($input['email'] ?? '');
    $otp = trim($input['otp'] ?? '');

    if (empty($email) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Email and OTP are required.']);
        return;
    }

    // Check verification attempts
    if ($_SESSION['otp_attempts'] >= 5) {
        echo json_encode([
            'success' => false,
            'error' => 'otp_attempts_exhausted',
            'message' => 'You have exceeded the maximum OTP input attempts. Please request a new OTP.',
            'redirect' => true
        ]);
        return;
    }

    // Find the user
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email.']);
        return;
    }

    // Retrieve active tokens (OTP hashes)
    $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE user_id = :user_id AND used_at IS NULL AND expires_at > NOW()");
    $stmt->execute(['user_id' => $user['id']]);
    $tokens = $stmt->fetchAll();

    $verified = false;
    $matchedTokenId = null;

    foreach ($tokens as $tokenRow) {
        if (password_verify($otp, $tokenRow['token'])) {
            $verified = true;
            $matchedTokenId = $tokenRow['id'];
            break;
        }
    }

    if ($verified) {
        // Mark OTP as used
        $stmt = $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $matchedTokenId]);

        // Generate actual reset token for the password reset page
        $resetToken = bin2hex(random_bytes(32));

        // Store secure reset token in password_reset_tokens table
        $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
        $stmt->execute([
            'user_id' => $user['id'],
            'token' => $resetToken
        ]);

        $_SESSION['otp_attempts'] = 0; // Reset attempts

        echo json_encode([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken
        ]);
    } else {
        $_SESSION['otp_attempts']++;
        $remaining = max(0, 5 - $_SESSION['otp_attempts']);

        if ($remaining <= 0) {
            // Invalidate current OTP
            $stmt = $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL");
            $stmt->execute(['user_id' => $user['id']]);

            echo json_encode([
                'success' => false,
                'error' => 'otp_attempts_exhausted',
                'message' => 'You have exceeded the maximum OTP input attempts (5). Current OTP invalidated. Please request a new OTP.',
                'redirect' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Invalid OTP code. Please check and try again. ({$remaining} attempts remaining)",
                'otp_attempts_remaining' => $remaining
            ]);
        }
    }
}

