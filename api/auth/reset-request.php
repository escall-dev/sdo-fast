<?php
/**
 * Password Reset Request API for SDO FAST.
 * Dispatches secure tokens via PHPMailer.
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Please enter a valid email address.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

// We always show a generic confirmation to prevent user enumeration
$successMsg = 'If the email address matches an active account, a password reset link has been sent to it. Please check your inbox.';

if ($fastPDO === null) {
    // Graceful fallback
    $_SESSION['flash_success'] = $successMsg;
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}

try {
    // 1. Verify user exists
    $stmt = $fastPDO->prepare("SELECT id, full_name FROM users WHERE email = :email AND status = 'active' LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $userId = $user['id'];
        
        // 2. Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        
        // 3. Invalidate previous tokens
        $invalidateStmt = $fastPDO->prepare("
            UPDATE password_reset_tokens 
            SET used_at = NOW() 
            WHERE user_id = :user_id AND used_at IS NULL
        ");
        $invalidateStmt->execute(['user_id' => $userId]);

        // 4. Save new token
        $insertStmt = $fastPDO->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires_at) 
            VALUES (:user_id, :token, :expires_at)
        ");
        $insertStmt->execute([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);

        // 5. Send Email via PHPMailer
        $resetLink = env('APP_URL') . "/views/reset-password.php?token=" . $token;
        
        $mail = new PHPMailer(true);
        try {
            // SMTP settings from .env
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'localhost');
            $mail->SMTPAuth   = !empty(env('MAIL_USERNAME'));
            $mail->Username   = env('MAIL_USERNAME', '');
            $mail->Password   = env('MAIL_PASSWORD', '');
            $mail->Port       = env('MAIL_PORT', 1025);
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            if ($mail->Port == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }
            
            // Allow self-signed certs for local development/testing (Mailpit/Mailhog)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@sdo-fast.gov.ph'), 'SDO FAST');
            $mail->addAddress($email, $user['full_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - SDO FAST';
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                    <h2 style='color: #1b4a9a; margin-top: 0;'>SDO FAST Password Reset</h2>
                    <p>Hello " . htmlspecialchars($user['full_name']) . ",</p>
                    <p>We received a request to reset your password. Click the button below to set a new password. This link is valid for <strong>60 minutes</strong>.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . htmlspecialchars($resetLink) . "' style='background-color: #1b4a9a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Reset Password</a>
                    </div>
                    <p style='color: #64748b; font-size: 0.9em;'>If the button doesn't work, copy and paste this URL into your browser:</p>
                    <p style='word-break: break-all; font-size: 0.85em;'><a href='" . htmlspecialchars($resetLink) . "'>" . htmlspecialchars($resetLink) . "</a></p>
                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                    <p style='color: #94a3b8; font-size: 0.8em;'>If you did not request this reset, you can safely ignore this email. Your password will remain unchanged.</p>
                </div>
            ";

            $mail->send();
            
            // Log successful email dispatch in activity_logs
            $actStmt = $fastPDO->prepare("
                INSERT INTO activity_logs (user_id, activity, old_value, ip_address) 
                VALUES (:user_id, 'Password reset email sent', :email, :ip)
            ");
            $actStmt->execute([
                'user_id' => $userId,
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);
            
        } catch (Exception $e) {
            error_log("PHPMailer Reset Dispatch Failure: " . $mail->ErrorInfo);
            // Log SMTP error but do not expose it to the user
            $actStmt = $fastPDO->prepare("
                INSERT INTO activity_logs (user_id, activity, old_value, ip_address) 
                VALUES (:user_id, 'Password reset email failed (SMTP error)', :error, :ip)
            ");
            $actStmt->execute([
                'user_id' => $userId,
                'error' => json_encode(['email' => $email, 'error' => $mail->ErrorInfo]),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);
        }
    } else {
        // Log query matching no user for security auditing
        $actStmt = $fastPDO->prepare("
            INSERT INTO activity_logs (user_id, activity, old_value, ip_address) 
            VALUES (NULL, 'Unrecognized password reset request', :email, :ip)
        ");
        $actStmt->execute([
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    }
    
    $_SESSION['flash_success'] = $successMsg;
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;

} catch (PDOException $e) {
    error_log("Password reset database error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'An unexpected database error occurred. Please try again.';
    header('Location: ' . env('APP_URL') . '/login.php');
    exit;
}
