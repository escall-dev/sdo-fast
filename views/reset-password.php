<?php
/**
 * Password Reset Submission Page for SDO FAST.
 * Aligned with SDO ALPAS format.
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
    <title>Reset Password - SDO FAST</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #214da2;
            --primary-light: #2563eb;
            --primary-dark: #1e3a8a;
            --accent: #3b82f6;
            --bg-dark: #081121;
            --bg-card: #0c1524;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(33, 77, 162, 0.15);
            --error: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container { width: 100%; max-width: 440px; }

        .card {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 32px 28px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35);
        }

        .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            background: transparent;
            border-radius: 50%;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.1);
        }

        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .card-header h1 {
            color: var(--text);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-header p {
            color: var(--text-muted);
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: var(--primary-light);
            width: 24px;
            border-radius: 4px;
        }

        .step-dot.completed { background: var(--success); }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 12px 42px 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            background: #070c17;
            border: 1px solid #121b2d;
            border-radius: 10px;
            color: var(--text);
            transition: all 0.2s ease;
        }

        .form-control::placeholder { color: #64748b; }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(33, 77, 162, 0.25);
            background: #0c1524;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.9rem;
        }

        .toggle-password:hover { color: var(--text); }

        .password-requirements {
            margin-top: 8px;
            padding: 10px 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .password-requirements p {
            color: var(--text-muted);
            font-size: 0.75rem;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .req-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 3px;
        }

        .req-item i { font-size: 0.7rem; }
        .req-item.valid { color: var(--success); }
        .req-item.invalid { color: var(--text-muted); }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 77, 162, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-success {
            background: var(--success);
            color: white;
            margin-top: 16px;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert i { margin-top: 2px; flex-shrink: 0; }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .hidden { display: none; }

        .brand-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <?php if (!$isValid): ?>
                <div class="card-header">
                    <div class="logo-badge">
                        <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo">
                    </div>
                    <h1>Link Expired</h1>
                    <p style="color: var(--error); margin-top: 6px;"><?php echo htmlspecialchars($errorMessage); ?></p>
                </div>
                <a href="<?php echo env('APP_URL'); ?>/login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Go to Login
                </a>
            <?php else: ?>
                <!-- Reset Form -->
                <div id="reset-form-section">
                    <div class="card-header">
                        <div class="logo-badge">
                            <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo">
                        </div>
                        <h1>SDO FAST</h1>
                        <p style="color: var(--accent); font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Set New Password</p>
                        <p>Create a strong new password for your account<br>
                        <strong style="color: var(--text);"><?php echo htmlspecialchars($tokenRow['email']); ?></strong></p>
                    </div>

                    <div class="step-indicator">
                        <div class="step-dot completed"></div>
                        <div class="step-dot completed"></div>
                        <div class="step-dot active"></div>
                    </div>

                    <div id="reset-alert" class="hidden"></div>

                    <form id="reset-form" onsubmit="return handleResetPassword(event)">
                        <div class="form-group">
                            <label class="form-label" for="new-password">New Password</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control" id="new-password" name="new_password" 
                                       placeholder="Enter new password" required oninput="checkPasswordStrength()">
                                <button type="button" class="toggle-password" onclick="togglePassword('new-password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <p>Password requirements:</p>
                                <div class="req-item" id="req-length">
                                    <i class="fas fa-circle"></i> At least 8 characters
                                </div>
                                <div class="req-item" id="req-upper">
                                    <i class="fas fa-circle"></i> One uppercase letter
                                </div>
                                <div class="req-item" id="req-lower">
                                    <i class="fas fa-circle"></i> One lowercase letter
                                </div>
                                <div class="req-item" id="req-number">
                                    <i class="fas fa-circle"></i> One number
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm-password">Confirm Password</label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control" id="confirm-password" name="confirm_password" 
                                       placeholder="Confirm new password" required oninput="checkPasswordMatch()">
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm-password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="match-status" class="req-item" style="margin-top: 6px; display: none;">
                                <i class="fas fa-circle"></i> <span></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="btn-reset">
                            <span class="spinner" id="reset-spinner"></span>
                            <i class="fas fa-save" id="reset-icon"></i>
                            <span id="reset-text">Reset Password</span>
                        </button>
                    </form>
                </div>

                <!-- Success State -->
                <div id="success-section" class="hidden">
                    <div class="card-header">
                        <div class="logo-badge">
                            <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo">
                        </div>
                        <h1>SDO FAST</h1>
                        <p style="color: var(--success); font-weight: 600;">Password Reset Successful</p>
                        <p style="margin-top: 6px;">Your password has been changed successfully. You can now log in with your new password.</p>
                    </div>

                    <div class="step-indicator">
                        <div class="step-dot completed"></div>
                        <div class="step-dot completed"></div>
                        <div class="step-dot completed"></div>
                    </div>

                    <a href="<?php echo env('APP_URL'); ?>/login.php" class="btn btn-success">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="brand-footer">
            <p>&copy; <?php echo date('Y'); ?> ICT Unit<br>
            DepEd - Schools Division Office of San Pedro City</p>
        </div>
    </div>

    <?php if ($isValid): ?>
    <script>
        const APP_URL = '<?php echo env('APP_URL'); ?>';
        const RESET_TOKEN = '<?php echo htmlspecialchars($token); ?>';

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function checkPasswordStrength() {
            const pw = document.getElementById('new-password').value;

            updateReq('req-length', pw.length >= 8);
            updateReq('req-upper', /[A-Z]/.test(pw));
            updateReq('req-lower', /[a-z]/.test(pw));
            updateReq('req-number', /[0-9]/.test(pw));

            checkPasswordMatch();
        }

        function updateReq(id, valid) {
            const el = document.getElementById(id);
            el.className = 'req-item ' + (valid ? 'valid' : 'invalid');
            el.querySelector('i').className = valid ? 'fas fa-check-circle' : 'fas fa-circle';
        }

        function checkPasswordMatch() {
            const pw = document.getElementById('new-password').value;
            const confirm = document.getElementById('confirm-password').value;
            const status = document.getElementById('match-status');

            if (!confirm) {
                status.style.display = 'none';
                return;
            }

            status.style.display = 'flex';
            if (pw === confirm) {
                status.className = 'req-item valid';
                status.querySelector('i').className = 'fas fa-check-circle';
                status.querySelector('span').textContent = 'Passwords match';
            } else {
                status.className = 'req-item invalid';
                status.querySelector('i').className = 'fas fa-times-circle';
                status.querySelector('span').textContent = 'Passwords do not match';
            }
        }

        function showAlert(type, message) {
            const alertEl = document.getElementById('reset-alert');
            const icons = { error: 'exclamation-triangle', success: 'check-circle' };
            alertEl.className = 'alert alert-' + type;
            alertEl.innerHTML = '<i class="fas fa-' + icons[type] + '"></i><span>' + message + '</span>';
            alertEl.classList.remove('hidden');
        }

        async function handleResetPassword(e) {
            e.preventDefault();

            const pw = document.getElementById('new-password').value;
            const confirm = document.getElementById('confirm-password').value;

            // Validate
            if (pw.length < 8) {
                showAlert('error', 'Password must be at least 8 characters long.');
                return false;
            }
            if (!/[A-Z]/.test(pw) || !/[a-z]/.test(pw) || !/[0-9]/.test(pw)) {
                showAlert('error', 'Password must contain at least one uppercase letter, one lowercase letter, and one number.');
                return false;
            }
            if (pw !== confirm) {
                showAlert('error', 'Passwords do not match.');
                return false;
            }

            // Submit
            const btn = document.getElementById('btn-reset');
            const spinner = document.getElementById('reset-spinner');
            const icon = document.getElementById('reset-icon');
            const text = document.getElementById('reset-text');

            btn.disabled = true;
            spinner.style.display = 'block';
            icon.style.display = 'none';
            text.textContent = 'Resetting...';

            try {
                const resp = await fetch(APP_URL + '/api/auth/reset-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        token: RESET_TOKEN,
                        password: pw,
                        confirm_password: confirm
                    })
                });

                const data = await resp.json();

                if (data.success) {
                    document.getElementById('reset-form-section').classList.add('hidden');
                    document.getElementById('success-section').classList.remove('hidden');
                } else {
                    showAlert('error', data.message || 'Failed to reset password. Please try again.');
                    btn.disabled = false;
                    spinner.style.display = 'none';
                    icon.style.display = 'inline';
                    text.textContent = 'Reset Password';
                }
            } catch (err) {
                showAlert('error', 'Network error. Please try again.');
                btn.disabled = false;
                spinner.style.display = 'none';
                icon.style.display = 'inline';
                text.textContent = 'Reset Password';
            }

            return false;
        }
    </script>
    <?php endif; ?>
</body>
</html>
