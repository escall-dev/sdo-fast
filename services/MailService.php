<?php
/**
 * Mail Service using PHPMailer.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

class MailService {
    public static function sendOTP($toEmail, $fullName, $otp) {
        // Also log to scratch/otp_log.txt for local testing convenience
        $logDir = __DIR__ . '/../scratch';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        file_put_contents($logDir . '/otp_log.txt', "[" . date('Y-m-d H:i:s') . "] OTP for {$toEmail} ({$fullName}): {$otp}\n", FILE_APPEND);

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'localhost');
            $mail->Port       = env('MAIL_PORT', 1025);
            $mail->SMTPAuth   = !empty(env('MAIL_USERNAME'));
            $mail->Username   = env('MAIL_USERNAME', '');
            $mail->Password   = env('MAIL_PASSWORD', '');
            
            // Set TLS/SSL if port is 465 or 587
            if (env('MAIL_PORT') == 587) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif (env('MAIL_PORT') == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            // Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@sdo-fast.gov.ph'), 'SDO FAST');
            $mail->addAddress($toEmail, $fullName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SDO FAST Email Verification OTP';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #1e293b; background-color: #f8fafc;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;'>
                        <div style='background-color: #1b4a9a; color: #ffffff; padding: 20px; text-align: center;'>
                            <h2 style='margin: 0;'>SDO FAST</h2>
                            <p style='margin: 5px 0 0 0; font-size: 14px;'>Financial Accounting Services & Transactions</p>
                        </div>
                        <div style='padding: 30px;'>
                            <p>Hello <strong>{$fullName}</strong>,</p>
                            <p>Thank you for registering on SDO FAST. To complete your account creation, please use the following One-Time Password (OTP) verification code:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1b4a9a; background-color: #f1f5f9; padding: 10px 20px; border-radius: 6px; border: 1px dashed #cbd5e1;'>{$otp}</span>
                            </div>
                            <p>This code is valid for 15 minutes. If you did not request this, please ignore this email.</p>
                            <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
                            <p style='font-size: 12px; color: #64748b;'>This is an automated system email. Please do not reply directly.</p>
                        </div>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hello {$fullName}, your SDO FAST registration OTP code is: {$otp}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer failed to send to {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }
}
