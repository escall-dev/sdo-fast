<?php
/**
 * Forgot Password Page for SDO FAST
 * Aligned with the SDO ALPAS layout and format standard.
 * 
 * Step 1: User enters email to request OTP
 * Step 2: OTP verification
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/env.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SDO FAST</title>
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
            margin-bottom: 4px;
        }

        .card-header p {
            color: var(--text-muted);
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
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

        .otp-input-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 16px 0;
        }

        .otp-input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            font-family: inherit;
            background: #070c17;
            border: 1px solid #121b2d;
            border-radius: 10px;
            color: var(--text);
            transition: all 0.2s ease;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #0c1524;
            box-shadow: 0 0 0 3px rgba(33, 77, 162, 0.25);
        }

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
            box-shadow: none;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border: 1px solid #121b2d;
            margin-top: 12px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--text-muted);
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

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }

        .timer {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.82rem;
            margin-top: 8px;
        }

        .timer span { color: var(--warning); font-weight: 600; }

        .resend-link {
            text-align: center;
            margin-top: 12px;
        }

        .resend-link a {
            color: var(--text);
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
        }

        .resend-link a:hover { text-decoration: underline; }

        .resend-link a.disabled {
            color: var(--text-muted);
            pointer-events: none;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-link a:hover { color: var(--accent); }

        .brand-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 0.75rem;
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

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .hidden { display: none; }

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

        @media (max-width: 480px) {
            .card { padding: 24px 20px; }
            .otp-input { width: 42px; height: 50px; font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Step 1: Request OTP -->
            <div id="step-request">
                <div class="card-header">
                    <div class="logo-badge">
                        <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo">
                    </div>
                    <h1>SDO FAST</h1>
                    <p style="color: var(--accent); font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Financial Accounting Services and Transactions</p>
                    <p>Enter your registered email address and we'll send you a 6-digit verification code (OTP) to reset your password.</p>
                </div>

                <div class="step-indicator">
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                </div>

                <div id="request-alert" class="hidden"></div>

                <form id="request-form" onsubmit="return handleRequestOTP(event)">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>"
                               placeholder="your.email@deped.gov.ph" required>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-request">
                        <span class="spinner" id="request-spinner"></span>
                        <i class="fas fa-paper-plane" id="request-icon"></i>
                        <span id="request-text">Send OTP</span>
                    </button>
                </form>

                <div class="back-link">
                    <a href="<?php echo env('APP_URL'); ?>/login.php">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>

            <!-- Step 2: Verify OTP -->
            <div id="step-verify" class="hidden">
                <div class="card-header">
                    <div class="logo-badge">
                        <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo">
                    </div>
                    <h1>SDO FAST</h1>
                    <p style="color: var(--accent); font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Verify OTP</p>
                    <p>Enter the 6-digit verification code sent to your email address.</p>
                </div>

                <div class="step-indicator">
                    <div class="step-dot completed"></div>
                    <div class="step-dot active"></div>
                </div>

                <div id="verify-alert" class="hidden"></div>

                <form id="verify-form" onsubmit="return handleVerifyOTP(event)">
                    <input type="hidden" id="verify-email" value="<?php echo htmlspecialchars($email); ?>">

                    <div class="otp-input-group">
                        <input type="text" class="otp-input" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                        <input type="text" class="otp-input" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    </div>

                    <div class="timer" id="otp-timer">
                        OTP expires in <span id="timer-countdown">5:00</span>
                    </div>

                    <button type="submit" class="btn btn-primary" id="btn-verify" style="margin-top: 16px;">
                        <span class="spinner" id="verify-spinner"></span>
                        <i class="fas fa-check-circle" id="verify-icon"></i>
                        <span id="verify-text">Verify OTP</span>
                    </button>
                </form>

                <div class="resend-link">
                    <a href="#" id="resend-link" class="disabled" onclick="return handleResendOTP(event)">
                        Resend OTP <span id="resend-timer">(wait 60s)</span>
                    </a>
                </div>

                <div class="back-link">
                    <a href="#" onclick="showStep('request'); return false;">
                        <i class="fas fa-arrow-left"></i> Change email
                    </a>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            <p>&copy; <?php echo date('Y'); ?> ICT Unit<br>
            DepEd - Schools Division Office of San Pedro City</p>
        </div>
    </div>

    <script>
        const APP_URL = '<?php echo env('APP_URL'); ?>';
        let otpTimerInterval = null;
        let resendTimerInterval = null;
        let currentEmail = '<?php echo htmlspecialchars($email, ENT_QUOTES); ?>';

        // OTP input navigation
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val;
                if (val && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (let i = 0; i < Math.min(paste.length, inputs.length); i++) {
                    inputs[i].value = paste[i];
                }
                const nextIndex = Math.min(paste.length, inputs.length - 1);
                inputs[nextIndex].focus();
            });
        });

        function getOTPValue() {
            return Array.from(document.querySelectorAll('.otp-input')).map(i => i.value).join('');
        }

        function clearOTPInputs() {
            document.querySelectorAll('.otp-input').forEach(i => { i.value = ''; });
            document.querySelector('.otp-input').focus();
        }

        function showStep(step) {
            document.getElementById('step-request').classList.add('hidden');
            document.getElementById('step-verify').classList.add('hidden');
            document.getElementById('step-' + step).classList.remove('hidden');
        }

        function showAlert(containerId, type, message) {
            const alertEl = document.getElementById(containerId);
            const icons = { error: 'exclamation-triangle', success: 'check-circle', warning: 'exclamation-circle' };
            alertEl.className = 'alert alert-' + type;
            alertEl.innerHTML = '<i class="fas fa-' + icons[type] + '"></i><span>' + message + '</span>';
            alertEl.classList.remove('hidden');
        }

        function hideAlert(containerId) {
            document.getElementById(containerId).classList.add('hidden');
        }

        function setButtonLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            const spinner = document.getElementById(btnId.replace('btn-', '') + '-spinner');
            const icon = document.getElementById(btnId.replace('btn-', '') + '-icon');
            const text = document.getElementById(btnId.replace('btn-', '') + '-text');

            if (loading) {
                btn.disabled = true;
                spinner.style.display = 'block';
                if (icon) icon.style.display = 'none';
                text.textContent = 'Please wait...';
            } else {
                btn.disabled = false;
                spinner.style.display = 'none';
                if (icon) icon.style.display = 'inline';
            }
        }

        function startOTPTimer(seconds) {
            if (otpTimerInterval) clearInterval(otpTimerInterval);
            let remaining = seconds;
            const timerEl = document.getElementById('timer-countdown');

            otpTimerInterval = setInterval(() => {
                remaining--;
                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;
                timerEl.textContent = mins + ':' + secs.toString().padStart(2, '0');

                if (remaining <= 0) {
                    clearInterval(otpTimerInterval);
                    timerEl.textContent = 'Expired';
                    timerEl.style.color = '#ef4444';
                    showAlert('verify-alert', 'error', 'Your OTP has expired. Please request a new one.');
                }
            }, 1000);
        }

        function startResendTimer() {
            if (resendTimerInterval) clearInterval(resendTimerInterval);
            let remaining = 60;
            const link = document.getElementById('resend-link');
            const timerSpan = document.getElementById('resend-timer');

            link.classList.add('disabled');
            timerSpan.textContent = '(wait ' + remaining + 's)';

            resendTimerInterval = setInterval(() => {
                remaining--;
                timerSpan.textContent = '(wait ' + remaining + 's)';

                if (remaining <= 0) {
                    clearInterval(resendTimerInterval);
                    link.classList.remove('disabled');
                    timerSpan.textContent = '';
                }
            }, 1000);
        }

        async function handleRequestOTP(e) {
            e.preventDefault();
            hideAlert('request-alert');

            const email = document.getElementById('email').value.trim();
            if (!email) {
                showAlert('request-alert', 'error', 'Please enter your email address.');
                return false;
            }

            currentEmail = email;
            setButtonLoading('btn-request', true);

            try {
                const resp = await fetch(APP_URL + '/api/auth/forgot-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'request', email: email })
                });

                const data = await resp.json();

                if (data.success) {
                    document.getElementById('verify-email').value = email;
                    showStep('verify');
                    showAlert('verify-alert', 'success', data.message);
                    startOTPTimer(300); // 5 minutes
                    startResendTimer();
                    setTimeout(() => document.querySelector('.otp-input').focus(), 100);
                } else {
                    showAlert('request-alert', 'error', data.message || 'Failed to send OTP.');
                }
            } catch (err) {
                showAlert('request-alert', 'error', 'Network error. Please try again.');
            } finally {
                setButtonLoading('btn-request', false);
                document.getElementById('request-text').textContent = 'Send OTP';
            }

            return false;
        }

        async function handleVerifyOTP(e) {
            e.preventDefault();
            hideAlert('verify-alert');

            const otp = getOTPValue();
            const email = document.getElementById('verify-email').value;

            if (otp.length !== 6) {
                showAlert('verify-alert', 'error', 'Please enter the complete 6-digit OTP.');
                return false;
            }

            setButtonLoading('btn-verify', true);

            try {
                const resp = await fetch(APP_URL + '/api/auth/forgot-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'verify', email: email, otp: otp })
                });

                const data = await resp.json();

                if (data.success) {
                    if (otpTimerInterval) clearInterval(otpTimerInterval);
                    window.location.href = APP_URL + '/views/reset-password.php?token=' + encodeURIComponent(data.reset_token);
                } else {
                    if (data.error === 'otp_attempts_exhausted') {
                        showAlert('verify-alert', 'error', data.message);
                        clearOTPInputs();
                        setTimeout(() => {
                            showStep('request');
                            showAlert('request-alert', 'warning', 'OTP attempts exhausted. Please request a new OTP.');
                        }, 3000);
                    } else {
                        showAlert('verify-alert', 'error', data.message || 'Invalid OTP.');
                    }
                }
            } catch (err) {
                showAlert('verify-alert', 'error', 'Network error. Please try again.');
            } finally {
                setButtonLoading('btn-verify', false);
                document.getElementById('verify-text').textContent = 'Verify OTP';
            }

            return false;
        }

        async function handleResendOTP(e) {
            e.preventDefault();
            const link = document.getElementById('resend-link');
            if (link.classList.contains('disabled')) return false;

            hideAlert('verify-alert');
            const email = document.getElementById('verify-email').value;

            try {
                const resp = await fetch(APP_URL + '/api/auth/forgot-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'resend', email: email })
                });

                const data = await resp.json();

                if (data.success) {
                    showAlert('verify-alert', 'success', 'New OTP sent! Check your email.');
                    clearOTPInputs();
                    startOTPTimer(300);
                    startResendTimer();
                } else {
                    showAlert('verify-alert', 'error', data.message || 'Failed to resend OTP.');
                }
            } catch (err) {
                showAlert('verify-alert', 'error', 'Network error. Please try again.');
            }

            return false;
        }
    </script>
</body>
</html>
