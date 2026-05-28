<?php
/**
 * Root Login View for SDO FAST.
 * Matches requested DepEd dark card theme layout over a #081121 background.
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/env.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;

// Clear flash messages
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);

// Generate CSRF token for the login form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | SDO FAST</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme Variables override for login -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/assets/css/style.css">
    
    <style>
        body {
            background-color: #081121; /* Dark navy/slate background matching screenshot */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35);
            background: #0c1524; /* Deep navy card background matching ALPAS screenshot */
            overflow: hidden;
            color: #f8fafc;
        }
        .login-body {
            padding: 3rem 2.25rem 2.5rem 2.25rem;
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        /* Custom Dark Inputs styling */
        .dark-input {
            background-color: #070c17 !important;
            border: 1px solid #121b2d !important;
            color: #f8fafc !important;
            height: 48px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .dark-input::placeholder {
            color: #64748b !important;
            opacity: 1;
        }
        .dark-input:focus {
            background-color: #0c1524 !important;
            border-color: #214da2 !important;
            box-shadow: 0 0 0 3px rgba(33, 77, 162, 0.25) !important;
            color: #ffffff !important;
        }
        .btn-signin {
            background-color: #214da2;
            border-color: #214da2;
            color: #ffffff;
            height: 48px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-signin:hover, .btn-signin:focus {
            background-color: #1a3d82;
            border-color: #1a3d82;
            transform: translateY(-1px);
        }
        .btn-register {
            background-color: #182335;
            border: 1px solid #25344c;
            color: #f8fafc;
            height: 48px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-register:hover {
            background-color: #202e45;
            color: #ffffff;
            border-color: #384c6c;
        }
        .text-accent-link {
            color: #94a3b8;
            transition: color 0.2s ease;
        }
        .text-accent-link:hover {
            color: #3b82f6;
        }
        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #475569;
            font-size: 0.75rem;
        }
        .divider-container::before, .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #1e293b;
        }
        .divider-container:not(:empty)::before {
            margin-right: .5em;
        }
        .divider-container:not(:empty)::after {
            margin-left: .5em;
        }
        .logo-img {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.1);
        }
        .page-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="login-card shadow-lg">
    <div class="login-body">
        
        <!-- SDO FAST Logo -->
        <div class="text-center mb-3">
            <img src="<?php echo env('APP_URL'); ?>/assets/img/sdo_logo.jpg" alt="SDO Logo" class="rounded-circle logo-img" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='inline-flex';">
            <div id="logo-fallback" class="bg-primary text-white rounded-circle d-none align-items-center justify-content-center border border-3 border-white-10 logo-img" style="margin: 0 auto;">
                <i class="bi bi-wallet2 fs-2 text-accent"></i>
            </div>
        </div>

        <!-- SDO FAST Title -->
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-white" style="letter-spacing: 0.5px; font-size: 1.5rem;">SDO FAST</h4>
            <div style="font-size: 0.72rem; color: #526685; letter-spacing: 0.3px; margin-top: 4px;">Financial Accounting Services and Transactions</div>
        </div>

        <?php if ($flashError): ?>
            <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-4" role="alert" style="background-color: #2a1b22; color: #f87171; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <small class="fw-semibold"><?php echo htmlspecialchars($flashError); ?></small>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4" role="alert" style="background-color: #112a20; color: #4ade80; border-radius: 8px;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <small class="fw-semibold"><?php echo htmlspecialchars($flashSuccess); ?></small>
            </div>
        <?php endif; ?>

        <!-- Credentials Form -->
        <form id="loginForm" action="<?php echo env('APP_URL'); ?>/api/auth/login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Email Input -->
            <div class="mb-3">
                <label for="identityInput" class="form-label">Email Address</label>
                <input type="text" name="identity" class="form-control dark-input" id="identityInput" placeholder="your.email@deped.gov.ph" required autocomplete="username">
            </div>
            
            <!-- Password Input -->
            <div class="mb-2">
                <label for="passwordInput" class="form-label">Password</label>
                <input type="password" name="password" class="form-control dark-input" id="passwordInput" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <!-- Forgot Password Link -->
            <div class="d-flex justify-content-end mb-4">
                <a href="#resetModal" data-bs-toggle="modal" class="text-decoration-none fs-8 text-accent-link fw-semibold">
                    <i class="bi bi-key-fill me-1"></i>Forgot Password?
                </a>
            </div>

            <!-- Sign In button -->
            <button type="submit" class="btn btn-signin w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>Sign In</span>
            </button>
            
            <div class="divider-container">Don't have an account?</div>

            <!-- Create Account button -->
            <button type="button" class="btn btn-register w-100 d-flex align-items-center justify-content-center gap-2" onclick="window.location.href='register.php'">
                <i class="bi bi-person-plus fs-5"></i>
                <span>Create Account</span>
            </button>
            
            <!-- Helpdesk info -->
            <div class="text-center mt-4">
                <a href="javascript:void(0)" onclick="showHelpdeskAlert()" class="text-decoration-none fs-8 text-accent-link">
                    Need help? Click <strong class="text-white">ICT Helpdesk</strong>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Footer Details (Division metadata) -->
<div class="page-footer">
    <div>DepEd — Schools Division Office of San San Pedro City</div>
    <div class="text-white-50">© 2026 ICT Unit</div>
</div>

<!-- =========================================================================
     MODALS & POPUPS
     ========================================================================= -->
<!-- Password Reset Request Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow" style="background-color: #0f1d3a; color: #f8fafc; border: 1px solid rgba(255,255,255,0.08) !important;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-white" id="resetModalLabel">Reset Your Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetForm" action="<?php echo env('APP_URL'); ?>/api/auth/reset-request.php" method="POST">
                <div class="modal-body py-3">
                    <p class="text-muted fs-8">Provide your registered email address below. If an account is associated with this email, we will dispatch a secure, 60-minute password reset link.</p>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-2">
                        <label for="resetEmail" class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control dark-input" id="resetEmail" placeholder="your.email@deped.gov.ph" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="min-height: 48px; border-radius: 8px;">Send Reset Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS (Includes Popper) CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
function showRegisterWarning() {
    alert("User registration is managed centrally by the SDO Accounting Unit. Please contact your system administrator to request an account.");
}

function showHelpdeskAlert() {
    alert("For authentication support or technical concerns, please submit an ICT ticket or email helpdesk@fast.sdo.gov.ph.");
}
</script>
</body>
</html>
