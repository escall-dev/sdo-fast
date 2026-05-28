<?php
/**
 * Employee Registration Page for SDO FAST.
 * Matches requested DepEd dark card theme layout over a #081121 background.
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/env.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Generate CSRF token for the registration form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SDO FAST</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme Variables -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/assets/css/style.css">
    
    <style>
        body {
            background-color: #081121; /* Matches ALPAS background */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .register-card {
            max-width: 650px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35);
            background: #0c1524; /* Deep navy card background matching ALPAS */
            overflow: hidden;
            color: #f8fafc;
        }
        .register-body {
            padding: 3rem 2.5rem;
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .text-danger-asterisk {
            color: #ef4444;
            margin-left: 2px;
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
            color: #4b5563 !important;
            opacity: 1;
        }
        .dark-input:focus {
            background-color: #0c1524 !important;
            border-color: #214da2 !important;
            box-shadow: 0 0 0 3px rgba(33, 77, 162, 0.25) !important;
            color: #ffffff !important;
        }
        select.dark-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
            appearance: none !important;
        }
        select.dark-input option {
            background-color: #0c1524;
            color: #f8fafc;
        }
        .input-subtext {
            font-size: 0.75rem;
            color: #475569;
            margin-top: 4px;
        }
        .btn-action {
            background-color: #214da2;
            border-color: #214da2;
            color: #ffffff;
            height: 48px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-action:hover, .btn-action:focus {
            background-color: #1a3d82;
            border-color: #1a3d82;
            transform: translateY(-1px);
        }
        .btn-back {
            background-color: #182335;
            border: 1px solid #25344c;
            color: #f8fafc;
            height: 48px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background-color: #202e45;
            color: #ffffff;
            border-color: #384c6c;
        }
        .page-footer {
            margin-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
        }
        .otp-display-banner {
            background-color: rgba(33, 77, 162, 0.15);
            border: 1px dashed rgba(33, 77, 162, 0.4);
            border-radius: 8px;
            color: #93c5fd;
            font-size: 0.85rem;
            padding: 10px 15px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="register-card shadow-lg">
    <div class="register-body">
        
        <!-- STEP 1: Registration Form -->
        <div id="registerStep">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1 text-white">
                    <i class="bi bi-person-plus-fill fs-3"></i>
                    <h3 class="fw-bold mb-0">Create Account</h3>
                </div>
                <div style="font-size: 0.85rem; color: #526685;">Register as an SDO Employee to file travel requests</div>
            </div>

            <!-- OTP Note Alert Box -->
            <div class="alert border-0 d-flex gap-3 align-items-start mb-4" role="alert" style="background-color: #0d213f; border: 1px solid rgba(33, 77, 162, 0.2) !important; color: #93c5fd; border-radius: 12px; padding: 1rem 1.25rem;">
                <i class="bi bi-info-circle-fill fs-5 mt-0.5 text-info"></i>
                <div style="font-size: 0.82rem; line-height: 1.45;">
                    An OTP will be sent to your email for verification. After verifying, your account will be created and ready to use immediately.
                </div>
            </div>

            <!-- Form Validation Alerts -->
            <div id="registerAlert" class="alert alert-danger border-0 d-none align-items-center gap-2 mb-4" role="alert" style="background-color: #2a1b22; color: #f87171; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <small id="registerAlertText" class="fw-semibold"></small>
            </div>

            <form id="registrationForm" onsubmit="handleRegisterSubmit(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="row g-3 mb-3">
                    <!-- Full Name -->
                    <div class="col-md-6">
                        <label for="fullNameInput" class="form-label">Full Name <span class="text-danger-asterisk">*</span></label>
                        <input type="text" name="full_name" class="form-control dark-input" id="fullNameInput" placeholder="Juan Dela Cruz" required>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="emailInput" class="form-label">Email <span class="text-danger-asterisk">*</span></label>
                        <input type="email" name="email" class="form-control dark-input" id="emailInput" placeholder="user@deped.gov.ph" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Office -->
                    <div class="col-md-6">
                        <label for="officeSelect" class="form-label">Office <span class="text-danger-asterisk">*</span></label>
                        <select name="office" class="form-select dark-input" id="officeSelect" onchange="handleOfficeChange()" required>
                            <option value="">-- Select Office --</option>
                            <option value="OSDS">OSDS (Office of the SDS Staff)</option>
                            <option value="SGOD">SGOD (School Governance and Operations Division)</option>
                            <option value="CID">CID (Curriculum Implementation Division)</option>
                        </select>
                        <div class="input-subtext text-muted">OSDS, SGOD, or CID</div>
                    </div>
                    
                    <!-- Unit / Section -->
                    <div class="col-md-6">
                        <label for="unitSelect" class="form-label">Unit/Section <span class="text-danger-asterisk">*</span></label>
                        <select name="unit_section" class="form-select dark-input" id="unitSelect" required disabled>
                            <option value="">-- Select Unit/Section --</option>
                        </select>
                        <div id="unitSubtext" class="input-subtext text-muted">Select an Office first</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Password -->
                    <div class="col-md-6">
                        <label for="passwordInput" class="form-label">Password <span class="text-danger-asterisk">*</span></label>
                        <input type="password" name="password" class="form-control dark-input" id="passwordInput" placeholder="Min. 8 characters" required minlength="8">
                        <div class="input-subtext text-muted">Minimum 8 characters</div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="col-md-6">
                        <label for="confirmPasswordInput" class="form-label">Confirm Password <span class="text-danger-asterisk">*</span></label>
                        <input type="password" name="confirm_password" class="form-control dark-input" id="confirmPasswordInput" placeholder="Re-enter password" required minlength="8">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Employee No -->
                    <div class="col-md-6">
                        <label for="employeeNoInput" class="form-label">Employee No. <span class="text-danger-asterisk">*</span></label>
                        <input type="text" name="employee_no" class="form-control dark-input" id="employeeNoInput" placeholder="E-12345" required>
                    </div>
                    
                    <!-- Position -->
                    <div class="col-md-6">
                        <label for="positionInput" class="form-label">Position <span class="text-danger-asterisk">*</span></label>
                        <input type="text" name="position" class="form-control dark-input" id="positionInput" placeholder="Teacher I" required>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-column gap-2">
                    <button type="submit" id="btnRegisterSubmit" class="btn btn-action w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-envelope-fill"></i>
                        <span>Verify Email & Register</span>
                    </button>
                    
                    <a href="login.php" class="btn btn-back w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back to Login</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- STEP 2: OTP Verification Card (Initially Hidden) -->
        <div id="otpStep" class="d-none">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1 text-white">
                    <i class="bi bi-shield-lock-fill fs-3 text-warning"></i>
                    <h3 class="fw-bold mb-0">Email Verification</h3>
                </div>
                <div style="font-size: 0.85rem; color: #526685;">Enter the One-Time Password (OTP) sent to your email</div>
            </div>

            <!-- Dev OTP Display (Shows up for local testing convenience) -->
            <div id="devOtpBanner" class="otp-display-banner d-none"></div>

            <!-- Verification Alert Box -->
            <div id="otpAlert" class="alert alert-danger border-0 d-none align-items-center gap-2 mb-4" role="alert" style="background-color: #2a1b22; color: #f87171; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <small id="otpAlertText" class="fw-semibold"></small>
            </div>

            <form id="otpForm" onsubmit="handleOtpSubmit(event)">
                <!-- OTP Entry Code -->
                <div class="mb-4 text-center">
                    <label for="otpCodeInput" class="form-label d-block text-center">Enter Verification Code</label>
                    <input type="text" class="form-control dark-input text-center fw-bold" id="otpCodeInput" placeholder="123456" maxlength="6" pattern="\d{6}" required style="font-size: 1.5rem; letter-spacing: 0.4rem; max-width: 260px; margin: 0 auto; height: 56px;">
                    <div class="input-subtext text-center mt-2">Check your email client or look for <code>otp_log.txt</code> inside the scratch directory.</div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-column gap-2">
                    <button type="submit" id="btnOtpSubmit" class="btn btn-action w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Verify & Complete Registration</span>
                    </button>
                    
                    <button type="button" class="btn btn-back w-100 d-flex align-items-center justify-content-center gap-2" onclick="goBackToRegister()">
                        <i class="bi bi-arrow-left"></i>
                        <span>Cancel & Back</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Footer -->
<div class="page-footer">
    <div>DepEd — Schools Division Office of San San Pedro City</div>
    <div style="opacity: 0.5; font-size: 0.7rem; margin-top: 4px;">© 2026 ICT Unit</div>
</div>

<script>
// Dynamic Dropdowns Configuration
const officeUnits = {
    "OSDS": [
        "OSDS (Office of the SDS Staff)",
        "OASDS (Office of the ASDS Staff)",
        "Personnel",
        "Property and Supply",
        "Records",
        "Cash",
        "Procurement",
        "General Services",
        "Legal",
        "ICT",
        "Accounting (Finance - Accounting)",
        "Budget (Finance - Budget)",
        "Administrative"
    ],
    "SGOD": [
        "SGOD (School Governance and Operations Division)",
        "SMME (School Management Monitoring and Evaluation)",
        "HRD (Human Resource Development)",
        "SMN (Social Mobilization and Networking)",
        "PR (Planning and Research)",
        "DRRM (Disaster Risk Reduction and Management)",
        "EF (Education Facilities)",
        "SHN_DENTAL (School Health and Nutrition - Dental)",
        "SHN_MEDICAL (School Health and Nutrition - Medical)",
        "SHN (School Health and Nutrition)"
    ],
    "CID": [
        "CID (Curriculum Implementation Division)",
        "IM (Instructional Management)",
        "LRM (Learning Resource Management)",
        "ALS (Alternative Learning System)",
        "DIS (District Instructional Supervision)"
    ]
};

function handleOfficeChange() {
    const officeSelect = document.getElementById('officeSelect');
    const unitSelect = document.getElementById('unitSelect');
    const unitSubtext = document.getElementById('unitSubtext');
    
    const selectedOffice = officeSelect.value;
    
    // Clear current items
    unitSelect.innerHTML = '<option value="">-- Select Unit/Section --</option>';
    
    if (selectedOffice && officeUnits[selectedOffice]) {
        unitSelect.removeAttribute('disabled');
        unitSubtext.textContent = 'Choose matching division section';
        
        // Populates elements
        officeUnits[selectedOffice].forEach(unit => {
            const opt = document.createElement('option');
            opt.value = unit;
            opt.textContent = unit;
            unitSelect.appendChild(opt);
        });
    } else {
        unitSelect.setAttribute('disabled', 'true');
        unitSubtext.textContent = 'Select an Office first';
    }
}

// Handler for registration form submission
function handleRegisterSubmit(event) {
    event.preventDefault();
    
    const alertBox = document.getElementById('registerAlert');
    const alertText = document.getElementById('registerAlertText');
    const submitBtn = document.getElementById('btnRegisterSubmit');
    
    // Hide alert
    alertBox.classList.add('d-none');
    
    // Validate passwords matching
    const pass = document.getElementById('passwordInput').value;
    const confirmPass = document.getElementById('confirmPasswordInput').value;
    
    if (pass !== confirmPass) {
        alertText.textContent = "Passwords do not match.";
        alertBox.classList.remove('d-none');
        return;
    }
    
    if (pass.length < 8) {
        alertText.textContent = "Password must be at least 8 characters.";
        alertBox.classList.remove('d-none');
        return;
    }
    
    // Gather form data
    const formData = new FormData(document.getElementById('registrationForm'));
    
    // Disable button to prevent double-submit
    submitBtn.setAttribute('disabled', 'true');
    submitBtn.querySelector('span').textContent = 'Sending Verification Code...';
    
    fetch('api/auth/register-request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify Email & Register';
        
        if (data.success) {
            // Success, transfer view to OTP verification step
            document.getElementById('registerStep').classList.add('d-none');
            document.getElementById('otpStep').classList.remove('d-none');
            
            // Show dev OTP banner if local env output it
            const devBanner = document.getElementById('devOtpBanner');
            if (data.dev_otp) {
                devBanner.innerHTML = `<i class="bi bi-bug-fill me-1"></i><strong>[Local Dev Mode]</strong> Generated verification OTP: <code>${data.dev_otp}</code>`;
                devBanner.classList.remove('d-none');
            } else {
                devBanner.classList.add('d-none');
            }
        } else {
            // Show error message
            alertText.textContent = data.message || "An error occurred. Please try again.";
            alertBox.classList.remove('d-none');
        }
    })
    .catch(err => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify Email & Register';
        alertText.textContent = "Unable to connect to registration server.";
        alertBox.classList.remove('d-none');
        console.error(err);
    });
}

// Handler for OTP verification form submission
function handleOtpSubmit(event) {
    event.preventDefault();
    
    const alertBox = document.getElementById('otpAlert');
    const alertText = document.getElementById('otpAlertText');
    const submitBtn = document.getElementById('btnOtpSubmit');
    const otpCode = document.getElementById('otpCodeInput').value;
    
    alertBox.classList.add('d-none');
    
    if (otpCode.length !== 6 || isNaN(otpCode)) {
        alertText.textContent = "Please enter a valid 6-digit OTP code.";
        alertBox.classList.remove('d-none');
        return;
    }
    
    submitBtn.setAttribute('disabled', 'true');
    submitBtn.querySelector('span').textContent = 'Verifying Account...';
    
    const formData = new FormData();
    formData.append('otp', otpCode);
    
    fetch('api/auth/verify-register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify & Complete Registration';
        
        if (data.success) {
            // Success redirect back to login page
            window.location.href = 'login.php';
        } else {
            // Show verification error
            alertText.textContent = data.message || "Verification failed.";
            alertBox.classList.remove('d-none');
        }
    })
    .catch(err => {
        submitBtn.removeAttribute('disabled');
        submitBtn.querySelector('span').textContent = 'Verify & Complete Registration';
        alertText.textContent = "Unable to connect to verification server.";
        alertBox.classList.remove('d-none');
        console.error(err);
    });
}

function goBackToRegister() {
    document.getElementById('otpStep').classList.add('d-none');
    document.getElementById('registerStep').classList.remove('d-none');
}
</script>

</body>
</html>
