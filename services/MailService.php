<?php
/**
 * Mail Service using PHPMailer.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

class MailService {
    public static function isMailEnabled() {
        $mailEnabled = env('MAIL_ENABLED', true);
        if ($mailEnabled === 'false' || $mailEnabled === false || $mailEnabled === '0') {
            return false;
        }
        return true;
    }

    private static function logDir() {
        $logDir = __DIR__ . '/../scratch';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        return $logDir;
    }

    private static function logOTP($toEmail, $fullName, $otp, $context = 'Registration') {
        $prefix = self::isMailEnabled() ? '' : '(MOCK) ';
        file_put_contents(
            self::logDir() . '/otp_log.txt',
            '[' . date('Y-m-d H:i:s') . "] {$prefix}[{$context}] OTP for {$toEmail} ({$fullName}): {$otp}\n",
            FILE_APPEND
        );
    }

    private static function configureMailer(PHPMailer $mail) {
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST', env('MAIL_HOST', 'localhost'));
        $mail->Port = (int) env('SMTP_PORT', env('MAIL_PORT', 1025));
        $mail->SMTPAuth = filter_var(env('SMTP_AUTH', !empty(env('MAIL_USERNAME'))), FILTER_VALIDATE_BOOLEAN);
        $mail->Username = env('SMTP_USERNAME', env('MAIL_USERNAME', ''));
        // Gmail app passwords are often pasted with spaces — strip them
        $mail->Password = str_replace(' ', '', env('SMTP_PASSWORD', env('MAIL_PASSWORD', '')));

        $encryption = strtolower((string) env('SMTP_ENCRYPTION', ''));
        if ($encryption === 'tls' || $mail->Port === 587) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl' || $mail->Port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom(
            env('MAIL_FROM_ADDRESS', 'ict.sanpedrocity@deped.gov.ph'),
            env('MAIL_FROM_NAME', 'SDO FAST')
        );
    }

    public static function sendOTP($toEmail, $fullName, $otp) {
        self::logOTP($toEmail, $fullName, $otp, 'Registration');

        if (!self::isMailEnabled()) {
            error_log("SDO FAST - Registration OTP for {$toEmail}: {$otp}");
            return true;
        }

        $mail = new PHPMailer(true);
        try {
            self::configureMailer($mail);
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'SDO FAST Email Verification OTP';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #1e293b; background-color: #f8fafc;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;'>
                        <div style='background-color: #1b4a9a; color: #ffffff; padding: 20px; text-align: center;'>
                            <h2 style='margin: 0;'>SDO FAST</h2>
                            <p style='margin: 5px 0 0 0; font-size: 14px;'>Financial Accounting Services & Transactions</p>
                        </div>
                        <div style='padding: 30px;'>
                            <p>Hello <strong>" . htmlspecialchars($fullName) . "</strong>,</p>
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
            error_log("PHPMailer registration OTP failed for {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendPasswordResetOTP($toEmail, $fullName, $otp) {
        self::logOTP($toEmail, $fullName, $otp, 'Password Reset');

        if (!self::isMailEnabled()) {
            error_log("SDO FAST - Password reset OTP for {$toEmail}: {$otp}");
            return true;
        }

        $mail = new PHPMailer(true);
        try {
            self::configureMailer($mail);
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'SDO FAST - Password Reset OTP';

            $sdoLogoPath = __DIR__ . '/../assets/img/sdo_logo.jpg';
            if (file_exists($sdoLogoPath)) {
                $mail->addEmbeddedImage($sdoLogoPath, 'sdo-logo', 'sdo_logo.jpg');
            }

            $mail->Body = "
                <div style=\"font-family: sans-serif; max-width: 500px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;\">
                    <div style=\"background: #214da2; padding: 24px; text-align: center; color: white;\">
                        <h2 style=\"margin: 0; font-size: 22px; font-weight: 700;\">SDO FAST</h2>
                        <p style=\"margin: 4px 0 0; font-size: 12px; color: rgba(255,255,255,0.8);\">Financial Accounting Services and Transactions</p>
                    </div>
                    <div style=\"padding: 30px;\">
                        <p style=\"margin-top: 0; color: #334155; font-size: 15px;\">Hello <strong>" . htmlspecialchars($fullName) . "</strong>,</p>
                        <p style=\"color: #475569; font-size: 14px; line-height: 1.6;\">We received a request to reset your password. Use the verification code (OTP) below to verify your request:</p>
                        <div style=\"text-align: center; margin: 30px 0;\">
                            <div style=\"display: inline-block; background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 12px 30px;\">
                                <span style=\"font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #1e293b;\">{$otp}</span>
                            </div>
                        </div>
                        <p style=\"color: #ef4444; font-size: 13px; text-align: center; font-weight: 500;\">This code is valid for 5 minutes.</p>
                        <hr style=\"border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;\">
                        <p style=\"margin: 0; color: #94a3b8; font-size: 12px;\">If you did not request this, you can safely ignore this email.</p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Hello {$fullName}, your SDO FAST password reset OTP is: {$otp}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer password reset OTP failed for {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }
}
