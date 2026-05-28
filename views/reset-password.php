<?php
/**
 * Password Reset Submission Page for SDO FAST.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/env.php';

$token = trim($_GET['token'] ?? '');
$isValid = false;
$errorMessage = 'Invalid or missing password reset token.';
$tokenRow = null;

if (!empty($token) && $fastPDO !== null) {
    try {
        // Query token availability, check if expired or already used
        $stmt = $fastPDO->prepare("
            SELECT t.*, u.full_name, u.email 
            FROM password_reset_tokens t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.token = :token AND t.used_at IS NULL AND t.expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        $tokenRow = $stmt->fetch();

        if ($tokenRow) {
            $isValid = true;
        } else {
            $errorMessage = 'This password reset link has expired, has already been used, or is invalid. Please request a new link.';
        }
    } catch (PDOException $e) {
        error_log("Token validation database error: " . $e->getMessage());
        $errorMessage = 'A database error occurred. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SDO FAST</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-card {
            max-width: 440px;
            width: 100%;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 45, 92, 0.08);
            background: #ffffff;
            overflow: hidden;
        }
        .reset-header {
            background-color: var(--color-primary);
            padding: 2.5rem 2rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
        .reset-body {
            padding: 2.5rem 2rem;
        }
    </style>
</head>
<body>

<div class="reset-card shadow-lg">
    <!-- Header -->
    <div class="reset-header">
        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
            <i class="bi bi-shield-lock-fill fs-2 text-accent"></i>
            <span class="logo-title text-white">SDO FAST</span>
        </div>
        <span class="logo-subtitle text-accent">Secure Password Reset</span>
    </div>
    
    <!-- Body -->
    <div class="reset-body">
        <?php if (!$isValid): ?>
            <div class="text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold text-danger">Reset Link Expired</h5>
                <p class="text-muted fs-8 mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
                <a href="<?php echo env('APP_URL'); ?>/login.php" class="btn btn-primary w-100 justify-content-center">Go to Login</a>
            </div>
        <?php else: ?>
            <p class="text-muted fs-8 mb-4">Hello <strong><?php echo htmlspecialchars($tokenRow['full_name']); ?></strong>, please specify a secure new password for your account (<code><?php echo htmlspecialchars($tokenRow['email']); ?></code>).</p>
            
            <form id="resetSubmitForm" action="<?php echo env('APP_URL'); ?>/api/auth/reset-password.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="resetPassword" placeholder="New Password" required minlength="8">
                    <label for="resetPassword">New Password</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required minlength="8">
                    <label for="confirmPassword">Confirm Password</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 justify-content-center align-items-center gap-2">
                    Update Password
                    <i class="bi bi-check-circle"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS (Includes Popper) CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
